<script setup lang="ts">
import { ref } from 'vue'
import { useForm } from '@inertiajs/vue3'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card'
import { FileText, Copy, Check } from 'lucide-vue-next'

interface DemoAccount {
  email: string
  role: string
  name: string
}

defineProps<{
  demoAccounts: DemoAccount[]
}>()

const form = useForm({
  email: '',
  password: '',
  remember: false
})

const copiedIndex = ref<number | null>(null)
const copiedPasswordAdmin = ref(false)
const copiedPasswordOthers = ref(false)

const copyToClipboard = async (text: string, type: 'email' | 'password-admin' | 'password-others', index?: number) => {
  try {
    await navigator.clipboard.writeText(text)
    if (type === 'email' && index !== undefined) {
      copiedIndex.value = index
      setTimeout(() => copiedIndex.value = null, 2000)
    } else if (type === 'password-admin') {
      copiedPasswordAdmin.value = true
      setTimeout(() => copiedPasswordAdmin.value = false, 2000)
    } else if (type === 'password-others') {
      copiedPasswordOthers.value = true
      setTimeout(() => copiedPasswordOthers.value = false, 2000)
    }
  } catch (err) {
    console.error('Failed to copy:', err)
  }
}

const getRoleLabel = (role: string) => {
  const labels: Record<string, string> = {
    'superadmin': 'SuperAdmin',
    'pic_development': 'PIC',
    'group_leader': 'Leader',
    'manager': 'Manager',
    'supplier': 'Supplier'
  }
  return labels[role] || role
}

const submit = () => {
  form.post('/login')
}
</script>

<template>
  <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-cyan-50 to-teal-50">
    <Card class="w-full max-w-md">
      <CardHeader class="text-center">
        <div class="flex justify-center mb-4">
          <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-cyan-500 to-teal-500 flex items-center justify-center">
            <FileText class="w-8 h-8 text-white" />
          </div>
        </div>
        <CardTitle class="text-2xl">E-Sign System</CardTitle>
        <CardDescription>Electronic Supplier Document Control</CardDescription>
      </CardHeader>
      <CardContent>
        <form @submit.prevent="submit" class="space-y-4">
          <div class="space-y-2">
            <Label for="email">Email</Label>
            <Input 
              id="email" 
              v-model="form.email" 
              type="email" 
              placeholder="pic@esign.local"
              required 
            />
            <div v-if="form.errors.email" class="text-sm text-red-500">{{ form.errors.email }}</div>
          </div>

          <div class="space-y-2">
            <Label for="password">Password</Label>
            <Input 
              id="password" 
              v-model="form.password" 
              type="password" 
              placeholder="••••••••"
              required 
            />
          </div>

          <Button type="submit" class="w-full" :disabled="form.processing">
            {{ form.processing ? 'Logging in...' : 'Login' }}
          </Button>

          <div v-if="demoAccounts && demoAccounts.length > 0" class="mt-6 p-4 bg-gray-50 rounded-lg text-sm">
            <p class="font-semibold mb-3">Demo Accounts:</p>
            <div class="space-y-2 max-h-60 overflow-y-auto">
              <div 
                v-for="(account, index) in demoAccounts" 
                :key="account.email"
                class="flex items-center justify-between gap-2 py-1"
              >
                <div class="flex-1 min-w-0">
                  <div class="text-xs font-medium text-gray-700">{{ getRoleLabel(account.role) }}</div>
                  <div class="text-xs text-gray-600 truncate">{{ account.email }}</div>
                </div>
                <Button
                  type="button"
                  variant="ghost"
                  size="sm"
                  class="h-7 px-2 flex-shrink-0"
                  @click="copyToClipboard(account.email, 'email', index)"
                >
                  <Check v-if="copiedIndex === index" class="w-3 h-3 text-green-600" />
                  <Copy v-else class="w-3 h-3" />
                </Button>
              </div>
            </div>
            <div class="space-y-2 pt-3 border-t border-gray-200">
              <div class="flex items-center justify-between gap-2">
                <span class="text-xs text-gray-600 font-medium">Admin Password: password</span>
                <Button
                  type="button"
                  variant="ghost"
                  size="sm"
                  class="h-7 px-2"
                  @click="copyToClipboard('password', 'password-admin')"
                >
                  <Check v-if="copiedPasswordAdmin" class="w-3 h-3 text-green-600" />
                  <Copy v-else class="w-3 h-3" />
                </Button>
              </div>
              <div class="flex items-center justify-between gap-2">
                <span class="text-xs text-gray-600 font-medium">Others Password: asdfasdf</span>
                <Button
                  type="button"
                  variant="ghost"
                  size="sm"
                  class="h-7 px-2"
                  @click="copyToClipboard('asdfasdf', 'password-others')"
                >
                  <Check v-if="copiedPasswordOthers" class="w-3 h-3 text-green-600" />
                  <Copy v-else class="w-3 h-3" />
                </Button>
              </div>
            </div>
          </div>
        </form>
      </CardContent>
    </Card>
  </div>
</template>
