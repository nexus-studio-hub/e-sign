<script setup lang="ts">
import { Head } from '@inertiajs/vue3'
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card'
import { Badge } from '@/components/ui/badge'

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
  qrCodeImage: string
  verificationUrl: string
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

      <Card>
        <CardHeader>
          <CardTitle>Supplier Information</CardTitle>
        </CardHeader>
        <CardContent class="space-y-4">
          <div>
            <p class="text-sm text-muted-foreground">Name</p>
            <p class="font-medium">{{ supplier.name }}</p>
          </div>
          <div>
            <p class="text-sm text-muted-foreground">Code</p>
            <p class="font-medium">{{ supplier.code }}</p>
          </div>
        </CardContent>
      </Card>

      <Card>
        <CardHeader>
          <CardTitle>Document Information</CardTitle>
        </CardHeader>
        <CardContent class="space-y-4">
          <div>
            <p class="text-sm text-muted-foreground">Document</p>
            <p class="font-medium">{{ document.name }}</p>
          </div>
          <div>
            <p class="text-sm text-muted-foreground">Part Name</p>
            <p class="font-medium">{{ part.name }}</p>
          </div>
          <div>
            <p class="text-sm text-muted-foreground">Part No</p>
            <p class="font-medium">{{ part.part_no }}</p>
          </div>
          <div>
            <p class="text-sm text-muted-foreground">Revision</p>
            <p class="font-medium">v{{ part.revision }}</p>
          </div>
          <div>
            <p class="text-sm text-muted-foreground">Status</p>
            <Badge class="bg-green-500">{{ document.status }}</Badge>
          </div>
          <div>
            <p class="text-sm text-muted-foreground">Stamped At</p>
            <p class="font-medium">{{ formatDate(document.stamped_at) }}</p>
          </div>
        </CardContent>
      </Card>

      <Card>
        <CardHeader>
          <CardTitle>QR Code Verification</CardTitle>
        </CardHeader>
        <CardContent>
          <div class="flex flex-col items-center space-y-4">
            <div class="bg-white p-4 rounded-lg border-2 border-gray-200">
              <img :src="qrCodeImage" alt="QR Code" class="w-64 h-64" />
            </div>
            <div class="text-center space-y-2">
              <p class="text-sm text-muted-foreground">Scan this QR code to verify this document</p>
              <div class="pt-2">
                <p class="text-xs text-muted-foreground mb-2">Or click the link below if you can't scan:</p>
                <a :href="verificationUrl" target="_blank" class="text-sm text-blue-600 hover:text-blue-800 underline break-all">
                  {{ verificationUrl }}
                </a>
              </div>
            </div>
          </div>
        </CardContent>
      </Card>

      <Card>
        <CardHeader>
          <CardTitle>Approval Chain</CardTitle>
        </CardHeader>
        <CardContent>
          <div class="space-y-4">
            <div 
              v-for="approval in approvals" 
              :key="approval.level"
              class="border rounded-lg p-4 space-y-2"
            >
              <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                  <Badge class="bg-green-500">Level {{ approval.level }}</Badge>
                  <span class="text-sm font-medium">{{ approval.role_name }}</span>
                </div>
                <span class="text-sm text-muted-foreground">{{ formatDate(approval.reviewed_at) }}</span>
              </div>
              <div class="space-y-1">
                <p class="text-sm"><span class="font-medium">Name:</span> {{ approval.reviewer_name }}</p>
                <p class="text-sm"><span class="font-medium">NIK:</span> {{ approval.reviewer_nik }}</p>
              </div>
              <div v-if="approval.comment" class="mt-2 p-2 bg-gray-50 rounded border border-gray-200">
                <span class="font-medium text-gray-700 text-sm">Comment:</span>
                <p class="text-sm text-gray-600 mt-1">{{ approval.comment }}</p>
              </div>
              <div v-else class="mt-2">
                <p class="text-sm text-gray-400 italic">No comment</p>
              </div>
            </div>
          </div>
        </CardContent>
      </Card>

      <div class="text-center text-sm text-gray-500 mt-8">
        This document has been verified and approved through the complete review process.
      </div>
    </div>
  </div>
</template>
