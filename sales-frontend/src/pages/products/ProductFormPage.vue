<template>
  <q-page class="q-pa-lg">
    <div class="q-mb-lg">
      <q-btn icon="arrow_back" flat dense label="Voltar" @click="$router.back()" class="q-mb-sm text-grey-7" />
      <div class="text-h4 text-weight-bold">{{ isEditing ? 'Editar Produto' : 'Novo Produto' }}</div>
      <div class="text-grey-6 q-mt-xs">{{ isEditing ? 'Atualize os dados do produto' : 'Preencha os dados para cadastrar um novo produto' }}</div>
    </div>

    <q-card flat bordered>
      <q-card-section class="q-pa-lg">
        <q-form @submit="onSubmit" class="q-gutter-md">

          <!-- Identificação -->
          <div class="text-subtitle1 text-weight-medium text-primary q-mb-sm">
            <q-icon name="inventory_2" class="q-mr-xs" />
            Identificação
          </div>
          <div class="row q-col-gutter-md">
            <div class="col-12 col-md-8">
              <q-input
                filled
                v-model="form.name"
                label="Nome do Produto *"
                :rules="[val => !!val || 'Nome é obrigatório']"
              />
            </div>
            <div class="col-12 col-md-4">
              <q-input
                filled
                v-model="form.sku"
                label="SKU"
              />
            </div>
            <div class="col-12">
              <q-input
                filled
                v-model="form.description"
                label="Descrição"
                type="textarea"
                rows="3"
              />
            </div>
          </div>

          <q-separator class="q-my-md" />

          <!-- Preços -->
          <div class="text-subtitle1 text-weight-medium text-primary q-mb-sm">
            <q-icon name="attach_money" class="q-mr-xs" />
            Preços
          </div>
          <div class="row q-col-gutter-md">
            <div class="col-12 col-md-6">
              <q-input
                filled
                v-model="form.price"
                label="Preço de Venda *"
                prefix="R$"
                type="number"
                step="0.01"
                :rules="[val => !!val || 'Preço é obrigatório']"
              />
            </div>
            <div class="col-12 col-md-6">
              <q-input
                filled
                v-model="form.cost"
                label="Custo"
                prefix="R$"
                type="number"
                step="0.01"
              />
            </div>
          </div>

          <q-separator class="q-my-md" />

          <!-- Estoque -->
          <div class="text-subtitle1 text-weight-medium text-primary q-mb-sm">
            <q-icon name="warehouse" class="q-mr-xs" />
            Estoque
          </div>
          <div class="row q-col-gutter-md">
            <div class="col-12 col-md-6">
              <q-input
                filled
                v-model="form.stock"
                label="Estoque Atual *"
                type="number"
                :rules="[val => val >= 0 || 'Estoque não pode ser negativo']"
              />
            </div>
            <div class="col-12 col-md-6">
              <q-input
                filled
                v-model="form.min_stock"
                label="Estoque Mínimo"
                type="number"
              />
            </div>
          </div>

          <q-separator class="q-my-md" />

          <!-- Configurações -->
          <div class="text-subtitle1 text-weight-medium text-primary q-mb-sm">
            <q-icon name="settings" class="q-mr-xs" />
            Configurações
          </div>
          <q-card flat class="bg-grey-1 rounded-borders q-pa-md">
            <div class="row items-center justify-between">
              <div>
                <div class="text-body1">Status do Produto</div>
                <div class="text-grey-6 text-caption">Produtos inativos não aparecem nas vendas</div>
              </div>
              <q-toggle v-model="form.is_active" color="positive" size="lg" />
            </div>
          </q-card>

          <!-- Ações -->
          <div class="row justify-end q-gutter-sm q-mt-md">
            <q-btn label="Cancelar" flat color="grey-7" @click="$router.back()" />
            <q-btn label="Salvar Produto" type="submit" color="primary" icon="save" :loading="loading" />
          </div>

        </q-form>
      </q-card-section>
    </q-card>
  </q-page>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useQuasar } from 'quasar'
import { useProducts } from 'src/composables/useProducts'

const route = useRoute()
const router = useRouter()
const $q = useQuasar()
const { getProduct, createProduct, updateProduct } = useProducts()

const isEditing = ref(false)
const loading = ref(false)
const productId = ref(null)

const form = ref({
  name: '',
  description: '',
  sku: '',
  price: '',
  cost: '',
  stock: 0,
  min_stock: 0,
  is_active: true
})

const onSubmit = async () => {
  loading.value = true
  try {
    const data = {
      ...form.value,
      price: parseFloat(form.value.price),
      cost: form.value.cost ? parseFloat(form.value.cost) : null,
      stock: parseInt(form.value.stock),
      min_stock: parseInt(form.value.min_stock) || 0
    }

    if (isEditing.value) {
      await updateProduct(productId.value, data)
      $q.notify({ color: 'positive', message: 'Produto atualizado com sucesso' })
    } else {
      await createProduct(data)
      $q.notify({ color: 'positive', message: 'Produto criado com sucesso' })
    }
    router.push('/products')
  } catch (error) {
    $q.notify({ color: 'negative', message: error.message || 'Erro ao salvar produto' })
  } finally {
    loading.value = false
  }
}

const loadProduct = async () => {
  if (productId.value) {
    try {
      const response = await getProduct(productId.value)
      const productData = response.data || response
      form.value = {
        name: productData.name,
        description: productData.description,
        sku: productData.sku,
        price: productData.price,
        cost: productData.cost,
        stock: productData.stock,
        min_stock: productData.minStock,
        is_active: productData.isActive
      }
    } catch (error) {
      $q.notify({ color: 'negative', message: 'Erro ao carregar produto' })
    }
  }
}

onMounted(() => {
  if (route.params.id) {
    productId.value = route.params.id
    isEditing.value = true
    loadProduct()
  }
})
</script>
