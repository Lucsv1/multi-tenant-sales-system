import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useQuasar } from 'quasar'
import { useProducts } from '../useProducts'

export function useProductList() {
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
    { name: 'cost', label: 'Custo', align: 'left', field: row => formatCurrency(row.cost) },
    { name: 'stock', label: 'Estoque', align: 'center', field: 'stock' },
    { name: 'min_stock', label: 'Estoque Mín.', align: 'center', field: 'minStock' },
    { name: 'isActive', label: 'Status', align: 'center', field: 'isActive' },
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
      const response = await getProducts({
        page: pagination.value.page,
        per_page: pagination.value.rowsPerPage
      })
      const data = response.data || response
      products.value = data.data || data
      pagination.value.rowsNumber = data.total || (data.data ? data.data.length : products.value.length)
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
        $q.notify({ color: 'positive', message: 'Produto removido com sucesso' })
        loadProducts()
      } catch (error) {
        $q.notify({ color: 'negative', message: 'Erro ao remover produto' })
      }
    })
  }

  const setupProductList = () => {
    onMounted(() => {
      loadProducts()
    })
  }

  return {
    products,
    loading,
    pagination,
    columns,
    formatCurrency,
    getStockColor,
    loadProducts,
    onRequest,
    editProduct,
    confirmDelete,
    setupProductList
  }
}
