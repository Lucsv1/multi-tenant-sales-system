import { api } from './useApi'

export function useSales() {
  const getSales = async (params = {}) => {
    const filteredParams = Object.fromEntries(
      Object.entries(params).filter(([_, v]) => v !== null && v !== '' && v !== 'null')
    )
    const queryString = new URLSearchParams(filteredParams).toString()
    const data = await api.get(`/sale?${queryString}`)
    return data
  }

  const getSale = async (id) => {
    return await api.get(`/sale/${id}`)
  }

  const createSale = async (saleData) => {
    return await api.post('/sale', saleData)
  }

  const cancelSale = async (id) => {
    return await api.delete(`/sale/${id}`)
  }

  const getReport = async (params = {}) => {
    const queryString = new URLSearchParams(params).toString()
    return await api.get(`/sale/report?${queryString}`)
  }

  const sendReportEmail = async (params = {}) => {
    const queryString = new URLSearchParams(params).toString()
    return await api.get(`/sale/report?send_email=true&${queryString}`)
  }

  return {
    getSales,
    getSale,
    createSale,
    cancelSale,
    getReport,
    sendReportEmail
  }
}
