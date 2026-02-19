import { api } from './useApi'

export function useUsers() {
  const getUsers = async (params = {}) => {
    const filteredParams = Object.fromEntries(
      Object.entries(params).filter(([_, v]) => v !== null && v !== '' && v !== 'null')
    )
    const queryString = new URLSearchParams(filteredParams).toString()
    const data = await api.get(`/user?${queryString}`)
    return data
  }

  const getUser = async (id) => {
    return await api.get(`/user/${id}`)
  }

  const createUser = async (data) => {
    return await api.post('/user', data)
  }

  const updateUser = async (id, data) => {
    return await api.put(`/user/${id}`, data)
  }

  const deleteUser = async (id) => {
    return await api.delete(`/user/${id}`)
  }

  return {
    getUsers,
    getUser,
    createUser,
    updateUser,
    deleteUser
  }
}
