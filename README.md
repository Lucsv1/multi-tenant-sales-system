# Sistema de Gestão de Vendas Multi-Tenant

## Sobre o Projeto

Sistema de gestão de vendas e estoque com suporte a múltiplas lojas (tenants) operando de forma isolada dentro da mesma aplicação. Desenvolvido com arquitetura hexagonal para garantir escalabilidade, testabilidade e separação de responsabilidades.

### Funcionalidades Principais

- **Multi-Tenancy**: Múltiplas lojas (tenants) com dados completamente isolados
- **Gestão de Produtos**: CRUD completo com controle de estoque
- **Gestão de Clientes**: Cadastro e gerenciamento de clientes por loja
- **Gestão de Vendas**: Registro de vendas com débitos atômicos no estoque
- **Relatórios**: Geração de relatórios em background via filas (Queue)
- **Dashboard**: Estatísticas em tempo real com cache Redis
- **Autenticação e Autorização**: Laravel Sanctum com tokens JWT
- **Controle de Acesso**: Roles (SuperAdmin, Admin, Vendedor) com permissões granulares

---

## Requisitos

### Obrigatórios

| Requisito | Versão |
|-----------|--------|
| Docker | 20.10+ |
| Docker Compose | 2.0+ |
| Git | 2.40+ |

### Recomendados

| Ferramenta | Versão |
|------------|--------|
| PHPStorm / VSCode | Última versão |
| Postman ou Insomnia | Última versão |

---

## Stack Tecnológica

| Tecnologia | Versão | Finalidade |
|------------|--------|------------|
| Laravel | 12.x | Framework principal |
| Vue.js (Quasar) | 3.x | Frontend SPA |
| MySQL | 8.0 | Banco de dados |
| Redis | Alpine | Cache e Filas |
| Laravel Sanctum | 4.x | Autenticação via tokens |
| Spatie Permissions | 6.x | Roles e Permissões |
| Laravel Horizon | - | Gerenciamento de filas |
| DomPDF | 3.x | Geração de PDF |
| OpenAPI/Swagger | l5-swagger | Documentação da API |
| Docker Compose | 2.x | Orquestração de containers |

---

## 📧 Servidor de E-mail (Obrigatório)

Para o sistema funcionar corretamente (envio de relatórios em PDF por e-mail), é necessário configurar um servidor de e-mail SMTP.

### Configuração no arquivo `.env`

Edite o arquivo `sales-backend/.env` com as suas credenciais:

```env
# Configuração do Servidor de E-mail
MAIL_MAILER=smtp
MAIL_HOST=smtp.seudominio.com.br
MAIL_PORT=587
MAIL_USERNAME=seu-email@seudominio.com.br
MAIL_PASSWORD=sua_senha_de_app
MAIL_FROM_ADDRESS=noreply@seudominio.com.br
MAIL_FROM_NAME="${APP_NAME}"
```

### Exemplos de Provedores

| Provedor | HOST | Porta | Observações |
|----------|------|-------|-------------|
| **Gmail** | smtp.gmail.com | 587 | Necesita senha de app |
| **Outlook** | smtp.office365.com | 587 | - |
| **Amazon SES** | email-smtp.amazonaws.com | 587 | Precisa criar credenciais |
| **Mailtrap** | smtp.mailtrap.io | 2525 | Para testes |

### Como gerar senha de app no Gmail

1. Acesse sua conta Google
2. Vá em **Segurança**
3. Ative a **Verificação em duas etapas**
4. Vá em **Senhas de App** (pesquise no campo de busca)
5. Crie uma nova senha para o aplicativo
6. Use essa senha no `MAIL_PASSWORD`

### Alternativa: Usar Mailtrap para desenvolvimento

Para testes locais, você pode usar o Mailtrap (gratuito):

1. Acesse https://mailtrap.io
2. Crie uma conta gratuita
3. Copie as credenciais SMTP fornecidas
4. Configure no seu `.env`

---

## Arquitetura do Sistema

### Modelo Multi-Tenant

O sistema utiliza a estratégia **Shared Database, Separate Schema**, onde todas as lojas compartilham o mesmo banco de dados, mas possuem registros isolados através do campo `tenant_id`.

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

### Arquitetura Hexagonal

O projeto segue o padrão de Arquitetura Hexagonal (Ports and Adapters):

```
app/
├── Application/           # Casos de uso (Services, DTOs)
├── Domain/                # Entidades e interfaces de repositório
├── Infra/                # Implementações (Eloquent, Repositories)
├── Interface/            # Controladores e Middleware
└── Jobs/                 # Jobs de fila
```

