<script setup lang="ts">
import { ref, computed, watch } from 'vue'
import { ChevronDown } from 'lucide-vue-next'
import { vClickAway } from '@/directives/clickAway'

interface Option {
  value: string | number
  label: string
}

interface Props {
  modelValue: string | number
  options: Option[]
  placeholder?: string
}

const props = withDefaults(defineProps<Props>(), {
  placeholder: 'Pilih...'
})

const emit = defineEmits(['update:modelValue'])

const isOpen = ref(false)
const searchQuery = ref('')
const searchInput = ref<HTMLInputElement | null>(null)

const filteredOptions = computed(() => {
  if (!searchQuery.value) return props.options
  return props.options.filter(option =>
    option.label.toLowerCase().includes(searchQuery.value.toLowerCase())
  )
})

const selectedLabel = computed(() => {
  const selected = props.options.find(opt => opt.value === props.modelValue)
  return selected ? selected.label : props.placeholder
})

const toggleDropdown = () => {
  isOpen.value = !isOpen.value
  if (isOpen.value) {
    setTimeout(() => searchInput.value?.focus(), 50)
  }
}

const selectOption = (value: string | number) => {
  emit('update:modelValue', value)
  isOpen.value = false
  searchQuery.value = ''
}

const closeDropdown = () => {
  isOpen.value = false
  searchQuery.value = ''
}

watch(isOpen, (newVal) => {
  if (!newVal) {
    searchQuery.value = ''
  }
})
</script>

<template>
  <div class="relative" v-click-away="closeDropdown">
    <button
      type="button"
      @click="toggleDropdown"
      class="flex h-10 w-full items-center justify-between rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
    >
      <span :class="{ 'text-muted-foreground': !modelValue }">{{ selectedLabel }}</span>
      <ChevronDown class="h-4 w-4 opacity-50" />
    </button>

    <div
      v-if="isOpen"
      class="absolute z-[9999] mt-1 w-full rounded-md border bg-popover shadow-md"
    >
      <div class="p-2">
        <input
          ref="searchInput"
          v-model="searchQuery"
          type="text"
          placeholder="Cari..."
          class="flex h-9 w-full rounded-md border border-input bg-background px-3 py-1 text-sm shadow-sm transition-colors placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring"
        />
      </div>
      <div class="max-h-[200px] overflow-auto p-1">
        <div
          v-for="option in filteredOptions"
          :key="option.value"
          @click="selectOption(option.value)"
          class="relative flex cursor-pointer select-none items-center rounded-sm px-2 py-1.5 text-sm outline-none hover:bg-accent hover:text-accent-foreground"
          :class="{ 'bg-accent': option.value === modelValue }"
        >
          {{ option.label }}
        </div>
        <div v-if="filteredOptions.length === 0" class="py-6 text-center text-sm text-muted-foreground">
          Tidak ada hasil
        </div>
      </div>
    </div>
  </div>
</template>
