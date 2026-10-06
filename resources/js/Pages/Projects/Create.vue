<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { useForm } from '@inertiajs/vue3'
import { Plus, Trash2 } from 'lucide-vue-next'
import { ref } from 'vue'

interface Props {
  suppliers: any[]
}

const props = defineProps<Props>()

const form = useForm({
  project_code: '',
  project_name: '',
  supplier_id: '',
  start_date: '',
  due_date: '',
  documents: [
    { document_name: 'PPAP', document_type: 'PPAP', is_required: true },
    { document_name: 'FMEA', document_type: 'FMEA', is_required: true },
    { document_name: 'Control Plan', document_type: 'Control Plan', is_required: true },
    { document_name: 'Drawing', document_type: 'Drawing', is_required: true }
  ]
})

const addDocument = () => {
  form.documents.push({ document_name: '', document_type: '', is_required: true })
}

const removeDocument = (index: number) => {
  form.documents.splice(index, 1)
}

const submit = () => {
  form.post('/projects')
}
</script>

<template>
  <AuthenticatedLayout>
    <div class="space-y-6">
      <div>
        <h1 class="text-2xl font-bold">Create New Project</h1>
        <p class="text-muted-foreground">Setup a new project with required documents</p>
      </div>

      <form @submit.prevent="submit" class="space-y-6">
        <Card>
          <CardHeader>
            <CardTitle>Project Information</CardTitle>
          </CardHeader>
          <CardContent class="space-y-4">
            <div class="grid grid-cols-2 gap-4">
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

              <div class="space-y-2">
                <Label for="supplier_id">Supplier *</Label>
                <select v-model="form.supplier_id" id="supplier_id" class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50" required>
                  <option value="">Select supplier</option>
                  <option v-for="supplier in suppliers" :key="supplier.id" :value="supplier.id">
                    {{ supplier.company_name }}
                  </option>
                </select>
                <div v-if="form.errors.supplier_id" class="text-sm text-red-500">{{ form.errors.supplier_id }}</div>
              </div>

              <div class="space-y-2">
                <Label for="start_date">Start Date *</Label>
                <Input id="start_date" v-model="form.start_date" type="date" required />
                <div v-if="form.errors.start_date" class="text-sm text-red-500">{{ form.errors.start_date }}</div>
              </div>

              <div class="space-y-2">
                <Label for="due_date">Due Date *</Label>
                <Input id="due_date" v-model="form.due_date" type="date" required />
                <div v-if="form.errors.due_date" class="text-sm text-red-500">{{ form.errors.due_date }}</div>
              </div>
            </div>
          </CardContent>
        </Card>

        <Card>
          <CardHeader>
            <div class="flex justify-between items-center">
              <CardTitle>Required Documents</CardTitle>
              <Button type="button" size="sm" variant="outline" @click="addDocument">
                <Plus class="w-4 h-4 mr-2" />
                Add Document
              </Button>
            </div>
          </CardHeader>
          <CardContent class="space-y-3">
            <div v-for="(doc, index) in form.documents" :key="index" class="flex gap-3 items-start">
              <div class="flex-1 grid grid-cols-2 gap-3">
                <div class="space-y-2">
                  <Label>Document Name</Label>
                  <Input v-model="doc.document_name" placeholder="e.g. PPAP" required />
                </div>
                <div class="space-y-2">
                  <Label>Document Type</Label>
                  <Input v-model="doc.document_type" placeholder="e.g. PPAP" required />
                </div>
              </div>
              <Button 
                type="button" 
                size="icon" 
                variant="ghost" 
                class="mt-8"
                @click="removeDocument(index)"
                :disabled="form.documents.length === 1"
              >
                <Trash2 class="w-4 h-4 text-red-500" />
              </Button>
            </div>
            <div v-if="form.errors.documents" class="text-sm text-red-500">{{ form.errors.documents }}</div>
          </CardContent>
        </Card>

        <div class="flex gap-3">
          <Button type="submit" :disabled="form.processing">
            {{ form.processing ? 'Creating...' : 'Create Project' }}
          </Button>
          <Button type="button" variant="outline" @click="$inertia.visit('/projects')">
            Cancel
          </Button>
        </div>
      </form>
    </div>
  </AuthenticatedLayout>
</template>
