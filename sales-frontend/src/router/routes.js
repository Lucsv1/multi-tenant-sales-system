const routes = [
  {
    path: '/login',
    component: () => import('pages/auth/LoginPage.vue')
  },
  {
    path: '/',
    component: () => import('layouts/MainLayout.vue'),
    children: [
      { path: '', component: () => import('pages/dashboard/DashboardPage.vue') },
      { path: 'customers', component: () => import('pages/customers/CustomerListPage.vue') },
      { path: 'customers/create', component: () => import('pages/customers/CustomerFormPage.vue') },
      { path: 'customers/:id/edit', component: () => import('pages/customers/CustomerFormPage.vue') },
      { path: 'products', component: () => import('pages/products/ProductListPage.vue') },
      { path: 'products/create', component: () => import('pages/products/ProductFormPage.vue') },
      { path: 'products/:id/edit', component: () => import('pages/products/ProductFormPage.vue') },
      { path: 'sales', component: () => import('pages/sales/SaleListPage.vue') },
      { path: 'sales/create', component: () => import('pages/sales/SaleFormPage.vue') },
      { path: 'sales/:id', component: () => import('pages/sales/SaleFormPage.vue') }
    ]
  },
  {
    path: '/:catchAll(.*)*',
    component: () => import('pages/ErrorNotFound.vue')
  }
]

export default routes
