<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card'
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table'
import { Badge } from '@/components/ui/badge'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Link, router } from '@inertiajs/vue3'
import { Plus, Pencil, Trash2 } from 'lucide-vue-next'
import { ref, watch } from 'vue'

interface Props {
  suppliers: {
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

const searchQuery = ref('')
const debounceTimer = ref<number | null>(null)

watch(searchQuery, (value) => {
  if (debounceTimer.value) {
    clearTimeout(debounceTimer.value)
  }
  
  debounceTimer.value = setTimeout(() => {
    router.get('/admin/suppliers', { search: value }, { 
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

const deleteSupplier = (id: number) => {
  if (confirm('Are you sure you want to delete this supplier?')) {
    router.delete(`/admin/suppliers/${id}`, {
      preserveState: true,
    })
  }
}
</script>

<template>
  <AuthenticatedLayout>
    <div class="space-y-6">
      <div class="flex justify-between items-center">
        <div>
          <h1 class="text-2xl font-bold">Supplier Management</h1>
          <p class="text-muted-foreground">Manage suppliers and their information</p>
        </div>
        <Link href="/admin/suppliers/create">
          <Button>
            <Plus class="w-4 h-4 mr-2" />
            Add Supplier
          </Button>
        </Link>
      </div>

      <Card>
        <CardHeader>
          <div class="flex justify-between items-center">
            <CardTitle>All Suppliers</CardTitle>
            <div class="w-64">
              <Input
                v-model="searchQuery"
                placeholder="Search suppliers..."
                class="h-9"
              />
            </div>
          </div>
        </CardHeader>
        <CardContent>
          <Table>
            <TableHeader>
              <TableRow>
                <TableHead>Code</TableHead>
                <TableHead>Company Name</TableHead>
                <TableHead>Contact Person</TableHead>
                <TableHead>Email</TableHead>
                <TableHead>Phone</TableHead>
                <TableHead>Parts</TableHead>
                <TableHead>Status</TableHead>
                <TableHead>Actions</TableHead>
              </TableRow>
            </TableHeader>
            <TableBody>
              <TableRow v-for="supplier in suppliers.data" :key="supplier.id">
                <TableCell class="font-medium">{{ supplier.code }}</TableCell>
                <TableCell>{{ supplier.company_name }}</TableCell>
                <TableCell>{{ supplier.contact_person }}</TableCell>
                <TableCell>{{ supplier.email }}</TableCell>
                <TableCell>{{ supplier.phone }}</TableCell>
                <TableCell>{{ supplier.parts_count }}</TableCell>
                <TableCell>
                  <Badge :class="supplier.status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'">
                    {{ supplier.status }}
                  </Badge>
                </TableCell>
                <TableCell>
                  <div class="flex gap-2">
                    <Link :href="`/admin/suppliers/${supplier.id}/edit`">
                      <Button size="sm" variant="outline">
                        <Pencil class="w-4 h-4" />
                      </Button>
                    </Link>
                    <Button 
                      size="sm" 
                      variant="outline"
                      @click="deleteSupplier(supplier.id)"
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
              Showing {{ suppliers.from }} to {{ suppliers.to }} of {{ suppliers.total }} results
            </div>
            <div class="flex gap-2">
              <Button 
                v-for="link in suppliers.links" 
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
