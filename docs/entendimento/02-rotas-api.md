# 2. Rotas web e API

## Organização

`bootstrap/app.php` carrega `routes/web.php`, `routes/api.php` e `routes/console.php`: `bootstrap/app.php:9`. O framework adiciona o prefixo `/api` e o grupo `api` ao arquivo de API: `vendor/laravel/framework/src/Illuminate/Foundation/Configuration/ApplicationBuilder.php:211`.

Não há versionamento como `/api/v1`; também não há nomes de rotas. As rotas são escritas individualmente, sem `Route::apiResource()`: `routes/api.php:13`.

## Endpoints da API

`api` significa o grupo base; `auth:api` exige Bearer token validado pelo Passport.

| Método | URI | Middleware | Destino | Comportamento |
|---|---|---|---|---|
| GET | `/api/user` | `api`, `auth:api` | Closure | Retorna o usuário autenticado: `routes/api.php:13`. |
| POST | `/api/register` | `api` | `AuthController@register` | Valida, cria usuário e emite token: `routes/api.php:19`, `app/Http/Controllers/Auth/AuthController.php:14`. |
| POST | `/api/login` | `api` | `AuthController@login` | Confere email/senha e emite token: `routes/api.php:21`, `app/Http/Controllers/Auth/AuthController.php:32`. |
| GET | `/api/service-types` | `api` | Closure | Retorna os casos do enum como `id`/`name`: `routes/api.php:25`. |
| GET | `/api/users` | `api`, `auth:api` | `UserController@index` | Retorna todos os usuários: `routes/api.php:33`, `app/Http/Controllers/UserController.php:12`. |
| GET | `/api/users/{id}` | `api`, `auth:api` | `UserController@show` | Retorna qualquer usuário existente: `routes/api.php:35`, `app/Http/Controllers/UserController.php:17`. |
| PUT | `/api/users/{id}` | `api`, `auth:api` | `UserController@update` | Atualiza apenas o próprio usuário: `routes/api.php:39`, `app/Http/Controllers/UserController.php:28`. |
| DELETE | `/api/users/{id}` | `api`, `auth:api` | `UserController@delete` | Exclui apenas o próprio usuário: `routes/api.php:37`, `app/Http/Controllers/UserController.php:41`. |
| GET | `/api/profile` | `api`, `auth:api` | `UserProfileController@show` | Retorna o perfil do usuário autenticado: `routes/api.php:43`. |
| POST | `/api/profile` | `api`, `auth:api` | `UserProfileController@create` | Cria perfil se ainda não existir: `routes/api.php:45`. |
| PUT | `/api/profile` | `api`, `auth:api` | `UserProfileController@update` | Atualiza o perfil autenticado: `routes/api.php:47`. |
| GET | `/api/vehicles` | `api`, `auth:api` | `VehicleController@index` | Lista veículos próprios e aceita `search`: `routes/api.php:52`, `app/Http/Controllers/VehicleController.php:13`. |
| POST | `/api/vehicles` | `api`, `auth:api` | `VehicleController@create` | Cria veículo ligado ao usuário autenticado: `routes/api.php:54`. |
| GET | `/api/vehicles/{id}` | `api`, `auth:api` | `VehicleController@show` | Retorna veículo somente se pertencer ao usuário: `routes/api.php:56`. |
| PUT | `/api/vehicles/{id}` | `api`, `auth:api` | `VehicleController@update` | Atualiza veículo próprio: `routes/api.php:60`. |
| DELETE | `/api/vehicles/{id}` | `api`, `auth:api` | `VehicleController@delete` | Exclui veículo próprio: `routes/api.php:58`. |
| GET | `/api/vehicles/{vehicleId}/service-records` | `api`, `auth:api` | `ServiceRecordController@index` | Lista serviços de veículo próprio: `routes/api.php:65`. |
| POST | `/api/vehicles/{vehicleId}/service-records` | `api`, `auth:api` | `ServiceRecordController@create` | Cria serviço para veículo próprio: `routes/api.php:67`. |
| GET | `/api/service-records/{id}` | `api`, `auth:api` | `ServiceRecordController@show` | Retorna serviço cujo veículo seja próprio: `routes/api.php:69`. |
| PUT | `/api/service-records/{id}` | `api`, `auth:api` | `ServiceRecordController@update` | Atualiza serviço cujo veículo seja próprio: `routes/api.php:71`. |
| DELETE | `/api/service-records/{id}` | `api`, `auth:api` | `ServiceRecordController@delete` | Exclui serviço cujo veículo seja próprio: `routes/api.php:73`. |
| GET | `/api/events` | `api`, `auth:api` | `EventController@index` | Lista todos os eventos e aceita `search`: `routes/api.php:77`. |
| POST | `/api/events` | `api`, `auth:api` | `EventController@create` | Cria evento com o autenticado como organizador: `routes/api.php:79`. |
| GET | `/api/events/{id}` | `api`, `auth:api` | `EventController@show` | Retorna qualquer evento existente: `routes/api.php:81`. |
| PUT | `/api/events/{id}` | `api`, `auth:api` | `EventController@update` | Atualiza somente se o autenticado for organizador: `routes/api.php:83`. |
| DELETE | `/api/events/{id}` | `api`, `auth:api` | `EventController@delete` | Exclui somente se o autenticado for organizador: `routes/api.php:85`. |
| POST | `/api/events/{eventId}/vehicles/{vehicleId}` | `api`, `auth:api` | `EventController@attachVehicle` | Inscreve veículo próprio no evento: `routes/api.php:87`. |
| DELETE | `/api/events/{eventId}/vehicles/{vehicleId}` | `api`, `auth:api` | `EventController@detachVehicle` | Organizador ou dono do veículo remove a inscrição: `routes/api.php:89`. |

## Web, health e console

| Tipo | Entrada | Middleware | Destino |
|---|---|---|---|
| Web | `GET /` | `web` | Retorna a view padrão `welcome`: `routes/web.php:5`. |
| Health | `GET /up` | globais; exceção à manutenção | Registrado por `bootstrap/app.php:13`; a implementação vem de `vendor/laravel/framework/src/Illuminate/Foundation/Configuration/ApplicationBuilder.php:223`. |
| Console | `php artisan inspire` | não usa pipeline HTTP | Closure definida em `routes/console.php:6`. |

## Diferença prática entre web e API

- `web` ativa cookies, sessão e CSRF: `vendor/laravel/framework/src/Illuminate/Foundation/Configuration/Middleware.php:484`.
- `api` é stateless neste projeto e contém apenas bindings: `vendor/laravel/framework/src/Illuminate/Foundation/Configuration/Middleware.php:495`.
- Exceptions de URLs `api/*` são renderizadas como JSON: `bootstrap/app.php:18`.
- A autenticação da API não vem do grupo base; é adicionada explicitamente como `auth:api`: `routes/api.php:29`.

## Pontos de atenção

- Login e cadastro não têm throttle: `routes/api.php:19`, `routes/api.php:21`.
- `GET /users` e `GET /users/{id}` expõem todos os usuários a qualquer autenticado: `app/Http/Controllers/UserController.php:12`, `app/Http/Controllers/UserController.php:17`.
- Não há paginação; os índices usam `all()` ou `get()`: `app/Http/Controllers/UserController.php:14`, `app/Http/Controllers/EventController.php:16`.
- Não existe route model binding efetivo; IDs inválidos são tratados manualmente.
- Os nomes `create` e `delete` divergem das ações REST convencionais `store` e `destroy`.
