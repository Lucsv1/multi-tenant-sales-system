import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useQuasar } from 'quasar'
import { useCustomers } from '../useCustomers'

export function useCustomerForm() {
  const route = useRoute()
  const router = useRouter()
  const $q = useQuasar()
  const { getCustomer, createCustomer, updateCustomer } = useCustomers()

  const isEditing = ref(false)
  const loading = ref(false)
  const customerId = ref(null)

  const form = ref({
    name: '',
    email: '',
    phone: '',
    cpf_cnpj: '',
    zip_code: '',
    address: '',
    number: '',
    complement: '',
    neighborhood: '',
    city: '',
    state: '',
    is_active: true
  })

  const onSubmit = async () => {
    loading.value = true
    try {
      const data = {
        ...form.value,
        is_active: form.value.is_active ? 1 : 0
      }

      if (isEditing.value) {
        await updateCustomer(customerId.value, data)
        $q.notify({ color: 'positive', message: 'Cliente atualizado com sucesso' })
      } else {
        await createCustomer(data)
        $q.notify({ color: 'positive', message: 'Cliente criado com sucesso' })
      }
      router.push('/customers')
    } catch (error) {
      let errorMessage = 'Erro ao salvar cliente'
      
      if (error.response) {
        const responseData = error.response.data
        if (error.response.status === 422 && responseData?.errors) {
          const errors = responseData.errors
          const cpfErrors = errors.cpf_cnpj || errors.cpfCnpj || errors.cpf
          if (cpfErrors && cpfErrors.length > 0) {
            errorMessage = cpfErrors[0]
          } else {
            const firstField = Object.keys(errors)[0]
            if (firstField && errors[firstField]?.length > 0) {
              errorMessage = errors[firstField][0]
            }
          }
        } else {
          errorMessage = responseData.message || error.message
        }
      } else {
        errorMessage = error.message
      }
      
      $q.notify({ color: 'negative', message: errorMessage })
    } finally {
      loading.value = false
    }
  }

  const loadCustomer = async () => {
    if (customerId.value) {
      try {
        const response = await getCustomer(customerId.value)
        const customerData = response.data || response
        form.value = {
          name: customerData.name,
          email: customerData.email,
          phone: customerData.phone,
          cpf_cnpj: customerData.cpfCnpj,
          zip_code: customerData.zipCode,
          address: customerData.address,
          number: customerData.number,
          complement: customerData.complement,
          neighborhood: customerData.neighborhood,
          city: customerData.city,
          state: customerData.state,
          is_active: customerData.isActive
        }
      } catch (error) {
        $q.notify({ color: 'negative', message: 'Erro ao carregar cliente' })
      }
    }
  }

  const setupCustomerForm = () => {
    onMounted(() => {
      if (route.params.id) {
        customerId.value = route.params.id
        isEditing.value = true
        loadCustomer()
      }
    })
  }

  return {
    isEditing,
    loading,
    customerId,
    form,
    onSubmit,
    loadCustomer,
    setupCustomerForm
  }
}
