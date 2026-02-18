<template>
  <q-page class="q-pa-md">
    <q-btn icon="arrow_back" flat label="Voltar" @click="$router.back()" class="q-mb-md" />

    <q-card>
      <q-card-section>
        <div class="text-h5">{{ isEditing ? 'Editar' : 'Novo' }} Usuário</div>
      </q-card-section>

      <q-card-section>
        <q-form @submit="onSubmit" class="q-gutter-md">
          <q-input
            filled
            v-model="form.name"
            label="Nome *"
            :rules="[val => !!val || 'Nome é obrigatório']"
          />

          <q-input
            filled
            v-model="form.email"
            label="Email *"
            type="email"
            :rules="[val => !!val || 'Email é obrigatório', val => /.+@.+/.test(val) || 'Email inválido']"
          />

          <q-input
            v-if="!isEditing"
            filled
            v-model="form.password"
            label="Senha *"
            type="password"
            :rules="[val => !!val || 'Senha é obrigatória']"
          />

          <q-input
            v-if="!isEditing"
            filled
            v-model="form.password_confirmation"
            label="Confirmar Senha *"
            type="password"
            :rules="[val => val === form.password || 'As senhas não conferem']"
          />

          <q-select
            filled
            v-model="form.role"
            :options="roleOptions"
            label="Perfil *"
            emit-value
            map-options
          />

          <div class="row items-center">
            <span class="q-mr-md">Status:</span>
            <q-toggle v-model="form.is_active" color="positive" size="lg" />
          </div>

          <div class="row justify-end q-gutter-sm q-mt-md">
            <q-btn label="Cancelar" flat color="grey-7" @click="$router.back()" />
            <q-btn label="Salvar" type="submit" color="primary" icon="save" :loading="loading" />
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
import { useUsers } from 'src/composables/useUsers'

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

onMounted(() => {
  if (route.params.id) {
    userId.value = route.params.id
    isEditing.value = true
    loadUser()
  }
})
</script>
