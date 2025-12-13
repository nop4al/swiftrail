<script setup>
import { ref, computed } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import qrisLogo from '../components/logo QRIS.png'

const router = useRouter()
const route = useRoute()

const total = Number(route.query.total || 0)
const orderNumber = route.query.orderNumber || ''
const paymentMethod = route.query.paymentMethod || 'swiftpay'
const selectedBank = route.query.selectedBank || 'MANDIRI'
const trainName = route.query.trainName || ''
const trainNumber = route.query.trainNumber || ''
const trainClass = route.query.trainClass || 'Eksekutif'
const passengersString = String(route.query.passengers || '1')
const adultCount = parseInt(passengersString.charAt(0)) || 1
const childCount = parseInt(passengersString.charAt(1)) || 0
const infantCount = parseInt(passengersString.charAt(2)) || 0
const passengers = adultCount + childCount + infantCount
const seatsString = route.query.seats || ''
const passengerName = route.query.passengerName || ''
const passengerType = route.query.passengerType || 'Dewasa'
const fromStation = route.query.fromStation || ''
const toStation = route.query.toStation || ''
const date = route.query.date || ''
const departure = route.query.departure || ''
const arrival = route.query.arrival || ''

const seats = computed(() => {
  if (!seatsString) return []
  return seatsString.split(',').map(s => s.trim()).filter(s => s)
})

const ticketPrice = Math.round(total / passengers)
const platformFee = 0 
const finalTotal = total + platformFee

const paymentMethodDisplay = computed(() => {
  if (paymentMethod === 'swiftpay') return 'SwiftPay'
  if (paymentMethod === 'qris') return 'QRIS'
  if (paymentMethod === 'ib') return `Internet Banking - ${selectedBank}`
  return 'Metode Pembayaran'
})

const goBack = () => router.back()

const handlePayment = () => {
  // Navigate to payment success page with all booking data
  router.push({
    path: '/payment-success',
    query: {
      orderNumber,
      total: finalTotal,
      paymentMethod,
      trainName,
      trainNumber,
      trainClass,
      fromStation,
      toStation,
      date,
      departure,
      arrival,
      seats: seatsString,
      passengerName
    }
  })
}
</script>

<template>
  <div class="checkout-page">
    <!-- Header -->
    <header class="header">
      <button class="back-btn" @click="goBack" aria-label="Kembali">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path d="M10 17L5 12M5 12L10 7M5 12H20" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
      </button>
      <div class="header-content">
        <h1 class="header-title">Konfirmasi Pembayaran</h1>
      </div>
    </header>

    <!-- Content -->
    <main class="content-wrapper">
      <!-- Payment Method -->
      <div class="payment-method-card">
        <div class="method-header">Metode Pembayaran</div>
        <div class="method-display">
          <div class="method-icon">
            <img v-if="paymentMethod === 'qris'" :src="qrisLogo" alt="QRIS" class="qris-logo" />
            <svg v-else-if="paymentMethod === 'swiftpay'" width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
              <rect x="2" y="5" width="20" height="14" rx="2" fill="currentColor"/>
              <path d="M6 10h12M6 15h8" stroke="white" stroke-width="1.5" stroke-linecap="round"/>
            </svg>
            <svg v-else width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
              <rect x="2" y="5" width="20" height="14" rx="2" fill="currentColor"/>
            </svg>
          </div>
          <div class="method-info">
            <div class="method-name">{{ paymentMethodDisplay }}</div>
            <div class="method-sub">Pembayaran aman</div>
          </div>
          <button class="change-btn" @click="goBack">Ubah</button>
        </div>
      </div>

      <!-- Passenger Info -->
      <div class="passenger-info-card" v-if="passengerName">
        <div class="info-header">Penumpang</div>
        <div class="passenger-details">
          <div class="passenger-item">
            <span class="label">Nama:</span>
            <span class="value">{{ passengerName }}</span>
          </div>
          <div class="passenger-item">
            <span class="label">Kategori:</span>
            <span class="value">{{ passengerType }}</span>
          </div>
          <div class="passenger-breakdown">
            <div v-if="adultCount > 0" class="breakdown-item">{{ adultCount }} Dewasa</div>
            <div v-if="childCount > 0" class="breakdown-item">{{ childCount }} Anak</div>
            <div v-if="infantCount > 0" class="breakdown-item">{{ infantCount }} Bayi</div>
          </div>
        </div>
      </div>

      <!-- Detail Harga -->
      <div class="detail-harga-card">
        <div class="detail-header">Detail Harga</div>
        
        <!-- Train and Seats Info -->
        <div class="detail-item">
          <div class="detail-label">
            <div class="train-info-block">
              <div class="train-header">
                <span class="train-name">{{ trainName }}</span>
                <span class="train-number" v-if="trainNumber">#{{ trainNumber }}</span>
              </div>
              <div class="train-route-info">
                <span class="route">{{ fromStation }} → {{ toStation }}</span>
              </div>
              <div class="train-seats">
                <span class="seats-label">Kursi:</span>
                <span class="seats-list">{{ seats.length ? seats.join(', ') : '-' }}</span>
              </div>
            </div>
            <span class="train-class">{{ trainClass }}</span>
          </div>
          <div class="detail-value">
            <span class="qty">{{ passengers }} tiket</span>
            <span class="price">Rp {{ ticketPrice.toLocaleString('id-ID') }}</span>
          </div>
        </div>

        <div class="divider"></div>

        <!-- Platform Fee -->
        <div class="detail-item">
          <div class="detail-label">
            <span class="fee-label">Platform Fee</span>
          </div>
          <div class="detail-value">Rp {{ platformFee.toLocaleString('id-ID') }}</div>
        </div>

        <div class="divider"></div>

        <!-- Total Payment -->
        <div class="total-payment">
          <span class="total-label">Total Pembayaran</span>
          <span class="total-amount">Rp {{ finalTotal.toLocaleString('id-ID') }}</span>
        </div>
      </div>
    </main>

    <!-- Payment Button -->
    <div class="sticky-actions">
      <button class="pay-button" @click="handlePayment">LANJUTKAN PEMBAYARAN</button>
    </div>
  </div>
