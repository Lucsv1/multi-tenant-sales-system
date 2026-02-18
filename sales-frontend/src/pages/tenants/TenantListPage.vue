<template>
  <q-page class="q-pa-md">
    <div class="row items-center q-mb-md">
      <div class="text-h5">Estabelecimentos</div>
      <q-space />
      <q-btn color="primary" icon="add" label="Novo Estabelecimento" to="/tenants/create" />
    </div>

    <q-card>
      <q-card-section>
        <q-table
          :rows="tenants"
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
              <q-btn flat round dense icon="edit" color="primary" @click="editTenant(props.row)" />
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
import { useTenants } from 'src/composables/useTenants'

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

onMounted(() => {
  loadTenants()
})
</script>
