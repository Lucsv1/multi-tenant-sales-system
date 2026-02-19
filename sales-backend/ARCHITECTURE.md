# Documentação de Arquitetura

## Visão Geral

Sistema de Gestão de Vendas Multi-Tenant desenvolvido com Laravel 12, utilizando arquitetura hexagonal (Ports and Adapters) para garantir escalabilidade, testabilidade e separação de responsabilidades.

---

## 🏗️ Arquitetura Hexagonal

O projeto segue o padrão de **Arquitetura Hexagonal** (também conhecida como Ports and Adapters), separando a lógica de negócio das dependências externas.

### Camadas

```
┌─────────────────────────────────────────────────────────┐
│                    Interface Layer                       │
│         (Controllers, HTTP, Middleware, Routes)         │
└─────────────────────────────────────────────────────────┘
                            │
                            ▼
┌─────────────────────────────────────────────────────────┐
│                  Application Layer                       │
│         (Services, DTOs, Use Cases, Commands)            │
└─────────────────────────────────────────────────────────┘
                            │
                            ▼
┌─────────────────────────────────────────────────────────┐
│                     Domain Layer                          │
│        (Entities, Value Objects, Interfaces)            │
└─────────────────────────────────────────────────────────┘
                            │
                            ▼
┌─────────────────────────────────────────────────────────┐
│                  Infrastructure Layer                     │
│      (Eloquent Models, Repositories, External APIs)     │
└─────────────────────────────────────────────────────────┘
```

---

## 📁 Estrutura de Diretórios

```
app/
├── Application/           # Casos de uso
│   ├── {Entity}/
│   │   ├── DTOs/         # Data Transfer Objects
│   │   └── Service/      # Lógica de negócio
│   └── Shared/           # Compartilhados
│
├── Domain/                # Entidades e interfaces
│   ├── {Entity}/
│   │   ├── Entity/       # Objetos de domínio
│   │   └── Repositories/ # Interfaces de repositório
│   └── Shared/
│       └── ValueObject/  # Objetos de valor
│
├── Infra/                 # Implementações
│   ├── {Entity}/
│   │   └── Persistence/
│   │       └── Eloquent/ # Models e Repositories
│   └── Persistence/
│       └── Traits/      # Traits compartilhados
│
├── Interface/             # Adaptadores de entrada
│   ├── {Module}/
│   │   └── Http/
│   │       ├── Controllers/
│   │       └── Middleware/
│   └── Shared/
│       └── Http/
│
└── Jobs/                  # Jobs de fila
```

---

## 🔐 Sistema de Multi-Tenancy

### Estratégia: Shared Database, Separate Schema

Todas as lojas (tenants) compartilham o mesmo banco de dados, mas possuem registros isolados através do campo `tenant_id`.

### Modelo de Dados

```
┌─────────────────────────────────────────────────────────┐
│                        tenants                           │
├─────────────────────────────────────────────────────────┤
│ id, name, slug, email, cnpj, phone, is_active, ...     │
└─────────────────────────────────────────────────────────┘
        ▲
        │ 1:N
┌───────┴────────────────────────────────────────────────┐
│  users │ products │ customers │ sales │ sale_items     │
├────────────────────────────────────────────────────────┤
│ tenant_id (FK) + dados específicos de cada entidade    │
└────────────────────────────────────────────────────────┘
```

### Middleware de Tenant

O `BelongsToTenant` trait automaticamente adiciona o filtro `where('tenant_id', auth()->user()->tenant_id)` em todas as queries.

---

## 🔒 Sistema de Autenticação e Autorização

### Autenticação

- **Laravel Sanctum** com tokens stateless
- Tokens expiram em 24 horas
- Bearer token no header `Authorization`

### Roles e Permissões

| Role | Descrição | Permissões |
|------|-----------|------------|
| SuperAdmin | Administrador do sistema | Gerenciar tenants, ver tudo |
| Admin | Administrador da loja | Gerenciar produtos, usuários, ver tudo |
| Vendedor | Funcionário | Criar/editar clientes e vendas |

### Permissions por Role

```
SuperAdmin: * (todas)

Admin:
  - product.create, product.read, product.update, product.delete
  - user.create, user.read, user.update, user.delete
  - customer.create, customer.read, customer.update
  - sale.create, sale.read, sale.cancel

Vendedor:
  - customer.create, customer.read, customer.update
  - sale.create, sale.read
```

---

## 💾 Controle de Estoque e Transações

### atomicidade

Todas as operações que envolvem estoque utilizam **Database Transactions** para garantir consistência:

```php
DB::transaction(function () {
    // Validar estoque
    // Criar venda
    // Criar itens
    // Debitar estoque
});
```

### Lock de Linhas

Para evitar race conditions, utiliza-se `lockForUpdate()` ao decrementar estoque:

```php
$product = Product::lockForUpdate()->findOrFail($id);
$product->decrementStock($quantity);
```

---

## 🎯 Filas e Processamento Background

### Queue Connection: Redis

- **Relatórios**: Geração de PDF com DomPDF + envio por e-mail
- Configurado no container `queue` do Docker Compose

### Job: GenerateSalesReportJob

1. Recebe parâmetros do relatório (filtros)
2. Gera dados do relatório
3. Cria PDF com DomPDF
4. Envia por e-mail (se solicitado)
5. Armazena PDF em disco

---

## ⚡ Cache com Redis

### Estratégia de Cache

- **Relatórios**: Cache de 30 minutos para relatórios
- **Listagens**: Cache de consultas pesadas
- **TTL configurável** por tipo de dado

---

## 🧪 Testes

### Tipos de Testes

- **Unitários**: Testam services e lógica de domínio
- **Feature**: Testam endpoints e integração

### Banco de Testes

- MySQL com database isolada para testes
- RefreshDatabase trait reseta o banco a cada teste

---

## 🐳 Docker Compose

### Services

| Serviço | Imagem | Função |
|---------|--------|--------|
| app | Laravel | Aplicação principal |
| nginx | nginx:alpine | Servidor web |
| mysql | mysql:8.0 | Banco de dados |
| redis | redis:alpine | Cache e Filas |
| queue | Laravel | Worker de filas |
| adminer | adminer | Gerenciador BD |

---

## 📝 Decisões de Design

### 1. Arquitetura Hexagonal
**Por quê?**: Separação clara entre regras de negócio e infraestrutura, facilitando manutenção e testes.

### 2. Multi-Tenant com tenant_id
**Por quê?**: Simples de implementar, baixo custo de manutenção, boa performance.

### 3. Laravel Sanctum
**Por quê?**: Leve, integração nativa com Laravel, ideal para APIs.

### 4. Spatie Permissions
**Por quê?**: Solidação tested, integração com Laravel, suporte a roles e permissions.

### 5. DomPDF para relatórios
**Por quê?**: Alternativa PHP pura ao TCPDF/wkhtmltopdf, sem dependências externas.

### 6. Redis para cache e filas
**Por quê?**: Alta performance, suporte a estruturas de dados complexas, integrado ao Laravel.
