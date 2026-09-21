# 10. Factories e seeders

## Ordem de execução

`DatabaseSeeder` chama, nesta ordem: usuários, perfis, veículos, serviços, eventos e pivot: `database/seeders/DatabaseSeeder.php:17`.

Essa ordem é necessária:

1. Perfis precisam de usuários.
2. Veículos precisam de usuários.
3. Serviços precisam de veículos.
4. Eventos precisam de usuários organizadores.
5. Inscrições precisam de eventos e veículos.

## Seeders

| Seeder | Dados gerados |
|---|---|
| `UsersSeeder` | Dez usuários: `database/seeders/UsersSeeder.php:12`. |
| `UserProfilesSeeder` | Um perfil para cada usuário existente: `database/seeders/UserProfilesSeeder.php:13`. |
| `VehiclesSeeder` | Entre um e três veículos para cada usuário: `database/seeders/VehiclesSeeder.php:13`. |
| `ServiceRecordsSeeder` | Entre um e quatro serviços para cada veículo: `database/seeders/ServiceRecordsSeeder.php:13`. |
| `EventsSeeder` | Cinco eventos com organizador aleatório: `database/seeders/EventsSeeder.php:13`. |
| `EventVehicleSeeder` | De dois a cinco veículos distintos por evento, limitado ao total disponível: `database/seeders/EventVehicleSeeder.php:13`. |

`DatabaseSeeder` usa `WithoutModelEvents`: `database/seeders/DatabaseSeeder.php:10`. Hoje não há observers/listeners próprios, mas isso impediria eventos de model durante o seeding caso fossem adicionados.

## Factories

### Usuário

Gera nome, email único, email já verificado, senha fixa `sysout300` com hash e remember token: `database/factories/UserFactory.php:24`. O único state próprio é `unverified()`: `database/factories/UserFactory.php:38`.

### Perfil

Gera bio, username prefixado com `@` e telefone formatado: `database/factories/UserProfileFactory.php:18`.

### Veículo

Escolhe marca e modelo de um mapa fixo, ano entre 2010 e 2026 e placa aleatória de sete caracteres: `database/factories/VehicleFactory.php:20`, `database/factories/VehicleFactory.php:34`.

### Serviço

Escolhe qualquer caso atual de `ServiceType`, data no último ano e custo entre 50 e 2000: `database/factories/ServiceRecordFactory.php:19`.

### Evento

Gera nome, data entre agora e seis meses e cidade: `database/factories/EventFactory.php:18`.

## Uso nos testes

Não encontrei uso dessas factories nos testes. Os dois testes atuais não criam dados: `tests/Feature/ExampleTest.php:13`, `tests/Unit/ExampleTest.php:12`.

## Pontos de atenção

- Todos os usuários seedados compartilham a senha conhecida `sysout300`.
- Factories de recursos filhos não definem relações por padrão; os seeders precisam injetar os IDs.
- `VehicleFactory` fixa o ano máximo em 2026; envelhece com o tempo: `database/factories/VehicleFactory.php:37`.
- Adicionar um caso ao enum faz a factory começar a produzi-lo automaticamente.
- Rodar seeders novamente sem limpar tabelas pode conflitar com uniques ou duplicar dados; eles não são idempotentes.
