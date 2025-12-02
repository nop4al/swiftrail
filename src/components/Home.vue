<script setup>
import { ref, computed } from 'vue'
import { tabs } from '../data/tabs.js'
import { formatDate, searchTrains as performSearch } from '../utils/helpers.js'

const activeTab = ref('antar-kota')
const fromStation = ref('Gambir (GMR)')
const toStation = ref('Bandung (BD)')
const travelDate = ref(new Date().toISOString().split('T')[0])

const formattedDate = computed(() => {
  return formatDate(travelDate.value)
})

const swapStations = () => {
  const temp = fromStation.value
  fromStation.value = toStation.value
  toStation.value = temp
}

const searchTrains = () => {
  performSearch({
    fromStation: fromStation.value,
    toStation: toStation.value,
    travelDate: travelDate.value,
    activeTab: activeTab.value
  })
}
</script>

<template>
  <div class="homepage">
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

    <!-- Hero Section -->
    <section class="hero">
      <div class="hero-bg">
        <div class="hero-gradient"></div>
        <div class="hero-pattern"></div>
      </div>
      
      <div class="hero-content">
        <h1 class="hero-title">Jelajahi Indonesia</h1>
        <p class="hero-subtitle">
          Akses layanan Kereta Api Antar Kota, Lokal, KRL, LRT, hingga Bandara dalam satu aplikasi.
        </p>
      </div>

      <!-- Booking Card -->
      <div class="booking-card">
        <!-- Tabs -->
        <div class="booking-tabs">
          <button 
            v-for="tab in tabs" 
            :key="tab.id"
            :class="['tab-btn', { active: activeTab === tab.id }]"
            @click="activeTab = tab.id"
          >
            <span class="tab-icon">
              <!-- Train Icon -->
              <svg v-if="tab.icon === 'train'" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M4 11V8C4 5.79086 5.79086 4 8 4H16C18.2091 4 20 5.79086 20 8V11M4 11V15C4 16.1046 4.89543 17 6 17H7M4 11H20M20 11V15C20 16.1046 19.1046 17 18 17H17M7 17V19L5 21M7 17H17M17 17V19L19 21" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                <rect x="7" y="7" width="3" height="2" rx="0.5" fill="currentColor"/>
                <rect x="14" y="7" width="3" height="2" rx="0.5" fill="currentColor"/>
              </svg>
              <!-- Tram Icon -->
              <svg v-else-if="tab.icon === 'tram'" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M5 10V16C5 17.1046 5.89543 18 7 18H17C18.1046 18 19 17.1046 19 16V10C19 7.79086 17.2091 6 15 6H9C6.79086 6 5 7.79086 5 10Z" stroke="currentColor" stroke-width="2"/>
                <path d="M8 18V21M16 18V21" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                <path d="M12 3V6M9 3H15" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                <circle cx="8" cy="14" r="1.5" fill="currentColor"/>
                <circle cx="16" cy="14" r="1.5" fill="currentColor"/>
              </svg>
              <!-- Commuter Icon -->
              <svg v-else-if="tab.icon === 'commuter'" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <rect x="5" y="4" width="14" height="14" rx="3" stroke="currentColor" stroke-width="2"/>
                <path d="M8 18V21M16 18V21" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                <rect x="8" y="7" width="3" height="3" rx="0.5" fill="currentColor"/>
                <rect x="13" y="7" width="3" height="3" rx="0.5" fill="currentColor"/>
                <circle cx="9" cy="14" r="1" fill="currentColor"/>
                <circle cx="15" cy="14" r="1" fill="currentColor"/>
              </svg>
              <!-- Bolt Icon -->
              <svg v-else-if="tab.icon === 'bolt'" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M13 2L4 14H11L10 22L20 10H13L13 2Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
              <!-- Plane Icon -->
              <svg v-else-if="tab.icon === 'plane'" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M3 17L9 12L3 7V17Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M9 12H21M21 12L17 8M21 12L17 16" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
            </span>
            <span class="tab-label">{{ tab.label }}</span>
          </button>
        </div>

        <!-- Booking Form -->
        <div class="booking-form">
          <div class="form-group">
            <label class="form-label">DARI STASIUN</label>
            <div class="input-wrapper">
              <span class="input-icon from">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path d="M4 11V8C4 5.79086 5.79086 4 8 4H16C18.2091 4 20 5.79086 20 8V11M4 11V15C4 16.1046 4.89543 17 6 17H18C19.1046 17 20 16.1046 20 15V11M4 11H20" stroke="currentColor" stroke-width="2"/>
                  <circle cx="8" cy="14" r="1" fill="currentColor"/>
                  <circle cx="16" cy="14" r="1" fill="currentColor"/>
                </svg>
              </span>
              <input 
                type="text" 
                v-model="fromStation"
                class="form-input"
                placeholder="Pilih stasiun keberangkatan"
              />
            </div>
          </div>

          <button class="swap-btn" @click="swapStations" title="Tukar stasiun">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M7 16L3 12M3 12L7 8M3 12H21M17 8L21 12M21 12L17 16" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
          </button>

          <div class="form-group">
            <label class="form-label">KE STASIUN</label>
            <div class="input-wrapper">
              <span class="input-icon to">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path d="M12 21C12 21 19 15.5 19 10C19 6.13401 15.866 3 12 3C8.13401 3 5 6.13401 5 10C5 15.5 12 21 12 21Z" stroke="currentColor" stroke-width="2"/>
                  <circle cx="12" cy="10" r="3" stroke="currentColor" stroke-width="2"/>
                </svg>
              </span>
              <input 
                type="text" 
                v-model="toStation"
                class="form-input"
                placeholder="Pilih stasiun tujuan"
              />
            </div>
          </div>

          <div class="form-group date-group">
            <label class="form-label">TANGGAL</label>
            <div class="input-wrapper">
              <span class="input-icon">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <rect x="3" y="6" width="18" height="15" rx="2" stroke="currentColor" stroke-width="2"/>
                  <path d="M3 10H21" stroke="currentColor" stroke-width="2"/>
                  <path d="M8 3V6M16 3V6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                </svg>
              </span>
              <input 
                type="date" 
                v-model="travelDate"
                class="form-input date-input"
              />
            </div>
          </div>

          <button class="search-btn" @click="searchTrains">
            Cari
          </button>
        </div>
      </div>
    </section>

    <!-- Features Section -->
    <section class="features">
      <div class="features-container">
        <div class="feature-card">
          <div class="feature-icon">
            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22Z" stroke="currentColor" stroke-width="2"/>
              <path d="M12 6V12L16 14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
            </svg>
          </div>
          <h3>Pemesanan Cepat</h3>
          <p>Pesan tiket dalam hitungan detik dengan proses yang mudah dan efisien.</p>
        </div>

        <div class="feature-card">
          <div class="feature-icon">
            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M9 12L11 14L15 10" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
              <path d="M20.618 5.984C17.456 2.844 12.518 2.122 8.618 4.192C5.018 6.104 2.748 9.944 3.058 14.024C3.268 16.934 4.678 19.614 6.988 21.394" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
              <path d="M17.012 21.394C19.322 19.614 20.732 16.934 20.942 14.024" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
            </svg>
          </div>
          <h3>Pembayaran Aman</h3>
          <p>Transaksi terjamin dengan berbagai metode pembayaran yang aman.</p>
        </div>

        <div class="feature-card">
          <div class="feature-icon">
            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M17 8H19C20.1046 8 21 8.89543 21 10V18C21 19.1046 20.1046 20 19 20H5C3.89543 20 3 19.1046 3 18V10C3 8.89543 3.89543 8 5 8H7" stroke="currentColor" stroke-width="2"/>
              <path d="M12 4V14M12 14L8 10M12 14L16 10" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
          </div>
          <h3>E-Tiket Instan</h3>
          <p>Dapatkan tiket elektronik langsung di aplikasi tanpa perlu cetak.</p>
        </div>

        <div class="feature-card">
          <div class="feature-icon">
            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M18 18.72C18.93 18.46 19.77 17.96 20.4 17.28C22.31 15.16 22.03 11.98 19.78 10.2C19.4 9.92 18.98 9.7 18.53 9.54" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
              <path d="M5.47 9.54C5.02 9.7 4.6 9.92 4.22 10.2C1.97 11.98 1.69 15.16 3.6 17.28C4.23 17.96 5.07 18.46 6 18.72" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
              <path d="M8.53 13.02C7.22 14.33 7.22 16.47 8.53 17.78C9.84 19.09 11.98 19.09 13.29 17.78C14.6 16.47 14.6 14.33 13.29 13.02C11.98 11.71 9.84 11.71 8.53 13.02Z" stroke="currentColor" stroke-width="2"/>
              <path d="M12 5C12 5 14.5 7.5 14.5 9.5C14.5 10.88 13.38 12 12 12C10.62 12 9.5 10.88 9.5 9.5C9.5 7.5 12 5 12 5Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
          </div>
          <h3>Layanan 24/7</h3>
          <p>Dukungan pelanggan siap membantu kapan saja Anda butuhkan.</p>
        </div>
      </div>
    </section>

    <!-- Promo Section -->
    <section class="promo">
      <div class="promo-container">
        <h2 class="section-title">Promo Terbaru</h2>
        <div class="promo-grid">
          <div class="promo-card">
            <div class="promo-badge">Diskon Spesial</div>
            <div class="promo-image" style="background: linear-gradient(135deg, #DC143C 0%, #8B0000 100%);">
              <div class="promo-building">Laboratorium Klinik<br>PRAMITA</div>
            </div>
            <div class="promo-content">
              <div class="promo-discount">25%</div>
              <p class="promo-desc">Diskon untuk pengguna SwiftRail</p>
            </div>
          </div>

          <div class="promo-card">
            <div class="promo-badge">Diskon Flash</div>
            <div class="promo-image" style="background: linear-gradient(135deg, #2C3E50 0%, #1A252F 100%);">
              <div class="promo-hotel">IBIS YIA KULON<br>PROGO</div>
            </div>
            <div class="promo-content">
              <div class="promo-discount">30%</div>
              <p class="promo-desc">Menginap lebih hemat</p>
            </div>
          </div>

          <div class="promo-card">
            <div class="promo-badge">Promo Gratis</div>
            <div class="promo-image" style="background: linear-gradient(135deg, #FF6B35 0%, #F7931E 100%);">
              <div class="promo-food">Makanan di<br>TERMINAL</div>
            </div>
            <div class="promo-content">
              <div class="promo-discount">Gratis</div>
              <p class="promo-desc">Voucher makan senilai Rp50k</p>
            </div>
          </div>

          <div class="promo-card">
            <div class="promo-badge">Cashback</div>
            <div class="promo-image" style="background: linear-gradient(135deg, #1ABC9C 0%, #16A085 100%);">
              <div class="promo-travel">PERJALANAN<br>ANDA</div>
            </div>
            <div class="promo-content">
              <div class="promo-discount">50K</div>
              <p class="promo-desc">Cashback setiap pembelian</p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Popular Destinations Section -->
    <section class="destinations">
      <div class="destinations-container">
        <h2 class="section-title">Tujuan Populer</h2>
        <div class="destinations-grid">
          <div class="destination-card jakarta">
            <h3>Jakarta</h3>
            <p class="destination-subtitle">Ibu Kota</p>
          </div>
          <div class="destination-card bandung">
            <h3>Bandung</h3>
            <p class="destination-subtitle">Kota Bunga</p>
          </div>
          <div class="destination-card yogya">
            <h3>Yogyakarta</h3>
            <p class="destination-subtitle">Budaya & Sejarah</p>
          </div>
          <div class="destination-card surabaya">
            <h3>Surabaya</h3>
            <p class="destination-subtitle">Pahlawan</p>
          </div>
        </div>
      </div>
    </section>

    <!-- Rewards Section -->
    <section class="rewards">
      <div class="rewards-container">
        <h2 class="section-title">Tukar Railpoin mu sekarang</h2>
        <div class="rewards-header">
          <span>Lihat Semua →</span>
        </div>
        
        <div class="rewards-grid">
          <div class="reward-card gold">
            <div class="reward-badge">RAILPOIN</div>
            <div class="reward-icon">🎫</div>
            <div class="reward-amount">25.000</div>
            <p class="reward-label">Poin Perjalanan</p>
          </div>
          <div class="reward-card platinum">
            <div class="reward-badge">RAILPOIN</div>
            <div class="reward-icon">💎</div>
            <div class="reward-amount">50.000</div>
            <p class="reward-label">Poin Premium</p>
          </div>
          <div class="reward-card silver">
            <div class="reward-badge">VOUCHER</div>
            <div class="reward-icon">🎁</div>
            <div class="reward-amount">100K</div>
            <p class="reward-label">Voucher Makanan</p>
          </div>
          <div class="reward-card bronze">
            <div class="reward-badge">BONUS</div>
            <div class="reward-icon">🎉</div>
            <div class="reward-amount">15.000</div>
            <p class="reward-label">Diskon Tiket</p>
          </div>
        </div>
      </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
      <div class="footer-container">
        <div class="footer-brand">
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
          <p class="footer-desc">Solusi transportasi kereta api terlengkap di Indonesia.</p>
        </div>
        
        <div class="footer-links">
          <div class="footer-col">
            <h4>Layanan</h4>
            <a href="#">Antar Kota</a>
            <a href="#">Lokal</a>
            <a href="#">KRL</a>
            <a href="#">LRT/MRT</a>
            <a href="#">Bandara</a>
          </div>
          <div class="footer-col">
            <h4>Perusahaan</h4>
            <a href="#">Tentang Kami</a>
            <a href="#">Karir</a>
            <a href="#">Blog</a>
            <a href="#">Kontak</a>
          </div>
          <div class="footer-col">
            <h4>Bantuan</h4>
            <a href="#">FAQ</a>
            <a href="#">Syarat & Ketentuan</a>
            <a href="#">Kebijakan Privasi</a>
            <a href="#">Pusat Bantuan</a>
          </div>
        </div>
      </div>
      
      <div class="footer-bottom">
        <p>&copy; 2025 SwiftRail. All rights reserved.</p>
      </div>
    </footer>
  </div>
