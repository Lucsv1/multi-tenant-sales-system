<template>
  <q-page class="q-pa-md">
    <div class="row items-center q-mb-md">
      <div class="text-h5">Clientes</div>
      <q-space />
      <q-btn color="primary" icon="add" label="Novo Cliente" to="/customers/create" />
    </div>

    <q-card>
      <q-card-section>
        <q-table
          :rows="customers"
          :columns="columns"
          row-key="id"
          :loading="loading"
          :pagination="pagination"
          @request="onRequest"
        >
          <template v-slot:body-cell-name="props">
            <q-td :props="props">
              <div class="text-weight-bold">{{ props.row.name }}</div>
              <div class="text-caption text-grey">{{ props.row.email }}</div>
            </q-td>
          </template>

          <template v-slot:body-cell-phone="props">
            <q-td :props="props">
              {{ props.row.phone || '-' }}
            </q-td>
          </template>

          <template v-slot:body-cell-cpf_cnpj="props">
            <q-td :props="props">
              {{ props.row.cpf_cnpj || '-' }}
            </q-td>
          </template>

          <template v-slot:body-cell-is_active="props">
            <q-td :props="props">
              <q-badge :color="props.row.isActive ? 'positive' : 'negative'">
                {{ props.row.isActive ? 'Ativo' : 'Inativo' }}
              </q-badge>
            </q-td>
          </template>

          <template v-slot:body-cell-actions="props">
            <q-td :props="props" class="q-gutter-sm">
              <q-btn flat round dense icon="edit" color="primary" @click="editCustomer(props.row)" />
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
import { useCustomers } from 'src/composables/useCustomers'

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
  { name: 'is_active', label: 'Status', align: 'center', field: 'is_active' },
  { name: 'actions', label: 'Ações', align: 'center' }
]

const loadCustomers = async () => {
  loading.value = true
  try {
    const data = await getCustomers({
      page: pagination.value.page,
      per_page: pagination.value.rowsPerPage
    })
    customers.value = data.data || data
    pagination.value.rowsNumber = data.total || customers.value.length
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
      $q.notify({ color: 'positive', message: 'Cliente excluído com sucesso' })
      loadCustomers()
    } catch (error) {
      $q.notify({ color: 'negative', message: 'Erro ao excluir cliente' })
    }
  })
}

onMounted(() => {
  loadCustomers()
})
</script>
