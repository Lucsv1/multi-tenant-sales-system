<template>
  <q-page class="q-pa-md">
    <q-btn icon="arrow_back" flat label="Voltar" @click="$router.back()" class="q-mb-md" />

    <q-card>
      <q-card-section>
        <div class="text-h5">{{ isEditing ? 'Editar' : 'Novo' }} Usuário</div>
      </q-card-section>

      <q-card-section>
        <q-form @submit="onSubmit" class="q-gutter-md">
          <q-input filled v-model="form.name" label="Nome *" :rules="[val => !!val || 'Nome é obrigatório']" />

          <q-input filled v-model="form.email" label="Email *" type="email"
            :rules="[val => !!val || 'Email é obrigatório', val => /.+@.+/.test(val) || 'Email inválido']" />

          <q-input v-if="!isEditing" filled v-model="form.password" label="Senha *" type="password"
            :rules="[val => !!val || 'Senha é obrigatória']" />

          <q-input v-if="!isEditing" filled v-model="form.password_confirmation" label="Confirmar Senha *"
            type="password" :rules="[val => val === form.password || 'As senhas não conferem']" />

          <q-select filled v-model="form.role" :options="roleOptions" label="Perfil *" emit-value map-options />

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
import { useUserForm } from 'src/composables/pages/useUserForm'

const route = useRoute()
const { isEditing, userId, loading, form, roleOptions, onSubmit, loadUser } = useUserForm()

onMounted(() => {
  if (route.params.id) {
    userId.value = route.params.id
    isEditing.value = true
    loadUser()
  }
})
</script>
