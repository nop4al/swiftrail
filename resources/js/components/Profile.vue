<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()

// Active Tab State
const activeTab = ref('profil') // 'profil', 'tiket', 'loyalty'
const activeTicketTab = ref('semua') // For ticket filtering

// User data
const userProfile = ref({
  name: 'Andi Saputra',
  email: 'andi@swiftrail.com',
  phone: '-',
  identityNumber: '-',
  address: '-',
  identityType: '-',
  city: '-',
  initials: 'AS',
  userId: 'SW-88291',
  balance: 250000,
  balanceLabel: 'SALDO SWIFTPAY',
  lastPasswordChange: '30 hari yang lalu'
})

// Loyalty data
const userLoyalty = ref({
  name: 'Andi Saputra',
  email: 'andi@swiftrail.com',
  status: 'Silver Member',
  totalPoints: 4500,
  balanceLabel: 'SALDO SWIFTPAY',
  balance: 250000,
  currentLevel: 'Silver',
  progress: 30, // 30% towards Gold
  currentPoints: 4500,
  nextLevelPoints: 7500
})

const membershipLevels = ref([
  {
    name: 'Silver',
    benefits: ['Dapatkan 1 poin per Rp 1.000', 'Gratis ongkir untuk pembelian pertama', 'Akses early booking']
  },
  {
    name: 'Gold',
    benefits: ['Dapatkan 1.5 poin per Rp 1.000', 'Gratis ongkir unlimited', 'Priority customer service', 'Birthday voucher']
  },
  {
    name: 'Platinum',
    benefits: ['Dapatkan 2 poin per Rp 1.000', 'Gratis ongkir + upgrade seat', 'VIP lounge access', 'Personal account manager']
  }
])

const pointsHistory = ref([
  { date: '15 Desember 2024', activity: 'Pembelian tiket KRL', points: '+250' },
  { date: '10 Desember 2024', activity: 'Pembelian tiket Kereta Jarak Jauh', points: '+500' },
  { date: '05 Desember 2024', activity: 'Bonus sign up member', points: '+100' },
  { date: '01 Desember 2024', activity: 'Pembelian tiket LRT', points: '+150' }
])

const ticketTabs = ref([
  { id: 'semua', label: 'Semua' },
  { id: 'antar-kota', label: 'Antar Kota' },
  { id: 'lokal', label: 'Lokal' },
  { id: 'krl', label: 'KRL' },
  { id: 'lrt', label: 'LRT' },
  { id: 'bandara', label: 'Bandara' }
])

const showLogoutConfirm = ref(false)

const handleLogout = () => {
  localStorage.removeItem('userToken')
  router.push('/login')
}

const goToEditProfile = () => {
  router.push('/edit-profile')
}

const changePassword = () => {
  router.push('/change-password')
}

const formatCurrency = (amount) => {
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    minimumFractionDigits: 0
  }).format(amount)
}
</script>

