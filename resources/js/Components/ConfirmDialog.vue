<script setup lang="ts">
import { Dialog, DialogContent, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog'
import { Button } from '@/components/ui/button'
import { useConfirm } from '@/composables/useConfirm'
import { AlertTriangle } from 'lucide-vue-next'

const { isOpen, options, handleConfirm, handleCancel } = useConfirm()
</script>

<template>
  <Dialog v-model:open="isOpen">
    <DialogContent class="sm:max-w-md">
      <DialogHeader>
        <DialogTitle class="flex items-center gap-2">
          <AlertTriangle 
            v-if="options.variant === 'danger'" 
            class="w-5 h-5 text-red-500" 
          />
          <AlertTriangle 
            v-else-if="options.variant === 'warning'" 
            class="w-5 h-5 text-orange-500" 
          />
          {{ options.title }}
        </DialogTitle>
      </DialogHeader>
      <div class="py-4">
        <p class="text-sm text-muted-foreground">{{ options.description }}</p>
      </div>
      <DialogFooter>
        <Button variant="outline" @click="handleCancel">
          {{ options.cancelText }}
        </Button>
        <Button 
          :variant="options.variant === 'danger' ? 'destructive' : 'default'"
          @click="handleConfirm"
        >
          {{ options.confirmText }}
        </Button>
      </DialogFooter>
    </DialogContent>
  </Dialog>
</template>
