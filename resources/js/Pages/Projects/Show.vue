<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card'
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { usePage, router, useForm, Link } from '@inertiajs/vue3'
import { Plus, Pencil, Trash2, FolderOpen } from 'lucide-vue-next'
import { ref } from 'vue'

interface Props {
  project: any
  suppliers: any[]
}

const props = defineProps<Props>()

const page = usePage()
const userRole = page.props.auth.user?.role
const showPartForm = ref(false)
const editingPart = ref<any>(null)

const partForm = useForm({
  part_number: '',
  part_name: '',
  model: '',
  supplier_id: '',
  documents: [
    { document_name: 'PPAP', document_type: 'PPAP', is_required: true, start_date: '', due_date: '' },
    { document_name: 'FMEA', document_type: 'FMEA', is_required: true, start_date: '', due_date: '' },
    { document_name: 'Control Plan', document_type: 'Control Plan', is_required: true, start_date: '', due_date: '' },
    { document_name: 'Drawing', document_type: 'Drawing', is_required: true, start_date: '', due_date: '' }
  ]
})

const openPartForm = (part: any = null) => {
  if (part) {
    editingPart.value = part
    partForm.part_number = part.part_number
    partForm.part_name = part.part_name
    partForm.model = part.model || ''
    partForm.supplier_id = part.supplier_id
  } else {
    editingPart.value = null
    partForm.reset()
  }
  showPartForm.value = true
}

const submitPart = () => {
  if (editingPart.value) {
    partForm.put(`/parts/${editingPart.value.id}`, {
      onSuccess: () => {
        showPartForm.value = false
        partForm.reset()
      }
    })
  } else {
    partForm.post(`/projects/${props.project.id}/parts`, {
      onSuccess: () => {
        showPartForm.value = false
        partForm.reset()
      }
    })
  }
}

const deletePart = (partId: number) => {
  if (confirm('Are you sure you want to delete this part?')) {
    router.delete(`/parts/${partId}`)
  }
}

const addDocument = () => {
  partForm.documents.push({ document_name: '', document_type: '', is_required: true, start_date: '', due_date: '' })
}

const removeDocument = (index: number) => {
  partForm.documents.splice(index, 1)
}
</script>

