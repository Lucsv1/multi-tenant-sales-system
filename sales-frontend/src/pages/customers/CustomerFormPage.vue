<template>
  <q-page class="q-pa-md">
    <q-btn icon="arrow_back" flat label="Voltar" @click="$router.back()" class="q-mb-md" />

    <div class="text-h5 q-mb-md">{{ isEditing ? 'Editar Cliente' : 'Novo Cliente' }}</div>

    <q-card>
      <q-card-section>
        <q-form @submit="onSubmit" class="q-gutter-md">
          <div class="row q-col-gutter-md">
            <div class="col-12 col-md-6">
              <q-input
                filled
                v-model="form.name"
                label="Nome *"
                :rules="[val => !!val || 'Nome é obrigatório']"
              />
            </div>
            <div class="col-12 col-md-6">
              <q-input
                filled
                v-model="form.email"
                label="Email"
                type="email"
              />
            </div>
          </div>

          <div class="row q-col-gutter-md">
            <div class="col-12 col-md-6">
              <q-input
                filled
                v-model="form.phone"
                label="Telefone"
                mask="(##) #####-####"
              />
            </div>
            <div class="col-12 col-md-6">
              <q-input
                filled
                v-model="form.cpf_cnpj"
                label="CPF/CNPJ"
                mask="###.###.###-##"
              />
            </div>
          </div>

          <div class="row q-col-gutter-md">
            <div class="col-12 col-md-4">
              <q-input filled v-model="form.zip_code" label="CEP" mask="#####-###" />
            </div>
            <div class="col-12 col-md-8">
              <q-input filled v-model="form.address" label="Endereço" />
            </div>
          </div>

          <div class="row q-col-gutter-md">
            <div class="col-12 col-md-3">
              <q-input filled v-model="form.number" label="Número" />
            </div>
            <div class="col-12 col-md-3">
              <q-input filled v-model="form.complement" label="Complemento" />
            </div>
            <div class="col-12 col-md-6">
              <q-input filled v-model="form.neighborhood" label="Bairro" />
            </div>
          </div>

          <div class="row q-col-gutter-md">
            <div class="col-12 col-md-6">
              <q-input filled v-model="form.city" label="Cidade" />
            </div>
            <div class="col-12 col-md-6">
              <q-input filled v-model="form.state" label="Estado" mask="AA" />
            </div>
          </div>

          <div class="col-12">
            <q-toggle v-model="form.is_active" label="Cliente ativo" />
          </div>

          <div class="q-mt-lg">
            <q-btn label="Salvar" type="submit" color="primary" :loading="loading" />
            <q-btn label="Cancelar" flat @click="$router.back()" class="q-ml-sm" />
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
import { useCustomers } from 'src/composables/useCustomers'

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
    if (isEditing.value) {
      await updateCustomer(customerId.value, form.value)
      $q.notify({ color: 'positive', message: 'Cliente atualizado com sucesso' })
    } else {
      await createCustomer(form.value)
      $q.notify({ color: 'positive', message: 'Cliente criado com sucesso' })
    }
    router.push('/customers')
  } catch (error) {
    $q.notify({ color: 'negative', message: error.message || 'Erro ao salvar cliente' })
  } finally {
    loading.value = false
  }
}

const loadCustomer = async () => {
  if (customerId.value) {
    try {
      const data = await getCustomer(customerId.value)
      form.value = { ...data }
    } catch (error) {
      $q.notify({ color: 'negative', message: 'Erro ao carregar cliente' })
    }
  }
}

onMounted(() => {
  if (route.params.id) {
    customerId.value = route.params.id
    isEditing.value = true
    loadCustomer()
  }
})
</script>
