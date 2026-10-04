<script setup lang="ts">
import { computed } from 'vue'
import { TabsContent } from 'reka-ui'
import { cn } from '@/lib/utils'

interface TabsContentProps {
  value: string
  forceMount?: boolean
  class?: any
}

const props = defineProps<TabsContentProps>()

const delegatedProps = computed(() => {
  const { class: _, ...delegated } = props
  return delegated
})
</script>

<template>
  <TabsContent
    v-bind="delegatedProps"
    :class="cn('mt-2 ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2', props.class)"
  >
    <slot />
  </TabsContent>
</template>
