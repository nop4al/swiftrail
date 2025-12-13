<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()
const fullName = ref('')
const email = ref('')
const password = ref('')
const confirmPassword = ref('')
const acceptTerms = ref(false)
const isLoading = ref(false)
const showPassword = ref(false)
const showConfirmPassword = ref(false)

const validateForm = () => {
  if (!fullName.value || !email.value || !password.value || !confirmPassword.value) {
    alert('Silakan isi semua field')
    return false
  }

  if (password.value !== confirmPassword.value) {
    alert('Password tidak cocok')
    return false
  }

  if (password.value.length < 8) {
    alert('Password minimal 8 karakter')
    return false
  }

  if (!acceptTerms.value) {
    alert('Anda harus menerima syarat dan ketentuan')
    return false
  }

  return true
}

const handleRegister = async () => {
  if (!validateForm()) return

  isLoading.value = true
  try {
    // Simulasi API call
    await new Promise(resolve => setTimeout(resolve, 1500))
    console.log('Register:', {
      fullName: fullName.value,
      email: email.value,
      password: password.value
    })
    // Redirect ke login setelah register berhasil
    alert('Registrasi berhasil! Silakan login.')
    router.push('/login')
  } catch (error) {
    console.error('Register error:', error)
  } finally {
    isLoading.value = false
  }
}

const togglePasswordVisibility = () => {
  showPassword.value = !showPassword.value
}

const toggleConfirmPasswordVisibility = () => {
  showConfirmPassword.value = !showConfirmPassword.value
}
</script>

<template>
  <div class="register-page">
    <div class="register-container">
      <!-- Left Section - Form -->
      <div class="register-form-section">
        <div class="form-wrapper">
          <router-link to="/login" class="back-btn">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M15 18L9 12L15 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            Kembali
          </router-link>

          <h1>Buat Akun Baru</h1>
          <p class="form-subtitle">Daftarkan diri Anda untuk memulai perjalanan bersama SwiftRail</p>

          <form @submit.prevent="handleRegister" class="register-form">
            <div class="form-group">
              <label for="fullname" class="form-label">Nama Lengkap</label>
              <div class="input-wrapper">
                <svg class="input-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <circle cx="12" cy="8" r="4" stroke="currentColor" stroke-width="2"/>
                  <path d="M4 20C4 20 6 17 12 17C18 17 20 20 20 20" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                </svg>
                <input
                  id="fullname"
                  v-model="fullName"
                  type="text"
                  class="form-input"
                  placeholder="Masukkan nama lengkap Anda"
                  required
                />
              </div>
            </div>

            <div class="form-group">
              <label for="email" class="form-label">Email</label>
              <div class="input-wrapper">
                <svg class="input-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <rect x="2" y="4" width="20" height="16" rx="2" stroke="currentColor" stroke-width="2"/>
                  <path d="M2 6L12 13L22 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <input
                  id="email"
                  v-model="email"
                  type="email"
                  class="form-input"
                  placeholder="Masukkan email Anda"
                  required
                />
              </div>
            </div>

            <div class="form-group">
              <label for="password" class="form-label">Password</label>
              <div class="input-wrapper">
                <svg class="input-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <rect x="3" y="11" width="18" height="10" rx="2" stroke="currentColor" stroke-width="2"/>
                  <path d="M7 11V7C7 4.23858 9.23858 2 12 2C14.7614 2 17 4.23858 17 7V11" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                </svg>
                <input
                  id="password"
                  v-model="password"
                  :type="showPassword ? 'text' : 'password'"
                  class="form-input"
                  placeholder="Minimal 8 karakter"
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

            <div class="form-group">
              <label for="confirm-password" class="form-label">Konfirmasi Password</label>
              <div class="input-wrapper">
                <svg class="input-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <rect x="3" y="11" width="18" height="10" rx="2" stroke="currentColor" stroke-width="2"/>
                  <path d="M7 11V7C7 4.23858 9.23858 2 12 2C14.7614 2 17 4.23858 17 7V11" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                </svg>
                <input
                  id="confirm-password"
                  v-model="confirmPassword"
                  :type="showConfirmPassword ? 'text' : 'password'"
                  class="form-input"
                  placeholder="Ulangi password Anda"
                  required
                />
                <button
                  type="button"
                  class="toggle-password"
                  @click="toggleConfirmPasswordVisibility"
                >
                  <svg v-if="!showConfirmPassword" width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
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

            <label class="checkbox-group">
              <input v-model="acceptTerms" type="checkbox" />
              <span>Saya setuju dengan <a href="#">Syarat & Ketentuan</a> dan <a href="#">Kebijakan Privasi</a></span>
            </label>

            <button type="submit" class="register-btn" :disabled="isLoading">
              <span v-if="!isLoading">Daftar</span>
              <span v-else>Memproses...</span>
            </button>
          </form>

          <div class="divider">
            <span>atau</span>
          </div>

          <div class="social-login">
            <button class="social-btn google" type="button">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
                <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/>
                <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
              </svg>
              Daftar dengan Google
            </button>
          </div>

          <p class="login-link">
            Sudah punya akun? <router-link to="/login">Masuk di sini</router-link>
          </p>
        </div>
      </div>

      <!-- Right Section - Branding -->
      <div class="register-branding">
        <div class="branding-content">
          <div class="features-list">
            <h2>Mengapa Bergabung dengan SwiftRail?</h2>
            
            <div class="feature-group">
              <div class="feature-item">
                <div class="feature-icon">
                  <svg width="32" height="32" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22Z" stroke="currentColor" stroke-width="2"/>
                    <path d="M12 6V12L16 14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                  </svg>
                </div>
                <div class="feature-text">
                  <h3>Pemesanan Instan</h3>
                  <p>Pesan tiket kereta dalam hitungan detik</p>
                </div>
              </div>

              <div class="feature-item">
                <div class="feature-icon">
                  <svg width="32" height="32" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M9 12L11 14L15 10" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="2"/>
                  </svg>
                </div>
                <div class="feature-text">
                  <h3>Pembayaran Aman</h3>
                  <p>Transaksi terenkripsi dengan teknologi terbaru</p>
                </div>
              </div>

              <div class="feature-item">
                <div class="feature-icon">
                  <svg width="32" height="32" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22Z" stroke="currentColor" stroke-width="2"/>
                    <path d="M7 13L11 17L17 8" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                  </svg>
                </div>
                <div class="feature-text">
                  <h3>E-Tiket Digital</h3>
                  <p>Terima tiket langsung di smartphone Anda</p>
                </div>
              </div>

              <div class="feature-item">
                <div class="feature-icon">
                  <svg width="32" height="32" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M18 18.72C18.93 18.46 19.77 17.96 20.4 17.28C22.31 15.16 22.03 11.98 19.78 10.2" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    <path d="M12 5C12 5 14.5 7.5 14.5 9.5C14.5 10.88 13.38 12 12 12C10.62 12 9.5 10.88 9.5 9.5C9.5 7.5 12 5 12 5Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                  </svg>
                </div>
                <div class="feature-text">
                  <h3>Dukungan 24/7</h3>
                  <p>Tim customer service siap membantu kapan saja</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.register-page {
  width: 100%;
  min-height: 100vh;
  background: var(--color-bg-light);
}

