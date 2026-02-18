import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useQuasar } from 'quasar'
import { useUsers } from '../useUsers'

export function useUserForm() {
  const route = useRoute()
  const router = useRouter()
  const $q = useQuasar()
  const { getUser, createUser, updateUser } = useUsers()

  const isEditing = ref(false)
  const loading = ref(false)
  const userId = ref(null)

  const form = ref({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    role: 'Vendedor',
    is_active: true
  })

  const roleOptions = [
    { label: 'Admin', value: 'Admin' },
    { label: 'Vendedor', value: 'Vendedor' }
  ]

  const onSubmit = async () => {
    loading.value = true
    try {
      const data = {
        name: form.value.name,
        email: form.value.email,
        role: form.value.role,
        is_active: form.value.is_active ? 1 : 0
      }

      if (!isEditing.value) {
        data.password = form.value.password
        data.password_confirmation = form.value.password_confirmation
      }

      if (isEditing.value) {
        await updateUser(userId.value, data)
        $q.notify({ color: 'positive', message: 'Usuário atualizado com sucesso' })
      } else {
        await createUser(data)
        $q.notify({ color: 'positive', message: 'Usuário criado com sucesso' })
      }
      router.push('/users')
    } catch (error) {
      $q.notify({ color: 'negative', message: error.message || 'Erro ao salvar usuário' })
    } finally {
      loading.value = false
    }
  }

  const loadUser = async () => {
    if (userId.value) {
      try {
        const response = await getUser(userId.value)
        const data = response.data || response
        form.value = {
          name: data.name,
          email: data.email,
          role: data.roles?.[0]?.name || 'Vendedor',
          is_active: data.isActive
        }
      } catch (error) {
        $q.notify({ color: 'negative', message: 'Erro ao carregar usuário' })
      }
    }
  }

  const setupUserForm = () => {
    onMounted(() => {
      if (route.params.id) {
        userId.value = route.params.id
        isEditing.value = true
        loadUser()
      }
    })
  }

  return {
    isEditing,
    loading,
    userId,
    form,
    roleOptions,
    onSubmit,
    loadUser,
    setupUserForm
  }
}