---

## Como Rodar o Projeto

### Pré-requisitos

1. **Clone o repositório**
```bash
git clone https://github.com/seu-repo/multi-tenant-sales-system.git
cd multi-tenant-sales-system
```

2. **Copie o arquivo de ambiente**
```bash
cp sales-backend/.env.example sales-backend/.env
```

### Opção 1: Docker Compose (Recomendado)

> ⚠️ **Nota**: O arquivo `.env` já vem configurado com as credenciais corretas do banco de dados e Redis conforme definido no `docker-compose.yml`.

1. **Suba os containers**
```bash
docker compose up -d
```

2. **Instale as dependências e gere a chave da aplicação**
```bash
docker compose exec app composer install
docker compose exec app php artisan key:generate
```

3. **Execute as migrações**
```bash
docker compose exec app php artisan migrate --force
```

4. **Execute os seeders**
```bash
docker compose exec app php artisan db:seed
```

5. **Acesse a aplicação**
- API: http://localhost:8000
- Swagger UI: http://localhost:8000/api/docs/view
- Frontend (Quasar): http://localhost:9000
- Adminer (Gerenciador de BD): http://localhost:8080

### Opção 2: Desenvolvimento Local (Sem Docker)

> ⚠️ **Nota**: Para desenvolvimento local sem Docker, configure as credenciais do banco e Redis conforme a seção "Variáveis de Ambiente" abaixo.

1. **Configure o ambiente**
```bash
cd sales-backend
docker compose exec app composer install
cp .env.example .env
docker compose exec app php artisan key:generate
```

2. **Rode as migrações e seeders**
```bash
docker compose exec app php artisan migrate --force
docker compose exec app php artisan db:seed
```

---

## ⚠️ IMPORTANTE: Credenciais do Sistema

O sistema cria automaticamente usuários e tenants na primeira execução via seeders:

### Super Admin (Global)

| Campo | Valor |
|-------|-------|
| **Email** | superadmin@sistema.com.br |
| **Senha** | superadmin123 |
| **Role** | SuperAdmin |
| **Acesso** | Todas as lojas e configuração do sistema |

**O SuperAdmin pode:**
- Criar e gerenciar tenants (lojas)
- Gerenciar usuários globais

### Admins por Tenant

Cada tenant criado recebe um usuário Admin automaticamente:

| Tenant | Email | Senha |
|-------|-------|-------|
| Loja Matriz | admin@lojamatriz.com.br | admin123 |
| Loja Filial Centro | admin@loja-filial-centro.com.br | admin123 |
| Restaurante Sabor & Cia | admin@restaurante-sabor.com.br | admin123 |
| Pizzaria Bella Napoli | admin@pizzaria-bella-napoli.com.br | admin123 |
| Farmácia Vida Saudável | admin@farmacia-vida-saudavel.com.br | admin123 |

**O Admin pode:**
- Gerenciar produtos (CRUD)
- Gerenciar usuários da loja
- Acessar dashboard e relatórios
- Gerenciar clientes e vendas

### Vendedores

Os vendedores são criados pelos Admins via interface ou API. Eles têm acesso limitado:
- Criar e editar clientes
- Criar e visualizar vendas

### Tenants Criados pelo Seeder

| Nome | Slug | CNPJ | Status |
|------|------|------|--------|
| Loja Matriz | loja-matriz | 38.084.343/0001-54 | Ativo |
| Loja Filial Centro | loja-filial-centro | 45.699.702/0001-43 | Ativo |
| Restaurante Sabor & Cia | restaurante-sabor | 96.406.377/0001-14 | Ativo |
| Pizzaria Bella Napoli | pizzaria-bella-napoli | 54.345.696/0001-17 | Ativo |
| Farmácia Vida Saudável | farmacia-vida-saudavel | 98.474.613/0001-65 | Ativo |
| Boutique Elegance | boutique-elegance | 31.382.574/0001-03 | Inativo |

**⚠️ IMPORTANTE: ALTERE A SENHA APÓS O PRIMEIRO LOGIN EM AMBIENTE DE PRODUÇÃO!**

---

## ⚠️ Validação de CPF e CNPJ

O sistema possui validação rigorosa para CPF e CNPJ. **Não são aceitos números aleatórios** - apenas documentos que passem na validação algorítmica oficial brasileira.

