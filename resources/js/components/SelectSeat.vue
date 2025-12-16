<script setup>
import { ref, computed } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { getAvailableSeats, createBooking } from '@/utils/api'
import { getSelectedSchedule, saveSelectedSeats, saveOrderSummary } from '@/utils/storage'
import { formatPrice, formatDate, showError, showSuccess } from '@/utils/helpers'

const router = useRouter()
const route = useRoute()

// Loading and error states
const isLoading = ref(false)
const hasError = ref(false)
const errorMessage = ref('')

const trainId = ref(route.query.trainId)
const trainName = ref(route.query.trainName)
const trainNumber = ref(route.query.trainNumber)
const departure = ref(route.query.departure)
const arrival = ref(route.query.arrival)
const fromStation = ref(route.query.fromStation)
const toStation = ref(route.query.toStation)
const date = ref(route.query.date)
const passengersParam = ref(route.query.passengers || '1')

const trainClass = ref(route.query.trainClass || 'Eksekutif 1')

const mapIncomingClass = (incoming) => {
  if (!incoming) return 'Eksekutif 1'
  const lower = String(incoming).toLowerCase()
  if (lower.includes('exec') || lower.includes('eksek')) return 'Eksekutif 1'
  if (lower.includes('econom')) return 'Eksekutif 2'
  if (lower.includes('compart')) return 'Eksekutif 3'
  return 'Eksekutif 1'
}

const selectedClass = ref(mapIncomingClass(trainClass.value))
const selectedSeats = ref([])

const adultCount = computed(() => {
  const str = passengersParam.value
  return str.length > 0 ? parseInt(str.charAt(0)) || 1 : 1
})

const generateRows = (count, cols = ['A','B','C','D']) => {
  const rows = []
  for (let i = 1; i <= count; i++) {
    rows.push({ row: i, seats: [...cols] })
  }
  return rows
}

const seatClasses = ref({
  'Eksekutif 1': generateRows(10, ['A','B','C','D']),
  'Eksekutif 2': generateRows(10, ['A','B','C','D']),
  'Eksekutif 3': generateRows(10, ['A','B','C','D'])
})

const seatStatus = ref({})

const getSeatKey = (row, col) => `${row}${col}`

const getSeatStatus = (row, col) => {
  const key = getSeatKey(row, col)
  return seatStatus.value[selectedClass.value]?.[key] || 'available'
}

const toggleSeat = (row, col) => {
  const key = getSeatKey(row, col)
  const status = getSeatStatus(row, col)
  
  if (status === 'available') {
    if (selectedSeats.value.length < adultCount.value) {
      if (!seatStatus.value[selectedClass.value]) {
        seatStatus.value[selectedClass.value] = {}
      }
      seatStatus.value[selectedClass.value][key] = 'chosen'
      selectedSeats.value.push(key)
    }
  } else if (status === 'chosen') {
    delete seatStatus.value[selectedClass.value][key]
    selectedSeats.value = selectedSeats.value.filter(s => s !== key)
  }
}

// Fetch available seats from API
const fetchSeats = async () => {
  try {
    isLoading.value = true
    hasError.value = false
    errorMessage.value = ''
    
    // Get stored schedule data
    const schedule = getSelectedSchedule()
    
    if (!schedule || !schedule.id) {
      errorMessage.value = 'Data jadwal tidak ditemukan. Silakan pilih jadwal kereta terlebih dahulu.'
      hasError.value = true
      return
    }
    
    // Call API to get available seats for this schedule
    const response = await getAvailableSeats(schedule.id, selectedClass.value)
    
    // Process seat availability data from API
    const seatsData = response.data || response || {}
    
    if (seatsData && Object.keys(seatsData).length > 0) {
      seatStatus.value[selectedClass.value] = seatsData
      showSuccess('Kursi tersedia dimuat')
    } else {
      // No seats data - use default availability (all available)
      if (!seatStatus.value[selectedClass.value]) {
        seatStatus.value[selectedClass.value] = {}
      }
    }
  } catch (error) {
    hasError.value = true
    errorMessage.value = error.message || 'Gagal memuat ketersediaan kursi. Silakan coba lagi.'
    showError(error, 'Muatan Kursi')
    console.error('Failed to fetch seats:', error)
  } finally {
    isLoading.value = false
  }
}

