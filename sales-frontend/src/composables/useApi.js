const API_URL = import.meta.env.VITE_API_URL || 'http://localhost:8000/api'

const getHeaders = () => {
  const token = localStorage.getItem('token')
  return {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
    'Authorization': token ? `Bearer ${token}` : ''
  }
}

export const api = {
  async get(endpoint) {
    const response = await fetch(`${API_URL}${endpoint}`, {
      method: 'GET',
      headers: getHeaders()
    })
    return handleResponse(response)
  },

  async post(endpoint, data = {}) {
    const response = await fetch(`${API_URL}${endpoint}`, {
      method: 'POST',
      headers: getHeaders(),
      body: JSON.stringify(data)
    })
    return handleResponse(response)
  },

  async put(endpoint, data = {}) {
    const response = await fetch(`${API_URL}${endpoint}`, {
      method: 'PUT',
      headers: getHeaders(),
      body: JSON.stringify(data)
    })
    return handleResponse(response)
  },

  async delete(endpoint) {
    const response = await fetch(`${API_URL}${endpoint}`, {
      method: 'DELETE',
      headers: getHeaders()
    })
    return handleResponse(response)
  }
}

class ApiError extends Error {
  constructor(message, response) {
    super(message)
    this.name = 'ApiError'
    this.response = response
  }
}

async function handleResponse(response) {
  const data = await response.json()
  
  if (!response.ok) {
    throw new ApiError(data.message || 'Erro na requisição', {
      status: response.status,
      data: data
    })
  }
  
  return data
}

export default API_URL
