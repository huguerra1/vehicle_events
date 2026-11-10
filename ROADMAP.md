# ROADMAP — vehicle_events

**Status:** PRONTO PARA REVISÃO · Prioridade 5 no portfólio

## Checklist de "pronto"
- [x] CI no GitHub Actions verde: PHPUnit (SQLite em memória) e ambiente Docker com a coleção do Postman rodando no newman
- [x] Testes de feature dos controllers principais (Vehicle, Event)
- [x] README com: o que é, como rodar em um comando, arquitetura, coleção de requisições
- [x] Sem segredos no código; `.env.example` documentado
- [x] Dependabot ativo (composer + npm)
- [x] Backlog restante registrado como issues (#14 a #16)

## Backlog
1. ~~Remover o arquivo de nome inválido que impedia o clone no Windows~~ (fase 1)
2. ~~Workflow de CI: `composer install`, `php artisan test`~~ (feito com SQLite em memória, como no phpunit.xml)
3. ~~Testes para `ServiceRecord` e `UserProfile` (cobertura de autorização: um usuário não acessa dados de outro)~~ (também `User` e `Auth`)
4. ~~Coleção Postman/Insomnia ou documentação OpenAPI (ex.: Scribe)~~ (`docs/vehicle_events.postman_collection.json`)
5. ~~`docker compose` com app + Postgres para rodar com um comando~~ (`Dockerfile`, `docker-compose.yml` e `docker/entrypoint.sh`)
6. Avaliar se `.agents/` e `.claude/skills` (gerados pelo Laravel Boost, idênticos) devem sair do versionamento e ser recriados com `php artisan boost:install`

## Log
- 2026-11-09 — Fase 1: arquivo inválido removido, licença MIT, regras de autonomia, roadmap.
- 2026-11-10 — Testes de feature de autorização para ServiceRecord, UserProfile, User e Auth.
- 2026-11-10 — Configuração Docker (`Dockerfile`, `docker-compose.yml`, `docker/entrypoint.sh`, `.dockerignore`) e README de portfólio completo.
- 2026-11-10 — Índice único em `user_profiles.user_id` (garante o 1:1 também com requisições simultâneas).
- 2026-11-10 — Coleção Postman v2.1 (`docs/vehicle_events.postman_collection.json`) cobrindo todas as rotas da API.
- 2026-11-10 — Backlog restante virou issues (#14–#16). Status: pronto para revisão.
