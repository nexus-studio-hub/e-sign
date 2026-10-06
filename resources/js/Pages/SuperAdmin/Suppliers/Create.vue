<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { Textarea } from '@/components/ui/textarea'
import { useForm } from '@inertiajs/vue3'

const form = useForm({
  code: '',
  company_name: '',
  contact_person: '',
  email: '',
  phone: '',
  address: '',
  status: 'active',
})

const submit = () => {
  form.post('/admin/suppliers')
}
</script>

<template>
  <AuthenticatedLayout>
    <div class="space-y-6">
      <div>
        <h1 class="text-2xl font-bold">Create Supplier</h1>
        <p class="text-muted-foreground">Add a new supplier to the system</p>
      </div>

      <Card>
        <CardHeader>
          <CardTitle>Supplier Information</CardTitle>
        </CardHeader>
        <CardContent>
          <form @submit.prevent="submit" class="space-y-4">
            <div class="grid grid-cols-2 gap-4">
              <div class="space-y-2">
                <Label for="code">Supplier Code *</Label>
                <Input id="code" v-model="form.code" required />
                <div v-if="form.errors.code" class="text-sm text-red-500">{{ form.errors.code }}</div>
              </div>

              <div class="space-y-2">
                <Label for="company_name">Company Name *</Label>
                <Input id="company_name" v-model="form.company_name" required />
                <div v-if="form.errors.company_name" class="text-sm text-red-500">{{ form.errors.company_name }}</div>
              </div>

              <div class="space-y-2">
                <Label for="contact_person">Contact Person *</Label>
                <Input id="contact_person" v-model="form.contact_person" required />
                <div v-if="form.errors.contact_person" class="text-sm text-red-500">{{ form.errors.contact_person }}</div>
              </div>

              <div class="space-y-2">
                <Label for="email">Email *</Label>
                <Input id="email" v-model="form.email" type="email" required />
                <div v-if="form.errors.email" class="text-sm text-red-500">{{ form.errors.email }}</div>
              </div>

              <div class="space-y-2">
                <Label for="phone">Phone *</Label>
                <Input id="phone" v-model="form.phone" required />
                <div v-if="form.errors.phone" class="text-sm text-red-500">{{ form.errors.phone }}</div>
              </div>

              <div class="space-y-2">
                <Label for="status">Status *</Label>
                <select v-model="form.status" id="status" class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50" required>
                  <option value="active">Active</option>
                  <option value="inactive">Inactive</option>
                </select>
                <div v-if="form.errors.status" class="text-sm text-red-500">{{ form.errors.status }}</div>
              </div>

              <div class="space-y-2 col-span-2">
                <Label for="address">Address</Label>
                <Textarea id="address" v-model="form.address" :rows="3" />
                <div v-if="form.errors.address" class="text-sm text-red-500">{{ form.errors.address }}</div>
              </div>
            </div>

            <div class="flex gap-3">
              <Button type="submit" :disabled="form.processing">
                {{ form.processing ? 'Creating...' : 'Create Supplier' }}
              </Button>
              <Button type="button" variant="outline" @click="$inertia.visit('/admin/suppliers')">
                Cancel
              </Button>
            </div>
          </form>
        </CardContent>
      </Card>
    </div>
  </AuthenticatedLayout>
</template>
