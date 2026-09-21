# 13. Testes

## Ferramentas

- PHPUnit 12.5.33: `composer.lock:8281`.
- Mockery 1.6.15: `composer.lock:7580`.
- Faker 1.24.1: `composer.lock:7094`.
- O Composer oferece `composer test`, que limpa config e chama `php artisan test`: `composer.json:48`.

Não encontrei Pest como dependência instalada. A entrada `allow-plugins` menciona `pestphp/pest-plugin`, mas não instala o pacote: `composer.json:80`.

## Ambiente de teste

`phpunit.xml` configura:

- `APP_ENV=testing`: `phpunit.xml:21`.
- bcrypt com custo 4: `phpunit.xml:23`.
- cache, sessão e mail em memória: `phpunit.xml:25`, `phpunit.xml:29`, `phpunit.xml:31`.
- queue síncrona: `phpunit.xml:30`.
- SQLite em memória: `phpunit.xml:26`.

## Cobertura existente

### Feature

O único teste faz `GET /` e espera status 200: `tests/Feature/ExampleTest.php:13`. `RefreshDatabase` está comentado: `tests/Feature/ExampleTest.php:5`.

### Unit

O único teste afirma que `true` é `true`: `tests/Unit/ExampleTest.php:12`.

Não encontrei testes para:

- cadastro, login ou Passport;
- autorização/isolamento entre usuários;
- CRUDs;
- filtros;
- enum e casts;
- migrations e constraints;
- factories/seeders;
- inscrição concorrente em evento;
- respostas de erro e validação.

## Como rodar

```bash
composer test
```

ou:

```bash
php artisan test
```

O comando não foi executado durante o reconhecimento.

## Pontos de atenção

- Testes usam SQLite, mas os scopes usam `ILIKE`; testes de busca falhariam ou exigiriam adaptação: `app/Models/Vehicle.php:27`, `app/Models/Event.php:30`.
- Não há configuração de client/chaves Passport específica para teste.
- As factories existem, mas nenhum teste as utiliza.
- O teste de `/` pode depender da sessão em banco no ambiente normal; em teste, `SESSION_DRIVER=array` mascara a ausência da migration de `sessions`.
- Status verde hoje só comprova a página inicial e uma asserção trivial.