</template>

<style scoped>
.homepage {
  width: 100%;
  min-height: 100vh;
  background: var(--color-bg-light);
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

/* Hero */
.hero {
  position: relative;
  padding-top: 64px;
  min-height: 600px;
  display: flex;
  flex-direction: column;
  align-items: center;
}

.hero-bg {
  position: absolute;
  top: 64px;
  left: 0;
  right: 0;
  height: 420px;
  overflow: hidden;
}

.hero-gradient {
  position: absolute;
  inset: 0;
  background: linear-gradient(135deg, var(--color-primary-dark) 0%, var(--color-primary) 50%, var(--color-secondary) 100%);
}

.hero-pattern {
  position: absolute;
  inset: 0;
  background-image: 
    radial-gradient(circle at 20% 80%, rgba(255,255,255,0.1) 0%, transparent 50%),
    radial-gradient(circle at 80% 20%, rgba(9, 216, 227, 0.15) 0%, transparent 40%),
    radial-gradient(circle at 40% 40%, rgba(255,255,255,0.05) 0%, transparent 60%);
}

.hero-content {
  position: relative;
  z-index: 1;
  text-align: center;
  padding: 4rem 1.5rem 2rem;
  max-width: 700px;
}

.hero-title {
  font-size: 3rem;
  font-weight: 700;
  color: var(--color-white);
  margin-bottom: 1rem;
  font-style: italic;
  text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.hero-subtitle {
  font-size: 1.125rem;
  color: rgba(255, 255, 255, 0.9);
  line-height: 1.7;
  max-width: 600px;
  margin: 0 auto;
}

/* Booking Card */
.booking-card {
  position: relative;
  z-index: 10;
  background: var(--color-white);
  border-radius: 16px;
  box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1), 0 2px 10px rgba(0, 0, 0, 0.05);
  margin: 0 1.5rem;
  max-width: 900px;
  width: calc(100% - 3rem);
  overflow: hidden;
}

.booking-tabs {
  display: flex;
  border-bottom: 1px solid #e5e7eb;
  overflow-x: auto;
}

.tab-btn {
  flex: 1;
  min-width: 100px;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 0.375rem;
  padding: 1rem 0.75rem;
  background: transparent;
  border: none;
  border-bottom: 3px solid transparent;
  color: var(--color-text-light);
}

.tab-btn:hover {
  color: var(--color-primary);
  background: rgba(22, 117, 231, 0.03);
}

.tab-btn.active {
  color: var(--color-primary);
  border-bottom-color: var(--color-primary);
}

.tab-icon {
  display: flex;
  align-items: center;
  justify-content: center;
}

.tab-label {
  font-size: 0.75rem;
  font-weight: 600;
  letter-spacing: 0.02em;
}

.booking-form {
  display: flex;
  align-items: flex-end;
  gap: 0.75rem;
  padding: 1.5rem;
  flex-wrap: wrap;
}

.form-group {
  flex: 1;
  min-width: 180px;
}

.date-group {
  flex: 0.8;
  min-width: 160px;
}

.form-label {
  display: block;
  font-size: 0.6875rem;
  font-weight: 600;
  color: var(--color-text-light);
  letter-spacing: 0.05em;
  margin-bottom: 0.5rem;
}

.input-wrapper {
  position: relative;
  display: flex;
  align-items: center;
}

.input-icon {
  position: absolute;
  left: 0.875rem;
  color: var(--color-text-light);
  display: flex;
  align-items: center;
  justify-content: center;
}

.input-icon.from {
  color: var(--color-primary);
}

.input-icon.to {
  color: var(--color-secondary);
}

.form-input {
  width: 100%;
  padding: 0.875rem 0.875rem 0.875rem 2.75rem;
  font-size: 0.9375rem;
  font-weight: 500;
  color: var(--color-text-dark);
  background: var(--color-bg-light);
  border: 1.5px solid #e5e7eb;
  border-radius: 10px;
}

.form-input:focus {
  outline: none;
  border-color: var(--color-primary);
  box-shadow: 0 0 0 3px rgba(22, 117, 231, 0.1);
}

.form-input::placeholder {
  color: #9ca3af;
}

.date-input {
  cursor: pointer;
}

.swap-btn {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 40px;
  height: 40px;
  background: var(--color-bg-light);
  border: 1.5px solid #e5e7eb;
  border-radius: 10px;
  color: var(--color-text-light);
  flex-shrink: 0;
  margin-bottom: 0.25rem;
}

.swap-btn:hover {
  background: var(--color-primary);
  border-color: var(--color-primary);
  color: var(--color-white);
  transform: rotate(180deg);
}

.search-btn {
  padding: 0.875rem 2.5rem;
  font-size: 1rem;
  font-weight: 600;
  color: var(--color-white);
  background: linear-gradient(135deg, var(--color-primary) 0%, var(--color-secondary) 100%);
  border: none;
  border-radius: 10px;
  box-shadow: 0 4px 15px rgba(22, 117, 231, 0.3);
  flex-shrink: 0;
}

.search-btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 20px rgba(22, 117, 231, 0.4);
}

