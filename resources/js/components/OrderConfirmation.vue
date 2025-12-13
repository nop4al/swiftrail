<script setup>
import { ref, computed } from 'vue'
import { useRouter, useRoute } from 'vue-router'

const router = useRouter()
const route = useRoute()

// build order info from route.query so the page reflects previous selections
const q = route.query || {}
const seatsParam = q.seats || ''
const seats = seatsParam ? String(seatsParam).split(',').filter(s => s) : []
const passengersParam = q.passengers || '1'

const defaultPrice = (cls) => (String(cls || '').toLowerCase().includes('eksek') ? 320000 : 180000)

const order = ref({
  date: q.date || '03 December 2025',
  departureTime: q.departure || '14:15',
  arrivalTime: q.arrival || '17:30',
  from: q.fromStation || 'Surabaya Gubeng (SGU)',
  to: q.toStation || 'Solo Balapan (SLO)',
  trainName: q.trainName || 'Mutiara Selatan',
  classType: q.trainClass || 'Eksekutif 1',
  passengerName: seats.length ? `NURIS SAFIRA` : 'NURIS SAFIRA',
  passengerType: 'Dewasa',
  coach: q.trainClass || 'Eksekutif 1',
  seat: seats[0] || '',
  price: defaultPrice(q.trainClass),
  platformFee: 0
})

const totalPayment = computed(() => order.value.price + order.value.platformFee)
const handlePay = () => {
  // Navigate to payment selection, pass total and complete booking data
  router.push({
    path: '/payment-methods',
    query: {
      total: totalPayment.value,
      orderNumber: `SR${Date.now()}`,
      // Pass all booking details
      trainName: order.value.trainName,
      trainNumber: route.query.trainNumber || '',
      trainClass: order.value.classType,
      fromStation: order.value.from,
      toStation: order.value.to,
      date: order.value.date,
      departure: order.value.departureTime,
      arrival: order.value.arrivalTime,
      passengers: passengersParam,
      seats: seats.join(','),
      passengerName: order.value.passengerName,
      passengerType: order.value.passengerType
    }
  })
}

const handleAddToCart = () => {
  alert("Ticket added to Cart (dummy action).")
}

const goBack = () => {
  router.push({
    path: '/select-seat',
    query: {
      trainName: route.query.trainName || order.value.trainName,
      trainNumber: route.query.trainNumber || '',
      fromStation: route.query.fromStation || order.value.from,
      toStation: route.query.toStation || order.value.to,
      date: route.query.date || order.value.date,
      departure: route.query.departure || order.value.departureTime,
      arrival: route.query.arrival || order.value.arrivalTime,
      trainClass: route.query.trainClass || order.value.classType,
      passengers: route.query.passengers || '1'
    }
  })
}
</script>

<template>
  <div class="checkout-page">

    <header class="header">
      <button class="back-btn" @click="goBack" aria-label="Kembali ke Pilih Kursi">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path d="M10 17L5 12M5 12L10 7M5 12H20" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
      </button>
      <div class="header-content">
        <h1 class="header-title">Konfirmasi Kursi</h1>
      </div>
    </header>

    <div class="content-wrapper">

      <div class="card">
        <h3 class="card-title">Kereta Pergi</h3>

        <p class="date-row">
          {{ order.date }} • {{ order.departureTime }} - {{ order.arrivalTime }}
        </p>

        <p class="route">{{ order.from }} → {{ order.to }}</p>

        <p class="train-info">
          {{ order.trainName }} • {{ order.classType.toUpperCase() }}
        </p>

        <div class="passenger-box">
          <div class="left">
            <p class="passenger-name">{{ order.passengerName }}</p>
            <p class="seat-info">{{ order.coach }} • {{ order.seat }}</p>
          </div>
          <div class="tag">{{ order.passengerType }}</div>
        </div>
      </div>

      <div class="card">
        <h3 class="card-title">Rincian Harga</h3>

        <div class="train-row">
          <div>
            <p class="train-name">{{ order.trainName }}</p>
            <span class="pill">{{ order.classType }}</span>
          </div>
        </div>

        <div class="price-item">
          <span>Dewasa x1</span>
          <strong>Rp{{ order.price.toLocaleString() }}</strong>
        </div>

        <div class="price-item">
          <span>Platform Fee</span>
          <strong>Rp{{ order.platformFee }}</strong>
        </div>

        <div class="total-box">
          <span>Total Pembayaran</span>
          <strong>Rp{{ totalPayment.toLocaleString() }}</strong>
        </div>
      </div>

      <button class="pay-btn" @click="handlePay">PAY</button>

    </div>
  </div>
</template>

<style scoped>
.checkout-page {
  width: 100%;
  min-height: 100vh;
  background: var(--color-bg-light);
  color: var(--color-text-dark);
  font-family: inherit;
}

.top-header { display:none; }

.header {
  display: flex;
  align-items: center;
  gap: 16px;
  padding: 16px;
  background: linear-gradient(135deg, var(--color-primary) 0%, var(--color-secondary) 100%);
  color: white;
  box-shadow: 0 2px 8px rgba(22, 117, 231, 0.15);
}

.back-btn {
  background: rgba(255,255,255,0.2);
  border: none;
  width: 40px;
  height: 40px;
  border-radius: 8px;
  cursor: pointer;
  display:flex; align-items:center; justify-content:center; color:white;
}

.back-btn:hover { background: rgba(255,255,255,0.28) }

.header-content { flex:1 }
.header-title { font-size:20px; font-weight:600; margin:0; color:white }

.extras-list { display:flex; gap:10px; overflow-x:auto }

.extra-pill {
  padding: 6px 12px;
  background: rgba(255,255,255,0.12);
  border-radius: 18px;
  font-size: 13px;
  color: var(--color-white);
  white-space: nowrap;
}

.extra-pill.active { background: var(--color-white); color: var(--color-text-dark) }
.checkmark { margin-right:6px; font-weight:700 }

.content-wrapper { padding: 18px }

.card {
  background: var(--color-white);
  padding: 18px;
  border-radius: 12px;
  margin-bottom: 18px;
  border: 1px solid rgba(14, 23, 36, 0.06);
}

.card-title { font-size:16px; font-weight:700; margin-bottom:12px }
.date-row { color: var(--color-text-light); margin-bottom:8px; font-size:14px }
.route { font-size:16px; font-weight:700; margin-bottom:6px }
.train-info { color: var(--color-text-light); margin-bottom:12px }

.passenger-box {
  display:flex; justify-content:space-between; align-items:center;
  background: #fbfdff; padding:12px; border-radius:10px;
}
.passenger-name { font-weight:700 }
.tag { background: rgba(22,117,231,0.12); color: var(--color-primary); padding:4px 10px; border-radius:16px; font-size:13px }

.price-item { display:flex; justify-content:space-between; margin-bottom:10px }
.total-box { margin-top:14px; padding-top:12px; border-top:1px solid rgba(14,23,36,0.06); display:flex; justify-content:space-between; font-size:18px }

.pill { display:inline-block; background: rgba(13, 82, 217, 0.08); padding:4px 8px; border-radius:6px; font-size:12px; color: var(--color-primary) }

.pay-btn {
  width:100%; padding:14px; background: linear-gradient(135deg,var(--color-primary),var(--color-primary-dark)); color:var(--color-white); border:none; border-radius:10px; font-size:16px; font-weight:700; margin-bottom:10px;
}

/* small responsive tweak */
@media (min-width: 768px) {
  .content-wrapper { padding: 28px }
  .card { padding: 20px }
}
</style>
