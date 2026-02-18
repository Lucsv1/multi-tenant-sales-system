<template>
  <q-page class="q-pa-md">
    <q-btn icon="arrow_back" flat label="Voltar" @click="$router.back()" class="q-mb-md" />

    <q-card>
      <q-card-section>
        <div class="text-h5">{{ isEditing ? 'Editar' : 'Novo' }} Estabelecimento</div>
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
            v-model="form.slug"
            label="Slug *"
            hint="URL amigável (ex: minha-empresa)"
            :rules="[val => !!val || 'Slug é obrigatório']"
          />

          <q-input
            filled
            v-model="form.email"
            label="Email *"
            type="email"
            :rules="[val => !!val || 'Email é obrigatório', val => /.+@.+/.test(val) || 'Email inválido']"
          />

          <q-input
            filled
            v-model="form.phone"
            label="Telefone"
          />

          <q-input
            filled
            v-model="form.cnpj"
            label="CNPJ *"
            :rules="[val => !!val || 'CNPJ é obrigatório']"
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
import { useTenants } from 'src/composables/useTenants'

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

onMounted(() => {
  if (route.params.id) {
    tenantId.value = route.params.id
    isEditing.value = true
    loadTenant()
  }
})
</script>
