import { defineRouter } from '#q-app/wrappers'
import { createRouter, createMemoryHistory, createWebHistory, createWebHashHistory } from 'vue-router'
import { api } from 'src/composables/useApi'
import routes from './routes'

export default defineRouter(function () {
  const createHistory = process.env.SERVER
    ? createMemoryHistory
    : (process.env.VUE_ROUTER_MODE === 'history' ? createWebHistory : createWebHashHistory)

  const Router = createRouter({
    scrollBehavior: () => ({ left: 0, top: 0 }),
    routes,
    history: createHistory(process.env.VUE_ROUTER_BASE)
  })

  const isAuthenticated = () => {
    const token = localStorage.getItem('token')
    return !!token
  }

  Router.beforeEach(async (to, from, next) => {
    const token = localStorage.getItem('token')
    
    if (to.path !== '/login') {
      if (!token) {
        next('/login')
        return
      }
      
      if (to.path === '/login') {
        next('/')
        return
      }
    }
    
    if (to.path === '/login' && token) {
      next('/')
      return
    }
    
    next()
  })

  return Router
})