### Por que não aceitamos números aleatórios?

CPF e CNPJ possuem algoritmos de validação específicos (módulos 10 e 11) que verificam se os dígitos verificadores estão corretos. O sistema valida esses dígitos automaticamente.

### Onde gerar CPF/CNPJ válidos para testes?

Utilize o gerador de CPF/CNPJ online:

- **CPF**: https://www.4devs.com.br/gerador_de_cpf
- **CNPJ**: https://www.4devs.com.br/gerador_de_cnpj

Esses sites geram documentos válidos algoritmicamente que passarão na validação do sistema.

### Campos que possuem validação

| Campo | Entidade |
|-------|----------|
| cpf | Users (opcional) |
| cnpj | Tenants (obrigatório) |
| cpf | Customers |

### Exemplos de documentos válidos (para testes)

| Tipo | Valor |
|------|-------|
| CPF válido | 123.456.789-00 (use o gerador) |
| CNPJ válido | 12.345.678/0001-90 (use o gerador) |

---

## 🔐 Autenticação e Autorização

### Sistema de Roles

O sistema possui 3 perfis de usuário com permissões específicas:

| Role | Descrição | Permissões |
|------|-----------|------------|
| **SuperAdmin** | Administrador do sistema | Gerenciar tenants, acessar todos os dados |
| **Admin** | Administrador da loja | Gerenciar produtos, usuários, clientes, vendas |
| **Vendedor** | Funcionário | Criar/editar clientes e vendas |

### Permissões por Role

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

### Como Fazer Login

1. **Endpoint de autenticação**
```http
POST /api/auth/login
Content-Type: application/json

{
  "email": "superadmin@sistema.com.br",
  "password": "superadmin123"
}
```

2. **Resposta com token**
```json
{
  "message": "Login realizado com sucesso",
  "data": {
    "user": {
      "id": 1,
      "name": "Super Administrador",
      "email": "superadmin@sistema.com.br",
      "tenant_id": null,
      "is_super_admin": true
    },
    "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...",
    "expires_at": "2026-02-20T12:00:00Z"
  }
}
```

3. **Usar o token nas requisições**
```http
GET /api/dashboard/stats
Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...
```

### Criar Novos Usuários

Após obter o token do admin, você pode criar outros usuários:

```http
POST /api/user
Authorization: Bearer <token_do_admin>
Content-Type: application/json

{
  "name": "João Vendedor",
  "email": "joao@lojamatriz.com.br",
  "password": "senha123",
  "role": "Vendedor"
}
```

### Logout

```http
POST /api/auth/logout
Authorization: Bearer <token>
```

---

## Documentação da API (Swagger)

### Acesso

Após iniciar a aplicação, acesso:

| Recurso | URL |
|---------|-----|
| Swagger UI | http://localhost:8000/api/docs/view |
| OpenAPI JSON | http://localhost:8000/api/docs |

### Endpoints Disponíveis

#### Autenticação
| Método | Endpoint | Descrição | Autenticação |
|--------|----------|-----------|--------------|
| POST | /api/auth/login | Login e geração de token | Pública |
| POST | /api/auth/register | Cadastro de novo usuário | Pública |
| POST | /api/auth/logout | Logout e invalidação de token | Autenticado |
| GET | /api/auth/me | Dados do usuário atual | Autenticado |

#### Dashboard
| Método | Endpoint | Descrição | Autenticação |
|--------|----------|-----------|--------------|
| GET | /api/dashboard/stats | Estatísticas do dashboard | Autenticado |

#### Produtos
| Método | Endpoint | Descrição | Permissão |
|--------|----------|-----------|-----------|
| GET | /api/product | Listar produtos | Admin |
| POST | /api/product | Criar produto | Admin |
| GET | /api/product/{id} | Buscar produto | Admin |
| PUT | /api/product/{id} | Atualizar produto | Admin |
| DELETE | /api/product/{id} | Excluir produto | Admin |

#### Clientes
| Método | Endpoint | Descrição | Permissão |
|--------|----------|-----------|-----------|
| GET | /api/customer | Listar clientes | Admin, Vendedor |
| POST | /api/customer | Criar cliente | Admin, Vendedor |
| GET | /api/customer/{id} | Buscar cliente | Admin, Vendedor |
| PUT | /api/customer/{id} | Atualizar cliente | Admin, Vendedor |
| DELETE | /api/customer/{id} | Excluir cliente | Admin |

