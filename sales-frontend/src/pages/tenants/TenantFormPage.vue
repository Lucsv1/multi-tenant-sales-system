<template>
  <q-page class="q-pa-md">
    <q-btn icon="arrow_back" flat label="Voltar" @click="$router.back()" class="q-mb-md" />

    <q-card>
      <q-card-section>
        <div class="text-h5">{{ isEditing ? 'Editar' : 'Novo' }} Estabelecimento</div>
      </q-card-section>

      <q-card-section>
        <q-form @submit="onSubmit" class="q-gutter-md">
          <q-input filled v-model="form.name" label="Nome *" :rules="[val => !!val || 'Nome é obrigatório']" />

          <q-input filled v-model="form.slug" label="Slug *" hint="URL amigável (ex: minha-empresa)"
            :rules="[val => !!val || 'Slug é obrigatório']" />

          <q-input filled v-model="form.email" label="Email *" type="email"
            :rules="[val => !!val || 'Email é obrigatório', val => /.+@.+/.test(val) || 'Email inválido']" />

          <q-input filled v-model="form.phone" label="Telefone" mask="(##) #####-####" />

          <q-input filled v-model="form.cnpj" label="CNPJ *" mask="##.###.###/####-##"
            :rules="[val => !!val || 'CNPJ é obrigatório']" />

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
import { onMounted } from 'vue'
import { useRoute } from 'vue-router'
import { useTenantForm } from 'src/composables/pages/useTenantForm'

const route = useRoute()
const { isEditing, tenantId, loading, form, onSubmit, loadTenant } = useTenantForm()

onMounted(() => {
  if (route.params.id) {
    tenantId.value = route.params.id
    isEditing.value = true
    loadTenant()
  }
})
</script>
