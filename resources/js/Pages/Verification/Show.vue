<script setup lang="ts">
import { Head } from '@inertiajs/vue3'

interface Approval {
  level: number
  role_name: string
  reviewer_name: string
  reviewer_nik: string
  reviewed_at: string
  comment: string | null
}

interface Props {
  serial: string
  supplier: {
    name: string
    code: string
  }
  document: {
    name: string
    status: string
    stamped_at: string
  }
  part: {
    name: string
    part_no: string
    revision: number
  }
  approvals: Approval[]
}

const props = defineProps<Props>()

const formatDate = (date: string) => {
  return new Date(date).toLocaleString('id-ID', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}
</script>

<template>
  <Head title="Document Verification" />
  
  <div class="min-h-screen bg-gray-50 py-12 px-4">
    <div class="max-w-4xl mx-auto space-y-6">
      <div class="text-center mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Document Verification</h1>
        <p class="text-lg text-gray-600">Serial: <span class="font-mono font-semibold">{{ serial }}</span></p>
      </div>

      <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-xl font-semibold mb-4">Supplier Information</h2>
        <div class="space-y-2">
          <div class="flex justify-between">
            <span class="text-gray-600">Name:</span>
            <span class="font-semibold">{{ supplier.name }}</span>
          </div>
          <div class="flex justify-between">
            <span class="text-gray-600">Code:</span>
            <span class="font-semibold">{{ supplier.code }}</span>
          </div>
        </div>
      </div>

      <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-xl font-semibold mb-4">Document Information</h2>
        <div class="space-y-2">
          <div class="flex justify-between">
            <span class="text-gray-600">Document:</span>
            <span class="font-semibold">{{ document.name }}</span>
          </div>
          <div class="flex justify-between">
            <span class="text-gray-600">Part Name:</span>
            <span class="font-semibold">{{ part.name }}</span>
          </div>
          <div class="flex justify-between">
            <span class="text-gray-600">Part No:</span>
            <span class="font-semibold">{{ part.part_no }}</span>
          </div>
          <div class="flex justify-between">
            <span class="text-gray-600">Revision:</span>
            <span class="font-semibold">v{{ part.revision }}</span>
          </div>
          <div class="flex justify-between">
            <span class="text-gray-600">Status:</span>
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">{{ document.status }}</span>
          </div>
          <div class="flex justify-between">
            <span class="text-gray-600">Stamped At:</span>
            <span class="font-semibold">{{ formatDate(document.stamped_at) }}</span>
          </div>
        </div>
      </div>

      <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-xl font-semibold mb-4">Approval Chain</h2>
        <div class="space-y-4">
          <div 
            v-for="approval in approvals" 
            :key="approval.level"
            class="border-l-4 border-green-500 pl-4 py-2"
          >
            <div class="font-semibold text-lg">Level {{ approval.level }} - {{ approval.role_name }}</div>
            <div class="text-sm text-gray-600 mt-1">
              <div>Name: {{ approval.reviewer_name }}</div>
              <div>NIK: {{ approval.reviewer_nik }}</div>
              <div>Date: {{ formatDate(approval.reviewed_at) }}</div>
              <div v-if="approval.comment" class="mt-2 p-2 bg-gray-50 rounded border border-gray-200">
                <span class="font-medium text-gray-700">Comment:</span>
                <span class="text-gray-600 ml-1">{{ approval.comment }}</span>
              </div>
              <div v-else class="mt-2 text-gray-400 italic text-xs">
                No comment
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="text-center text-sm text-gray-500 mt-8">
        This document has been verified and approved through the complete review process.
      </div>
    </div>
  </div>
</template>
