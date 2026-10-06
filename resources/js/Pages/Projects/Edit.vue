<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { useForm } from '@inertiajs/vue3'

interface Props {
  project: any
  suppliers: any[]
}

const props = defineProps<Props>()

const form = useForm({
  project_code: props.project.project_code,
  project_name: props.project.project_name,
  supplier_id: props.project.supplier_id,
  start_date: props.project.start_date,
  due_date: props.project.due_date,
  status: props.project.status,
})

const submit = () => {
  form.put(`/projects/${props.project.id}`)
}
</script>

<template>
  <AuthenticatedLayout>
    <div class="space-y-6">
      <div>
        <h1 class="text-2xl font-bold">Edit Project</h1>
        <p class="text-muted-foreground">Update project information</p>
      </div>

      <Card>
        <CardHeader>
          <CardTitle>Project Information</CardTitle>
        </CardHeader>
        <CardContent>
          <form @submit.prevent="submit" class="space-y-4">
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

              <div class="space-y-2">
                <Label for="status">Status *</Label>
                <select v-model="form.status" id="status" class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50" required>
                  <option value="active">Active</option>
                  <option value="completed">Completed</option>
                  <option value="cancelled">Cancelled</option>
                </select>
                <div v-if="form.errors.status" class="text-sm text-red-500">{{ form.errors.status }}</div>
              </div>
            </div>

            <div class="flex gap-3">
              <Button type="submit" :disabled="form.processing">
                {{ form.processing ? 'Updating...' : 'Update Project' }}
              </Button>
              <Button type="button" variant="outline" @click="$inertia.visit(`/projects/${project.id}`)">
                Cancel
              </Button>
            </div>
          </form>
        </CardContent>
      </Card>
    </div>
  </AuthenticatedLayout>
</template>
