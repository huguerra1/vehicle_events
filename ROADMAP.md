# ROADMAP — vehicle_events

**Status:** EM ANDAMENTO · Prioridade 5 no portfólio

## Checklist de "pronto"
- [ ] CI no GitHub Actions (PHPUnit + Postgres) verde
- [x] Testes de feature dos controllers principais (Vehicle, Event)
- [ ] README com: o que é, como rodar em um comando, arquitetura, coleção de requisições
- [x] Sem segredos no código; `.env.example` documentado
- [ ] Dependabot ativo (composer + npm)
- [ ] Backlog restante registrado como issues

## Backlog
1. ~~Remover o arquivo de nome inválido que impedia o clone no Windows~~ (fase 1)
2. Workflow de CI: `composer install`, `php artisan test` com serviço Postgres
3. Testes para `ServiceRecord` e `UserProfile` (cobertura de autorização: um usuário não acessa dados de outro)
4. Coleção Postman/Insomnia ou documentação OpenAPI (ex.: Scribe)
5. `docker compose` com app + Postgres para rodar com um comando
6. Avaliar se `.agents/` e `.claude/skills` (gerados pelo Laravel Boost, idênticos) devem sair do versionamento e ser recriados com `php artisan boost:install`

## Log
- 2026-11-09 — Fase 1: arquivo inválido removido, licença MIT, regras de autonomia, roadmap.
