<script setup lang="ts">
import { ref } from 'vue'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card'
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table'
import { Badge } from '@/components/ui/badge'
import { Button } from '@/components/ui/button'
import { Progress } from '@/components/ui/progress'
import { useForm, usePage } from '@inertiajs/vue3'
import { Upload, Download, CheckCircle, XCircle, Pencil, Trash2 } from 'lucide-vue-next'
import { Link, router } from '@inertiajs/vue3'

interface Props {
  project: any
}

const props = defineProps<Props>()

const page = usePage()
const userRole = page.props.auth.user?.role

const uploadForm = useForm({
  file: null as File | null
})

const uploadingFiles = ref<Record<number, boolean>>({})
const selectedFiles = ref<Record<number, string>>({})

const handleFileUpload = (documentId: number, event: Event) => {
  const target = event.target as HTMLInputElement
  if (target.files && target.files[0]) {
    const file = target.files[0]
    
    selectedFiles.value[documentId] = file.name
    uploadingFiles.value[documentId] = true
    
    const formData = new FormData()
    formData.append('file', file)
    
    uploadForm.post(`/documents/${documentId}/upload`, {
      data: formData,
      forceFormData: true,
      onSuccess: () => {
        uploadingFiles.value[documentId] = false
        selectedFiles.value[documentId] = ''
        uploadForm.reset()
        target.value = ''
      },
      onError: (errors) => {
        uploadingFiles.value[documentId] = false
        console.error('Upload error:', errors)
      }
    })
  }
}

const submitDocument = (documentId: number) => {
  useForm({}).post(`/documents/${documentId}/submit`)
}

const getStatusColor = (status: string) => {
  const colors: Record<string, string> = {
    draft: 'bg-gray-100 text-gray-800',
    submitted: 'bg-blue-100 text-blue-800',
    under_pic_review: 'bg-purple-100 text-purple-800',
    under_group_review: 'bg-purple-100 text-purple-800',
    under_manager_review: 'bg-purple-100 text-purple-800',
    need_revision: 'bg-red-100 text-red-800',
    complete: 'bg-green-100 text-green-800'
  }
  return colors[status] || 'bg-gray-100 text-gray-800'
}

const completedDocs = props.project.documents.filter((d: any) => d.status === 'complete').length
const totalDocs = props.project.documents.length
const progress = totalDocs > 0 ? (completedDocs / totalDocs) * 100 : 0

const deleteProject = () => {
  if (confirm('Are you sure you want to delete this project?')) {
    router.delete(`/projects/${props.project.id}`)
  }
}

const downloadCertificate = () => {
  window.location.href = `/certificate/${props.project.certificate_token}/download`
}
</script>

