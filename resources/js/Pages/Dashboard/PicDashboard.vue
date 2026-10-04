<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card'
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table'
import { Badge } from '@/components/ui/badge'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { FolderOpen, FileText, Clock, AlertCircle } from 'lucide-vue-next'
import { router } from '@inertiajs/vue3'
import { ref, watch } from 'vue'

interface Props {
  stats: {
    totalProjects: number
    draft: number
    submitted: number
    needRevision: number
  }
  projects: {
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

const getStatusColor = (status: string) => {
  const colors: Record<string, string> = {
    draft: 'bg-gray-100 text-gray-800',
    submitted: 'bg-blue-100 text-blue-800',
    under_pic_review: 'bg-purple-100 text-purple-800',
    need_revision: 'bg-red-100 text-red-800',
    complete: 'bg-green-100 text-green-800'
  }
  return colors[status] || 'bg-gray-100 text-gray-800'
}
</script>

<template>
  <AuthenticatedLayout>
    <div class="space-y-6">
      <div>
        <h1 class="text-2xl font-bold">PIC Development Dashboard</h1>
        <p class="text-muted-foreground">Manage projects and review documents</p>
      </div>

      <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
        <Card>
          <CardContent class="p-6">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm font-medium text-muted-foreground">Total Projects</p>
                <p class="text-2xl font-bold">{{ stats.totalProjects }}</p>
              </div>
              <div class="w-12 h-12 rounded-lg bg-cyan-500/10 flex items-center justify-center">
                <FolderOpen class="w-6 h-6 text-cyan-500" />
              </div>
            </div>
          </CardContent>
        </Card>

        <Card>
          <CardContent class="p-6">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm font-medium text-muted-foreground">Draft</p>
                <p class="text-2xl font-bold">{{ stats.draft }}</p>
              </div>
              <div class="w-12 h-12 rounded-lg bg-gray-500/10 flex items-center justify-center">
                <FileText class="w-6 h-6 text-gray-500" />
              </div>
            </div>
          </CardContent>
        </Card>

        <Card>
          <CardContent class="p-6">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm font-medium text-muted-foreground">Submitted</p>
                <p class="text-2xl font-bold">{{ stats.submitted }}</p>
              </div>
              <div class="w-12 h-12 rounded-lg bg-blue-500/10 flex items-center justify-center">
                <Clock class="w-6 h-6 text-blue-500" />
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
            <CardTitle>Recent Projects</CardTitle>
            <div class="w-64">
              <Input
                v-model="searchQuery"
                placeholder="Search projects..."
                class="h-9"
              />
            </div>
          </div>
        </CardHeader>
        <CardContent>
          <Table>
            <TableHeader>
              <TableRow>
                <TableHead>Project Code</TableHead>
                <TableHead>Project Name</TableHead>
                <TableHead>Supplier</TableHead>
                <TableHead>Due Date</TableHead>
                <TableHead>Status</TableHead>
                <TableHead>Action</TableHead>
              </TableRow>
            </TableHeader>
            <TableBody>
              <TableRow v-for="project in projects.data" :key="project.id">
                <TableCell class="font-medium">{{ project.project_code }}</TableCell>
                <TableCell>{{ project.project_name }}</TableCell>
                <TableCell>{{ project.supplier?.name }}</TableCell>
                <TableCell>{{ project.due_date }}</TableCell>
                <TableCell>
                  <Badge :class="getStatusColor(project.status)">{{ project.status }}</Badge>
                </TableCell>
                <TableCell>
                  <a :href="`/projects/${project.id}`" class="text-cyan-600 hover:underline">View</a>
                </TableCell>
              </TableRow>
            </TableBody>
          </Table>

          <!-- Pagination -->
          <div class="flex items-center justify-between mt-4">
            <div class="text-sm text-muted-foreground">
              Showing {{ projects.from }} to {{ projects.to }} of {{ projects.total }} results
            </div>
            <div class="flex gap-2">
              <Button 
                v-for="link in projects.links" 
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
