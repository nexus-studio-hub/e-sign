<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Badge } from '@/components/ui/badge'
import { Textarea } from '@/components/ui/textarea'
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table'
import { ArrowLeft, Upload, FileText, Download, Send, Eye, X, CheckCircle, XCircle } from 'lucide-vue-next'
import { Link, usePage, useForm, router } from '@inertiajs/vue3'
import { ref, computed } from 'vue'

const props = defineProps<{
  document: any
}>()

const page = usePage()
const userRole = page.props.auth.user?.role
const previewUrl = ref('')
const showSuccessModal = ref(false)

const openPreview = (versionId: number) => {
  previewUrl.value = `/documents/versions/${versionId}/preview?t=${Date.now()}`
}

const closePreview = () => {
  previewUrl.value = ''
}

const uploadForm = useForm({
  file: null as File | null
})

const reviewForm = useForm({
  status: '',
  comment: ''
})

const canReview = computed(() => {
  return userRole === 'reviewer' && 
    (props.document.status === 'under_pic_review' || 
     props.document.status === 'under_group_review' || 
     props.document.status === 'under_manager_review' ||
     props.document.status === 'need_revision')
})

const handleFileChange = (event: Event) => {
  const target = event.target as HTMLInputElement
  if (target.files && target.files[0]) {
    uploadForm.file = target.files[0]
  }
}

const submitUpload = () => {
  if (!uploadForm.file) {
    console.log('No file selected')
    return
  }
  
  console.log('Starting upload...', uploadForm.file.name)
  
  uploadForm.post(`/documents/${props.document.id}/upload`, {
    forceFormData: true,
    onSuccess: () => {
      console.log('Upload success!')
      uploadForm.reset()
      const fileInput = document.querySelector('input[type="file"]') as HTMLInputElement
      if (fileInput) fileInput.value = ''
      showSuccessModal.value = true
      setTimeout(() => {
        showSuccessModal.value = false
      }, 3000)
    },
    onError: (errors) => {
      console.error('Upload error:', errors)
      alert('Upload failed: ' + JSON.stringify(errors))
    }
  })
}

const submitReview = (status: string) => {
  const route = status === 'approved' 
    ? `/reviews/${props.document.id}/approve`
    : `/reviews/${props.document.id}/reject`
  
  reviewForm.post(route, {
    onSuccess: () => {
      reviewForm.reset()
    }
  })
}

const getStatusColor = (status: string) => {
  const colors: Record<string, string> = {
    draft: 'bg-gray-500',
    under_pic_review: 'bg-blue-500',
    under_group_review: 'bg-yellow-500',
    under_manager_review: 'bg-orange-500',
    approved: 'bg-green-500',
    need_revision: 'bg-red-500',
  }
  return colors[status] || 'bg-gray-500'
}

const formatDate = (date: string) => {
  if (!date) return '-'
  return new Date(date).toLocaleDateString('id-ID')
}

const formatDateTime = (date: string) => {
  if (!date) return '-'
  const d = new Date(date)
  return d.toLocaleDateString('id-ID') + ' ' + d.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' })
}

const formatFileSize = (bytes: number) => {
  if (bytes < 1024) return bytes + ' B'
  if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(2) + ' KB'
  return (bytes / (1024 * 1024)).toFixed(2) + ' MB'
}
</script>

