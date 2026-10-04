<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head } from '@inertiajs/vue3'
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card'
</script>

<template>
  <Head title="Settings" />

  <AuthenticatedLayout>
    <div class="space-y-6">
      <div>
        <h1 class="text-3xl font-bold">Settings</h1>
        <p class="text-muted-foreground mt-2">Manage your application settings</p>
      </div>

      <Card>
        <CardHeader>
          <CardTitle>General Settings</CardTitle>
        </CardHeader>
        <CardContent>
          <p class="text-sm text-muted-foreground">Settings page coming soon...</p>
        </CardContent>
      </Card>
    </div>
  </AuthenticatedLayout>
</template>