</template>

<style scoped>
/* Header */
.header {
  display: flex;
  align-items: center;
  gap: 16px;
  padding: 16px;
  background: linear-gradient(135deg, var(--color-primary) 0%, var(--color-secondary) 100%);
  color: white;
  box-shadow: 0 2px 8px rgba(22, 117, 231, 0.15);
  position: sticky;
  top: 0;
  z-index: 100;
}

.back-btn {
  background: rgba(255, 255, 255, 0.2);
  border: none;
  width: 40px;
  height: 40px;
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  cursor: pointer;
  transition: all 0.3s ease;
}

.back-btn:hover {
  background: rgba(255, 255, 255, 0.3);
}

.header-title {
  font-size: 20px;
  font-weight: 600;
  margin: 0;
}

/* Layout */
.checkout-page {
  display: flex;
  flex-direction: column;
  min-height: 100vh;
  background: #f8f9fa;
}

.content-wrapper {
  flex: 1;
  padding: 16px;
  overflow-y: auto;
  padding-bottom: 100px; 
}

/* Payment Method Card */
.payment-method-card {
  background: white;
  padding: 16px;
  border-radius: 12px;
  border: 1px solid rgba(14, 23, 36, 0.06);
  margin-bottom: 12px;
}

.method-header {
  font-size: 14px;
  font-weight: 600;
  color: var(--color-text-light);
  margin-bottom: 12px;
}

.method-display {
  display: flex;
  align-items: center;
  gap: 12px;
}

.method-icon {
  width: 48px;
  height: 48px;
  display: flex;
  align-items: center;
  justify-content: center;
  background: linear-gradient(135deg, var(--color-primary), var(--color-secondary));
  color: white;
  border-radius: 10px;
  flex-shrink: 0;
}

.method-info {
  flex: 1;
}

.method-name {
  font-weight: 700;
  font-size: 15px;
  color: var(--color-text);
}

.method-sub {
  font-size: 12px;
  color: var(--color-text-light);
  margin-top: 4px;
}

.change-btn {
  background: transparent;
  border: 1px solid var(--color-primary);
  color: var(--color-primary);
  padding: 8px 16px;
  border-radius: 8px;
  font-size: 12px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
  white-space: nowrap;
}

.change-btn:hover {
  background: rgba(22, 117, 231, 0.1);
}

.qris-logo {
  width: 24px;
  height: 24px;
  object-fit: contain;
}

/* Passenger Info Card */
.passenger-info-card {
  background: white;
  padding: 16px;
  border-radius: 12px;
  border: 1px solid rgba(14, 23, 36, 0.06);
  margin-bottom: 12px;
}

.info-header {
  font-size: 14px;
  font-weight: 600;
  color: var(--color-text-light);
  margin-bottom: 12px;
}

