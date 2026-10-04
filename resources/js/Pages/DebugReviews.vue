<script setup lang="ts">
const props = defineProps<{
  reviews_count: number
  reviews: any[]
  document: any
}>()

console.log('=== DEBUG INERTIA ===')
console.log('reviews_count prop:', props.reviews_count)
console.log('reviews array length:', props.reviews?.length)
console.log('reviews data:', props.reviews)
console.log('document.reviews length:', props.document?.reviews?.length)
console.log('document.reviews data:', props.document?.reviews)
</script>

<template>
  <div style="padding: 20px; font-family: monospace;">
    <h1>Debug Inertia Reviews</h1>
    <p>reviews_count: {{ reviews_count }}</p>
    <p>reviews.length: {{ reviews?.length }}</p>
    <p>document.reviews.length: {{ document?.reviews?.length }}</p>
    <hr>
    <h2>Check Browser Console (F12)</h2>
    <pre>{{ reviews }}</pre>
  </div>
</template>
