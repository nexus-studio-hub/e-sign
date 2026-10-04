<script setup lang="ts">

import { DialogTrigger } from 'reka-ui'

interface Props { as?: any; asChild?: boolean }
const props = defineProps<Props>()
</script>

<template>
  <DialogTrigger
    data-slot="sheet-trigger"
    v-bind="props"
  >
    <slot />
  </DialogTrigger>
</template>
