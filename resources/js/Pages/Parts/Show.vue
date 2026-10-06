<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { Badge } from '@/components/ui/badge'
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table'
import { ArrowLeft, FileText, Clock, Pencil, Trash2 } from 'lucide-vue-next'
import { Link, usePage, useForm, router } from '@inertiajs/vue3'
import { ref } from 'vue'

const props = defineProps<{
  part: any
}>()

const page = usePage()
const userRole = page.props.auth.user?.role
const showEditForm = ref(false)
const editingDoc = ref<any>(null)

const docForm = useForm({
  document_name: '',
  document_type: '',
  start_date: '',
  due_date: ''
})

const openEditForm = (reqDoc: any) => {
  editingDoc.value = reqDoc
  docForm.document_name = reqDoc.document_name
  docForm.document_type = reqDoc.document_type
  docForm.start_date = reqDoc.start_date || ''
  docForm.due_date = reqDoc.due_date || ''
  showEditForm.value = true
}

const submitEdit = () => {
  docForm.put(`/required-documents/${editingDoc.value.id}`, {
    onSuccess: () => {
      showEditForm.value = false
      docForm.reset()
    }
  })
}

const deleteDocument = (id: number) => {
  if (confirm('Are you sure you want to delete this document?')) {
    router.delete(`/required-documents/${id}`)
  }
}

const getStatusColor = (status: string) => {
  const colors: Record<string, string> = {
    draft: 'bg-gray-500',
    under_pic_review: 'bg-blue-500',
    under_group_review: 'bg-yellow-500',
    under_manager_review: 'bg-orange-500',
    approved: 'bg-green-500',
    need_revision: 'bg-red-500',
  }
  return colors[status] || 'bg-gray-500'
}

const formatDate = (date: string) => {
  if (!date) return '-'
  return new Date(date).toLocaleDateString('id-ID')
}
</script>

<template>
  <AuthenticatedLayout>
    <div class="space-y-6">
      <div class="flex items-center gap-4">
        <Link :href="`/projects/${part.project.id}`">
          <Button variant="ghost" size="sm">
            <ArrowLeft class="w-4 h-4 mr-2" />
            Back to Project
          </Button>
        </Link>
      </div>

      <Card>
        <CardHeader>
          <CardTitle>Part Details</CardTitle>
        </CardHeader>
        <CardContent class="space-y-4">
          <div class="grid grid-cols-2 gap-4">
            <div>
              <p class="text-sm text-muted-foreground">Part Number</p>
              <p class="font-medium">{{ part.part_number }}</p>
            </div>
            <div>
              <p class="text-sm text-muted-foreground">Part Name</p>
              <p class="font-medium">{{ part.part_name }}</p>
            </div>
            <div>
              <p class="text-sm text-muted-foreground">Model</p>
              <p class="font-medium">{{ part.model || '-' }}</p>
            </div>
            <div>
              <p class="text-sm text-muted-foreground">Supplier</p>
              <p class="font-medium">{{ part.supplier?.company_name }}</p>
            </div>
            <div>
              <p class="text-sm text-muted-foreground">Project</p>
              <p class="font-medium">{{ part.project?.project_name }}</p>
            </div>
          </div>
        </CardContent>
      </Card>

      <Card>
        <CardHeader>
          <CardTitle>Documents</CardTitle>
        </CardHeader>
        <CardContent>
          <div v-if="showEditForm" class="mb-6 p-4 border rounded-lg bg-gray-50">
            <h3 class="text-lg font-semibold mb-4">Edit Document</h3>
            <form @submit.prevent="submitEdit" class="space-y-4">
              <div class="grid grid-cols-2 gap-4">
                <div class="space-y-2">
                  <Label for="document_name">Document Name *</Label>
                  <Input id="document_name" v-model="docForm.document_name" required />
                  <div v-if="docForm.errors.document_name" class="text-sm text-red-500">{{ docForm.errors.document_name }}</div>
                </div>
                
                <div class="space-y-2">
                  <Label for="document_type">Document Type *</Label>
                  <Input id="document_type" v-model="docForm.document_type" required />
                  <div v-if="docForm.errors.document_type" class="text-sm text-red-500">{{ docForm.errors.document_type }}</div>
                </div>
                
                <div class="space-y-2">
                  <Label for="start_date">Start Date</Label>
                  <Input id="start_date" v-model="docForm.start_date" type="date" />
                </div>
                
                <div class="space-y-2">
                  <Label for="due_date">Due Date</Label>
                  <Input id="due_date" v-model="docForm.due_date" type="date" />
                </div>
              </div>
              
              <div class="flex gap-2">
                <Button type="submit" :disabled="docForm.processing">
                  {{ docForm.processing ? 'Saving...' : 'Update Document' }}
                </Button>
                <Button type="button" variant="outline" @click="showEditForm = false">
                  Cancel
                </Button>
              </div>
            </form>
          </div>

          <Table>
            <TableHeader>
              <TableRow>
                <TableHead>Document Name</TableHead>
                <TableHead>Type</TableHead>
                <TableHead>Start Date</TableHead>
                <TableHead>Due Date</TableHead>
                <TableHead>Status</TableHead>
                <TableHead>Actions</TableHead>
              </TableRow>
            </TableHeader>
            <TableBody>
              <TableRow v-for="reqDoc in part.required_documents" :key="reqDoc.id">
                <TableCell class="font-medium">{{ reqDoc.document_name }}</TableCell>
                <TableCell>{{ reqDoc.document_type }}</TableCell>
                <TableCell>
                  <div class="flex items-center gap-1 text-sm">
                    <Clock class="w-3 h-3" />
                    {{ formatDate(reqDoc.start_date) }}
                  </div>
                </TableCell>
                <TableCell>
                  <div class="flex items-center gap-1 text-sm">
                    <Clock class="w-3 h-3" />
                    {{ formatDate(reqDoc.due_date) }}
                  </div>
                </TableCell>
                <TableCell>
                  <Badge :class="getStatusColor(reqDoc.document?.status)">
                    {{ reqDoc.document?.status?.replace(/_/g, ' ') }}
                  </Badge>
                </TableCell>
                <TableCell>
                  <div class="flex gap-2">
                    <Link :href="`/documents/${reqDoc.document?.id}`" v-if="reqDoc.document">
                      <Button size="sm" variant="ghost">
                        <FileText class="w-4 h-4" />
                      </Button>
                    </Link>
                    <template v-if="userRole === 'supplier'">
                      <Link :href="`/documents/${reqDoc.document?.id}`" v-if="reqDoc.document">
                        <Button size="sm" variant="outline">
                          Upload
                        </Button>
                      </Link>
                    </template>
                    <template v-if="userRole === 'reviewer' || userRole === 'superadmin'">
                      <Button size="sm" variant="ghost" @click="openEditForm(reqDoc)">
                        <Pencil class="w-4 h-4" />
                      </Button>
                      <Button size="sm" variant="ghost" @click="deleteDocument(reqDoc.id)">
                        <Trash2 class="w-4 h-4 text-red-500" />
                      </Button>
                    </template>
                  </div>
                </TableCell>
              </TableRow>
              <TableRow v-if="!part.required_documents || part.required_documents.length === 0">
                <TableCell colspan="6" class="text-center text-muted-foreground">
                  No documents found
                </TableCell>
              </TableRow>
            </TableBody>
          </Table>
        </CardContent>
      </Card>
    </div>
  </AuthenticatedLayout>
</template>