.passenger-details {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.passenger-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.passenger-item .label {
  color: var(--color-text-light);
  font-weight: 500;
  font-size: 13px;
}

.passenger-item .value {
  color: var(--color-text);
  font-weight: 600;
  font-size: 14px;
}

.passenger-breakdown {
  display: flex;
  gap: 8px;
  flex-wrap: wrap;
  margin-top: 8px;
  padding-top: 8px;
  border-top: 1px solid rgba(14, 23, 36, 0.06);
}

.breakdown-item {
  background: rgba(22, 117, 231, 0.1);
  color: var(--color-primary);
  padding: 4px 8px;
  border-radius: 6px;
  font-size: 12px;
  font-weight: 600;
}

/* Detail Harga Card */
.detail-harga-card {
  background: white;
  padding: 16px;
  border-radius: 12px;
  border: 1px solid rgba(14, 23, 36, 0.06);
  margin-bottom: 12px;
}

.detail-header {
  font-size: 14px;
  font-weight: 600;
  color: var(--color-text-light);
  margin-bottom: 16px;
}

.detail-item {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  gap: 12px;
  margin-bottom: 12px;
}

.detail-label {
  display: flex;
  align-items: center;
  gap: 12px;
  flex: 1;
}

.train-info-block {
  display: flex;
  flex-direction: column;
  gap: 6px;
  flex: 1;
}

.train-header {
  display: flex;
  align-items: center;
  gap: 8px;
}

.train-name {
  font-weight: 700;
  color: var(--color-text);
  font-size: 15px;
}

.train-number {
  font-size: 12px;
  color: var(--color-text-light);
  background: rgba(22, 117, 231, 0.1);
  padding: 2px 6px;
  border-radius: 4px;
}

.train-route-info {
  display: flex;
  align-items: center;
  gap: 6px;
  font-size: 13px;
}

.route {
  color: var(--color-text);
  font-weight: 500;
}

.train-seats {
  display: flex;
  align-items: center;
  gap: 6px;
  font-size: 13px;
}

.seats-label {
  color: var(--color-text-light);
  font-weight: 500;
}

.seats-list {
  color: var(--color-text);
  font-weight: 600;
}

.train-route {
  font-weight: 600;
  color: var(--color-text);
}

.train-class {
  background: linear-gradient(135deg, var(--color-primary), var(--color-secondary));
  color: white;
  padding: 4px 8px;
  border-radius: 6px;
  font-size: 11px;
  font-weight: 700;
  white-space: nowrap;
}

.fee-label {
  color: var(--color-text);
  font-weight: 500;
}

.info-icon {
  color: var(--color-text-light);
  cursor: help;
}

.detail-value {
  display: flex;
  flex-direction: column;
  align-items: flex-end;
  gap: 4px;
}

.qty {
  font-size: 13px;
  color: var(--color-text-light);
}

.price {
  font-weight: 700;
  color: var(--color-text);
}

.divider {
  height: 1px;
  background: rgba(14, 23, 36, 0.06);
  margin: 12px 0;
}

/* Total Payment */
.total-payment {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 12px;
  background: linear-gradient(135deg, rgba(22, 117, 231, 0.08), rgba(52, 156, 255, 0.08));
  border-radius: 10px;
  margin-top: 12px;
}

.total-label {
  font-weight: 600;
  color: var(--color-text);
}

.total-amount {
  font-size: 18px;
  font-weight: 800;
  color: var(--color-primary);
}

/* Procedures Card */
.procedures-card {
  background: white;
  padding: 16px;
  border-radius: 12px;
  border: 1px solid rgba(14, 23, 36, 0.06);
  margin-bottom: 12px;
}

.procedures-toggle {
  display: flex;
  align-items: center;
  gap: 12px;
  background: transparent;
  border: none;
  width: 100%;
  cursor: pointer;
  color: var(--color-text);
  font-weight: 600;
  font-size: 14px;
  transition: all 0.3s ease;
}

.procedures-toggle:hover {
  color: var(--color-primary);
}
.sticky-actions {
  position: fixed;
  bottom: 0;
  left: 0;
  right: 0;
  padding: 12px 16px 16px;
  background: white;
  border-top: 1px solid rgba(14, 23, 36, 0.06);
  display: flex;
  gap: 12px;
}

.pay-button {
  flex: 1;
  padding: 16px;
  background: linear-gradient(135deg, var(--color-primary), var(--color-secondary));
  color: white;
  border: none;
  border-radius: 10px;
  font-weight: 700;
  font-size: 14px;
  cursor: pointer;
  box-shadow: 0 4px 12px rgba(22, 117, 231, 0.3);
  transition: all 0.3s ease;
}

.pay-button:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 16px rgba(22, 117, 231, 0.4);
}

.pay-button:active {
  transform: translateY(0);
}

/* Responsive */
@media (max-width: 640px) {
  .content-wrapper {
    padding: 12px;
  }

  .header-title {
    font-size: 18px;
  }

  .detail-item {
    flex-direction: column;
    align-items: flex-start;
  }

  .detail-value {
    align-items: flex-start;
    width: 100%;
  }

  .total-amount {
    font-size: 16px;
  }
}
</style>
