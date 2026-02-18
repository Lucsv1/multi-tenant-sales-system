import { ref, computed } from 'vue'
import { useSales } from '../useSales'
import { useCustomers } from '../useCustomers'
import { useProducts } from '../useProducts'
import { useAuth } from '../useAuth'
import { useQuasar } from 'quasar'

export function useDashboard() {
  const $q = useQuasar()
  const { getSales, sendReportEmail } = useSales()
  const { getCustomers } = useCustomers()
  const { getProducts } = useProducts()
  const { user, hasRole } = useAuth()

  const isAdmin = computed(() => hasRole('Admin') || user.value?.is_super_admin)

  const stats = ref({
    todaySales: 0,
    todayRevenue: 0,
    totalCustomers: 0,
    totalProducts: 0
  })

  const recentSales = ref([])
  const showReportDialog = ref(false)
  const reportEmail = ref('')
  const reportLoading = ref(false)

  const formatCurrency = (value) => {
    return Number(value || 0).toFixed(2).replace('.', ',')
  }

  const formatDate = (date) => {
    if (!date) return '-'
    return new Date(date).toLocaleDateString('pt-BR', {
      day: '2-digit',
      month: '2-digit',
      year: 'numeric',
      hour: '2-digit',
      minute: '2-digit'
    })
  }

  const getStatusColor = (status) => {
    const colors = {
      completed: 'positive',
      pending: 'warning',
      cancelled: 'negative'
    }
    return colors[status] || 'grey'
  }

  const getStatusLabel = (status) => {
    const labels = {
      completed: 'Concluída',
      pending: 'Pendente',
      cancelled: 'Cancelada'
    }
    return labels[status] || status
  }

  const loadDashboard = async () => {
    try {
      const salesResponse = await getSales({ per_page: 100 })
      const salesData = salesResponse.data || salesResponse
      const sales = salesData.data || salesData
      
      const today = new Date().toISOString().split('T')[0]
      const todaySales = sales.filter(s => s.saleDate && s.saleDate.startsWith(today))

      const statsData = {
        todaySales: todaySales.length,
        todayRevenue: todaySales.reduce((sum, s) => sum + Number(s.total), 0),
        totalCustomers: 0,
        totalProducts: 0
      }

      const customersResponse = await getCustomers({ per_page: 100 })
      statsData.totalCustomers = customersResponse.total || customersResponse.data?.length || 0

      if (isAdmin.value) {
        const productsResponse = await getProducts({ per_page: 100 })
        statsData.totalProducts = productsResponse.total || productsResponse.data?.length || 0
      }

      stats.value = statsData
      recentSales.value = sales.slice(0, 5)
    } catch (error) {
      console.error('Erro ao carregar dashboard:', error)
    }
  }

  const generateReport = async () => {
    reportLoading.value = true
    try {
      const params = {}
      if (reportEmail.value) {
        params.email = reportEmail.value
      }
      await sendReportEmail(params)
      $q.notify({
        color: 'positive',
        message: 'Relatório sendo gerado e será enviado por email',
        icon: 'check'
      })
      showReportDialog.value = false
      reportEmail.value = ''
    } catch (error) {
      $q.notify({
        color: 'negative',
        message: error.message || 'Erro ao gerar relatório'
      })
    } finally {
      reportLoading.value = false
    }
  }

  const setupDashboard = () => {
    reportEmail.value = user.value?.email || ''
    loadDashboard()
  }

  return {
    isAdmin,
    stats,
    recentSales,
    showReportDialog,
    reportEmail,
    reportLoading,
    formatCurrency,
    formatDate,
    getStatusColor,
    getStatusLabel,
    loadDashboard,
    generateReport,
    setupDashboard
  }
}
