<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { Bell } from 'lucide-vue-next'
import { Button } from '@/components/ui/button'
import { router } from '@inertiajs/vue3'

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

const fetchNotifications = async () => {
  try {
    const response = await fetch('/notifications', {
      headers: {
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest'
      }
    })
    const data = await response.json()
    notifications.value = data.notifications
    unreadCount.value = data.unread_count
  } catch (error) {
    console.error('Failed to fetch notifications:', error)
  }
}

const markAsRead = async (id: number, link: string | null) => {
  try {
    const response = await fetch(`/notifications/${id}/read`, { 
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest'
      }
    })
    
    if (response.ok) {
      // Update local state immediately
      const notif = notifications.value.find(n => n.id === id)
      if (notif) {
        notif.read_at = new Date().toISOString()
        unreadCount.value = Math.max(0, unreadCount.value - 1)
      }
      
      // Navigate if link exists
      if (link) {
        isOpen.value = false
        router.visit(link, {
          onFinish: () => {
            // Refresh notifications after navigation
            fetchNotifications()
          }
        })
      }
    }
  } catch (error) {
    console.error('Failed to mark as read:', error)
  }
}

const markAllAsRead = async () => {
  try {
    await fetch('/notifications/read-all', { 
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest'
      }
    })
    
    // Update local state immediately
    notifications.value.forEach(notif => {
      notif.read_at = new Date().toISOString()
    })
    unreadCount.value = 0
  } catch (error) {
    console.error('Failed to mark all as read:', error)
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

onMounted(() => {
  fetchNotifications()
  setInterval(fetchNotifications, 30000)
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
        <div 
          v-for="notif in notifications" 
          :key="notif.id"
          @click="markAsRead(notif.id, notif.link)"
          class="p-4 border-b hover:bg-gray-50 cursor-pointer transition-colors"
          :class="!notif.read_at ? 'bg-blue-50' : ''"
        >
          <div class="font-semibold text-sm">{{ notif.title }}</div>
          <div class="text-xs text-gray-600 mt-1">{{ notif.message }}</div>
          <div class="text-xs text-gray-400 mt-2">{{ formatDate(notif.created_at) }}</div>
        </div>
        <div v-if="notifications.length === 0" class="p-8 text-center text-gray-500 text-sm">
          No notifications
        </div>
      </div>
    </div>
  </div>
</template>
