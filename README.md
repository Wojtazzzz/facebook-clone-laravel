# Surface App

<img align="right" src="/public/Logo.svg" height="150px" alt="Surface App Logo">

**Surface App** is simple clone of Facebook app.

-   friendships system
-   posts, comments, likes
-   real-time chat
-   pokes
-   own profiles

[Live Demo](https://surface-app.site/)

## Requirements - backend

| Name                            | Version |
| ------------------------------- | ------- |
| [PHP](https://www.php.net/)     | ^8.0    |
| [MySQL](https://www.mysql.com/) | ^8.0    |
| [Composer](https://getcomposer.org/) | ^2.4    |

## Built with - backend

-   ![PHP](https://img.shields.io/static/v1?style=for-the-badge&message=PHP&color=777BB4&logo=PHP&logoColor=FFFFFF&label=)
-   ![MySQL](https://img.shields.io/static/v1?style=for-the-badge&message=MySQL&color=4479A1&logo=MySQL&logoColor=FFFFFF&label=)
-   ![Laravel](https://img.shields.io/static/v1?style=for-the-badge&message=Laravel&color=FF2D20&logo=Laravel&logoColor=FFFFFF&label=)
-   ![Pusher](https://img.shields.io/static/v1?style=for-the-badge&message=Pusher&color=300D4F&logo=Pusher&logoColor=FFFFFF&label=)

## Run Locally - backend

- Clone github repository

```bash
gh repo clone Wojtazzzz/facebook-clone-laravel
```

- Install dependencies

```bash
cd facebook-clone-laravel
```

```bash
composer install
```

- Duplicate **.env.example** as **.env**, fill it by your own variables. You can also create **.env.testing** for tests environment
- Generate app key

```bash
php artisan key:generate
```

- Create storage link

```bash
php artisan storage:link
```

- Run migrations

```bash
php artisan migrate
```

- Run local server

```bash
php artisan serve
```

## Todo

-   marketplace
-   sharing posts

## License

[![MIT license](https://img.shields.io/badge/License-MIT-blue.svg)](https://lbesson.mit-license.org/)
