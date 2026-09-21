# Entendimento do projeto Vehicle Events

Este diretório documenta o comportamento encontrado no código em 1º de setembro de 2026. Ele descreve o estado atual; não pressupõe que o README da raiz esteja sincronizado com a implementação.

## Índice

1. [Entrypoints, ciclo de vida e middlewares](01-entrypoints-ciclo-de-vida.md)
2. [Rotas web e API](02-rotas-api.md)
3. [Autenticação e autorização](03-autenticacao-autorizacao.md)
4. [Models e relacionamentos](04-models-relacionamentos.md)
5. [Migrations e schema](05-migrations.md)
6. [Enum `ServiceType`](06-enums.md)
7. [Requests, Resources e DTOs](07-requests-resources-dtos.md)
8. [Controllers](08-controllers.md)
9. [Camada de negócio](09-camada-de-negocio.md)
10. [Factories e seeders](10-factories-seeders.md)
11. [Assíncrono, eventos e console](11-assincrono.md)
12. [Configuração e ambiente](12-configuracao.md)
13. [Testes](13-testes.md)
14. [Convenções e divergências](14-convencoes.md)

## Stack confirmada

- PHP `^8.3` e Laravel 13; a instalação travada está em Laravel 13.29.0: `composer.json:9`, `composer.json:10`, `composer.lock:1192`.
- Passport 13.7.6 com Personal Access Tokens: `composer.lock:1419`, `app/Http/Controllers/Auth/AuthController.php:24`.
- PostgreSQL no ambiente atual, embora `.env.example` aponte para SQLite: `README.md:15`, `.env.example:23`.
- Queue, cache e sessão configurados com driver de banco: `.env.example:30`, `.env.example:38`, `.env.example:40`.
- PHPUnit 12.5.33, Mockery 1.6.15 e Faker 1.24.1: `composer.lock:8281`, `composer.lock:7580`, `composer.lock:7094`.
- Vite 8 e Tailwind 4 declarados, mas a instalação npm está incompleta: `package.json:5`.

## As 10 coisas que você precisa saber antes de alterar o código

1. **O isolamento de dados não é transversal.** Veículos, perfis e serviços fazem checagens manuais, mas qualquer usuário autenticado lista todos os usuários e todos os eventos: `app/Http/Controllers/UserController.php:12`, `app/Http/Controllers/EventController.php:14`.
2. **Não há rate limiting nas APIs.** O grupo `api` contém somente bindings; inclusive login e cadastro ficam sem `throttle`: `bootstrap/app.php:15`, `routes/api.php:19`, `routes/api.php:21`.
3. **Não existe logout ou revogação exposta pela aplicação.** Só foram encontrados os dois pontos que criam tokens: `app/Http/Controllers/Auth/AuthController.php:24`, `app/Http/Controllers/Auth/AuthController.php:44`.
4. **Várias relações das migrations não criam foreign keys reais.** `unsignedBigInteger(...)->references(...)` não usa `ForeignIdColumnDefinition`; a pivot ainda contém `refences`: `database/migrations/2026_08_27_161922_create_vehicles_table.php:16`, `database/migrations/2026_08_27_172140_create_event_vehicle_table.php:17`.
5. **A relação perfil–usuário não é 1:1 no banco.** O model usa `hasOne`, mas `user_profiles.user_id` não é `unique`: `app/Models/User.php:43`, `database/migrations/2026_08_27_162054_create_user_profiles_table.php:17`.
6. **O template SQLite conflita com os filtros.** Os scopes usam `ILIKE`, operador de PostgreSQL: `.env.example:23`, `app/Models/Vehicle.php:27`, `app/Models/Event.php:30`.
7. **A sessão usa banco, mas não há migration de `sessions`.** A página `/` atravessa `StartSession`; as migrations locais só criam cache, filas, OAuth e tabelas de domínio: `config/session.php:21`, `routes/web.php:5`.
8. **A camada de negócio está nos controllers e models.** Não existem Services, Actions, Repositories, Policies ou DTOs; a inscrição em evento inteira está no controller: `app/Http/Controllers/EventController.php:67`.
9. **As respostas expõem models diretamente.** Não existem API Resources; mudar casts, hidden ou relações pode mudar o contrato JSON: `app/Http/Controllers/VehicleController.php:17`, `app/Models/User.php:31`.
10. **A cobertura automatizada não protege os fluxos da aplicação.** Existem apenas os testes de exemplo do esqueleto: `tests/Feature/ExampleTest.php:13`, `tests/Unit/ExampleTest.php:12`.

## Ausências verificadas

Não foram encontrados em `app/`, `routes/`, `bootstrap/`, `database/` ou `tests/`: Policies, Gates próprios, Services, Actions, Repositories, Resources, DTOs, Jobs, Events próprios, Listeners, Observers, Notifications próprias ou tarefas agendadas.

Há um arquivo rastreado estranho na raiz, com saída de Tinker e não código executável: `cription' => 'teste direto no tinker']);:1`.