<template>
  <AuthenticatedLayout>
    <div class="space-y-6">
      <div class="flex justify-between items-center">
        <div>
          <h1 class="text-2xl font-bold">{{ project.project_name }}</h1>
          <p class="text-muted-foreground">{{ project.project_code }}</p>
        </div>
        <div v-if="userRole === 'pic_development' || userRole === 'superadmin'" class="flex gap-2">
          <Link :href="`/projects/${project.id}/edit`">
            <Button variant="outline">
              <Pencil class="w-4 h-4 mr-2" />
              Edit
            </Button>
          </Link>
          <Button variant="outline" @click="deleteProject">
            <Trash2 class="w-4 h-4 mr-2 text-red-500" />
            Delete
          </Button>
        </div>
      </div>

      <div class="grid gap-6 md:grid-cols-3">
        <Card>
          <CardHeader>
            <CardTitle>Project Information</CardTitle>
          </CardHeader>
          <CardContent class="space-y-2">
            <div class="grid grid-cols-2 gap-2 text-sm">
              <div class="text-muted-foreground">Model:</div>
              <div class="font-medium">{{ project.model }}</div>
              
              <div class="text-muted-foreground">Part Number:</div>
              <div class="font-medium">{{ project.part_number }}</div>
              
              <div class="text-muted-foreground">Part Name:</div>
              <div class="font-medium">{{ project.part_name }}</div>
              
              <div class="text-muted-foreground">Supplier:</div>
              <div class="font-medium">{{ project.supplier?.name }}</div>
              
              <div class="text-muted-foreground">PIC:</div>
              <div class="font-medium">{{ project.pic?.name }}</div>
              
              <div class="text-muted-foreground">Due Date:</div>
              <div class="font-medium">{{ project.due_date }}</div>
            </div>
          </CardContent>
        </Card>

        <Card>
          <CardHeader>
            <CardTitle>Progress</CardTitle>
          </CardHeader>
          <CardContent class="space-y-4">
            <div>
              <div class="flex justify-between text-sm mb-2">
                <span>Documents Completed</span>
                <span class="font-medium">{{ completedDocs }} / {{ totalDocs }}</span>
              </div>
              <div class="relative w-full overflow-hidden rounded-full bg-secondary h-2">
                <div 
                  class="h-full transition-all"
                  :class="progress === 100 ? 'bg-green-500' : 'bg-primary'"
                  :style="`width: ${progress}%`"
                ></div>
              </div>
            </div>
            <div class="text-sm text-muted-foreground">
              {{ Math.round(progress) }}% Complete
            </div>
            
            <!-- Approvers List (only if 100% complete) -->
            <div v-if="progress === 100">
              <h4 class="text-xs font-semibold mb-2 text-green-700 flex items-center gap-1">
                <CheckCircle class="w-3 h-3" />
                Approved By:
              </h4>
              <div class="space-y-1">
                <div 
                  v-for="approver in project.approvers" 
                  :key="approver.id"
                  class="text-xs text-slate-700 bg-green-50 px-2 py-1 rounded"
                >
                  {{ approver.name }} - {{ approver.role.replace('_', ' ') }}
                </div>
              </div>
            </div>
          </CardContent>
        </Card>

        <!-- Approval Certificate Card (only if 100% complete) -->
        <Card v-if="progress === 100">
          <CardHeader>
            <CardTitle>🎉 Certificate</CardTitle>
          </CardHeader>
          <CardContent class="space-y-4">
            <div class="flex flex-col items-center">
              <img 
                :src="`/certificate/${project.certificate_token}/qr`" 
                alt="QR Code" 
                class="w-40 h-40 md:w-48 md:h-48"
              />
              <a 
                :href="`/certificate/verify/${project.certificate_token}`"
                class="text-xs text-blue-600 hover:underline mt-3 text-center"
              >
                View Certificate
              </a>
            </div>
            
            <Button @click="downloadCertificate" size="sm" class="w-full">
              <Download class="w-4 h-4 mr-2" />
              Download
            </Button>
          </CardContent>
        </Card>
      </div>

      <Card>
        <CardHeader>
          <CardTitle>Document Checklist</CardTitle>
        </CardHeader>
        <CardContent>
          <Table>
            <TableHeader>
              <TableRow>
                <TableHead>Document Name</TableHead>
                <TableHead>Type</TableHead>
                <TableHead>Required</TableHead>
                <TableHead>Version</TableHead>
                <TableHead>Status</TableHead>
                <TableHead>Action</TableHead>
              </TableRow>
            </TableHeader>
            <TableBody>
              <TableRow v-for="reqDoc in project.required_documents" :key="reqDoc.id">
                <TableCell class="font-medium">{{ reqDoc.document_name }}</TableCell>
                <TableCell>{{ reqDoc.document_type }}</TableCell>
                <TableCell>
                  <CheckCircle v-if="reqDoc.is_required" class="w-4 h-4 text-green-500" />
                  <XCircle v-else class="w-4 h-4 text-gray-400" />
                </TableCell>
                <TableCell>
                  <span v-if="reqDoc.document?.current_version">v{{ reqDoc.document.current_version }}</span>
                  <span v-else class="text-muted-foreground">-</span>
                </TableCell>
                <TableCell>
                  <Badge v-if="reqDoc.document" :class="getStatusColor(reqDoc.document.status)">
                    {{ reqDoc.document.status }}
                  </Badge>
                </TableCell>
                <TableCell>
                  <div class="flex gap-2 items-center">
                    <div v-if="userRole === 'supplier' && reqDoc.document && (reqDoc.document.status === 'draft' || reqDoc.document.status === 'need_revision')" class="flex items-center gap-2">
                      <input 
                        type="file" 
                        :id="`file-${reqDoc.document.id}`"
                        class="hidden"
                        accept=".pdf,.doc,.docx,.xls,.xlsx"
                        @change="handleFileUpload(reqDoc.document.id, $event)"
                      />
                      <label :for="`file-${reqDoc.document.id}`" class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:pointer-events-none disabled:opacity-50 border border-input bg-background shadow-sm hover:bg-accent hover:text-accent-foreground h-9 px-3 cursor-pointer">
                        <Upload class="w-4 h-4 mr-1" />
                        {{ uploadingFiles[reqDoc.document.id] ? 'Uploading...' : 'Upload' }}
                      </label>
                      
                      <span v-if="selectedFiles[reqDoc.document.id]" class="text-xs text-gray-600">
                        {{ selectedFiles[reqDoc.document.id] }}
                      </span>
                      <span v-else-if="reqDoc.document?.latest_version" class="text-xs text-gray-600">
                        {{ reqDoc.document.latest_version.file_name }}
                      </span>
                    </div>
                    
                    <Button 
                      v-if="userRole === 'supplier' && reqDoc.document?.current_version > 0 && reqDoc.document?.status === 'draft'"
                      size="sm" 
                      @click="submitDocument(reqDoc.document.id)"
                    >
                      Submit
                    </Button>

                    <a 
                      v-if="reqDoc.document?.latest_version"
                      :href="`/documents/versions/${reqDoc.document.latest_version.id}/download`"
                    >
                      <Button size="sm" variant="ghost">
                        <Download class="w-4 h-4" />
                      </Button>
                    </a>
                    
                    <a 
                      v-if="(userRole === 'pic_development' && reqDoc.document?.status === 'under_pic_review') ||
                            (userRole === 'group_leader' && reqDoc.document?.status === 'under_group_review') ||
                            (userRole === 'manager' && reqDoc.document?.status === 'under_manager_review')"
                      :href="`/reviews/${reqDoc.document.id}`"
                    >
                      <Button size="sm">
                        Review
                      </Button>
                    </a>
                  </div>
                </TableCell>
              </TableRow>
            </TableBody>
          </Table>
        </CardContent>
      </Card>
    </div>
  </AuthenticatedLayout>
</template>
