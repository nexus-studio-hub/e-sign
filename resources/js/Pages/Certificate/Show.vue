<script setup lang="ts">
import GuestLayout from '@/Layouts/GuestLayout.vue'
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card'
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table'
import { Badge } from '@/components/ui/badge'
import { CheckCircle } from 'lucide-vue-next'

interface Props {
  project: any
  approvers: any[]
  isFullyCompleted: boolean
}

defineProps<Props>()
</script>

<template>
  <GuestLayout title="Certificate Verification">
    <div class="space-y-4 md:space-y-6 max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
      <!-- Header -->
      <div class="text-center py-4 md:py-8 bg-gradient-to-r from-green-50 to-teal-50 rounded-lg border-2 border-green-200">
        <div class="flex justify-center mb-3 md:mb-4">
          <CheckCircle class="w-12 h-12 md:w-16 md:h-16 text-green-600" />
        </div>
        <h1 class="text-lg md:text-2xl lg:text-3xl font-bold text-green-700 px-4">VERIFIED APPROVAL CERTIFICATE</h1>
        <p class="text-xs md:text-sm text-slate-600 mt-2 px-4">This document has been successfully approved</p>
      </div>

      <!-- Project Information -->
      <Card>
        <CardHeader>
          <CardTitle class="text-base md:text-lg">Project Information</CardTitle>
        </CardHeader>
        <CardContent>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-3 md:gap-4 text-xs md:text-sm">
            <div>
              <div class="text-muted-foreground">Project Code</div>
              <div class="font-bold text-base md:text-lg">{{ project.project_code }}</div>
            </div>
            <div>
              <div class="text-muted-foreground">Project Name</div>
              <div class="font-bold text-base md:text-lg">{{ project.project_name }}</div>
            </div>
            <div>
              <div class="text-muted-foreground">Model</div>
              <div class="font-medium">{{ project.model }}</div>
            </div>
            <div>
              <div class="text-muted-foreground">Part Number</div>
              <div class="font-medium">{{ project.part_number }}</div>
            </div>
            <div>
              <div class="text-muted-foreground">Part Name</div>
              <div class="font-medium">{{ project.part_name }}</div>
            </div>
            <div>
              <div class="text-muted-foreground">Supplier</div>
              <div class="font-medium">{{ project.supplier?.name }}</div>
            </div>
          </div>
        </CardContent>
      </Card>

      <!-- Document Status -->
      <Card>
        <CardHeader>
          <CardTitle class="text-base md:text-lg">Document Status</CardTitle>
        </CardHeader>
        <CardContent>
          <div class="overflow-x-auto">
            <Table>
              <TableHeader>
                <TableRow>
                  <TableHead class="text-xs md:text-sm">Document Name</TableHead>
                  <TableHead class="text-xs md:text-sm">Type</TableHead>
                  <TableHead class="text-xs md:text-sm">Version</TableHead>
                  <TableHead class="text-xs md:text-sm">Status</TableHead>
                </TableRow>
              </TableHeader>
              <TableBody>
              <TableRow v-for="doc in project.documents" :key="doc.id">
                <TableCell class="font-medium text-xs md:text-sm">{{ doc.required_document?.document_name }}</TableCell>
                <TableCell class="text-xs md:text-sm">{{ doc.required_document?.document_type }}</TableCell>
                <TableCell class="text-xs md:text-sm">v{{ doc.current_version }}</TableCell>
                <TableCell>
                  <Badge class="bg-green-100 text-green-800 text-xs">
                    ✓ {{ doc.status }}
                  </Badge>
                </TableCell>
              </TableRow>
            </TableBody>
          </Table>
          </div>
        </CardContent>
      </Card>

      <!-- Approval Chain -->
      <Card>
        <CardHeader>
          <CardTitle class="text-base md:text-lg">Approval Chain</CardTitle>
        </CardHeader>
        <CardContent>
          <div class="space-y-2 md:space-y-3">
            <div 
              v-for="(approver, index) in approvers" 
              :key="approver.id"
              class="flex items-start gap-3 md:gap-4 p-3 md:p-4 bg-green-50 border-l-4 border-green-500 rounded"
            >
              <div class="flex-shrink-0 w-7 h-7 md:w-8 md:h-8 bg-green-500 text-white rounded-full flex items-center justify-center font-bold text-sm md:text-base">
                {{ index + 1 }}
              </div>
              <div class="flex-1">
                <div class="font-semibold text-base md:text-lg">{{ approver.name }}</div>
                <div class="text-xs md:text-sm text-muted-foreground capitalize">
                  {{ approver.role.replace('_', ' ') }}
                </div>
                <div class="text-xs text-muted-foreground mt-1">
                  ✓ Approved on: {{ approver.reviewed_at }}
                </div>
              </div>
            </div>
          </div>
        </CardContent>
      </Card>

      <!-- Footer -->
      <div class="text-center text-xs md:text-sm text-slate-500 py-4">
        <p>This is an electronically verified certificate from E-SIGN Document Control System</p>
        <p class="mt-1">Verified at: {{ new Date().toLocaleString() }}</p>
      </div>
    </div>
  </GuestLayout>
</template>
