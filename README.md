## API авторизации

### `login` - авторизация

- **Запрос:** `POST /api/lk/auth/login`
- **Описание:** Авторизует пользователя и возвращает токен.

#### Входные параметры
- `phone` (string) - Обязательно, телефон пользователя
- `password` (string) - Обязательно, минимум 8 символов, пароль пользователя

#### Выходные параметры
- `access_token` (string): Токен пользователя
- `token_type` (string): bearer
- `expires_in` (int): когда истекает

#### Пример успешного запроса

**Ответ:**
```json
{
    "access_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vMTI3LjAuMC4xOjgwMDAvYXBpL2xrL2F1dGgvbG9naW4iLCJpYXQiOjE3NzE1NzAwMjksImV4cCI6MTc3MTU3MzYyOSwibmJmIjoxNzcxNTcwMDI5LCJqdGkiOiJQYlpyQnhRTUVRbldEMTVOIiwic3ViIjoiMSIsInBydiI6IjY3ZTE1Y2FhY2ZjYTY3YTE5MGMzMzg0Y2VkMzQxZWI4MTE4NzM1M2YifQ.cuqEsr4m6b8urBO_12jFsGeUoP79EBmf1LnH9rJFRUo",
    "token_type": "bearer",
    "expires_in": 3600
}
```


### `register` - регистрация

- **Запрос:** `POST /api/lk/auth/register`
- **Описание:** Регистрирует нового пользователя и возвращает токен.

#### Входные параметры
- `phone` (string) - Обязательно, телефон пользователя
- `password` (string) - Обязательно, минимум 8 символов, пароль пользователя

#### Выходные параметры
- `access_token` (string): Токен пользователя
- `token_type` (string): bearer
- `expires_in` (int): когда истекает

#### Пример успешного запроса

**Ответ:**
```json
{
    "access_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vMTI3LjAuMC4xOjgwMDAvYXBpL2xrL2F1dGgvbG9naW4iLCJpYXQiOjE3NzE1NzAwMjksImV4cCI6MTc3MTU3MzYyOSwibmJmIjoxNzcxNTcwMDI5LCJqdGkiOiJQYlpyQnhRTUVRbldEMTVOIiwic3ViIjoiMSIsInBydiI6IjY3ZTE1Y2FhY2ZjYTY3YTE5MGMzMzg0Y2VkMzQxZWI4MTE4NzM1M2YifQ.cuqEsr4m6b8urBO_12jFsGeUoP79EBmf1LnH9rJFRUo",
    "token_type": "bearer",
    "expires_in": 3600
}
```


### `logout` - выход

- **Запрос:** `POST /api/lk/auth/logout`
- **Описание:** Разавторизуется и делает токен невалидным.

#### Входные параметры
- `token` (string) - Токен пользователя

#### Выходные параметры
- `status` (string): Статус выполения команды

#### Пример успешного запроса

**Ответ:**
```json
{
    "status": "Success"
}
```


### `refresh` - обновление токена

- **Запрос:** `POST /api/lk/auth/refresh`
- **Описание:** Обновляет токен и возвращает его.

#### Входные параметры
- `token` (string) - Токен пользователя

#### Выходные параметры
- `access_token` (string): Токен пользователя
- `token_type` (string): bearer
- `expires_in` (int): когда истекает

#### Пример успешного запроса

**Ответ:**
```json
{
    "access_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vMTI3LjAuMC4xOjgwMDAvYXBpL2xrL2F1dGgvbG9naW4iLCJpYXQiOjE3NzE1NzAwMjksImV4cCI6MTc3MTU3MzYyOSwibmJmIjoxNzcxNTcwMDI5LCJqdGkiOiJQYlpyQnhRTUVRbldEMTVOIiwic3ViIjoiMSIsInBydiI6IjY3ZTE1Y2FhY2ZjYTY3YTE5MGMzMzg0Y2VkMzQxZWI4MTE4NzM1M2YifQ.cuqEsr4m6b8urBO_12jFsGeUoP79EBmf1LnH9rJFRUo",
    "token_type": "bearer",
    "expires_in": 3600
}
```


### `send_code` - сгенерировать код авторизации

- **Запрос:** `POST /api/lk/auth/send_code`
- **Описание:** Генерирует код авторизации и посылает его пользователю.

#### Входные параметры
- `token` (string) - Токен пользователя

#### Выходные параметры
- `success` (bool): True.
- `message` (string): Verification code sent
- `expires_in` (int): 900

#### Пример успешного запроса

**Ответ:**
```json
{
    "success": 1,
    "message": "Verification code sent",
    "expires_in": 900
}
```

### `accept_code` - ввод кода

- **Запрос:** `POST /api/lk/auth/accept_code`
- **Описание:** Ввод и принятие кода авторизации

#### Входные параметры
- `token` (string) - Токен пользователя
- `code` (string) - Обязательно, код авторизации 

#### Выходные параметры
- `status` (string): Статус выполения команды

#### Пример успешного запроса

**Ответ:**
```json
{
    "status": "Success"
}
```


### `get_vk_client_id` - получить Client ID VK

- **Запрос:** `POST /api/lk/auth/get_vk_client_id`
- **Описание:** Получаем Client ID VK из настроек приложения

#### Входные параметры

#### Выходные параметры
- `status` (string): Статус выполения команды
- `client_id` (string): Client ID VK

#### Пример успешного запроса

**Ответ:**
```json
{
    "status": "Success",
    "client_id": "1234567890"
}
```


### `get_vk_code_challenge` - получить code_challenge VK

- **Запрос:** `POST /api/lk/auth/get_vk_code_challenge`
- **Описание:** Получаем code_challenge VK

#### Входные параметры

#### Выходные параметры
- `status` (string): Статус выполения команды
- `code_challenge` (string): code_challenge VK

#### Пример успешного запроса

**Ответ:**
```json
{
    "status": "Success",
    "code_challenge": "1234567890"
}
```


### `save_phone` - Сохранить номер телефона

- **Запрос:** `POST /api/lk/auth/save_phone`
- **Описание:** Сохраняет номер телефона пользователя

#### Входные параметры
- `token` (string) - Токен пользователя
- `phone` (string) - Обязательно - телефон пользователя

#### Выходные параметры
- `status` (string): Статус выполения команды

#### Пример успешного запроса

**Ответ:**
```json
{
    "status": "Success"
}
```


### `save_userdata` - Сохранить email и имя пользователя

- **Запрос:** `POST /api/lk/auth/save_userdata`
- **Описание:** Сохраняет email и имя пользователя

#### Входные параметры
- `token` (string) - Токен пользователя
- `email` (string) - email пользователя
- `name` (string) - Имя пользователя

#### Выходные параметры
- `status` (string): Статус выполения команды

#### Пример успешного запроса

**Ответ:**
```json
{
    "status": "Success"
}
```