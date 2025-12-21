// Admin API Service
const API_BASE_URL = '/api/v1/admin'

export const adminApi = {
  // STATIONS
  async getStations() {
    const response = await fetch(`${API_BASE_URL}/stations`, {
      headers: { 'Authorization': `Bearer ${localStorage.getItem('auth_token')}` }
    })
    return await response.json()
  },

  async getStation(id) {
    const response = await fetch(`${API_BASE_URL}/stations/${id}`, {
      headers: { 'Authorization': `Bearer ${localStorage.getItem('auth_token')}` }
    })
    return await response.json()
  },

  async createStation(data) {
    const response = await fetch(`${API_BASE_URL}/stations`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Authorization': `Bearer ${localStorage.getItem('auth_token')}`
      },
      body: JSON.stringify(data)
    })
    return await response.json()
  },

  async updateStation(id, data) {
    const response = await fetch(`${API_BASE_URL}/stations/${id}`, {
      method: 'PUT',
      headers: {
        'Content-Type': 'application/json',
        'Authorization': `Bearer ${localStorage.getItem('auth_token')}`
      },
      body: JSON.stringify(data)
    })
    return await response.json()
  },

  async deleteStation(id) {
    const response = await fetch(`${API_BASE_URL}/stations/${id}`, {
      method: 'DELETE',
      headers: { 'Authorization': `Bearer ${localStorage.getItem('auth_token')}` }
    })
    return await response.json()
  },

  // TRAINS
  async getTrains() {
    const response = await fetch(`${API_BASE_URL}/trains`, {
      headers: { 'Authorization': `Bearer ${localStorage.getItem('auth_token')}` }
    })
    return await response.json()
  },

  async getTrain(id) {
    const response = await fetch(`${API_BASE_URL}/trains/${id}`, {
      headers: { 'Authorization': `Bearer ${localStorage.getItem('auth_token')}` }
    })
    return await response.json()
  },

  async createTrain(data) {
    const response = await fetch(`${API_BASE_URL}/trains`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Authorization': `Bearer ${localStorage.getItem('auth_token')}`
      },
      body: JSON.stringify(data)
    })
    return await response.json()
  },

  async updateTrain(id, data) {
    const response = await fetch(`${API_BASE_URL}/trains/${id}`, {
      method: 'PUT',
      headers: {
        'Content-Type': 'application/json',
        'Authorization': `Bearer ${localStorage.getItem('auth_token')}`
      },
      body: JSON.stringify(data)
    })
    return await response.json()
  },

  async deleteTrain(id) {
    const response = await fetch(`${API_BASE_URL}/trains/${id}`, {
      method: 'DELETE',
      headers: { 'Authorization': `Bearer ${localStorage.getItem('auth_token')}` }
    })
    return await response.json()
  },

  // ROUTES
  async getRoutes() {
    const response = await fetch(`${API_BASE_URL}/routes`, {
      headers: { 'Authorization': `Bearer ${localStorage.getItem('auth_token')}` }
    })
    return await response.json()
  },

  async getRoute(id) {
    const response = await fetch(`${API_BASE_URL}/routes/${id}`, {
      headers: { 'Authorization': `Bearer ${localStorage.getItem('auth_token')}` }
    })
    return await response.json()
  },

  async createRoute(data) {
    const response = await fetch(`${API_BASE_URL}/routes`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Authorization': `Bearer ${localStorage.getItem('auth_token')}`
      },
      body: JSON.stringify(data)
    })
    return await response.json()
  },

  async updateRoute(id, data) {
    const response = await fetch(`${API_BASE_URL}/routes/${id}`, {
      method: 'PUT',
      headers: {
        'Content-Type': 'application/json',
        'Authorization': `Bearer ${localStorage.getItem('auth_token')}`
      },
      body: JSON.stringify(data)
    })
    return await response.json()
  },

  async deleteRoute(id) {
    const response = await fetch(`${API_BASE_URL}/routes/${id}`, {
      method: 'DELETE',
      headers: { 'Authorization': `Bearer ${localStorage.getItem('auth_token')}` }
    })
    return await response.json()
  },

  // SCHEDULES
  async getSchedules() {
    const response = await fetch(`${API_BASE_URL}/schedules`, {
      headers: { 'Authorization': `Bearer ${localStorage.getItem('auth_token')}` }
    })
    return await response.json()
  },

  async getSchedule(id) {
    const response = await fetch(`${API_BASE_URL}/schedules/${id}`, {
      headers: { 'Authorization': `Bearer ${localStorage.getItem('auth_token')}` }
    })
    return await response.json()
  },

  async createSchedule(data) {
    const response = await fetch(`${API_BASE_URL}/schedules`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Authorization': `Bearer ${localStorage.getItem('auth_token')}`
      },
      body: JSON.stringify(data)
    })
    return await response.json()
  },

  async updateSchedule(id, data) {
    const response = await fetch(`${API_BASE_URL}/schedules/${id}`, {
      method: 'PUT',
      headers: {
        'Content-Type': 'application/json',
        'Authorization': `Bearer ${localStorage.getItem('auth_token')}`
      },
      body: JSON.stringify(data)
    })
    return await response.json()
  },

  async deleteSchedule(id) {
    const response = await fetch(`${API_BASE_URL}/schedules/${id}`, {
      method: 'DELETE',
      headers: { 'Authorization': `Bearer ${localStorage.getItem('auth_token')}` }
    })
    return await response.json()
  },

  // REFUNDS
  async getRefunds() {
    const response = await fetch(`${API_BASE_URL}/refunds`, {
      headers: { 'Authorization': `Bearer ${localStorage.getItem('auth_token')}` }
    })
    return await response.json()
  },

  async updateRefundStatus(id, status) {
    const response = await fetch(`${API_BASE_URL}/refunds/${id}/status`, {
      method: 'PUT',
      headers: {
        'Content-Type': 'application/json',
        'Authorization': `Bearer ${localStorage.getItem('auth_token')}`
      },
      body: JSON.stringify({ status })
    })
    return await response.json()
  },

  // DASHBOARD
  async getDashboardStats() {
    const response = await fetch(`${API_BASE_URL}/stats`, {
      headers: { 'Authorization': `Bearer ${localStorage.getItem('auth_token')}` }
    })
    return await response.json()
  }
}

export default adminApi
