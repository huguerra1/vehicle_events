# 9. Camada de negócio

## O que existe

Não há uma camada nomeada de negócio. Não encontrei diretórios ou classes de Service, Action, Repository, Trait de domínio ou helper próprio em `app/`.

As regras estão distribuídas entre:

- Form Requests: formato dos dados, por exemplo `app/Http/Requests/StoreServiceRecordRequest.php:25`.
- Controllers: posse, duplicidade e fluxo, por exemplo `app/Http/Controllers/EventController.php:67`.
- Models: relações, casts e filtros, por exemplo `app/Models/Vehicle.php:21`.
- Enum: catálogo de tipos de serviço, em `app/Enums/ServiceType.php:5`.

## Regras identificadas

| Regra | Implementação |
|---|---|
| Email de usuário é único | Validação e índice: `app/Http/Requests/RegisterRequest.php:27`, `database/migrations/2026_08_27_161854_create_users_table.php:17`. |
| Usuário só altera/exclui a si mesmo | Comparação de ID: `app/Http/Controllers/UserController.php:32`, `app/Http/Controllers/UserController.php:45`. |
| Só existe um perfil por usuário | Check no controller: `app/Http/Controllers/UserProfileController.php:25`; não é garantido no banco. |
| Veículo pertence ao autenticado | Relação para criar/listar e comparação nas ações por ID: `app/Http/Controllers/VehicleController.php:15`, `app/Http/Controllers/VehicleController.php:31`. |
| Serviço só é acessível pelo dono do veículo | Navegação `serviceRecord->vehicle->user_id`: `app/Http/Controllers/ServiceRecordController.php:47`. |
| Só organizador altera/exclui evento | `app/Http/Controllers/EventController.php:45`, `app/Http/Controllers/EventController.php:58`. |
| Só veículo próprio pode ser inscrito | `app/Http/Controllers/EventController.php:72`. |
| Inscrição duplicada é conflito | Consulta antes do attach: `app/Http/Controllers/EventController.php:76`. |
| Organizador ou dono remove inscrição | `app/Http/Controllers/EventController.php:98`. |

## Filtros

`Vehicle::scopeSearch()` lê `search` do request e aplica `ILIKE` em marca, modelo e placa: `app/Models/Vehicle.php:21`. `Event::scopeSearch()` faz o mesmo para nome e local: `app/Models/Event.php:24`.

Os filtros não são um pacote externo. Eles acoplam Eloquent a `Illuminate\Http\Request`, dificultando reutilização por command/job e impedindo portabilidade direta para SQLite/MySQL.

## Acoplamentos e duplicações

- Controllers conhecem detalhes de ownership e schema.
- Models conhecem o request HTTP nos scopes.
- Contrato JSON depende diretamente dos models.
- A resposta 404 para falta de posse está repetida em diversos controllers.
- IDs de dono são fillable nos models, apesar de normalmente serem derivados do contexto autenticado: `app/Models/Vehicle.php:13`, `app/Models/Event.php:13`.

## Riscos

- Uma nova action pode esquecer a comparação manual e abrir acesso cruzado.
- Concorrência pode criar inscrições duplicadas porque não há unique no banco.
- Deletar usuário/veículo pode deixar órfãos ou falhar, dependendo do schema efetivo, porque não há cascatas declaradas.
- O README afirma isolamento total, mas `UserController@index` retorna todos os usuários: `README.md:7`, `app/Http/Controllers/UserController.php:14`.
