<script setup lang="ts">
import type { SelectRootEmits, SelectRootProps } from 'reka-ui'
import { SelectRoot, useForwardPropsEmits } from 'reka-ui'

interface Props { open?: boolean; defaultOpen?: boolean; defaultValue?: string; modelValue?: string; dir?: "ltr" | "rtl"; name?: string; autocomplete?: string; disabled?: boolean; required?: boolean }
const props = defineProps<Props>()
const emits = defineEmits<{ "update:modelValue": [value: string]; "update:open": [value: boolean] }>()

const forwarded = useForwardPropsEmits(props, emits)
</script>

<template>
  <SelectRoot
    v-slot="slotProps"
    data-slot="select"
    v-bind="forwarded"
  >
    <slot v-bind="slotProps" />
  </SelectRoot>
</template>
