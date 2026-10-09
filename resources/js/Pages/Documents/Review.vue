<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card'
import { Button } from '@/components/ui/button'
import { Textarea } from '@/components/ui/textarea'
import { Badge } from '@/components/ui/badge'
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table'
import { useForm, usePage } from '@inertiajs/vue3'
import { ref, computed, watch } from 'vue'
import { Download, CheckCircle, XCircle, Eye, X } from 'lucide-vue-next'

interface Props {
  document: any
  allReviews: any[]
}

const props = defineProps<Props>()

console.log('=== REVIEW PAGE ===')
console.log('allReviews count:', props.allReviews?.length)
console.log('allReviews:', props.allReviews)

const page = usePage()
const userRole = page.props.auth.user?.role
const previewUrl = ref('')

const openPreview = (versionId: number) => {
  previewUrl.value = `/documents/versions/${versionId}/preview?t=${Date.now()}`
}

const closePreview = () => {
  previewUrl.value = ''
}

const canReview = computed(() => {
  if (userRole !== 'reviewer') return false
  
  const reviewerRole = page.props.auth.user?.reviewer_role
  if (!reviewerRole) return false
  
  const level = reviewerRole.level
  const status = props.document?.status
  
  // Check if document status matches reviewer level
  if (level === 1 && status === 'under_pic_review') return true
  if (level === 2 && status === 'under_group_review') return true
  if (level === 3 && status === 'under_manager_review') return true
  
  return false
})

const reviewerLevel = computed(() => {
  return page.props.auth.user?.reviewer_role?.level || 0
})

const lockMessage = computed(() => {
  if (!canViewDocument.value || canReview.value) return null
  
  const level = reviewerLevel.value
  
  if (level === 2) {
    if (level1Status.value !== 'approved') {
      return '🔒 Waiting for Level 1 (PIC Development) approval before you can review'
    }
  }
  
  if (level === 3) {
    if (level1Status.value !== 'approved') {
      return '🔒 Waiting for Level 1 (PIC Development) approval'
    }
    if (level2Status.value !== 'approved') {
      return '🔒 Waiting for Level 2 (Group Leader) approval before you can review'
    }
  }
  
  return '⏳ This document is not at your review level yet'
})

const canViewDocument = computed(() => {
  // Reviewers can always view documents (even if not their turn)
  return userRole === 'reviewer'
})

const currentVersionReviews = computed(() => {
  if (!props.allReviews || !props.document?.latest_version) return []
  // Filter reviews for CURRENT version only
  return props.allReviews.filter(r =>
    r.document_version_id === props.document.latest_version.id
  )
})

const level1Status = computed(() => {
  const review = currentVersionReviews.value.find(r => 
    r.reviewer_role === 'pic_development' && r.status !== 'rejected'
  )
  return review?.status || 'pending'
})

const level2Status = computed(() => {
  const review = currentVersionReviews.value.find(r => 
    r.reviewer_role === 'group_leader' && r.status !== 'rejected'
  )
  return review?.status || 'pending'
})

const level3Status = computed(() => {
  const review = currentVersionReviews.value.find(r => 
    r.reviewer_role === 'manager' && r.status !== 'rejected'
  )
  return review?.status || 'pending'
})

const userReview = computed(() => {
  return props.allReviews?.find((r: any) => r.reviewer_id === page.props.auth.user?.id)
})

const reviewAction = computed(() => {
  return userReview.value ? 'Update' : 'Submit'
})

const reviewForm = useForm({
  comment: ''
})

// Don't pre-fill form - always start with empty comment

const approve = () => {
  if (userReview.value && userReview.value.status === 'rejected') {
    if (!confirm('You previously rejected this document. Are you sure you want to change to approve?')) {
      return
    }
  }
  reviewForm.post(`/reviews/${props.document.id}/approve`, {
    preserveScroll: true,
    onSuccess: () => {
      reviewForm.reset()
    }
  })
}

const reject = () => {
  if (userReview.value && userReview.value.status === 'approved') {
    if (!confirm('You previously approved this document. Are you sure you want to change to reject?')) {
      return
    }
  }
  reviewForm.post(`/reviews/${props.document.id}/reject`, {
    preserveScroll: true,
    onSuccess: () => {
      reviewForm.reset()
    }
  })
}

