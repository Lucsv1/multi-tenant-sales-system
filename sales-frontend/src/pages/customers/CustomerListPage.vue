<template>
  <q-page class="q-pa-md">
    <div class="row items-center q-mb-md">
      <div class="text-h5">Clientes</div>
      <q-space />
      <q-btn color="primary" icon="add" label="Novo Cliente" to="/customers/create" />
    </div>

    <q-card>
      <q-card-section>
        <q-table
          :rows="customers"
          :columns="columns"
          row-key="id"
          :loading="loading"
          :pagination="pagination"
          @request="onRequest"
        >
          <template v-slot:body-cell-name="props">
            <q-td :props="props">
              <div class="text-weight-bold">{{ props.row.name }}</div>
              <div class="text-caption text-grey">{{ props.row.email }}</div>
            </q-td>
          </template>

          <template v-slot:body-cell-phone="props">
            <q-td :props="props">
              {{ props.row.phone || '-' }}
            </q-td>
          </template>

          <template v-slot:body-cell-cpf_cnpj="props">
            <q-td :props="props">
              {{ props.row.cpf_cnpj || '-' }}
            </q-td>
          </template>

          <template v-slot:body-cell-isActive="props">
            <q-td :props="props">
              <q-badge :color="props.row.isActive ? 'positive' : 'negative'">
                {{ props.row.isActive ? 'Ativo' : 'Inativo' }}
              </q-badge>
            </q-td>
          </template>

          <template v-slot:body-cell-actions="props">
            <q-td :props="props" class="q-gutter-sm">
              <q-btn flat round dense icon="edit" color="primary" @click="editCustomer(props.row)" />
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
import { useCustomerList } from 'src/composables/pages/useCustomerList'

const { customers, loading, pagination, columns, loadCustomers, onRequest, editCustomer, confirmDelete } = useCustomerList()

onMounted(() => {
  loadCustomers()
})
</script>