.search-btn:active {
  transform: translateY(0);
}

/* Features */
.features {
  padding: 6rem 1.5rem;
  background: var(--color-white);
  margin-top: 3rem;
}

.features-container {
  max-width: 1200px;
  margin: 0 auto;
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 2rem;
}

.feature-card {
  text-align: center;
  padding: 2rem 1.5rem;
  border-radius: 16px;
}

.feature-card:hover {
  background: var(--color-bg-light);
  transform: translateY(-4px);
}

.feature-icon {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 64px;
  height: 64px;
  background: linear-gradient(135deg, rgba(22, 117, 231, 0.1) 0%, rgba(9, 216, 227, 0.1) 100%);
  border-radius: 16px;
  color: var(--color-primary);
  margin-bottom: 1.25rem;
}

.feature-card h3 {
  font-size: 1.125rem;
  font-weight: 600;
  color: var(--color-text-dark);
  margin-bottom: 0.75rem;
}

.feature-card p {
  font-size: 0.9375rem;
  color: var(--color-text-light);
  line-height: 1.6;
}

/* Footer */
.footer {
  background: var(--color-text-dark);
  color: var(--color-white);
  padding-top: 4rem;
}

.footer-container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 1.5rem;
  display: grid;
  grid-template-columns: 1.5fr 2fr;
  gap: 4rem;
}