const goBack = () => {
  router.back()
}

const saveSeat = async () => {
  // Validate seats are selected
  if (selectedSeats.value.length === 0) {
    showError(new Error('Tidak ada kursi yang dipilih'), 'Pilih Kursi')
    return
  }
  
  if (selectedSeats.value.length !== adultCount.value) {
    showError(new Error(`Pilih ${adultCount.value} kursi untuk penumpang`), 'Pilih Kursi')
    return
  }
  
  try {
    isLoading.value = true
    
    // Get stored schedule and passenger data
    const schedule = getSelectedSchedule()
    const passengerData = JSON.parse(sessionStorage.getItem('passengerData') || '{}')
    
    if (!schedule) {
      showError(new Error('Data jadwal tidak ditemukan'), 'Simpan Kursi')
      return
    }
    
    // Save selected seats to storage
    saveSelectedSeats({
      seats: selectedSeats.value,
      class: selectedClass.value,
      trainId: schedule.id,
      seatCount: selectedSeats.value.length
    })
    
    // Create booking via API
    const bookingPayload = {
      schedule_id: schedule.id,
      class_type: selectedClass.value,
      seats: selectedSeats.value,
      adult_count: adultCount.value,
      child_count: passengerData.children || 0,
      infant_count: passengerData.infants || 0
    }
    
    const bookingResponse = await createBooking(bookingPayload)
    const bookingId = bookingResponse.data?.id || bookingResponse.id
    
    // Save booking summary to storage
    saveOrderSummary({
      bookingId: bookingId,
      schedule: schedule,
      seats: selectedSeats.value,
      class: selectedClass.value,
      totalPassengers: adultCount.value + (passengerData.children || 0),
      createdAt: new Date().toISOString()
    })
    
    showSuccess('Kursi berhasil disimpan. Melanjutkan ke konfirmasi pesanan...')
    
    // Navigate to order confirmation
    setTimeout(() => {
      router.push({
        path: '/order-confirmation',
        query: {
          bookingId: bookingId,
          trainName: trainName.value || '',
          trainNumber: trainNumber.value || '',
          fromStation: fromStation.value || '',
          toStation: toStation.value || '',
          date: date.value || '',
          departure: departure.value || '',
          arrival: arrival.value || '',
          trainClass: selectedClass.value || '',
          passengers: passengersParam.value || '1',
          seats: selectedSeats.value.join(',')
        }
      })
    }, 500)
  } catch (error) {
    showError(error, 'Simpan Kursi')
    console.error('Failed to save seats:', error)
  } finally {
    isLoading.value = false
  }
}

const selectedSeatsDisplay = computed(() => {
  return selectedSeats.value.join(', ')
})

const currentSelectedSeat = computed(() => {
  if (selectedSeats.value.length === 0) return null
  return selectedSeats.value[0]
})

const seatInfoDisplay = computed(() => {
  if (!currentSelectedSeat.value) {
    return { name: '', seat: '', show: false }
  }
  return {
    name: 'NURIS SAFIRA',
    seat: `${selectedClass.value} / Seat ${currentSelectedSeat.value}`,
    show: true
  }
})

// Load available seats when component mounts
import { onMounted } from 'vue'
onMounted(async () => {
  await fetchSeats()
})
</script>

