import { api } from './useApi'

export function useProducts() {
  const getProducts = async (params = {}) => {
    const queryString = new URLSearchParams(params).toString()
    const data = await api.get(`/product?${queryString}`)
    return data
  }

  const getProduct = async (id) => {
    return await api.get(`/product/${id}`)
  }

  const createProduct = async (productData) => {
    return await api.post('/product', productData)
  }

  const updateProduct = async (id, productData) => {
    return await api.put(`/product/${id}`, productData)
  }

  const deleteProduct = async (id) => {
    return await api.delete(`/product/${id}`)
  }

  return {
    getProducts,
    getProduct,
    createProduct,
    updateProduct,
    deleteProduct
  }
}
