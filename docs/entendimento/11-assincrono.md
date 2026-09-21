# 11. Assíncrono, eventos e console

## Filas

O driver padrão é `database`: `config/queue.php:16`, `.env.example:38`. A conexão grava na tabela `jobs`, fila `default`, com `retry_after` de 90 segundos: `config/queue.php:38`.

As migrations criam:

- `jobs`: `database/migrations/0001_01_01_000002_create_jobs_table.php:14`.
- `job_batches`: `database/migrations/0001_01_01_000002_create_jobs_table.php:24`.
- `failed_jobs`: `database/migrations/0001_01_01_000002_create_jobs_table.php:37`.

Apesar da infraestrutura, não encontrei classes Job, uso de `dispatch()` ou implementação de `ShouldQueue` em `app/`, `routes/`, `bootstrap/`, `database/` ou `tests/`. Na prática, a aplicação atual executa os casos de uso de forma síncrona dentro do request.

## Events e listeners

Não encontrei Events ou Listeners próprios. O nome `Event` em `app/Models/Event.php:9` é um model de evento automotivo, não um evento do dispatcher Laravel.

`DatabaseSeeder` desabilita eventos de model durante o seeding: `database/seeders/DatabaseSeeder.php:10`, mas hoje não há observers de domínio para serem disparados.

## Observers e notifications

Não encontrei Observers nem classes Notification. `User` usa o trait `Notifiable`: `app/Models/User.php:11`, `app/Models/User.php:23`, mas não há chamada a `notify()` no código da aplicação.

## Commands e agendamento

O único command próprio é uma Closure `inspire`, herdada do esqueleto: `routes/console.php:6`. Não encontrei classes Command em `app/Console/Commands`.

Também não encontrei `withSchedule()` em `bootstrap/app.php` nem chamadas a `schedule()` em `routes/console.php`. Portanto, não há tarefas agendadas pela aplicação.

## Gatilhos atuais

| Componente | Gatilho |
|---|---|
| Queue database | Nenhum produtor encontrado. |
| Failed jobs | Só seria usada por workers caso Jobs fossem adicionados. |
| Notifications | Nenhum; apenas o trait está disponível. |
| Eventos de domínio | Nenhum. |
| `inspire` | Execução manual de `php artisan inspire`: `routes/console.php:6`. |

## Pontos de atenção

- Configurar `QUEUE_CONNECTION=database` não torna ações assíncronas sozinho; é preciso dispatch e worker.
- Se Jobs forem adicionados, `after_commit` está `false`: `config/queue.php:44`. Um Job pode ser consumido antes de uma transação de banco ser confirmada.
- Não há supervisão de worker ou scheduler documentada no repositório.
- A tabela de filas existe, mas isso não prova que um worker esteja rodando.
