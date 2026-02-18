import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useQuasar } from 'quasar'
import { useUsers } from '../useUsers'

export function useUserList() {
  const router = useRouter()
  const $q = useQuasar()
  const { getUsers, deleteUser } = useUsers()

  const users = ref([])
  const loading = ref(false)

  const pagination = ref({
    page: 1,
    rowsPerPage: 10,
    rowsNumber: 0
  })

  const columns = [
    { name: 'name', label: 'Nome', align: 'left', field: 'name', sortable: true },
    { name: 'email', label: 'Email', align: 'left', field: 'email' },
    { name: 'role', label: 'Perfil', align: 'left', field: row => row.roles?.[0]?.name || 'Vendedor' },
    { name: 'is_active', label: 'Status', align: 'center', field: 'isActive' },
    { name: 'actions', label: 'Ações', align: 'center' }
  ]

  const loadUsers = async () => {
    loading.value = true
    try {
      const response = await getUsers({
        page: pagination.value.page,
        per_page: pagination.value.rowsPerPage
      })
      const data = response.data || response
      users.value = data.data || data
      pagination.value.rowsNumber = data.total || (data.data ? data.data.length : users.value.length)
    } catch (error) {
      $q.notify({ color: 'negative', message: 'Erro ao carregar usuários' })
    } finally {
      loading.value = false
    }
  }

  const onRequest = (props) => {
    pagination.value.page = props.pagination.page
    pagination.value.rowsPerPage = props.pagination.rowsPerPage
    loadUsers()
  }

  const editUser = (user) => {
    router.push(`/users/${user.id}/edit`)
  }

  const confirmDelete = (user) => {
    $q.dialog({
      title: 'Confirmar exclusão',
      message: `Deseja realmente excluir o usuário "${user.name}"?`,
      cancel: true,
      persistent: true
    }).onOk(async () => {
      try {
        await deleteUser(user.id)
        $q.notify({ color: 'positive', message: 'Usuário removido com sucesso' })
        loadUsers()
      } catch (error) {
        $q.notify({ color: 'negative', message: 'Erro ao remover usuário' })
      }
    })
  }

  const setupUserList = () => {
    onMounted(() => {
      loadUsers()
    })
  }

  return {
    users,
    loading,
    pagination,
    columns,
    loadUsers,
    onRequest,
    editUser,
    confirmDelete,
    setupUserList
  }
}
