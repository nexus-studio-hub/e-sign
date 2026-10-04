<script setup lang="ts">
import { DialogClose } from 'reka-ui'

interface Props {
  as?: any
  asChild?: boolean
}

const props = defineProps<Props>()
</script>

<template>
  <DialogClose
    data-slot="sheet-close"
    v-bind="props"
  >
    <slot />
  </DialogClose>
</template>
