# 14. Convenções e divergências

## Padrões seguidos

- Namespace PSR-4 padrão `App\`, `Database\Factories\` e `Database\Seeders\`: `composer.json:23`.
- Controllers separados por recurso.
- Form Requests separados para create/update.
- Relações e mass assignment declarados nos models.
- Respostas HTTP usam constantes de `Illuminate\Http\Response`, por exemplo `app/Http/Controllers/Auth/AuthController.php:29`.
- Falta de posse geralmente retorna 404 para não revelar existência: `app/Http/Controllers/VehicleController.php:31`.
- Rotas são explícitas em vez de `apiResource`: `routes/api.php:33`.

## Convenções próprias

- Actions usam `create` e `delete`, não `store` e `destroy`: `routes/api.php:54`, `routes/api.php:58`.
- Rotas usam `{id}` em vez de model binding: `routes/api.php:56`.
- JSON é produzido diretamente nos controllers, sem Resources: `app/Http/Controllers/VehicleController.php:17`.
- Ownership é verificado dentro de cada action.
- Campos e nomes de código são em inglês; mensagens de API são em português.

## Onde o estilo quebra

- Espaçamento entre argumentos das rotas é inconsistente: `routes/api.php:33`, `routes/api.php:35`.
- `ServiceType.php` possui namespace e classe indentados no nível raiz: `app/Enums/ServiceType.php:3`.
- Models possuem linhas em branco/imports redundantes e indentação variável: `app/Models/User.php:5`, `app/Models/Vehicle.php:11`.
- Métodos de controller e relacionamentos não declaram tipos de retorno.
- O comentário de ServiceRecord afirma relação 1:1, mas o código define N:1: `app/Models/ServiceRecord.php:29`, `app/Models/Vehicle.php:39`.
- A pivot contém o typo `refences`: `database/migrations/2026_08_27_172140_create_event_vehicle_table.php:17`.

## Divergências entre documentação e código

| README | Código real |
|---|---|
| Afirma isolamento de todos os dados: `README.md:7`. | `UserController@index` retorna todos os usuários e `EventController@index` todos os eventos: `app/Http/Controllers/UserController.php:14`, `app/Http/Controllers/EventController.php:16`. |
| Diz que perfil tem `user_id` unique: `README.md:31`. | Migration não chama `unique()`: `database/migrations/2026_08_27_162054_create_user_profiles_table.php:17`. |
| Veículo teria `make` e `license_plate`: `README.md:33`. | Schema usa `brand` e `plate`: `database/migrations/2026_08_27_161922_create_vehicles_table.php:17`. |
| Diz que usuário trabalha pela rota `/me`: `README.md:61`. | Existem `/user` e `/users/{id}`, não `/me`: `routes/api.php:13`, `routes/api.php:35`. |
| Diz que autenticação retorna `access_token`: `README.md:83`. | Controllers retornam a chave `token`: `app/Http/Controllers/Auth/AuthController.php:26`. |
| Tecnologias indicam PostgreSQL: `README.md:15`. | `.env.example` configura SQLite: `.env.example:23`. |

## Riscos para manter o estilo atual

- Copiar controllers atuais amplia a duplicação de autorização.
- Criar novos endpoints devolvendo models diretamente amplia o contrato implícito.
- Usar `unsignedBigInteger()->references()` em migrations repete a falsa foreign key.
- Confiar apenas no README leva a nomes de campos e rotas inexistentes.
- Rodar Pint produziria alterações extensas por causa das inconsistências de formatação; nenhum formatador foi executado nesta documentação.

## Orientação para código novo compatível

Enquanto a arquitetura não for deliberadamente alterada:

1. Use Form Request para payload.
2. Derive owner do usuário autenticado, não do corpo.
3. Retorne os mesmos status já usados: 201 create, 204 delete, 404 inexistência/sem posse.
4. Teste isolamento entre dois usuários.
5. Ao criar relações no banco, use `foreignId()->constrained()` e defina comportamento de delete explicitamente.
6. Não introduza novo formato JSON sem decidir se haverá Resources/versionamento.
