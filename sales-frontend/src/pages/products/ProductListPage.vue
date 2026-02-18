<template>
  <q-page class="q-pa-md">
    <div class="row items-center q-mb-md">
      <div class="text-h5">Produtos</div>
      <q-space />
      <q-btn color="primary" icon="add" label="Novo Produto" to="/products/create" />
    </div>

    <q-card>
      <q-card-section>
        <q-table
          :rows="products"
          :columns="columns"
          row-key="id"
          :loading="loading"
          :pagination="pagination"
          @request="onRequest"
        >
          <template v-slot:body-cell-name="props">
            <q-td :props="props">
              <div class="text-weight-bold">{{ props.row.name }}</div>
              <div class="text-caption text-grey">{{ props.row.sku }}</div>
            </q-td>
          </template>

          <template v-slot:body-cell-price="props">
            <q-td :props="props">
              R$ {{ formatCurrency(props.row.price) }}
            </q-td>
          </template>

          <template v-slot:body-cell-stock="props">
            <q-td :props="props">
              <q-badge :color="getStockColor(props.row.stock, props.row.min_stock)">
                {{ props.row.stock }}
              </q-badge>
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
              <q-btn flat round dense icon="edit" color="primary" @click="editProduct(props.row)" />
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
import { useProductList } from 'src/composables/pages/useProductList'

const { products, loading, pagination, columns, formatCurrency, getStockColor, loadProducts, onRequest, editProduct, confirmDelete } = useProductList()

onMounted(() => {
  loadProducts()
})
</script>
