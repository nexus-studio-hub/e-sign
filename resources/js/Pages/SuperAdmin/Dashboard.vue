<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card'
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table'
import { Button } from '@/components/ui/button'
import { Link } from '@inertiajs/vue3'
import { Users, Building, FolderOpen, FileText, UserCheck, Plus } from 'lucide-vue-next'

interface Props {
  stats: {
    totalUsers: number
    totalSuppliers: number
    totalProjects: number
    totalDocuments: number
    activeUsers: number
    usersByRole: Array<{ role: string; total: number }>
  }
}

defineProps<Props>()
</script>

<template>
  <AuthenticatedLayout>
    <div class="space-y-6">
      <div>
        <h1 class="text-2xl font-bold">SuperAdmin Dashboard</h1>
        <p class="text-muted-foreground">Manage users, suppliers, and system overview</p>
      </div>

      <!-- Stats Cards -->
      <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-5">
        <Card>
          <CardContent class="p-6">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm font-medium text-muted-foreground">Total Users</p>
                <p class="text-2xl font-bold">{{ stats.totalUsers }}</p>
              </div>
              <div class="w-12 h-12 rounded-lg bg-blue-500/10 flex items-center justify-center">
                <Users class="w-6 h-6 text-blue-500" />
              </div>
            </div>
          </CardContent>
        </Card>

        <Card>
          <CardContent class="p-6">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm font-medium text-muted-foreground">Active Users</p>
                <p class="text-2xl font-bold">{{ stats.activeUsers }}</p>
              </div>
              <div class="w-12 h-12 rounded-lg bg-green-500/10 flex items-center justify-center">
                <UserCheck class="w-6 h-6 text-green-500" />
              </div>
            </div>
          </CardContent>
        </Card>

        <Card>
          <CardContent class="p-6">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm font-medium text-muted-foreground">Suppliers</p>
                <p class="text-2xl font-bold">{{ stats.totalSuppliers }}</p>
              </div>
              <div class="w-12 h-12 rounded-lg bg-purple-500/10 flex items-center justify-center">
                <Building class="w-6 h-6 text-purple-500" />
              </div>
            </div>
          </CardContent>
        </Card>

        <Card>
          <CardContent class="p-6">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm font-medium text-muted-foreground">Projects</p>
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
                <p class="text-sm font-medium text-muted-foreground">Documents</p>
                <p class="text-2xl font-bold">{{ stats.totalDocuments }}</p>
              </div>
              <div class="w-12 h-12 rounded-lg bg-orange-500/10 flex items-center justify-center">
                <FileText class="w-6 h-6 text-orange-500" />
              </div>
            </div>
          </CardContent>
        </Card>
      </div>

      <!-- Users by Role -->
      <Card>
        <CardHeader>
          <CardTitle>Users by Role</CardTitle>
        </CardHeader>
        <CardContent>
          <Table>
            <TableHeader>
              <TableRow>
                <TableHead>Role</TableHead>
                <TableHead>Total Users</TableHead>
              </TableRow>
            </TableHeader>
            <TableBody>
              <TableRow v-for="role in stats.usersByRole" :key="role.role">
                <TableCell class="font-medium">{{ role.role }}</TableCell>
                <TableCell>{{ role.total }}</TableCell>
              </TableRow>
            </TableBody>
          </Table>
        </CardContent>
      </Card>

      <!-- Quick Actions -->
      <div class="flex gap-4">
        <Link href="/admin/users/create">
          <Button>
            <Plus class="w-4 h-4 mr-2" />
            Add User
          </Button>
        </Link>
        <Link href="/admin/suppliers/create">
          <Button variant="outline">
            <Plus class="w-4 h-4 mr-2" />
            Add Supplier
          </Button>
        </Link>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
