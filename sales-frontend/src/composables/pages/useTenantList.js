import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useQuasar } from 'quasar'
import { useTenants } from '../useTenants'

export function useTenantList() {
  const router = useRouter()
  const $q = useQuasar()
  const { getTenants, deleteTenant } = useTenants()

  const tenants = ref([])
  const loading = ref(false)

  const pagination = ref({
    page: 1,
    rowsPerPage: 10,
    rowsNumber: 0
  })

  const columns = [
    { name: 'name', label: 'Nome', align: 'left', field: 'name', sortable: true },
    { name: 'slug', label: 'Slug', align: 'left', field: 'slug' },
    { name: 'email', label: 'Email', align: 'left', field: 'email' },
    { name: 'phone', label: 'Telefone', align: 'left', field: 'phone' },
    { name: 'is_active', label: 'Status', align: 'center', field: 'isActive' },
    { name: 'actions', label: 'Ações', align: 'center' }
  ]

  const loadTenants = async () => {
    loading.value = true
    try {
      const response = await getTenants({
        page: pagination.value.page,
        per_page: pagination.value.rowsPerPage
      })
      const data = response.data || response
      tenants.value = data.data || data
      pagination.value.rowsNumber = data.total || (data.data ? data.data.length : tenants.value.length)
    } catch (error) {
      $q.notify({ color: 'negative', message: 'Erro ao carregar estabelecimentos' })
    } finally {
      loading.value = false
    }
  }

  const onRequest = (props) => {
    pagination.value.page = props.pagination.page
    pagination.value.rowsPerPage = props.pagination.rowsPerPage
    loadTenants()
  }

  const editTenant = (tenant) => {
    router.push(`/tenants/${tenant.id}/edit`)
  }

  const confirmDelete = (tenant) => {
    $q.dialog({
      title: 'Confirmar exclusão',
      message: `Deseja realmente excluir o estabelecimento "${tenant.name}"?`,
      cancel: true,
      persistent: true
    }).onOk(async () => {
      try {
        await deleteTenant(tenant.id)
        $q.notify({ color: 'positive', message: 'Estabelecimento removido com sucesso' })
        loadTenants()
      } catch (error) {
        $q.notify({ color: 'negative', message: 'Erro ao remover estabelecimento' })
      }
    })
  }

  const setupTenantList = () => {
    onMounted(() => {
      loadTenants()
    })
  }

  return {
    tenants,
    loading,
    pagination,
    columns,
    loadTenants,
    onRequest,
    editTenant,
    confirmDelete,
    setupTenantList
  }
}