.footer-brand .logo-title {
  color: var(--color-white);
}

.footer-brand .logo-subtitle {
  color: rgba(255, 255, 255, 0.6);
}

.footer-desc {
  margin-top: 1rem;
  font-size: 0.9375rem;
  color: rgba(255, 255, 255, 0.7);
  line-height: 1.6;
}

.footer-links {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 2rem;
}

.footer-col h4 {
  font-size: 0.875rem;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  margin-bottom: 1.25rem;
  color: var(--color-white);
}

.footer-col a {
  display: block;
  font-size: 0.9375rem;
  color: rgba(255, 255, 255, 0.7);
  margin-bottom: 0.75rem;
}

.footer-col a:hover {
  color: var(--color-accent);
}

.footer-bottom {
  margin-top: 3rem;
  padding: 1.5rem;
  border-top: 1px solid rgba(255, 255, 255, 0.1);
  text-align: center;
}

.footer-bottom p {
  font-size: 0.875rem;
  color: rgba(255, 255, 255, 0.5);
}

/* Promo Section */
.promo {
  padding: 4rem 1.5rem;
  background: var(--color-white);
}

.promo-container {
  max-width: 1200px;
  margin: 0 auto;
}

.section-title {
  font-size: 1.75rem;
  font-weight: 700;
  color: var(--color-text-dark);
  margin-bottom: 2rem;
}

