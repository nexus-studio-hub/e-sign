<script setup lang="ts">
import { ref, onMounted, onUnmounted } from 'vue'
import { Bell } from 'lucide-vue-next'
import { Button } from '@/components/ui/button'
import { Link, router } from '@inertiajs/vue3'

interface Notification {
  id: number
  title: string
  message: string
  link: string | null
  read_at: string | null
  created_at: string
}

const notifications = ref<Notification[]>([])
const unreadCount = ref(0)
const isOpen = ref(false)
const isUpdating = ref(false)
let pollingInterval: number | null = null

const fetchNotifications = async () => {
  try {
    const response = await fetch('/notifications', {
      headers: {
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest'
      }
    })
    const data = await response.json()
    
    // Merge: Keep local read_at if server hasn't updated yet
    const mergedNotifications = data.notifications.map((serverNotif: Notification) => {
      const localNotif = notifications.value.find(n => n.id === serverNotif.id)
      if (localNotif && localNotif.read_at && !serverNotif.read_at) {
        // Local marked as read but server hasn't updated yet - keep local
        return { ...serverNotif, read_at: localNotif.read_at }
      }
      return serverNotif
    })
    
    notifications.value = mergedNotifications
    unreadCount.value = data.unread_count
  } catch (error) {
    console.error('Failed to fetch notifications:', error)
  }
}

const markAsRead = async (id: number): Promise<boolean> => {
  console.log('🔵 markAsRead START:', id)
  try {
    const response = await fetch(`/notifications/${id}/read`, { 
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest'
      }
    })
    console.log('🟢 Response status:', response.status)
    
    if (!response.ok) {
      console.error('❌ Server error:', response.status, response.statusText)
      if (response.status === 419) {
        console.error('❌ CSRF token mismatch! Check meta tag.')
      }
      return false
    }
    
    console.log('✅ Server confirmed!')
    return true
  } catch (error) {
    console.error('❌ markAsRead ERROR:', error)
    return false
  }
}

const markAllAsRead = async () => {
  isUpdating.value = true
  try {
    const response = await fetch('/notifications/read-all', {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest'
      }
    })
    
    if (!response.ok) {
      console.error('Failed to mark all as read:', response.status)
      return
    }
    
    notifications.value = notifications.value.map(notif => ({
      ...notif,
      read_at: new Date().toISOString()
    }))
    unreadCount.value = 0
    
    await fetchNotifications()
  } catch (error) {
    console.error('Failed to mark all as read:', error)
  } finally {
    isUpdating.value = false
  }
}

const handleNotificationClick = async (notif: Notification) => {
  isUpdating.value = true
  try {
    console.log('🎯 Clicked notification:', notif.id, 'read_at:', notif.read_at)
    
    const wasUnread = !notif.read_at
    if (wasUnread) {
      notifications.value = notifications.value.map(n => 
        n.id === notif.id 
          ? { ...n, read_at: new Date().toISOString() }
          : n
      )
      unreadCount.value = Math.max(0, unreadCount.value - 1)
      console.log('⚡ UI updated! New count:', unreadCount.value)
    }
    
    markAsRead(notif.id).then(success => {
      if (!success && wasUnread) {
        notifications.value = notifications.value.map(n =>
          n.id === notif.id
            ? { ...n, read_at: null }
            : n
        )
        unreadCount.value += 1
        console.log('🔄 Rolled back! Count restored:', unreadCount.value)
      }
    })
    
    isOpen.value = false
    if (notif.link) {
      console.log('🔗 Navigating to:', notif.link)
      router.visit(notif.link)
    }
  } finally {
    isUpdating.value = false
  }
}

const formatDate = (dateString: string) => {
  const date = new Date(dateString)
  const now = new Date()
  const diff = now.getTime() - date.getTime()
  const minutes = Math.floor(diff / 60000)
  const hours = Math.floor(diff / 3600000)
  const days = Math.floor(diff / 86400000)
  
  if (minutes < 1) return 'Just now'
  if (minutes < 60) return `${minutes}m ago`
  if (hours < 24) return `${hours}h ago`
  if (days < 7) return `${days}d ago`
  return date.toLocaleDateString()
}

const startPolling = () => {
  if (pollingInterval) return
  pollingInterval = setInterval(() => {
    if (!isOpen.value && !isUpdating.value) {
      fetchNotifications()
    }
  }, 30000)
}

onMounted(() => {
  fetchNotifications()
  startPolling()
})

onUnmounted(() => {
  if (pollingInterval) {
    clearInterval(pollingInterval)
    pollingInterval = null
  }
})
</script>

<template>
  <div class="relative">
    <Button 
      variant="ghost" 
      size="icon"
      @click="isOpen = !isOpen"
      class="relative"
    >
      <Bell class="w-5 h-5" />
      <span
        v-if="unreadCount > 0"
        class="absolute top-1 right-1 w-4 h-4 bg-red-500 text-white text-xs rounded-full flex items-center justify-center"
      >
        {{ unreadCount > 9 ? '9+' : unreadCount }}
      </span>
    </Button>
    
    <div 
      v-if="isOpen"
      class="absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-lg border z-50"
    >
      <div class="p-4 border-b flex justify-between items-center">
        <h3 class="font-semibold">Notifications</h3>
        <Button 
          v-if="unreadCount > 0"
          variant="ghost" 
          size="sm"
          @click="markAllAsRead"
          class="text-xs"
        >
          Mark all read
        </Button>
      </div>
      
      <div class="max-h-96 overflow-y-auto">
        <Link 
          v-for="notif in notifications" 
          :key="notif.id"
          :href="notif.link || '#'"
          @click.prevent="handleNotificationClick(notif)"
          class="block p-4 border-b hover:bg-gray-50 cursor-pointer transition-colors"
          :class="!notif.read_at ? 'bg-blue-50' : ''"
        >
          <div class="font-semibold text-sm">{{ notif.title }}</div>
          <div class="text-xs text-gray-600 mt-1">{{ notif.message }}</div>
          <div class="text-xs text-gray-400 mt-2">{{ formatDate(notif.created_at) }}</div>
        </Link>
        <div v-if="notifications.length === 0" class="p-8 text-center text-gray-500 text-sm">
          No notifications
        </div>
      </div>
    </div>
  </div>
</template>
