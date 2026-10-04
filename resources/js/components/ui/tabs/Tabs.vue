<script setup lang="ts">
import { computed } from 'vue'
import {
  TabsRoot,
  useForwardPropsEmits,
} from 'reka-ui'
import { cn } from '@/lib/utils'

interface TabsProps {
  defaultValue?: string
  orientation?: 'horizontal' | 'vertical'
  dir?: 'ltr' | 'rtl'
  activationMode?: 'automatic' | 'manual'
  modelValue?: string
  class?: any
}

const props = withDefaults(defineProps<TabsProps>(), {
  orientation: 'horizontal',
  activationMode: 'automatic'
})

const emits = defineEmits<{
  'update:modelValue': [value: string]
}>()

const delegatedProps = computed(() => {
  const { class: _, ...delegated } = props
  return delegated
})

const forwarded = useForwardPropsEmits(delegatedProps, emits)
</script>

<template>
  <TabsRoot v-bind="forwarded" :class="cn('', props.class)">
    <slot />
  </TabsRoot>
</template>
