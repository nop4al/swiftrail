import axios from 'axios'

// Setup base URL - gunakan environment variable atau default /api/v1
const apiBaseURL = import.meta.env.VITE_API_URL || '/api/v1'

// Create axios instance
const axiosInstance = axios.create({
  baseURL: apiBaseURL,
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json'
  },
  timeout: 30000 // 30 seconds timeout
})

// Request interceptor - tambah token jika ada
axiosInstance.interceptors.request.use(
  (config) => {
    const token = localStorage.getItem('auth_token')
    if (token) {
      config.headers.Authorization = `Bearer ${token}`
    }
    return config
  },
  (error) => {
    return Promise.reject(error)
  }
)

// Response interceptor - handle errors globally
axiosInstance.interceptors.response.use(
  (response) => {
    return response
  },
  (error) => {
    // Handle 401 Unauthorized
    if (error.response?.status === 401) {
      localStorage.removeItem('auth_token')
      localStorage.removeItem('user')
      window.location.href = '/login'
      return Promise.reject(new Error('Sesi Anda telah berakhir. Silakan login kembali.'))
    }

    // Handle 403 Forbidden
    if (error.response?.status === 403) {
      return Promise.reject(new Error('Anda tidak memiliki izin untuk mengakses resource ini.'))
    }

    // Handle 404 Not Found
    if (error.response?.status === 404) {
      return Promise.reject(new Error('Resource tidak ditemukan.'))
    }

    // Handle 409 Conflict (duplikasi kursi misalnya)
    if (error.response?.status === 409) {
      return Promise.reject(error.response.data || new Error('Data konflik terjadi.'))
    }

    // Handle 422 Validation Error
    if (error.response?.status === 422) {
      return Promise.reject(error.response.data || new Error('Data validasi gagal.'))
    }

    // Handle 500+ Server Error
    if (error.response?.status >= 500) {
      return Promise.reject(new Error('Kesalahan server terjadi. Silakan coba lagi nanti.'))
    }

    // Handle network error (no response from server)
    if (!error.response) {
      if (error.code === 'ECONNABORTED') {
        return Promise.reject(new Error('Permintaan timeout. Silakan coba lagi.'))
      }
      return Promise.reject(new Error('Gagal terhubung ke server. Periksa koneksi internet Anda.'))
    }

    return Promise.reject(error)
  }
)

export default axiosInstance
