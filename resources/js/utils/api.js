// API Base URL
const API_BASE = '/api/v1'

/**
 * Fetch promos dari backend
 */
export const fetchPromos = async () => {
  try {
    console.log('Fetching promos from:', `${API_BASE}/promos`)
    const response = await fetch(`${API_BASE}/promos`)
    console.log('Promos response status:', response.status)
    
    if (!response.ok) {
      console.error('Failed to fetch promos - Status:', response.status)
      throw new Error(`HTTP ${response.status}: Failed to fetch promos`)
    }
    
    const result = await response.json()
    console.log('Promos result:', result)
    
    // Return data array jika ada, atau empty array
    return result.data || result || []
  } catch (error) {
    console.error('Error fetching promos:', error)
    return []
  }
}

/**
 * Fetch destinasi dari backend
 */
export const fetchDestinations = async () => {
  try {
    console.log('Fetching destinations from:', `${API_BASE}/destinations`)
    const response = await fetch(`${API_BASE}/destinations`)
    console.log('Destinations response status:', response.status)
    
    if (!response.ok) {
      console.error('Failed to fetch destinations - Status:', response.status)
      throw new Error(`HTTP ${response.status}: Failed to fetch destinations`)
    }
    
    const result = await response.json()
    console.log('Destinations result:', result)
    
    // Return data array jika ada, atau empty array
    return result.data || result || []
  } catch (error) {
    console.error('Error fetching destinations:', error)
    return []
  }
}

/**
 * Fetch stasiun dari backend
 */
export const fetchStations = async () => {
  try {
    const response = await fetch(`${API_BASE}/stations`)
    if (!response.ok) throw new Error('Failed to fetch stations')
    
    const result = await response.json()
    return result.data || result || []
  } catch (error) {
    console.error('Error fetching stations:', error)
    return []
  }
}

/**
 * Cari kereta
 */
export const searchTrains = async (params) => {
  try {
    const response = await fetch(`${API_BASE}/trains/search`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({
        from_station: params.fromStation,
        to_station: params.toStation,
        travel_date: params.travelDate,
        trip_type: params.activeTab === 'one-way' ? 'one-way' : 'round-trip'
      })
    })
    
    if (!response.ok) throw new Error('Failed to search trains')
    return await response.json()
  } catch (error) {
    console.error('Error searching trains:', error)
    return {
      success: false,
      results: [],
      error: error.message
    }
  }
}

/**
 * Get detail kereta
 */
export const getTrainDetail = async (id) => {
  try {
    const response = await fetch(`${API_BASE}/trains/${id}`)
    if (!response.ok) throw new Error('Failed to fetch train detail')
    return await response.json()
  } catch (error) {
    console.error('Error fetching train detail:', error)
    return null
  }
}

/**
 * Get app info
 */
export const fetchAppInfo = async () => {
  try {
    const response = await fetch(`${API_BASE}/info`)
    if (!response.ok) throw new Error('Failed to fetch app info')
    return await response.json()
  } catch (error) {
    console.error('Error fetching app info:', error)
    return {}
  }
}

/**
 * Format tanggal
 */
export const formatDate = (dateString) => {
  const date = new Date(dateString)
  return date.toLocaleDateString('id-ID', { 
    month: 'long', 
    day: 'numeric', 
    year: 'numeric'
  })
}

/**
 * Format harga ke Rupiah
 */
export const formatPrice = (price) => {
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    minimumFractionDigits: 0
  }).format(price)
}

/**
 * Swap values
 */
export const swapValues = (value1, value2) => {
  return { from: value2, to: value1 }
}
