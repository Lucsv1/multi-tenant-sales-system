import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useQuasar } from 'quasar'
import { useSales } from '../useSales'

export function useSaleDetail() {
  const route = useRoute()
  const router = useRouter()
  const $q = useQuasar()
  const { getSale } = useSales()

  const sale = ref(null)
  const loading = ref(true)

  const formatCurrency = (value) => {
    return Number(value || 0).toFixed(2).replace('.', ',')
  }

  const formatDate = (date) => {
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

  const loadSale = async () => {
    loading.value = true
    try {
      const response = await getSale(route.params.id)
      sale.value = response.data || response
    } catch (error) {
      $q.notify({ color: 'negative', message: 'Erro ao carregar venda' })
      router.back()
    } finally {
      loading.value = false
    }
  }

  const setupSaleDetail = () => {
    onMounted(() => {
      loadSale()
    })
  }

  return {
    sale,
    loading,
    formatCurrency,
    formatDate,
    getStatusColor,
    getStatusLabel,
    getPaymentMethodLabel,
    loadSale,
    setupSaleDetail
  }
}