#### Vendas
| Método | Endpoint | Descrição | Permissão |
|--------|----------|-----------|-----------|
| GET | /api/sale | Listar vendas | Admin, Vendedor |
| POST | /api/sale | Criar venda | Admin, Vendedor |
| GET | /api/sale/{id} | Buscar venda | Admin, Vendedor |
| DELETE | /api/sale/{id} | Cancelar venda | Admin |
| GET | /api/sale/report | Relatório de vendas | Autenticado |

#### Usuários
| Método | Endpoint | Descrição | Permissão |
|--------|----------|-----------|-----------|
| GET | /api/user | Listar usuários | Admin |
| POST | /api/user | Criar usuário | Admin |
| GET | /api/user/{id} | Buscar usuário | Admin |
| PUT | /api/user/{id} | Atualizar usuário | Admin |
| DELETE | /api/user/{id} | Excluir usuário | Admin |

#### Tenants
| Método | Endpoint | Descrição | Permissão |
|--------|----------|-----------|-----------|
| GET | /api/tenant | Listar tenants | SuperAdmin |
| POST | /api/tenant | Criar tenant | SuperAdmin |
| GET | /api/tenant/{id} | Buscar tenant | SuperAdmin |
| PUT | /api/tenant/{id} | Atualizar tenant | SuperAdmin |
| DELETE | /api/tenant/{id} | Excluir tenant | SuperAdmin |

---

## Controle de Estoque e Transações

### Atomicidade

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

### Fluxo de uma Venda

```
1. Receber itens da venda
2. Validar estoque disponível (para cada produto)
3. Bloquear registro do produto (lockForUpdate)
4. Criar registro da venda
5. Criar itens da venda
6. Debitar quantidade do estoque
7. Invalidar cache do dashboard
8. Confirmar transação (commit)
```

---

## Filas e Processamento Background

### Queue Connection: Redis

- **Relatórios**: Geração de PDF com DomPDF + envio por e-mail
- Configurado no container `queue` do Docker Compose

### Job: GenerateSalesReportJob

1. Recebe parâmetros do relatório (filtros)
2. Gera dados do relatório
3. Cria PDF com DomPDF
4. Envia por e-mail (se solicitado)
5. Armazena PDF em disco

### Como Disparar um Relatório

```http
GET /api/sale/report?date_from=2026-01-01&date_to=2026-01-31
Authorization: Bearer <token>
```

Este endpoint retorna os dados diretamente. Para geração em background:

```php
GenerateSalesReportJob::dispatch(
    auth()->user()->tenant_id,
    auth()->id(),
    $filters,
    $email // opcional
);
```

---

## Cache com Redis

### Estratégia de Cache

- **Dashboard**: Cache de 100 segundos para estatísticas
- **Listagens**: Cache de consultas pesadas
- **TTL configurável** por tipo de dado

### Invalidar Cache

O cache é automaticamente invalidado quando:
- Uma nova venda é criada/cancelada
- Um produto é criado/atualizado/excluído
- Um cliente é criado/atualizado/excluído

---

## Estrutura do Projeto

```
multi-tenant-sales-system/
|
+-- sales-backend/              # API Laravel
|   +-- app/
|   │   +-- Application/       # Casos de uso (Services, DTOs)
|   │   +-- Domain/            # Entidades e interfaces
|   │   +-- Infra/             # Implementações (Eloquent, Repositories)
|   │   +-- Interface/         # Controllers e Middleware
|   │   +-- Jobs/              # Jobs de fila
|   +-- database/
|   │   +-- migrations/        # Migrações do banco
|   │   +-- seeders/           # Dados iniciais
|   │   +-- factories/        #Factories para testes
|   +-- config/                # Configurações do Laravel
|   +-- routes/                # Rotas da API
|   +-- tests/                 # Testes unitários e feature
|   +-- Dockerfile             # Container PHP-FPM
|   +-- docker/                # Configurações Docker (PHP, Nginx)
|
+-- sales-frontend/            # Frontend Vue.js (Quasar)
|   +-- src/
|   │   +-- pages/             # Páginas da aplicação
|   │   +-- layouts/           # Layouts principais
|   │   +-- router/            # Rotas Vue Router
|   │   +-- composables/       # Hooks reutilizáveis
|   │   +-- stores/            # Estado global (Pinia)
|   +-- Dockerfile             # Container Node
|
+-- docker compose.yml         # Orquestração de containers
+-- README.md                  # Este arquivo
+-- ARCHITECTURE.md            # Documentação de arquitetura
```

