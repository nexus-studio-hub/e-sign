<script setup lang="ts">
import { X } from 'lucide-vue-next'

const emit = defineEmits<{
  close: []
}>()
</script>

<template>
  <div class="bg-white rounded-lg shadow-xl p-6 max-h-[90vh] overflow-y-auto" @click.stop>
    <slot />
  </div>
</template>
