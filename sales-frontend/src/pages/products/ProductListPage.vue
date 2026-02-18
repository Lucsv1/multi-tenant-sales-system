<template>
  <q-page class="q-pa-md">
    <div class="row items-center q-mb-md">
      <div class="text-h5">Produtos</div>
      <q-space />
      <q-btn color="primary" icon="add" label="Novo Produto" to="/products/create" />
    </div>

    <q-card>
      <q-card-section>
        <q-table
          :rows="products"
          :columns="columns"
          row-key="id"
          :loading="loading"
          :pagination="pagination"
          @request="onRequest"
        >
          <template v-slot:body-cell-name="props">
            <q-td :props="props">
              <div class="text-weight-bold">{{ props.row.name }}</div>
              <div class="text-caption text-grey">{{ props.row.sku }}</div>
            </q-td>
          </template>

          <template v-slot:body-cell-price="props">
            <q-td :props="props">
              R$ {{ formatCurrency(props.row.price) }}
            </q-td>
          </template>

          <template v-slot:body-cell-stock="props">
            <q-td :props="props">
              <q-badge :color="getStockColor(props.row.stock, props.row.min_stock)">
                {{ props.row.stock }}
              </q-badge>
            </q-td>
          </template>

          <template v-slot:body-cell-is_active="props">
            <q-td :props="props">
              <q-badge :color="props.row.isActive ? 'positive' : 'negative'">
                {{ props.row.isActive ? 'Ativo' : 'Inativo' }}
              </q-badge>
            </q-td>
          </template>

          <template v-slot:body-cell-actions="props">
            <q-td :props="props" class="q-gutter-sm">
              <q-btn flat round dense icon="edit" color="primary" @click="editProduct(props.row)" />
              <q-btn flat round dense icon="delete" color="negative" @click="confirmDelete(props.row)" />
            </q-td>
          </template>
        </q-table>
      </q-card-section>
    </q-card>
  </q-page>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useQuasar } from 'quasar'
import { useProducts } from 'src/composables/useProducts'

const router = useRouter()
const $q = useQuasar()
const { getProducts, deleteProduct } = useProducts()

const products = ref([])
const loading = ref(false)
const pagination = ref({
  page: 1,
  rowsPerPage: 10,
  rowsNumber: 0
})

const columns = [
  { name: 'name', label: 'Produto', align: 'left', field: 'name', sortable: true },
  { name: 'price', label: 'Preço', align: 'left', field: 'price' },
  { name: 'cost', label: 'Custo', align: 'left', field: 'row => formatCurrency(row.cost)' },
  { name: 'stock', label: 'Estoque', align: 'center', field: 'stock' },
  { name: 'min_stock', label: 'Estoque Mín.', align: 'center', field: 'min_stock' },
  { name: 'is_active', label: 'Status', align: 'center', field: 'is_active' },
  { name: 'actions', label: 'Ações', align: 'center' }
]

const formatCurrency = (value) => {
  return Number(value || 0).toFixed(2).replace('.', ',')
}

const getStockColor = (stock, minStock) => {
  if (stock <= 0) return 'negative'
  if (stock <= minStock) return 'warning'
  return 'positive'
}

const loadProducts = async () => {
  loading.value = true
  try {
    const data = await getProducts({
      page: pagination.value.page,
      per_page: pagination.value.rowsPerPage
    })
    products.value = data.data || data
    pagination.value.rowsNumber = data.total || products.value.length
  } catch (error) {
    $q.notify({ color: 'negative', message: 'Erro ao carregar produtos' })
  } finally {
    loading.value = false
  }
}

const onRequest = (props) => {
  pagination.value.page = props.pagination.page
  pagination.value.rowsPerPage = props.pagination.rowsPerPage
  loadProducts()
}

const editProduct = (product) => {
  router.push(`/products/${product.id}/edit`)
}

const confirmDelete = (product) => {
  $q.dialog({
    title: 'Confirmar exclusão',
    message: `Deseja realmente excluir o produto "${product.name}"?`,
    cancel: true,
    persistent: true
  }).onOk(async () => {
    try {
      await deleteProduct(product.id)
      $q.notify({ color: 'positive', message: 'Produto excluído com sucesso' })
      loadProducts()
    } catch (error) {
      $q.notify({ color: 'negative', message: 'Erro ao excluir produto' })
    }
  })
}

onMounted(() => {
  loadProducts()
})
</script>