<template>
  <div class="select-seat-container">
    <!-- Header -->
    <header class="header">
      <button class="back-btn" @click="goBack">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path d="M10 17L5 12M5 12L10 7M5 12H20" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
      </button>
      <div class="header-content">
        <h1 class="header-title">Pilih Kursi</h1>
        <p class="header-subtitle">{{ fromStation }} – {{ toStation }}</p>
      </div>
    </header>

    <main class="main-content">
      <!-- Trip Info -->
      <div class="trip-info">
        <div class="trip-header">
          <span class="trip-time">{{ departure }} - {{ arrival }}</span>
          <span class="trip-passengers">{{ adultCount }} Adult</span>
        </div>
          <div class="trip-details">
            <p class="trip-name">{{ trainName }} • {{ trainClass }}</p>
            <p class="trip-date">{{ date }}</p>
          </div>
      </div>

      <!-- Loading State -->
      <div v-if="isLoading" class="loading-container">
        <div class="spinner"></div>
        <h3>Memuat Ketersediaan Kursi...</h3>
        <p>Tunggu sebentar, kami sedang memproses data kursi Anda</p>
      </div>

      <!-- Error State -->
      <div v-else-if="hasError" class="error-container">
        <svg width="64" height="64" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
          <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2"/>
          <path d="M12 7V13M12 17H12.01" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
        </svg>
        <h3>Terjadi Kesalahan</h3>
        <p>{{ errorMessage }}</p>
        <button class="retry-btn" @click="fetchSeats">Coba Lagi</button>
      </div>

      <!-- Seat Selection Content -->
      <div v-else class="seats-content">
        <!-- Seat Info -->
        <div v-if="seatInfoDisplay.show" class="seat-info">
          <div class="passenger-info">
            <span class="passenger-name">{{ seatInfoDisplay.name }}</span>
            <span class="passenger-seat">{{ seatInfoDisplay.seat }}</span>
          </div>
        </div>

        <!-- Seat Legend -->
        <div class="seat-legend">
          <div class="legend-item">
            <div class="legend-seat available"></div>
            <span>Available</span>
          </div>
          <div class="legend-item">
            <div class="legend-seat chosen"></div>
            <span>Chosen</span>
          </div>
          <div class="legend-item">
            <div class="legend-seat filled"></div>
            <span>L</span>
          </div>
          <div class="legend-item">
            <div class="legend-seat filled filled-female"></div>
            <span>P</span>
          </div>
        </div>

        <!-- Seat Grid -->
        <div class="seat-container">
          <!-- Class Tabs -->
          <div class="class-tabs">
            <button 
              v-for="(seats, className) in seatClasses"
              :key="className"
              :class="['class-tab', { active: selectedClass === className }]"
              @click="selectedClass = className"
            >
              {{ className }}
            </button>
          </div>

          <!-- Seats Grid -->
          <div class="seats-grid">
            <div v-for="row in seatClasses[selectedClass]" :key="row.row" class="seat-row">
              <div class="seat-row-content">
                <div class="row-number">{{ row.row }}</div>
                <div class="row-seats-container">
                  <div class="row-seats-left">
                    <button 
                      v-for="col in ['A', 'B']"
                      :key="col"
                      :class="['seat', getSeatStatus(row.row, col)]"
                      :disabled="getSeatStatus(row.row, col) !== 'available' && getSeatStatus(row.row, col) !== 'chosen'"
                      @click="toggleSeat(row.row, col)"
                    >
                      <span v-if="getSeatStatus(row.row, col) === 'filled_male'" class="seat-gender">L</span>
                      <span v-else-if="getSeatStatus(row.row, col) === 'filled_female'" class="seat-gender">P</span>
                      <span v-else class="seat-label">{{ col }}</span>
                    </button>
                  </div>
                  <div class="row-gap"></div>
                  <div class="row-seats-right">
                    <button 
                      v-for="col in ['C', 'D']"
                      :key="col"
                      :class="['seat', getSeatStatus(row.row, col)]"
                      :disabled="getSeatStatus(row.row, col) !== 'available' && getSeatStatus(row.row, col) !== 'chosen'"
                      @click="toggleSeat(row.row, col)"
                    >
                      <span v-if="getSeatStatus(row.row, col) === 'filled_male'" class="seat-gender">L</span>
                      <span v-else-if="getSeatStatus(row.row, col) === 'filled_female'" class="seat-gender">P</span>
                      <span v-else class="seat-label">{{ col }}</span>
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Save Button -->
      <button class="save-btn" @click="saveSeat">SAVE</button>
    </main>
  </div>