<template>
  <div class="profile-page">
    <!-- Header -->
    <header class="header">
      <div class="header-container">
        <div class="logo">
          <div class="logo-icon">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
              <rect width="24" height="24" rx="6" fill="#1675E7"/>
              <path d="M6 8C6 6.89543 6.89543 6 8 6H16C17.1046 6 18 6.89543 18 8V14C18 15.1046 17.1046 16 16 16H8C6.89543 16 6 15.1046 6 14V8Z" fill="white"/>
              <rect x="8" y="8" width="3" height="2" rx="0.5" fill="#1675E7"/>
              <rect x="13" y="8" width="3" height="2" rx="0.5" fill="#1675E7"/>
              <circle cx="9" cy="18" r="1.5" fill="white"/>
              <circle cx="15" cy="18" r="1.5" fill="white"/>
            </svg>
          </div>
          <div class="logo-text">
            <span class="logo-title">SwiftRail</span>
            <span class="logo-subtitle">SUPER APP</span>
          </div>
        </div>
        
        <nav class="nav">
          <router-link to="/profile" class="nav-link">Profile</router-link>
          <router-link to="/login" class="nav-link">Masuk</router-link>
          <router-link to="/register" class="nav-btn">Daftar</router-link>
        </nav>
      </div>
    </header>

    <div class="profile-container">
      <!-- Main Content Card -->
      <div class="main-content-card">
        <!-- Profile Tab Content -->
        <div v-if="activeTab === 'profil'" class="tab-content profile-content">
          <!-- Header Background -->
          <div class="profile-header-bg"></div>

          <!-- Profile Header Content -->
          <div class="profile-header-content">
            <!-- Avatar and Text Row -->
            <div class="profile-avatar-text">
              <!-- Avatar -->
              <div class="avatar">
                {{ userProfile.initials }}
              </div>

              <!-- Profile Info -->
              <div class="profile-info-text">
                <h1 class="profile-name">{{ userProfile.name }}</h1>
                <p class="profile-email">{{ userProfile.email }}</p>
                <p class="profile-id">ID: {{ userProfile.userId }}</p>
              </div>

              <!-- Edit Button -->
              <button class="edit-btn" @click="goToEditProfile">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path d="M11 4H4C2.89543 4 2 4.89543 2 6V20C2 21.1046 2.89543 22 4 22H18C19.1046 22 20 21.1046 20 20V13" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                  <path d="M18.5 2.5C19.3284 1.67157 20.6716 1.67157 21.5 2.5C22.3284 3.32843 22.3284 4.67157 21.5 5.5L11 16H7V12L18.5 2.5Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                Edit Profil
              </button>
            </div>
          </div>

          <!-- Info Grid -->
          <div class="info-grid">
            <div class="info-card">
              <label class="info-label">NOMOR TELEPON</label>
              <p class="info-value">{{ userProfile.phone }}</p>
            </div>
            <div class="info-card">
              <label class="info-label">IDENTITAS (KTP)</label>
              <p class="info-value">{{ userProfile.identityNumber }}</p>
            </div>
            <div class="info-card">
              <label class="info-label">DOMISILI</label>
              <p class="info-value">{{ userProfile.address }}</p>
            </div>
          </div>

          <!-- Security Section -->
          <div class="security-section">
            <div class="security-content">
              <h3 class="security-title">Keamanan Akun</h3>
              <p class="security-text">Kata sandi terakhir diubah 30 hari yang lalu.</p>
            </div>
            <button class="update-password-btn" @click="changePassword">
              Ubah Kata Sandi
            </button>
          </div>
        </div>

        <!-- Tickets Tab Content -->
        <div v-if="activeTab === 'tiket'" class="tab-content tickets-content">
          <!-- Ticket Tabs -->
          <div class="ticket-tabs">
            <button 
              v-for="tab in ticketTabs" 
              :key="tab.id"
              :class="['ticket-tab', { active: activeTicketTab === tab.id }]"
              @click="activeTicketTab = tab.id"
            >
              {{ tab.label }}
            </button>
          </div>

          <!-- Empty State -->
          <div class="empty-state">
            <div class="empty-icon">
              <svg width="64" height="64" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M3 5a2 2 0 0 1 2-2h4.586a2 2 0 0 1 1.414.586l7.414 7.414a2 2 0 0 1 0 2.828l-7.414 7.414a2 2 0 0 1-2.828 0L3 12.414V5z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
                <circle cx="8" cy="8" r="1.5" fill="currentColor"/>
              </svg>
            </div>
            <h3 class="empty-title">Belum ada tiket</h3>
            <p class="empty-description">Anda belum membeli tiket apapun untuk kategori ini</p>
            <router-link to="/" class="empty-link">Cari tiket sekarang</router-link>
          </div>
        </div>

        <!-- Loyalty Tab Content -->
        <div v-if="activeTab === 'loyalty'" class="tab-content loyalty-content">
          <!-- Status Card -->
          <div class="loyalty-status-card">
            <div class="status-header">
              <div class="status-info">
                <h3 class="status-title">{{ userLoyalty.status }}</h3>
                <p class="status-points">{{ formatCurrency(userLoyalty.totalPoints) }} poin</p>
              </div>
            </div>

            <!-- Progress Bar -->
            <div class="progress-section">
              <div class="progress-labels">
                <span class="progress-from">{{ userLoyalty.currentLevel }}</span>
                <span class="progress-to">Gold</span>
              </div>
              <div class="progress-bar">
                <div class="progress-fill" :style="{ width: userLoyalty.progress + '%' }"></div>
              </div>
              <div class="progress-info">
                <span>{{ userLoyalty.currentPoints }} poin</span>
                <span>{{ userLoyalty.nextLevelPoints }} poin</span>
              </div>
            </div>
          </div>

          <!-- Membership Levels Grid -->
          <div class="membership-grid">
            <div v-for="level in membershipLevels" :key="level.name" class="membership-card">
              <div class="membership-icon">
                <svg width="32" height="32" viewBox="0 0 24 24" fill="currentColor">
                  <path d="M12 1L15.09 8.26H23L17.55 12.25L19.64 19.52L12 15.5L4.36 19.52L6.45 12.25L1 8.26H8.91L12 1Z"/>
                </svg>
              </div>
              <h4 class="membership-name">{{ level.name }}</h4>
              <ul class="membership-benefits">
                <li v-for="(benefit, idx) in level.benefits" :key="idx">{{ benefit }}</li>
              </ul>
            </div>
          </div>

          <!-- Points History -->
          <div class="points-history">
            <h4 class="history-title">Riwayat Poin</h4>
            <div class="history-table">
              <div class="history-header">
                <span>Tanggal</span>
                <span>Aktivitas</span>
                <span>Poin</span>
              </div>
              <div v-for="(item, idx) in pointsHistory" :key="idx" class="history-row">
                <span class="history-date">{{ item.date }}</span>
                <span class="history-activity">{{ item.activity }}</span>
                <span class="history-points">{{ item.points }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Sidebar Menu -->
      <div class="sidebar">
        <!-- Balance Card -->
        <div class="balance-card">
          <p class="balance-label">SALDO SWIFTPAY</p>
          <p class="balance-amount">{{ formatCurrency(userProfile.balance) }}</p>
          <div class="balance-actions">
            <button class="balance-btn">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M12 5V19M5 12H19" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
              </svg>
              Top Up
            </button>
            <button class="balance-btn">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M17 12H7M12 17L7 12L12 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
              Riwayat
            </button>
          </div>
        </div>

        <!-- Menu Items -->
        <div class="menu-list">
          <button class="menu-item" :class="{ active: activeTab === 'tiket' }" @click="activeTab = 'tiket'">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M3 5a2 2 0 0 1 2-2h4.586a2 2 0 0 1 1.414.586l7.414 7.414a2 2 0 0 1 0 2.828l-7.414 7.414a2 2 0 0 1-2.828 0L3 12.414V5z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
              <circle cx="8" cy="8" r="1.5" fill="currentColor"/>
            </svg>
            Tiket Saya
          </button>
          <button class="menu-item" :class="{ active: activeTab === 'profil' }" @click="activeTab = 'profil'">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
              <circle cx="12" cy="8" r="4" stroke="currentColor" stroke-width="2"/>
              <path d="M6 20c0-3.3 2.69-6 6-6s6 2.7 6 6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
            </svg>
            Profil Saya
          </button>
          <button class="menu-item" :class="{ active: activeTab === 'loyalty' }" @click="activeTab = 'loyalty'">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M12 1L15.09 8.26H23L17.55 12.25L19.64 19.52L12 15.5L4.36 19.52L6.45 12.25L1 8.26H8.91L12 1Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
            </svg>
            Loyalty
          </button>
          <button class="menu-item logout" @click="showLogoutConfirm = true">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M19 12H5M5 12L12 19M5 12L12 5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            Keluar
          </button>
        </div>
      </div>
    </div>

    <!-- Logout Confirmation Modal -->
    <Transition name="modal">
      <div v-if="showLogoutConfirm" class="modal-overlay" @click="showLogoutConfirm = false">
        <div class="modal-content" @click.stop>
          <div class="modal-header">
            <h3>Konfirmasi Logout</h3>
            <button class="modal-close" @click="showLogoutConfirm = false">
              <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M18 6L6 18M6 6L18 18" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
              </svg>
            </button>
          </div>
          <div class="modal-body">
            <p>Apakah Anda yakin ingin keluar dari akun ini?</p>
          </div>
          <div class="modal-footer">
            <button class="btn-cancel" @click="showLogoutConfirm = false">Batal</button>
            <button class="btn-confirm" @click="handleLogout">Ya, Keluar</button>
          </div>
        </div>
      </div>
    </Transition>
  </div>
</template>

<style scoped>
.profile-page {
  width: 100%;
  min-height: 100vh;
  background: var(--color-bg-light);
  padding-top: 64px;
}

/* Header */
.header {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  background: var(--color-white);
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
  z-index: 100;
}

.header-container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0.875rem 1.5rem;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.logo {
  display: flex;
  align-items: center;
  gap: 0.625rem;
}

.logo-icon {
  display: flex;
  align-items: center;
  justify-content: center;
}

.logo-text {
  display: flex;
  flex-direction: column;
}

.logo-title {
  font-size: 1.25rem;
  font-weight: 700;
  color: var(--color-text-dark);
  line-height: 1.2;
}

.logo-subtitle {
  font-size: 0.625rem;
  font-weight: 600;
  color: var(--color-text-light);
  letter-spacing: 0.1em;
}

.nav {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.nav-link {
  font-size: 0.9375rem;
  font-weight: 500;
  color: var(--color-text-dark);
  padding: 0.5rem 1rem;
}

.nav-link:hover {
  color: var(--color-primary);
}

.nav-btn {
  font-size: 0.9375rem;
  font-weight: 600;
  color: var(--color-white);
  background: var(--color-primary);
  padding: 0.625rem 1.5rem;
  border-radius: 8px;
}

.nav-btn:hover {
  background: var(--color-primary-dark);
  transform: translateY(-1px);
}

.profile-container {
  max-width: 1200px;
  margin: 0 auto;
  display: grid;
  grid-template-columns: 1fr 320px;
  gap: 2rem;
  padding: 2rem 1.5rem;
}

/* Main Content Card */
.main-content-card {
  background: var(--color-white);
  border-radius: 16px;
  overflow: hidden;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
  display: flex;
  flex-direction: column;
}

/* Tab Navigation */
.tab-navigation {
  display: flex;
  gap: 0;
  border-bottom: 2px solid #e5e7eb;
  padding: 0 2rem;
  background: var(--color-white);
}

.tab-btn {
  flex: 1;
  padding: 1.25rem 1rem;
  background: none;
  border: none;
  border-bottom: 3px solid transparent;
  color: var(--color-text-light);
  font-weight: 600;
  font-size: 0.95rem;
  cursor: pointer;
  transition: all 0.3s ease;
  position: relative;
  margin-bottom: -2px;
}

.tab-btn:hover {
  color: var(--color-primary);
}

.tab-btn.active {
  color: var(--color-primary);
  border-bottom-color: var(--color-primary);
}

/* Tab Content */
.tab-content {
  flex: 1;
  animation: fadeIn 0.3s ease;
}

@keyframes fadeIn {
  from {
    opacity: 0;
  }
  to {
    opacity: 1;
  }
}

/* Profile Content */
.profile-content {
  display: flex;
  flex-direction: column;
}

/* Tickets Content */
.tickets-content {
  padding: 2rem;
}

.ticket-tabs {
  display: flex;
  gap: 1rem;
  flex-wrap: wrap;
  margin-bottom: 2rem;
}

.ticket-tab {
  padding: 0.625rem 1.25rem;
  background: var(--color-bg-light);
  border: 1px solid #e5e7eb;
  border-radius: 8px;
  color: var(--color-text-dark);
  font-weight: 500;
  font-size: 0.875rem;
  cursor: pointer;
  transition: all 0.2s ease;
}

.ticket-tab:hover {
  border-color: var(--color-primary);
  color: var(--color-primary);
}

.ticket-tab.active {
  background: var(--color-primary);
  color: var(--color-white);
  border-color: var(--color-primary);
}

.empty-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 4rem 2rem;
  text-align: center;
}

.empty-icon {
  color: var(--color-text-light);
  margin-bottom: 1rem;
  opacity: 0.5;
}

.empty-title {
  font-size: 1.25rem;
  font-weight: 700;
  color: var(--color-text-dark);
  margin-bottom: 0.5rem;
}

.empty-description {
  color: var(--color-text-light);
  font-size: 0.9375rem;
  margin-bottom: 1.5rem;
}

.empty-link {
  padding: 0.75rem 1.75rem;
  background: var(--color-primary);
  color: var(--color-white);
  border-radius: 8px;
  font-weight: 600;
  text-decoration: none;
  cursor: pointer;
  transition: all 0.2s ease;
}

.empty-link:hover {
  background: var(--color-primary-dark);
  transform: translateY(-2px);
}

/* Loyalty Content */
.loyalty-content {
  padding: 2rem;
  display: flex;
  flex-direction: column;
  gap: 2rem;
}

.loyalty-status-card {
  background: linear-gradient(135deg, var(--color-primary) 0%, var(--color-secondary) 100%);
  color: var(--color-white);
  padding: 2rem;
  border-radius: 12px;
  box-shadow: 0 4px 12px rgba(22, 117, 231, 0.2);
}

.status-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 2rem;
}

