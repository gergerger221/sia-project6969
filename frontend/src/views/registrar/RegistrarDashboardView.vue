<template>
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="bg-slate-900 rounded-3xl p-6 sm:p-8 border border-slate-800 shadow-xl mb-8 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
      <div>
        <div class="inline-flex items-center space-x-2 px-3 py-1 rounded-full bg-blue-950 text-blue-400 border border-blue-500/30 text-xs font-bold uppercase tracking-wider mb-2">
          <span>Registrar Admissions & Enrollment Management</span>
        </div>
        <h1 class="text-2xl sm:text-3xl font-extrabold text-white">Student Requirement Verification & Queue</h1>
        <p class="text-xs text-slate-400 mt-1">Review incoming student credentials, approve verified applicants, and manage the enrollment queue.</p>
      </div>

      <div class="flex items-center space-x-2">
        <button 
          @click="activeTab = 'applications'"
          :class="activeTab === 'applications' ? 'bg-blue-600 text-white font-bold' : 'bg-slate-800 text-slate-300 hover:bg-slate-700'"
          class="px-4 py-2 rounded-xl text-xs transition"
        >
          Applications ({{ applications.length }})
        </button>
        <button 
          @click="activeTab = 'queue'"
          :class="activeTab === 'queue' ? 'bg-blue-600 text-white font-bold' : 'bg-slate-800 text-slate-300 hover:bg-slate-700'"
          class="px-4 py-2 rounded-xl text-xs transition"
        >
          Enrollment Queue ({{ queueList.length }})
        </button>
      </div>
    </div>

    <!-- Alert Notifications -->
    <div v-if="successMessage" class="p-4 rounded-2xl bg-emerald-950/80 border border-emerald-500 text-emerald-300 text-xs mb-6 flex items-center justify-between shadow-md">
      <span>{{ successMessage }}</span>
      <button @click="successMessage = ''" class="font-bold cursor-pointer">✕</button>
    </div>

    <div v-if="errorMessage" class="p-4 rounded-2xl bg-rose-950/80 border border-rose-500 text-rose-300 text-xs mb-6 flex items-center justify-between shadow-md">
      <span>{{ errorMessage }}</span>
      <button @click="errorMessage = ''" class="font-bold cursor-pointer">✕</button>
    </div>

    <!-- TAB 1: ADMISSION APPLICATIONS REVIEW -->
    <div v-if="activeTab === 'applications'" class="bg-white rounded-3xl p-6 border border-slate-200 shadow-sm">
      <!-- Filter Bar -->
      <div class="flex flex-col sm:flex-row items-center justify-between gap-4 mb-6">
        <div class="relative w-full sm:w-80">
          <input 
            v-model="searchQuery" 
            @input="loadApplications"
            type="text" 
            placeholder="Search by name, LRN, or App #..."
            class="w-full pl-9 pr-4 py-2 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-blue-500"
          />
          <Search class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" />
        </div>

        <div class="flex items-center space-x-2 w-full sm:w-auto">
          <select v-model="filterStatus" @change="loadApplications" class="px-3 py-2 rounded-xl border border-slate-300 text-xs bg-white">
            <option value="">All Statuses</option>
            <option value="Pending">Pending</option>
            <option value="Under Review">Under Review</option>
            <option value="Approved">Approved</option>
            <option value="Queued for Enrollment">Queued for Enrollment</option>
            <option value="Enrolled">Enrolled</option>
          </select>
          <button @click="loadApplications" class="p-2 rounded-xl border border-slate-300 hover:bg-slate-50 text-slate-600" title="Refresh">
            <RefreshCw class="w-4 h-4" />
          </button>
        </div>
      </div>

      <!-- Applications Table -->
      <div class="overflow-x-auto">
        <table class="w-full text-xs text-left border-collapse">
          <thead>
            <tr class="bg-slate-50 text-slate-600 font-bold uppercase tracking-wider border-b border-slate-200">
              <th class="p-3.5">App #</th>
              <th class="p-3.5">Applicant Name</th>
              <th class="p-3.5">DepEd LRN</th>
              <th class="p-3.5">Grade & Strand</th>
              <th class="p-3.5">Docs Uploaded</th>
              <th class="p-3.5">Status</th>
              <th class="p-3.5 text-right">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100">
            <tr v-for="app in applications" :key="app.id" class="hover:bg-slate-50/80 transition">
              <td class="p-3.5 font-mono font-bold text-slate-900">{{ app.application_no }}</td>
              <td class="p-3.5">
                <div class="font-bold text-slate-800">{{ app.last_name }}, {{ app.first_name }} {{ app.middle_name || '' }}</div>
                <div class="text-[11px] text-slate-400">{{ app.email }} • {{ app.contact_number }}</div>
              </td>
              <td class="p-3.5 font-mono">{{ app.lrn || 'N/A' }}</td>
              <td class="p-3.5">
                <span class="font-semibold text-slate-800">{{ app.grade_level_name }}</span>
                <span v-if="app.strand_code" class="ml-1 px-1.5 py-0.5 rounded bg-emerald-50 text-emerald-700 font-bold text-[10px]">
                  {{ app.strand_code }}
                </span>
              </td>
              <td class="p-3.5">
                <span class="font-bold" :class="app.verified_doc_count >= 2 ? 'text-emerald-600' : 'text-amber-600'">
                  {{ app.verified_doc_count }} / {{ app.doc_count }} Verified
                </span>
              </td>
              <td class="p-3.5">
                <span :class="getStatusBadgeClass(app.status)" class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase">
                  {{ app.status }}
                </span>
              </td>
              <td class="p-3.5 text-right">
                <button 
                  @click="openReviewModal(app.id)" 
                  class="px-3 py-1.5 rounded-lg text-xs font-bold bg-blue-600 hover:bg-blue-500 text-white shadow-sm transition"
                >
                  Evaluate Docs
                </button>
              </td>
            </tr>
            <tr v-if="applications.length === 0">
              <td colspan="7" class="p-8 text-center text-slate-400 text-xs">
                No admission applications found matching the selected filter.
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- TAB 2: LIVE ENROLLMENT QUEUE -->
    <div v-if="activeTab === 'queue'" class="bg-white rounded-3xl p-6 border border-slate-200 shadow-sm">
      <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
        <div>
          <h2 class="text-base font-bold text-slate-800">
            {{ queueFilterStatus === 'active' ? 'Active Enrollment Queue' : queueFilterStatus === 'completed' ? 'Completed / Enrolled Queue History' : 'All Enrollment Queues' }}
          </h2>
          <p class="text-xs text-slate-500">
            {{ queueFilterStatus === 'active' ? 'Approved students waiting for Assessment Form generation and Cashier payment.' : 'Historical queue records and payment statuses.' }}
          </p>
        </div>
        
        <div class="flex items-center space-x-2">
          <select 
            v-model="queueFilterStatus" 
            @change="loadQueue" 
            class="px-3 py-1.5 rounded-xl border border-slate-300 text-xs bg-white font-medium focus:ring-2 focus:ring-blue-500"
          >
            <option value="active">Active Pending Queue</option>
            <option value="completed">Completed / Enrolled</option>
            <option value="all">All Queue Entries</option>
          </select>

          <button @click="loadQueue" class="px-3 py-1.5 rounded-xl border border-slate-300 hover:bg-slate-50 text-xs flex items-center space-x-1">
            <RefreshCw class="w-3.5 h-3.5" />
            <span>Refresh Queue</span>
          </button>
        </div>
      </div>

      <div class="overflow-x-auto">
        <table class="w-full text-xs text-left border-collapse">
          <thead>
            <tr class="bg-slate-50 text-slate-600 font-bold uppercase tracking-wider border-b border-slate-200">
              <th class="p-3.5">Queue #</th>
              <th class="p-3.5">Student Name</th>
              <th class="p-3.5">Grade & Strand</th>
              <th class="p-3.5">Assigned Section</th>
              <th class="p-3.5">Assessment Net</th>
              <th class="p-3.5">Queue Status</th>
              <th class="p-3.5">Enrollment Status</th>
              <th class="p-3.5 text-right">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100">
            <tr v-for="q in queueList" :key="q.id" class="hover:bg-slate-50 transition">
              <td class="p-3.5 font-bold font-mono text-blue-600 text-sm">#{{ q.queue_number }}</td>
              <td class="p-3.5 font-bold text-slate-800">{{ q.last_name }}, {{ q.first_name }}</td>
              <td class="p-3.5">{{ q.grade_level_name }} {{ q.strand_code ? '(' + q.strand_code + ')' : '' }}</td>
              <td class="p-3.5 font-semibold text-slate-700">{{ q.section_name || 'Pending Section' }}</td>
              <td class="p-3.5 font-mono font-bold text-slate-900">
                ₱{{ (q.net_payable || 0).toLocaleString('en-US', {minimumFractionDigits: 2}) }}
              </td>
              <td class="p-3.5">
                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-200">
                  {{ q.status }}
                </span>
              </td>
              <td class="p-3.5">
                <span :class="getStatusBadgeClass(q.enrollment_status)" class="px-2 py-0.5 rounded text-[10px] font-bold">
                  {{ q.enrollment_status }}
                </span>
              </td>
              <td class="p-3.5 text-right whitespace-nowrap space-x-1.5">
                <button 
                  type="button" 
                  @click="openPrintForm(q.application_id)" 
                  class="px-2.5 py-1.5 rounded-xl font-bold bg-slate-900 text-white hover:bg-slate-800 text-[11px] shadow-sm transition inline-flex items-center space-x-1"
                  title="Print Official Student Pre-Enrollment / Registration Form"
                >
                  <Printer class="w-3.5 h-3.5" />
                  <span>Print Form</span>
                </button>
                <button 
                  v-if="q.enrollment_status !== 'Enrolled' && q.payment_status !== 'Paid'"
                  type="button" 
                  @click="openUndoModal(q.application_id, q.application_no, `${q.first_name} ${q.last_name}`)" 
                  class="px-2.5 py-1.5 rounded-xl font-bold bg-amber-50 text-amber-800 border border-amber-300 hover:bg-amber-100 text-[11px] shadow-sm transition inline-flex items-center space-x-1"
                  title="Undo approval and return applicant to review"
                >
                  <RotateCcw class="w-3.5 h-3.5 text-amber-700" />
                  <span>Undo Approval</span>
                </button>
                <span v-else class="text-[10px] text-emerald-700 font-bold bg-emerald-50 px-2 py-0.5 rounded border border-emerald-200">
                  Enrolled
                </span>
              </td>
            </tr>
            <tr v-if="queueList.length === 0">
              <td colspan="8" class="p-8 text-center text-slate-400 text-xs">
                Enrollment queue is currently empty.
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- EVALUATION & APPROVAL MODAL -->
    <div v-if="selectedApp" class="fixed inset-0 z-50 bg-black/60 backdrop-blur-sm flex items-center justify-center p-4">
      <div class="bg-white rounded-3xl max-w-3xl w-full max-h-[90vh] overflow-y-auto p-6 sm:p-8 shadow-2xl border border-slate-200">
        <!-- Modal Header -->
        <div class="flex items-center justify-between border-b border-slate-100 pb-4 mb-6">
          <div>
            <span class="px-2.5 py-0.5 rounded text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-200">
              {{ selectedApp.application_no }}
            </span>
            <h3 class="text-xl font-bold text-slate-900 mt-1">
              {{ selectedApp.first_name }} {{ selectedApp.middle_name || '' }} {{ selectedApp.last_name }}
            </h3>
          </div>
          <button @click="selectedApp = null" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-600 flex items-center justify-center font-bold">✕</button>
        </div>

        <!-- Student Profile Overview -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 bg-slate-50 p-4 rounded-2xl border border-slate-200 text-xs mb-6">
          <div><span class="text-slate-400 block">LRN:</span> <strong class="font-mono">{{ selectedApp.lrn || 'N/A' }}</strong></div>
          <div><span class="text-slate-400 block">Grade Level:</span> <strong>{{ selectedApp.grade_level_name }}</strong></div>
          <div><span class="text-slate-400 block">Strand:</span> <strong>{{ selectedApp.strand_name || 'JHS General' }}</strong></div>
          <div><span class="text-slate-400 block">Voucher Subsidy:</span> <strong class="text-emerald-700">{{ selectedApp.voucher_status }}</strong></div>
        </div>

        <!-- Uploaded Documents Evaluation -->
        <div class="mb-6">
          <h4 class="text-xs font-bold uppercase tracking-wider text-slate-700 mb-3">Submitted Admission Credentials</h4>
          <div class="space-y-3">
            <div 
              v-for="doc in selectedApp.documents" 
              :key="doc.id"
              class="p-3.5 rounded-xl border border-slate-200 bg-white flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 text-xs"
            >
              <div>
                <div class="font-bold text-slate-800">{{ doc.document_type }}</div>
                <div class="text-[11px] text-slate-400">{{ doc.original_filename }} ({{ (doc.file_size / 1024).toFixed(1) }} KB)</div>
                <div v-if="doc.verification_notes" class="text-amber-600 text-[11px] mt-0.5">Note: {{ doc.verification_notes }}</div>
              </div>

              <!-- Verification Actions -->
              <div class="flex items-center space-x-2">
                <button 
                  type="button" 
                  @click="openPreviewDoc(doc)" 
                  class="px-2.5 py-1 rounded-lg text-xs font-bold bg-blue-50 text-blue-700 border border-blue-200 hover:bg-blue-100 flex items-center space-x-1"
                  title="View document in pop-up modal"
                >
                  <Eye class="w-3.5 h-3.5" />
                  <span>View</span>
                </button>
                <span :class="getStatusBadgeClass(doc.status)" class="px-2 py-0.5 rounded text-[10px] font-bold uppercase">
                  {{ doc.status }}
                </span>

                <!-- IF OFFICIALLY ENROLLED: LOCK DOCUMENT STATUS -->
                <span v-if="isOfficiallyEnrolled" class="px-2.5 py-1 rounded-lg text-[11px] font-bold bg-slate-100 text-slate-600 border border-slate-300 flex items-center space-x-1" title="Document locked because student is officially enrolled">
                  <Lock class="w-3 h-3 text-slate-500" />
                  <span>Locked</span>
                </span>

                <!-- OTHERWISE SHOW VERIFY & DEFICIENT BUTTONS -->
                <template v-else>
                  <button 
                    @click="verifyDoc(doc.id, 'Verified')"
                    class="px-2.5 py-1 rounded-lg text-xs font-bold bg-emerald-600 hover:bg-emerald-500 text-white shadow-sm transition"
                  >
                    ✓ Verify
                  </button>
                  <button 
                    @click="openDeficiencyModal(doc)"
                    class="px-2.5 py-1 rounded-lg text-xs font-bold bg-rose-600 hover:bg-rose-500 text-white shadow-sm transition"
                  >
                    ✕ Deficient
                  </button>
                </template>
              </div>
            </div>
            <div v-if="!selectedApp.documents || selectedApp.documents.length === 0" class="text-xs text-slate-400 text-center py-4">
              No documents uploaded yet by the applicant.
            </div>
          </div>
        </div>

        <!-- Section Assignment & Approve to Queue (With Deficiency Guard) -->
        <div class="p-5 rounded-2xl border text-xs" :class="isAlreadyQueued ? 'bg-emerald-50/70 border-emerald-200' : 'bg-blue-50/70 border-blue-200'">
          <div class="flex items-center justify-between mb-3">
            <h4 class="font-bold uppercase" :class="isAlreadyQueued ? 'text-emerald-950' : 'text-blue-950'">
              {{ isAlreadyQueued ? 'Enrollment Queue Status (Active)' : 'Approve & Push to Enrollment Queue' }}
            </h4>
            <span v-if="isAlreadyQueued" class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-100 text-emerald-800 border border-emerald-300 flex items-center space-x-1">
              <span>✓ Currently in Queue</span>
            </span>
          </div>

          <!-- IF ALREADY IN QUEUE: SHOW CONFIRMED DETAILS & ONLY UNDO BUTTON (APPROVE BUTTON HIDDEN) -->
          <template v-if="isAlreadyQueued">
            <div class="p-3.5 rounded-xl bg-white border border-emerald-200 text-slate-700 space-y-2 mb-4">
              <div class="flex items-center justify-between">
                <span class="font-semibold text-slate-500">Queue & Evaluation Status:</span>
                <span class="font-bold text-emerald-800">{{ selectedApp.status }}</span>
              </div>
              <div class="flex items-center justify-between">
                <span class="font-semibold text-slate-500">Evaluation Remarks:</span>
                <span class="font-medium text-slate-800">{{ selectedApp.remarks || 'Requirements verified and approved.' }}</span>
              </div>
            </div>

            <div class="flex flex-wrap items-center justify-between gap-3 pt-2 border-t border-emerald-200/60">
              <!-- IF OFFICIALLY ENROLLED: SHOW PERMANENT BADGE INSTEAD OF UNDO BUTTON -->
              <div v-if="isOfficiallyEnrolled" class="px-3.5 py-2 rounded-xl bg-emerald-100/90 text-emerald-900 border border-emerald-300 font-bold text-xs flex items-center space-x-1.5 shadow-sm">
                <CheckCircle2 class="w-4 h-4 text-emerald-700 shrink-0" />
                <span>Student Officially Enrolled (Approval Permanent)</span>
              </div>

              <!-- IF IN QUEUE BUT NOT YET ENROLLED: ALLOW UNDO APPROVAL -->
              <button 
                v-else
                type="button" 
                @click="openUndoModal(selectedApp.id, selectedApp.application_no, `${selectedApp.first_name} ${selectedApp.last_name}`)" 
                class="px-4 py-2.5 rounded-xl font-bold bg-amber-500 hover:bg-amber-400 text-slate-950 text-xs shadow-md transition flex items-center space-x-1.5"
                title="Revert application back to Under Review"
              >
                <RotateCcw class="w-3.5 h-3.5" />
                <span>Undo Approval & Return to Review</span>
              </button>

              <div class="flex items-center space-x-2">
                <button 
                  type="button" 
                  @click="openPrintForm(selectedApp.id)" 
                  class="px-4 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs shadow-md transition flex items-center space-x-1.5"
                >
                  <Printer class="w-3.5 h-3.5" />
                  <span>Print Student Form</span>
                </button>

                <button @click="selectedApp = null" class="px-5 py-2.5 rounded-xl bg-white border border-slate-300 hover:bg-slate-100 text-slate-700 font-bold text-xs shadow-sm">
                  Close
                </button>
              </div>
            </div>
          </template>

          <!-- IF NOT YET QUEUED: SHOW APPROVAL CONTROLS -->
          <template v-else>
            <!-- DEFICIENCY BLOCKER WARNING BANNER -->
            <div v-if="hasDeficiencies" class="mb-4 p-3.5 rounded-xl bg-rose-100/90 border border-rose-300 text-rose-950 flex items-start space-x-2.5">
              <AlertTriangle class="w-4 h-4 text-rose-600 shrink-0 mt-0.5" />
              <div>
                <span class="font-extrabold text-xs text-rose-900 uppercase tracking-wider block">Approval Blocked: Deficiencies Detected</span>
                <p class="text-xs text-rose-800 mt-0.5">
                  This applicant has <strong class="underline">{{ deficientDocsList.length }} deficient requirement(s)</strong> ({{ deficientDocsList.map(d => d.document_type).join(', ') }}).
                  All requirements must be resolved and verified before queuing this student.
                </p>
              </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
              <div>
                <label class="block font-semibold text-slate-700 mb-1">Assign Section *</label>
                <select v-model="approvalForm.section_id" :disabled="hasDeficiencies" class="w-full px-3 py-2 rounded-xl border border-slate-300 bg-white text-xs disabled:bg-slate-100 disabled:opacity-60">
                  <option :value="0">-- Auto Assign Available Section --</option>
                  <option v-for="sec in selectedApp.available_sections" :key="sec.id" :value="sec.id">
                    {{ sec.name }} ({{ sec.current_enrolled }}/{{ sec.max_capacity }} enrolled)
                  </option>
                </select>
              </div>
              <div>
                <label class="block font-semibold text-slate-700 mb-1">Evaluation Remarks</label>
                <input v-model="approvalForm.remarks" :disabled="hasDeficiencies" type="text" placeholder="e.g. Complete documents verified" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs disabled:bg-slate-100 disabled:opacity-60" />
              </div>
            </div>

            <div class="flex items-center justify-end space-x-3 pt-2 border-t border-blue-200/60">
              <button @click="selectedApp = null" class="px-4 py-2 rounded-xl text-slate-600 hover:bg-slate-100 font-semibold text-xs">
                Cancel
              </button>
              <button 
                @click="submitApproval" 
                :disabled="hasDeficiencies || isApproving"
                :class="[
                  hasDeficiencies 
                    ? 'bg-slate-300 text-slate-500 cursor-not-allowed' 
                    : 'bg-blue-600 hover:bg-blue-500 text-white shadow-md'
                ]"
                class="px-6 py-2.5 rounded-xl font-bold text-xs transition flex items-center space-x-1.5"
              >
                <span v-if="isApproving" class="w-3.5 h-3.5 border-2 border-white border-t-transparent rounded-full animate-spin"></span>
                <span>{{ hasDeficiencies ? 'Cannot Approve (Resolve Deficiencies)' : 'Approve & Add to Enrollment Queue' }}</span>
              </button>
            </div>
          </template>
        </div>
      </div>
    </div>

    <!-- DOCUMENT PREVIEW POP-UP MODAL FOR REGISTRAR -->
    <div v-if="previewDoc" class="fixed inset-0 z-50 bg-black/75 backdrop-blur-sm flex items-center justify-center p-4">
      <div class="bg-white rounded-3xl max-w-4xl w-full max-h-[92vh] flex flex-col shadow-2xl overflow-hidden border border-slate-200 animate-in fade-in zoom-in-95 duration-200">
        <div class="p-4 sm:px-6 bg-slate-900 text-white flex items-center justify-between border-b border-slate-800">
          <div class="flex items-center space-x-3">
            <div class="w-9 h-9 rounded-xl bg-blue-950 border border-blue-500/30 text-blue-400 flex items-center justify-center shrink-0">
              <FileText class="w-5 h-5" />
            </div>
            <div>
              <h3 class="font-bold text-sm text-white">{{ previewDoc.document_type }}</h3>
              <p class="text-[11px] text-slate-400 font-mono">{{ previewDoc.original_filename }}</p>
            </div>
          </div>
          <div class="flex items-center space-x-2">
            <a 
              :href="getFileUrl(previewDoc.file_path)" 
              target="_blank" 
              download
              class="px-3 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-semibold flex items-center space-x-1.5 transition"
            >
              <Download class="w-3.5 h-3.5" />
              <span>Download</span>
            </a>
            <button 
              @click="previewDoc = null" 
              class="w-8 h-8 rounded-full bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white flex items-center justify-center font-bold transition"
            >
              ✕
            </button>
          </div>
        </div>

        <div class="flex-1 p-4 sm:p-6 overflow-y-auto bg-slate-100 flex items-center justify-center min-h-[400px]">
          <iframe 
            v-if="isPdf(previewDoc.file_path)" 
            :src="getFileUrl(previewDoc.file_path)" 
            class="w-full h-[70vh] rounded-xl border border-slate-300 bg-white shadow-inner"
          ></iframe>
          <div v-else class="max-h-[70vh] overflow-auto flex items-center justify-center">
            <img 
              :src="getFileUrl(previewDoc.file_path)" 
              :alt="previewDoc.document_type" 
              class="max-w-full max-h-[68vh] rounded-xl shadow-lg object-contain border border-slate-300 bg-white"
            />
          </div>
        </div>
      </div>
    </div>

    <!-- CUSTOM DEFICIENCY REMARK MODAL (REPLACES BROWSER PROMPT) -->
    <div v-if="deficiencyModal.isOpen" class="fixed inset-0 z-50 bg-black/75 backdrop-blur-sm flex items-center justify-center p-4">
      <div class="bg-white rounded-3xl max-w-lg w-full shadow-2xl overflow-hidden border border-slate-200 animate-in fade-in zoom-in-95 duration-150">
        <!-- Header -->
        <div class="p-5 bg-gradient-to-r from-rose-700 to-rose-900 text-white flex items-center justify-between">
          <div class="flex items-center space-x-3">
            <div class="w-10 h-10 rounded-2xl bg-white/15 border border-white/20 flex items-center justify-center text-white shrink-0">
              <AlertTriangle class="w-5 h-5 text-amber-300" />
            </div>
            <div>
              <h3 class="font-extrabold text-base text-white">Mark as Deficient</h3>
              <p class="text-xs text-rose-200">{{ deficiencyModal.docType }}</p>
            </div>
          </div>
          <button @click="closeDeficiencyModal" class="w-8 h-8 rounded-full bg-white/10 hover:bg-white/20 text-white flex items-center justify-center font-bold">
            ✕
          </button>
        </div>

        <!-- Body -->
        <div class="p-6 space-y-4 text-xs">
          <!-- Document Name Tag -->
          <div class="p-3 rounded-xl bg-slate-50 border border-slate-200 flex items-center justify-between text-slate-600">
            <span class="font-semibold text-slate-700">Uploaded File:</span>
            <span class="font-mono text-[11px] text-slate-800 bg-white px-2 py-0.5 rounded border border-slate-200 truncate max-w-[200px]">
              {{ deficiencyModal.fileName }}
            </span>
          </div>

          <!-- Quick Preset Reason Chips -->
          <div>
            <label class="block font-bold text-slate-700 mb-2 uppercase text-[10px] tracking-wider">
              Quick Preset Reasons (Click to Select)
            </label>
            <div class="flex flex-wrap gap-1.5">
              <button 
                type="button" 
                v-for="(preset, pIdx) in deficiencyPresets" 
                :key="pIdx"
                @click="deficiencyModal.reason = preset"
                :class="deficiencyModal.reason === preset ? 'bg-rose-600 text-white font-bold border-rose-600' : 'bg-slate-100 text-slate-700 border-slate-200 hover:bg-slate-200'"
                class="px-2.5 py-1 rounded-lg border text-[11px] transition text-left"
              >
                {{ preset }}
              </button>
            </div>
          </div>

          <!-- Custom Remarks Textarea -->
          <div>
            <label class="block font-bold text-slate-700 mb-1">Specific Deficiency Instructions for Student *</label>
            <textarea 
              v-model="deficiencyModal.reason" 
              rows="3" 
              placeholder="e.g. Blurry photo. Please upload a clear scanned copy showing complete text and signatures."
              class="w-full p-3 rounded-xl border border-slate-300 focus:ring-2 focus:ring-rose-500 text-xs text-slate-800"
            ></textarea>
            <span class="text-[10px] text-slate-400">This instruction will appear directly on the student's admission dashboard.</span>
          </div>
        </div>

        <!-- Footer Actions -->
        <div class="p-4 bg-slate-50 border-t border-slate-100 flex items-center justify-end space-x-2">
          <button 
            type="button" 
            @click="closeDeficiencyModal" 
            class="px-4 py-2 rounded-xl text-slate-600 hover:bg-slate-200 font-semibold text-xs transition"
          >
            Cancel
          </button>
          <button 
            type="button" 
            @click="submitDeficiencyModal" 
            :disabled="!deficiencyModal.reason.trim() || isSubmittingDeficiency"
            class="px-5 py-2.5 rounded-xl font-bold bg-rose-600 hover:bg-rose-500 disabled:opacity-50 text-white text-xs shadow-md transition flex items-center space-x-1.5"
          >
            <span v-if="isSubmittingDeficiency" class="w-3.5 h-3.5 border-2 border-white border-t-transparent rounded-full animate-spin"></span>
            <span>Confirm Deficiency Remark</span>
          </button>
        </div>
      </div>
    </div>

    <!-- CUSTOM UNDO APPROVAL CONFIRMATION MODAL -->
    <div v-if="undoModal.isOpen" class="fixed inset-0 z-50 bg-black/70 backdrop-blur-sm flex items-center justify-center p-4">
      <div class="bg-white rounded-3xl max-w-md w-full shadow-2xl overflow-hidden border border-slate-200 animate-in fade-in zoom-in-95 duration-200">
        <!-- Modal Header -->
        <div class="p-5 bg-gradient-to-r from-amber-500 to-orange-500 text-white flex items-center justify-between">
          <div class="flex items-center space-x-3">
            <div class="w-10 h-10 rounded-2xl bg-white/20 backdrop-blur-sm flex items-center justify-center text-white shrink-0">
              <RotateCcw class="w-5 h-5" />
            </div>
            <div>
              <h3 class="font-extrabold text-base leading-tight">Undo Approval</h3>
              <p class="text-amber-100 text-xs font-medium">Revoke Enrollment Queue Status</p>
            </div>
          </div>
          <button 
            type="button" 
            @click="closeUndoModal" 
            class="w-8 h-8 rounded-full bg-black/10 hover:bg-black/20 text-white flex items-center justify-center font-bold text-sm transition"
          >
            ✕
          </button>
        </div>

        <!-- Body Content -->
        <div class="p-6 space-y-4 text-xs">
          <!-- Applicant Info Pill -->
          <div class="p-3.5 rounded-2xl bg-amber-50 border border-amber-200 text-slate-800">
            <div class="flex items-center justify-between mb-1">
              <span class="text-slate-500 font-semibold">Application Number:</span>
              <span class="font-mono font-bold text-amber-900 bg-amber-200/60 px-2 py-0.5 rounded text-[11px]">{{ undoModal.appNo }}</span>
            </div>
            <div v-if="undoModal.studentName" class="flex items-center justify-between">
              <span class="text-slate-500 font-semibold">Student Name:</span>
              <span class="font-bold text-slate-900">{{ undoModal.studentName }}</span>
            </div>
          </div>

          <p class="text-slate-600 leading-relaxed font-medium">
            Are you sure you want to revert this approved application? This action will perform the following adjustments:
          </p>

          <!-- Impact Checklist -->
          <div class="space-y-2 bg-slate-50 p-3.5 rounded-2xl border border-slate-200">
            <div class="flex items-start space-x-2.5">
              <span class="w-5 h-5 rounded-full bg-amber-100 text-amber-800 font-bold flex items-center justify-center shrink-0 text-[10px] mt-0.5">1</span>
              <span class="text-slate-700 font-medium"><strong>Remove from Queue:</strong> The applicant is pulled out of Treasury assessment.</span>
            </div>
            <div class="flex items-start space-x-2.5">
              <span class="w-5 h-5 rounded-full bg-amber-100 text-amber-800 font-bold flex items-center justify-center shrink-0 text-[10px] mt-0.5">2</span>
              <span class="text-slate-700 font-medium"><strong>Release Section Seat:</strong> The reserved section capacity counter is decremented by 1.</span>
            </div>
            <div class="flex items-start space-x-2.5">
              <span class="w-5 h-5 rounded-full bg-amber-100 text-amber-800 font-bold flex items-center justify-center shrink-0 text-[10px] mt-0.5">3</span>
              <span class="text-slate-700 font-medium"><strong>Revert Status:</strong> Application returns to <em>"Under Review"</em> for re-evaluation.</span>
            </div>
          </div>
        </div>

        <!-- Actions -->
        <div class="p-4 bg-slate-50 border-t border-slate-100 flex items-center justify-end space-x-2.5">
          <button 
            type="button" 
            @click="closeUndoModal" 
            :disabled="isUndoing"
            class="px-4 py-2.5 rounded-xl text-slate-600 hover:bg-slate-200 font-semibold text-xs transition"
          >
            Keep Approved
          </button>
          <button 
            type="button" 
            @click="confirmUndoApproval" 
            :disabled="isUndoing"
            class="px-5 py-2.5 rounded-xl font-bold bg-amber-500 hover:bg-amber-400 disabled:opacity-50 text-slate-950 text-xs shadow-md transition flex items-center space-x-1.5"
          >
            <span v-if="isUndoing" class="w-3.5 h-3.5 border-2 border-slate-950 border-t-transparent rounded-full animate-spin"></span>
            <span>{{ isUndoing ? 'Undoing Approval...' : 'Confirm Undo Approval' }}</span>
          </button>
        </div>
      </div>
    </div>

    <!-- REGISTRAR PRINTABLE STUDENT PRE-ENROLLMENT & ASSESSMENT FORM -->
    <div v-if="selectedPrintForm" class="fixed inset-0 z-50 bg-slate-900/80 backdrop-blur-sm overflow-y-auto p-4 sm:p-6 flex flex-col items-center">
      <!-- Action Bar (Hidden in Print) -->
      <div class="no-print w-full max-w-4xl flex items-center justify-between mb-4 bg-white p-4 rounded-2xl border border-slate-200 shadow-lg">
        <button 
          @click="selectedPrintForm = null" 
          class="px-4 py-2 rounded-xl text-slate-700 hover:bg-slate-100 font-semibold text-xs transition flex items-center space-x-2"
        >
          <ArrowLeft class="w-4 h-4" />
          <span>Back to Registrar Portal</span>
        </button>

        <div class="flex items-center space-x-3">
          <button 
            @click="printStudentForm" 
            class="px-6 py-2.5 rounded-xl font-bold bg-slate-900 hover:bg-slate-800 text-white text-xs shadow-md transition flex items-center space-x-2"
          >
            <Printer class="w-4 h-4" />
            <span>Print Student Form</span>
          </button>
        </div>
      </div>

      <!-- Printable Document Sheet (1-Page Optimized) -->
      <div class="bg-white text-slate-900 rounded-3xl p-6 sm:p-8 print:p-3.5 border-2 border-slate-800 shadow-2xl max-w-4xl w-full text-xs font-sans print:border-slate-800 print:shadow-none print:rounded-none">
        <!-- School & DepEd Header -->
        <div class="text-center border-b-2 border-slate-800 pb-2.5 mb-3 print:pb-2 print:mb-2">
          <div class="text-[11px] print:text-[9.5px] font-semibold tracking-widest uppercase text-slate-600">Republic of the Philippines • Department of Education</div>
          <h2 class="text-lg print:text-base font-extrabold tracking-tight uppercase mt-0.5 mb-0.5 text-slate-900">SIA HIGH SCHOOL - BASIC EDUCATION DEPARTMENT</h2>
          <p class="text-xs print:text-[10px] text-slate-600 font-medium">Office of the Registrar • Student Registration & Pre-Enrollment Assessment Form</p>
          <div class="inline-block mt-1.5 px-3 py-0.5 rounded bg-slate-900 text-white text-xs print:text-[10px] font-bold font-mono uppercase tracking-wider">
            SY 2026-2027 • {{ selectedPrintForm.grade_category === 'SHS' ? '1st Semester' : 'Full Academic Year' }}
          </div>
        </div>

        <!-- Student & Academic Meta Grid -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 print:gap-2 text-xs print:text-[10.5px] mb-3 print:mb-2 bg-slate-50 p-3 print:p-2 rounded-lg border border-slate-200">
          <div>
            <span class="text-slate-500 block text-[9.5px] uppercase font-bold">Generated Student Number:</span>
            <span class="font-bold font-mono text-sm print:text-xs text-blue-700">{{ selectedPrintForm.student_no || selectedPrintForm.enrollment_info?.student_no || 'PENDING' }}</span>
          </div>
          <div>
            <span class="text-slate-500 block text-[9.5px] uppercase font-bold">Applicant / Student Name:</span>
            <span class="font-bold text-xs print:text-[11px] uppercase text-slate-900">{{ selectedPrintForm.last_name }}, {{ selectedPrintForm.first_name }} {{ selectedPrintForm.middle_name || '' }}</span>
          </div>
          <div>
            <span class="text-slate-500 block text-[9.5px] uppercase font-bold">DepEd LRN:</span>
            <span class="font-bold font-mono text-xs print:text-[11px]">{{ selectedPrintForm.lrn || 'N/A' }}</span>
          </div>
          <div>
            <span class="text-slate-500 block text-[9.5px] uppercase font-bold">Application Ref #:</span>
            <span class="font-bold font-mono text-xs print:text-[11px] text-slate-700">{{ selectedPrintForm.application_no }}</span>
          </div>
          <div>
            <span class="text-slate-500 block text-[9.5px] uppercase font-bold">Grade & Program:</span>
            <span class="font-bold text-emerald-800 text-xs print:text-[11px]">{{ selectedPrintForm.grade_level_name }} {{ selectedPrintForm.strand_code ? '(' + selectedPrintForm.strand_code + ')' : '' }}</span>
          </div>
          <div>
            <span class="text-slate-500 block text-[9.5px] uppercase font-bold">Assigned Section:</span>
            <span class="font-bold text-blue-800 text-xs print:text-[11px]">{{ selectedPrintForm.queue_info?.section_name || selectedPrintForm.enrollment_info?.section_name || 'Main Section' }}</span>
          </div>
          <div>
            <span class="text-slate-500 block text-[9.5px] uppercase font-bold">Assigned Room:</span>
            <span class="font-semibold text-slate-700 text-xs print:text-[11px]">{{ selectedPrintForm.queue_info?.section_room || selectedPrintForm.enrollment_info?.section_room || 'Room Assigned' }}</span>
          </div>
          <div>
            <span class="text-slate-500 block text-[9.5px] uppercase font-bold">Contact Number:</span>
            <span class="font-mono text-xs print:text-[11px] text-slate-700">{{ selectedPrintForm.contact_number }}</span>
          </div>
        </div>

        <!-- Enrolled Subjects & Section Schedule Table -->
        <div class="mb-3 print:mb-2">
          <h3 class="text-xs print:text-[10.5px] font-bold uppercase tracking-wider text-slate-700 mb-1.5 print:mb-1">Enrolled Subjects & Section Class Schedule</h3>
          <table class="w-full text-xs print:text-[10px] text-left border-collapse border border-slate-300">
            <thead>
              <tr class="bg-slate-100 border-b border-slate-300 font-bold">
                <th class="p-1.5 print:py-0.5 print:px-1.5 border-r border-slate-300">Subject Code</th>
                <th class="p-1.5 print:py-0.5 print:px-1.5 border-r border-slate-300">Descriptive Title</th>
                <th class="p-1.5 print:py-0.5 print:px-1.5 border-r border-slate-300">Classification</th>
                <th class="p-1.5 print:py-0.5 print:px-1.5 border-r border-slate-300">Schedule / Day</th>
                <th class="p-1.5 print:py-0.5 print:px-1.5 border-r border-slate-300">Time Slot</th>
                <th class="p-1.5 print:py-0.5 print:px-1.5 text-center">Units</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="sub in (selectedPrintForm.enrolled_subjects || [])" :key="sub.id" class="border-b border-slate-200">
                <td class="p-1.5 print:py-0.5 print:px-1.5 font-mono font-bold border-r border-slate-200">{{ sub.subject_code }}</td>
                <td class="p-1.5 print:py-0.5 print:px-1.5 border-r border-slate-200">{{ sub.subject_title }}</td>
                <td class="p-1.5 print:py-0.5 print:px-1.5 border-r border-slate-200">{{ sub.category }}</td>
                <td class="p-1.5 print:py-0.5 print:px-1.5 border-r border-slate-200 font-medium">{{ sub.day_of_week || 'Mon-Fri' }}</td>
                <td class="p-1.5 print:py-0.5 print:px-1.5 border-r border-slate-200 font-mono text-[10.5px] print:text-[9.5px]">
                  {{ sub.time_start && sub.time_end ? sub.time_start.slice(0,5) + ' - ' + sub.time_end.slice(0,5) : '08:00 - 09:00' }}
                </td>
                <td class="p-1.5 print:py-0.5 print:px-1.5 text-center font-bold">{{ sub.units || '1.0' }}</td>
              </tr>
              <tr v-if="!selectedPrintForm.enrolled_subjects || selectedPrintForm.enrolled_subjects.length === 0" class="border-b border-slate-200">
                <td colspan="6" class="p-2 text-center text-slate-400 italic">Curriculum core subjects automatically assigned based on track and section loading.</td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Fee Assessment & Subsidy Breakdown -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 print:gap-3 pt-2.5 print:pt-1.5 border-t border-slate-200 text-xs print:text-[10px]">
          <div>
            <h4 class="font-bold text-slate-800 uppercase mb-1">DepEd Subsidy / Voucher Information</h4>
            <div class="p-2.5 print:p-2 bg-slate-50 border border-slate-200 rounded-lg text-slate-600 space-y-0.5">
              <div>Voucher Category: <span class="font-bold text-slate-900">{{ selectedPrintForm.voucher_status || 'None' }}</span></div>
              <div>School of Origin: <span class="font-bold text-slate-900">{{ selectedPrintForm.last_school_attended }}</span></div>
              <div class="text-[9.5px] text-slate-500 italic mt-0.5">Government voucher subsidies apply directly to base assessment fees upon Treasury verification.</div>
            </div>
          </div>

          <div>
            <h4 class="font-bold text-slate-800 uppercase mb-1">Treasury Assessment Summary</h4>
            <div class="bg-slate-50 p-2.5 print:p-2 rounded-lg border border-slate-200 space-y-0.5 font-mono">
              <div class="flex justify-between">
                <span>Tuition Base:</span>
                <span>₱{{ Number(selectedPrintForm.assessment_info?.total_tuition || 12000).toLocaleString('en-US', {minimumFractionDigits: 2}) }}</span>
              </div>
              <div class="flex justify-between">
                <span>Miscellaneous & Lab Fees:</span>
                <span>₱{{ ((Number(selectedPrintForm.assessment_info?.total_miscellaneous) || 4000) + (Number(selectedPrintForm.assessment_info?.total_laboratory) || 2500)).toLocaleString('en-US', {minimumFractionDigits: 2}) }}</span>
              </div>
              <div class="flex justify-between text-emerald-700 font-bold border-t border-slate-200 pt-0.5">
                <span>Less: DepEd Voucher Subsidy:</span>
                <span>- ₱{{ Number(selectedPrintForm.assessment_info?.voucher_discount || 0).toLocaleString('en-US', {minimumFractionDigits: 2}) }}</span>
              </div>
              <div class="flex justify-between text-xs print:text-[11px] font-extrabold text-slate-900 border-t-2 border-slate-800 pt-0.5">
                <span>Total Net Payable:</span>
                <span>₱{{ Number(selectedPrintForm.assessment_info?.net_payable || 18500).toLocaleString('en-US', {minimumFractionDigits: 2}) }}</span>
              </div>
              <div class="flex justify-between text-[10px] text-slate-600 pt-0.5">
                <span>Required Minimum Downpayment:</span>
                <span>₱3,000.00</span>
              </div>
            </div>
          </div>
        </div>

        <!-- Signatures & Authority Section -->
        <div class="grid grid-cols-3 gap-6 print:gap-4 text-center text-xs print:text-[10px] mt-6 print:mt-4 pt-4 print:pt-2.5 border-t border-slate-300">
          <div>
            <div class="border-b border-slate-400 pb-0.5 mb-0.5 font-bold uppercase">{{ selectedPrintForm.first_name }} {{ selectedPrintForm.last_name }}</div>
            <span class="text-[9px] text-slate-500 uppercase">Student / Applicant Signature</span>
          </div>
          <div>
            <div class="border-b border-slate-400 pb-0.5 mb-0.5 font-bold">Office of the Registrar</div>
            <span class="text-[9px] text-slate-500 uppercase">Evaluated & Approved</span>
          </div>
          <div>
            <div class="border-b border-slate-400 pb-0.5 mb-0.5 font-bold">Treasury / Cashier</div>
            <span class="text-[9px] text-slate-500 uppercase">Official Receipt Stamp</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { Search, RefreshCw, Eye, Download, FileText, AlertTriangle, RotateCcw, Printer, ArrowLeft, Lock, CheckCircle2 } from 'lucide-vue-next';
