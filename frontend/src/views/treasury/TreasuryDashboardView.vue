<template>
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Top Header -->
    <div class="no-print bg-slate-900 rounded-3xl p-6 sm:p-8 border border-slate-800 shadow-xl mb-8 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
      <div>
        <div class="inline-flex items-center space-x-2 px-3 py-1 rounded-full bg-emerald-950 text-emerald-400 border border-emerald-500/30 text-xs font-bold uppercase tracking-wider mb-2">
          <span>Treasury & Finance Management</span>
        </div>
        <h1 class="text-2xl sm:text-3xl font-extrabold text-white">Enrollment Billing & Official Receipts</h1>
        <p class="text-xs text-slate-400 mt-1">Collect tuition downpayments, apply DepEd voucher subsidies, and issue Official Receipts.</p>
      </div>

      <div class="flex items-center space-x-2">
        <button 
          @click="activeTab = 'assessments'"
          :class="activeTab === 'assessments' ? 'bg-emerald-600 text-white font-bold' : 'bg-slate-800 text-slate-300 hover:bg-slate-700'"
          class="px-4 py-2 rounded-xl text-xs transition"
        >
          Billing Assessments ({{ assessments.length }})
        </button>
        <button 
          @click="activeTab = 'fees'"
          :class="activeTab === 'fees' ? 'bg-emerald-600 text-white font-bold' : 'bg-slate-800 text-slate-300 hover:bg-slate-700'"
          class="px-4 py-2 rounded-xl text-xs transition"
        >
          Fee Structures
        </button>
      </div>
    </div>

    <!-- Alert Notification -->
    <div v-if="successMessage" class="no-print p-4 rounded-2xl bg-emerald-950/80 border border-emerald-500 text-emerald-300 text-xs mb-6 flex items-center justify-between">
      <span>{{ successMessage }}</span>
      <button @click="successMessage = ''" class="font-bold">✕</button>
    </div>

    <!-- TAB 1: BILLING ASSESSMENTS & CASHIER POS -->
    <div v-if="activeTab === 'assessments'" class="no-print bg-white rounded-3xl p-6 border border-slate-200 shadow-sm">
      <div class="flex flex-col sm:flex-row items-center justify-between gap-4 mb-6">
        <div class="relative w-full sm:w-80">
          <input 
            v-model="searchQuery" 
            @input="loadAssessments"
            type="text" 
            placeholder="Search by student name, Ass #, or Enr #..."
            class="w-full pl-9 pr-4 py-2 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-emerald-500"
          />
          <Search class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" />
        </div>

        <div class="flex items-center space-x-2 w-full sm:w-auto">
          <select v-model="filterStatus" @change="loadAssessments" class="px-3 py-2 rounded-xl border border-slate-300 text-xs bg-white">
            <option value="">All Payment Statuses</option>
            <option value="Unpaid">Unpaid</option>
            <option value="Partially Paid">Partially Paid</option>
            <option value="Fully Paid">Fully Paid</option>
          </select>
          <button @click="loadAssessments" class="p-2 rounded-xl border border-slate-300 hover:bg-slate-50 text-slate-600">
            <RefreshCw class="w-4 h-4" />
          </button>
        </div>
      </div>

      <div class="overflow-x-auto">
        <table class="w-full text-xs text-left border-collapse">
          <thead>
            <tr class="bg-slate-50 text-slate-600 font-bold uppercase tracking-wider border-b border-slate-200">
              <th class="p-3.5">Ass # / Enr #</th>
              <th class="p-3.5">Student / Applicant</th>
              <th class="p-3.5">Grade & Strand</th>
              <th class="p-3.5">Voucher Subsidy</th>
              <th class="p-3.5">Net Payable</th>
              <th class="p-3.5">Total Paid</th>
              <th class="p-3.5">Status</th>
              <th class="p-3.5 text-right">Cashier Action</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100">
            <tr v-for="ass in assessments" :key="ass.id" class="hover:bg-slate-50 transition">
              <td class="p-3.5">
                <div class="font-bold font-mono text-slate-900">{{ ass.assessment_no }}</div>
                <div class="text-[11px] font-mono text-slate-400">{{ ass.enrollment_no }}</div>
              </td>
              <td class="p-3.5">
                <div class="font-bold text-slate-800">{{ ass.last_name }}, {{ ass.first_name }} {{ ass.middle_name || '' }}</div>
                <div v-if="ass.permanent_student_no" class="text-[11px] font-mono text-emerald-700 font-bold">
                  ID: {{ ass.permanent_student_no }}
                </div>
              </td>
              <td class="p-3.5">{{ ass.grade_level_name }} {{ ass.strand_code ? '(' + ass.strand_code + ')' : '' }}</td>
              <td class="p-3.5">
                <span v-if="ass.voucher_discount > 0" class="text-emerald-700 font-bold">
                  - ₱{{ Number(ass.voucher_discount).toLocaleString('en-US', {minimumFractionDigits: 2}) }}
                </span>
                <span v-else class="text-slate-400">None</span>
              </td>
              <td class="p-3.5 font-mono font-bold text-slate-900">
                ₱{{ Number(ass.net_payable).toLocaleString('en-US', {minimumFractionDigits: 2}) }}
              </td>
              <td class="p-3.5 font-mono font-bold text-emerald-600">
                ₱{{ Number(ass.total_paid).toLocaleString('en-US', {minimumFractionDigits: 2}) }}
              </td>
              <td class="p-3.5">
                <span :class="getStatusBadgeClass(ass.status)" class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase">
                  {{ ass.status }}
                </span>
              </td>
              <td class="p-3.5 text-right">
                <button 
                  @click="openPaymentModal(ass.id)" 
                  class="px-3 py-1.5 rounded-lg text-xs font-bold bg-emerald-600 hover:bg-emerald-500 text-white shadow-sm transition"
                >
                  {{ ass.status === 'Fully Paid' ? 'View OR & History' : 'Process Payment' }}
                </button>
              </td>
            </tr>
            <tr v-if="assessments.length === 0">
              <td colspan="8" class="p-8 text-center text-slate-400 text-xs">
                No assessments found.
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- TAB 2: FEE STRUCTURES CATALOG -->
    <div v-if="activeTab === 'fees'" class="no-print bg-white rounded-3xl p-6 border border-slate-200 shadow-sm">
      <div class="mb-4">
        <h2 class="text-base font-bold text-slate-800">DepEd Compliant Standard Fee Matrix</h2>
        <p class="text-xs text-slate-500">Configured tuition, miscellaneous, laboratory, and campus development fees for SY 2026-2027.</p>
      </div>

      <div class="overflow-x-auto">
        <table class="w-full text-xs text-left border-collapse">
          <thead>
            <tr class="bg-slate-50 text-slate-600 font-bold uppercase tracking-wider border-b border-slate-200">
              <th class="p-3.5">Grade Level / Strand</th>
              <th class="p-3.5">Fee Category</th>
              <th class="p-3.5">Fee Description</th>
              <th class="p-3.5 text-right">Amount (PHP)</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100">
            <tr v-for="fee in feeStructuresList" :key="fee.id" class="hover:bg-slate-50 transition">
              <td class="p-3.5 font-semibold text-slate-800">
                {{ fee.grade_level_name }} {{ fee.strand_code ? '(' + fee.strand_code + ')' : '' }}
              </td>
              <td class="p-3.5">
                <span class="px-2 py-0.5 rounded bg-slate-100 font-bold text-[10px]">{{ fee.category_name }}</span>
              </td>
              <td class="p-3.5">{{ fee.name }}</td>
              <td class="p-3.5 text-right font-mono font-bold text-slate-900">
                ₱{{ Number(fee.amount).toLocaleString('en-US', {minimumFractionDigits: 2}) }}
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- PAYMENT COLLECTION & OR ISSUANCE MODAL -->
    <div v-if="selectedAssessment" class="no-print fixed inset-0 z-50 bg-black/60 backdrop-blur-sm flex items-center justify-center p-4">
      <div class="bg-white rounded-3xl max-w-2xl w-full max-h-[90vh] overflow-y-auto p-6 sm:p-8 shadow-2xl border border-slate-200">
        <div class="flex items-center justify-between border-b border-slate-100 pb-4 mb-6">
          <div>
            <span class="px-2.5 py-0.5 rounded text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
              {{ selectedAssessment.assessment_no }}
            </span>
            <h3 class="text-xl font-bold text-slate-900 mt-1">
              {{ selectedAssessment.first_name }} {{ selectedAssessment.last_name }}
            </h3>
          </div>
          <button @click="selectedAssessment = null" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-600 flex items-center justify-center font-bold">✕</button>
        </div>

        <!-- Assessment Financial Breakdown -->
        <div class="bg-slate-50 p-4 rounded-2xl border border-slate-200 text-xs font-mono mb-6 space-y-1.5">
          <div class="flex justify-between text-slate-600">
            <span>Tuition Base:</span>
            <span>₱{{ Number(selectedAssessment.total_tuition || 0).toLocaleString('en-US', {minimumFractionDigits: 2}) }}</span>
          </div>
          <div class="flex justify-between text-slate-600">
            <span>Misc & Lab Fees:</span>
            <span>₱{{ ((Number(selectedAssessment.total_miscellaneous) || 0) + (Number(selectedAssessment.total_laboratory) || 0) + (Number(selectedAssessment.total_other_fees) || 0)).toLocaleString('en-US', {minimumFractionDigits: 2}) }}</span>
          </div>
          <div class="flex justify-between text-emerald-700 font-bold">
            <span>DepEd Voucher / ESC Subsidy:</span>
            <span>- ₱{{ Number(selectedAssessment.voucher_discount || 0).toLocaleString('en-US', {minimumFractionDigits: 2}) }}</span>
          </div>
          <div class="flex justify-between font-bold text-slate-900 border-t border-slate-200 pt-1 text-sm">
            <span>Total Net Payable:</span>
            <span>₱{{ Number(selectedAssessment.net_payable || 0).toLocaleString('en-US', {minimumFractionDigits: 2}) }}</span>
          </div>
          <div class="flex justify-between text-emerald-600 font-bold">
            <span>Total Paid to Date:</span>
            <span>₱{{ Number(selectedAssessment.total_paid || 0).toLocaleString('en-US', {minimumFractionDigits: 2}) }}</span>
          </div>
          <div class="flex justify-between text-rose-600 font-bold border-t border-slate-200 pt-1">
            <span>Remaining Balance:</span>
            <span>₱{{ Number(selectedAssessment.remaining_balance || 0).toLocaleString('en-US', {minimumFractionDigits: 2}) }}</span>
          </div>
        </div>

        <!-- Record Payment Form (if balance remains) -->
        <form v-if="selectedAssessment.remaining_balance > 0" @submit.prevent="submitPayment" class="p-5 rounded-2xl bg-emerald-50/70 border border-emerald-200 text-xs space-y-4 mb-6">
          <h4 class="font-bold text-emerald-950 uppercase">Accept Cashier Payment</h4>
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <div class="flex items-center justify-between mb-1">
                <label class="block font-semibold text-slate-700">Amount to Pay (PHP) *</label>
                <button 
                  type="button" 
                  @click="paymentForm.amount_paid = Number(selectedAssessment.remaining_balance)" 
                  class="text-[10px] text-emerald-700 hover:text-emerald-900 font-bold underline cursor-pointer"
                >
                  Pay Full Balance
                </button>
              </div>
              <input 
                v-model.number="paymentForm.amount_paid" 
                type="number" 
                step="0.01" 
                min="1"
                :max="Number(selectedAssessment.remaining_balance)"
                @input="handleAmountInput"
                required 
                class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-mono font-bold focus:ring-2 focus:ring-emerald-500"
              />
              <div class="flex items-center justify-between text-[10px] text-slate-500 mt-1">
                <span>Min: ₱{{ (Number(selectedAssessment.total_paid) > 0 ? 1 : Math.min(3000, Number(selectedAssessment.remaining_balance))).toLocaleString('en-US', {minimumFractionDigits: 2}) }}</span>
                <span class="font-bold text-rose-600">Max Limit: ₱{{ Number(selectedAssessment.remaining_balance).toLocaleString('en-US', {minimumFractionDigits: 2}) }}</span>
              </div>
            </div>
            <div>
              <label class="block font-semibold text-slate-700 mb-1">Payment Method *</label>
              <select v-model="paymentForm.payment_method" class="w-full px-3 py-2 rounded-xl border border-slate-300 bg-white text-xs">
                <option value="Cash">Cash</option>
                <option value="Credit / Debit Card">Credit / Debit Card</option>
                <option value="Bank Transfer">Bank Transfer</option>
                <option value="GCash / Maya">GCash / Maya</option>
                <option value="Over-The-Counter">Over-The-Counter</option>
              </select>
            </div>
            <div v-if="paymentForm.payment_method !== 'Cash'" class="sm:col-span-2">
              <label class="block font-semibold text-slate-700 mb-1">
                {{ paymentForm.payment_method === 'Credit / Debit Card' ? 'Card Approval Code / POS Reference *' : 'Transaction Reference / Trace No. *' }}
              </label>
              <input 
                v-model="paymentForm.reference_no" 
                type="text" 
                :placeholder="paymentForm.payment_method === 'Credit / Debit Card' ? 'e.g. AUTH-948201 / VISA-4242' : 'e.g. REF-12345678'"
                required
                class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-mono"
              />
            </div>
          </div>

          <div class="flex justify-end space-x-3 pt-2">
            <button 
              type="submit" 
              :disabled="isPaying"
              class="px-6 py-2.5 rounded-xl font-bold bg-emerald-600 hover:bg-emerald-500 text-white shadow-md transition flex items-center space-x-1.5"
            >
              <span v-if="isPaying" class="w-3.5 h-3.5 border-2 border-white border-t-transparent rounded-full animate-spin"></span>
              <span>Submit Payment & Issue Official Receipt</span>
            </button>
          </div>
        </form>

        <!-- Previous Payment Receipts History -->
        <div>
          <h4 class="text-xs font-bold uppercase tracking-wider text-slate-700 mb-3">Issued Official Receipts (OR)</h4>
          <div class="space-y-2">
            <div 
              v-for="p in selectedAssessment.payments" 
              :key="p.id"
              class="p-3 rounded-xl border border-slate-200 bg-slate-50 flex items-center justify-between text-xs font-mono"
            >
              <div>
                <strong class="text-slate-900">{{ p.or_number }}</strong>
                <div class="text-[11px] text-slate-500">{{ p.payment_date }} • {{ p.payment_method }}</div>
              </div>
              <div class="flex items-center space-x-3 text-right">
                <div>
                  <strong class="text-emerald-700 text-sm block">₱{{ Number(p.amount_paid).toLocaleString('en-US', {minimumFractionDigits: 2}) }}</strong>
                  <div class="text-[10px] text-slate-400">Cashier: {{ p.received_by_user }}</div>
                </div>
                <button 
                  type="button" 
                  @click="openReceipt(p, selectedAssessment)" 
                  class="px-2.5 py-1.5 rounded-lg text-xs font-bold bg-slate-900 hover:bg-slate-800 text-white shadow-sm transition flex items-center space-x-1"
                  title="Print Official Receipt"
                >
                  <Printer class="w-3.5 h-3.5" />
                  <span>Print OR</span>
                </button>
              </div>
            </div>
            <div v-if="!selectedAssessment.payments || selectedAssessment.payments.length === 0" class="text-xs text-slate-400 text-center py-4">
              No payments recorded yet.
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- OFFICIAL RECEIPT PRINTABLE VIEW -->
    <div v-if="selectedReceipt" class="fixed inset-0 z-50 bg-slate-900/80 backdrop-blur-sm overflow-y-auto p-4 sm:p-6 flex flex-col items-center">
      <!-- Print Action Bar (Hidden in Print) -->
      <div class="no-print w-full max-w-3xl flex items-center justify-between mb-4 bg-white p-4 rounded-2xl border border-slate-200 shadow-lg">
        <button 
          @click="selectedReceipt = null" 
          class="px-4 py-2 rounded-xl text-slate-700 hover:bg-slate-100 font-semibold text-xs transition flex items-center space-x-2"
        >
          <ArrowLeft class="w-4 h-4" />
          <span>Back to Dashboard</span>
        </button>

        <div class="flex items-center space-x-3">
          <button 
            @click="printReceipt" 
            class="px-6 py-2.5 rounded-xl font-bold bg-emerald-600 hover:bg-emerald-500 text-white text-xs shadow-md transition flex items-center space-x-2"
          >
            <Printer class="w-4 h-4" />
            <span>Print Official Receipt</span>
          </button>
        </div>
      </div>

      <!-- Printable Document Sheet -->
      <div class="bg-white text-slate-900 rounded-3xl p-8 sm:p-10 border border-slate-200 shadow-2xl max-w-3xl w-full text-xs print:p-0 print:border-none print:shadow-none print:rounded-none">
        <!-- School Header -->
        <div class="text-center border-b-2 border-slate-900 pb-4 mb-6">
          <div class="flex items-center justify-center space-x-3 mb-2">
            <div class="w-10 h-10 rounded-xl bg-emerald-700 flex items-center justify-center text-white font-bold text-lg">
              SIA
            </div>
            <div>
              <h1 class="text-xl font-extrabold tracking-wide uppercase text-slate-900">SIA HIGH SCHOOL</h1>
              <p class="text-[11px] text-slate-600 font-medium">Junior High School & Senior High School Department</p>
            </div>
          </div>
          <p class="text-[10px] text-slate-500 font-mono">
            DepEd School ID: 405621 • Recognized K-12 Educational Institution • Finance & Treasury Division
          </p>
          <div class="inline-block mt-3 px-4 py-1 rounded-full bg-slate-900 text-white font-extrabold text-xs tracking-widest uppercase">
            OFFICIAL RECEIPT (DUPLICATE COPY)
          </div>
        </div>

        <!-- Receipt Metadata Grid -->
        <div class="grid grid-cols-2 gap-4 bg-slate-50 p-4 rounded-2xl border border-slate-200 mb-6 font-mono text-[11px]">
          <div class="space-y-1.5">
            <div><span class="text-slate-500">OR Number:</span> <strong class="text-emerald-800 text-sm">{{ selectedReceipt.or_number }}</strong></div>
            <div><span class="text-slate-500">Assessment No:</span> <strong>{{ selectedReceipt.assessment_no }}</strong></div>
            <div><span class="text-slate-500">Student Name:</span> <strong class="text-slate-900 uppercase">{{ selectedReceipt.student_name }}</strong></div>
            <div><span class="text-slate-500">Student ID / LRN:</span> <strong>{{ selectedReceipt.permanent_student_no || 'Pending' }} / {{ selectedReceipt.lrn || 'N/A' }}</strong></div>
          </div>
          <div class="space-y-1.5 text-right sm:text-left">
            <div><span class="text-slate-500">Date Issued:</span> <strong>{{ selectedReceipt.payment_date }}</strong></div>
            <div><span class="text-slate-500">Grade & Section:</span> <strong>{{ selectedReceipt.grade_level_name }} - {{ selectedReceipt.section_name || 'Assigned' }}</strong></div>
            <div><span class="text-slate-500">Track & Strand:</span> <strong>{{ selectedReceipt.strand_name || 'JHS General' }}</strong></div>
            <div><span class="text-slate-500">Payment Method:</span> <strong class="uppercase text-emerald-800">{{ selectedReceipt.payment_method }}</strong></div>
          </div>
        </div>

        <!-- Particulars & Assessment Breakdown Table -->
        <div class="border border-slate-300 rounded-xl overflow-hidden mb-6">
          <table class="w-full text-xs text-left border-collapse">
            <thead>
              <tr class="bg-slate-100 text-slate-700 font-bold uppercase border-b border-slate-300">
                <th class="p-3">Payment Particulars / Assessment Items</th>
                <th class="p-3 text-right">Amount (PHP)</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-200 font-mono">
              <tr>
                <td class="p-2.5">Tuition Fee Base</td>
                <td class="p-2.5 text-right">₱{{ Number(selectedReceipt.total_tuition || 0).toLocaleString('en-US', {minimumFractionDigits: 2}) }}</td>
              </tr>
              <tr>
                <td class="p-2.5">Miscellaneous & Laboratory Fees</td>
                <td class="p-2.5 text-right">₱{{ ((Number(selectedReceipt.total_miscellaneous) || 0) + (Number(selectedReceipt.total_laboratory) || 0)).toLocaleString('en-US', {minimumFractionDigits: 2}) }}</td>
              </tr>
              <tr v-if="selectedReceipt.voucher_discount > 0" class="text-emerald-700">
                <td class="p-2.5">Less: DepEd Voucher Subsidy ({{ selectedReceipt.voucher_status }})</td>
                <td class="p-2.5 text-right">- ₱{{ Number(selectedReceipt.voucher_discount).toLocaleString('en-US', {minimumFractionDigits: 2}) }}</td>
              </tr>
              <tr class="bg-slate-50 font-bold">
                <td class="p-2.5">Total Net Payable Assessment</td>
                <td class="p-2.5 text-right">₱{{ Number(selectedReceipt.net_payable || 0).toLocaleString('en-US', {minimumFractionDigits: 2}) }}</td>
              </tr>
              <tr class="bg-emerald-50 text-emerald-950 font-bold border-t-2 border-b-2 border-emerald-600">
                <td class="p-3 uppercase text-xs tracking-wider">AMOUNT PAID THIS RECEIPT</td>
                <td class="p-3 text-right text-sm">₱{{ Number(selectedReceipt.amount_paid).toLocaleString('en-US', {minimumFractionDigits: 2}) }}</td>
              </tr>
              <tr>
                <td class="p-2.5 text-slate-600">Remaining Balance After This Payment</td>
                <td class="p-2.5 text-right font-bold text-slate-900">₱{{ Number(selectedReceipt.remaining_balance || 0).toLocaleString('en-US', {minimumFractionDigits: 2}) }}</td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Signatures & Authority Section -->
        <div class="grid grid-cols-2 gap-8 pt-6 border-t border-slate-200 mt-8">
          <div class="text-center">
            <div class="border-b border-slate-900 w-48 mx-auto mb-1"></div>
            <p class="font-bold text-slate-800 uppercase">{{ selectedReceipt.received_by_user || 'Cashier' }}</p>
            <p class="text-[10px] text-slate-500">Authorized Collecting Officer / Cashier</p>
          </div>
          <div class="text-center">
            <div class="border-b border-slate-900 w-48 mx-auto mb-1"></div>
            <p class="font-bold text-slate-800 uppercase">{{ selectedReceipt.student_name }}</p>
            <p class="text-[10px] text-slate-500">Student / Authorized Payor Signature</p>
          </div>
        </div>

        <!-- Official Notice -->
        <div class="mt-8 text-center text-[10px] text-slate-400 border-t border-slate-100 pt-4">
          <p>This Official Receipt is system-generated by SIA High School Information & Accounting System.</p>
          <p>Please present this receipt during enrollment consultations, ID processing, and credential releasing.</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { Search, RefreshCw, Printer, ArrowLeft } from 'lucide-vue-next';
