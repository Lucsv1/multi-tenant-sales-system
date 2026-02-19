# Sistema de Gestão de Vendas SaaS (Multi-Tenant)

## 📋 Descrição

API RESTful para sistema de gestão de vendas e estoque com suporte a múltiplas lojas (tenants). Desenvolvido em Laravel 12 com arquitetura hexagonal e Vue.js.

## 🏗️ Arquitetura

- **Backend**: Laravel 12 com API RESTful
- **Frontend**: Vue.js com Vue Router
- **Banco de Dados**: MySQL 8.0
- **Cache**: Redis
- **Filas**: Redis Queue com Laravel Queue
- **Autenticação**: Laravel Sanctum (Tokens)
- **Containerização**: Docker + Docker Compose

## 🚀 Pré-requisitos

- Docker
- Docker Compose
- Git

## 📦 Instalação

### 1. Clonar o projeto

```bash
git clone <repository-url>
cd sales-backend
```

### 2. Configurar variáveis de ambiente

```bash
cp .env.example .env
```

Edite o arquivo `.env` com as configurações do seu ambiente:

```env
APP_NAME="Sales System"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=db-sales
DB_USERNAME=sales
DB_PASSWORD=sales

QUEUE_CONNECTION=redis
CACHE_STORE=redis
REDIS_HOST=redis
```

### 3. Iniciar containers Docker

```bash
docker-compose up -d
```

### 4. Instalar dependências

```bash
docker-compose exec app composer install
docker-compose exec app npm install
```

### 5. Configurar aplicação

```bash
# Gerar chave da aplicação
docker-compose exec app php artisan key:generate

# Executar migrações
docker-compose exec app php artisan migrate --force

# Executar seeders (opcional)
docker-compose exec app php artisan db:seed
```

### 6. Acessar a aplicação

- **API**: http://localhost:8000
- **Adminer** (Gerenciador BD): http://localhost:8080

## 🔧 Comandos Úteis

```bash
# Ver logs
docker-compose logs -f app

# Acessar container
docker-compose exec app bash

# Executar testes
docker-compose exec app php artisan test

# Limpar cache
docker-compose exec app php artisan cache:clear
docker-compose exec app php artisan config:clear

# Recriar banco
docker-compose exec app php artisan migrate:fresh --seed
```

## 📚 Endpoints da API

### Autenticação

| Método | Endpoint | Descrição |
|--------|----------|-----------|
| POST | `/api/auth/register` | Registrar novo usuário |
| POST | `/api/auth/login` | Login |
| POST | `/api/auth/logout` | Logout |
| GET | `/api/auth/me` | Dados do usuário |

### Lojas (Tenants) - Apenas SuperAdmin

| Método | Endpoint | Descrição |
|--------|----------|-----------|
| GET | `/api/tenant` | Listar lojas |
| POST | `/api/tenant` | Criar loja |
| GET | `/api/tenant/{id}` | Ver loja |
| PUT | `/api/tenant/{id}` | Atualizar loja |
| DELETE | `/api/tenant/{id}` | Excluir loja |

### Produtos - Apenas Admin

| Método | Endpoint | Descrição |
|--------|----------|-----------|
| GET | `/api/product` | Listar produtos |
| POST | `/api/product` | Criar produto |
| GET | `/api/product/{id}` | Ver produto |
| PUT | `/api/product/{id}` | Atualizar produto |
| DELETE | `/api/product/{id}` | Excluir produto |

### Clientes - Admin e Vendedor

| Método | Endpoint | Descrição |
|--------|----------|-----------|
| GET | `/api/customer` | Listar clientes |
| POST | `/api/customer` | Criar cliente |
| GET | `/api/customer/{id}` | Ver cliente |
| PUT | `/api/customer/{id}` | Atualizar cliente |
| DELETE | `/api/customer/{id}` | Excluir cliente |

### Vendas - Admin e Vendedor

| Método | Endpoint | Descrição |
|--------|----------|-----------|
| GET | `/api/sale` | Listar vendas |
| POST | `/api/sale` | Criar venda |
| GET | `/api/sale/{id}` | Ver venda |
| DELETE | `/api/sale/{id}` | Cancelar venda |
| GET | `/api/sale/report` | Gerar relatório |

### Relatório de Vendas

```
GET /api/sale/report?status=completed&date_from=2024-01-01&date_to=2024-12-31
```

Parâmetros opcionais:
- `status`: completed, cancelled
- `date_from`: Data inicial (Y-m-d)
- `date_to`: Data final (Y-m-d)
- `customer_id`: ID do cliente
- `cached`: true (usar cache)
- `send_email`: true (enviar por email)
- `email`: email@exemplo.com (destinatário)

### Usuários - Apenas Admin

| Método | Endpoint | Descrição |
|--------|----------|-----------|
| GET | `/api/user` | Listar usuários |
| POST | `/api/user` | Criar usuário |
| GET | `/api/user/{id}` | Ver usuário |
| PUT | `/api/user/{id}` | Atualizar usuário |
| DELETE | `/api/user/{id}` | Excluir usuário |

## 🔐 Roles e Permissões

| Role | Permissões |
|------|-----------|
| **SuperAdmin** | Gerenciar tenants, ver tudo |
| **Admin** | Gerenciar produtos, usuários, ver clientes e vendas |
| **Vendedor** | Gerenciar clientes e vendas |

## 🧪 Testes

```bash
# Executar todos os testes
docker-compose exec app php artisan test

# Executar testes unitários
docker-compose exec app php artisan test --testsuite=Unit

# Executar testes de feature
docker-compose exec app php artisan test --testsuite=Feature
```

## 📂 Estrutura do Projeto

```
sales-backend/
├── app/
│   ├── Application/       # Casos de uso (Services, DTOs)
│   ├── Domain/           # Entidades e interfaces
│   ├── Infra/            # Implementações (Eloquent, Repositories)
│   ├── Interface/        # Controllers, HTTP
│   └── Jobs/             # Jobs para filas
├── config/               # Configurações Laravel
├── database/
│   ├── factories/        # Factories para testes
│   ├── migrations/       # Migrações
│   └── seeders/         # Seeders
├── docker/               # Configurações Docker
├── resources/            # Views, Assets
├── routes/               # Rotas API
├── tests/                # Testes
└── vendor/               # Dependências
```

## 🐳 Docker Compose Services

| Serviço | Porta | Descrição |
|---------|-------|-----------|
| app | 9000 | Aplicação Laravel |
| nginx | 8000 | Servidor Web |
| mysql | 3306 | Banco de Dados |
| redis | 6379 | Cache e Filas |
| queue | - | Worker de Filas |
| adminer | 8080 | Gerenciador BD |

## 📄 Licença

MIT
