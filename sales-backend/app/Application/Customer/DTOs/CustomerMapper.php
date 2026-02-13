<?php

namespace App\Application\Customer\DTOs;

use App\Domain\Customer\Entity\Customer as DomainCustomer;
use App\Domain\Shared\ValueObject\CpfCnpj;
use App\Domain\Shared\ValueObject\Email;
use App\Infra\Customer\Persistence\Eloquent\Customer as EloquentCustomer;

class CustomerMapper
{
    public static function toDomain(EloquentCustomer $eloquent): DomainCustomer
    {

        return new DomainCustomer(
            id: $eloquent->id,
            tenantId: $eloquent->tenant_id,
            name: $eloquent->name,
            email: $eloquent->email ? new Email($eloquent->email) : null,
            phone: $eloquent->phone,
            cpfCnpj: $eloquent->cpf_cnpj ? new CpfCnpj($eloquent->cpf_cnpj) : null,
            zipCode: $eloquent->zip_code,
            address: $eloquent->address,
            number: $eloquent->number,
            complement: $eloquent->complement,
            neighborhood: $eloquent->neighborhood,
            city: $eloquent->city,
            state: $eloquent->state,
            isActive: (bool) $eloquent->is_active,
        );
    }
}