import api from '../../services/api';

const activeTab = ref('assessments');
const assessments = ref([]);
const feeStructuresList = ref([]);
const selectedAssessment = ref(null);
const selectedReceipt = ref(null);
const searchQuery = ref('');
const filterStatus = ref('');
const isPaying = ref(false);
const successMessage = ref('');

const paymentForm = ref({
  amount_paid: 3000,
  payment_method: 'Cash',
  reference_no: '',
  remarks: 'Enrollment Initial Payment'
});

const getStatusBadgeClass = (status) => {
  if (status === 'Fully Paid') return 'bg-emerald-100 text-emerald-800';
  if (status === 'Partially Paid') return 'bg-blue-100 text-blue-800';
  return 'bg-amber-100 text-amber-800';
};

const loadAssessments = async () => {
  try {
    let params = '';
    if (filterStatus.value) params += `status=${filterStatus.value}&`;
    if (searchQuery.value) params += `search=${encodeURIComponent(searchQuery.value)}`;
    const res = await api.getAssessments(params);
    assessments.value = res.data;
  } catch (err) {
    console.error('Failed to load assessments:', err);
  }
};

const loadFeeStructures = async () => {
  try {
    const res = await api.getFeeStructures();
    feeStructuresList.value = res.data.fees;
  } catch (err) {
    console.error('Failed to load fee structures:', err);
  }
};

