<?php

namespace App\Application\Customer\DTOs;

use App\Domain\Customer\Entity\Customer;
use App\Domain\Shared\ValueObject\CpfCnpj;
use App\Domain\Shared\ValueObject\Email;

class CustomerResponse
{
    public function __construct(
        public readonly string $id,
        public readonly int $tenantId,
        public readonly string $name,
        public readonly ?Email $email,
        public readonly ?string $phone,
        public readonly ?CpfCnpj $cpfCnpj,
        public readonly ?string $zipCode,
        public readonly ?string $address,
        public readonly ?string $number,
        public readonly ?string $complement,
        public readonly ?string $neighborhood,
        public readonly ?string $city,
        public readonly ?string $state,
        public readonly bool $isActive,
    ) {}

    public static function fromEntity(Customer $customer): self
    {
        return new self(
            id: $customer->getId(),
            tenantId: $customer->getTenantId(),
            name: $customer->getName(),
            email: $customer->getEmail(),
            phone: $customer->getPhone(),
            cpfCnpj: $customer->getCpfCnpj(),
            zipCode: $customer->getZipCode(),
            address: $customer->getAddress(),
            number: $customer->getNumber(),
            complement: $customer->getComplement(),
            neighborhood: $customer->getNeighborhood(),
            city: $customer->getCity(),
            state: $customer->getState(),
            isActive: $customer->isActive(),
        );
    }

    public function toArray()
    {
        return [
            'id' => $this->id,
            'tenantId' => $this->tenantId,
            'name' => $this->name,
            'email' => $this->email?->getValue(),
            'phone' => $this->phone,
            'cpfCnpj' => $this->cpfCnpj?->getValue(),
            'zipCode' => $this->zipCode,
            'address' => $this->address,
            'number' => $this->number,
            'complement' => $this->complement,
            'neighborhood' => $this->neighborhood,
            'city' => $this->city,
            'state' => $this->state,
            'isActive' => $this->isActive,
        ];
    }
}
