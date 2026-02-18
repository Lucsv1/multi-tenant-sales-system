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
          <span class="text-caption">{{ userData?.name || user?.name }}</span>
          <q-btn flat round icon="logout" @click="handleLogout">
            <q-tooltip>Sair</q-tooltip>
          </q-btn>
        </div>
      </q-toolbar>
    </q-header>

    <q-drawer
      v-model="leftDrawerOpen"
      bordered
    >
      <q-list>
        <q-item-label header>Menu</q-item-label>

        <q-item v-if="!isSuperAdmin" clickable to="/dashboard" exact>
          <q-item-section  avatar>
            <q-icon name="dashboard" />
          </q-item-section>
          <q-item-section>
            <q-item-label>Dashboard</q-item-label>
          </q-item-section>
        </q-item>

        <q-item v-if="!isSuperAdmin" clickable to="/sales">
          <q-item-section avatar>
            <q-icon name="point_of_sale" />
          </q-item-section>
          <q-item-section>
            <q-item-label>Vendas</q-item-label>
          </q-item-section>
        </q-item>

        <q-item v-if="!isSuperAdmin" clickable to="/customers">
          <q-item-section avatar>
            <q-icon name="people" />
          </q-item-section>
          <q-item-section>
            <q-item-label>Clientes</q-item-label>
          </q-item-section>
        </q-item>

        <q-item v-if="isAdmin && !isSuperAdmin" clickable to="/products">
          <q-item-section avatar>
            <q-icon name="inventory" />
          </q-item-section>
          <q-item-section>
            <q-item-label>Produtos</q-item-label>
          </q-item-section>
        </q-item>

        <q-item v-if="isAdmin && !isSuperAdmin" clickable to="/users">
          <q-item-section avatar>
            <q-icon name="person" />
          </q-item-section>
          <q-item-section>
            <q-item-label>Usuários</q-item-label>
          </q-item-section>
        </q-item>

        <q-item v-if="isSuperAdmin" clickable to="/tenants">
          <q-item-section avatar>
            <q-icon name="business" />
          </q-item-section>
          <q-item-section>
            <q-item-label>Estabelecimentos</q-item-label>
          </q-item-section>
        </q-item>

        <q-separator v-if="!isSuperAdmin" class="q-my-md" />

        <q-item>
          <q-item-section>
            <q-item-label caption>{{ userData?.email || user?.email }}</q-item-label>
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
const userData = ref(null)

const isAdmin = computed(() => {
  const u = userData.value || user.value
  return u?.roles?.some(r => r.name === 'Admin') || u?.is_super_admin
})

const isSuperAdmin = computed(() => {
  const u = userData.value || user.value
  return u?.is_super_admin === true || u?.is_super_admin === 1
})

function toggleLeftDrawer () {
  leftDrawerOpen.value = !leftDrawerOpen.value
}

const handleLogout = async () => {
  await logout()
  router.push('/login')
}

onMounted(() => {
  userData.value = getUser()
})
</script>