const openPaymentModal = async (id) => {
  try {
    const res = await api.getAssessmentDetails(id);
    selectedAssessment.value = res.data;
    const rem = Number(res.data.remaining_balance || 0);
    paymentForm.value.amount_paid = rem > 0 ? Math.min(3000, rem) : 0;
  } catch (err) {
    console.error('Failed to load assessment details:', err);
  }
};

const handleAmountInput = () => {
  if (!selectedAssessment.value) return;
  const max = Number(selectedAssessment.value.remaining_balance || 0);
  if (paymentForm.value.amount_paid > max) {
    paymentForm.value.amount_paid = max;
  }
};

const openReceipt = (payment, assessment) => {
  selectedReceipt.value = {
    or_number: payment.or_number,
    payment_date: payment.payment_date,
    payment_method: payment.payment_method,
    reference_no: payment.reference_no,
    amount_paid: payment.amount_paid,
    received_by_user: payment.received_by_user,
    assessment_no: assessment.assessment_no,
    enrollment_no: assessment.enrollment_no,
    student_name: `${assessment.first_name} ${assessment.middle_name || ''} ${assessment.last_name}`.trim(),
    permanent_student_no: assessment.permanent_student_no,
    lrn: assessment.lrn,
    grade_level_name: assessment.grade_level_name,
    section_name: assessment.section_name,
    strand_name: assessment.strand_name,
    voucher_status: assessment.voucher_status,
    total_tuition: assessment.total_tuition,
    total_miscellaneous: assessment.total_miscellaneous,
    total_laboratory: assessment.total_laboratory,
    voucher_discount: assessment.voucher_discount,
    net_payable: assessment.net_payable,
    remaining_balance: assessment.remaining_balance
  };
};

