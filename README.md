# Vehicle Events API

API REST desenvolvida em Laravel para cadastro e gerenciamento de usuários, veículos, manutenções e eventos automotivos. Projeto desenvolvido como teste técnico para vaga de estagiário desenvolvedor.

## Sobre o projeto

Cada usuário autenticado pode cadastrar seu próprio perfil, veículos e o histórico de manutenções de cada veículo, além de organizar e se inscrever em eventos. Todo o acesso aos dados é isolado por usuário: cada um só visualiza e gerencia os próprios registros.

## Tecnologias

- PHP 8.3
- Laravel 13
- Eloquent ORM
- Laravel Passport (autenticação via OAuth2 / tokens)
- PostgreSQL

## Modelagem do banco de dados

| Tabela | Relacionamento |
|---|---|
| `users` → `user_profiles` | 1:1 |
| `users` → `vehicles` | 1:N |
| `vehicles` → `service_records` | 1:N |
| `users` (organizer) → `events` | 1:N |
| `events` ↔ `vehicles` | N:N (via `event_vehicle`) |

### Estrutura das tabelas

**users**: `id`, `name`, `email`, `password_hash`, `created_at`

**user_profiles**: `id`, `user_id` (FK, unique), `bio`, `instagram_handle`, `phone`

**vehicles**: `id`, `user_id` (FK), `make`, `model`, `year`, `license_plate`

**service_records**: `id`, `vehicle_id` (FK), `service_type` (int, mapeado via PHP Enum), `description`, `service_date`, `cost`

**events**: `id`, `organizer_id` (FK → users), `event_name`, `event_date`, `location`

**event_vehicle** (pivô): `id`, `event_id` (FK), `vehicle_id` (FK), `registration_date`

## Decisões técnicas

**Autenticação com Passport**
Requisito da tarefa. Implementado via Personal Access Tokens (grant mais simples do Passport), já que a API não envolve aplicações terceiras solicitando autorização — apenas o próprio usuário gerando um token para si mesmo.

**Isolamento de dados por usuário**
Rotas de recursos pessoais (`vehicles`, perfil) nunca recebem o id do dono pela URL. O dono é sempre `auth()->user()`, extraído do token — isso evita que um usuário troque um parâmetro na URL e acesse dado de outro usuário. Rotas de item específico (`show`, `update`, `destroy`) fazem checagem explícita de posse antes de qualquer operação.

**service_records aninhado em vehicles**
`/vehicles/{vehicle}/service-records`, pois uma manutenção não existe sem o veículo associado — diferente de `vehicles`, que é um recurso de primeiro nível.

**service_type como PHP Enum, não tabela separada**
Conjunto fixo e conhecido do domínio (revisão, troca de óleo, pneu, freio...). Optou-se por um enum backed em `int`, guardando o identificador numérico no banco e mantendo o mapeamento nome/label inteiramente no código, evitando o overhead de uma tabela + FK para um catálogo que não muda em runtime.

**make/model do veículo como string**
Avaliada a normalização em tabelas próprias (`makes`, `models`), mas mantido como string simples conforme o schema fornecido, dado o universo aberto de fabricantes/modelos possíveis.

**Rotas escritas manualmente, sem `Route::apiResource()`**
Decisão consciente para deixar explícito o mapeamento verbo HTTP → método do controller, servindo como demonstração de conhecimento do padrão REST para fins de avaliação técnica.

**Sem CRUD genérico de usuários**
A tarefa não previu um papel de administrador gerenciando outros usuários — cada usuário só acessa e edita os próprios dados via rota `/me`.

## Instalação

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan install:api --passport
```

Configure o banco de dados no arquivo `.env` antes de rodar as migrations.

## Autenticação

```
POST /api/register
POST /api/login
```

Ambas retornam um `access_token` que deve ser enviado nas demais requisições:

```
Authorization: Bearer {token}
Accept: application/json
```

## Testes

Collection de testes das rotas disponível via [Bruno](https://www.usebruno.com/).

## Status do projeto

Em desenvolvimento — acompanhamento de progresso e decisões técnicas disponível na aba [Issues](../../issues) e no [Project Board](../../projects) deste repositório.

<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

In addition, [Laracasts](https://laracasts.com) contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

You can also watch bite-sized lessons with real-world projects on [Laravel Learn](https://laravel.com/learn), where you will be guided through building a Laravel application from scratch while learning PHP fundamentals.

## Agentic Development

Laravel's predictable structure and conventions make it ideal for AI coding agents like Claude Code, Cursor, and GitHub Copilot. Install [Laravel Boost](https://laravel.com/docs/ai) to supercharge your AI workflow:

```bash
composer require laravel/boost --dev

php artisan boost:install
```

Boost provides your agent 15+ tools and skills that help agents build Laravel applications while following best practices.

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
