import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useQuasar } from 'quasar'
import { useTenants } from '../useTenants'

export function useTenantForm() {
  const route = useRoute()
  const router = useRouter()
  const $q = useQuasar()
  const { getTenant, createTenant, updateTenant } = useTenants()

  const isEditing = ref(false)
  const loading = ref(false)
  const tenantId = ref(null)

  const form = ref({
    name: '',
    slug: '',
    email: '',
    phone: '',
    cnpj: '',
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
        await updateTenant(tenantId.value, data)
        $q.notify({ color: 'positive', message: 'Estabelecimento atualizado com sucesso' })
      } else {
        await createTenant(data)
        $q.notify({ color: 'positive', message: 'Estabelecimento criado com sucesso' })
      }
      router.push('/tenants')
    } catch (error) {
      $q.notify({ color: 'negative', message: error.message || 'Erro ao salvar estabelecimento' })
    } finally {
      loading.value = false
    }
  }

  const loadTenant = async () => {
    if (tenantId.value) {
      try {
        const response = await getTenant(tenantId.value)
        const data = response.data || response
        form.value = {
          name: data.name,
          slug: data.slug,
          email: data.email,
          phone: data.phone,
          cnpj: data.cnpj,
          is_active: data.isActive
        }
      } catch (error) {
        $q.notify({ color: 'negative', message: 'Erro ao carregar estabelecimento' })
      }
    }
  }

  const setupTenantForm = () => {
    onMounted(() => {
      if (route.params.id) {
        tenantId.value = route.params.id
        isEditing.value = true
        loadTenant()
      }
    })
  }

  return {
    isEditing,
    loading,
    tenantId,
    form,
    onSubmit,
    loadTenant,
    setupTenantForm
  }
}