.register-container {
  display: grid;
  grid-template-columns: 1fr 1fr;
  min-height: 100vh;
}

/* Form Section */
.register-form-section {
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

.back-btn {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  color: var(--color-primary);
  text-decoration: none;
  font-size: 0.875rem;
  font-weight: 600;
  margin-bottom: 1.5rem;
  transition: opacity 0.2s;
}

.back-btn:hover {
  opacity: 0.8;
}

.back-btn svg {
  width: 16px;
  height: 16px;
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

.register-form {
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
  transition: all 0.2s;
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
  transition: color 0.2s;
}

.toggle-password:hover {
  color: var(--color-primary);
}

.checkbox-group {
  display: flex;
  align-items: flex-start;
  gap: 0.625rem;
  margin-bottom: 1.5rem;
  font-size: 0.875rem;
  color: var(--color-text-dark);
  line-height: 1.5;
}

.checkbox-group input {
  margin-top: 3px;
  width: 16px;
  height: 16px;
  cursor: pointer;
  accent-color: var(--color-primary);
  flex-shrink: 0;
}

.checkbox-group a {
  color: var(--color-primary);
  text-decoration: none;
  font-weight: 600;
}

.checkbox-group a:hover {
  text-decoration: underline;
}

.register-btn {
  width: 100%;
  padding: 0.875rem;
  font-size: 1rem;
  font-weight: 600;
  color: white;
  background: linear-gradient(135deg, var(--color-primary) 0%, var(--color-secondary) 100%);
  border: none;
  border-radius: 10px;
  transition: all 0.3s;
  box-shadow: 0 4px 15px rgba(22, 117, 231, 0.3);
  cursor: pointer;
}

.register-btn:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 6px 20px rgba(22, 117, 231, 0.4);
}

.register-btn:disabled {
  opacity: 0.7;
  cursor: not-allowed;
}

.divider {
  display: flex;
  align-items: center;
  gap: 1rem;
  margin: 1.5rem 0;
  color: var(--color-text-light);
  font-size: 0.875rem;
}

.divider::before,
.divider::after {
  content: '';
  flex: 1;
  height: 1px;
  background: #e5e7eb;
}

.social-login {
  display: grid;
  grid-template-columns: 1fr;
  gap: 0.875rem;
  margin-bottom: 1.5rem;
}

.social-btn {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.75rem;
  padding: 0.875rem 1rem;
  font-size: 0.9375rem;
  font-weight: 600;
  border: 1.5px solid #e5e7eb;
  border-radius: 10px;
  background: white;
  color: var(--color-text-dark);
  cursor: pointer;
  transition: all 0.2s;
}

.social-btn:hover {
  border-color: var(--color-primary);
  background: var(--color-bg-light);
}

.social-btn.google {
  color: var(--color-text-dark);
}

.login-link {
  text-align: center;
  font-size: 0.9375rem;
  color: var(--color-text-light);
}

.login-link a {
  color: var(--color-primary);
  font-weight: 600;
  text-decoration: none;
  transition: opacity 0.2s;
}

.login-link a:hover {
  opacity: 0.8;
  text-decoration: underline;
}

/* Branding Section */
.register-branding {
  background: linear-gradient(135deg, var(--color-primary-dark) 0%, var(--color-primary) 50%, var(--color-secondary) 100%);
  color: white;
  padding: 3rem;
  display: flex;
  align-items: center;
  justify-content: center;
  position: relative;
  overflow: hidden;
}

.register-branding::before {
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
}

.features-list h2 {
  font-size: 1.875rem;
  font-weight: 700;
  margin-bottom: 2rem;
  line-height: 1.3;
}

.feature-group {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

.feature-item {
  display: flex;
  gap: 1rem;
}

.feature-icon {
  flex-shrink: 0;
  display: flex;
  align-items: center;
  justify-content: center;
  width: 48px;
  height: 48px;
  background: rgba(255, 255, 255, 0.15);
  border-radius: 12px;
  color: white;
}

.feature-text h3 {
  font-size: 0.95rem;
  font-weight: 700;
  margin-bottom: 0.25rem;
}

.feature-text p {
  font-size: 0.85rem;
  opacity: 0.95;
  line-height: 1.4;
}

/* Responsive */
@media (max-width: 768px) {
  .register-container {
    grid-template-columns: 1fr;
  }

  .register-branding {
    display: none;
  }

  .register-form-section {
    padding: 2rem 1.5rem;
    min-height: 100vh;
  }

  .form-wrapper {
    max-width: 100%;
  }

  .form-wrapper h1 {
    font-size: 1.5rem;
  }
}

@media (max-width: 480px) {
  .register-form-section {
    padding: 1.5rem 1rem;
  }

  .form-wrapper h1 {
    font-size: 1.25rem;
  }

  .form-wrapper p {
    font-size: 0.875rem;
  }

  .social-login {
    gap: 0.625rem;
  }

  .checkbox-group {
    font-size: 0.8125rem;
  }
}

/* Register Animations */
.register-container {
  animation: fadeInUp 0.6s ease-out;
}

.register-form-section {
  animation: slideInLeft 0.8s ease-out;
}

.register-branding {
  animation: slideInRight 0.8s ease-out;
}

.form-wrapper h1 {
  animation: fadeInUp 0.8s ease-out 0.2s both;
}

.form-subtitle {
  animation: fadeInUp 0.8s ease-out 0.3s both;
}

.register-form {
  animation: fadeInUp 0.8s ease-out 0.4s both;
}

.register-btn {
  transition: all 0.3s ease;
}

.register-btn:hover:not(:disabled) {
  animation: bounce 0.6s ease-out;
}

.form-input {
  transition: all 0.3s ease;
}

.form-input:focus {
  animation: scaleIn 0.3s ease-out;
}

.social-btn {
  transition: all 0.3s ease;
  animation: fadeInUp 0.8s ease-out 0.5s both;
}

.social-btn:hover {
  animation: bounce 0.4s ease-out;
}

.feature-item {
  animation: fadeInUp 0.8s ease-out;
  animation-timeline: view();
  animation-range: entry 0% cover 30%;
}

.back-btn {
  animation: fadeInDown 0.6s ease-out;
  transition: opacity 0.3s ease;
}

.back-btn:hover {
  animation: bounce 0.4s ease-out;
}

.checkbox-group {
  animation: fadeInUp 0.8s ease-out 0.5s both;
}

@keyframes fadeInUp {
  from {
    opacity: 0;
    transform: translateY(30px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

@keyframes slideInLeft {
  from {
    opacity: 0;
    transform: translateX(-50px);
  }
  to {
    opacity: 1;
    transform: translateX(0);
  }
}

@keyframes slideInRight {
  from {
    opacity: 0;
    transform: translateX(50px);
  }
  to {
    opacity: 1;
    transform: translateX(0);
  }
}

@keyframes fadeInDown {
  from {
    opacity: 0;
    transform: translateY(-30px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

@keyframes scaleIn {
  from {
    opacity: 0;
    transform: scale(0.95);
  }
  to {
    opacity: 1;
    transform: scale(1);
  }
}

@keyframes bounce {
  0%, 100% {
    transform: translateY(0);
  }
  50% {
    transform: translateY(-5px);
  }
}
</style>
