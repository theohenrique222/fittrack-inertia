# FitTrack 🚀

-----

#### FitTrack é um sistema web em desenvolvimento que será futuramente expandido para React Native, transformando-se em um app completo de personal trainer e nutrição.

#### O diferencial do FitTrack é que qualquer usuário pode utilizar o sistema, informando suas medidas corporais, que serão usadas para gerar cálculos de metabolismo basal, macronutrientes e dietas personalizadas, além de treinos gerados por inteligência artificial.

-----

### Tecnologias Utilizadas:
- **Backend**: Laravel 13
- **Frontend**: Vue 3 + TypeScript + Inertia.js
- **Banco de Dados**: MySQL/PostgreSQL
- **UI**: Tailwind CSS + ShadCN + Componentes customizados (Sheets, DataTables, Buttons)
- **Autenticação**: Laravel Fortify

-----

### Funcionalidades:
- Cadastro, listagem, edição e exclusão de **Clientes** e **Treinadores**
- Cálculo automático de metabolismo basal e macronutrientes baseado nas medidas do usuário
- Geração automática de dietas e treinos personalizados via Inteligência Artificial
- Interface dinâmica com DataTables, incluindo ações de **Editar** e **Deletar**
- Formulários em Sheets/Modais para criação e edição de dados
- Controle de roles e permissões para diferentes tipos de usuários
- Código modular e escalável, seguindo padrões de **Clean Code**

-----

### Estrutura do Projeto:
app/
├─ Actions/          # Lógica de negócio separada (Store, Update, Destroy)
├─ Http/Controllers/ # Controllers organizados por módulo
├─ Http/Resources/   # Transformação de dados via Resources
resources/js/
├─ pages/            # Views do Inertia.js + Vue 3
└─ components/       # Componentes reutilizáveis (DataTable, Sheet, Buttons)


-----

### Instalação:
1. Clone o repositório: `git clone <repo-url>`
2. Acesse a pasta do projeto: `cd fittrack`
3. Instale as dependências do backend: `composer install`
4. Configure o arquivo `.env` com as credenciais do banco de dados
5. Crie sua chave de aplicativo: `php artisan key:generate`
6. Execute as migrações: `php artisan migrate`
7. Instale as dependências do frontend: `npm install`
8. Inicialize o servidor de desenvolvimento: `npm run dev` ou `php artisan serve`
9. Acesse `http://localhost:8000` para visualizar o sistema
10. Crie uma conta como super admin com o comando: `php artisan app:create-super-admin`

-----

### Padrão de Desenvolvimento
- **Clean Code**: Separação de responsabilidades, tipagem e modularidade
- **Actions**: Lógica de negócio isolada em classes específicas (Store, Update, Destroy)
- **Inertia.js**: Comunicação fluida entre backend e frontend, sem necessidade de APIs REST
- **Vue 3 + TypeScript**: Tipagem completa e reatividade com `<script setup>`
- **DataTables e Sheets**: Componentes reutilizáveis e escaláveis para listas e formulários
- **IA**: Integração futura para geração automática de treinos e dietas

-----

### Contato
- **Desenvolvido** por *Theo Henrique*
- 📧 Email: theodoro222@hotmail.com
- 💼 LinkedIn: [linkedin.com/in/theohenrique](https://linkedin.com/in/theohenrique)