</template>

<style scoped>
* {
  --color-primary: #1675E7;
  --color-primary-dark: #275CDE;
  --color-secondary: #0C9DE5;
  --color-accent: #09D8E3;
  --color-text-dark: #1a1a2e;
  --color-text-light: #6b7280;
  --color-bg-light: #f8fafc;
  --color-success: #10b981;
  --color-warning: #f59e0b;
  --color-error: #ef4444;
}

.select-seat-container {
  display: flex;
  flex-direction: column;
  height: 100vh;
  background: linear-gradient(135deg, var(--color-bg-light) 0%, #ffffff 100%);
  font-family: 'Geist', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
}

/* Header */
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
  background: rgba(255, 255, 255, 0.2);
  border: none;
  width: 40px;
  height: 40px;
  border-radius: 8px;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  transition: all 0.3s ease;
}

.back-btn:hover {
  background: rgba(255, 255, 255, 0.3);
}

.header-content {
  flex: 1;
}

.header-title {
  font-size: 20px;
  font-weight: 600;
  margin: 0;
  color: white;
}

.header-subtitle {
  font-size: 13px;
  color: rgba(255, 255, 255, 0.9);
  margin: 4px 0 0 0;
}

/* Main Content */
.main-content {
  flex: 1;
  overflow-y: auto;
  padding: 20px 16px;
}

/* Trip Info */
.trip-info {
  background: white;
  border-radius: 12px;
  padding: 16px;
  margin-bottom: 16px;
  border-left: 4px solid var(--color-primary);
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
}

.trip-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 8px;
}

.trip-time {
  font-weight: 600;
  color: var(--color-text-dark);
  font-size: 14px;
}

.trip-passengers {
  font-size: 12px;
  color: var(--color-text-light);
  background: var(--color-bg-light);
  padding: 4px 8px;
  border-radius: 6px;
}

.trip-details {
  margin: 0;
}

.trip-name {
  font-size: 13px;
  font-weight: 500;
  color: var(--color-text-dark);
  margin: 0;
}

.trip-date {
  font-size: 12px;
  color: var(--color-text-light);
  margin: 4px 0 0 0;
}

/* Loading State */
.loading-container {
  text-align: center;
  padding: 60px 20px;
  background: white;
  border-radius: 12px;
  color: var(--color-text-light);
  margin-bottom: 16px;
}

.spinner {
  width: 48px;
  height: 48px;
  margin: 0 auto 24px;
  border: 4px solid #f0f0f0;
  border-top: 4px solid var(--color-primary);
  border-radius: 50%;
  animation: spin 1s linear infinite;
}

.loading-container h3 {
  font-size: 18px;
  font-weight: 600;
  color: var(--color-text-dark);
  margin-bottom: 8px;
}

.loading-container p {
  margin: 0;
  font-size: 14px;
}

/* Error State */
.error-container {
  text-align: center;
  padding: 60px 20px;
  background: white;
  border-radius: 12px;
  color: var(--color-text-light);
  margin-bottom: 16px;
}

.error-container svg {
  margin-bottom: 16px;
  color: #ef4444;
  width: 64px;
  height: 64px;
}

.error-container h3 {
  font-size: 18px;
  font-weight: 600;
  color: var(--color-text-dark);
  margin-bottom: 8px;
}

.error-container p {
  margin: 0 0 24px 0;
  font-size: 14px;
}

