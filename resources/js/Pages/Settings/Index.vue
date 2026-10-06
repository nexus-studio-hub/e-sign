<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, Link } from '@inertiajs/vue3'
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card'
import { Button } from '@/components/ui/button'
</script>

<template>
  <Head title="Settings" />

  <AuthenticatedLayout>
    <div class="space-y-6">
      <div>
        <h1 class="text-3xl font-bold">Settings</h1>
        <p class="text-muted-foreground mt-2">Manage your application settings</p>
      </div>

      <div class="grid gap-6 md:grid-cols-2">
        <Card>
          <CardHeader>
            <CardTitle>Role Management</CardTitle>
            <CardDescription>Kelola role reviewer (PIC, Group Leader, Manager)</CardDescription>
          </CardHeader>
          <CardContent>
            <Link href="/admin/roles">
              <Button>Manage Roles</Button>
            </Link>
          </CardContent>
        </Card>

        <Card>
          <CardHeader>
            <CardTitle>User Management</CardTitle>
            <CardDescription>Kelola user dan assign role</CardDescription>
          </CardHeader>
          <CardContent>
            <Link href="/admin/users">
              <Button>Manage Users</Button>
            </Link>
          </CardContent>
        </Card>

        <Card>
          <CardHeader>
            <CardTitle>Supplier Management</CardTitle>
            <CardDescription>Kelola data supplier</CardDescription>
          </CardHeader>
          <CardContent>
            <Link href="/admin/suppliers">
              <Button>Manage Suppliers</Button>
            </Link>
          </CardContent>
        </Card>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
