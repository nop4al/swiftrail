export const formatDate = (dateString) => {
  const date = new Date(dateString)
  return date.toLocaleDateString('en-US', { 
    month: '2-digit', 
    day: '2-digit', 
    year: 'numeric' 
  })
}

export const swapValues = (value1, value2) => {
  return { from: value2, to: value1 }
}

export const searchTrains = (params) => {
  console.log('Searching trains:', {
    from: params.fromStation,
    to: params.toStation,
    date: params.travelDate,
    type: params.activeTab
  })
}
