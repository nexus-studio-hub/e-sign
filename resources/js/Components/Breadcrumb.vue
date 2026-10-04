<script setup lang="ts">
import { Link } from '@inertiajs/vue3'
import { ChevronRight, Home } from 'lucide-vue-next'

interface BreadcrumbItem {
  label: string
  href?: string
  current?: boolean
}

defineProps<{
  items: BreadcrumbItem[]
}>()
</script>

<template>
  <nav class="flex items-center space-x-2 text-sm">
    <Link href="/dashboard" class="text-muted-foreground hover:text-cyan-600 transition-colors">
      <Home class="w-4 h-4" />
    </Link>
    
    <template v-for="(item, index) in items" :key="index">
      <ChevronRight class="w-4 h-4 text-muted-foreground" />
      
      <Link
        v-if="item.href && !item.current"
        :href="item.href"
        class="text-muted-foreground hover:text-cyan-600 transition-colors"
      >
        {{ item.label }}
      </Link>
      <span v-else class="text-foreground font-semibold">
        {{ item.label }}
      </span>
    </template>
  </nav>
</template>