.status-info {
  flex: 1;
}

.status-title {
  font-size: 1.5rem;
  font-weight: 700;
  margin-bottom: 0.5rem;
}

.status-points {
  font-size: 1.125rem;
  opacity: 0.9;
}

.progress-section {
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
}

.progress-labels {
  display: flex;
  justify-content: space-between;
  font-size: 0.875rem;
}

.progress-from,
.progress-to {
  font-weight: 600;
}

.progress-bar {
  height: 8px;
  background: rgba(255, 255, 255, 0.3);
  border-radius: 4px;
  overflow: hidden;
}

.progress-fill {
  height: 100%;
  background: rgba(255, 255, 255, 0.9);
  border-radius: 4px;
  transition: width 0.3s ease;
}

.progress-info {
  display: flex;
  justify-content: space-between;
  font-size: 0.8125rem;
  opacity: 0.85;
}

.membership-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 1.5rem;
}

.membership-card {
  background: var(--color-bg-light);
  border: 1px solid #e5e7eb;
  padding: 1.5rem;
  border-radius: 12px;
  text-align: center;
  transition: all 0.2s ease;
}

.membership-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
  border-color: var(--color-primary);
}

.membership-icon {
  font-size: 2rem;
  color: var(--color-primary);
  margin-bottom: 1rem;
}

