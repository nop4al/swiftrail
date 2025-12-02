<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()

// Login State
const isLoggedIn = ref(false)
const adminEmail = ref('')
const adminPassword = ref('')
const rememberMe = ref(false)
const isLoading = ref(false)
const showPassword = ref(false)

// Admin Dashboard State
const adminName = ref('Admin SwiftRail')
const adminRole = ref('Administrator')

// Chart references
const revenueChartCanvas = ref(null)
const ticketChartCanvas = ref(null)
const userChartCanvas = ref(null)

const handleAdminLogin = async () => {
  if (!adminEmail.value || !adminPassword.value) {
    alert('Silakan isi semua field')
    return
  }

  isLoading.value = true
  try {
    // Simulasi API call
    await new Promise(resolve => setTimeout(resolve, 1000))
    
    // Validasi simple - ubah sesuai kebutuhan
    if (adminEmail.value === 'admin@swiftrail.com' && adminPassword.value === 'admin123') {
      isLoggedIn.value = true
      localStorage.setItem('adminToken', 'token-' + Date.now())
      console.log('Login Admin:', { email: adminEmail.value, rememberMe: rememberMe.value })
    } else {
      alert('Email atau password salah')
    }
  } catch (error) {
    console.error('Admin login error:', error)
  } finally {
    isLoading.value = false
  }
}

const togglePasswordVisibility = () => {
  showPassword.value = !showPassword.value
}

const handleLogout = () => {
  isLoggedIn.value = false
  adminEmail.value = ''
  adminPassword.value = ''
  localStorage.removeItem('adminToken')
}

