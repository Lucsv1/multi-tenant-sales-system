import { api } from './useApi'

export function useCustomers() {
  const getCustomers = async (params = {}) => {
    const queryString = new URLSearchParams(params).toString()
    const data = await api.get(`/customer?${queryString}`)
    return data
  }

  const getCustomer = async (id) => {
    return await api.get(`/customer/${id}`)
  }

  const createCustomer = async (customerData) => {
    return await api.post('/customer', customerData)
  }

  const updateCustomer = async (id, customerData) => {
    return await api.put(`/customer/${id}`, customerData)
  }

  const deleteCustomer = async (id) => {
    return await api.delete(`/customer/${id}`)
  }

  return {
    getCustomers,
    getCustomer,
    createCustomer,
    updateCustomer,
    deleteCustomer
  }
}
