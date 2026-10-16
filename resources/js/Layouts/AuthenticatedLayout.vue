<script setup lang="ts">
import { ref, watch, computed } from 'vue'
import { Link, usePage, router } from '@inertiajs/vue3'
import { LayoutDashboard, Users, Building, FileText, Settings, Menu, Bell, HelpCircle, ChevronDown, LogOut } from 'lucide-vue-next'
import { Sheet, SheetContent } from '@/components/ui/sheet'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Card } from '@/components/ui/card'
import Breadcrumb from '@/Components/Breadcrumb.vue'
import NotificationDropdown from '@/Components/NotificationDropdown.vue'
import RecentActivity from '@/Components/RecentActivity.vue'

const page = usePage()

const userRole = computed(() => page.props.auth.user?.role)
const userRoleLevel = computed(() => page.props.auth.user?.reviewer_role?.level)

const mainMenuItems = computed(() => {
  const items = []
  
  if (userRole.value === 'superadmin') {
    items.push(
      { name: 'Dashboard', href: '/admin/dashboard', icon: LayoutDashboard },
      { name: 'Suppliers', href: '/admin/suppliers', icon: Building },
      { name: 'Projects', href: '/projects', icon: FileText },
    )
  } else if (userRole.value === 'reviewer') {
    items.push(
      { name: 'Dashboard', href: '/dashboard', icon: LayoutDashboard },
      { name: 'Projects', href: '/projects', icon: FileText },
    )
  } else if (userRole.value === 'supplier') {
    items.push(
      { name: 'Dashboard', href: '/dashboard', icon: LayoutDashboard },
      { name: 'Projects', href: '/projects', icon: FileText },
    )
  }
  
  return items
})

const settingsMenuItems = computed(() => {
  if (userRole.value === 'superadmin') {
    return [
      { name: 'Users', href: '/admin/users', icon: Users },
      { name: 'Settings', href: '/settings', icon: Settings },
    ]
  }
  return []
})

const mobileOpen = ref(false)
const openSubmenu = ref<string | null>(null)
const profileOpen = ref(false)

const logout = () => {
  router.post('/logout')
}

const toggleSubmenu = (itemName: string) => {
  openSubmenu.value = openSubmenu.value === itemName ? null : itemName
}

// Auto-expand submenu based on current URL
watch(() => page.url, (newUrl) => {
  mainMenuItems.value.forEach(item => {
    if (item.submenu) {
      const isInSubmenu = item.submenu.some(sub => newUrl === sub.href)
      if (isInSubmenu) {
        openSubmenu.value = item.name
      }
    }
  })
}, { immediate: true })

// Breadcrumb items based on current URL
const breadcrumbItems = computed(() => {
  const url = page.url
  const items: Array<{ label: string; href?: string; current?: boolean }> = []
  
  if (url === '/dashboard') {
    items.push({ label: 'Dashboard', current: true })
  } else if (url.startsWith('/projects')) {
    items.push({ label: 'Projects', current: true })
  } else if (url === '/settings') {
    items.push({ label: 'Settings', current: true })
  }
  
  return items
})
</script>

