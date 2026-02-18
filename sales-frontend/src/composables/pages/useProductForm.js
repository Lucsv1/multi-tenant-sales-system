import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useQuasar } from 'quasar'
import { useProducts } from '../useProducts'

export function useProductForm() {
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
        console.log('Loading product:', productId.value)
        const response = await getProduct(productId.value)
        console.log('Product response:', response)
        const productData = response.data || response
        console.log('Product data:', productData)
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
        console.log('Form updated:', form.value)
      } catch (error) {
        console.error('Error loading product:', error)
        $q.notify({ color: 'negative', message: 'Erro ao carregar produto' })
      }
    }
  }

  const setupProductForm = () => {
    onMounted(() => {
      if (route.params.id) {
        productId.value = route.params.id
        isEditing.value = true
        loadProduct()
      }
    })
  }

  return {
    isEditing,
    loading,
    productId,
    form,
    onSubmit,
    loadProduct,
    setupProductForm
  }
}