.promo-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 1.5rem;
}

.promo-card {
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
}

.promo-card:hover {
  transform: translateY(-6px) scale(1.02);
  box-shadow: 0 12px 24px rgba(0, 0, 0, 0.15);
}

.promo-badge {
  position: absolute;
  top: 0.75rem;
  left: 0.75rem;
  background: rgba(255, 255, 255, 0.95);
  color: var(--color-text-dark);
  padding: 0.4rem 0.8rem;
  border-radius: 20px;
  font-size: 0.65rem;
  font-weight: 700;
  z-index: 2;
  backdrop-filter: blur(10px);
}

.promo-image {
  position: relative;
  height: 140px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-weight: 700;
  text-align: center;
  padding: 1rem;
}

.promo-building,
.promo-hotel,
.promo-food,
.promo-travel {
  font-size: 1rem;
  line-height: 1.4;
  text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
}

.promo-content {
  padding: 1rem;
  background: white;
}

.promo-discount {
  font-size: 1.75rem;
  font-weight: 700;
  color: var(--color-primary);
  margin-bottom: 0.5rem;
}

.promo-desc {
  font-size: 0.8rem;
  color: var(--color-text-light);
  line-height: 1.4;
}

/* Destinations Section */
.destinations {
  padding: 4rem 1.5rem;
  background: var(--color-bg-light);
}

