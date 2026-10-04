<script setup lang="ts">
import { computed } from 'vue'
import { cn } from '@/lib/utils'

const props = defineProps<{
  checked?: boolean
  disabled?: boolean
  class?: string
}>()

const emit = defineEmits<{
  'update:checked': [value: boolean]
}>()

const handleChange = (event: Event) => {
  const target = event.target as HTMLInputElement
  emit('update:checked', target.checked)
}
</script>

<template>
  <input
    type="checkbox"
    :checked="checked"
    :disabled="disabled"
    @change="handleChange"
    :class="cn('h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500', props.class)"
  />
</template>
