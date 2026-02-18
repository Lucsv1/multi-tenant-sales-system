<template>
  <q-layout view="lHh Lpr lFf">
    <q-header elevated class="bg-primary">
      <q-toolbar>
        <q-btn
          flat
          dense
          round
          icon="menu"
          aria-label="Menu"
          @click="toggleLeftDrawer"
        />

        <q-toolbar-title>
          Sistema de Vendas
        </q-toolbar-title>

        <div class="row items-center q-gutter-sm">
          <span class="text-caption">{{ user?.name }}</span>
          <q-btn flat round icon="logout" @click="handleLogout">
            <q-tooltip>Sair</q-tooltip>
          </q-btn>
        </div>
      </q-toolbar>
    </q-header>

    <q-drawer
      v-model="leftDrawerOpen"
      show-if-above
      bordered
    >
      <q-list>
        <q-item-label header>Menu</q-item-label>

        <q-item clickable to="/" exact>
          <q-item-section avatar>
            <q-icon name="dashboard" />
          </q-item-section>
          <q-item-section>
            <q-item-label>Dashboard</q-item-label>
          </q-item-section>
        </q-item>

        <q-item clickable to="/sales">
          <q-item-section avatar>
            <q-icon name="point_of_sale" />
          </q-item-section>
          <q-item-section>
            <q-item-label>Vendas</q-item-label>
          </q-item-section>
        </q-item>

        <q-item clickable to="/customers">
          <q-item-section avatar>
            <q-icon name="people" />
          </q-item-section>
          <q-item-section>
            <q-item-label>Clientes</q-item-label>
          </q-item-section>
        </q-item>

        <q-item v-if="isAdmin" clickable to="/products">
          <q-item-section avatar>
            <q-icon name="inventory" />
          </q-item-section>
          <q-item-section>
            <q-item-label>Produtos</q-item-label>
          </q-item-section>
        </q-item>

        <q-separator class="q-my-md" />

        <q-item>
          <q-item-section>
            <q-item-label caption>{{ user?.email }}</q-item-label>
          </q-item-section>
        </q-item>
      </q-list>
    </q-drawer>

    <q-page-container>
      <router-view />
    </q-page-container>
  </q-layout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuth } from 'src/composables/useAuth'

const router = useRouter()
const { user, logout, getUser, hasRole } = useAuth()

const leftDrawerOpen = ref(false)
const isAdmin = computed(() => hasRole('Admin'))

function toggleLeftDrawer () {
  leftDrawerOpen.value = !leftDrawerOpen.value
}

const handleLogout = async () => {
  await logout()
  router.push('/login')
}

onMounted(() => {
  getUser()
  if (!user.value) {
    router.push('/login')
  }
})
</script>
