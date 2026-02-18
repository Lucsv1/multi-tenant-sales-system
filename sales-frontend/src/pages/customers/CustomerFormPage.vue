<template>
  <q-page class="q-pa-lg">
    <div class="q-mb-lg">
      <q-btn icon="arrow_back" flat dense label="Voltar" @click="$router.back()" class="q-mb-sm text-grey-7" />
      <div class="text-h4 text-weight-bold">{{ isEditing ? 'Editar Cliente' : 'Novo Cliente' }}</div>
      <div class="text-grey-6 q-mt-xs">{{ isEditing ? 'Atualize os dados do cliente' : 'Preencha os dados para cadastrar um novo cliente' }}</div>
    </div>

    <q-card flat bordered>
      <q-card-section class="q-pa-lg">

        <q-form @submit="onSubmit" class="q-gutter-md">

          <!-- Dados Pessoais -->
          <div class="text-subtitle1 text-weight-medium text-primary q-mb-sm">
            <q-icon name="person" class="q-mr-xs" />
            Dados Pessoais
          </div>
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

          <q-separator class="q-my-md" />

          <!-- Endereço -->
          <div class="text-subtitle1 text-weight-medium text-primary q-mb-sm">
            <q-icon name="place" class="q-mr-xs" />
            Endereço
          </div>
          <div class="row q-col-gutter-md">
            <div class="col-12 col-md-4">
              <q-input filled v-model="form.zip_code" label="CEP" mask="#####-###" />
            </div>
            <div class="col-12 col-md-8">
              <q-input filled v-model="form.address" label="Endereço" />
            </div>
            <div class="col-12 col-md-3">
              <q-input filled v-model="form.number" label="Número" />
            </div>
            <div class="col-12 col-md-3">
              <q-input filled v-model="form.complement" label="Complemento" />
            </div>
            <div class="col-12 col-md-6">
              <q-input filled v-model="form.neighborhood" label="Bairro" />
            </div>
            <div class="col-12 col-md-6">
              <q-input filled v-model="form.city" label="Cidade" />
            </div>
            <div class="col-12 col-md-6">
              <q-input filled v-model="form.state" label="Estado" mask="AA" />
            </div>
          </div>

          <q-separator class="q-my-md" />

          <!-- Status -->
          <div class="text-subtitle1 text-weight-medium text-primary q-mb-sm">
            <q-icon name="settings" class="q-mr-xs" />
            Configurações
          </div>
          <q-card flat class="bg-grey-1 rounded-borders q-pa-md">
            <div class="row items-center justify-between">
              <div>
                <div class="text-body1">Status do Cliente</div>
                <div class="text-grey-6 text-caption">Clientes inativos não aparecem nas buscas</div>
              </div>
              <q-toggle v-model="form.is_active" color="positive" size="lg" />
            </div>
          </q-card>

          <!-- Ações -->
          <div class="row justify-end q-gutter-sm q-mt-md">
            <q-btn label="Cancelar" flat color="grey-7" @click="$router.back()" />
            <q-btn label="Salvar Cliente" type="submit" color="primary" icon="save" :loading="loading" />
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

onMounted(() => {
  if (route.params.id) {
    customerId.value = route.params.id
    isEditing.value = true
    loadCustomer()
  }
})
</script>