const getStatusColor = (status: string) => {
  const colors: Record<string, string> = {
    draft: 'bg-gray-100 text-gray-800',
    submitted: 'bg-blue-100 text-blue-800',
    under_pic_review: 'bg-purple-100 text-purple-800',
    under_group_review: 'bg-blue-100 text-blue-800',
    under_manager_review: 'bg-orange-100 text-orange-800',
    need_revision: 'bg-red-100 text-red-800',
    complete: 'bg-green-100 text-green-800'
  }
  return colors[status] || 'bg-gray-100 text-gray-800'
}

const getStatusText = (status: string) => {
  const texts: Record<string, string> = {
    draft: 'Draft',
    submitted: 'Submitted',
    under_pic_review: 'Under PIC Review',
    under_group_review: 'Under Group Review',
    under_manager_review: 'Under Manager Review',
    need_revision: 'Need Revision',
    complete: 'Complete'
  }
  return texts[status] || status
}
</script>

<template>
  <AuthenticatedLayout>
    <div class="space-y-6">
      <div>
        <h1 class="text-2xl font-bold">Review Document</h1>
        <p class="text-muted-foreground">{{ document.project?.project_code }} - {{ document.required_document?.document_name }}</p>
      </div>

      <div class="grid gap-6 md:grid-cols-2">
        <Card>
          <CardHeader>
            <CardTitle>Document Information</CardTitle>
          </CardHeader>
          <CardContent class="space-y-2">
            <div class="grid grid-cols-2 gap-2 text-sm">
              <div class="text-muted-foreground">Project:</div>
              <div class="font-medium">{{ document.project?.project_name }}</div>
              
              <div class="text-muted-foreground">Document:</div>
              <div class="font-medium">{{ document.required_document?.document_name }}</div>
              
              <div class="text-muted-foreground">Type:</div>
              <div class="font-medium">{{ document.required_document?.document_type }}</div>
              
              <div class="text-muted-foreground">Version:</div>
              <div class="font-medium">v{{ document.current_version }}</div>
              
              <div class="text-muted-foreground">Status:</div>
                  <div>
                    <Badge :class="getStatusColor(document.status)">{{ getStatusText(document.status) }}</Badge>
                  </div>
              
              <div class="text-muted-foreground">Uploaded:</div>
              <div class="font-medium">{{ document.latest_version?.formatted_uploaded_at }}</div>
            </div>
          </CardContent>
        </Card>

        <Card>
          <CardHeader>
            <CardTitle>File</CardTitle>
          </CardHeader>
          <CardContent>
            <div class="space-y-4">
              <div class="p-4 border rounded-lg">
                <div class="flex items-center justify-between">
                  <div>
                    <p class="font-medium">{{ document.latest_version?.file_name }}</p>
                    <p class="text-sm text-muted-foreground">
                      {{ Math.round(document.latest_version?.file_size / 1024) }} KB
                    </p>
                  </div>
                  <a :href="`/documents/versions/${document.latest_version?.id}/download`">
                    <Button size="sm">
                      <Download class="w-4 h-4 mr-2" />
                      Download
                    </Button>
                  </a>
                </div>
              </div>
            </div>
          </CardContent>
        </Card>
      </div>

      <!-- Approval Progress and Review Action - Side by Side -->
      <div class="grid gap-6 md:grid-cols-2">
        <!-- Approval Progress -->
        <Card>
          <CardHeader>
            <CardTitle>Approval Progress (Current Version)</CardTitle>
          </CardHeader>
          <CardContent>
            <div class="space-y-3">
              <div class="flex items-center gap-3">
                <Badge :class="level1Status === 'approved' ? 'bg-green-500' : level1Status === 'rejected' ? 'bg-red-500' : 'bg-gray-300'">
                  Level 1 (PIC)
                </Badge>
                <span v-if="level1Status === 'approved'" class="text-green-600">✅ Approved</span>
                <span v-else-if="level1Status === 'rejected'" class="text-red-600">❌ Rejected</span>
                <span v-else class="text-gray-500">⏳ Pending</span>
              </div>
              
              <div class="flex items-center gap-3">
                <Badge :class="level2Status === 'approved' ? 'bg-green-500' : level2Status === 'rejected' ? 'bg-red-500' : 'bg-gray-300'">
                  Level 2 (Group Leader)
                </Badge>
                <span v-if="level2Status === 'approved'" class="text-green-600">✅ Approved</span>
                <span v-else-if="level2Status === 'rejected'" class="text-red-600">❌ Rejected</span>
                <span v-else-if="level1Status === 'approved'" class="text-yellow-600">⏳ Pending</span>
                <span v-else class="text-gray-400">🔒 Locked (waiting for Level 1)</span>
              </div>
              
              <div class="flex items-center gap-3">
                <Badge :class="level3Status === 'approved' ? 'bg-green-500' : level3Status === 'rejected' ? 'bg-red-500' : 'bg-gray-300'">
                  Level 3 (Manager)
                </Badge>
                <span v-if="level3Status === 'approved'" class="text-green-600">✅ Approved</span>
                <span v-else-if="level3Status === 'rejected'" class="text-red-600">❌ Rejected</span>
                <span v-else-if="level2Status === 'approved'" class="text-yellow-600">⏳ Pending</span>
                <span v-else class="text-gray-400">🔒 Locked (waiting for Level 2)</span>
              </div>
            </div>
          </CardContent>
        </Card>

        <!-- Review Action -->
        <Card>
          <CardHeader>
            <CardTitle>Review Action</CardTitle>
          </CardHeader>
          <CardContent>
            <div v-if="!canReview && !canViewDocument" class="text-center py-8">
              <p class="text-muted-foreground">You don't have permission to review this document.</p>
            </div>
            <div v-else-if="!canReview && canViewDocument" class="bg-yellow-50 border border-yellow-200 rounded p-4">
              <p class="text-sm text-yellow-800 font-medium">
                {{ lockMessage }}
              </p>
              <p class="text-xs text-yellow-700 mt-2">
                You can view the document but cannot approve/reject until previous levels complete their review.
              </p>
            </div>
            <div v-else class="space-y-4">
              <div v-if="userReview" class="mb-4 p-3 bg-blue-50 border border-blue-200 rounded-md">
                <p class="text-sm text-blue-800">
                  You have already reviewed this document as <strong>{{ userReview.status }}</strong>. 
                  You can update your review below.
                </p>
              </div>
              
              <div class="space-y-2">
                <label class="text-sm font-medium">Comment (Optional)</label>
                <Textarea v-model="reviewForm.comment" placeholder="Add your review comment (optional)..." :rows="3" />
              </div>
              <div class="flex gap-2 mt-4">
                <Button @click="approve" :disabled="reviewForm.processing">
                  <CheckCircle class="w-4 h-4 mr-2" />
                  {{ reviewAction }} Approve
                </Button>
                <Button variant="destructive" @click="reject" :disabled="reviewForm.processing">
                  <XCircle class="w-4 h-4 mr-2" />
                  {{ reviewAction }} Reject
                </Button>
              </div>
            </div>
          </CardContent>
        </Card>
      </div>

      <!-- Review History - Full Width Below -->
      <Card v-if="allReviews && allReviews.length > 0">
        <CardHeader>
          <CardTitle>Review History (All Versions)</CardTitle>
        </CardHeader>
        <CardContent>
          <div class="space-y-3 max-h-96 overflow-y-auto">
            <div v-for="review in allReviews" :key="review.id" class="border-b pb-3">
              <div class="flex justify-between items-start">
                <div>
                  <Badge :class="review.status === 'approved' ? 'bg-green-500' : 'bg-red-500'" class="mr-2">
                    {{ review.status }}
                  </Badge>
                    <span class="font-medium">{{ review.reviewer?.name }}</span>
                    <span class="text-sm text-gray-500 ml-2">({{ review.reviewer_role }})</span>
                    <Badge variant="outline" class="ml-2 text-xs">
                      v{{ review.document_version?.version_number || '?' }}
                    </Badge>
                  </div>
                  <span class="text-sm text-gray-500">{{ review.reviewed_at }}</span>
                </div>
                <p class="text-sm mt-1 text-gray-700">{{ review.comment || 'No comment' }}</p>
              </div>
            </div>
          </CardContent>
        </Card>

      <!-- Version History Table -->
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
                <TableCell>{{ Math.round(version.file_size / 1024) }} KB</TableCell>
                <TableCell>{{ version.uploader?.name }}</TableCell>
                <TableCell>{{ version.formatted_uploaded_at }}</TableCell>
                <TableCell>
                  <div class="flex gap-2">
                    <Button size="sm" variant="ghost" @click="openPreview(version.id)">
                      <Eye class="w-4 h-4" />
                    </Button>
                    <a :href="`/documents/versions/${version.id}/download`" target="_blank">
                      <Button size="sm" variant="ghost">
                        <Download class="w-4 h-4" />
                      </Button>
                    </a>
                  </div>
                </TableCell>
              </TableRow>
            </TableBody>
          </Table>
        </CardContent>
      </Card>

      <!-- Document Preview -->
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
  </AuthenticatedLayout>
</template>
