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

interface UploadHistory {
  version: number
  file_name: string
  file_size: string
  uploaded_by: string
  uploaded_at: string
}

interface Props {
  serial: string
  supplier: {
    company_name: string
    code: string
    contact_person: string | null
    email: string | null
    phone: string | null
  }
  document: {
    name: string
    type: string
    status: string
    stamped_at: string
  }
  part: {
    name: string
    number: string
    model: string | null
    revision: number
  }
  project: {
    name: string
    code: string
  }
  uploadHistory: UploadHistory[]
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
  
  <div class="min-h-screen bg-gray-50 py-8 px-4">
    <div class="max-w-7xl mx-auto">
      <div class="text-center mb-6">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Document Verification</h1>
        <p class="text-lg text-gray-600">Serial: <span class="font-mono font-semibold">{{ serial }}</span></p>
      </div>

      <!-- 2 Column Layout -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        <!-- Left Column -->
        <div class="space-y-6">
          
          <!-- Project Information -->
          <Card>
            <CardHeader>
              <CardTitle>Project Information</CardTitle>
            </CardHeader>
            <CardContent class="space-y-2">
              <div class="flex justify-between">
                <span class="text-sm text-muted-foreground">Project Name:</span>
                <span class="font-medium text-right">{{ project.name }}</span>
              </div>
              <div class="flex justify-between">
                <span class="text-sm text-muted-foreground">Project Code:</span>
                <span class="font-medium">{{ project.code }}</span>
              </div>
            </CardContent>
          </Card>

          <!-- Supplier Information -->
          <Card>
            <CardHeader>
              <CardTitle>Supplier Information</CardTitle>
            </CardHeader>
            <CardContent class="space-y-2">
              <div class="flex justify-between">
                <span class="text-sm text-muted-foreground">Company Name:</span>
                <span class="font-medium text-right">{{ supplier.company_name }}</span>
              </div>
              <div class="flex justify-between">
                <span class="text-sm text-muted-foreground">Supplier Code:</span>
                <span class="font-medium">{{ supplier.code }}</span>
              </div>
              <div v-if="supplier.contact_person" class="flex justify-between">
                <span class="text-sm text-muted-foreground">Contact Person:</span>
                <span class="font-medium text-right">{{ supplier.contact_person }}</span>
              </div>
              <div v-if="supplier.email" class="flex justify-between">
                <span class="text-sm text-muted-foreground">Email:</span>
                <span class="font-medium text-right">{{ supplier.email }}</span>
              </div>
              <div v-if="supplier.phone" class="flex justify-between">
                <span class="text-sm text-muted-foreground">Phone:</span>
                <span class="font-medium">{{ supplier.phone }}</span>
              </div>
            </CardContent>
          </Card>

          <!-- Part Information -->
          <Card>
            <CardHeader>
              <CardTitle>Part Information</CardTitle>
            </CardHeader>
            <CardContent class="space-y-2">
              <div class="flex justify-between">
                <span class="text-sm text-muted-foreground">Part Name:</span>
                <span class="font-medium text-right">{{ part.name }}</span>
              </div>
              <div class="flex justify-between">
                <span class="text-sm text-muted-foreground">Part Number:</span>
                <span class="font-medium">{{ part.number }}</span>
              </div>
              <div v-if="part.model" class="flex justify-between">
                <span class="text-sm text-muted-foreground">Model:</span>
                <span class="font-medium">{{ part.model }}</span>
              </div>
            </CardContent>
          </Card>

          <!-- Document Information -->
          <Card>
            <CardHeader>
              <CardTitle>Document Information</CardTitle>
            </CardHeader>
            <CardContent class="space-y-2">
              <div class="flex justify-between">
                <span class="text-sm text-muted-foreground">Document Name:</span>
                <span class="font-medium text-right">{{ document.name }}</span>
              </div>
              <div class="flex justify-between">
                <span class="text-sm text-muted-foreground">Document Type:</span>
                <span class="font-medium">{{ document.type }}</span>
              </div>
              <div class="flex justify-between">
                <span class="text-sm text-muted-foreground">Revision:</span>
                <span class="font-medium">v{{ part.revision }}</span>
              </div>
              <div class="flex justify-between items-center">
                <span class="text-sm text-muted-foreground">Status:</span>
                <Badge class="bg-green-500">{{ document.status }}</Badge>
              </div>
              <div class="flex justify-between">
                <span class="text-sm text-muted-foreground">Stamped At:</span>
                <span class="font-medium text-right text-sm">{{ formatDate(document.stamped_at) }}</span>
              </div>
            </CardContent>
          </Card>

        </div>

        <!-- Right Column -->
        <div class="space-y-6">
          
          <!-- Upload History -->
          <Card>
            <CardHeader>
              <CardTitle>Upload History</CardTitle>
            </CardHeader>
            <CardContent>
              <div class="space-y-3">
                <div 
                  v-for="upload in uploadHistory" 
                  :key="upload.version"
                  class="border rounded-lg p-3 space-y-1.5"
                >
                  <div class="flex justify-between items-center mb-2">
                    <Badge class="bg-blue-500">Version {{ upload.version }}</Badge>
                    <span class="text-xs text-muted-foreground">{{ formatDate(upload.uploaded_at) }}</span>
                  </div>
                  <div class="flex justify-between text-sm">
                    <span class="text-muted-foreground">File:</span>
                    <span class="font-medium text-right text-xs">{{ upload.file_name }}</span>
                  </div>
                  <div class="flex justify-between text-sm">
                    <span class="text-muted-foreground">Size:</span>
                    <span class="font-medium">{{ upload.file_size }}</span>
                  </div>
                  <div class="flex justify-between text-sm">
                    <span class="text-muted-foreground">Uploaded by:</span>
                    <span class="font-medium">{{ upload.uploaded_by }}</span>
                  </div>
                </div>
              </div>
            </CardContent>
          </Card>

          <!-- Approval Chain -->
          <Card>
            <CardHeader>
              <CardTitle>Approval Chain</CardTitle>
            </CardHeader>
            <CardContent>
              <div class="space-y-3">
                <div 
                  v-for="approval in approvals" 
                  :key="approval.level"
                  class="border rounded-lg p-3 space-y-1.5"
                >
                  <div class="flex justify-between items-center mb-2">
                    <div class="flex items-center gap-2">
                      <Badge class="bg-green-500">Level {{ approval.level }}</Badge>
                      <span class="text-sm font-medium">{{ approval.role_name }}</span>
                    </div>
                    <span class="text-xs text-muted-foreground">{{ formatDate(approval.reviewed_at) }}</span>
                  </div>
                  <div class="flex justify-between text-sm">
                    <span class="text-muted-foreground">Name:</span>
                    <span class="font-medium">{{ approval.reviewer_name }}</span>
                  </div>
                  <div class="flex justify-between text-sm">
                    <span class="text-muted-foreground">NIK:</span>
                    <span class="font-medium">{{ approval.reviewer_nik }}</span>
                  </div>
                  <div v-if="approval.comment" class="mt-2 p-2 bg-gray-50 rounded border border-gray-200">
                    <span class="font-medium text-gray-700 text-xs">Comment:</span>
                    <p class="text-xs text-gray-600 mt-1">{{ approval.comment }}</p>
                  </div>
                  <div v-else class="mt-1">
                    <p class="text-xs text-gray-400 italic">No comment</p>
                  </div>
                </div>
              </div>
            </CardContent>
          </Card>

        </div>

      </div>

      <div class="text-center text-sm text-gray-500 mt-6">
        This document has been verified and approved through the complete review process.
      </div>
    </div>
  </div>
</template>
