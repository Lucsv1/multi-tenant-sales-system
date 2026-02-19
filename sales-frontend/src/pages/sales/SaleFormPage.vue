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
              @click="onSubmit"
            />
          </q-card-section>
        </q-card>
      </div>
    </div>
  </q-page>
</template>

<script setup>
import { onMounted } from 'vue'
import { useSaleForm } from 'src/composables/pages/useSaleForm'

const { 
  loading, products, customers, selectedProduct, items, form, paymentMethods,
  customerOptions, productOptions, subtotal, total, formatCurrency,
  addProduct, removeItem, onSubmit, loadData
} = useSaleForm()

onMounted(() => {
  loadData()
})
</script>
