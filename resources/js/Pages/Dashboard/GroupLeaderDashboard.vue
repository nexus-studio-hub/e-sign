<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card'
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table'
import { Badge } from '@/components/ui/badge'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { ClipboardCheck, CheckCircle, AlertCircle } from 'lucide-vue-next'
import { router } from '@inertiajs/vue3'
import { ref, watch } from 'vue'

interface Props {
  stats: {
    waitingReview: number
    approved: number
    needRevision: number
  }
  documents: {
    data: any[]
    current_page: number
    last_page: number
    per_page: number
    total: number
    from: number
    to: number
    links: any[]
  }
}

defineProps<Props>()

const searchQuery = ref('')

watch(searchQuery, (value) => {
  router.get('/dashboard', { search: value }, { 
    preserveState: true,
    replace: true 
  })
})

const goToPage = (url: string) => {
  if (url) {
    router.get(url, {}, { preserveState: true })
  }
}
</script>

<template>
  <AuthenticatedLayout>
    <div class="space-y-6">
      <div>
        <h1 class="text-2xl font-bold">Group Leader Dashboard</h1>
        <p class="text-muted-foreground">Review and approve documents</p>
      </div>

      <div class="grid gap-4 md:grid-cols-3">
        <Card>
          <CardContent class="p-6">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm font-medium text-muted-foreground">Waiting Review</p>
                <p class="text-2xl font-bold">{{ stats.waitingReview }}</p>
              </div>
              <div class="w-12 h-12 rounded-lg bg-purple-500/10 flex items-center justify-center">
                <ClipboardCheck class="w-6 h-6 text-purple-500" />
              </div>
            </div>
          </CardContent>
        </Card>

        <Card>
          <CardContent class="p-6">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm font-medium text-muted-foreground">Approved</p>
                <p class="text-2xl font-bold">{{ stats.approved }}</p>
              </div>
              <div class="w-12 h-12 rounded-lg bg-green-500/10 flex items-center justify-center">
                <CheckCircle class="w-6 h-6 text-green-500" />
              </div>
            </div>
          </CardContent>
        </Card>

        <Card>
          <CardContent class="p-6">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm font-medium text-muted-foreground">Need Revision</p>
                <p class="text-2xl font-bold">{{ stats.needRevision }}</p>
              </div>
              <div class="w-12 h-12 rounded-lg bg-red-500/10 flex items-center justify-center">
                <AlertCircle class="w-6 h-6 text-red-500" />
              </div>
            </div>
          </CardContent>
        </Card>
      </div>

      <Card>
        <CardHeader>
          <div class="flex justify-between items-center">
            <CardTitle>Documents Waiting Review</CardTitle>
            <div class="w-64">
              <Input
                v-model="searchQuery"
                placeholder="Search documents..."
                class="h-9"
              />
            </div>
          </div>
        </CardHeader>
        <CardContent>
          <Table>
            <TableHeader>
              <TableRow>
                <TableHead>Project</TableHead>
                <TableHead>Document</TableHead>
                <TableHead>Type</TableHead>
                <TableHead>Action</TableHead>
              </TableRow>
            </TableHeader>
            <TableBody>
              <TableRow v-for="doc in documents.data" :key="doc.id">
                <TableCell class="font-medium">{{ doc.project?.project_code }}</TableCell>
                <TableCell>{{ doc.required_document?.document_name }}</TableCell>
                <TableCell>{{ doc.required_document?.document_type }}</TableCell>
                <TableCell>
                  <a :href="`/reviews/${doc.id}`" class="text-cyan-600 hover:underline">Review</a>
                </TableCell>
              </TableRow>
            </TableBody>
          </Table>

          <!-- Pagination -->
          <div class="flex items-center justify-between mt-4">
            <div class="text-sm text-muted-foreground">
              Showing {{ documents.from }} to {{ documents.to }} of {{ documents.total }} results
            </div>
            <div class="flex gap-2">
              <Button 
                v-for="link in documents.links" 
                :key="link.label"
                size="sm" 
                variant="outline"
                :disabled="!link.url || link.active"
                :class="link.active ? 'bg-primary text-white' : ''"
                @click="goToPage(link.url)"
                v-html="link.label"
              />
            </div>
          </div>
        </CardContent>
      </Card>
    </div>
  </AuthenticatedLayout>
</template>
