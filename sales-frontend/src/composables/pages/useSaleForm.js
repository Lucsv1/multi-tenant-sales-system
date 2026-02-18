import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useQuasar } from 'quasar'
import { useSales } from '../useSales'
import { useProducts } from '../useProducts'
import { useCustomers } from '../useCustomers'

export function useSaleForm() {
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
    payment_method: { label: 'Dinheiro', value: 'cash' },
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

  const customerOptions = computed(() => {
    return customers.value.filter(c => c.isActive)
  })

  const productOptions = computed(() => {
    return products.value.filter(p => p.isActive && p.stock > 0)
  })

  const subtotal = computed(() => {
    return items.value.reduce((sum, item) => sum + (item.price * item.quantity), 0)
  })

  const total = computed(() => {
    return subtotal.value - (form.value.discount || 0)
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

  const updateItemQuantity = (index, quantity) => {
    if (quantity < 1) {
      removeItem(index)
    } else {
      items.value[index].quantity = quantity
    }
  }

  const onSubmit = async () => {
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

  const loadData = async () => {
    try {
      const [productsData, customersData] = await Promise.all([
        getProducts({ per_page: 100 }),
        getCustomers({ per_page: 100 })
      ])
      products.value = productsData.data?.data || productsData.data || productsData
      customers.value = customersData.data?.data || customersData.data || customersData
    } catch (error) {
      $q.notify({ color: 'negative', message: 'Erro ao carregar dados' })
    }
  }

  const setupSaleForm = () => {
    onMounted(() => {
      loadData()
    })
  }

  return {
    loading,
    products,
    customers,
    selectedProduct,
    items,
    form,
    paymentMethods,
    customerOptions,
    productOptions,
    subtotal,
    total,
    formatCurrency,
    addProduct,
    removeItem,
    updateItemQuantity,
    onSubmit,
    loadData,
    setupSaleForm
  }
}
