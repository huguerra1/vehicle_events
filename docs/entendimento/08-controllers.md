# 8. Controllers

## Responsabilidades atuais

### `AuthController`

Registra usuário, verifica credenciais e emite tokens: `app/Http/Controllers/Auth/AuthController.php:14`, `app/Http/Controllers/Auth/AuthController.php:32`. Mistura persistência de usuário, hashing e OAuth, mas tem somente 51 linhas.

### `UserController`

Lista, exibe, atualiza e exclui usuários: `app/Http/Controllers/UserController.php:12`. Update e delete verificam identidade; index e show não restringem: `app/Http/Controllers/UserController.php:14`, `app/Http/Controllers/UserController.php:32`.

### `UserProfileController`

Opera sempre sobre `$request->user()->profile`: `app/Http/Controllers/UserProfileController.php:12`. Garante no código que só exista um perfil antes de criar: `app/Http/Controllers/UserProfileController.php:25`.

### `VehicleController`

CRUD de veículos do autenticado. Index e create partem da relação do usuário; ações por ID fazem `find()` e comparação manual: `app/Http/Controllers/VehicleController.php:13`, `app/Http/Controllers/VehicleController.php:27`.

### `ServiceRecordController`

CRUD de manutenção. Toda autorização passa pelo dono do veículo: `app/Http/Controllers/ServiceRecordController.php:14`, `app/Http/Controllers/ServiceRecordController.php:43`. Repete a mesma busca e condição em cinco métodos.

### `EventController`

É o controller mais carregado, com três responsabilidades:

1. CRUD de evento: `app/Http/Controllers/EventController.php:14`.
2. Autorização do organizador: `app/Http/Controllers/EventController.php:41`.
3. Inscrição e remoção de veículos: `app/Http/Controllers/EventController.php:67`.

Também possui um helper privado `save()` que chama `fill()` e `save()`: `app/Http/Controllers/EventController.php:107`.

## Fluxo de escrita típico

Exemplo de criação de serviço:

1. `StoreServiceRecordRequest` valida o payload.
2. O controller busca o veículo: `app/Http/Controllers/ServiceRecordController.php:29`.
3. Compara `vehicle.user_id` com usuário autenticado: `app/Http/Controllers/ServiceRecordController.php:31`.
4. Injeta `vehicle_id` no array validado: `app/Http/Controllers/ServiceRecordController.php:35`.
5. Cria com Eloquent e retorna 201: `app/Http/Controllers/ServiceRecordController.php:38`.

Não há service, action, repository, transaction ou evento de domínio entre controller e Eloquent.

## O que está gordo demais

- `EventController` concentra CRUD e o caso de uso N:N. `attachVehicle()` inclui busca, autorização, regra de duplicidade, persistência e releitura: `app/Http/Controllers/EventController.php:67`.
- `ServiceRecordController` repete resolução de posse em todos os métodos: `app/Http/Controllers/ServiceRecordController.php:16`, `app/Http/Controllers/ServiceRecordController.php:45`, `app/Http/Controllers/ServiceRecordController.php:56`.
- `VehicleController` repete a mesma condição três vezes: `app/Http/Controllers/VehicleController.php:29`, `app/Http/Controllers/VehicleController.php:40`, `app/Http/Controllers/VehicleController.php:53`.

Possíveis fronteiras futuras, sem alteração realizada:

- Policy para posse de Vehicle, ServiceRecord e Event.
- Route model binding escopado.
- Action `RegisterVehicleInEvent` para regra da pivot.
- Action/service de emissão e revogação de tokens.

## Pontos de atenção

- `EventController@show` e `index` não limitam organizador; isso pode ser regra de eventos públicos ou falta de isolamento. O código não esclarece: `app/Http/Controllers/EventController.php:14`, `app/Http/Controllers/EventController.php:21`.
- `attachVehicle()` permite ao dono do veículo inscrevê-lo em qualquer evento, sem aprovação do organizador: `app/Http/Controllers/EventController.php:72`.
- `detachVehicle()` autoriza organizador ou dono do veículo: `app/Http/Controllers/EventController.php:98`.
- O check de duplicidade seguido de `attach()` não é atômico e o banco não tem unique composto: `app/Http/Controllers/EventController.php:76`, `database/migrations/2026_08_27_172140_create_event_vehicle_table.php:14`.
- Nenhuma escrita envolvendo mais de uma operação usa transaction.
