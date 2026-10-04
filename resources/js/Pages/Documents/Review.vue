<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card'
import { Button } from '@/components/ui/button'
import { Textarea } from '@/components/ui/textarea'
import { Badge } from '@/components/ui/badge'
import { useForm, usePage } from '@inertiajs/vue3'
import { ref, computed, watch } from 'vue'
import { Download, CheckCircle, XCircle } from 'lucide-vue-next'

interface Props {
  document: any
}

const props = defineProps<Props>()

const page = usePage()
const userRole = page.props.auth.user?.role

const canReview = computed(() => {
  // Allow review if user has appropriate role
  // Don't check document status - allow editing existing reviews
  if (userRole === 'pic_development') return true
  if (userRole === 'group_leader') return true
  if (userRole === 'manager') return true
  return false
})

const userReview = computed(() => {
  return props.document.reviews?.find((r: any) => r.reviewer_id === page.props.auth.user?.id)
})

const reviewAction = computed(() => {
  return userReview.value ? 'Update' : 'Submit'
})

const approveForm = useForm({
  comment: ''
})

const rejectForm = useForm({
  comment: ''
})

// Pre-fill forms when userReview exists
watch(() => userReview.value, (review) => {
  if (review) {
    approveForm.comment = review.comment || ''
    rejectForm.comment = review.comment || ''
  }
}, { immediate: true })

const showRejectForm = ref(false)

const approve = () => {
  if (userReview.value && userReview.value.status === 'rejected') {
    if (!confirm('You previously rejected this document. Are you sure you want to change to approve?')) {
      return
    }
  }
  approveForm.post(`/reviews/${props.document.id}/approve`)
}

const reject = () => {
  if (!rejectForm.comment) {
    alert('Comment is required when rejecting')
    return
  }
  if (userReview.value && userReview.value.status === 'approved') {
    if (!confirm('You previously approved this document. Are you sure you want to change to reject?')) {
      return
    }
  }
  rejectForm.post(`/reviews/${props.document.id}/reject`)
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
                <Badge :class="getStatusColor(document.status)">{{ document.status }}</Badge>
              </div>
              
              <div class="text-muted-foreground">Uploaded:</div>
              <div class="font-medium">{{ document.latest_version?.uploaded_at }}</div>
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

      <Card v-if="document.reviews?.length > 0">
        <CardHeader>
          <CardTitle>Review History</CardTitle>
        </CardHeader>
        <CardContent>
          <div class="space-y-4">
            <div v-for="review in document.reviews" :key="review.id" class="border-l-4 pl-4" :class="review.status === 'approved' ? 'border-green-500' : 'border-red-500'">
              <div class="flex items-center gap-2 mb-1">
                <CheckCircle v-if="review.status === 'approved'" class="w-4 h-4 text-green-500" />
                <XCircle v-else class="w-4 h-4 text-red-500" />
                <span class="font-medium">{{ review.reviewer?.name }}</span>
                <span class="text-sm text-muted-foreground">({{ review.reviewer_role }})</span>
              </div>
              <p class="text-sm text-muted-foreground">{{ review.reviewed_at }}</p>
              <p v-if="review.comment" class="text-sm mt-2">{{ review.comment }}</p>
            </div>
          </div>
        </CardContent>
      </Card>

      <Card>
        <CardHeader>
          <CardTitle>Review Action</CardTitle>
        </CardHeader>
        <CardContent>
          <div v-if="!canReview" class="text-center py-8">
            <p class="text-muted-foreground">You don't have permission to review this document.</p>
          </div>
          <div v-else class="space-y-4">
            <div v-if="userReview" class="mb-4 p-3 bg-blue-50 border border-blue-200 rounded-md">
              <p class="text-sm text-blue-800">
                You have already reviewed this document as <strong>{{ userReview.status }}</strong>. 
                You can update your review below.
              </p>
            </div>
            
            <div v-if="!showRejectForm">
              <div class="space-y-2">
                <label class="text-sm font-medium">Comment (Optional)</label>
                <Textarea v-model="approveForm.comment" placeholder="Add your review comment..." :rows="3" />
              </div>
              <div class="flex gap-2 mt-4">
                <Button @click="approve" :disabled="approveForm.processing">
                  <CheckCircle class="w-4 h-4 mr-2" />
                  {{ reviewAction }} Approve
                </Button>
                <Button variant="destructive" @click="showRejectForm = true">
                  <XCircle class="w-4 h-4 mr-2" />
                  {{ reviewAction }} Reject
                </Button>
              </div>
            </div>

            <div v-else>
              <div class="space-y-2">
                <label class="text-sm font-medium">Rejection Reason (Required)</label>
                <Textarea v-model="rejectForm.comment" placeholder="Please provide reason for rejection..." :rows="3" />
              </div>
              <div class="flex gap-2 mt-4">
                <Button variant="destructive" @click="reject" :disabled="rejectForm.processing">
                  <XCircle class="w-4 h-4 mr-2" />
                  Confirm {{ reviewAction }} Reject
                </Button>
                <Button variant="outline" @click="showRejectForm = false">
                  Cancel
                </Button>
              </div>
            </div>
          </div>
        </CardContent>
      </Card>
    </div>
  </AuthenticatedLayout>
</template>
