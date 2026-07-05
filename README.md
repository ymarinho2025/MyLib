# MyLib — padrão PHP-web-app

Projeto reorganizado para seguir a lógica do repositório `ymarinho2025/PHP-web-app`:

- `api/*.php`: páginas e endpoints PHP públicos.
- `public/css` e `public/js`: arquivos estáticos.
- `src/Controllers`: regras de negócio, conexão e controllers.
- `composer.json`: dependência `firebase/php-jwt`.
- `vercel.json`: rotas no mesmo formato do PHP-web-app.

## Instalação local sem Apache

```bash
composer install
cp .env.example .env
php -S localhost:8000 -t api
```

Acesse:

```text
http://localhost:8000
```

## NeonDB

No `.env`, coloque sua URL:

```env
DATABASE_URL="postgresql://usuario:senha@host/neondb?sslmode=require"
JWT_SECRET="troque_por_uma_chave_grande"
```

## Criar tabelas e inserir biblioteca

Pelo navegador:

```text
http://localhost:8000/migrate.php
```

Ou pelo terminal:

```bash
php src/Controllers/migrate.php
```

## Rotas principais

### Páginas

- `/index.php`
- `/login-page.php`
- `/cadastro.php`
- `/home.php`
- `/explorar.php`
- `/admin.php`

### API

- `POST /register.php`
- `POST /login.php`
- `GET /me.php`
- `GET /books.php?q=`
- `POST /add-book.php`
- `POST /update-book.php`
- `POST /comment.php`
- `GET /users.php?q=`
- `GET /profile.php?username=yuri`
- `POST /update-profile.php`
- `POST /pix.php`
- `POST /follow.php`
- `POST /unfollow.php`
- `GET /feed.php`
- `POST /gift.php`

## Comandos para subir no GitHub

```bash
git clone https://github.com/ymarinho2025/MyLib.git
cd MyLib
# copie os arquivos desta pasta para dentro do repositório
composer install
git add .
git commit -m "Reorganiza MyLib no padrão PHP-web-app"
git push origin main
```