.retry-btn {
  padding: 12px 32px;
  background: var(--color-primary);
  color: white;
  border: none;
  border-radius: 8px;
  font-size: 14px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
}

.retry-btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(22, 117, 231, 0.3);
}

.seats-content {
  display: contents;
}

/* Seat Info */
.seat-info {
  background: white;
  border-radius: 12px;
  padding: 12px 16px;
  margin-bottom: 16px;
  border: 1px solid #e5e7eb;
}

.passenger-info {
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.passenger-name {
  font-weight: 600;
  color: var(--color-text-dark);
  font-size: 14px;
}

.passenger-seat {
  font-size: 12px;
  color: var(--color-text-light);
}

/* Seat Legend */
.seat-legend {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 12px;
  margin-bottom: 16px;
  padding: 12px;
  background: white;
  border-radius: 12px;
  border: 1px solid #e5e7eb;
}

.legend-item {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 12px;
  color: var(--color-text-dark);
}

.legend-seat {
  width: 24px;
  height: 24px;
  border-radius: 4px;
  flex-shrink: 0;
}

.legend-seat.available {
  border: 2px solid #d1d5db;
  background: white;
}

.legend-seat.chosen {
  background: var(--color-primary);
  border: 2px solid var(--color-primary);
}

.legend-seat.filled {
  background: #9ca3af;
  border: 2px solid #9ca3af;
}

.legend-seat.filled-female {
  background: #ec4899;
  border: 2px solid #ec4899;
}

/* Seat Container */
.seat-container {
  background: white;
  border-radius: 12px;
  padding: 16px;
  margin-bottom: 16px;
  border: 1px solid #e5e7eb;
}

/* Class Tabs */
.class-tabs {
  display: flex;
  gap: 32px;
  margin-bottom: 20px;
  overflow-x: auto;
  padding-bottom: 8px;
  justify-content: center;
  padding-left: 12px;
  padding-right: 12px;
}

.class-tab {
  padding: 10px 22px;
  border: 2px solid #d1d5db;
  background: white;
  color: var(--color-text-light);
  border-radius: 28px;
  cursor: pointer;
  font-size: 14px;
  font-weight: 600;
  white-space: nowrap;
  transition: all 0.25s ease;
}

.class-tab:hover {
  border-color: var(--color-primary);
  color: var(--color-primary);
}

.class-tab.active {
  background: var(--color-primary);
  border-color: var(--color-primary);
  color: white;
}

/* Seats Grid */
.seats-grid {
  display: flex;
  flex-direction: column;
  gap: 16px;
  margin-bottom: 16px;
}

.seat-row {
  display: flex;
  justify-content: center;
}

.seat-row-content {
  display: flex;
  align-items: center;
  gap: 16px;
}

.row-number {
  width: 28px;
  text-align: right;
  font-size: 12px;
  font-weight: 600;
  color: var(--color-text-light);
  flex: 0 0 28px;
}

.row-seats-container {
  display: flex;
  align-items: center;
  gap: 0;
  justify-content: center;
}

.row-seats-left,
.row-seats-right {
  display: flex;
  gap: 8px;
}

.row-gap {
  width: 80px;
  flex: 0 0 80px;
}

.seat {
  width: 56px;
  height: 56px;
  flex: 0 0 56px;
  border-radius: 6px;
  border: 2px solid #d1d5db;
  background: white;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 12px;
  font-weight: 600;
  transition: all 0.18s ease;
  color: var(--color-text-dark);
}

.seat:hover:not(:disabled) {
  transform: translateY(-2px);
  border-color: var(--color-primary);
  background: rgba(22, 117, 231, 0.06);
}

.seat.available {
  cursor: pointer;
}

.seat.chosen {
  background: var(--color-primary);
  border-color: var(--color-primary);
  color: white;
}

.seat.filled_male,
.seat.filled_female {
  background: #9ca3af;
  border-color: #9ca3af;
  color: white;
  cursor: not-allowed;
}

.seat.filled_female {
  background: #ec4899;
  border-color: #ec4899;
}

.seat:disabled {
  cursor: not-allowed;
}

.seat-label {
  font-weight: 600;
}

.seat-gender {
  font-weight: 700;
  font-size: 12px;
}

/* Row Labels */
.row-labels {
  display: flex;
  gap: 8px;
  margin-left: 32px;
}

.row-label {
  flex: 1;
  text-align: center;
  font-size: 12px;
  font-weight: 600;
  color: var(--color-text-light);
  padding-top: 8px;
  border-top: 1px solid #e5e7eb;
}

/* Save Button */
.save-btn {
  width: 100%;
  padding: 14px 24px;
  background: linear-gradient(135deg, var(--color-primary) 0%, var(--color-secondary) 100%);
  color: white;
  border: none;
  border-radius: 8px;
  font-weight: 600;
  font-size: 14px;
  cursor: pointer;
  transition: all 0.3s ease;
  box-shadow: 0 4px 12px rgba(22, 117, 231, 0.3);
  margin-bottom: 20px;
}

.save-btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 16px rgba(22, 117, 231, 0.4);
}