.membership-name {
  font-size: 1rem;
  font-weight: 700;
  color: var(--color-text-dark);
  margin-bottom: 1rem;
}

.membership-benefits {
  list-style: none;
  padding: 0;
  margin: 0;
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.membership-benefits li {
  font-size: 0.8125rem;
  color: var(--color-text-light);
  line-height: 1.4;
}

.points-history {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.history-title {
  font-size: 1rem;
  font-weight: 700;
  color: var(--color-text-dark);
  margin: 0;
}

.history-table {
  background: var(--color-bg-light);
  border: 1px solid #e5e7eb;
  border-radius: 12px;
  overflow: hidden;
}

.history-header {
  display: grid;
  grid-template-columns: 1fr 2fr 1fr;
  padding: 1rem;
  background: #f3f4f6;
  font-weight: 600;
  font-size: 0.875rem;
  color: var(--color-text-dark);
  border-bottom: 1px solid #e5e7eb;
}

.history-row {
  display: grid;
  grid-template-columns: 1fr 2fr 1fr;
  padding: 1rem;
  border-bottom: 1px solid #e5e7eb;
  align-items: center;
  font-size: 0.875rem;
}

.history-row:last-child {
  border-bottom: none;
}

.history-date {
  color: var(--color-text-light);
}

.history-activity {
  color: var(--color-text-dark);
}

.history-points {
  font-weight: 600;
  color: var(--color-primary);
}

/* Profile Card */
.profile-card {
  background: var(--color-white);
  border-radius: 16px;
  overflow: hidden;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
}

.profile-header-bg {
  height: 120px;
  background: linear-gradient(135deg, var(--color-primary) 0%, var(--color-secondary) 100%);
  position: relative;
}

.profile-header-bg::after {
  content: '';
  position: absolute;
  inset: 0;
  background-image: 
    radial-gradient(circle at 20% 80%, rgba(255,255,255,0.1) 0%, transparent 50%),
    radial-gradient(circle at 80% 20%, rgba(9, 216, 227, 0.15) 0%, transparent 40%);
}

.profile-header-content {
  padding: 2rem;
  display: flex;
  flex-direction: column;
  margin-top: -60px;
  position: relative;
  z-index: 1;
  width: 100%;
  box-sizing: border-box;
}

.profile-avatar-text {
  display: flex;
  align-items: center;
  gap: 1.5rem;
  width: 100%;
}

.avatar {
  width: 100px;
  height: 100px;
  background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
  border: 4px solid var(--color-white);
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 2rem;
  font-weight: 700;
  color: var(--color-white);
  flex-shrink: 0;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.profile-info-text {
  flex: 1;
  min-width: 0;
  margin-top: 1.5rem;
}

.profile-name {
  font-size: 1.75rem;
  font-weight: 700;
  color: var(--color-text-dark);
  margin-bottom: 0;
  margin-top: 0.5rem;
}

.profile-email {
  font-size: 0.9375rem;
  color: var(--color-text-light);
  margin-bottom: 0;
  margin-top: 0;
}

.profile-id {
  font-size: 0.8125rem;
  color: var(--color-primary);
  font-weight: 600;
  margin-top: 0;
}

.edit-btn {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
  padding: 0.75rem 1.5rem;
  background: linear-gradient(135deg, var(--color-primary) 0%, var(--color-secondary) 100%);
  color: var(--color-white);
  border: none;
  border-radius: 10px;
  font-weight: 600;
  font-size: 0.875rem;
  cursor: pointer;
  transition: all 0.2s ease;
  flex-shrink: 0;
  white-space: nowrap;
}

.edit-btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 16px rgba(22, 117, 231, 0.3);
}

/* Info Grid */
.info-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 1rem;
  padding: 2rem;
  border-bottom: 1px solid #e5e7eb;
}

.info-card {
  background: var(--color-bg-light);
  padding: 1.25rem;
  border-radius: 12px;
  border: 1px solid #e5e7eb;
}

.info-label {
  font-size: 0.75rem;
  font-weight: 600;
  color: var(--color-text-light);
  text-transform: uppercase;
  letter-spacing: 0.05em;
  margin-bottom: 0.75rem;
  display: block;
}

.info-value {
  font-size: 1rem;
  font-weight: 600;
  color: var(--color-text-dark);
}

/* Security Section */
.security-section {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 1.5rem 2rem;
  background: #eff6ff;
  border-left: 4px solid var(--color-primary);
}

.security-content {
  flex: 1;
}

.security-title {
  font-size: 1rem;
  font-weight: 700;
  color: var(--color-primary);
  margin-bottom: 0.25rem;
}

.security-text {
  font-size: 0.875rem;
  color: var(--color-text-light);
}

.update-password-btn {
  padding: 0.625rem 1rem;
  background: var(--color-white);
  color: var(--color-primary);
  border: 1px solid var(--color-primary);
  border-radius: 8px;
  font-weight: 600;
  font-size: 0.875rem;
  cursor: pointer;
  transition: all 0.2s ease;
  white-space: nowrap;
}

.update-password-btn:hover {
  background: var(--color-primary);
  color: var(--color-white);
}

/* Sidebar */
.sidebar {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

.balance-card {
  background: linear-gradient(135deg, var(--color-primary) 0%, var(--color-secondary) 100%);
  color: var(--color-white);
  padding: 2rem;
  border-radius: 16px;
  box-shadow: 0 4px 12px rgba(22, 117, 231, 0.2);
}

.balance-label {
  font-size: 0.75rem;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  opacity: 0.9;
  margin-bottom: 0.75rem;
}

.balance-amount {
  font-size: 1.75rem;
  font-weight: 700;
  margin-bottom: 1.5rem;
}

.balance-actions {
  display: flex;
  gap: 0.75rem;
}

.balance-btn {
  flex: 1;
  padding: 0.625rem;
  background: rgba(255, 255, 255, 0.2);
  color: var(--color-white);
  border: 1px solid rgba(255, 255, 255, 0.4);
  border-radius: 8px;
  font-weight: 600;
  font-size: 0.75rem;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
  transition: all 0.2s ease;
}

.balance-btn:hover {
  background: rgba(255, 255, 255, 0.3);
  border-color: rgba(255, 255, 255, 0.6);
}

/* Menu List */
.menu-list {
  background: var(--color-white);
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
}

.menu-item {
  display: flex;
  align-items: center;
  gap: 1rem;
  padding: 1.25rem;
  background: transparent;
  border: none;
  border-bottom: 1px solid #e5e7eb;
  color: var(--color-text-dark);
  font-weight: 500;
  font-size: 0.9375rem;
  cursor: pointer;
  transition: all 0.2s ease;
  width: 100%;
  text-align: left;
}

.menu-item:last-child {
  border-bottom: none;
}

.menu-item:hover {
  background: var(--color-bg-light);
  color: var(--color-primary);
}

.menu-item.active {
  background: rgba(22, 117, 231, 0.05);
  color: var(--color-primary);
  font-weight: 600;
}

.menu-item.logout {
  color: #f97316;
}

.menu-item.logout:hover {
  color: #ea580c;
  background: #fff7ed;
}

.menu-item svg {
  flex-shrink: 0;
}

/* Modal */
.modal-overlay {
  position: fixed;
  inset: 0;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
  padding: 1rem;
}

.modal-content {
  background: var(--color-white);
  border-radius: 16px;
  box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
  max-width: 420px;
  width: 100%;
  overflow: hidden;
}

.modal-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 1.5rem;
  border-bottom: 1px solid #e5e7eb;
}

.modal-header h3 {
  font-size: 1.25rem;
  font-weight: 700;
  color: var(--color-text-dark);
}

.modal-close {
  background: none;
  border: none;
  color: var(--color-text-light);
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
}

.modal-close:hover {
  color: var(--color-primary);
}

.modal-body {
  padding: 1.5rem;
}

.modal-body p {
  font-size: 0.9375rem;
  color: var(--color-text-dark);
  line-height: 1.6;
}

.modal-footer {
  display: flex;
  gap: 1rem;
  padding: 1.5rem;
  border-top: 1px solid #e5e7eb;
}

.btn-cancel,
.btn-confirm {
  flex: 1;
  padding: 0.75rem 1.25rem;
  font-size: 0.95rem;
  font-weight: 600;
  border: none;
  border-radius: 10px;
  cursor: pointer;
  transition: all 0.2s ease;
}

.btn-cancel {
  background: var(--color-bg-light);
  color: var(--color-text-dark);
  border: 1.5px solid #e5e7eb;
}

.btn-cancel:hover {
  border-color: var(--color-text-dark);
  background: #f3f4f6;
}

.btn-confirm {
  background: linear-gradient(135deg, var(--color-primary) 0%, var(--color-secondary) 100%);
  color: var(--color-white);
}

.btn-confirm:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 16px rgba(22, 117, 231, 0.3);
}

