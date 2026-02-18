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
          <template v-slot:body-cell-saleNumber="props">
            <q-td :props="props">
              <div class="text-weight-bold">{{ props.row.saleNumber }}</div>
              <div class="text-caption text-grey">{{ formatDate(props.row.saleDate) }}</div>
            </q-td>
          </template>

          <template v-slot:body-cell-customer="props">
            <q-td :props="props">
              {{ props.row.customer?.name || 'Consumidor' }}
            </q-td>
          </template>

          <template v-slot:body-cell-paymentMethod="props">
            <q-td :props="props">
              {{ getPaymentMethodLabel(props.row.paymentMethod) }}
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
import { onMounted } from 'vue'
import { useSaleList } from 'src/composables/pages/useSaleList'

const { 
  sales, loading, showReportDialog, reportEmail, reportLoading, filters, pagination, statusOptions, columns,
  formatCurrency, formatDate, getStatusColor, getStatusLabel, getPaymentMethodLabel,
  loadSales, onRequest, applyFilters, clearFilters, viewSale, confirmCancel, generateReport, user
} = useSaleList()

onMounted(() => {
  reportEmail.value = user.value?.email || ''
  loadSales()
})
</script>