// Initialize charts
const initCharts = () => {
  // Pastikan Chart.js sudah loaded
  if (typeof Chart === 'undefined') {
    console.error('Chart.js tidak loaded')
    return
  }

  // Revenue Chart (Line Chart)
  if (revenueChartCanvas.value) {
    const revenueCtx = revenueChartCanvas.value.getContext('2d')
    new Chart(revenueCtx, {
      type: 'line',
      data: {
        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'],
        datasets: [{
          label: 'Revenue (Juta Rp)',
          data: [12, 19, 15, 25, 22, 30, 28, 35, 32, 38, 42, 45],
          borderColor: '#1675E7',
          backgroundColor: 'rgba(22, 117, 231, 0.1)',
          borderWidth: 3,
          fill: true,
          tension: 0.4,
          pointBackgroundColor: '#1675E7',
          pointBorderColor: '#fff',
          pointBorderWidth: 2,
          pointRadius: 5,
          pointHoverRadius: 7
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
          legend: {
            display: true,
            labels: {
              font: { size: 12, weight: 'bold' },
              color: '#333'
            }
          }
        },
        scales: {
          y: {
            beginAtZero: true,
            ticks: { color: '#666', font: { size: 11 } },
            grid: { color: '#e5e7eb' }
          },
          x: {
            ticks: { color: '#666', font: { size: 11 } },
            grid: { color: '#e5e7eb' }
          }
        }
      }
    })
  }

  // Ticket Sales Chart (Bar Chart)
  if (ticketChartCanvas.value) {
    const ticketCtx = ticketChartCanvas.value.getContext('2d')
    new Chart(ticketCtx, {
      type: 'bar',
      data: {
        labels: ['KRL', 'LRT', 'Kereta Jarak Jauh', 'Bandara', 'Lokal'],
        datasets: [{
          label: 'Tiket Terjual',
          data: [450, 320, 280, 200, 380],
          backgroundColor: [
            '#1675E7',
            '#09D8E3',
            '#FF6B6B',
            '#FFA500',
            '#4ECDC4'
          ],
          borderRadius: 8,
          borderSkipped: false
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: true,
        indexAxis: 'y',
        plugins: {
          legend: {
            display: true,
            labels: {
              font: { size: 12, weight: 'bold' },
              color: '#333'
            }
          }
        },
        scales: {
          x: {
            beginAtZero: true,
            ticks: { color: '#666', font: { size: 11 } },
            grid: { color: '#e5e7eb' }
          },
          y: {
            ticks: { color: '#666', font: { size: 11 } },
            grid: { display: false }
          }
        }
      }
    })
  }

  // User Growth Chart (Doughnut Chart)
  if (userChartCanvas.value) {
    const userCtx = userChartCanvas.value.getContext('2d')
    new Chart(userCtx, {
      type: 'doughnut',
      data: {
        labels: ['Regular', 'Silver Member', 'Gold Member', 'Platinum Member'],
        datasets: [{
          data: [450, 280, 180, 88],
          backgroundColor: [
            '#E5E7EB',
            '#FFD700',
            '#C0C0C0',
            '#D946A6'
          ],
          borderColor: '#fff',
          borderWidth: 3
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
          legend: {
            position: 'bottom',
            labels: {
              font: { size: 11 },
              color: '#333',
              padding: 15
            }
          }
        }
      }
    })
  }
}

// Check jika sudah login saat mount
onMounted(() => {
  const token = localStorage.getItem('adminToken')
  if (token) {
    isLoggedIn.value = true
    // Load Chart.js script
    const script = document.createElement('script')
    script.src = 'https://cdn.jsdelivr.net/npm/chart.js'
    script.async = true
    script.onload = () => {
      setTimeout(initCharts, 100)
    }
    document.head.appendChild(script)
  }
})
</script>

<template>
  <!-- Admin Login Page -->
  <div v-if="!isLoggedIn" class="admin-login-page">
    <div class="admin-login-container">
      <!-- Left Section - Branding -->
      <div class="admin-branding">
        <div class="branding-content">
          <div class="logo">
            <div class="logo-icon">
              <svg width="48" height="48" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
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
              <span class="logo-subtitle">ADMIN</span>
            </div>
          </div>
          
          <div class="branding-message">
            <h2>Panel Administrasi</h2>
            <p>Kelola sistem SwiftRail dengan mudah dan efisien.</p>
          </div>

          <div class="benefits">
            <div class="benefit-item">
              <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M9 12L11 14L15 10" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="2"/>
              </svg>
              <span>Manajemen Data Lengkap</span>
            </div>
            <div class="benefit-item">
              <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22Z" stroke="currentColor" stroke-width="2"/>
                <path d="M12 6V12L16 14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
              </svg>
              <span>Akses Real-time</span>
            </div>
            <div class="benefit-item">
              <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M12 1L15.09 8.26H23L17.55 12.25L19.64 19.52L12 15.5L4.36 19.52L6.45 12.25L1 8.26H8.91L12 1Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
              </svg>
              <span>Kontrol Penuh Sistem</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Right Section - Form -->
      <div class="admin-form-section">
        <div class="form-wrapper">
          <h1>Masuk ke Panel Admin</h1>
          <p class="form-subtitle">Masukkan kredensial admin Anda</p>

          <form @submit.prevent="handleAdminLogin" class="admin-form">
            <div class="form-group">
              <label for="admin-email" class="form-label">Email Admin</label>
              <div class="input-wrapper">
                <svg class="input-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <rect x="2" y="4" width="20" height="16" rx="2" stroke="currentColor" stroke-width="2"/>
                  <path d="M2 6L12 13L22 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <input
                  id="admin-email"
                  v-model="adminEmail"
                  type="email"
                  class="form-input"
                  placeholder="Masukkan email admin"
                  required
                />
              </div>
            </div>

            <div class="form-group">
              <label for="admin-password" class="form-label">Password</label>
              <div class="input-wrapper">
                <svg class="input-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <rect x="3" y="11" width="18" height="10" rx="2" stroke="currentColor" stroke-width="2"/>
                  <path d="M7 11V7C7 4.23858 9.23858 2 12 2C14.7614 2 17 4.23858 17 7V11" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                </svg>
                <input
                  id="admin-password"
                  v-model="adminPassword"
                  :type="showPassword ? 'text' : 'password'"
                  class="form-input"
                  placeholder="Masukkan password admin"
                  required
                />
                <button
                  type="button"
                  class="toggle-password"
                  @click="togglePasswordVisibility"
                >
                  <svg v-if="!showPassword" width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12 5C7 5 2.73 8.11 1 12.46C2.73 16.89 7 20 12 20C17 20 21.27 16.89 23 12.46C21.27 8.11 17 5 12 5ZM12 17C9.33 17 7 14.67 7 12C7 9.33 9.33 7 12 7C14.67 7 17 9.33 17 12C17 14.67 14.67 17 12 17Z" fill="currentColor"/>
                    <circle cx="12" cy="12" r="2" fill="currentColor"/>
                  </svg>
                  <svg v-else width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M11.83 9L15.64 12.81C15.87 12.35 16 11.83 16 11.29C16 9.91 15.09 8.75 13.71 8.75C13.17 8.75 12.65 8.88 12.19 9.11M7.53 9.8C6.58 10.45 5.77 11.27 5.14 12.29M7 17.05V17H7.07C7.25 17.34 7.44 17.67 7.65 17.99M19.07 4.57L18 3.5M9.9 5.02L8.81 3.93" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M1 1L23 23" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                  </svg>
                </button>
              </div>
            </div>

            <div class="form-options">
              <label class="checkbox">
                <input v-model="rememberMe" type="checkbox" />
                <span>Ingat saya</span>
              </label>
              <a href="#" class="forgot-password">Lupa password?</a>
            </div>

            <button type="submit" class="admin-login-btn" :disabled="isLoading">
              <span v-if="!isLoading">Masuk ke Admin</span>
              <span v-else>Memproses...</span>
            </button>
          </form>

          <div class="info-box">
            <p><strong>Demo Admin:</strong></p>
            <p>Email: admin@swiftrail.com</p>
            <p>Password: admin123</p>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Admin Dashboard Page -->
  <div v-else class="admin-dashboard">
    <!-- Header -->
    <header class="admin-header">
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
            <span class="logo-subtitle">ADMIN</span>
          </div>
        </div>
        
        <nav class="nav">
          <div class="user-profile-header">
            <div class="avatar-header">{{ adminName.charAt(0) }}</div>
            <div class="user-info">
              <p class="user-name-header">{{ adminName }}</p>
              <p class="user-role-header">{{ adminRole }}</p>
            </div>
          </div>
          <button class="logout-btn-header" @click="handleLogout">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M19 12H5M5 12L12 19M5 12L12 5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            Logout
          </button>
        </nav>
      </div>
    </header>

    <!-- Main Content -->
    <div class="dashboard-content">
      <div class="welcome-section">
        <h1>Selamat Datang, {{ adminName }}!</h1>
        <p>Kelola sistem SwiftRail dari panel administrasi ini</p>
      </div>

      <!-- Dashboard Cards -->
      <div class="dashboard-grid">
        <div class="dashboard-card">
          <div class="card-header">
            <h3>Total Pengguna</h3>
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
              <circle cx="12" cy="8" r="4" stroke="currentColor" stroke-width="2"/>
              <path d="M6 20c0-3.3 2.69-6 6-6s6 2.7 6 6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
            </svg>
          </div>
          <p class="card-value">1,248</p>
          <p class="card-subtitle">+15 bulan ini</p>
        </div>

        <div class="dashboard-card">
          <div class="card-header">
            <h3>Total Transaksi</h3>
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
              <rect x="2" y="6" width="20" height="14" rx="2" stroke="currentColor" stroke-width="2"/>
              <path d="M2 10H22" stroke="currentColor" stroke-width="2"/>
            </svg>
          </div>
          <p class="card-value">8,542</p>
          <p class="card-subtitle">+342 hari ini</p>
        </div>

        <div class="dashboard-card">
          <div class="card-header">
            <h3>Revenue</h3>
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M12 1V23M17 4H9C7.89543 4 7 4.89543 7 6V18C7 19.1046 7.89543 20 9 20H15C16.1046 20 17 19.1046 17 18V6C17 4.89543 16.1046 4 15 4Z" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
            </svg>
          </div>
          <p class="card-value">Rp 45,2M</p>
          <p class="card-subtitle">+8% bulan lalu</p>
        </div>
      </div>

      <!-- Charts Section -->
      <div class="charts-section">
        <div class="charts-grid">
          <!-- Revenue Chart -->
          <div class="chart-container">
            <h3>Revenue Trend</h3>
            <canvas ref="revenueChartCanvas"></canvas>
          </div>

          <!-- Ticket Sales Chart -->
          <div class="chart-container">
            <h3>Penjualan Tiket per Kategori</h3>
            <canvas ref="ticketChartCanvas"></canvas>
          </div>

          <!-- User Membership Chart -->
          <div class="chart-container full-width">
            <h3>Distribusi Member</h3>
            <div class="chart-wrapper">
              <canvas ref="userChartCanvas"></canvas>
            </div>
          </div>
        </div>
      </div>

      <!-- Coming Soon Section -->
      <div class="coming-soon">
        <h2>📋 Fitur Admin Lainnya</h2>
        <div class="features-grid">
          <div class="feature-item">
            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M3 5a2 2 0 0 1 2-2h4.586a2 2 0 0 1 1.414.586l7.414 7.414a2 2 0 0 1 0 2.828l-7.414 7.414a2 2 0 0 1-2.828 0L3 12.414V5z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
              <circle cx="8" cy="8" r="1.5" fill="currentColor"/>
            </svg>
            <h4>Manajemen Tiket</h4>
            <p>Kelola semua tiket dan jadwal kereta</p>
          </div>

          <div class="feature-item">
            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
              <circle cx="12" cy="8" r="4" stroke="currentColor" stroke-width="2"/>
              <path d="M6 20c0-3.3 2.69-6 6-6s6 2.7 6 6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
            </svg>
            <h4>Manajemen Pengguna</h4>
            <p>Kelola akun dan data pengguna</p>
          </div>

          <div class="feature-item">
            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
              <rect x="2" y="6" width="20" height="14" rx="2" stroke="currentColor" stroke-width="2"/>
              <path d="M2 10H22" stroke="currentColor" stroke-width="2"/>
            </svg>
            <h4>Laporan Keuangan</h4>
            <p>Lihat laporan transaksi dan revenue</p>
          </div>

          <div class="feature-item">
            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M9 12L11 14L15 10" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
              <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="2"/>
            </svg>
            <h4>Verifikasi Pembayaran</h4>
            <p>Verifikasi transaksi pembayaran</p>
          </div>

          <div class="feature-item">
            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M12 1L15.09 8.26H23L17.55 12.25L19.64 19.52L12 15.5L4.36 19.52L6.45 12.25L1 8.26H8.91L12 1Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
            </svg>
            <h4>Manajemen Loyalitas</h4>
            <p>Kelola program loyalitas pengguna</p>
          </div>

          <div class="feature-item">
            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22Z" stroke="currentColor" stroke-width="2"/>
              <path d="M12 6V12L16 14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
            </svg>
            <h4>Sistem & Monitoring</h4>
            <p>Monitor performa sistem real-time</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Footer -->
    <footer class="admin-footer">
      <div class="footer-container">
        <div class="footer-brand">
          <div class="logo">
            <div class="logo-icon">
              <svg width="32" height="32" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
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
              <span class="logo-subtitle">ADMIN</span>
            </div>
          </div>
          <p class="footer-desc">Panel administrasi untuk mengelola sistem SwiftRail.</p>
        </div>

        <div class="footer-links">
          <div class="footer-col">
            <h4>Menu</h4>
            <a href="#dashboard">Dashboard</a>
            <a href="#users">Pengguna</a>
            <a href="#tickets">Tiket</a>
            <a href="#transactions">Transaksi</a>
          </div>
          <div class="footer-col">
            <h4>Fitur</h4>
            <a href="#reports">Laporan</a>
            <a href="#analytics">Analitik</a>
            <a href="#settings">Pengaturan</a>
            <a href="#security">Keamanan</a>
          </div>
          <div class="footer-col">
            <h4>Support</h4>
            <a href="#help">Bantuan</a>
            <a href="#docs">Dokumentasi</a>
            <a href="#contact">Hubungi Kami</a>
            <a href="#status">Status Sistem</a>
          </div>
        </div>
      </div>

      <div class="footer-bottom">
        <p>&copy; 2024 SwiftRail Admin. All rights reserved.</p>
      </div>
    </footer>
  </div>
</template>

<style scoped>
/* ===== LOGIN PAGE STYLES ===== */
.admin-login-page {
  width: 100%;
  min-height: 100vh;
  background: var(--color-bg-light);
}

.admin-login-container {
  display: grid;
  grid-template-columns: 1fr 1fr;
  min-height: 100vh;
}

.admin-branding {
  background: linear-gradient(135deg, #1e293b 0%, var(--color-primary) 50%, var(--color-secondary) 100%);
  color: white;
  padding: 3rem;
  display: flex;
  align-items: center;
  justify-content: center;
  position: relative;
  overflow: hidden;
}

.admin-branding::before {
  content: '';
  position: absolute;
  inset: 0;
  background-image: 
    radial-gradient(circle at 20% 80%, rgba(255,255,255,0.1) 0%, transparent 50%),
    radial-gradient(circle at 80% 20%, rgba(9, 216, 227, 0.15) 0%, transparent 40%);
  pointer-events: none;
}

.branding-content {
  position: relative;
  z-index: 1;
  text-align: center;
  max-width: 400px;
}

.logo {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.625rem;
  margin-bottom: 2rem;
  color: white;
}

.logo-icon {
  display: flex;
  align-items: center;
  justify-content: center;
}

.logo-text {
  display: flex;
  flex-direction: column;
  text-align: left;
}

.logo-title {
  font-size: 1.5rem;
  font-weight: 700;
  line-height: 1.2;
}

.logo-subtitle {
  font-size: 0.625rem;
  font-weight: 600;
  letter-spacing: 0.1em;
  opacity: 0.9;
}

.branding-message {
  margin-bottom: 3rem;
}

.branding-message h2 {
  font-size: 2rem;
  font-weight: 700;
  margin-bottom: 1rem;
}

.branding-message p {
  font-size: 1rem;
  opacity: 0.95;
  line-height: 1.6;
}

.benefits {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
  text-align: left;
}

.benefit-item {
  display: flex;
  align-items: center;
  gap: 1rem;
  font-size: 0.95rem;
}

.benefit-item svg {
  flex-shrink: 0;
}

.admin-form-section {
  background: white;
  padding: 3rem;
  display: flex;
  align-items: center;
  justify-content: center;
  overflow-y: auto;
}

.form-wrapper {
  width: 100%;
  max-width: 420px;
}

.form-wrapper h1 {
  font-size: 1.875rem;
  font-weight: 700;
  color: var(--color-text-dark);
  margin-bottom: 0.5rem;
}

.form-subtitle {
  font-size: 0.9375rem;
  color: var(--color-text-light);
  margin-bottom: 2rem;
}

.admin-form {
  margin-bottom: 1.5rem;
}

.form-group {
  margin-bottom: 1.5rem;
}

.form-label {
  display: block;
  font-size: 0.875rem;
  font-weight: 600;
  color: var(--color-text-dark);
  margin-bottom: 0.625rem;
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
  pointer-events: none;
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
  background: white;
}

.form-input::placeholder {
  color: #9ca3af;
}

.toggle-password {
  position: absolute;
  right: 0.875rem;
  background: none;
  border: none;
  color: var(--color-text-light);
  cursor: pointer;
  padding: 0.25rem;
  display: flex;
  align-items: center;
  justify-content: center;
}

.toggle-password:hover {
  color: var(--color-primary);
}

.form-options {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1.5rem;
  font-size: 0.875rem;
}

.checkbox {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  cursor: pointer;
  color: var(--color-text-dark);
}

.checkbox input {
  cursor: pointer;
  width: 16px;
  height: 16px;
  accent-color: var(--color-primary);
}

.forgot-password {
  color: var(--color-primary);
  text-decoration: none;
}

.forgot-password:hover {
  opacity: 0.8;
}

.admin-login-btn {
  width: 100%;
  padding: 0.875rem;
  font-size: 1rem;
  font-weight: 600;
  color: white;
  background: linear-gradient(135deg, var(--color-primary) 0%, var(--color-secondary) 100%);
  border: none;
  border-radius: 10px;
  box-shadow: 0 4px 15px rgba(22, 117, 231, 0.3);
  cursor: pointer;
}

.admin-login-btn:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 6px 20px rgba(22, 117, 231, 0.4);
}

.admin-login-btn:disabled {
  opacity: 0.7;
  cursor: not-allowed;
}

.info-box {
  margin-top: 2rem;
  padding: 1.5rem;
  background: #eff6ff;
  border: 1px solid #bfdbfe;
  border-radius: 10px;
  text-align: left;
  font-size: 0.875rem;
  color: var(--color-text-dark);
}

.info-box p {
  margin: 0.5rem 0;
}

.info-box p:first-child {
  font-weight: 600;
  margin-bottom: 1rem;
}

/* ===== DASHBOARD STYLES ===== */
.admin-dashboard {
  width: 100%;
  min-height: 100vh;
  background: var(--color-bg-light);
}

.admin-header {
  background: var(--color-white);
  border-bottom: 1px solid #e5e7eb;
  position: sticky;
  top: 0;
  z-index: 100;
  display: flex;
  align-items: center;
}

.header-container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0.5rem 1rem;
  display: flex;
  justify-content: space-between;
  align-items: center;
  width: 100%;
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
  gap: 0;
  line-height: 1;
}

.logo-title {
  font-size: 0.8rem;
  font-weight: 700;
  color: var(--color-text-dark);
  line-height: 1.1;
  margin: 0;
}

.logo-subtitle {
  font-size: 0.5rem;
  font-weight: 600;
  color: var(--color-text-light);
  letter-spacing: 0.05em;
  line-height: 1;
  margin: 0;
}

.nav {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.user-profile-header {
  display: flex;
  align-items: center;
  gap: 0.75rem;
}

.avatar-header {
  width: 32px;
  height: 32px;
  background: linear-gradient(135deg, var(--color-primary) 0%, var(--color-secondary) 100%);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-weight: 700;
  font-size: 0.8rem;
}

.user-info {
  display: flex;
  flex-direction: column;
  gap: 0;
  line-height: 1;
}

.user-name-header {
  font-size: 0.75rem;
  font-weight: 600;
  color: var(--color-text-dark);
  margin: 0;
  line-height: 1.1;
}

.user-role-header {
  font-size: 0.65rem;
  color: var(--color-text-light);
  margin: 0;
  line-height: 1;
}

.logout-btn-header {
  display: flex;
  align-items: center;
  gap: 0.3rem;
  padding: 0.4rem 0.8rem;
  background: #fef2f2;
  color: #f97316;
  border: 1px solid #fed7aa;
  border-radius: 6px;
  font-weight: 600;
  font-size: 0.75rem;
  cursor: pointer;
  transition: all 0.2s ease;
}

.logout-btn-header:hover {
  background: #fee2e2;
  border-color: #fbcfe8;
}

.dashboard-content {
  max-width: 1200px;
  margin: 0 auto;
  padding: 1.5rem 1.5rem 0;
}

.welcome-section {
  margin-bottom: 1.5rem;
}

.welcome-section h1 {
  font-size: 1.5rem;
  font-weight: 700;
  color: var(--color-text-dark);
  margin-bottom: 0.25rem;
}

.welcome-section p {
  font-size: 0.875rem;
  color: var(--color-text-light);
}

.dashboard-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 1rem;
  margin-bottom: 2rem;
}

.dashboard-card {
  background: var(--color-white);
  padding: 1rem;
  border-radius: 12px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  transition: all 0.2s ease;
}

.dashboard-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.card-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1rem;
}

.card-header h3 {
  font-size: 0.875rem;
  font-weight: 600;
  color: var(--color-text-light);
  text-transform: uppercase;
  letter-spacing: 0.05em;
  margin: 0;
}

.card-header svg {
  color: var(--color-primary);
}

.card-value {
  font-size: 1.75rem;
  font-weight: 700;
  color: var(--color-text-dark);
  margin: 0.5rem 0;
}

.card-subtitle {
  font-size: 0.875rem;
  color: var(--color-text-light);
  margin: 0;
}

.charts-section {
  margin-bottom: 2rem;
}

.charts-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 1.5rem;
}

.chart-container {
  background: var(--color-white);
  padding: 1.25rem;
  border-radius: 12px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.chart-container h3 {
  font-size: 1rem;
  font-weight: 700;
  color: var(--color-text-dark);
  margin-bottom: 1rem;
}

.chart-container.full-width {
  grid-column: 1 / -1;
}

.chart-wrapper {
  display: flex;
  justify-content: center;
  max-width: 400px;
  margin: 0 auto;
}

.chart-container canvas {
  max-width: 100%;
  height: auto;
}

.coming-soon {
  background: var(--color-white);
  padding: 1.5rem;
  border-radius: 12px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  margin-bottom: 2rem;
}

.coming-soon h2 {
  font-size: 1.25rem;
  font-weight: 700;
  color: var(--color-text-dark);
  margin-bottom: 1.5rem;
}

.features-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 1rem;
}

.feature-item {
  display: flex;
  flex-direction: column;
  align-items: center;
  text-align: center;
  padding: 1rem;
  background: var(--color-bg-light);
  border-radius: 10px;
  transition: all 0.2s ease;
}

.feature-item:hover {
  background: #eff6ff;
  transform: translateY(-2px);
}

.feature-item svg {
  color: var(--color-primary);
  margin-bottom: 0.75rem;
  width: 28px;
  height: 28px;
}

.feature-item h4 {
  font-size: 0.9rem;
  font-weight: 600;
  color: var(--color-text-dark);
  margin-bottom: 0.25rem;
}

.feature-item p {
  font-size: 0.75rem;
  color: var(--color-text-light);
  margin: 0;
}

/* ===== FOOTER STYLES ===== */
.admin-footer {
  background: var(--color-text-dark);
  color: var(--color-white);
  padding-top: 2rem;
  margin-top: 2rem;
}

.footer-container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 1.5rem;
  display: grid;
  grid-template-columns: 1.5fr 2fr;
  gap: 4rem;
}

