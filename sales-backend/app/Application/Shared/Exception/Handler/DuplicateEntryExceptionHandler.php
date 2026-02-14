<?php

namespace App\Application\Shared\Exception\Handler;

use App\Application\Shared\Exception\DuplicateEntryException;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;

class DuplicateEntryExceptionHandler
{
    public function handle(QueryException $e): ?JsonResponse
    {
        if ($e->getCode() !== '23000') {
            return null;
        }

        $message = $e->getMessage();
        
        $duplicateException = $this->parseDuplicateEntry($message);

        if ($duplicateException instanceof DuplicateEntryException) {
            return response()->json([
                'message' => $duplicateException->getMessage(),
            ], 422);
        }

        return response()->json([
            'message' => 'Dados duplicados. Um registro com estes valores já existe.',
        ], 422);
    }

    private function parseDuplicateEntry(string $message): ?DuplicateEntryException
    {
        if (str_contains($message, 'tenant_id')) {
            if (str_contains($message, 'email')) {
                return new DuplicateEntryException(
                    'Este email já está em uso neste estabelecimento.',
                    'email',
                    'tenant_related'
                );
            }
            if (str_contains($message, 'cpf_cnpj')) {
                return new DuplicateEntryException(
                    'Este CPF/CNPJ já está cadastrado neste estabelecimento.',
                    'cpf_cnpj',
                    'tenant_related'
                );
            }
            if (str_contains($message, 'sku')) {
                return new DuplicateEntryException(
                    'Este SKU já está em uso neste estabelecimento.',
                    'sku',
                    'tenant_related'
                );
            }
        }

        if (str_contains($message, 'tenants.slug')) {
            return new DuplicateEntryException(
                'Este slug já está em uso por outro estabelecimento.',
                'slug',
                'tenants'
            );
        }
        if (str_contains($message, 'tenants.cnpj')) {
            return new DuplicateEntryException(
                'Este CNPJ já está cadastrado no sistema.',
                'cnpj',
                'tenants'
            );
        }
        if (str_contains($message, 'tenants.email')) {
            return new DuplicateEntryException(
                'Este email já está em uso por outro estabelecimento.',
                'email',
                'tenants'
            );
        }

        return null;
    }
}
