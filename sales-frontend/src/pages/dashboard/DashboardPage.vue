<template>
  <q-page class="q-pa-md">
    <div class="text-h5 q-mb-md">Dashboard</div>

    <div class="row q-col-gutter-md q-mb-md">
      <div class="col-12 col-sm-6 col-md-3">
        <q-card class="bg-primary text-white">
          <q-card-section>
            <div class="text-h6">Vendas Hoje</div>
            <div class="text-h4">{{ stats.todaySales }}</div>
          </q-card-section>
        </q-card>
      </div>
      
      <div class="col-12 col-sm-6 col-md-3">
        <q-card class="bg-green text-white">
          <q-card-section>
            <div class="text-h6">Faturamento Hoje</div>
            <div class="text-h4">R$ {{ formatCurrency(stats.todayRevenue) }}</div>
          </q-card-section>
        </q-card>
      </div>
      
      <div class="col-12 col-sm-6 col-md-3">
        <q-card class="bg-orange text-white">
          <q-card-section>
            <div class="text-h6">Total Clientes</div>
            <div class="text-h4">{{ stats.totalCustomers }}</div>
          </q-card-section>
        </q-card>
      </div>
      
      <div class="col-12 col-sm-6 col-md-3">
        <q-card class="bg-purple text-white">
          <q-card-section>
            <div class="text-h6">Total Produtos</div>
            <div class="text-h4">{{ stats.totalProducts }}</div>
          </q-card-section>
        </q-card>
      </div>
    </div>

    <div class="row q-col-gutter-md">
      <div class="col-12 col-md-8">
        <q-card>
          <q-card-section>
            <div class="text-h6">Vendas Recentes</div>
          </q-card-section>
          <q-separator />
          <q-card-section>
            <q-list separator>
              <q-item v-for="sale in recentSales" :key="sale.id">
                <q-item-section>
                  <q-item-label>{{ sale.saleNumber }}</q-item-label>
                  <q-item-label caption>{{ formatDate(sale.saleDate) }}</q-item-label>
                </q-item-section>
                <q-item-section side>
                  <q-badge :color="getStatusColor(sale.status)">
                    {{ getStatusLabel(sale.status) }}
                  </q-badge>
                </q-item-section>
                <q-item-section side>
                  <q-item-label class="text-weight-bold">
                    R$ {{ formatCurrency(sale.total) }}
                  </q-item-label>
                </q-item-section>
              </q-item>
            </q-list>
          </q-card-section>
        </q-card>
      </div>

      <div class="col-12 col-md-4">
        <q-card>
          <q-card-section>
            <div class="text-h6">Ações Rápidas</div>
          </q-card-section>
          <q-separator />
          <q-card-section class="q-gutter-sm">
            <q-btn color="primary" icon="point_of_sale" label="Nova Venda" to="/sales/create" class="full-width" />
            <q-btn color="green" icon="person_add" label="Novo Cliente" to="/customers/create" class="full-width" />
            <q-btn color="orange" icon="inventory" label="Produtos" to="/products" class="full-width" />
            <q-btn color="purple" icon="assessment" label="Gerar Relatório" @click="showReportDialog = true" class="full-width" />
          </q-card-section>
        </q-card>
      </div>
    </div>

    <q-dialog v-model="showReportDialog">
      <q-card style="min-width: 350px">
        <q-card-section>
          <div class="text-h6">Gerar Relatório</div>
        </q-card-section>

        <q-card-section>
          <q-input v-model="reportEmail" label="Email para envio" filled class="q-mb-md" />
          <div class="text-caption">
            O relatório será enviado para o email informado.
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
import { useSales } from 'src/composables/useSales'
import { useCustomers } from 'src/composables/useCustomers'
import { useProducts } from 'src/composables/useProducts'
import { useAuth } from 'src/composables/useAuth'
import { useQuasar } from 'quasar'

const $q = useQuasar()
const { getSales, sendReportEmail } = useSales()
const { getCustomers } = useCustomers()
const { getProducts } = useProducts()
const { user } = useAuth()

const stats = ref({
  todaySales: 0,
  todayRevenue: 0,
  totalCustomers: 0,
  totalProducts: 0
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
    const [salesResponse, customersResponse, productsResponse] = await Promise.all([
      getSales({ per_page: 100 }),
      getCustomers({ per_page: 100 }),
      getProducts({ per_page: 100 })
    ])

    const salesData = salesResponse.data || salesResponse
    const sales = salesData.data || salesData
    
    const today = new Date().toISOString().split('T')[0]
    const todaySales = sales.filter(s => s.saleDate && s.saleDate.startsWith(today))

    stats.value = {
      todaySales: todaySales.length,
      todayRevenue: todaySales.reduce((sum, s) => sum + Number(s.total), 0),
      totalCustomers: customersResponse.total || customersResponse.data?.length || 0,
      totalProducts: productsResponse.total || productsResponse.data?.length || 0
    }

    recentSales.value = sales.slice(0, 5)
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

onMounted(() => {
  reportEmail.value = user.value?.email || ''
  loadDashboard()
})
</script>