<template>
  <AuthenticatedLayout>
    <div class="space-y-6">
      <div class="flex items-center">
        <Link :href="`/parts/${document.required_document?.part_id}`">
          <Button variant="ghost" size="sm">
            <ArrowLeft class="w-4 h-4 mr-2" />
            Back to Part
          </Button>
        </Link>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <Card>
          <CardHeader>
            <CardTitle>Document Details</CardTitle>
          </CardHeader>
          <CardContent class="space-y-4">
            <div>
              <p class="text-sm text-muted-foreground">Document Name</p>
              <p class="font-medium">{{ document.required_document?.document_name }}</p>
            </div>
            <div>
              <p class="text-sm text-muted-foreground">Document Type</p>
              <p class="font-medium">{{ document.required_document?.document_type }}</p>
            </div>
            <div>
              <p class="text-sm text-muted-foreground">Project</p>
              <p class="font-medium">{{ document.project?.project_name }}</p>
            </div>
            <div>
              <p class="text-sm text-muted-foreground">Current Version</p>
              <p class="font-medium">v{{ document.current_version }}</p>
            </div>
            <div>
              <p class="text-sm text-muted-foreground">Start Date</p>
              <p class="font-medium">{{ formatDate(document.required_document?.start_date) }}</p>
            </div>
            <div>
              <p class="text-sm text-muted-foreground">Due Date</p>
              <p class="font-medium">{{ formatDate(document.required_document?.due_date) }}</p>
            </div>
            <div>
              <p class="text-sm text-muted-foreground">Status</p>
              <Badge :class="getStatusColor(document.status)">
                {{ document.status?.replace(/_/g, ' ') }}
              </Badge>
            </div>
          </CardContent>
        </Card>

        <Card v-if="canReview">
          <CardHeader>
            <CardTitle>Review Document</CardTitle>
          </CardHeader>
          <CardContent>
            <form @submit.prevent class="space-y-4">
              <div class="space-y-2">
                <label class="text-sm font-medium">Comment</label>
                <Textarea 
                  v-model="reviewForm.comment"
                  placeholder="Enter your review comment..."
                  rows="4"
                />
              </div>
              
              <div class="flex gap-2">
                <Button 
                  type="button"
                  @click="submitReview('approved')"
                  :disabled="reviewForm.processing"
                  class="bg-green-600 hover:bg-green-700"
                >
                  <CheckCircle class="w-4 h-4 mr-2" />
                  Approve
                </Button>
                
                <Button 
                  type="button"
                  variant="destructive"
                  @click="submitReview('need_revision')"
                  :disabled="reviewForm.processing"
                >
                  <XCircle class="w-4 h-4 mr-2" />
                  Need Revision
                </Button>
              </div>
            </form>
          </CardContent>
        </Card>

        <Card>
          <CardHeader>
            <CardTitle>Review History</CardTitle>
          </CardHeader>
          <CardContent class="max-h-[400px] overflow-y-auto">
            <div v-if="document.reviews && document.reviews.length > 0" class="space-y-4">
              <div v-for="review in document.reviews" :key="review.id" class="border rounded-lg p-4 space-y-2">
                <div class="flex items-center justify-between">
                  <div class="flex items-center gap-2">
                    <Badge :class="review.status === 'approved' ? 'bg-green-500' : 'bg-red-500'">
                      {{ review.status }}
                    </Badge>
                    <span class="text-sm font-medium">{{ review.reviewer?.name }}</span>
                  </div>
                  <span class="text-sm text-muted-foreground">{{ formatDateTime(review.created_at) }}</span>
                </div>
                <p class="text-sm">{{ review.comment || 'No comment' }}</p>
              </div>
            </div>
            <div v-else class="space-y-4">
              <div class="border rounded-lg p-4 space-y-2 animate-pulse">
                <div class="flex items-center justify-between">
                  <div class="flex items-center gap-2">
                    <div class="h-5 w-20 bg-gray-200 rounded"></div>
                    <div class="h-4 w-32 bg-gray-200 rounded"></div>
                  </div>
                  <div class="h-4 w-24 bg-gray-200 rounded"></div>
                </div>
                <div class="h-4 w-full bg-gray-200 rounded"></div>
              </div>
            </div>
          </CardContent>
        </Card>
      </div>

      <Card v-if="userRole === 'supplier' && (document.status === 'draft' || document.status === 'need_revision')">
        <CardHeader>
          <CardTitle>Upload Document</CardTitle>
        </CardHeader>
        <CardContent>
          <form @submit.prevent="submitUpload" class="space-y-4">
            <div class="space-y-2">
              <Input 
                type="file" 
                @change="handleFileChange"
                accept=".pdf,.doc,.docx,.xls,.xlsx"
              />
              <p class="text-sm text-muted-foreground">Max file size: 10MB. Accepted formats: PDF, DOC, DOCX, XLS, XLSX</p>
            </div>
            
            <Button type="submit" :disabled="!uploadForm.file || uploadForm.processing">
              <Upload class="w-4 h-4 mr-2" />
              {{ uploadForm.processing ? 'Uploading...' : (document.status === 'need_revision' ? 'Upload Revision' : 'Upload For Review') }}
            </Button>
          </form>
        </CardContent>
      </Card>

      <Card>
        <CardHeader>
          <CardTitle>Version History</CardTitle>
        </CardHeader>
        <CardContent>
          <Table>
            <TableHeader>
              <TableRow>
                <TableHead>Version</TableHead>
                <TableHead>File Name</TableHead>
                <TableHead>Size</TableHead>
                <TableHead>Uploaded By</TableHead>
                <TableHead>Uploaded At</TableHead>
                <TableHead>Actions</TableHead>
              </TableRow>
            </TableHeader>
            <TableBody>
              <TableRow v-for="version in document.versions" :key="version.id">
                <TableCell class="font-medium">v{{ version.version_number }}</TableCell>
                <TableCell>{{ version.file_name }}</TableCell>
                <TableCell>{{ formatFileSize(version.file_size) }}</TableCell>
                <TableCell>{{ version.uploader?.name }}</TableCell>
                <TableCell>{{ formatDate(version.uploaded_at) }}</TableCell>
                <TableCell>
                  <div class="flex gap-2">
                    <Button size="sm" variant="ghost" @click="openPreview(version.id)">
                      <Eye class="w-4 h-4" />
                    </Button>
                    <a :href="`/documents/versions/${version.id}/download?t=${Date.now()}`" target="_blank">
                      <Button size="sm" variant="ghost">
                        <Download class="w-4 h-4" />
                      </Button>
                    </a>
                  </div>
                </TableCell>
              </TableRow>
              <TableRow v-if="!document.versions || document.versions.length === 0">
                <TableCell colspan="6" class="text-center text-muted-foreground">
                  No files uploaded yet
                </TableCell>
              </TableRow>
            </TableBody>
          </Table>
        </CardContent>
      </Card>

      <Card v-if="previewUrl">
        <CardHeader>
          <div class="flex items-center justify-between">
            <CardTitle>Document Preview</CardTitle>
            <Button size="sm" variant="ghost" @click="closePreview">
              <X class="w-4 h-4" />
            </Button>
          </div>
        </CardHeader>
        <CardContent>
          <iframe 
            :src="previewUrl" 
            class="w-full h-[1200px] border rounded"
            frameborder="0"
          ></iframe>
        </CardContent>
      </Card>
    </div>

    <!-- Success Modal -->
    <Transition
      enter-active-class="transition ease-out duration-300"
      enter-from-class="opacity-0 scale-90"
      enter-to-class="opacity-100 scale-100"
      leave-active-class="transition ease-in duration-200"
      leave-from-class="opacity-100 scale-100"
      leave-to-class="opacity-0 scale-90"
    >
      <div v-if="showSuccessModal" class="fixed inset-0 z-50 flex items-center justify-center">
        <div class="bg-white rounded-lg p-8 max-w-md mx-4 text-center">
          <div class="mb-4 flex justify-center">
            <div class="relative">
              <div class="absolute inset-0 bg-green-500 rounded-full animate-ping opacity-75"></div>
              <CheckCircle class="w-16 h-16 text-green-500 relative animate-bounce" />
            </div>
          </div>
          <h3 class="text-xl font-semibold mb-2">Document Successfully Uploaded!</h3>
          <p class="text-muted-foreground">Your document has been submitted and is now waiting for review.</p>
        </div>
      </div>
    </Transition>
  </AuthenticatedLayout>
</template>
