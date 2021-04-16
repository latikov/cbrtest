# Тестовое задание CbrTest

Установка:

```shell
composer install
```
скопировать .env.example в .env и прописать подключение к БД

```shell
php artisan migrate
```

Далее есть возможность предварительно подготовить кеш за определенный период времени, например с начала года:

```shell
php artisan cbr:prepare-cache 2021-01-01
```

#API

###Получение курса валют на определенный день

```json
POST {{api}}/rates
Content-Type: application/json

{
  "date": "2021-04-15",
  "codes": ["USD", "EUR"]
}
```

```json
{
  "rates": {
    "EUR": "90,5391",
    "USD": "75,6826"
  }
}
```

###Сохранение набора валют (пресета)

```json
POST {{api}}/save-preset
Content-Type: application/json

{
  "codes": ["USD", "EUR"]
}
```

```json
{
  "preset": {
    "key": "W7NhhIbS15Fw5SA4",
    "comment": null,
    "codes": [
      "USD",
      "EUR"
    ]
  }
}
```

###Получение валют по пресету

```json
POST {{api}}/rates
Content-Type: application/json

{
  "date": "2021-04-15",
  "preset": "W7NhhIbS15Fw5SA4"
}
```

```json
{
  "rates": {
    "EUR": "90,5391",
    "USD": "75,6826"
  }
}
```

###Сохранение комментария к пресету

```json
POST {{api}}/save-preset-comment
Content-Type: application/json

{
  "preset": "W7NhhIbS15Fw5SA4",
  "comment": "Основные валюты"
}
```

```json
{
  "preset": {
    "key": "W7NhhIbS15Fw5SA4",
    "comment": "Основные валюты",
    "codes": [
      "USD",
      "EUR"
    ]
  }
}
```

В **tests/api.http** лежат заготовки для HTTP клиента PhpStorm.