---

## Variáveis de Ambiente

> ⚠️ **Nota**: O arquivo `.env` que acompanha o projeto já contém as credenciais corretas para o banco de dados e Redis conforme definidas no `docker-compose.yml`. alteration only.

### Banco de Dados

| Variável | Descrição | Padrão |
|----------|-----------|--------|
| DB_CONNECTION | Driver do banco | mysql |
| DB_HOST | Host do banco | mysql |
| DB_PORT | Porta do banco | 3306 |
| DB_DATABASE | Nome do banco | sales-db |
| DB_USERNAME | Usuário do banco | sales-user |
| DB_PASSWORD | Senha do banco | password |

### Redis

| Variável | Descrição | Padrão |
|----------|-----------|--------|
| REDIS_HOST | Host do Redis | redis |
| REDIS_PASSWORD | Senha do Redis | null |
| REDIS_PORT | Porta do Redis | 6379 |

### Cache e Filas

| Variável | Descrição | Padrão |
|----------|-----------|--------|
| CACHE_DRIVER | Driver de cache | redis |
| QUEUE_CONNECTION | Driver de filas | redis |
| SESSION_DRIVER | Driver de sessões | redis |

### Acesso ao Banco de Dados

Para acesso direto ao banco de dados (via Adminer, DBeaver, MySQL Workbench, etc.), utilize:

| Parâmetro | Valor |
|-----------|-------|
| Servidor | mysql |
| Usuário | sales-user |
| Senha | password |
| Banco de dados | sales-db |

---

### Aplicação

| Variável | Descrição | Padrão |
|----------|-----------|--------|
| APP_NAME | Nome da aplicação | Laravel |
| APP_ENV | Ambiente | local |
| APP_DEBUG | Modo debug | true |
| APP_URL | URL da aplicação | http://localhost:8000 |

### Autenticação

| Variável | Descrição | Padrão |
|----------|-----------|--------|
| SANCTUM_STATEFUL_DOMAINS | Domínios permitidos | localhost:9000 |

### E-mail

| Variável | Descrição | Padrão |
|----------|-----------|--------|
| MAIL_MAILER | Driver de e-mail | smtp |
| MAIL_HOST | Servidor SMTP | smtp.gmail.com |
| MAIL_PORT | Porta SMTP | 587 |
| MAIL_USERNAME | Usuário SMTP | - |
| MAIL_PASSWORD | Senha SMTP | - |

---

## Comandos Úteis

### Docker Compose

```bash
# Subir todos os serviços
docker compose up -d

# Subir apenas app e banco
docker compose up -d app mysql redis

# Ver logs
docker compose logs -f

# Ver logs de um serviço específico
docker compose logs -f app
docker compose logs -f mysql

# Parar serviços
docker compose down

# Parar e remover volumes (limpa banco)
docker compose down -v

# Rebuild de um serviço
docker compose build app
docker compose up -d app

# Listar containers
docker compose ps
```

### Artisan (Laravel)

```bash
# Executar migrações
docker compose exec app php artisan migrate

# Reverter última migração
docker compose exec app php artisan migrate:rollback

# Executar seeders
docker compose exec app php artisan db:seed

# Limpar cache
docker compose exec app php artisan config:clear
docker compose exec app php artisan cache:clear
docker compose exec app php artisan route:clear

# Criar cache
docker compose exec app php artisan config:cache
docker compose exec app php artisan route:cache
docker compose exec app php artisan view:cache

# Listar rotas
docker compose exec app php artisan route:list

# Criar chave da aplicação
docker compose exec app php artisan key:generate

# Publicar assets
docker compose exec app php artisan vendor:publish --all
```

### Testes

```bash
# Rodar todos os testes
docker compose exec app php artisan test

# Rodar testes unitários
docker compose exec app php artisan test --testsuite=Unit

# Rodar testes feature
docker compose exec app php artisan test --testsuite=Feature
```

### Filas

```bash
# Tentar novamente jobs falhados
docker compose exec app php artisan queue:retry all
```

### Frontend (Quasar)

```bash
# Instalar dependências
docker compose exec frontend npm install
```

---

## Testes

### Executar Testes

```bash
# Todos os testes
docker compose exec app php artisan test

# Com cobertura
docker compose exec app php artisan test --coverage

# Teste específico
docker compose exec app php artisan test --filter=SaleServiceTest
```

### Estrutura de Testes

