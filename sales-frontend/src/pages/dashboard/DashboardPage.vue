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
                  <q-item-label>{{ sale.sale_number }}</q-item-label>
                  <q-item-label caption>{{ formatDate(sale.sale_date) }}</q-item-label>
                </q-item-section>
                <q-item-section side>
                  <q-badge :color="getStatusColor(sale.status)">
                    {{ sale.status }}
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
            <q-btn color="purple" icon="assessment" label="Gerar Relatório" @click="generateReport" class="full-width" />
          </q-card-section>
        </q-card>
      </div>
    </div>
  </q-page>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useSales } from 'src/composables/useSales'
import { useCustomers } from 'src/composables/useCustomers'
import { useProducts } from 'src/composables/useProducts'
import { useQuasar } from 'quasar'

const $q = useQuasar()
const { getSales } = useSales()
const { getCustomers } = useCustomers()
const { getProducts } = useProducts()

const stats = ref({
  todaySales: 0,
  todayRevenue: 0,
  totalCustomers: 0,
  totalProducts: 0
})

const recentSales = ref([])

const formatCurrency = (value) => {
  return Number(value || 0).toFixed(2).replace('.', ',')
}

const formatDate = (date) => {
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

const loadDashboard = async () => {
  try {
    const [salesData, customersData, productsData] = await Promise.all([
      getSales({ limit: 5 }),
      getCustomers({ limit: 100 }),
      getProducts({ limit: 100 })
    ])

    const sales = salesData.data || salesData
    
    const today = new Date().toISOString().split('T')[0]
    const todaySales = sales.filter(s => s.sale_date && s.sale_date.startsWith(today))

    stats.value = {
      todaySales: todaySales.length,
      todayRevenue: todaySales.reduce((sum, s) => sum + Number(s.total), 0),
      totalCustomers: customersData.total || customersData.data?.length || 0,
      totalProducts: productsData.total || productsData.data?.length || 0
    }

    recentSales.value = sales.slice(0, 5)
  } catch (error) {
    console.error('Erro ao carregar dashboard:', error)
  }
}

const generateReport = () => {
  $q.notify({
    color: 'positive',
    message: 'Relatório sendo gerado e será enviado por email',
    icon: 'check'
  })
}

onMounted(() => {
  loadDashboard()
})
</script>
