# 5. Migrations e schema

## Ordem

| Ordem | Migration | Cria |
|---|---|---|
| 1 | `0001_01_01_000001_create_cache_table.php` | `cache`, `cache_locks`: `database/migrations/0001_01_01_000001_create_cache_table.php:14` |
| 2 | `0001_01_01_000002_create_jobs_table.php` | `jobs`, `job_batches`, `failed_jobs`: `database/migrations/0001_01_01_000002_create_jobs_table.php:14` |
| 3 | `2026_08_27_150955_create_oauth_auth_codes_table.php` | `oauth_auth_codes`: `database/migrations/2026_08_27_150955_create_oauth_auth_codes_table.php:14` |
| 4 | `2026_08_27_150956_create_oauth_access_tokens_table.php` | `oauth_access_tokens`: `database/migrations/2026_08_27_150956_create_oauth_access_tokens_table.php:14` |
| 5 | `2026_08_27_150957_create_oauth_refresh_tokens_table.php` | `oauth_refresh_tokens`: `database/migrations/2026_08_27_150957_create_oauth_refresh_tokens_table.php:14` |
| 6 | `2026_08_27_150958_create_oauth_clients_table.php` | `oauth_clients`: `database/migrations/2026_08_27_150958_create_oauth_clients_table.php:14` |
| 7 | `2026_08_27_150959_create_oauth_device_codes_table.php` | `oauth_device_codes`: `database/migrations/2026_08_27_150959_create_oauth_device_codes_table.php:14` |
| 8 | `2026_08_27_161854_create_users_table.php` | `users`: `database/migrations/2026_08_27_161854_create_users_table.php:14` |
| 9 | `2026_08_27_161922_create_vehicles_table.php` | `vehicles`: `database/migrations/2026_08_27_161922_create_vehicles_table.php:14` |
| 10 | `2026_08_27_162024_create_events_table.php` | `events`: `database/migrations/2026_08_27_162024_create_events_table.php:14` |
| 11 | `2026_08_27_162041_create_service_records_table.php` | `service_records`: `database/migrations/2026_08_27_162041_create_service_records_table.php:14` |
| 12 | `2026_08_27_162054_create_user_profiles_table.php` | `user_profiles`: `database/migrations/2026_08_27_162054_create_user_profiles_table.php:14` |
| 13 | `2026_08_27_172140_create_event_vehicle_table.php` | `event_vehicle`: `database/migrations/2026_08_27_172140_create_event_vehicle_table.php:14` |

Não encontrei migrations posteriores que alterem ou corrijam tabelas anteriores.

## Índices e constraints encontrados

- `users.email` é unique: `database/migrations/2026_08_27_161854_create_users_table.php:17`.
- `vehicles.plate` é unique e limitado a sete caracteres: `database/migrations/2026_08_27_161922_create_vehicles_table.php:20`.
- Cache possui PK por chave e índices em expiração: `database/migrations/0001_01_01_000001_create_cache_table.php:15`.
- Filas indexam `queue`; failed jobs têm UUID unique e índice composto: `database/migrations/0001_01_01_000002_create_jobs_table.php:16`, `database/migrations/0001_01_01_000002_create_jobs_table.php:39`.
- Tabelas OAuth possuem PKs, índices e uniques próprios do Passport, mas os `client_id` não chamam `constrained()`: `database/migrations/2026_08_27_150956_create_oauth_access_tokens_table.php:15`.
- `service_records.vehicle_id` cria foreign key porque usa `foreignId()->references()->on()`: `database/migrations/2026_08_27_162041_create_service_records_table.php:17`.

## Relações que parecem FK, mas não são

Estas declarações começam com `unsignedBigInteger()`, que devolve `ColumnDefinition`, não `ForeignIdColumnDefinition`:

- `vehicles.user_id`: `database/migrations/2026_08_27_161922_create_vehicles_table.php:16`.
- `events.organizer_id`: `database/migrations/2026_08_27_162024_create_events_table.php:17`.
- `user_profiles.user_id`: `database/migrations/2026_08_27_162054_create_user_profiles_table.php:17`.
- `event_vehicle.vehicle_id`: `database/migrations/2026_08_27_172140_create_event_vehicle_table.php:19`.

Em Laravel, somente `ForeignIdColumnDefinition::references()` adiciona o comando de foreign key: `vendor/laravel/framework/src/Illuminate/Database/Schema/ForeignIdColumnDefinition.php:52`. Métodos desconhecidos em `ColumnDefinition` apenas viram atributos fluentes: `vendor/laravel/framework/src/Illuminate/Support/Fluent.php:299`.

`event_vehicle.event_id` ainda contém o typo `refences`, portanto também não cria constraint: `database/migrations/2026_08_27_172140_create_event_vehicle_table.php:17`.

## Ausências no schema

- Não há unique em `user_profiles.user_id`, apesar do model declarar `hasOne`.
- Não há unique composto `(event_id, vehicle_id)` na pivot; duplicidade é impedida apenas por consulta anterior ao `attach()`: `app/Http/Controllers/EventController.php:76`.
- Não há `onDelete('cascade')` nas relações.
- Não há migration de `sessions`, embora sessão use banco: `config/session.php:21`.
- Não há migration de `password_reset_tokens`, embora o broker aponte para essa tabela: `config/auth.php:100`.

## Rollback

Cada migration remove as tabelas que criou. A migration de filas remove `jobs`, depois `job_batches` e `failed_jobs`: `database/migrations/0001_01_01_000002_create_jobs_table.php:53`. As migrations não tratam dependências/cascade durante rollback; isso fica sensível às constraints que efetivamente existirem.

## Pontos de atenção

- O `.env.example` usa SQLite, mas o código de filtro usa `ILIKE`; migrations podem subir, mas as consultas de busca não são portáveis: `.env.example:23`, `app/Models/Event.php:30`.
- A validação de update aceita placa com oito caracteres, enquanto banco permite sete: `app/Http/Requests/UpdateVehicleRequest.php:22`, `database/migrations/2026_08_27_161922_create_vehicles_table.php:20`.
- `registration_date` é `date`; `now()` perde a parte de horário ao persistir: `database/migrations/2026_08_27_172140_create_event_vehicle_table.php:22`, `app/Http/Controllers/EventController.php:80`.
- Não rodei migrations para produzir esta análise.