.save-btn:active {
  transform: translateY(0);
}

/* Responsive */
@media (max-width: 768px) {
  .main-content {
    padding: 16px 12px;
  }

  .header {
    padding: 12px;
    gap: 12px;
  }

  .header-title {
    font-size: 18px;
  }

  .trip-info {
    padding: 12px;
  }

  .seat-legend {
    grid-template-columns: repeat(2, 1fr);
    gap: 8px;
  }

  .class-tabs {
    gap: 8px;
  }

  .class-tab {
    padding: 6px 12px;
    font-size: 12px;
  }

  .seat-container {
    padding: 12px;
  }

  .seat-row {
    gap: 6px;
  }

  .seat {
    border-radius: 4px;
    font-size: 10px;
  }

  .save-btn {
    padding: 12px 20px;
    font-size: 13px;
  }
}

@media (max-width: 480px) {
  .select-seat-container {
    height: 100vh;
  }

  .header {
    padding: 12px;
    gap: 8px;
  }

  .header-title {
    font-size: 16px;
  }

  .header-subtitle {
    font-size: 12px;
  }

  .main-content {
    padding: 12px;
  }

  .trip-info {
    padding: 10px;
    margin-bottom: 12px;
  }

  .trip-time {
    font-size: 13px;
  }

  .trip-name {
    font-size: 12px;
  }

  .seat-legend {
    grid-template-columns: repeat(2, 1fr);
    gap: 6px;
    padding: 10px;
    margin-bottom: 12px;
  }

  .legend-item {
    gap: 6px;
    font-size: 11px;
  }

  .legend-seat {
    width: 20px;
    height: 20px;
  }

  .warning-box {
    padding: 10px;
    font-size: 11px;
    gap: 8px;
  }

  .warning-box svg {
    width: 16px;
    height: 16px;
  }

  .seat-container {
    padding: 10px;
    margin-bottom: 12px;
  }

  .class-tabs {
    gap: 6px;
    margin-bottom: 12px;
  }

  .class-tab {
    padding: 6px 10px;
    font-size: 11px;
  }

  .row-number {
    width: 20px;
    font-size: 11px;
  }

  .seat-row {
    gap: 4px;
  }

  .seat {
    border-radius: 4px;
    font-size: 9px;
  }

  .row-labels {
    margin-left: 24px;
    gap: 4px;
  }

  .row-label {
    font-size: 11px;
  }

  .save-btn {
    padding: 12px 18px;
    font-size: 12px;
    margin-bottom: 16px;
  }
}

@keyframes spin {
  from {
    transform: rotate(0deg);
  }
  to {
    transform: rotate(360deg);
  }
}
</style>
