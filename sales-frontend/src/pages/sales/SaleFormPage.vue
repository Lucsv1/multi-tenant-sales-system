<template>
  <q-page class="q-pa-md">
    <q-btn icon="arrow_back" flat label="Voltar" @click="$router.back()" class="q-mb-md" />

    <div class="text-h5 q-mb-md">Nova Venda</div>

    <div class="row q-col-gutter-md">
      <div class="col-12 col-md-8">
        <q-card>
          <q-card-section>
            <div class="text-subtitle1 q-mb-md">Itens da Venda</div>

            <div class="row q-col-gutter-md q-mb-md">
              <div class="col-12 col-md-6">
                <q-select
                  filled
                  v-model="selectedProduct"
                  label="Selecionar Produto"
                  :options="productOptions"
                  option-label="name"
                  option-value="id"
                  emit-value
                  map-options
                  @update:model-value="addProduct"
                  clearable
                />
              </div>
            </div>

            <q-list separator bordered>
              <q-item v-for="(item, index) in items" :key="index">
                <q-item-section>
                  <q-item-label>{{ item.product.name }}</q-item-label>
                  <q-item-label caption>
                    R$ {{ formatCurrency(item.price) }} x {{ item.quantity }}
                  </q-item-label>
                </q-item-section>
                <q-item-section side>
                  <q-item-label class="text-weight-bold">
                    R$ {{ formatCurrency(item.price * item.quantity) }}
                  </q-item-label>
                </q-item-section>
                <q-item-section side>
                  <q-btn flat round dense icon="delete" color="negative" @click="removeItem(index)" />
                </q-item-section>
              </q-item>
            </q-list>

            <div v-if="items.length === 0" class="text-center q-pa-md text-grey">
              Nenhum item adicionado
            </div>
          </q-card-section>
        </q-card>

        <q-card class="q-mt-md">
          <q-card-section>
            <div class="text-subtitle1 q-mb-md">Dados do Cliente</div>

            <q-select
              filled
              v-model="form.customer_id"
              label="Cliente (opcional)"
              :options="customerOptions"
              option-label="name"
              option-value="id"
              emit-value
              map-options
              clearable
            />
          </q-card-section>
        </q-card>
      </div>

      <div class="col-12 col-md-4">
        <q-card>
          <q-card-section>
            <div class="text-subtitle1 q-mb-md">Resumo</div>

            <div class="row justify-between q-mb-sm">
              <span>Subtotal:</span>
              <span>R$ {{ formatCurrency(subtotal) }}</span>
            </div>

            <div class="row justify-between q-mb-sm">
              <span>Desconto:</span>
              <q-input
                v-model.number="form.discount"
                type="number"
                prefix="R$"
                step="0.01"
                min="0"
                dense
                outlined
                style="width: 120px"
              />
            </div>

            <q-separator class="q-my-md" />

            <div class="row justify-between text-h6">
              <span>Total:</span>
              <span>R$ {{ formatCurrency(total) }}</span>
            </div>

            <q-separator class="q-my-md" />

            <q-select
              filled
              v-model="form.payment_method"
              label="Forma de Pagamento"
              :options="paymentMethods"
              class="q-mb-md"
            />

            <q-input
              filled
              v-model="form.notes"
              label="Observações"
              type="textarea"
              rows="2"
              class="q-mb-md"
            />

            <q-btn
              color="primary"
              label="Finalizar Venda"
              class="full-width"
              :loading="loading"
              :disable="items.length === 0"
              @click="submitSale"
            />
          </q-card-section>
        </q-card>
      </div>
    </div>
  </q-page>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useQuasar } from 'quasar'
import { useSales } from 'src/composables/useSales'
import { useProducts } from 'src/composables/useProducts'
import { useCustomers } from 'src/composables/useCustomers'

const router = useRouter()
const $q = useQuasar()
const { createSale } = useSales()
const { getProducts } = useProducts()
const { getCustomers } = useCustomers()

const loading = ref(false)
const products = ref([])
const customers = ref([])
const selectedProduct = ref(null)
const items = ref([])

const form = ref({
  customer_id: null,
  payment_method: 'cash',
  discount: 0,
  notes: ''
})

const paymentMethods = [
  { label: 'Dinheiro', value: 'cash' },
  { label: 'Cartão de Crédito', value: 'credit_card' },
  { label: 'Cartão de Débito', value: 'debit_card' },
  { label: 'PIX', value: 'pix' },
  { label: 'Transferência', value: 'transfer' }
]

const subtotal = computed(() => {
  return items.value.reduce((sum, item) => sum + (item.price * item.quantity), 0)
})

const total = computed(() => {
  return subtotal.value - (form.value.discount || 0)
})

const productOptions = computed(() => {
  return products.value.filter(p => p.isActive && p.stock > 0)
})

const customerOptions = computed(() => {
  return customers.value.filter(c => c.isActive)
})

const formatCurrency = (value) => {
  return Number(value || 0).toFixed(2).replace('.', ',')
}

const addProduct = (productId) => {
  if (!productId) return

  const product = products.value.find(p => String(p.id) === String(productId))
  if (!product) return

  const existing = items.value.find(i => String(i.product.id) === String(productId))
  if (existing) {
    if (existing.quantity < product.stock) {
      existing.quantity++
    } else {
      $q.notify({ color: 'warning', message: 'Estoque insuficiente' })
    }
  } else {
    items.value.push({
      product,
      price: parseFloat(product.price),
      quantity: 1
    })
  }
  selectedProduct.value = null
}

const removeItem = (index) => {
  items.value.splice(index, 1)
}

const submitSale = async () => {
  if (items.value.length === 0) {
    $q.notify({ color: 'warning', message: 'Adicione pelo menos um produto' })
    return
  }

  loading.value = true
  try {
    const saleData = {
      customer_id: form.value.customer_id ? String(form.value.customer_id) : null,
      payment_method: form.value.payment_method?.value || form.value.payment_method,
      discount: form.value.discount || 0,
      notes: form.value.notes,
      items: items.value.map(item => ({
        product_id: Number(item.product.id),
        quantity: Number(item.quantity),
        price: Number(item.price),
        discount: 0
      }))
    }

    const response = await createSale(saleData)
    const saleDataResponse = response.data || response
    $q.notify({ color: 'positive', message: 'Venda realizada com sucesso!' })
    router.push(`/sales/${saleDataResponse.id}`)
  } catch (error) {
    $q.notify({ color: 'negative', message: error.message || 'Erro ao criar venda' })
  } finally {
    loading.value = false
  }
}

onMounted(async () => {
  try {
    const [productsData, customersData] = await Promise.all([
      getProducts({ per_page: 100 }),
      getCustomers({ per_page: 100 })
    ])
    products.value = productsData.data || productsData
    customers.value = customersData.data || customersData
  } catch (error) {
    $q.notify({ color: 'negative', message: 'Erro ao carregar dados' })
  }
})
</script>
