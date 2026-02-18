<template>
  <q-page class="flex flex-center">
    <q-spinner size="50px" color="primary" />
  </q-page>
</template>

<script setup>
import { onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuth } from 'src/composables/useAuth'

const router = useRouter()
const { hasRole, getUser } = useAuth()

onMounted(() => {
  const user = getUser()
  if (user?.is_super_admin) {
    router.replace('/tenants')
  } else {
    router.replace('/')
  }
})
</script>
