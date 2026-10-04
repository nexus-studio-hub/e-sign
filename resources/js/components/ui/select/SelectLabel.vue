<script setup lang="ts">

import type { HTMLAttributes } from 'vue'
import { SelectLabel } from 'reka-ui'
import { cn } from '@/lib/utils'

interface Props { as?: any; asChild?: boolean; class?: HTMLAttributes["class"] }
const props = defineProps<Props>()
</script>

<template>
  <SelectLabel
    data-slot="select-label"
    :class="cn('text-muted-foreground px-1.5 py-1 text-xs', props.class)"
  >
    <slot />
  </SelectLabel>
</template>
