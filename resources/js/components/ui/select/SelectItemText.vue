<script setup lang="ts">

import { SelectItemText } from 'reka-ui'

interface Props { as?: any; asChild?: boolean }
const props = defineProps<Props>()
</script>

<template>
  <SelectItemText
    data-slot="select-item-text"
    v-bind="props"
  >
    <slot />
  </SelectItemText>
</template>
