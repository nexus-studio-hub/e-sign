<script setup lang="ts">
import type { TooltipRootEmits, TooltipRootProps } from 'reka-ui'
import { TooltipRoot, useForwardPropsEmits } from 'reka-ui'

interface Props { open?: boolean; defaultOpen?: boolean; delayDuration?: number; disableHoverableContent?: boolean }
const props = defineProps<Props>()
const emits = defineEmits<{ "update:open": [value: boolean] }>()

const forwarded = useForwardPropsEmits(props, emits)
</script>

<template>
  <TooltipRoot
    v-slot="slotProps"
    data-slot="tooltip"
    v-bind="forwarded"
  >
    <slot v-bind="slotProps" />
  </TooltipRoot>
</template>