.destinations-container {
  max-width: 1200px;
  margin: 0 auto;
}

.destinations-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 1.5rem;
}

.destination-card {
  height: 200px;
  border-radius: 16px;
  overflow: hidden;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  color: white;
  font-size: 1.75rem;
  font-weight: 700;
  cursor: pointer;
  position: relative;
}

.destination-card:hover {
  transform: scale(1.05);
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
}

.destination-subtitle {
  font-size: 0.875rem;
  font-weight: 500;
  margin-top: 0.5rem;
  opacity: 0.95;
}

.destination-card.jakarta {
  background: linear-gradient(135deg, rgba(22, 117, 231, 0.8), rgba(12, 157, 229, 0.8)), url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 400 250"><rect fill="%231675E7" width="400" height="250"/></svg>');
  background-size: cover;
  background-position: center;
}

.destination-card.bandung {
  background: linear-gradient(135deg, rgba(255, 100, 100, 0.8), rgba(220, 20, 60, 0.8)), url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 400 250"><rect fill="%23FF6464" width="400" height="250"/></svg>');
  background-size: cover;
  background-position: center;
}

.destination-card.yogya {
  background: linear-gradient(135deg, rgba(255, 165, 0, 0.8), rgba(255, 140, 0, 0.8)), url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 400 250"><rect fill="%23FFA500" width="400" height="250"/></svg>');
  background-size: cover;
  background-position: center;
}