import api, { getFileUrl } from '../../services/api';

const activeTab = ref('applications');
const applications = ref([]);
const queueList = ref([]);
const queueFilterStatus = ref('active');
const selectedApp = ref(null);
const selectedPrintForm = ref(null);
const searchQuery = ref('');
const filterStatus = ref('');
const isApproving = ref(false);
const isSubmittingDeficiency = ref(false);
const isUndoing = ref(false);
const successMessage = ref('');
const errorMessage = ref('');

const undoModal = ref({
  isOpen: false,
  appId: null,
  appNo: '',
  studentName: ''
});

const isOfficiallyEnrolled = computed(() => {
  return selectedApp.value?.status === 'Enrolled';
});

const openUndoModal = (appId, appNo, studentName = '') => {
  if (isOfficiallyEnrolled.value) {
    errorMessage.value = 'Cannot undo approval: This student has already completed treasury payment and is officially enrolled.';
    return;
  }
  undoModal.value = {
    isOpen: true,
    appId,
    appNo,
    studentName
  };
};

const closeUndoModal = () => {
  undoModal.value.isOpen = false;
};

const confirmUndoApproval = async () => {
  if (!undoModal.value.appId) return;
  if (isOfficiallyEnrolled.value) {
    errorMessage.value = 'Cannot undo approval: This student is already officially enrolled.';
    return;
  }
  isUndoing.value = true;
  errorMessage.value = '';
  try {
    const res = await api.undoApproval(undoModal.value.appId);
    successMessage.value = res.message || `Approval successfully undone for #${undoModal.value.appNo}.`;
    const reversedAppId = undoModal.value.appId;
    closeUndoModal();
    if (selectedApp.value && selectedApp.value.id === reversedAppId) {
      selectedApp.value = null;
    }
    await Promise.all([loadApplications(), loadQueue()]);
  } catch (err) {
    errorMessage.value = err.message || 'Failed to undo approval.';
  } finally {
    isUndoing.value = false;
  }
};