.footer-brand .logo {
  display: flex;
  align-items: center;
  gap: 0.625rem;
  margin-bottom: 1rem;
}

.footer-brand .logo-icon {
  display: flex;
  align-items: center;
  justify-content: center;
}

.footer-brand .logo-text {
  display: flex;
  flex-direction: column;
}

.footer-brand .logo-title {
  font-size: 1.25rem;
  font-weight: 700;
  color: var(--color-white);
  line-height: 1.2;
}

.footer-brand .logo-subtitle {
  font-size: 0.625rem;
  font-weight: 600;
  letter-spacing: 0.1em;
  color: rgba(255, 255, 255, 0.6);
}

.footer-desc {
  font-size: 0.9375rem;
  color: rgba(255, 255, 255, 0.7);
  line-height: 1.6;
  margin: 0;
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
  margin-top: 0;
}

.footer-col a {
  display: block;
  font-size: 0.9375rem;
  color: rgba(255, 255, 255, 0.7);
  margin-bottom: 0.75rem;
  text-decoration: none;
  transition: color 0.2s ease;
}

.footer-col a:hover {
  color: #09D8E3;
}

.footer-bottom {
  margin-top: 2rem;
  padding: 1.5rem;
  border-top: 1px solid rgba(255, 255, 255, 0.1);
  text-align: center;
}

