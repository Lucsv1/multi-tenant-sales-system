<template>
  <q-layout view="lHh LpR lFf">
    <q-page-container>
      <q-page class="flex flex-center bg-grey-2">
        <q-card class="q-pa-md shadow-2 my_card" bordered style="width: 400px; max-width: 90vw;">
          <q-card-section class="text-center">
            <div class="text-h4 text-primary q-mb-md">Sistema de Vendas</div>
            <div class="text-subtitle1 text-grey-7">Faça login para continuar</div>
          </q-card-section>

          <q-card-section>
            <q-form @submit="onSubmit" class="q-gutter-md">
              <q-input
                filled
                v-model="email"
                label="Email *"
                hint="Seu email de acesso"
                lazy-rules
                :rules="[ val => val && val.length > 0 || 'Email é obrigatório']"
              />

              <q-input
                filled
                v-model="password"
                label="Senha *"
                :type="isPwd ? 'password' : 'text'"
                hint="Sua senha"
                lazy-rules
                :rules="[ val => val && val.length > 0 || 'Senha é obrigatória']"
              >
                <template v-slot:append>
                  <q-icon
                    :name="isPwd ? 'visibility_off' : 'visibility'"
                    class="cursor-pointer"
                    @click="isPwd = !isPwd"
                  />
                </template>
              </q-input>

              <div>
                <q-btn label="Entrar" type="submit" color="primary" class="full-width q-mt-md" :loading="loading" />
              </div>
            </q-form>
          </q-card-section>

          <q-card-section v-if="error" class="q-pt-none">
            <q-banner class="bg-negative text-white">
              {{ error }}
            </q-banner>
          </q-card-section>
        </q-card>
      </q-page>
    </q-page-container>
  </q-layout>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuth } from 'src/composables/useAuth'

const router = useRouter()
const { login, getUser } = useAuth()

const email = ref('')
const password = ref('')
const isPwd = ref(true)
const loading = ref(false)
const error = ref('')

onMounted(() => {
  const token = localStorage.getItem('token')
  if (token) {
    router.push('/')
  }
})

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
</script>

<style scoped>
.my_card {
  border-radius: 12px;
}
</style>
