<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card'
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table'
import { Badge } from '@/components/ui/badge'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Link, router } from '@inertiajs/vue3'
import { Plus, Pencil, Trash2, Power } from 'lucide-vue-next'
import { ref, watch } from 'vue'
import ConfirmDialog from '@/Components/ConfirmDialog.vue'
import { useConfirm } from '@/composables/useConfirm'

interface Props {
  users: {
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

const { confirm } = useConfirm()

const searchQuery = ref('')
const debounceTimer = ref<number | null>(null)

watch(searchQuery, (value) => {
  if (debounceTimer.value) {
    clearTimeout(debounceTimer.value)
  }
  
  debounceTimer.value = setTimeout(() => {
    router.get('/admin/users', { search: value }, { 
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

const deleteUser = async (id: number) => {
  const confirmed = await confirm({
    title: 'Hapus User',
    description: 'Apakah Anda yakin ingin menghapus user ini secara permanen? Tindakan ini tidak dapat dibatalkan.',
    confirmText: 'Hapus',
    cancelText: 'Batal',
    variant: 'danger'
  })

  if (confirmed) {
    router.delete(`/admin/users/${id}`)
  }
}

const toggleStatus = async (id: number, currentStatus: boolean) => {
  const action = currentStatus ? 'nonaktifkan' : 'aktifkan'
  const confirmed = await confirm({
    title: currentStatus ? 'Nonaktifkan User' : 'Aktifkan User',
    description: `Apakah Anda yakin ingin ${action} user ini?`,
    confirmText: currentStatus ? 'Nonaktifkan' : 'Aktifkan',
    cancelText: 'Batal',
    variant: currentStatus ? 'warning' : 'default'
  })

  if (confirmed) {
    router.post(`/admin/users/${id}/toggle-status`)
  }
}

const getRoleBadgeColor = (role: string) => {
  const colors: Record<string, string> = {
    superadmin: 'bg-red-100 text-red-800',
    reviewer: 'bg-blue-100 text-blue-800',
    supplier: 'bg-green-100 text-green-800',
  }
  return colors[role] || 'bg-gray-100 text-gray-800'
}

const getRoleDisplay = (user: any) => {
  if (user.role === 'superadmin') return 'Super Admin'
  if (user.role === 'supplier') return 'Supplier'
  if (user.role === 'reviewer' && user.reviewer_role) {
    return `${user.reviewer_role.name} (Level ${user.reviewer_role.level})`
  }
  return user.role
}
</script>

<template>
  <AuthenticatedLayout>
    <div class="space-y-6">
      <div class="flex justify-between items-center">
        <div>
          <h1 class="text-2xl font-bold">User Management</h1>
          <p class="text-muted-foreground">Manage system users and their roles</p>
        </div>
        <Link href="/admin/users/create">
          <Button>
            <Plus class="w-4 h-4 mr-2" />
            Add User
          </Button>
        </Link>
      </div>

      <Card>
        <CardHeader>
          <div class="flex justify-between items-center">
            <CardTitle>All Users</CardTitle>
            <div class="w-64">
              <Input
                v-model="searchQuery"
                placeholder="Search users..."
                class="h-9"
              />
            </div>
          </div>
        </CardHeader>
        <CardContent>
          <Table>
            <TableHeader>
              <TableRow>
                <TableHead>Name</TableHead>
                <TableHead>Email</TableHead>
                <TableHead>Role</TableHead>
                <TableHead>Supplier</TableHead>
                <TableHead>Status</TableHead>
                <TableHead>Actions</TableHead>
              </TableRow>
            </TableHeader>
            <TableBody>
              <TableRow v-for="user in users.data" :key="user.id">
                <TableCell class="font-medium">{{ user.name }}</TableCell>
                <TableCell>{{ user.email }}</TableCell>
                <TableCell>
                  <Badge :class="getRoleBadgeColor(user.role)">
                    {{ getRoleDisplay(user) }}
                  </Badge>
                </TableCell>
                <TableCell>{{ user.supplier?.company_name || '-' }}</TableCell>
                <TableCell>
                  <Badge :class="user.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'">
                    {{ user.is_active ? 'Active' : 'Inactive' }}
                  </Badge>
                </TableCell>
                <TableCell>
                  <div class="flex gap-2">
                    <Link :href="`/admin/users/${user.id}/edit`">
                      <Button size="sm" variant="outline">
                        <Pencil class="w-4 h-4" />
                      </Button>
                    </Link>
                    <Button 
                      v-if="user.role !== 'superadmin'"
                      size="sm" 
                      variant="outline"
                      @click="toggleStatus(user.id, user.is_active)"
                      :title="user.is_active ? 'Deactivate user' : 'Activate user'"
                    >
                      <Power :class="['w-4 h-4', user.is_active ? 'text-orange-500' : 'text-green-500']" />
                    </Button>
                    <Button 
                      v-if="user.role !== 'superadmin'"
                      size="sm" 
                      variant="outline"
                      @click="deleteUser(user.id)"
                    >
                      <Trash2 class="w-4 h-4 text-red-500" />
                    </Button>
                  </div>
                </TableCell>
              </TableRow>
            </TableBody>
          </Table>

          <!-- Pagination -->
          <div class="flex items-center justify-between mt-4">
            <div class="text-sm text-muted-foreground">
              Showing {{ users.from }} to {{ users.to }} of {{ users.total }} results
            </div>
            <div class="flex gap-2">
              <Button 
                v-for="link in users.links" 
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

    <ConfirmDialog />
  </AuthenticatedLayout>
</template>
