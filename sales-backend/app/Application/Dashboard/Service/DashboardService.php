<?php

namespace App\Application\Dashboard\Service;

use App\Infra\Customer\Persistence\Eloquent\Repositories\CustomerRepository;
use App\Infra\Product\Persistence\Eloquent\Repositories\ProductRepository;
use App\Infra\Sale\Persistence\Eloquent\Repositories\SaleRepository;
use App\Infra\User\Persistence\Eloquent\Repositories\UserRepository;
use App\Application\Support\CacheHelper;
use Illuminate\Support\Facades\DB;

class DashboardService
{
    protected SaleRepository $saleRepository;
    protected ProductRepository $productRepository;
    protected CustomerRepository $customerRepository;
    protected UserRepository $userRepository;

    public function __construct(
        SaleRepository $saleRepository,
        ProductRepository $productRepository,
        CustomerRepository $customerRepository,
        UserRepository $userRepository
    ) {
        $this->saleRepository = $saleRepository;
        $this->productRepository = $productRepository;
        $this->customerRepository = $customerRepository;
        $this->userRepository = $userRepository;
    }

    public function getStats(): array
    {
        $cacheKey = 'dashboard:stats';

        return CacheHelper::remember($cacheKey, 100, function () {
            return DB::transaction(function () {
                $tenantId = auth()->user()->tenant_id;

                $totalSales = $this->saleRepository->getTotalByTenant($tenantId);
                $totalCustomers = $this->customerRepository->countByTenant($tenantId);
                $totalProducts = $this->productRepository->countByTenant($tenantId);
                $totalUsers = $this->userRepository->countByTenant($tenantId);

                $salesToday = $this->saleRepository->getSalesToday($tenantId);
                $salesThisMonth = $this->saleRepository->getSalesThisMonth($tenantId);

                return [
                    'total_sales' => $totalSales,
                    'total_customers' => $totalCustomers,
                    'total_products' => $totalProducts,
                    'total_users' => $totalUsers,
                    'sales_today' => $salesToday,
                    'sales_this_month' => $salesThisMonth,
                ];
            });
        });
    }

    public function invalidateStats(): void
    {
        CacheHelper::invalidateDashboard();
    }
}
