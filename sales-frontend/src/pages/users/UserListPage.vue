<template>
  <q-page class="q-pa-md">
    <div class="row items-center q-mb-md">
      <div class="text-h5">Usuários</div>
      <q-space />
      <q-btn color="primary" icon="add" label="Novo Usuário" to="/users/create" />
    </div>

    <q-card>
      <q-card-section>
        <q-table
          :rows="users"
          :columns="columns"
          :loading="loading"
          row-key="id"
          flat
          :pagination="pagination"
          @request="onRequest"
        >
          <template v-slot:body-cell-is_active="props">
            <q-td :props="props">
              <q-badge :color="props.row.isActive ? 'positive' : 'negative'">
                {{ props.row.isActive ? 'Ativo' : 'Inativo' }}
              </q-badge>
            </q-td>
          </template>

          <template v-slot:body-cell-actions="props">
            <q-td :props="props" class="q-gutter-sm">
              <q-btn flat round dense icon="edit" color="primary" @click="editUser(props.row)" />
              <q-btn flat round dense icon="delete" color="negative" @click="confirmDelete(props.row)" />
            </q-td>
          </template>
        </q-table>
      </q-card-section>
    </q-card>
  </q-page>
</template>

<script setup>
import { onMounted } from 'vue'
import { useUserList } from 'src/composables/pages/useUserList'

const { users, loading, pagination, columns, loadUsers, onRequest, editUser, confirmDelete } = useUserList()

onMounted(() => {
  loadUsers()
})
</script>
