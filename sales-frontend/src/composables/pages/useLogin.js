import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuth } from '../useAuth'

export function useLogin() {
  const router = useRouter()
  const { login } = useAuth()

  const email = ref('')
  const password = ref('')
  const isPwd = ref(true)
  const loading = ref(false)
  const error = ref('')

  const onSubmit = async () => {
    loading.value = true
    error.value = ''
    
    try {
      await login(email.value, password.value)
      router.push('/')
    } catch (e) {
      error.value = e.message || 'Erro ao fazer login'
    } finally {
      loading.value = false
    }
  }

  return {
    email,
    password,
    isPwd,
    loading,
    error,
    onSubmit
  }
}
