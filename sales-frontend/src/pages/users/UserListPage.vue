<template>
  <q-page class="q-pa-md">
    <div class="row items-center q-mb-md">
      <div class="text-h5">Usuários</div>
      <q-space />
      <q-btn color="primary" icon="add" label="Novo Usuário" to="/users/create" />
    </div>

    <q-card>
      <q-card-section>
        <q-table
          :rows="users"
          :columns="columns"
          :loading="loading"
          row-key="id"
          flat
          :pagination="pagination"
          @request="onRequest"
        >
          <template v-slot:body-cell-is_active="props">
            <q-td :props="props">
              <q-badge :color="props.row.isActive ? 'positive' : 'negative'">
                {{ props.row.isActive ? 'Ativo' : 'Inativo' }}
              </q-badge>
            </q-td>
          </template>

          <template v-slot:body-cell-actions="props">
            <q-td :props="props" class="q-gutter-sm">
              <q-btn flat round dense icon="edit" color="primary" @click="editUser(props.row)" />
              <q-btn flat round dense icon="delete" color="negative" @click="confirmDelete(props.row)" />
            </q-td>
          </template>
        </q-table>
      </q-card-section>
    </q-card>
  </q-page>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useQuasar } from 'quasar'
import { useUsers } from 'src/composables/useUsers'

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

onMounted(() => {
  loadUsers()
})
</script>