<template>
  <div class="flex h-screen bg-background">
    <!-- Desktop Sidebar -->
    <aside class="hidden lg:flex w-72 bg-card flex-col">
      <!-- Logo -->
      <div class="h-20 px-6 border-b flex items-center">
        <div class="flex items-center gap-2">
          <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-cyan-500 to-teal-500 flex items-center justify-center">
            <FileText class="w-4 h-4 text-white" />
          </div>
          <div class="flex flex-col">
            <span class="text-sm font-bold leading-none">E-SIGN</span>
            <span class="text-xs text-muted-foreground leading-none mt-0.5">Document Control</span>
          </div>
        </div>
      </div>

      <!-- Navigation -->
      <nav class="flex-1 px-4 pt-6 overflow-y-auto scrollbar-hide">
        <!-- Main Menu -->
        <div class="space-y-1">
          <template v-for="item in mainMenuItems" :key="item.href">
            <!-- Menu with submenu -->
            <div v-if="item.submenu">
              <button
                @click="toggleSubmenu(item.name)"
                class="w-full flex items-center gap-3 px-4 py-4 rounded-xl text-sm font-medium transition-all"
                :class="$page.url.startsWith(item.href)
                  ? 'bg-white shadow-sm text-foreground' 
                  : 'text-muted-foreground hover:bg-muted'"
              >
                <div 
                  :class="[
                    'w-8 h-8 rounded-lg flex items-center justify-center transition-colors',
                    $page.url.startsWith(item.href)
                      ? 'bg-gradient-to-br from-cyan-500 to-teal-500' 
                      : 'bg-white'
                  ]"
                >
                  <component 
                    :is="item.icon" 
                    :class="[
                      'w-4 h-4',
                      $page.url.startsWith(item.href) ? 'text-white' : 'text-cyan-500'
                    ]" 
                  />
                </div>
                <span class="flex-1 text-left">{{ item.name }}</span>
                <ChevronDown 
                  :class="[
                    'w-4 h-4 transition-transform',
                    openSubmenu === item.name ? 'rotate-180' : ''
                  ]"
                />
              </button>
              
              <!-- Submenu -->
              <transition
                enter-active-class="transition-all duration-200 ease-out"
                enter-from-class="opacity-0 -translate-y-2 max-h-0"
                enter-to-class="opacity-100 translate-y-0 max-h-96"
                leave-active-class="transition-all duration-200 ease-in"
                leave-from-class="opacity-100 translate-y-0 max-h-96"
                leave-to-class="opacity-0 -translate-y-2 max-h-0"
              >
                <div 
                  v-show="openSubmenu === item.name"
                  class="ml-8 mt-1 space-y-1 overflow-hidden relative"
                >
                  <!-- Vertical line from icon -->
                  <div class="absolute left-2 top-0 bottom-2 w-px bg-gray-300"></div>
                  
                  <Link
                    v-for="(subitem, index) in item.submenu"
                    :key="subitem.href"
                    :href="subitem.href"
                    class="flex items-center gap-3 pl-8 pr-3 py-2 rounded-lg text-sm transition-colors relative"
                    :class="$page.url === subitem.href
                      ? 'text-cyan-600 font-medium'
                      : 'text-muted-foreground hover:text-foreground'"
                  >
                    <!-- Horizontal branch line -->
                    <span class="absolute left-2 top-1/2 w-4 h-px bg-gray-300"></span>
                    {{ subitem.name }}
                    <span v-if="subitem.badge" class="ml-auto px-2 py-0.5 text-xs bg-cyan-100 text-cyan-800 rounded-full font-semibold">
                      {{ subitem.badge }}
                    </span>
                  </Link>
                </div>
              </transition>
            </div>
            
            <!-- Regular menu item -->
            <Link
              v-else
              :href="item.href"
              class="flex items-center gap-3 px-4 py-4 rounded-xl text-sm font-medium transition-all"
              :class="$page.url === item.href 
                ? 'bg-white shadow-sm text-foreground' 
                : 'text-muted-foreground hover:bg-muted'"
            >
              <div 
                :class="[
                  'w-8 h-8 rounded-lg flex items-center justify-center transition-colors',
                  $page.url === item.href 
                    ? 'bg-gradient-to-br from-cyan-500 to-teal-500' 
                    : 'bg-white'
                ]"
              >
                <component 
                  :is="item.icon" 
                  :class="[
                    'w-4 h-4',
                    $page.url === item.href ? 'text-white' : 'text-cyan-500'
                  ]" 
                />
              </div>
              <span>{{ item.name }}</span>
            </Link>
          </template>
        </div>

        <!-- Settings Section (SuperAdmin only) -->
        <div v-if="userRole === 'superadmin'" class="mt-8">
          <div class="px-3 mb-2">
            <p class="text-xs font-semibold text-muted-foreground uppercase tracking-wider">
              Settings
            </p>
          </div>
          <div class="space-y-1">
            <Link
              v-for="item in settingsMenuItems"
              :key="item.href"
              :href="item.href"
              class="flex items-center gap-3 px-4 py-4 rounded-xl text-sm font-medium transition-all"
              :class="$page.url === item.href 
                ? 'bg-white shadow-md text-foreground' 
                : 'text-muted-foreground hover:bg-muted'"
            >
              <div 
                :class="[
                  'w-8 h-8 rounded-lg flex items-center justify-center transition-colors',
                  $page.url === item.href 
                    ? 'bg-gradient-to-br from-cyan-500 to-teal-500' 
                    : 'bg-white'
                ]"
              >
                <component 
                  :is="item.icon" 
                  :class="[
                    'w-4 h-4',
                    $page.url === item.href ? 'text-white' : 'text-cyan-500'
                  ]" 
                />
              </div>
              <span>{{ item.name }}</span>
            </Link>
          </div>
        </div>

        <!-- Recent Activity -->
        <RecentActivity />
      </nav>

      <!-- User Profile -->
      <div class="p-4">
        <button class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-muted transition-colors">
          <div class="w-9 h-9 rounded-full bg-gradient-to-br from-cyan-500 to-teal-500 flex items-center justify-center">
            <span class="text-sm font-semibold text-white">{{ page.props.auth.user?.name?.substring(0, 2).toUpperCase() }}</span>
          </div>
          <div class="flex-1 text-left min-w-0">
            <p class="text-sm font-medium truncate">{{ page.props.auth.user?.name }}</p>
            <p class="text-xs text-muted-foreground truncate">{{ page.props.auth.user?.email }}</p>
          </div>
        </button>
      </div>
    </aside>

    <!-- Mobile Sidebar -->
    <Sheet v-model:open="mobileOpen">
      <SheetContent side="left" class="w-72 p-0">
        <div class="flex flex-col h-full">
          <!-- Logo -->
          <div class="h-20 px-6 border-b flex items-center">
            <div class="flex items-center gap-2">
              <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-cyan-500 to-teal-500 flex items-center justify-center">
                <FileText class="w-4 h-4 text-white" />
              </div>
              <div class="flex flex-col">
                <span class="text-sm font-bold leading-none">E-SIGN</span>
                <span class="text-xs text-muted-foreground leading-none mt-0.5">Document Control</span>
              </div>
            </div>
          </div>

          <!-- Navigation -->
          <nav class="flex-1 px-4 py-4 overflow-y-auto scrollbar-hide">
            <!-- Main Menu -->
            <div class="space-y-1">
              <template v-for="item in mainMenuItems" :key="item.href">
                <!-- Menu with submenu -->
                <div v-if="item.submenu">
                  <button
                    @click="toggleSubmenu(item.name)"
                    class="w-full flex items-center gap-3 px-4 py-4 rounded-xl text-sm font-medium transition-all"
                    :class="$page.url.startsWith(item.href)
                      ? 'bg-white shadow-sm text-foreground' 
                      : 'text-muted-foreground hover:bg-muted'"
                  >
                    <div 
                      :class="[
                        'w-8 h-8 rounded-lg flex items-center justify-center transition-colors',
                        $page.url.startsWith(item.href)
                          ? 'bg-gradient-to-br from-cyan-500 to-teal-500' 
                          : 'bg-white'
                      ]"
                    >
                      <component 
                        :is="item.icon" 
                        :class="[
                          'w-4 h-4',
                          $page.url.startsWith(item.href) ? 'text-white' : 'text-cyan-500'
                        ]" 
                      />
                    </div>
                    <span class="flex-1 text-left">{{ item.name }}</span>
                    <ChevronDown 
                      :class="[
                        'w-4 h-4 transition-transform',
                        openSubmenu === item.name ? 'rotate-180' : ''
                      ]"
                    />
                  </button>
                  
                  <!-- Submenu -->
                  <transition
                    enter-active-class="transition-all duration-200 ease-out"
                    enter-from-class="opacity-0 -translate-y-2 max-h-0"
                    enter-to-class="opacity-100 translate-y-0 max-h-96"
                    leave-active-class="transition-all duration-200 ease-in"
                    leave-from-class="opacity-100 translate-y-0 max-h-96"
                    leave-to-class="opacity-0 -translate-y-2 max-h-0"
                  >
                    <div 
                      v-show="openSubmenu === item.name"
                      class="ml-8 mt-1 space-y-1 overflow-hidden relative"
                    >
                      <!-- Vertical line from icon -->
                      <div class="absolute left-2 top-0 bottom-2 w-px bg-gray-300"></div>
                      
                      <Link
                        v-for="(subitem, index) in item.submenu"
                        :key="subitem.href"
                        :href="subitem.href"
                        @click="mobileOpen = false"
                        class="flex items-center gap-3 pl-8 pr-3 py-2 rounded-lg text-sm transition-colors relative"
                        :class="$page.url === subitem.href
                          ? 'text-cyan-600 font-medium'
                          : 'text-muted-foreground hover:text-foreground'"
                      >
                        <!-- Horizontal branch line -->
                        <span class="absolute left-2 top-1/2 w-4 h-px bg-gray-300"></span>
                        {{ subitem.name }}
                      </Link>
                    </div>
                  </transition>
                </div>
                
                <!-- Regular menu item -->
                <Link
                  v-else
                  :href="item.href"
                  @click="mobileOpen = false"
                  class="flex items-center gap-3 px-4 py-4 rounded-xl text-sm font-medium transition-all"
                  :class="$page.url === item.href 
                    ? 'bg-white shadow-md text-foreground' 
                    : 'text-muted-foreground hover:bg-muted'"
                >
                  <div 
                    :class="[
                      'w-8 h-8 rounded-lg flex items-center justify-center transition-colors',
                      $page.url === item.href 
                        ? 'bg-gradient-to-br from-cyan-500 to-teal-500' 
                        : 'bg-white'
                    ]"
                  >
                    <component 
                      :is="item.icon" 
                      :class="[
                        'w-4 h-4',
                        $page.url === item.href ? 'text-white' : 'text-cyan-500'
                      ]" 
                    />
                  </div>
                  <span>{{ item.name }}</span>
                </Link>
              </template>
            </div>

            <!-- Settings Section (SuperAdmin only) -->
            <div v-if="userRole === 'superadmin'" class="mt-8">
              <div class="px-3 mb-2">
                <p class="text-xs font-semibold text-muted-foreground uppercase tracking-wider">
                  Settings
                </p>
              </div>
              <div class="space-y-1">
                <Link
                  v-for="item in settingsMenuItems"
                  :key="item.href"
                  :href="item.href"
                  @click="mobileOpen = false"
                  class="flex items-center gap-3 px-4 py-4 rounded-xl text-sm font-medium transition-all"
                  :class="$page.url === item.href 
                    ? 'bg-white shadow-md text-foreground' 
                    : 'text-muted-foreground hover:bg-muted'"
                >
                  <div 
                    :class="[
                      'w-8 h-8 rounded-lg flex items-center justify-center transition-colors',
                      $page.url === item.href 
                        ? 'bg-gradient-to-br from-cyan-500 to-teal-500' 
                        : 'bg-white'
                    ]"
                  >
                    <component 
                      :is="item.icon" 
                      :class="[
                        'w-4 h-4',
                        $page.url === item.href ? 'text-white' : 'text-cyan-500'
                      ]" 
                    />
                  </div>
                  <span>{{ item.name }}</span>
                </Link>
              </div>
            </div>

            <!-- Recent Activity -->
            <RecentActivity />
          </nav>

          <!-- User Profile -->
          <div class="p-4">
            <button class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-muted transition-colors">
              <div class="w-9 h-9 rounded-full bg-gradient-to-br from-cyan-500 to-teal-500 flex items-center justify-center">
                <span class="text-sm font-semibold text-white">{{ page.props.auth.user?.name?.substring(0, 2).toUpperCase() }}</span>
              </div>
              <div class="flex-1 text-left min-w-0">
                <p class="text-sm font-medium truncate">{{ page.props.auth.user?.name }}</p>
                <p class="text-xs text-muted-foreground truncate">{{ page.props.auth.user?.email }}</p>
              </div>
            </button>
          </div>
        </div>
      </SheetContent>
    </Sheet>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col overflow-hidden">
      <!-- Header -->
      <header class="h-20 border-b px-6 flex items-center gap-4">
        <!-- Mobile Menu Button -->
        <Button variant="ghost" size="icon" class="lg:hidden" @click="mobileOpen = true">
          <Menu class="w-5 h-5" />
        </Button>

        <!-- Breadcrumb -->
        <div class="flex-1">
          <Breadcrumb :items="breadcrumbItems" />
        </div>

        <!-- Notifications -->
        <NotificationDropdown />

        <!-- Profile -->
        <div class="relative">
          <button @click="profileOpen = !profileOpen" class="flex items-center gap-2 hover:opacity-80 transition-opacity">
            <div class="w-8 h-8 rounded-full bg-gradient-to-br from-cyan-500 to-teal-500 flex items-center justify-center">
              <span class="text-xs font-semibold text-white">{{ page.props.auth.user?.name?.substring(0, 2).toUpperCase() }}</span>
            </div>
            <ChevronDown class="w-4 h-4 text-muted-foreground" />
          </button>
          
          <div v-if="profileOpen" @click="profileOpen = false" class="fixed inset-0 z-40"></div>
          <transition
            enter-active-class="transition ease-out duration-100"
            enter-from-class="transform opacity-0 scale-95"
            enter-to-class="transform opacity-100 scale-100"
            leave-active-class="transition ease-in duration-75"
            leave-from-class="transform opacity-100 scale-100"
            leave-to-class="transform opacity-0 scale-95"
          >
            <div v-if="profileOpen" class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border z-50 py-1">
              <button @click="logout" class="w-full flex items-center gap-3 px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors">
                <LogOut class="w-4 h-4" />
                <span>Logout</span>
              </button>
            </div>
          </transition>
        </div>
      </header>

      <!-- Content -->
      <main class="flex-1 overflow-auto p-4 lg:p-6">
        <!-- Flash Messages -->
        <div v-if="$page.props.flash?.success" class="mb-4 p-4 bg-green-50 border border-green-200 text-green-800 rounded-lg flex items-center justify-between">
          <span>{{ $page.props.flash.success }}</span>
          <button @click="$page.props.flash.success = null" class="text-green-600 hover:text-green-800">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>
        
        <div v-if="$page.props.flash?.error" class="mb-4 p-4 bg-red-50 border border-red-200 text-red-800 rounded-lg flex items-center justify-between">
          <span>{{ $page.props.flash.error }}</span>
          <button @click="$page.props.flash.error = null" class="text-red-600 hover:text-red-800">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>
        
        <slot />
      </main>
    </div>
  </div>
</template>
