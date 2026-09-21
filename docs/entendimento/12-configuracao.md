# 12. Configuração e ambiente

## Variáveis críticas

| Variável | Onde entra | Se estiver errada |
|---|---|---|
| `APP_KEY` | Criptografia da aplicação: `config/app.php:98`, `.env.example:3` | Cookies e dados criptografados não podem ser lidos; chave vazia impede operações criptográficas. |
| `APP_ENV` | Seleciona ambiente: `config/app.php:29` | Pode ativar comportamento de ambiente inadequado. |
| `APP_DEBUG` | Exibição de detalhes de erro: `config/app.php:42` | `true` em produção pode expor stack traces e dados sensíveis. |
| `APP_URL` | Geração de URLs e filesystem público: `config/app.php:55`, `config/filesystems.php:44` | URLs geradas, redirects e links de storage ficam incorretos. |
| `APP_LOCALE` | Locale padrão: `config/app.php:81` | Mensagens/formatos usam locale inesperado; o exemplo continua `en`: `.env.example:7`. |
| `DB_CONNECTION` e `DB_*` | Conexão Eloquent: `config/database.php:20`, `config/database.php:87` | A API não lê usuários, tokens nem domínio. SQLite também quebra filtros com `ILIKE`. |
| `CACHE_STORE` | Cache padrão: `config/cache.php:18` | Store inexistente ou tabela ausente gera falhas nas operações de cache/locks. |
| `QUEUE_CONNECTION` | Backend de filas: `config/queue.php:16` | Jobs futuros podem executar sincronamente, acumular em backend errado ou falhar. |
| `SESSION_DRIVER` | Sessões web: `config/session.php:21` | Com `database`, exige tabela `sessions`, que não tem migration local. |
| `PASSPORT_PRIVATE_KEY` | Assinatura de tokens: `config/passport.php:31` | Emissão falha ou tokens são assinados com chave errada. |
| `PASSPORT_PUBLIC_KEY` | Verificação de tokens: `config/passport.php:33` | Bearer tokens válidos deixam de ser aceitos. |
| `PASSPORT_CONNECTION` | Banco das tabelas OAuth: `config/passport.php:46` | Tokens e clients podem ser procurados em banco diferente dos usuários. |
| `LOG_CHANNEL`, `LOG_STACK`, `LOG_LEVEL` | Destino e verbosidade de logs: `config/logging.php:21`, `config/logging.php:55` | Erros podem desaparecer ou dados demais podem ser registrados. |
| `MAIL_MAILER` | Transporte de email: `config/mail.php:17` | O padrão `log` não envia email real. Hoje não há mailables/notifications próprias. |
| `FILESYSTEM_DISK` | Disco padrão: `config/filesystems.php:16` | Escritas futuras podem ir para disco privado, público ou remoto incorreto. |

## Estado indicado pelo template

`.env.example` define:

- aplicação local e debug ligado: `.env.example:2`, `.env.example:4`;
- SQLite: `.env.example:23`;
- sessão, fila e cache no banco: `.env.example:30`, `.env.example:38`, `.env.example:40`;
- broadcasting e mail apenas em log: `.env.example:36`, `.env.example:50`;
- filesystem local: `.env.example:37`.

O ambiente carregado durante o reconhecimento informou PostgreSQL, não SQLite. O `.env` real não foi reproduzido para não expor credenciais.

## Banco e Redis

`config/database.php` possui conexões SQLite, MySQL, MariaDB, PostgreSQL e SQL Server: `config/database.php:33`. A configuração Redis existe em `config/database.php:146`, mas cache e queue usam database no template.

## Passport

Sem variáveis de chave, Passport usa arquivos locais. O ambiente contém `storage/oauth-private.key` e `storage/oauth-public.key`; seus conteúdos não foram lidos. A conexão OAuth herda o banco padrão se `PASSPORT_CONNECTION` estiver ausente: `config/passport.php:46`.

## Pontos de atenção

- `.env.example` não lista `PASSPORT_PRIVATE_KEY`, `PASSPORT_PUBLIC_KEY` nem `PASSPORT_CONNECTION`, embora `config/passport.php` aceite essas variáveis.
- `.env.example` usa SQLite, enquanto README e ambiente usam PostgreSQL: `.env.example:23`, `README.md:15`.
- Timezone está fixado em UTC e não vem do ambiente: `config/app.php:68`.
- A API retorna mensagens próprias em português, mas locale padrão é inglês.
- `APP_DEBUG=true` é adequado apenas para desenvolvimento.