/* Modal Animation */
.modal-enter-active,
.modal-leave-active {
  transition: opacity 0.2s ease;
}

.modal-enter-from,
.modal-leave-to {
  opacity: 0;
}

/* Responsive */
@media (max-width: 1024px) {
  .profile-container {
    grid-template-columns: 1fr;
  }

  .sidebar {
    grid-template-columns: repeat(2, 1fr);
  }

  .info-grid {
    grid-template-columns: repeat(2, 1fr);
  }

  .membership-grid {
    grid-template-columns: repeat(2, 1fr);
  }

  .header-container {
    padding: 0.75rem 1rem;
  }

  .logo-title {
    font-size: 1.1rem;
  }

  .nav {
    gap: 0.5rem;
  }

  .nav-link {
    padding: 0.4rem 0.75rem;
    font-size: 0.875rem;
  }

  .nav-btn {
    padding: 0.5rem 1rem;
    font-size: 0.875rem;
  }
}

@media (max-width: 768px) {
  .profile-page {
    padding-top: 56px;
  }

  .profile-header-content {
    flex-direction: column;
    margin-top: -50px;
    padding: 1.5rem 1rem;
    gap: 1rem;
  }

  .edit-btn {
    width: 100%;
  }

  .info-grid {
    grid-template-columns: 1fr;
    gap: 1rem;
  }

  .security-section {
    flex-direction: column;
    gap: 1rem;
    align-items: flex-start;
  }

  .update-password-btn {
    width: 100%;
  }

  .membership-grid {
    grid-template-columns: 1fr;
  }

  .history-header,
  .history-row {
    grid-template-columns: 0.8fr 1.2fr 0.8fr;
  }

  .tab-navigation {
    padding: 0 1rem;
  }

  .tab-btn {
    padding: 1rem 0.75rem;
    font-size: 0.875rem;
  }

  .tickets-content,
  .loyalty-content {
    padding: 1.5rem;
  }

  .balance-card {
    padding: 1.5rem;
  }

  .balance-label {
    font-size: 0.7rem;
  }

  .balance-amount {
    font-size: 1.5rem;
  }

  .balance-actions {
    gap: 0.5rem;
  }

  .balance-btn {
    font-size: 0.7rem;
    padding: 0.5rem;
  }

  .menu-item {
    padding: 1rem;
    font-size: 0.875rem;
    gap: 0.75rem;
  }

  .menu-item svg {
    width: 18px;
    height: 18px;
  }

  .header-container {
    padding: 0.75rem 1rem;
  }

  .logo-title {
    font-size: 1rem;
  }

  .logo-subtitle {
    font-size: 0.5rem;
  }

  .nav {
    gap: 0.5rem;
  }

  .nav-link {
    padding: 0.4rem 0.5rem;
    font-size: 0.8rem;
  }

  .nav-btn {
    padding: 0.4rem 0.8rem;
    font-size: 0.8rem;
  }
}

