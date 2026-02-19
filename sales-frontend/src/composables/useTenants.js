import { api } from './useApi'

export function useTenants() {
  const getTenants = async (params = {}) => {
    const filteredParams = Object.fromEntries(
      Object.entries(params).filter(([_, v]) => v !== null && v !== '' && v !== 'null')
    )
    const queryString = new URLSearchParams(filteredParams).toString()
    const data = await api.get(`/tenant?${queryString}`)
    return data
  }

  const getTenant = async (id) => {
    return await api.get(`/tenant/${id}`)
  }

  const createTenant = async (data) => {
    return await api.post('/tenant', data)
  }

  const updateTenant = async (id, data) => {
    return await api.put(`/tenant/${id}`, data)
  }

  const deleteTenant = async (id) => {
    return await api.delete(`/tenant/${id}`)
  }

  return {
    getTenants,
    getTenant,
    createTenant,
    updateTenant,
    deleteTenant
  }
}