.destination-card.surabaya {
  background: linear-gradient(135deg, rgba(52, 152, 219, 0.8), rgba(44, 62, 80, 0.8)), url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 400 250"><rect fill="%233498DB" width="400" height="250"/></svg>');
  background-size: cover;
  background-position: center;
}

/* Rewards Section */
.rewards {
  padding: 4rem 1.5rem;
  background: white;
}

.rewards-container {
  max-width: 1200px;
  margin: 0 auto;
}

.rewards-header {
  display: flex;
  justify-content: flex-end;
  margin-bottom: 2rem;
}

.rewards-header span {
  color: var(--color-primary);
  font-weight: 600;
  cursor: pointer;
}

.rewards-header span:hover {
  opacity: 0.8;
}

.rewards-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 1.5rem;
}

.reward-card {
  padding: 1.5rem;
  border-radius: 12px;
  text-align: center;
  position: relative;
  overflow: hidden;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.reward-card::before {
  content: '';
  position: absolute;
  top: -50%;
  right: -50%;
  width: 200px;
  height: 200px;
  background: radial-gradient(circle, rgba(255, 255, 255, 0.3) 0%, transparent 70%);
  border-radius: 50%;
}

.reward-card.gold {
  background: linear-gradient(135deg, #FFE5B4 0%, #FFD699 100%);
}

.reward-card.platinum {
  background: linear-gradient(135deg, #E0E0E0 0%, #B0C4DE 100%);
}

.reward-card.silver {
  background: linear-gradient(135deg, #F0E68C 0%, #DAA520 100%);
}

.reward-card.bronze {
  background: linear-gradient(135deg, #CD7F32 0%, #8B4513 100%);
}

.reward-card:hover {
  transform: translateY(-6px) scale(1.02);
  box-shadow: 0 12px 24px rgba(0, 0, 0, 0.2);
}

.reward-badge {
  display: inline-block;
  background: rgba(0, 0, 0, 0.15);
  color: white;
  padding: 0.4rem 0.8rem;
  border-radius: 6px;
  font-size: 0.7rem;
  font-weight: 700;
  margin-bottom: 0.75rem;
  position: relative;
  z-index: 1;
}

.reward-icon {
  font-size: 2.5rem;
  margin-bottom: 0.5rem;
  position: relative;
  z-index: 1;
}

.reward-amount {
  font-size: 1.75rem;
  font-weight: 700;
  color: rgba(0, 0, 0, 0.8);
  margin-bottom: 0.5rem;
  position: relative;
  z-index: 1;
}

.reward-label {
  font-size: 0.8rem;
  font-weight: 600;
  color: rgba(0, 0, 0, 0.7);
  position: relative;
  z-index: 1;
}

/* Responsive */
@media (max-width: 1024px) {
  .features-container {
    grid-template-columns: repeat(2, 1fr);
  }
  
  .footer-container {
    grid-template-columns: 1fr;
    gap: 3rem;
  }
  
  .promo-grid,
  .destinations-grid,
  .rewards-grid {
    grid-template-columns: repeat(2, 1fr);
  }
}

@media (max-width: 768px) {
  .hero-title {
    font-size: 2.25rem;
  }
  
  .hero-subtitle {
    font-size: 1rem;
  }
  
  .booking-form {
    flex-direction: column;
    align-items: stretch;
  }
  
  .form-group,
  .date-group {
    min-width: 100%;
  }
  
  .swap-btn {
    align-self: center;
    margin: 0.5rem 0;
    transform: rotate(90deg);
  }
  
  .swap-btn:hover {
    transform: rotate(270deg);
  }
  
  .search-btn {
    width: 100%;
    margin-top: 0.5rem;
  }
  
  .features-container {
    grid-template-columns: 1fr;
  }
  
  .footer-links {
    grid-template-columns: repeat(2, 1fr);
  }

  .promo {
    padding: 3rem 1.5rem;
  }

  .section-title {
    font-size: 1.5rem;
    margin-bottom: 1.5rem;
  }

  .destination-card {
    height: 160px;
    font-size: 1.25rem;
  }

  .destination-subtitle {
    font-size: 0.75rem;
  }

  .reward-card {
    padding: 1.25rem;
  }

  .reward-amount {
    font-size: 1.5rem;
  }

  .reward-icon {
    font-size: 2rem;
  }

  .promo-grid,
  .destinations-grid,
  .rewards-grid {
    grid-template-columns: repeat(2, 1fr);
  }
}

@media (max-width: 480px) {
  .header-container {
    padding: 0.75rem 1rem;
  }
  
  .nav-link {
    padding: 0.5rem;
    font-size: 0.875rem;
  }
  
  .nav-btn {
    padding: 0.5rem 1rem;
    font-size: 0.875rem;
  }
  
  .hero-content {
    padding: 3rem 1rem 1.5rem;
  }
  
  .hero-title {
    font-size: 1.875rem;
  }
  
  .booking-card {
    margin: 0 1rem;
    width: calc(100% - 2rem);
  }
  
  .booking-tabs {
    justify-content: flex-start;
  }
  
  .tab-btn {
    min-width: 70px;
    padding: 0.75rem 0.5rem;
  }
  
  .tab-label {
    font-size: 0.625rem;
  }
  
  .booking-form {
    padding: 1rem;
  }
  
  .footer-links {
    grid-template-columns: 1fr;
  }

  .promo {
    padding: 2rem 1rem;
  }

  .section-title {
    font-size: 1.25rem;
    margin-bottom: 1rem;
  }

  .promo-image {
    height: 120px;
  }

  .promo-building,
  .promo-hotel,
  .promo-food,
  .promo-travel {
    font-size: 0.85rem;
  }

  .promo-content {
    padding: 0.75rem;
  }

  .promo-discount {
    font-size: 1.25rem;
  }

  .promo-desc {
    font-size: 0.7rem;
  }

  .destinations {
    padding: 2rem 1rem;
  }

  .destinations-grid {
    gap: 1rem;
  }

  .destination-card {
    height: 150px;
    font-size: 1.25rem;
  }

  .rewards {
    padding: 2rem 1rem;
  }

  .rewards-grid {
    gap: 1rem;
  }

  .reward-card {
    padding: 1rem;
  }

  .reward-amount {
    font-size: 1.5rem;
  }

  .reward-label {
    font-size: 0.75rem;
  }
}

</style>

