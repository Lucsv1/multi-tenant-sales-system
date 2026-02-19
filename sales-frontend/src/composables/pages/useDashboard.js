import { ref, computed } from 'vue'
import { useSales } from '../useSales'
import { useAuth } from '../useAuth'
import { useQuasar } from 'quasar'
import { api } from '../useApi'

export function useDashboard() {
  const $q = useQuasar()
  const { getSales, sendReportEmail } = useSales()
  const { user, hasRole } = useAuth()

  const isAdmin = computed(() => hasRole('Admin') || user.value?.is_super_admin)

  const stats = ref({
    todaySales: 0,
    todayRevenue: 0,
    totalCustomers: 0,
    totalProducts: 0,
    totalUsers: 0,
    salesThisMonth: 0
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
      const dashboardStats = await api.get('/dashboard/stats')

      stats.value = {
        todaySales: dashboardStats.sales_today || 0,
        todayRevenue: dashboardStats.sales_today || 0,
        totalCustomers: dashboardStats.total_customers || 0,
        totalProducts: dashboardStats.total_products || 0,
        totalUsers: dashboardStats.total_users || 0,
        salesThisMonth: dashboardStats.sales_this_month || 0
      }

      const salesResponse = await getSales({ per_page: 5 })
      const salesData = salesResponse.data || salesResponse
      recentSales.value = (salesData.data || salesData).slice(0, 5)
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
