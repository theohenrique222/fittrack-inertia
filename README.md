# FitTrack

> Plataforma completa de gestão fitness para personal trainers e entusiastas. Monte treinos, planeje dietas e acompanhe seu progresso — tudo em uma interface moderna.

![FitTrack Landing](public/img/previews/landing-preview.png)

![FitTrack Dashboard](public/img/previews/dashboard-preview.png)

## Visão Geral

FitTrack é uma aplicação web moderna projetada para ajudar personal trainers a gerenciar seus clientes e entusiastas fitness a acompanhar seu progresso. A plataforma calcula metabolismo basal, macronutrientes e gera dietas personalizadas e sugestões de treino com inteligência artificial.

## Funcionalidades

- **Gestão de Clientes** — Cadastro, listagem, edição e exclusão de clientes com perfil completo
- **Cálculos Inteligentes** — Cálculo automático de metabolismo basal e macronutrientes baseado nas medidas corporais
- **Treinos com IA** — Geração de planos de treino personalizados utilizando inteligência artificial
- **Controle de Acesso** — Dashboards e permissões separados para treinadores e clientes
- **Interface Moderna** — Tema escuro construído com Tailwind CSS e componentes ShadCN
- **DataTables** — Tabelas de dados reutilizáveis com ações de edição e exclusão
- **Formulários em Sheets** — Modais limpos para criação e edição de dados

## Tecnologias

| Categoria | Tecnologia |
|-----------|------------|
| **Backend** | Laravel 13, PHP 8.3 |
| **Frontend** | Vue 3, TypeScript, Inertia.js v3 |
| **Banco de Dados** | MySQL / PostgreSQL |
| **UI** | Tailwind CSS v4, ShadCN Vue |
| **Autenticação** | Laravel Fortify |
| **Testes** | Pest v4 |

## Estrutura do Projeto

```
app/
├── Actions/              # Lógica de negócio (Store, Update, Destroy)
├── Http/Controllers/     # Controllers enxutos organizados por módulo
├── Http/Resources/       # Transformação de dados via Resources
├── Models/               # Models Eloquent
└── Contexts/             # Ações específicas por domínio

resources/js/
├── pages/                # Páginas Inertia.js + Vue 3
├── components/           # Componentes reutilizáveis (DataTable, Sheet, Buttons)
└── layouts/              # Componentes de layout
```

## Instalação

### Pré-requisitos

- Docker + Docker Compose instalados

### Passos

1. Clone o repositório:
   ```bash
   git clone https://github.com/theohenrique222/fittrack-inertia.git
   cd fittrack-inertia
   ```

2. Copie o arquivo de ambiente:
   ```bash
   cp .env.example .env
   ```

3. Configure o UID/GID do seu usuário (Linux):
   ```bash
   echo "WWWUSER=$(id -u)" >> .env
   echo "WWWGROUP=$(id -g)" >> .env
   ```

4. Inicie os containers:
   ```bash
   docker compose up -d --build
   ```

5. Instale as dependências:
   ```bash
   docker compose exec fittrack composer install
   docker compose exec fittrack npm install
   ```

6. Gere a chave da aplicação:
   ```bash
   docker compose exec fittrack php artisan key:generate
   ```

7. Execute as migrações:
   ```bash
   docker compose exec fittrack php artisan migrate
   ```

8. Inicie o servidor de desenvolvimento:
   ```bash
   docker compose exec fittrack composer run dev:docker
   ```

9. Acesse a aplicação em `http://localhost:8088`

### Serviços

| Serviço | Container | Porta | Descrição |
|---------|-----------|-------|-----------|
| `fittrack` | fittrack-app | 9000, 5173 | PHP 8.3-FPM + Node 22 + Composer |
| `nginx` | fittrack-nginx | 8088 | Proxy reverso |
| `mysql` | fittrack-mysql | 3307 | Banco de dados |

### Estrutura Docker

```
docker/
├── php/
│   ├── Dockerfile          # Imagem PHP 8.3 + Node 22
│   └── www.conf            # Configuração do PHP-FPM
├── nginx/
│   └── default.conf        # Configuração do Nginx
docker-compose.yml          # Orquestração dos serviços
.dockerignore               # Arquivos ignorados no build
```

### Comandos Úteis

| Comando | Descrição |
|---------|-----------|
| `docker compose exec fittrack bash` | Acessa o terminal do container |
| `docker compose exec fittrack composer install` | Instala dependências PHP |
| `docker compose exec fittrack npm install` | Instala dependências Node |
| `docker compose exec fittrack composer run dev:docker` | Inicia Vite + queue worker |
| `docker compose exec fittrack npm run dev` | Inicia apenas o Vite |
| `docker compose exec fittrack php artisan migrate` | Executa migrações |
| `docker compose exec fittrack php artisan test` | Executa os testes |
| `docker compose exec fittrack php artisan db:seed` | Executa seeders |
| `docker compose logs -f fittrack` | Visualiza logs da aplicação |
| `docker compose logs -f nginx` | Visualiza logs do Nginx |
| `docker compose logs -f mysql` | Visualiza logs do MySQL |
| `docker compose down -v` | Para containers e remove volumes |
| `docker compose build --no-cache` | Rebuild sem cache |

### Troubleshooting

**Erro de permissão:** execute `docker compose down && docker compose up -d --build` para reconstruir com as permissões corretas.

**Porta 3307 em uso:** altere `FORWARD_DB_PORT` no `.env` para outra porta disponível.

**Vite não conecta:** verifique se a porta `5173` está acessível e se o `npm run dev` está rodando no container.

## Padrões de Desenvolvimento

- **Padrão de Actions** — Lógica de negócio isolada em classes Action dedicadas
- **Controllers Enxutos** — Controllers delegam para Actions, mantendo-se mínimos
- **Clean Code** — Nomenclatura clara, separação de responsabilidades e tipagem
- **Inertia.js** — Comunicação fluida entre backend e frontend sem APIs REST
- **Vue 3 Composition API** — Componentes reativos com `<script setup>` e TypeScript

## Autor

**Theo Henrique**

- 📧 theodoro222@hotmail.com
- 💼 [linkedin.com/in/theohenrique](https://linkedin.com/in/theohenrique)
