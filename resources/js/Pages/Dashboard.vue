<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card'
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table'
import { Button } from '@/components/ui/button'
import { Users, Calendar, Clock, TrendingUp, Activity, Eye, ArrowUpRight } from 'lucide-vue-next'

interface Props {
  stats: {
    totalUsers: number
    todayActivity: number
    pendingTasks: number
    monthlyRevenue: number
  }
  recentItems: Array<{
    id: number
    code: string
    name: string
    value: number
    category: string
    date: string
    status: string
  }>
}

defineProps<Props>()
</script>

<template>
  <AuthenticatedLayout>
    <div class="space-y-6">
      <!-- Stats Cards -->
      <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
        <Card class="bg-white shadow-sm">
          <CardContent class="p-6">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm font-medium text-muted-foreground">Total Users</p>
                <p class="text-2xl font-bold">{{ stats.totalUsers }}</p>
              </div>
              <div class="w-12 h-12 rounded-lg bg-cyan-500/10 flex items-center justify-center">
                <Users class="w-6 h-6 text-cyan-500" />
              </div>
            </div>
          </CardContent>
        </Card>

        <Card class="bg-white shadow-sm">
          <CardContent class="p-6">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm font-medium text-muted-foreground">Today's Activity</p>
                <p class="text-2xl font-bold">{{ stats.todayActivity }}</p>
              </div>
              <div class="w-12 h-12 rounded-lg bg-cyan-500/10 flex items-center justify-center">
                <Calendar class="w-6 h-6 text-cyan-500" />
              </div>
            </div>
          </CardContent>
        </Card>

        <Card class="bg-white shadow-sm">
          <CardContent class="p-6">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm font-medium text-muted-foreground">Pending Tasks</p>
                <p class="text-2xl font-bold">{{ stats.pendingTasks }}</p>
              </div>
              <div class="w-12 h-12 rounded-lg bg-cyan-500/10 flex items-center justify-center">
                <Clock class="w-6 h-6 text-cyan-500" />
              </div>
            </div>
          </CardContent>
        </Card>

        <Card class="bg-white shadow-sm">
          <CardContent class="p-6">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm font-medium text-muted-foreground">Revenue</p>
                <p class="text-2xl font-bold">${{ (stats.monthlyRevenue / 1000).toFixed(1) }}K</p>
              </div>
              <div class="w-12 h-12 rounded-lg bg-cyan-500/10 flex items-center justify-center">
                <TrendingUp class="w-6 h-6 text-cyan-500" />
              </div>
            </div>
          </CardContent>
        </Card>
      </div>

      <!-- Main Content Grid -->
      <div class="grid gap-6 lg:grid-cols-7">
        <!-- Chart Section -->
        <Card class="lg:col-span-4 bg-white shadow-sm">
          <CardHeader>
            <CardTitle class="text-base">Activity Overview</CardTitle>
            <CardDescription>Your activity statistics</CardDescription>
          </CardHeader>
          <CardContent>
            <div class="h-[300px] flex items-center justify-center text-muted-foreground">
              Chart placeholder - integrate your preferred chart library
            </div>
          </CardContent>
        </Card>

        <!-- Quick Stats -->
        <Card class="lg:col-span-3 bg-white shadow-sm">
          <CardHeader>
            <CardTitle class="text-base">Quick Statistics</CardTitle>
          </CardHeader>
          <CardContent class="space-y-4">
            <div class="flex items-center justify-between p-3 rounded-lg bg-white shadow-sm">
              <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-cyan-500/10 flex items-center justify-center">
                  <Activity class="w-5 h-5 text-cyan-500" />
                </div>
                <div>
                  <p class="text-sm font-medium">Active Items</p>
                  <p class="text-xs text-muted-foreground">This month</p>
                </div>
              </div>
              <p class="text-lg font-semibold">248</p>
            </div>

            <div class="flex items-center justify-between p-3 rounded-lg bg-white shadow-sm">
              <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-cyan-500/10 flex items-center justify-center">
                  <Calendar class="w-5 h-5 text-cyan-500" />
                </div>
                <div>
                  <p class="text-sm font-medium">Scheduled</p>
                  <p class="text-xs text-muted-foreground">This week</p>
                </div>
              </div>
              <p class="text-lg font-semibold">42</p>
            </div>

            <div class="flex items-center justify-between p-3 rounded-lg bg-white shadow-sm">
              <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-cyan-500/10 flex items-center justify-center">
                  <Users class="w-5 h-5 text-cyan-500" />
                </div>
                <div>
                  <p class="text-sm font-medium">New Users</p>
                  <p class="text-xs text-muted-foreground">This month</p>
                </div>
              </div>
              <p class="text-lg font-semibold">18</p>
            </div>
          </CardContent>
        </Card>
      </div>

      <!-- Recent Items Table -->
      <Card class="bg-white shadow-sm">
        <CardHeader class="flex flex-row items-center justify-between">
          <div>
            <CardTitle class="text-base">Recent Items</CardTitle>
            <CardDescription>Latest activity in your dashboard</CardDescription>
          </div>
          <Button variant="ghost" size="sm" class="gap-1">
            View All
            <ArrowUpRight class="w-4 h-4" />
          </Button>
        </CardHeader>
        <CardContent>
          <Table>
            <TableHeader>
              <TableRow>
                <TableHead>Code</TableHead>
                <TableHead>Name</TableHead>
                <TableHead>Value</TableHead>
                <TableHead>Category</TableHead>
                <TableHead>Date</TableHead>
                <TableHead>Status</TableHead>
                <TableHead class="text-right">Action</TableHead>
              </TableRow>
            </TableHeader>
            <TableBody>
              <TableRow v-for="item in recentItems" :key="item.id">
                <TableCell class="font-medium">{{ item.code }}</TableCell>
                <TableCell>{{ item.name }}</TableCell>
                <TableCell>{{ item.value }}</TableCell>
                <TableCell>{{ item.category }}</TableCell>
                <TableCell>{{ item.date }}</TableCell>
                <TableCell>
                  <span 
                    :class="[
                      'inline-flex items-center px-2 py-1 rounded-md text-xs font-medium',
                      item.status === 'active' 
                        ? 'bg-primary/10 text-primary' 
                        : 'bg-muted text-muted-foreground'
                    ]"
                  >
                    {{ item.status }}
                  </span>
                </TableCell>
                <TableCell class="text-right">
                  <Button variant="ghost" size="sm">
                    <Eye class="w-4 h-4" />
                  </Button>
                </TableCell>
              </TableRow>
            </TableBody>
          </Table>
        </CardContent>
      </Card>
    </div>
  </AuthenticatedLayout>
</template>