```
tests/
+-- Unit/
|   +-- Services/
|       +-- SaleServiceTest.php
|       +-- ProductServiceTest.php
|       +-- CustomerServiceTest.php
|       +-- UserServiceTest.php
|       +-- TenantServiceTest.php
+-- Feature/
    +-- ExampleTest.php
```

---

## 🧪 Testando o Sistema (Passo a Passo)

### 1. Login como SuperAdmin

```bash
curl -X POST http://localhost:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "superadmin@sistema.com.br",
    "password": "superadmin123"
  }'
```

**Resposta:**
```json
{
  "message": "Login realizado com sucesso",
  "data": {
    "user": {
      "id": 1,
      "name": "Super Administrador",
      "email": "superadmin@sistema.com.br",
      "tenant_id": null,
      "is_super_admin": true
    },
    "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...",
    "expires_at": "2026-02-20T12:00:00Z"
  }
}
```

### 2. Listar Tenants (apenas SuperAdmin)

```bash
curl -X GET http://localhost:8000/api/tenant \
  -H "Authorization: Bearer SEU_TOKEN_AQUI"
```

### 3. Login como Admin de uma Loja

```bash
curl -X POST http://localhost:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "admin@lojamatriz.com.br",
    "password": "admin123"
  }'
```

### 4. Ver Dashboard

```bash
curl -X GET http://localhost:8000/api/dashboard/stats \
  -H "Authorization: Bearer SEU_TOKEN_AQUI"
```

### 5. Listar Produtos

```bash
curl -X GET http://localhost:8000/api/product \
  -H "Authorization: Bearer SEU_TOKEN_AQUI"
```

### 6. Criar uma Venda

```bash
curl -X POST http://localhost:8000/api/sale \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer SEU_TOKEN_AQUI" \
  -d '{
    "customer_id": 1,
    "payment_method": "credit_card",
    "items": [
      {
        "product_id": 1,
        "quantity": 2
      }
    ]
  }'
```

---

## Troubleshooting

### Erro: "Connection refused" ao banco de dados

- Verifique se o container MySQL está rodando: `docker ps`
- Verifique as credenciais no `.env`
- Aguarde alguns segundos após iniciar o MySQL

### Erro: "Class not found" ao rodar migrations

- Execute: `docker compose exec app composer install`
- Execute: `docker compose exec app php artisan config:clear`

### Erro: "Redis connection refused"

- Verifique se o container Redis está rodando: `docker ps`
- Verifique a variável `REDIS_HOST=redis` no `.env`

### Erro 401 Unauthorized nos endpoints

- Verifique se fez login e obteve o token JWT
- Certifique-se de incluir o header: `Authorization: Bearer <token>`
- Verifique se o token não expirou

### Erro 403 Forbidden

- Seu usuário não tem permissão para acessar esse endpoint
- Verifique a role necessária na documentação da API
- Use o usuário admin para operações administrativas

### Erro: "Too many connections" no MySQL

- Aumente o limite de conexões no MySQL
- Verifique se há conexões não fechadas no código

### Usuários não foram criados

```bash
# Verificar se a migration rodou
docker compose exec app php artisan migrate:status

# Executar seeders novamente
docker compose exec app php artisan db:seed --force

# Verificar se usuários existem
docker compose exec app php artisan tinker
App\Models\User::all()
```

### Frontend não conecta com API

- Verifique se a API está rodando na porta 8000
- Configure o proxy no frontend ou permita CORS
- Verifique a URL da API no arquivo de configuração do frontend

---

## Análise de Vulnerabilidades

### Resumo de Segurança

| Categoria | Status |
|-----------|--------|
| Autenticação | ✅ Laravel Sanctum com tokens seguros |
| Autorização | ✅ Spatie Permissions com roles granulares |
| SQL Injection | ✅ Query Builder com bindings seguros |
| XSS | ✅ Laravel Blade escaping |
| CSRF | ✅ Tokens CSRF (quando aplicável) |
| Dados sensíveis | ⚠️ .env deve ser adicionado ao .gitignore |

### Recomendações de Segurança

1. **Em produção:**
   - Altere todas as senhas padrão
   - Configure `APP_DEBUG=false`
   - Use HTTPS
   - Configure CORS corretamente
   - Adicione rate limiting

2. **Nunca exponha:**
   - Credenciais no código
   - Arquivo `.env` no repositório
   - Chaves de API em código

---

## Contato

**Desenvolvedor:** Lucas Vinicius

**Repositório:** https://github.com/seu-repo/multi-tenant-sales-system
