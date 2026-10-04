<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { Upload, CheckCircle, XCircle, FileText, RefreshCw, ArrowDownCircle, ArrowUpCircle } from 'lucide-vue-next'

interface Activity {
  id: number
  type: string
  action: string
  icon: string
  created_at: string
}

const activities = ref<Activity[]>([])

const fetchActivities = async () => {
  try {
    const response = await fetch('/activities', {
      headers: {
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest'
      }
    })
    const data = await response.json()
    activities.value = data.activities
  } catch (error) {
    console.error('Failed to fetch activities:', error)
  }
}

const getIcon = (iconName: string) => {
  const icons: Record<string, any> = {
    'upload': Upload,
    'check-circle': CheckCircle,
    'x-circle': XCircle,
    'file': FileText,
    'file-text': FileText,
    'refresh': RefreshCw,
  }
  return icons[iconName] || FileText
}

const getActivityStyle = (type: string) => {
  // Incoming activities (masuk) - hijau
  const incomingTypes = ['project_assigned', 'review_received']
  
  // Outgoing activities (keluar) - merah
  const outgoingTypes = ['upload', 'document_submitted', 'review_approved', 'review_rejected', 'project_created']
  
  if (incomingTypes.includes(type)) {
    return {
      bg: 'bg-green-50',
      text: 'text-green-700',
      icon: ArrowDownCircle
    }
  } else if (outgoingTypes.includes(type)) {
    return {
      bg: 'bg-red-50',
      text: 'text-red-700',
      icon: ArrowUpCircle
    }
  }
  
  return {
    bg: 'bg-gray-50',
    text: 'text-gray-700',
    icon: null
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
  fetchActivities()
})
</script>

<template>
  <div class="mt-8">
    <div class="px-3 mb-2">
      <p class="text-xs font-semibold text-muted-foreground uppercase tracking-wider">
        Recent Activity
      </p>
    </div>
    
    <div class="space-y-2 px-3">
      <div 
        v-for="activity in activities" 
        :key="activity.id"
        :class="[
          'flex items-start gap-2 text-xs py-2 px-2 rounded-md',
          getActivityStyle(activity.type).bg,
          getActivityStyle(activity.type).text
        ]"
      >
        <component
          :is="getActivityStyle(activity.type).icon || getIcon(activity.icon)" 
          class="w-4 h-4 flex-shrink-0 mt-0.5"
        />
        <div class="flex-1">
          <div>{{ activity.action }}</div>
          <div class="text-xs opacity-70 mt-0.5">{{ formatDate(activity.created_at) }}</div>
        </div>
      </div>
      
      <div v-if="activities.length === 0" class="text-xs text-center text-gray-400 py-4">
        No recent activity
      </div>
    </div>
  </div>
</template>