const printReceipt = () => {
  window.print();
};

const submitPayment = async () => {
  if (!selectedAssessment.value) return;

  const max = Number(selectedAssessment.value.remaining_balance || 0);
  if (Number(paymentForm.value.amount_paid) > max) {
    alert(`Payment amount cannot exceed the remaining balance of ₱${max.toLocaleString('en-US', {minimumFractionDigits: 2})}.`);
    paymentForm.value.amount_paid = max;
    return;
  }
  if (Number(paymentForm.value.amount_paid) <= 0) {
    alert('Please enter a valid payment amount greater than 0.');
    return;
  }

  isPaying.value = true;
  try {
    const res = await api.processPayment({
      assessment_id: selectedAssessment.value.id,
      amount_paid: paymentForm.value.amount_paid,
      payment_method: paymentForm.value.payment_method,
      reference_no: paymentForm.value.reference_no,
      remarks: paymentForm.value.remarks
    });

    successMessage.value = `Official Receipt ${res.data.or_number} generated! Student is now Officially Enrolled.`;
    await openPaymentModal(selectedAssessment.value.id);
    await loadAssessments();

    // Auto-open printable receipt for the newly created payment
    if (selectedAssessment.value.payments && selectedAssessment.value.payments.length > 0) {
      const latestPayment = selectedAssessment.value.payments[0];
      openReceipt(latestPayment, selectedAssessment.value);
    }
  } catch (err) {
    alert(err.message || 'Payment processing failed.');
  } finally {
    isPaying.value = false;
  }
};

onMounted(() => {
  loadAssessments();
  loadFeeStructures();
});
</script>
