<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import SearchableSelect from '@/Components/SearchableSelect.vue'
import { useForm } from '@inertiajs/vue3'
import { ref, watch, computed } from 'vue'

interface Role {
  id: number
  name: string
  level: number
}

interface Props {
  user: any
  suppliers: any[]
  roles: Role[]
}

const props = defineProps<Props>()

const form = useForm({
  name: props.user.name,
  email: props.user.email,
  password: '',
  role: props.user.role,
  role_id: props.user.role_id || '',
  supplier_id: props.user.supplier_id || '',
  is_active: props.user.is_active,
})

const showSupplierField = ref(props.user.role === 'supplier')
const showRoleField = ref(props.user.role === 'reviewer')

const roleOptions = computed(() => {
  return props.roles.map(role => ({
    value: role.id,
    label: `Level ${role.level} - ${role.name}`
  }))
})

watch(() => form.role, (value) => {
  showSupplierField.value = value === 'supplier'
  showRoleField.value = value === 'reviewer'
  
  if (value !== 'supplier') {
    form.supplier_id = ''
  }
  if (value !== 'reviewer') {
    form.role_id = ''
  }
})

const submit = () => {
  form.put(`/admin/users/${props.user.id}`)
}
</script>

<template>
  <AuthenticatedLayout>
    <div class="space-y-6">
      <div>
        <h1 class="text-2xl font-bold">Edit User</h1>
        <p class="text-muted-foreground">Update user information</p>
      </div>

      <Card class="overflow-visible">
        <CardHeader>
          <CardTitle>User Information</CardTitle>
        </CardHeader>
        <CardContent class="overflow-visible">
          <form @submit.prevent="submit" class="space-y-4">
            <div class="grid grid-cols-2 gap-4">
              <div class="space-y-2">
                <Label for="name">Nama Pegawai *</Label>
                <Input id="name" v-model="form.name" required />
                <div v-if="form.errors.name" class="text-sm text-red-500">{{ form.errors.name }}</div>
              </div>

              <div class="space-y-2">
                <Label for="email">Email *</Label>
                <Input id="email" v-model="form.email" type="email" required />
                <div v-if="form.errors.email" class="text-sm text-red-500">{{ form.errors.email }}</div>
              </div>

              <div class="space-y-2">
                <Label for="password">Password (leave blank to keep current)</Label>
                <Input id="password" v-model="form.password" type="password" />
                <div v-if="form.errors.password" class="text-sm text-red-500">{{ form.errors.password }}</div>
              </div>

              <div class="space-y-2">
                <Label for="role">Tipe User *</Label>
                <select v-model="form.role" id="role" class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50" required>
                  <option value="reviewer">Reviewer</option>
                  <option value="supplier">Supplier</option>
                </select>
                <div v-if="form.errors.role" class="text-sm text-red-500">{{ form.errors.role }}</div>
              </div>

              <div v-if="showRoleField" class="space-y-2">
                <Label for="role_id">Posisi</Label>
                <SearchableSelect
                  v-model="form.role_id"
                  :options="roleOptions"
                  placeholder="Pilih posisi"
                />
                <div v-if="form.errors.role_id" class="text-sm text-red-500">{{ form.errors.role_id }}</div>
              </div>

              <div v-if="showSupplierField" class="space-y-2 col-span-2">
                <Label for="supplier_id">Supplier *</Label>
                <select v-model="form.supplier_id" id="supplier_id" class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50" required>
                  <option value="">Select supplier</option>
                  <option v-for="supplier in suppliers" :key="supplier.id" :value="supplier.id">
                    {{ supplier.company_name }}
                  </option>
                </select>
                <div v-if="form.errors.supplier_id" class="text-sm text-red-500">{{ form.errors.supplier_id }}</div>
              </div>

              <div class="space-y-2 col-span-2">
                <div class="flex items-center gap-2">
                  <input type="checkbox" id="is_active" v-model="form.is_active" class="w-4 h-4" />
                  <Label for="is_active">Active</Label>
                </div>
              </div>
            </div>

            <div class="flex gap-3">
              <Button type="submit" :disabled="form.processing">
                {{ form.processing ? 'Updating...' : 'Update User' }}
              </Button>
              <Button type="button" variant="outline" @click="$inertia.visit('/admin/users')">
                Cancel
              </Button>
            </div>
          </form>
        </CardContent>
      </Card>
    </div>
  </AuthenticatedLayout>
</template>
