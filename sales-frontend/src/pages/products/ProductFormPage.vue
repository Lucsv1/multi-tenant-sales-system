<template>
  <q-page class="q-pa-md">
    <q-btn icon="arrow_back" flat label="Voltar" @click="$router.back()" class="q-mb-md" />

    <div class="text-h5 q-mb-md">{{ isEditing ? 'Editar Produto' : 'Novo Produto' }}</div>

    <q-card>
      <q-card-section>
        <q-form @submit="onSubmit" class="q-gutter-md">
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
          </div>

          <q-input
            filled
            v-model="form.description"
            label="Descrição"
            type="textarea"
            rows="3"
          />

          <div class="row q-col-gutter-md">
            <div class="col-12 col-md-4">
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
            <div class="col-12 col-md-4">
              <q-input
                filled
                v-model="form.cost"
                label="Custo"
                prefix="R$"
                type="number"
                step="0.01"
              />
            </div>
            <div class="col-12 col-md-4">
              <q-input
                filled
                v-model="form.stock"
                label="Estoque *"
                type="number"
                :rules="[val => val >= 0 || 'Estoque não pode ser negativo']"
              />
            </div>
          </div>

          <div class="row q-col-gutter-md">
            <div class="col-12 col-md-4">
              <q-input
                filled
                v-model="form.min_stock"
                label="Estoque Mínimo"
                type="number"
              />
            </div>
            <div class="col-12 col-md-4">
              <q-toggle v-model="form.is_active" label="Produto ativo" />
            </div>
          </div>

          <div class="q-mt-lg">
            <q-btn label="Salvar" type="submit" color="primary" :loading="loading" />
            <q-btn label="Cancelar" flat @click="$router.back()" class="q-ml-sm" />
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
      const data = await getProduct(productId.value)
      form.value = { ...data }
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
