import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useQuasar } from 'quasar'
import { useCustomers } from '../useCustomers'

export function useCustomerList() {
  const router = useRouter()
  const $q = useQuasar()
  const { getCustomers, deleteCustomer } = useCustomers()

  const customers = ref([])
  const loading = ref(false)

  const pagination = ref({
    page: 1,
    rowsPerPage: 10,
    rowsNumber: 0
  })

  const columns = [
    { name: 'name', label: 'Nome', align: 'left', field: 'name', sortable: true },
    { name: 'phone', label: 'Telefone', align: 'left', field: 'phone' },
    { name: 'cpf_cnpj', label: 'CPF/CNPJ', align: 'left', field: 'cpf_cnpj' },
    { name: 'city', label: 'Cidade', align: 'left', field: 'city' },
    { name: 'isActive', label: 'Status', align: 'center', field: 'isActive' },
    { name: 'actions', label: 'Ações', align: 'center' }
  ]

  const loadCustomers = async () => {
    loading.value = true
    try {
      const response = await getCustomers({
        page: pagination.value.page,
        per_page: pagination.value.rowsPerPage
      })
      const data = response.data || response
      customers.value = data.data || data
      pagination.value.rowsNumber = data.total || (data.data ? data.data.length : customers.value.length)
    } catch (error) {
      $q.notify({ color: 'negative', message: 'Erro ao carregar clientes' })
    } finally {
      loading.value = false
    }
  }

  const onRequest = (props) => {
    pagination.value.page = props.pagination.page
    pagination.value.rowsPerPage = props.pagination.rowsPerPage
    loadCustomers()
  }

  const editCustomer = (customer) => {
    router.push(`/customers/${customer.id}/edit`)
  }

  const confirmDelete = (customer) => {
    $q.dialog({
      title: 'Confirmar exclusão',
      message: `Deseja realmente excluir o cliente "${customer.name}"?`,
      cancel: true,
      persistent: true
    }).onOk(async () => {
      try {
        await deleteCustomer(customer.id)
        $q.notify({ color: 'positive', message: 'Cliente removido com sucesso' })
        loadCustomers()
      } catch (error) {
        $q.notify({ color: 'negative', message: 'Erro ao remover cliente' })
      }
    })
  }

  const setupCustomerList = () => {
    onMounted(() => {
      loadCustomers()
    })
  }

  return {
    customers,
    loading,
    pagination,
    columns,
    loadCustomers,
    onRequest,
    editCustomer,
    confirmDelete,
    setupCustomerList
  }
}
