<template>
  <q-page class="q-pa-md">
    <div class="row items-center q-mb-md">
      <div class="text-h5">Vendas</div>
      <q-space />
      <q-btn color="primary" icon="add" label="Nova Venda" to="/sales/create" />
    </div>

    <q-card class="q-mb-md">
      <q-card-section>
        <div class="row q-col-gutter-md items-end">
          <div class="col-12 col-md-3">
            <q-input filled v-model="filters.date_from" label="Data Inicial" type="date" />
          </div>
          <div class="col-12 col-md-3">
            <q-input filled v-model="filters.date_to" label="Data Final" type="date" />
          </div>
          <div class="col-12 col-md-3">
            <q-select
              filled
              v-model="filters.status"
              label="Status"
              :options="statusOptions"
              emit-value
              map-options
              clearable
            />
          </div>
          <div class="col-12 col-md-3">
            <q-btn color="primary" label="Filtrar" @click="applyFilters" />
            <q-btn flat label="Limpar" @click="clearFilters" class="q-ml-sm" />
          </div>
        </div>
      </q-card-section>
    </q-card>

    <q-card>
      <q-card-section>
        <q-table
          :rows="sales"
          :columns="columns"
          row-key="id"
          :loading="loading"
          :pagination="pagination"
          @request="onRequest"
        >
          <template v-slot:body-cell-sale_number="props">
            <q-td :props="props">
              <div class="text-weight-bold">{{ props.row.sale_number }}</div>
              <div class="text-caption text-grey">{{ formatDate(props.row.sale_date) }}</div>
            </q-td>
          </template>

          <template v-slot:body-cell-customer="props">
            <q-td :props="props">
              {{ props.row.customer?.name || 'Consumidor' }}
            </q-td>
          </template>

          <template v-slot:body-cell-total="props">
            <q-td :props="props">
              <div class="text-weight-bold">R$ {{ formatCurrency(props.row.total) }}</div>
            </q-td>
          </template>

          <template v-slot:body-cell-status="props">
            <q-td :props="props">
              <q-badge :color="getStatusColor(props.row.status)">
                {{ getStatusLabel(props.row.status) }}
              </q-badge>
            </q-td>
          </template>

          <template v-slot:body-cell-actions="props">
            <q-td :props="props" class="q-gutter-sm">
              <q-btn flat round dense icon="visibility" color="primary" @click="viewSale(props.row)" />
              <q-btn 
                v-if="props.row.status === 'completed'"
                flat 
                round 
                dense 
                icon="cancel" 
                color="negative" 
                @click="confirmCancel(props.row)" 
              />
            </q-td>
          </template>
        </q-table>
      </q-card-section>
    </q-card>

    <q-dialog v-model="showReportDialog">
      <q-card style="min-width: 350px">
        <q-card-section>
          <div class="text-h6">Gerar Relatório</div>
        </q-card-section>

        <q-card-section>
          <q-input v-model="reportEmail" label="Email para envio" filled class="q-mb-md" />
          <div class="text-caption">
            O relatório será enviado para o seu email em background.
          </div>
        </q-card-section>

        <q-card-actions align="right">
          <q-btn flat label="Cancelar" v-close-popup />
          <q-btn color="primary" label="Gerar" @click="generateReport" :loading="reportLoading" />
        </q-card-actions>
      </q-card>
    </q-dialog>
  </q-page>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useQuasar } from 'quasar'
import { useSales } from 'src/composables/useSales'
import { useAuth } from 'src/composables/useAuth'

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
  date_from: '',
  date_to: '',
  status: null
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
  { name: 'sale_number', label: 'Venda', align: 'left', field: 'sale_number' },
  { name: 'customer', label: 'Cliente', align: 'left', field: 'customer' },
  { name: 'payment_method', label: 'Pagamento', align: 'left', field: 'payment_method' },
  { name: 'total', label: 'Total', align: 'right', field: 'total' },
  { name: 'status', label: 'Status', align: 'center', field: 'status' },
  { name: 'actions', label: 'Ações', align: 'center' }
]

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

const loadSales = async () => {
  loading.value = true
  try {
    const params = {
      page: pagination.value.page,
      per_page: pagination.value.rowsPerPage,
      ...filters.value
    }
    const data = await getSales(params)
    sales.value = data.data || data
    pagination.value.rowsNumber = data.total || sales.value.length
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

const confirmCancel = (sale) => {
  $q.dialog({
    title: 'Confirmar cancelamento',
    message: `Deseja realmente cancelar a venda "${sale.sale_number}"?`,
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

onMounted(() => {
  reportEmail.value = user.value?.email || ''
  loadSales()
})
</script>
