# 3. Autenticação e autorização

## Componentes

- Passport é dependência direta: `composer.json:11`; versão travada 13.7.6: `composer.lock:1419`.
- O guard padrão continua `web`; o guard `api` usa driver `passport`: `config/auth.php:18`, `config/auth.php:46`.
- Ambos usam o provider `users`, que carrega `App\Models\User` via Eloquent: `config/auth.php:69`.
- `User` implementa `OAuthenticatable` e usa `HasApiTokens`: `app/Models/User.php:12`, `app/Models/User.php:21`, `app/Models/User.php:23`.

Não há Sanctum nem biblioteca JWT configurada como mecanismo independente. JWT é detalhe interno transitivo do Passport.

## Grant usado e emissão

O projeto usa o grant `personal_access`. A chamada `createToken()` do trait cria um Personal Access Token: `app/Http/Controllers/Auth/AuthController.php:24`, `vendor/laravel/passport/src/HasApiTokens.php:101`, `vendor/laravel/passport/src/PersonalAccessTokenFactory.php:43`.

### Cadastro

1. `POST /api/register` é público: `routes/api.php:19`.
2. `RegisterRequest` exige nome, email único e senha mínima de oito caracteres: `app/Http/Requests/RegisterRequest.php:23`.
3. O controller grava a senha em `password_hash` usando bcrypt: `app/Http/Controllers/Auth/AuthController.php:18`.
4. `createToken('api-token')` cria o token: `app/Http/Controllers/Auth/AuthController.php:24`.
5. A resposta contém `{user, token}` com status 201: `app/Http/Controllers/Auth/AuthController.php:26`.

### Login

1. `POST /api/login` é público: `routes/api.php:21`.
2. `LoginRequest` exige email e senha: `app/Http/Requests/LoginRequest.php:23`.
3. O usuário é encontrado por email e a senha é comparada com `password_hash`: `app/Http/Controllers/Auth/AuthController.php:36`, `app/Http/Controllers/Auth/AuthController.php:38`.
4. Credencial inválida retorna 401; válida emite outro token pessoal: `app/Http/Controllers/Auth/AuthController.php:39`, `app/Http/Controllers/Auth/AuthController.php:44`.

Cada login cria um token novo. Não há reutilização nem revogação dos tokens anteriores.

## Validação do Bearer token

1. Rotas protegidas carregam `auth:api`: `routes/api.php:29`.
2. O alias `auth` resolve `Illuminate\Auth\Middleware\Authenticate`: `vendor/laravel/framework/src/Illuminate/Foundation/Configuration/Middleware.php:805`.
3. O parâmetro `api` seleciona o guard Passport: `config/auth.php:46`.
4. O Passport cria um `TokenGuard`: `vendor/laravel/passport/src/PassportServiceProvider.php:315`.
5. O Resource Server valida assinatura, expiração e revogação do Bearer token: `vendor/laravel/passport/src/Guards/TokenGuard.php:176`, `vendor/league/oauth2-server/src/AuthorizationValidators/BearerTokenValidator.php:130`.
6. O usuário é carregado pelo provider Eloquent e fica acessível por `$request->user()`: `config/auth.php:69`, `vendor/laravel/framework/src/Illuminate/Http/Request.php:675`.

Token ausente, inválido, expirado ou revogado não chega ao controller.

## Chaves e clients

Passport usa chaves assimétricas. A configuração permite fornecê-las por `PASSPORT_PRIVATE_KEY` e `PASSPORT_PUBLIC_KEY`: `config/passport.php:21`, `config/passport.php:31`. Sem essas variáveis, o pacote usa `oauth-private.key` e `oauth-public.key` no diretório de chaves, normalmente `storage/`: `vendor/laravel/passport/src/PassportServiceProvider.php:290`, `vendor/laravel/passport/src/Passport.php:433`.

Os arquivos `storage/oauth-private.key` e `storage/oauth-public.key` existem no ambiente inspecionado; seu conteúdo não foi lido nem reproduzido.

Clients ficam em `oauth_clients`. A tabela guarda UUID, owner polimórfico, segredo, provider, redirects, grants e revogação: `database/migrations/2026_08_27_150958_create_oauth_clients_table.php:14`. `createToken()` precisa encontrar um client cujo `grant_types` contenha `personal_access`: `vendor/laravel/passport/src/ClientRepository.php:61`.

Tokens emitidos ficam em `oauth_access_tokens`: `database/migrations/2026_08_27_150956_create_oauth_access_tokens_table.php:14`.

## Revogação e scopes

Não encontrei rota de logout, método de revogação ou chamada a `token()->revoke()` em `app/` e `routes/`. Os únicos usos de `createToken()` são cadastro e login: `app/Http/Controllers/Auth/AuthController.php:24`, `app/Http/Controllers/Auth/AuthController.php:44`.

Também não encontrei `Passport::tokensCan()`, scopes declarados ou middleware `scope`/`scopes`. `createToken()` é chamado sem o segundo argumento, portanto não solicita scopes: `app/Http/Controllers/Auth/AuthController.php:24`.

## Autorização

Não existem Policies, Gates ou pacote de roles. Os Form Requests retornam `true` em `authorize()`, por exemplo `app/Http/Requests/UpdateVehicleRequest.php:10`.

A autorização de domínio é feita com comparações manuais:

```php
if (! $vehicle || $vehicle->user_id !== $request->user()->id) {
    return response()->json(['message' => 'Não encontrado.'], 404);
}
```

Origem: `app/Http/Controllers/VehicleController.php:29`.

Retornar 404 em vez de 403 esconde se o recurso de outro usuário existe.

`users.is_admin` existe no banco, mas não foi encontrado em guards, gates, policies ou controllers: `database/migrations/2026_08_27_161854_create_users_table.php:20`.

## Pontos de atenção

- Login e cadastro não têm rate limiting.
- Não há revogação pública; tokens acumulam até expirar ou serem revogados fora desta API.
- Nenhuma duração de token é configurada em `AppServiceProvider`, que está vazio: `app/Providers/AppServiceProvider.php:20`.
- Não há verificação de email apesar de `email_verified_at` existir: `database/migrations/2026_08_27_161854_create_users_table.php:19`.
- A autorização manual está duplicada e pode divergir entre ações.
- Excluir usuário depende de constraints/cascatas que não estão corretamente definidas nas migrations de domínio.