const deficientDocsList = computed(() => {
  return selectedApp.value?.documents?.filter(d => d.status === 'Deficient' || d.status === 'Rejected') || [];
});

const hasDeficiencies = computed(() => {
  return deficientDocsList.value.length > 0;
});

const isAlreadyQueued = computed(() => {
  return ['Queued for Enrollment', 'Approved', 'Assessed', 'Enrolled'].includes(selectedApp.value?.status);
});

const deficiencyPresets = [
  'Blurry or unreadable scanned copy. Please re-upload.',
  'Missing back page / Principal or Adviser signature.',
  'Unauthenticated PSA SECPA certificate copy.',
  'Incorrect or invalid document uploaded for this requirement.',
  'Expired or outdated certificate.'
];

const deficiencyModal = ref({
  isOpen: false,
  docId: null,
  docType: '',
  fileName: '',
  reason: ''
});

const openDeficiencyModal = (doc) => {
  if (!doc) return;
  if (isOfficiallyEnrolled.value) {
    alert('Cannot mark documents as deficient: This student is already officially enrolled.');
    return;
  }
  deficiencyModal.value = {
    isOpen: true,
    docId: doc.id,
    docType: doc.document_type,
    fileName: doc.original_filename,
    reason: doc.verification_notes || deficiencyPresets[0]
  };
};

