<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card'
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table'
import { Badge } from '@/components/ui/badge'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { Dialog, DialogContent, DialogHeader, DialogTitle } from '@/components/ui/dialog'
import { Link, usePage, router, useForm } from '@inertiajs/vue3'
import { Plus, FolderOpen } from 'lucide-vue-next'
import { ref, watch } from 'vue'

interface Props {
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

const props = defineProps<Props>()

const page = usePage()
const userRole = page.props.auth.user?.role
const searchQuery = ref('')
const showModal = ref(false)

const form = useForm({
  project_code: '',
  project_name: '',
})

const debounceTimer = ref<number | null>(null)

watch(searchQuery, (value) => {
  if (debounceTimer.value) {
    clearTimeout(debounceTimer.value)
  }
  
  debounceTimer.value = setTimeout(() => {
    router.get('/projects', { search: value }, { 
      preserveState: true,
      replace: true 
    })
  }, 500)
})

const goToPage = (url: string) => {
  if (url) {
    router.get(url, {}, { preserveState: true })
  }
}

const submitProject = () => {
  form.post('/projects', {
    onSuccess: () => {
      showModal.value = false
      form.reset()
    }
  })
}

const getStatusColor = (status: string) => {
  const colors: Record<string, string> = {
    draft: 'bg-gray-100 text-gray-800',
    active: 'bg-blue-100 text-blue-800',
    completed: 'bg-green-100 text-green-800',
    cancelled: 'bg-red-100 text-red-800'
  }
  return colors[status] || 'bg-gray-100 text-gray-800'
}

const getStatusText = (status: string) => {
  const texts: Record<string, string> = {
    draft: 'Draft',
    active: 'Active',
    completed: 'Completed',
    cancelled: 'Cancelled'
  }
  return texts[status] || status
}
</script>

<template>
  <AuthenticatedLayout>
    <div class="space-y-6">
      <div class="flex justify-between items-center">
        <div>
          <h1 class="text-2xl font-bold">Projects</h1>
          <p class="text-muted-foreground">Manage all projects</p>
        </div>
        <div class="flex gap-2">
          <Button v-if="userRole === 'reviewer' || userRole === 'superadmin'" @click="showModal = true">
            <Plus class="w-4 h-4 mr-2" />
            New Project
          </Button>
          
          <Dialog v-model:open="showModal">
            <DialogContent>
              <DialogHeader>
                <DialogTitle>Create New Project</DialogTitle>
              </DialogHeader>
              <form @submit.prevent="submitProject" class="space-y-4">
                <div class="space-y-2">
                  <Label for="project_code">Project Code *</Label>
                  <Input id="project_code" v-model="form.project_code" required />
                  <div v-if="form.errors.project_code" class="text-sm text-red-500">{{ form.errors.project_code }}</div>
                </div>
                
                <div class="space-y-2">
                  <Label for="project_name">Project Name *</Label>
                  <Input id="project_name" v-model="form.project_name" required />
                  <div v-if="form.errors.project_name" class="text-sm text-red-500">{{ form.errors.project_name }}</div>
                </div>
                
                <div class="flex gap-2">
                  <Button type="submit" :disabled="form.processing">
                    {{ form.processing ? 'Creating...' : 'Create Project' }}
                  </Button>
                  <Button type="button" variant="outline" @click="showModal = false">
                    Cancel
                  </Button>
                </div>
              </form>
            </DialogContent>
          </Dialog>
        </div>
      </div>

      <Card>
        <CardHeader>
          <div class="flex justify-between items-center">
            <CardTitle>All Projects</CardTitle>
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
                <TableHead>Parts</TableHead>
                <TableHead>Status</TableHead>
                <TableHead>Actions</TableHead>
              </TableRow>
            </TableHeader>
            <TableBody>
              <TableRow v-for="project in projects.data" :key="project.id">
                <TableCell class="font-medium">{{ project.project_code }}</TableCell>
                <TableCell>{{ project.project_name }}</TableCell>
                <TableCell>{{ project.parts_count || 0 }}</TableCell>
                <TableCell>
                  <Badge :class="getStatusColor(project.status)">
                    {{ getStatusText(project.status) }}
                  </Badge>
                </TableCell>
                <TableCell>
                  <Link :href="`/projects/${project.id}`">
                    <Button size="sm" variant="outline">
                      <FolderOpen class="w-4 h-4 mr-1" />
                      Open
                    </Button>
                  </Link>
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
