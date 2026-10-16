<script setup lang="ts">
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table'
import { Pencil, Trash2 } from 'lucide-vue-next'
import ConfirmDialog from '@/Components/ConfirmDialog.vue'
import { useConfirm } from '@/composables/useConfirm'

interface Role {
  id: number
  name: string
  level: number
  code: string
  created_at: string
  updated_at: string
}

const props = defineProps<{
  roles: Role[]
}>()

const { confirm } = useConfirm()

const showForm = ref(false)
const editingRole = ref<Role | null>(null)
const form = ref({
  name: '',
  level: ''
})

const openCreateForm = () => {
  editingRole.value = null
  form.value = { name: '', level: '' }
  showForm.value = true
}

const openEditForm = (role: Role) => {
  editingRole.value = role
  form.value = {
    name: role.name,
    level: role.level.toString()
  }
  showForm.value = true
}

const closeForm = () => {
  showForm.value = false
  editingRole.value = null
  form.value = { name: '', level: '' }
}

const submitForm = () => {
  if (editingRole.value) {
    router.put(`/admin/roles/${editingRole.value.id}`, form.value, {
      onSuccess: () => closeForm()
    })
  } else {
    router.post('/admin/roles', form.value, {
      onSuccess: () => closeForm()
    })
  }
}

const deleteRole = async (id: number) => {
  const confirmed = await confirm({
    title: 'Hapus Role',
    description: 'Apakah Anda yakin ingin menghapus role ini? Role yang masih digunakan oleh user tidak dapat dihapus.',
    confirmText: 'Hapus',
    cancelText: 'Batal',
    variant: 'danger'
  })

  if (confirmed) {
    router.delete(`/admin/roles/${id}`)
  }
}
</script>

<template>
  <AuthenticatedLayout>
    <div class="p-6 space-y-6">
      <div class="flex justify-between items-center">
        <h1 class="text-2xl font-bold">Role Management</h1>
        <Button @click="openCreateForm">Tambah Role</Button>
      </div>

      <Card v-if="showForm">
        <CardHeader>
          <CardTitle>{{ editingRole ? 'Edit Role' : 'Tambah Role Baru' }}</CardTitle>
        </CardHeader>
        <CardContent>
          <form @submit.prevent="submitForm" class="space-y-4">
            <div>
              <Label for="level">Level</Label>
              <select v-model="form.level" id="level" class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50" required>
                <option value="">Pilih level</option>
                <option value="1">Level 1</option>
                <option value="2">Level 2</option>
                <option value="3">Level 3</option>
              </select>
              <p class="text-xs text-muted-foreground mt-1">Level 1 = terendah, Level 3 = tertinggi (Manager)</p>
            </div>
            
            <div>
              <Label for="name">Nama Posisi</Label>
              <Input id="name" v-model="form.name" placeholder="e.g., PIC Development, Quality Manager, Direksi" required />
              <p class="text-xs text-muted-foreground mt-1">Bebas diisi sesuai kebutuhan, bisa ada beberapa role dengan level yang sama</p>
            </div>
            
            <div class="flex gap-2">
              <Button type="submit">{{ editingRole ? 'Update' : 'Simpan' }}</Button>
              <Button type="button" variant="outline" @click="closeForm">Batal</Button>
            </div>
          </form>
        </CardContent>
      </Card>

      <Card>
        <CardHeader>
          <CardTitle>Daftar Role ({{ roles.length }})</CardTitle>
        </CardHeader>
        <CardContent>
          <Table>
            <TableHeader>
              <TableRow>
                <TableHead class="w-24">Level</TableHead>
                <TableHead class="w-auto">Nama Posisi</TableHead>
                <TableHead class="w-32">Action</TableHead>
              </TableRow>
            </TableHeader>
            <TableBody>
              <TableRow v-for="role in roles" :key="role.id">
                <TableCell class="w-24">{{ role.level }}</TableCell>
                <TableCell class="w-auto font-medium">{{ role.name }}</TableCell>
                <TableCell class="w-32">
                  <div class="flex gap-2">
                    <Button size="sm" variant="ghost" @click="openEditForm(role)">
                      <Pencil class="w-4 h-4" />
                    </Button>
                    <Button size="sm" variant="ghost" @click="deleteRole(role.id)">
                      <Trash2 class="w-4 h-4" />
                    </Button>
                  </div>
                </TableCell>
              </TableRow>
            </TableBody>
          </Table>
        </CardContent>
      </Card>
    </div>

    <ConfirmDialog />
  </AuthenticatedLayout>
</template>