@media (max-width: 480px) {
  .profile-page {
    padding: 52px 0.75rem 1rem;
  }

  .profile-card {
    border-radius: 12px;
  }

  .profile-header-bg {
    height: 100px;
  }

  .profile-header-content {
    flex-direction: column;
    padding: 1.5rem 1rem;
    margin-top: -50px;
  }

  .avatar {
    width: 80px;
    height: 80px;
    font-size: 1.75rem;
  }

  .profile-name {
    font-size: 1.5rem;
  }

  .info-grid {
    padding: 1.5rem 1rem;
  }

  .info-label {
    font-size: 0.7rem;
  }

  .info-value {
    font-size: 0.9rem;
  }

  .security-section {
    padding: 1rem;
    border-left: 3px solid var(--color-primary);
  }

  .security-title {
    font-size: 0.9rem;
  }

  .security-text {
    font-size: 0.8rem;
  }

  .update-password-btn {
    padding: 0.5rem 0.75rem;
    font-size: 0.8rem;
  }

  .tab-navigation {
    padding: 0 0.75rem;
  }

  .tab-btn {
    padding: 0.875rem 0.5rem;
    font-size: 0.75rem;
  }

  .tickets-content,
  .loyalty-content {
    padding: 1rem 0.75rem;
  }

  .ticket-tabs {
    gap: 0.5rem;
    margin-bottom: 1rem;
  }

  .ticket-tab {
    padding: 0.5rem 0.75rem;
    font-size: 0.75rem;
  }

  .empty-state {
    padding: 2rem 1rem;
  }

  .empty-icon {
    width: 48px;
    height: 48px;
  }

  .empty-title {
    font-size: 1rem;
  }

  .empty-description {
    font-size: 0.8rem;
  }

  .loyalty-status-card {
    padding: 1.25rem;
  }

  .status-title {
    font-size: 1.25rem;
  }

  .status-points {
    font-size: 1rem;
  }

  .membership-grid {
    grid-template-columns: 1fr;
    gap: 1rem;
  }

  .membership-card {
    padding: 1rem;
  }

  .membership-icon {
    font-size: 1.75rem;
  }

  .membership-name {
    font-size: 0.9rem;
  }

  .membership-benefits li {
    font-size: 0.75rem;
  }

  .history-title {
    font-size: 0.9rem;
  }

  .history-header,
  .history-row {
    grid-template-columns: 0.8fr 1fr 0.6fr;
    font-size: 0.75rem;
    padding: 0.75rem;
  }

  .header-container {
    padding: 0.5rem 0.75rem;
  }

  .logo {
    gap: 0.4rem;
  }

  .logo-icon {
    width: 24px;
    height: 24px;
  }

  .logo-title {
    font-size: 0.9rem;
  }

  .logo-subtitle {
    font-size: 0.5rem;
  }

  .nav {
    gap: 0.3rem;
  }

  .nav-link {
    display: none;
  }

  .nav-btn {
    padding: 0.4rem 0.6rem;
    font-size: 0.7rem;
  }
}

</style>
