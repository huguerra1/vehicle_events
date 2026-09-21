# 7. Requests, Resources e DTOs

## Como a validação entra no fluxo

Controllers tipam parâmetros com subclasses de `FormRequest`. Antes da action executar, Laravel resolve o objeto, chama `authorize()` e valida `rules()`. Os controllers consomem apenas `$request->validated()`, por exemplo `app/Http/Controllers/Auth/AuthController.php:16` e `app/Http/Controllers/VehicleController.php:22`.

Todos os Form Requests retornam `true` em `authorize()`. Exemplo: `app/Http/Requests/StoreEventRequest.php:13`. Portanto, eles validam formato, mas não autorização de domínio.

## Regras

| Request | Regras |
|---|---|
| `LoginRequest` | `email`: obrigatório/email; `password`: obrigatório: `app/Http/Requests/LoginRequest.php:23`. |
| `RegisterRequest` | nome obrigatório até 255; email único; senha string mínima 8: `app/Http/Requests/RegisterRequest.php:23`. |
| `StoreVehicleRequest` | marca/modelo obrigatórios; ano inteiro com quatro dígitos; placa única até 7: `app/Http/Requests/StoreVehicleRequest.php:23`. |
| `UpdateVehicleRequest` | campos opcionais; placa única ignorando o ID da rota, mas aceita até 8: `app/Http/Requests/UpdateVehicleRequest.php:15`. |
| `StoreEventRequest` | nome, data e local obrigatórios: `app/Http/Requests/StoreEventRequest.php:23`. |
| `UpdateEventRequest` | os três campos opcionais por `sometimes`: `app/Http/Requests/UpdateEventRequest.php:23`. |
| `StoreServiceRecordRequest` | enum válido, descrição nullable, data obrigatória, custo numérico >= 0: `app/Http/Requests/StoreServiceRecordRequest.php:25`. |
| `UpdateServiceRecordRequest` | mesmos campos opcionais; descrição aceita null: `app/Http/Requests/UpdateServiceRecordRequest.php:25`. |
| `StoreUserProfileRequest` | bio nullable; Instagram/telefone nullable até 255: `app/Http/Requests/StoreUserProfileRequest.php:23`. |
| `UpdateUserProfileRequest` | mesmos campos com `sometimes`: `app/Http/Requests/UpdateUserProfileRequest.php:23`. |
| `UpdateUserRequest` | nome opcional; email opcional e único ignorando o ID da rota: `app/Http/Requests/UpdateUserRequest.php:24`. |

## Formato das respostas

Não encontrei classes em `app/Http/Resources/`, DTOs ou transformers. Os controllers usam `response()->json()` com models e collections diretamente: `app/Http/Controllers/UserController.php:14`, `app/Http/Controllers/EventController.php:18`.

Padrões encontrados:

- 200 para leitura e update: `app/Http/Controllers/VehicleController.php:35`, `app/Http/Controllers/VehicleController.php:48`.
- 201 para criação: `app/Http/Controllers/VehicleController.php:24`.
- 204 para delete: `app/Http/Controllers/VehicleController.php:61`.
- 404 com `{ "message": "Não encontrado." }` para inexistência ou falta de posse: `app/Http/Controllers/VehicleController.php:31`.
- 409 para perfil já existente ou veículo já inscrito: `app/Http/Controllers/UserProfileController.php:25`, `app/Http/Controllers/EventController.php:76`.
- 401 para credencial inválida: `app/Http/Controllers/Auth/AuthController.php:38`.

Erros de validação e demais exceptions da API são renderizados como JSON por `bootstrap/app.php:18`.

## Pontos de atenção

- `UpdateVehicleRequest` aceita placa de oito caracteres, mas criação e banco limitam sete: `app/Http/Requests/UpdateVehicleRequest.php:22`, `database/migrations/2026_08_27_161922_create_vehicles_table.php:20`.
- Datas validam apenas como `date`; não há regra de passado/futuro.
- `year` exige quatro dígitos, mas não verifica intervalo plausível.
- Instagram, telefone e placa não são normalizados.
- A API não possui envelope consistente, metadados, paginação ou versionamento de schema.
- Alterar `$hidden`, `$casts` ou atributos do model altera a resposta pública sem uma camada de Resource.