<template>
  <AuthenticatedLayout>
    <div class="space-y-6">
      <div>
        <h1 class="text-2xl font-bold">{{ project.project_code }}</h1>
        <p class="text-muted-foreground">{{ project.project_name }}</p>
      </div>

      <Card>
        <CardHeader>
          <div class="flex justify-between items-center">
            <CardTitle>Parts</CardTitle>
            <Button v-if="userRole === 'reviewer' || userRole === 'superadmin'" @click="openPartForm()">
              <Plus class="w-4 h-4 mr-2" />
              Add Part
            </Button>
          </div>
        </CardHeader>
        
        <CardContent>
          <div v-if="showPartForm" class="mb-6 p-4 border rounded-lg bg-gray-50">
            <h3 class="text-lg font-semibold mb-4">{{ editingPart ? 'Edit Part' : 'Add New Part' }}</h3>
            <form @submit.prevent="submitPart" class="space-y-4">
              <div class="grid grid-cols-2 gap-4">
                <div class="space-y-2">
                  <Label for="part_number">Part Number *</Label>
                  <Input id="part_number" v-model="partForm.part_number" required />
                  <div v-if="partForm.errors.part_number" class="text-sm text-red-500">{{ partForm.errors.part_number }}</div>
                </div>
                
                <div class="space-y-2">
                  <Label for="part_name">Part Name *</Label>
                  <Input id="part_name" v-model="partForm.part_name" required />
                  <div v-if="partForm.errors.part_name" class="text-sm text-red-500">{{ partForm.errors.part_name }}</div>
                </div>
                
                <div class="space-y-2">
                  <Label for="model">Model</Label>
                  <Input id="model" v-model="partForm.model" />
                </div>
                
                <div class="space-y-2">
                  <Label for="supplier_id">Supplier *</Label>
                  <select v-model="partForm.supplier_id" id="supplier_id" class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm" required>
                    <option value="">Select supplier</option>
                    <option v-for="supplier in suppliers" :key="supplier.id" :value="supplier.id">
                      {{ supplier.company_name }}
                    </option>
                  </select>
                  <div v-if="partForm.errors.supplier_id" class="text-sm text-red-500">{{ partForm.errors.supplier_id }}</div>
                </div>
              </div>

              <div v-if="!editingPart" class="space-y-4">
                <div class="flex justify-between items-center">
                  <Label>Documents</Label>
                  <Button type="button" size="sm" variant="outline" @click="addDocument">
                    <Plus class="w-4 h-4 mr-1" />
                    Add Document
                  </Button>
                </div>
                
                <div v-for="(doc, index) in partForm.documents" :key="index" class="border rounded p-3 space-y-2 bg-white">
                  <div class="flex justify-between items-center">
                    <Label>Document {{ index + 1 }}</Label>
                    <Button type="button" size="sm" variant="ghost" @click="removeDocument(index)">
                      <Trash2 class="w-4 h-4" />
                    </Button>
                  </div>
                  <div class="grid grid-cols-2 gap-2">
                    <Input v-model="doc.document_name" placeholder="Document Name" required />
                    <Input v-model="doc.document_type" placeholder="Document Type" required />
                    <Input v-model="doc.start_date" type="date" placeholder="Start Date" />
                    <Input v-model="doc.due_date" type="date" placeholder="Due Date" />
                  </div>
                </div>
              </div>
              
              <div class="flex gap-2">
                <Button type="submit" :disabled="partForm.processing">
                  {{ partForm.processing ? 'Saving...' : (editingPart ? 'Update Part' : 'Add Part') }}
                </Button>
                <Button type="button" variant="outline" @click="showPartForm = false">
                  Cancel
                </Button>
              </div>
            </form>
          </div>
          
          <Table>
            <TableHeader>
              <TableRow>
                <TableHead>Part Number</TableHead>
                <TableHead>Part Name</TableHead>
                <TableHead>Model</TableHead>
                <TableHead>Supplier</TableHead>
                <TableHead>Documents</TableHead>
                <TableHead>Actions</TableHead>
              </TableRow>
            </TableHeader>
            <TableBody>
              <TableRow v-for="part in project.parts" :key="part.id">
                <TableCell class="font-medium">{{ part.part_number }}</TableCell>
                <TableCell>{{ part.part_name }}</TableCell>
                <TableCell>{{ part.model || '-' }}</TableCell>
                <TableCell>{{ part.supplier?.company_name }}</TableCell>
                <TableCell>{{ part.required_documents?.length || 0 }}</TableCell>
                <TableCell>
                  <div class="flex gap-2">
                    <Link :href="`/parts/${part.id}`">
                      <Button size="sm" variant="ghost">
                        <FolderOpen class="w-4 h-4" />
                      </Button>
                    </Link>
                    <template v-if="userRole === 'reviewer' || userRole === 'superadmin'">
                      <Button size="sm" variant="ghost" @click="openPartForm(part)">
                        <Pencil class="w-4 h-4" />
                      </Button>
                      <Button size="sm" variant="ghost" @click="deletePart(part.id)">
                        <Trash2 class="w-4 h-4 text-red-500" />
                      </Button>
                    </template>
                  </div>
                </TableCell>
              </TableRow>
              <TableRow v-if="!project.parts || project.parts.length === 0">
                <TableCell colspan="6" class="text-center text-muted-foreground">
                  No parts added yet
                </TableCell>
              </TableRow>
            </TableBody>
          </Table>
        </CardContent>
      </Card>
    </div>
  </AuthenticatedLayout>
</template>
