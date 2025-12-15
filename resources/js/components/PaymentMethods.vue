<script setup>
import { ref } from 'vue'
import { useRouter, useRoute } from 'vue-router'

const router = useRouter()
const route = useRoute()

const total = Number(route.query.total || 0)
const orderNumber = route.query.orderNumber || ''

const selected = ref('swiftpay')
const swiftBalance = ref(500000) // mock balance
const banks = []
const selectedBank = ref(banks[0])

const swiftSufficient = () => swiftBalance.value >= total

const goBack = () => router.back()

const doPay = () => {
  if (selected.value === 'swiftpay' && !swiftSufficient()) {
    alert('Saldo SwiftPay tidak mencukupi. Silakan pilih metode lain.')
    return
  }

  // Navigate to checkout with payment method and other details
  router.push({
    path: '/checkout',
    query: {
      total: route.query.total,
      orderNumber,
      paymentMethod: selected.value,
      selectedBank: selected.value === 'ib' ? selectedBank.value : '',
      trainName: route.query.trainName,
      trainNumber: route.query.trainNumber,
      trainClass: route.query.trainClass,
      passengers: route.query.passengers,
      fromStation: route.query.fromStation,
      toStation: route.query.toStation,
      date: route.query.date,
      departure: route.query.departure,
      arrival: route.query.arrival,
      seats: route.query.seats,
      passengerName: route.query.passengerName,
      passengerType: route.query.passengerType
    }
  })
}
</script>

<template>
  <div class="payment-page">
    <header class="header">
      <button class="back-btn" @click="goBack" aria-label="Kembali">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path d="M10 17L5 12M5 12L10 7M5 12H20" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
      </button>
      <div class="header-content">
        <h1 class="header-title">Pilih Metode Pembayaran</h1>
      </div>
    </header>

    <main class="content-wrapper">
      <div class="card">
        <!-- SwiftPay (distinct style) -->
        <div class="method swiftpay" :class="{ active: selected === 'swiftpay' }" @click="selected = 'swiftpay'">
          <div class="method-left">
            <div class="method-title">SwiftPay</div>
            <div class="method-sub">Saldo SwiftPay: Rp {{ swiftBalance.toLocaleString('id-ID') }}</div>
          </div>
          <div class="method-right">
            <div class="balance-badge">Rp {{ swiftBalance.toLocaleString('id-ID') }}</div>
            <div class="choose-label">{{ selected === 'swiftpay' ? 'Terpilih' : 'Pilih' }}</div>
          </div>
        </div>

        <!-- QRIS -->
        <div class="method" :class="{ active: selected === 'qris' }" @click="selected = 'qris'">
          <div class="method-left">
            <div class="method-title">QRIS</div>
            <div class="method-sub">Bayar lewat pemindai QR</div>
          </div>
          <div class="method-right">{{ selected === 'qris' ? 'Terpilih' : 'Pilih' }}</div>
        </div>

        <!-- Internet Banking with dropdown -->
        <div class="method" :class="{ active: selected === 'ib' }" @click="selected = 'ib'">
          <div class="method-left">
            <div class="method-title">Internet Banking</div>
            <div class="method-sub">Transfer via bank</div>
          </div>
          <div class="method-right">{{ selected === 'ib' ? 'Terpilih' : 'Pilih' }}</div>
        </div>

        <div v-if="selected === 'ib'" class="bank-select">
          <label for="bank" class="bank-label">Pilih Bank</label>
          <select id="bank" v-model="selectedBank" class="bank-dropdown">
            <option v-for="b in banks" :key="b" :value="b">{{ b }}</option>
          </select>
        </div>
      </div>

      <div class="summary-card">
        <div class="summary-row label-left">
          <span class="label">Total Pembayaran</span>
          <strong class="amount">Rp {{ total.toLocaleString('id-ID') }}</strong>
        </div>

        <div class="summary-actions">
          <button class="pay-action" :disabled="selected === 'swiftpay' && !swiftSufficient()" @click="doPay">PAY</button>
          <div v-if="selected === 'swiftpay' && !swiftSufficient()" class="warning">Saldo SwiftPay tidak mencukupi untuk pembayaran.</div>
        </div>
      </div>
    </main>
  </div>
</template>

<style scoped>
.header {
  display:flex; align-items:center; gap:16px; padding:16px; background: linear-gradient(135deg, var(--color-primary) 0%, var(--color-secondary) 100%); color:white; box-shadow:0 2px 8px rgba(22,117,231,0.15);
}
.back-btn { background: rgba(255,255,255,0.2); border:none; width:40px; height:40px; border-radius:8px; display:flex; align-items:center; justify-content:center; color:white }
.header-title { font-size:20px; font-weight:600 }
.content-wrapper { padding:18px }
.card { background:var(--color-white); padding:12px; border-radius:10px; border:1px solid rgba(14,23,36,0.06); margin-bottom:12px }
.method { display:flex; justify-content:space-between; align-items:center; padding:12px; border-radius:8px; cursor:pointer; border:1px solid rgba(14,23,36,0.04); margin-bottom:8px }
.method.active { box-shadow:0 4px 20px rgba(22,117,231,0.12); border-color: rgba(22,117,231,0.12) }
.method-title { font-weight:700 }
.method-sub { font-size:13px; color:var(--color-text-light) }
.summary-card { position:sticky; bottom:12px; background:var(--color-white); padding:12px; border-radius:10px; border:1px solid rgba(14,23,36,0.06); display:flex; flex-direction:column; gap:12px }
.pay-action { padding:12px; background:linear-gradient(135deg,var(--color-primary),var(--color-primary-dark)); color:var(--color-white); border:none; border-radius:8px; font-weight:700 }
.pay-action:disabled { opacity:0.5; cursor:not-allowed }

/* SwiftPay distinct styling */
.method.swiftpay { background: linear-gradient(135deg,#5dd6ff,#3a8dff); color: #fff; border: none }
.method.swiftpay .method-sub { color: rgba(255,255,255,0.9) }
.balance-badge { background: rgba(255,255,255,0.18); padding:6px 8px; border-radius:8px; font-weight:700; margin-bottom:6px }
.choose-label { font-size:12px; opacity:0.9 }
.bank-select { margin-top:8px; display:flex; flex-direction:column; gap:6px }
.bank-label { font-size:13px; color:var(--color-text); font-weight:600 }
.bank-dropdown { padding:8px; border-radius:8px; border:1px solid rgba(14,23,36,0.06) }
.warning { color:#b22222; font-size:13px }

/* Summary layout tweaks: put amount on left */
.summary-row.label-left { display:flex; justify-content:space-between; align-items:center; gap:12px; padding:6px 2px }
.summary-row.label-left .amount { font-size:16px; font-weight:800 }
.summary-row.label-left .label { color:var(--color-text-light); font-size:14px; font-weight:600 }
.summary-actions { display:flex; flex-direction:column; gap:8px; margin-top:8px }
</style>