.footer-bottom p {
  font-size: 0.875rem;
  color: rgba(255, 255, 255, 0.5);
  margin: 0;
}

/* Responsive Footer */
@media (max-width: 768px) {
  .footer-container {
    grid-template-columns: 1fr;
    gap: 2rem;
  }

  .footer-links {
    grid-template-columns: repeat(2, 1fr);
  }
}

@media (max-width: 480px) {
  .footer-container {
    padding: 0 1rem;
  }

  .footer-links {
    grid-template-columns: 1fr;
  }
}

/* Responsive */
@media (max-width: 1024px) {
  .admin-login-container {
    grid-template-columns: 1fr;
  }

  .admin-branding {
    display: none;
  }

  .dashboard-grid {
    grid-template-columns: repeat(2, 1fr);
  }

  .features-grid {
    grid-template-columns: repeat(2, 1fr);
  }

  .charts-grid {
    grid-template-columns: 1fr;
  }

  .chart-container.full-width {
    grid-column: 1;
  }
}

@media (max-width: 768px) {
  .header-container {
    flex-direction: column;
    gap: 1rem;
  }

  .admin-info {
    width: 100%;
    justify-content: space-between;
  }

  .dashboard-grid {
    grid-template-columns: 1fr;
  }

  .features-grid {
    grid-template-columns: 1fr;
  }

  .form-wrapper {
    max-width: 100%;
  }
}

@media (max-width: 480px) {
  .dashboard-content {
    padding: 1.5rem 1rem;
  }

  .welcome-section h1 {
    font-size: 1.5rem;
  }

  .dashboard-card {
    padding: 1rem;
  }

  .card-value {
    font-size: 1.5rem;
  }
}

</style>
