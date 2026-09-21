# 6. Enum `ServiceType`

## Casos

`ServiceType` é um backed enum de `int`: `app/Enums/ServiceType.php:5`.

| Valor | Caso | Label |
|---:|---|---|
| 1 | `Revisao` | Revisão |
| 2 | `TrocaOleo` | Troca de óleo |
| 3 | `TrocaPneu` | Troca de pneu |
| 4 | `Freio` | Freio |
| 5 | `Suspensao` | Suspensão |
| 6 | `Eletrica` | Elétrica |
| 7 | `Outro` | Outro |

Casos: `app/Enums/ServiceType.php:7`; labels: `app/Enums/ServiceType.php:15`.

## Onde participa do fluxo

1. O banco guarda inteiro em `service_records.service_type`: `database/migrations/2026_08_27_162041_create_service_records_table.php:18`.
2. O model converte inteiro em enum e enum em inteiro: `app/Models/ServiceRecord.php:22`.
3. Requests de criação e update validam com `Enum(ServiceType::class)`: `app/Http/Requests/StoreServiceRecordRequest.php:28`, `app/Http/Requests/UpdateServiceRecordRequest.php:28`.
4. A factory escolhe um caso e grava seu valor: `database/factories/ServiceRecordFactory.php:22`.
5. `GET /api/service-types` chama `options()` e devolve `[{id, name}]`: `routes/api.php:25`, `app/Enums/ServiceType.php:28`.

## Adicionar um caso novo

- Não exige migration porque a coluna é integer sem check constraint.
- A validação passa a aceitar o novo valor automaticamente.
- A factory pode começar a gerar o valor automaticamente.
- O endpoint público inclui o caso automaticamente por `self::cases()`.
- O `match` de `label()` precisa ganhar um braço; caso contrário, chamar `label()` no novo caso gera `UnhandledMatchError`: `app/Enums/ServiceType.php:17`.
- Clientes que assumem somente IDs 1–7 precisam ser atualizados.

## Dados inválidos

Como o banco não tem check constraint, um inteiro fora do enum pode ser inserido por SQL ou código que ignore o model. Ao hidratar `ServiceRecord`, o cast não consegue construir `ServiceType` para esse valor: `app/Models/ServiceRecord.php:23`.

## Pontos de atenção

- Labels estão no código e não usam tradução/localização.
- Renomear o caso PHP sem mudar seu valor preserva o banco, mas pode quebrar referências de código.
- Trocar o valor inteiro muda o significado de dados já armazenados.
- Remover um caso torna registros antigos inválidos para o cast.
