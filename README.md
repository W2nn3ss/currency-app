# currency-app

## Развертывание приложения

Для развертывания приложения необходимо получить данные приложения из репозитория GitHub с помощью кнопки "Code".

После того, как все файлы из репозитория будут скачены, необходимо открыть консоль и выполнить следующие команды:

```shell
composer update
php artisan initialize:env
php artisan key:generate
```
composer update обновит зависимости приложения.
php artisan initialize:env скопирует файл .env.example и создаст файл .env.
php artisan key:generate сгенерирует ключ для приложения Laravel и добавит его в файл .env.

## Правки файла .env
Откройте файл .env и настройте соединение с Redis, кэширование и выполнение очередей. Внесите следующие изменения:
```shell
CACHE_DRIVER=redis
QUEUE_CONNECTION=redis

REDIS_HOST=your_redis_host
REDIS_PASSWORD=your_redis_password
REDIS_PORT=your_redis_port
```
Замените your_redis_host, your_redis_password и your_redis_port на свои настройки подключения к Redis.

## Работа приложения
## Основные команды:

```shell
php artisan send:active - запускает очередь для получения данных за последние 180 дней. Можно добавить параметры, например:
php artisan send:active 2023-05-01 USD - Эта команда начнет отсчет от указанной даты и получит данные по указанной валюте. Данные сохраняются в кэше Redis.
php artisan send:get_currency - команда для получения курса за определенный день и его разницы за предыдущий. Пример:

php artisan send:get_currency 2023-09-17 USD RUR
php artisan queue:work - запуск воркера для работы с очередью. Вы также можете настроить оптимизацию с помощью сервера, чтобы постоянно слушать очереди.
```