const closeDeficiencyModal = () => {
  deficiencyModal.value.isOpen = false;
};

const submitDeficiencyModal = async () => {
  if (!deficiencyModal.value.docId) return;
  isSubmittingDeficiency.value = true;
  try {
    await api.verifyDocument({
      document_id: deficiencyModal.value.docId,
      status: 'Deficient',
      verification_notes: deficiencyModal.value.reason.trim()
    });
    closeDeficiencyModal();
    if (selectedApp.value) {
      await openReviewModal(selectedApp.value.id);
    }
  } catch (err) {
    alert(err.message || 'Failed to update document status.');
  } finally {
    isSubmittingDeficiency.value = false;
  }
};

const approvalForm = ref({
  section_id: 0,
  remarks: 'Requirements verified and approved.'
});

const getStatusBadgeClass = (status) => {
  if (status === 'Enrolled' || status === 'Verified') return 'bg-emerald-100 text-emerald-800';
  if (status === 'Approved' || status === 'Queued for Enrollment') return 'bg-blue-100 text-blue-800';
  if (status === 'Deficient' || status === 'Rejected' || status === 'Requirements Deficient') return 'bg-rose-100 text-rose-800';
  return 'bg-amber-100 text-amber-800';
};

const loadApplications = async () => {
  try {
    let params = '';
    if (filterStatus.value) params += `status=${filterStatus.value}&`;
    if (searchQuery.value) params += `search=${encodeURIComponent(searchQuery.value)}`;
    const res = await api.getApplications(params);
    applications.value = res.data;
  } catch (err) {
    console.error('Failed to load applications:', err);
  }
};

