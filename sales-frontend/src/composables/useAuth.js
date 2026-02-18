import { api } from './useApi'
import { ref } from 'vue'

const user = ref(null)
const isAuthenticated = ref(false)

export function useAuth() {
  const login = async (email, password) => {
    const data = await api.post('/auth/login', { email, password })
    localStorage.setItem('token', data.token || data.access_token)
    localStorage.setItem('user', JSON.stringify(data.user))
    user.value = data.user
    isAuthenticated.value = true
    return data
  }

  const logout = async () => {
    try {
      await api.post('/auth/logout')
    } catch (e) {
      console.log('Logout API error:', e.message)
    } finally {
      localStorage.removeItem('token')
      localStorage.removeItem('user')
      user.value = null
      isAuthenticated.value = false
    }
  }

  const getUser = () => {
    const userData = localStorage.getItem('user')
    if (userData) {
      user.value = JSON.parse(userData)
      isAuthenticated.value = true
    }
    return user.value
  }

  const hasRole = (role) => {
    const userData = getUser()
    if (!userData) return false
    
    if (role === 'SuperAdmin') {
      return userData.is_super_admin === true || userData.is_super_admin === 1
    }
    
    if (role === 'Admin') {
      const roleNames = userData.roles?.map(r => r.name) || []
      return roleNames.includes('Admin') || userData.is_super_admin
    }
    
    const roleNames = userData.roles?.map(r => r.name) || []
    return roleNames.includes(role)
  }

  const canAccess = (roles = []) => {
    const userData = getUser()
    if (!userData) return false
    
    if (userData.is_super_admin) return true
    
    return roles.some(role => hasRole(role))
  }

  return {
    user,
    isAuthenticated,
    login,
    logout,
    getUser,
    hasRole,
    canAccess
  }
}
