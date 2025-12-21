// User Refund API Service
const API_BASE_URL = '/api/v1'

export const refundApi = {
  // Get user's tickets
  async getUserTickets() {
    const response = await fetch(`${API_BASE_URL}/user/tickets`, {
      headers: { 'Authorization': `Bearer ${localStorage.getItem('auth_token')}` }
    })
    if (!response.ok) {
      throw new Error(`HTTP ${response.status}: ${response.statusText}`)
    }
    return await response.json()
  },

  // Get user's SwiftPay wallets
  async getUserWallets() {
    const response = await fetch(`${API_BASE_URL}/user/swift-pay-wallets`, {
      headers: { 'Authorization': `Bearer ${localStorage.getItem('auth_token')}` }
    })
    if (!response.ok) {
      throw new Error(`HTTP ${response.status}: ${response.statusText}`)
    }
    return await response.json()
  },

  // Request a refund
  async requestRefund(data) {
    const response = await fetch(`${API_BASE_URL}/refunds`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Authorization': `Bearer ${localStorage.getItem('auth_token')}`
      },
      body: JSON.stringify(data)
    })
    if (!response.ok) {
      const error = await response.text()
      throw new Error(`HTTP ${response.status}: ${error}`)
    }
    return await response.json()
  },

  // Get user's refund history
  async getUserRefunds(status = null) {
    let url = `${API_BASE_URL}/refunds`
    if (status) {
      url += `?status=${status}`
    }
    const response = await fetch(url, {
      headers: { 'Authorization': `Bearer ${localStorage.getItem('auth_token')}` }
    })
    if (!response.ok) {
      throw new Error(`HTTP ${response.status}: ${response.statusText}`)
    }
    return await response.json()
  },

  // Get specific refund details
  async getRefund(id) {
    const response = await fetch(`${API_BASE_URL}/refunds/${id}`, {
      headers: { 'Authorization': `Bearer ${localStorage.getItem('auth_token')}` }
    })
    if (!response.ok) {
      throw new Error(`HTTP ${response.status}: ${response.statusText}`)
    }
    return await response.json()
  }
}

export default refundApi