const loadQueue = async () => {
  try {
    const params = queueFilterStatus.value ? `status=${queueFilterStatus.value}` : '';
    const res = await api.getEnrollmentQueue(params);
    queueList.value = res.data;
  } catch (err) {
    console.error('Failed to load queue:', err);
  }
};

const openReviewModal = async (id) => {
  try {
    const res = await api.getApplicationDetails(id);
    selectedApp.value = res.data;
    approvalForm.value.section_id = res.data.available_sections?.[0]?.id || 0;
  } catch (err) {
    console.error('Failed to load application details:', err);
  }
};

const verifyDoc = async (docId, status) => {
  if (isOfficiallyEnrolled.value) {
    alert('Cannot modify document verification: This student is already officially enrolled.');
    return;
  }

  if (status === 'Deficient') {
    const doc = selectedApp.value?.documents?.find(d => d.id === docId);
    if (doc) {
      openDeficiencyModal(doc);
      return;
    }
  }

  try {
    await api.verifyDocument({
      document_id: docId,
      status: 'Verified',
      verification_notes: 'Document verified by Registrar'
    });
    if (selectedApp.value) {
      await openReviewModal(selectedApp.value.id);
    }
  } catch (err) {
    console.error('Verification failed:', err);
  }
};

const submitApproval = async () => {
  if (!selectedApp.value || hasDeficiencies.value) return;
  isApproving.value = true;
  errorMessage.value = '';
  const appNo = selectedApp.value.application_no || 'Applicant';
  try {
    const res = await api.approveAndQueue({
      application_id: selectedApp.value.id,
      section_id: approvalForm.value.section_id,
      remarks: approvalForm.value.remarks
    });
    successMessage.value = res?.message || `Applicant ${appNo} successfully approved & added to the Enrollment Queue!`;
    selectedApp.value = null;
    await Promise.all([loadApplications(), loadQueue()]);
  } catch (err) {
    errorMessage.value = err.message || 'Approval failed.';
  } finally {
    isApproving.value = false;
  }
};

const previewDoc = ref(null);

const openPreviewDoc = (doc) => {
  if (!doc) return;
  previewDoc.value = doc;
};

const isPdf = (filePath) => {
  if (!filePath) return false;
  return filePath.toLowerCase().endsWith('.pdf');
};

const openPrintForm = async (appId) => {
  try {
    const res = await api.getApplicationDetails(appId);
    selectedPrintForm.value = res.data;
  } catch (err) {
    alert(err.message || 'Failed to load student registration details.');
  }
};

const printStudentForm = () => {
  window.print();
};

onMounted(() => {
  loadApplications();
  loadQueue();
});
</script>
