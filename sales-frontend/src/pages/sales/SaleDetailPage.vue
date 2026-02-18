<template>
  <q-page class="q-pa-md">
    <q-btn icon="arrow_back" flat label="Voltar" @click="$router.back()" class="q-mb-md" />

    <div v-if="loading" class="text-center q-pa-lg">
      <q-spinner size="50px" color="primary" />
    </div>

    <div v-else-if="sale">
      <div class="row items-center q-mb-md">
        <div class="text-h5">Venda {{ sale.saleNumber }}</div>
        <q-space />
        <q-badge :color="getStatusColor(sale.status)" class="q-ml-md">
          {{ getStatusLabel(sale.status) }}
        </q-badge>
      </div>

      <div class="row q-col-gutter-md">
        <div class="col-12 col-md-8">
          <q-card>
            <q-card-section>
              <div class="text-subtitle1 q-mb-md">Itens da Venda</div>
              
              <q-list separator bordered>
                <q-item v-for="item in sale.items" :key="item.id">
                  <q-item-section>
                    <q-item-label>{{ item.productName || 'Produto' }}</q-item-label>
                    <q-item-label caption>
                      R$ {{ formatCurrency(item.price) }} x {{ item.quantity }}
                    </q-item-label>
                  </q-item-section>
                  <q-item-section side>
                    <q-item-label class="text-weight-bold">
                      R$ {{ formatCurrency(item.price * item.quantity) }}
                    </q-item-label>
                  </q-item-section>
                </q-item>
              </q-list>

              <div v-if="!sale.items || sale.items.length === 0" class="text-center q-pa-md text-grey">
                Nenhum item encontrado
              </div>
            </q-card-section>
          </q-card>
        </div>

        <div class="col-12 col-md-4">
          <q-card>
            <q-card-section>
              <div class="text-subtitle1 q-mb-md">Resumo</div>
              
              <div class="row justify-between q-mb-sm">
                <span>Cliente:</span>
                <span>{{ sale.customer?.name || 'Consumidor' }}</span>
              </div>
              
              <div class="row justify-between q-mb-sm">
                <span>Data:</span>
                <span>{{ formatDate(sale.saleDate) }}</span>
              </div>

              <div class="row justify-between q-mb-sm">
                <span>Forma de Pagamento:</span>
                <span>{{ getPaymentMethodLabel(sale.paymentMethod) }}</span>
              </div>

              <q-separator class="q-my-md" />

              <div class="row justify-between q-mb-sm">
                <span>Subtotal:</span>
                <span>R$ {{ formatCurrency(sale.subtotal) }}</span>
              </div>

              <div class="row justify-between q-mb-sm">
                <span>Desconto:</span>
                <span>- R$ {{ formatCurrency(sale.discount) }}</span>
              </div>

              <q-separator class="q-my-md" />

              <div class="row justify-between text-h6">
                <span>Total:</span>
                <span>R$ {{ formatCurrency(sale.total) }}</span>
              </div>

              <div v-if="sale.notes" class="q-mt-md">
                <div class="text-subtitle2">Observações:</div>
                <div class="text-grey-7">{{ sale.notes }}</div>
              </div>
            </q-card-section>
          </q-card>
        </div>
      </div>
    </div>
  </q-page>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useQuasar } from 'quasar'
import { useSales } from 'src/composables/useSales'

const route = useRoute()
const router = useRouter()
const $q = useQuasar()
const { getSale } = useSales()

const sale = ref(null)
const loading = ref(true)

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

const getPaymentMethodLabel = (method) => {
  const labels = {
    cash: 'Dinheiro',
    credit_card: 'Cartão de Crédito',
    debit_card: 'Cartão de Débito',
    pix: 'PIX',
    transfer: 'Transferência'
  }
  return labels[method] || method
}

const loadSale = async () => {
  loading.value = true
  try {
    const response = await getSale(route.params.id)
    sale.value = response.data || response
  } catch (error) {
    $q.notify({ color: 'negative', message: 'Erro ao carregar venda' })
    router.back()
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  loadSale()
})
</script>
