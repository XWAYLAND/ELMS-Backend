{{-- Shared admin loan detail and verification modal. --}}
<template x-teleport="body">
  <div class="verify-overlay" x-show="verifyOpen" x-cloak @keydown.escape.window="if(verifyOpen) { verifyOpen = false; resetModal(); }">
    <div class="verify-modal" @click.outside="verifyOpen = false; resetModal()">
    <div x-show="!loan" class="verify-step">
      <div class="verify-header">
        <h2>Verify Loan Code</h2>
        <p>Scan QR or type unique code</p>
      </div>
      <form @submit.prevent="submitKode()" class="verify-form">
        <input
          type="text"
          x-ref="kodeInput"
          x-model="kodeInput"
          placeholder="Type or scan code..."
          class="verify-input"
          maxlength="20"
          autocomplete="off"
          @input="error = ''"
        >
        <div x-show="error" class="verify-error" x-text="error"></div>
        <div class="verify-actions">
          <button type="button" class="btn btn-secondary" @click="verifyOpen = false; resetModal()">Cancel</button>
          <button type="submit" class="btn btn-primary" :disabled="loading || !kodeInput">
            <span x-show="!loading">Verify</span>
            <span x-show="loading">Loading...</span>
          </button>
        </div>
      </form>
    </div>

    <div x-show="loan" class="verify-step" x-cloak>
      <div class="verify-header">
        <h2>Loan Details</h2>
        <p>Code: <strong x-text="loan?.kode_unik"></strong></p>
      </div>

      <div class="verify-details">
        <div class="detail-row">
          <span class="detail-label">Status</span>
          <span class="detail-value" x-text="loan?.status_label"></span>
        </div>
        <div class="detail-row">
          <span class="detail-label">Member</span>
          <span class="detail-value" x-text="loan?.anggota_nama + ' (' + loan?.anggota_nis + ')' "></span>
        </div>
        <div class="detail-row">
          <span class="detail-label">Book</span>
          <span class="detail-value" x-text="loan?.buku_judul"></span>
        </div>
        <div class="detail-row">
          <span class="detail-label">Duration</span>
          <span class="detail-value" x-text="loan?.durasi_hari + ' days'"></span>
        </div>
        <div class="detail-row">
          <span class="detail-label">Requested</span>
          <span class="detail-value" x-text="loan?.waktu_pengajuan"></span>
        </div>
        <div class="detail-row">
          <span class="detail-label">Due Date</span>
          <span class="detail-value" x-text="loan?.batas_waktu || '-' "></span>
        </div>
      </div>

      <div class="verify-actions" x-show="!pendingAction">
        <button type="button" class="btn btn-secondary" @click="verifyOpen = false; resetModal()">Close</button>
        <button x-show="loan?.can_approve" type="button" class="btn btn-success" :disabled="actionLoading" @click="actionLoan('approve')">Approve</button>
        <button x-show="loan?.can_approve" type="button" class="btn btn-danger" :disabled="actionLoading" @click="actionLoan('reject')">Reject</button>
        <button x-show="loan?.can_return" type="button" class="btn btn-primary" :disabled="actionLoading" @click="actionLoan('return')">Confirm Return</button>
      </div>

      <div class="verify-actions" x-show="pendingAction" x-cloak style="background: #F5F6FF; padding: 16px; border-radius: 8px; flex-direction: column; align-items: center; justify-content: center; border: 1px solid #D5D7FF; gap: 12px; margin-top: 16px;">
        <span style="color: var(--color-primary, #141DFE); font-weight: 600; font-size: 16px;" x-text="confirmMessage(pendingAction)"></span>
        <div style="display: flex; gap: 8px; width: 100%;">
          <button type="button" class="btn btn-secondary" @click="cancelAction()" style="flex: 1; border: 1px solid var(--color-primary, #141DFE); color: var(--color-primary, #141DFE); background: white;">Cancel</button>
          <button type="button" class="btn btn-primary" @click="executeAction()" style="flex: 1; background: var(--color-primary, #141DFE); color: white;">Yes, Confirm</button>
        </div>
      </div>
    </div>
    </div>
  </div>
</template>

<div class="toast toast-error" x-show="error && !verifyOpen" x-cloak x-text="error" x-transition></div>

<script>
function verifyKode() {
  return {
    verifyOpen: false,
    kodeInput: '',
    loan: null,
    error: '',
    loading: false,
    actionLoading: false,
    action: null,
    pendingAction: null,

    submitKode() {
      if (!this.kodeInput.trim()) return;
      this.loading = true;
      this.error = '';

      fetch('{{ route("admin.loans.verify") }}', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
          'Accept': 'application/json',
        },
        body: JSON.stringify({ kode_unik: this.kodeInput.trim() })
      })
      .then(res => res.json().then(data => ({ status: res.status, data })))
      .then(({ data }) => {
        if (data.success) {
          this.loan = data.loan;
        } else {
          this.error = data.message || 'Invalid code.';
        }
      })
      .catch(() => { this.error = 'Error occurred. Try again.'; })
      .finally(() => { this.loading = false; });
    },

    loadLoanByTransaksi(id) {
      this.loading = true;
      this.error = '';
      this.loan = null;

      fetch('{{ url("admin/loans") }}/by-transaksi/' + encodeURIComponent(id), {
        method: 'GET',
        headers: {
          'Accept': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        },
      })
      .then(res => res.json().then(data => ({ status: res.status, data })))
      .then(({ data }) => {
        if (data.success) {
          this.loan = data.loan;
        } else {
          this.error = data.message || 'Could not load details.';
        }
      })
      .catch(() => { this.error = 'Error occurred.'; })
      .finally(() => { this.loading = false; });
    },

    actionLoan(type) {
      if (!this.loan) return;
      this.pendingAction = type;
    },

    cancelAction() {
      this.pendingAction = null;
    },

    executeAction() {
      const type = this.pendingAction;
      if (!this.loan || !type) return;

      this.pendingAction = null;
      this.actionLoading = true;
      this.action = type;
      this.error = '';

      const url = type === 'approve'
        ? `{{ url('admin/loans') }}/${this.loan.id_transaksi}/approve-via-kode`
        : type === 'reject'
        ? `{{ url('admin/loans') }}/${this.loan.id_transaksi}/reject-via-kode`
        : `{{ url('admin/loans') }}/${this.loan.id_transaksi}/return-via-kode`;

      fetch(url, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
          'Accept': 'application/json',
        },
      })
      .then(res => res.json().then(data => ({ status: res.status, data })))
      .then(({ data }) => {
        if (data.success) {
          sessionStorage.setItem('toast_message', data.message || 'Action successful!');
          window.location.reload();
        } else {
          this.error = data.message || 'Processing failed.';
        }
      })
      .catch(() => { this.error = 'Error occurred.'; })
      .finally(() => { this.actionLoading = false; this.action = null; });
    },

    confirmMessage(type) {
      if (type === 'approve') return 'Approve this loan?';
      if (type === 'reject') return 'Reject this loan?';
      return 'Confirm book return?';
    },

    resetModal() {
      this.kodeInput = '';
      this.loan = null;
      this.error = '';
      this.loading = false;
      this.actionLoading = false;
      this.pendingAction = null;
    }
  };
}
</script>
