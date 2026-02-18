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

      <div class="col-12 col-sm-6 col-md-3" v-if="isAdmin">
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
            <q-btn v-if="isAdmin" color="orange" icon="inventory" label="Produtos" to="/products" class="full-width" />
            <q-btn color="purple" icon="assessment" label="Gerar Relatório" @click="showReportDialog = true"
              class="full-width" />
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
import { onMounted } from 'vue'
import { useDashboard } from 'src/composables/pages/useDashboard'

const {
  isAdmin, stats, recentSales, showReportDialog, reportEmail, reportLoading,
  formatCurrency, formatDate, getStatusColor, getStatusLabel, loadDashboard, generateReport
} = useDashboard()

onMounted(() => {
  loadDashboard()
})
</script>
