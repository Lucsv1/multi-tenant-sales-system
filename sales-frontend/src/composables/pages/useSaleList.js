import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useQuasar } from 'quasar'
import { useSales } from '../useSales'
import { useAuth } from '../useAuth'

export function useSaleList() {
  const router = useRouter()
  const $q = useQuasar()
  const { getSales, cancelSale, sendReportEmail } = useSales()
  const { user } = useAuth()

  const sales = ref([])
  const loading = ref(false)
  const showReportDialog = ref(false)
  const reportEmail = ref('')
  const reportLoading = ref(false)

  const filters = ref({
    status: null,
    date_from: '',
    date_to: ''
  })

  const pagination = ref({
    page: 1,
    rowsPerPage: 10,
    rowsNumber: 0
  })

  const statusOptions = [
    { label: 'Concluída', value: 'completed' },
    { label: 'Pendente', value: 'pending' },
    { label: 'Cancelada', value: 'cancelled' }
  ]

  const columns = [
    { name: 'saleNumber', label: 'Venda', align: 'left', field: 'saleNumber' },
    { name: 'customer', label: 'Cliente', align: 'left', field: 'customer' },
    { name: 'paymentMethod', label: 'Pagamento', align: 'left', field: 'paymentMethod' },
    { name: 'total', label: 'Total', align: 'left', field: 'total' },
    { name: 'status', label: 'Status', align: 'center', field: 'status' },
    { name: 'actions', label: 'Ações', align: 'center' }
  ]

  const formatCurrency = (value) => {
    return Number(value || 0).toFixed(2).replace('.', ',')
  }

  const formatDate = (date) => {
    if (!date) return '-'
    return new Date(date).toLocaleDateString('pt-BR')
  }

  const getStatusColor = (status) => {
    const colors = { completed: 'positive', pending: 'warning', cancelled: 'negative' }
    return colors[status] || 'grey'
  }

  const getStatusLabel = (status) => {
    const labels = { completed: 'Concluída', pending: 'Pendente', cancelled: 'Cancelada' }
    return labels[status] || status
  }

  const getPaymentMethodLabel = (method) => {
    const labels = {
      cash: 'Dinheiro',
      credit_card: 'Cartão de Crédito',
      debit_card: 'Cartão de Débito',
      pix: 'PIX',
      transfer: 'Transferência'
    }
    return labels[method] || method
  }

  const loadSales = async () => {
    loading.value = true
    try {
      const params = {
        page: pagination.value.page,
        per_page: pagination.value.rowsPerPage,
        ...filters.value
      }
      const response = await getSales(params)
      const data = response.data || response
      sales.value = data.data || data
      pagination.value.rowsNumber = data.total || (data.data ? data.data.length : sales.value.length)
    } catch (error) {
      $q.notify({ color: 'negative', message: 'Erro ao carregar vendas' })
    } finally {
      loading.value = false
    }
  }

  const onRequest = (props) => {
    pagination.value.page = props.pagination.page
    pagination.value.rowsPerPage = props.pagination.rowsPerPage
    loadSales()
  }

  const applyFilters = () => {
    pagination.value.page = 1
    loadSales()
  }

  const clearFilters = () => {
    filters.value = { date_from: '', date_to: '', status: null }
    applyFilters()
  }

  const viewSale = (sale) => {
    router.push(`/sales/${sale.id}`)
  }

  const createSale = () => {
    router.push('/sales/create')
  }

  const confirmCancel = (sale) => {
    $q.dialog({
      title: 'Cancelar Venda',
      message: `Deseja realmente cancelar a venda "${sale.saleNumber}"?`,
      cancel: true,
      persistent: true
    }).onOk(async () => {
      try {
        await cancelSale(sale.id)
        $q.notify({ color: 'positive', message: 'Venda cancelada com sucesso' })
        loadSales()
      } catch (error) {
        $q.notify({ color: 'negative', message: 'Erro ao cancelar venda' })
      }
    })
  }

  const generateReport = async () => {
    reportLoading.value = true
    try {
      const params = { ...filters.value }
      if (reportEmail.value) {
        params.email = reportEmail.value
      }
      await sendReportEmail(params)
      $q.notify({ color: 'positive', message: 'Relatório será enviado por email' })
      showReportDialog.value = false
    } catch (error) {
      $q.notify({ color: 'negative', message: 'Erro ao gerar relatório' })
    } finally {
      reportLoading.value = false
    }
  }

  const setupSaleList = () => {
    onMounted(() => {
      reportEmail.value = user.value?.email || ''
      loadSales()
    })
  }

  return {
    sales,
    loading,
    showReportDialog,
    reportEmail,
    reportLoading,
    filters,
    pagination,
    statusOptions,
    columns,
    formatCurrency,
    formatDate,
    getStatusColor,
    getStatusLabel,
    getPaymentMethodLabel,
    loadSales,
    onRequest,
    applyFilters,
    clearFilters,
    viewSale,
    createSale,
    confirmCancel,
    generateReport,
    setupSaleList,
    user
  }
}
