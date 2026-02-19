const routes = [
  {
    path: '/login',
    component: () => import('pages/auth/LoginPage.vue')
  },
  {
    path: '/',
    component: () => import('layouts/MainLayout.vue'),
    children: [
      { path: '', component: () => import('pages/auth/AuthRedirectPage.vue') },
      { path: 'dashboard', component: () => import('pages/dashboard/DashboardPage.vue') },
      { path: 'customers', component: () => import('pages/customers/CustomerListPage.vue') },
      { path: 'customers/create', component: () => import('pages/customers/CustomerFormPage.vue') },
      { path: 'customers/:id/edit', component: () => import('pages/customers/CustomerFormPage.vue') },
      { path: 'products', component: () => import('pages/products/ProductListPage.vue') },
      { path: 'products/create', component: () => import('pages/products/ProductFormPage.vue') },
      { path: 'products/:id/edit', component: () => import('pages/products/ProductFormPage.vue') },
      { path: 'sales', component: () => import('pages/sales/SaleListPage.vue') },
      { path: 'sales/create', component: () => import('pages/sales/SaleFormPage.vue') },
      { path: 'sales/:id', component: () => import('pages/sales/SaleDetailPage.vue') },
      { path: 'tenants', component: () => import('pages/tenants/TenantListPage.vue') },
      { path: 'tenants/create', component: () => import('pages/tenants/TenantFormPage.vue') },
      { path: 'tenants/:id/edit', component: () => import('pages/tenants/TenantFormPage.vue') },
      { path: 'users', component: () => import('pages/users/UserListPage.vue') },
      { path: 'users/create', component: () => import('pages/users/UserFormPage.vue') },
      { path: 'users/:id/edit', component: () => import('pages/users/UserFormPage.vue') }
    ]
  },
  {
    path: '/:catchAll(.*)*',
    component: () => import('pages/ErrorNotFound.vue')
  }
]

export default routes
