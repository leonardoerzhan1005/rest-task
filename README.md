# Task REST API (Symfony)

## Описание
RESTful API для управления задачами на Symfony + Doctrine + PostgreSQL.

## Требования
- PHP >=8.2
- Composer
- PostgreSQL
- Symfony CLI (желательно)

## Установка
1. Клонируйте репозиторий:
   ```bash
   git clone https://github.com/leonardoerzhan1005/rest-task.git
   cd rest-task
   ```
2. Установите зависимости:
   ```bash
   composer install
   ```
3. Настройте подключение к БД в `.env`:
   ```env
   DATABASE_URL="postgresql://username:password@127.0.0.1:5432/task_db?serverVersion=15&charset=utf8"
   ```
4. Создайте базу данных и выполните миграции:
   ```bash
   php bin/console doctrine:database:create
   php bin/console doctrine:migrations:migrate
   ```
5. (Опционально) Загрузите тестовые данные:
   ```bash
   php bin/console doctrine:fixtures:load --no-interaction
   ```

## Запуск
```bash
symfony server:start
```
или
```bash
php -S 127.0.0.1:8000 -t public
```

## Тестирование
```bash
php bin/phpunit
```

## Примеры запросов

### Получить все задачи (с пагинацией и фильтром по статусу)
```
GET /api/tasks?page=1&limit=10&status=done
```

### Получить задачу по ID
```
GET /api/tasks/1
```

### Создать задачу
```
POST /api/tasks
Content-Type: application/json
{
  "title": "Новая задача",
  "description": "Описание задачи",
  "status": "new"
}
```

### Обновить задачу
```
PUT /api/tasks/1
Content-Type: application/json
{
  "title": "Обновлённая задача",
  "status": "in_progress"
}
```

### Удалить задачу
```
DELETE /api/tasks/1
```

## Аутентификация (JWT)
1. Получите токен:
   ```
   POST /api/login_check
   Content-Type: application/json
   {
     "username": "user@example.com",
     "password": "password"
   }
   ```
2. Используйте токен в заголовке:
   ```
   Authorization: Bearer <ваш_токен>
   ```

## Лицензия
MIT 