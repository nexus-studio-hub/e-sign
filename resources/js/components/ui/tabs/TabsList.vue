<script setup lang="ts">
import { computed } from 'vue'
import { TabsList } from 'reka-ui'
import { cn } from '@/lib/utils'

interface TabsListProps {
  loop?: boolean
  class?: any
}

const props = withDefaults(defineProps<TabsListProps>(), {
  loop: true
})

const delegatedProps = computed(() => {
  const { class: _, ...delegated } = props
  return delegated
})
</script>

<template>
  <TabsList
    v-bind="delegatedProps"
    :class="cn('inline-flex h-10 items-center justify-center rounded-md bg-muted p-1 text-muted-foreground', props.class)"
  >
    <slot />
  </TabsList>
</template>
