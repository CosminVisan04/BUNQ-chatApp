# Chat Application Backend

This is a simple chat application backend built using the Slim PHP framework and SQLite for the database. The application enables creating users, creating and joingin groups, and send messages within groups, and retrieve those messages. It is built with an easy-to-use RESTful API.

## Table of Contents

- [Features](#features)
- [Technologies](#technologies)
- [Running the Application](#running-the-application)
- [API Endpoints](#api-endpoints)
- [Database](#database)
- [Testing](#testing)

## Features

- User creation
- Group creation and joining functionality
- Posting and retrieving messages within groups
- Error handling for API routes
- Lightweight database using SQLite

## Technologies

- **Language**: PHP 8.1+
- **Framework**: [Slim PHP](https://www.slimframework.com/)
- **Database**: SQLite using [Eloquent ORM](https://laravel.com/docs/8.x/eloquent)
- **Dependency Manager**: Composer
- **Web Server**: PHP Built-in Development Server or Docker
- **Error Handling**: Custom error responses with appropriate HTTP status codes

## Running the Application

1. **Clone the Repository:**

   ```bash
   git clone https://github.com/CosminVisan04/BUNQ-chatApp.git
   cd chat-app-backend

2. **Choose what method to run:**

   Run the server using PHP's built-in web server:

   ```bash
   php -S localhost:8080 -t public
    ```

   Run using Docker, and visit : http://localhost:8080/

   ```bash
   docker-compose up --build 
    ```

## API Endpoints

### Users

- **GET** `/users`: Get all users
  
  **Response Example:**
  
  ```json
  [
    {
      "id": 1,
      "username": "Alice",
      "token": "tokenAlice",
      "created_at": "2024-10-10 10:00:00",
      "updated_at": "2024-10-10 10:00:00"
    },
    {
      "id": 2,
      "username": "Bob",
      "token": "tokenBob",
      "created_at": "2024-10-10 10:00:00",
      "updated_at": "2024-10-10 10:00:00"
    }
  ]
  ```

  **Error Response Example:**
  
  ```json
  {
    "error": "Unable to fetch users"
  }
  ```

- **POST** `/users`: Create a new user

  **Request Body Example:**
  
  ```json
  {
    "username": "Alice",
    "token": "uniqueToken"
  }
  ```

  **Response Example:**
  
  ```json
  {
    "message": "User created"
  }
  ```

  **Error Response Example:**
  
  ```json
  {
    "error": "Unable to create user"
  }
  ```

- **GET** `/users/{id}`: Get a specific user by ID

  **Response Example:**
  
  ```json
  {
    "id": 1,
    "username": "Alice",
    "token": "tokenAlice",
    "created_at": "2024-10-10 10:00:00",
    "updated_at": "2024-10-10 10:00:00"
  }
  ```

  **Error Response Example:**
  
  ```json
  {
    "error": "User not found"
  }
  ```

  ```json
  {
    "error": "Unable to fetch user"
  }
  ```

---

### Groups

- **GET** `/groups`: Get all groups

  **Response Example:**
  
  ```json
  [
    {
      "id": 1,
      "name": "Music Lovers",
      "created_at": "2024-10-10 10:00:00",
      "updated_at": "2024-10-10 10:00:00"
    },
    {
      "id": 2,
      "name": "Sports Fans",
      "created_at": "2024-10-10 10:00:00",
      "updated_at": "2024-10-10 10:00:00"
    }
  ]
  ```

  **Error Response Example:**
  
  ```json
  {
    "error": "Unable to fetch groups"
  }
  ```

- **POST** `/groups`: Create a new group

  **Request Body Example:**
  
  ```json
  {
    "name": "Music Lovers"
  }
  ```

  **Response Example:**
  
  ```json
  {
    "message": "Group created"
  }
  ```

  **Error Response Example:**
  
  ```json
  {
    "error": "Unable to create group"
  }
  ```

- **POST** `/groups/{group_id}/join`: Join a group

  **Response Example:**
  
  ```json
  {
    "message": "Joined group"
  }
  ```

  **Error Response Example:**
  
  ```json
  {
    "error": "Group not found"
  }
  ```

  ```json
  {
    "error": "Unable to join group"
  }
  ```

---

### Messages

- **GET** `/groups/{group_id}/messages`: Get all messages in a group

  **Response Example:**
  
  ```json
  [
    {
      "id": 1,
      "group_id": 1,
      "user_id": 1,
      "content": "This is a test message",
      "created_at": "2024-10-10 10:10:00",
      "updated_at": "2024-10-10 10:10:00"
    }
  ]
  ```

  **Error Response Example:**
  
  ```json
  {
    "error": "No messages found in this group"
  }
  ```

  ```json
  {
    "error": "Unable to fetch messages"
  }
  ```

- **POST** `/groups/{group_id}/messages`: Send a message to a group

  **Request Body Example:**
  
  ```json
  {
    "user_id": 1,
    "content": "Hello everyone!"
  }
  ```

  **Response Example:**
  
  ```json
  {
    "message": "Message sent"
  }
  ```

  **Error Response Example:**
  
  ```json
  {
    "error": "Group not found"
  }
  ```

  ```json
  {
    "error": "Unable to send message"
  }
  ```

## Database

The application uses SQLite as the database. Three tables are created:

### 1. Users Table

The `users` table stores the details of users in the chat application.

**Schema:**

- `id`: Auto-incrementing primary key (integer)
- `username`: The username of the user (string)
- `token`: A unique token for user identification (string, unique)
- `created_at`: The timestamp when the user was created (datetime)
- `updated_at`: The timestamp when the user was last updated (datetime)

**Example Data:**

| id  | username | token      | created_at          | updated_at          |
| --- | -------- | ---------- | ------------------- | ------------------- |
| 1   | Alice    | tokenAlice | 2024-10-10 10:00:00 | 2024-10-10 10:00:00 |
| 2   | Bob      | tokenBob   | 2024-10-10 10:00:00 | 2024-10-10 10:00:00 |

---

### 2. Groups Table

The `groups` table stores the details of the groups in the chat application.

**Schema:**

- `id`: Auto-incrementing primary key (integer)
- `name`: The name of the group (string)
- `created_at`: The timestamp when the group was created (datetime)
- `updated_at`: The timestamp when the group was last updated (datetime)

**Example Data:**

| id  | name           | created_at          | updated_at          |
| --- | -------------- | ------------------- | ------------------- |
| 1   | Music Lovers   | 2024-10-10 10:00:00 | 2024-10-10 10:00:00 |
| 2   | Sports Fans    | 2024-10-10 10:00:00 | 2024-10-10 10:00:00 |

---

### 3. Messages Table

The `messages` table stores the messages exchanged between users in different groups.

**Schema:**

- `id`: Auto-incrementing primary key (integer)
- `group_id`: The ID of the group where the message was sent (integer)
- `user_id`: The ID of the user who sent the message (integer)
- `content`: The content of the message (text)
- `created_at`: The timestamp when the message was created (datetime)
- `updated_at`: The timestamp when the message was last updated (datetime)

**Example Data:**

| id  | group_id | user_id | content              | created_at          | updated_at          |
| --- | -------- | ------- | -------------------- | ------------------- | ------------------- |
| 1   | 1        | 1       | Hello everyone!      | 2024-10-10 10:10:00 | 2024-10-10 10:10:00 |
| 2   | 2        | 2       | Ready for the match? | 2024-10-10 10:15:00 | 2024-10-10 10:15:00 |

---

### Foreign Key Constraints

- The `group_id` column in the `messages` table references the `id` column in the `groups` table.
- The `user_id` column in the `messages` table references the `id` column in the `users` table.

These foreign key constraints ensure referential integrity, meaning that a message must belong to a valid group and be sent by a valid user.



Here is your modified `README.md` section for testing, updated to correspond to the provided testing files:

## Testing

This project includes PHPUnit tests for the different routes defined in the application. The tests cover functionalities for users, groups, and messages, ensuring that the API works as expected. The tests are designed to run in isolation, leaving the database in its original state after the tests are completed, using transactions that roll back after each test.

### Running the Tests

To run all the tests, use the following command:

```bash
vendor/bin/phpunit tests/
```

This will execute all the test files in the `tests/` directory, including:

- `UserRoutesTest.php`
- `GroupRoutesTest.php`
- `MessageRoutesTest.php`

### What is Tested

The project includes the following test files, each covering different aspects of the API:

1. **UserRoutesTest.php**
   - **Test creating a user** (`POST /users`): Verifies that a new user can be created and stored in the database. The test also ensures the user is properly inserted within the transaction, and then the transaction is rolled back.
   - **Test fetching all users** (`GET /users`): Verifies that all users are correctly retrieved from the database. The test inserts a user into the database and verifies that the user is included in the list.
   - **Test fetching a user by ID** (`GET /users/{id}`): Verifies that a specific user can be retrieved by their ID. The test inserts a user and verifies the correct user is returned.
   - **Test fetching a non-existing user** (`GET /users/{id}`): Verifies that the API responds with a 404 status when trying to retrieve a non-existing user.

2. **GroupRoutesTest.php**
   - **Test creating a group** (`POST /groups`): Verifies that a new group can be created and stored in the database. The test ensures the group is properly inserted and then rolls back the transaction.
   - **Test fetching all groups** (`GET /groups`): Verifies that all groups are correctly retrieved from the database. The test inserts a group into the database and checks that the group appears in the results.
   - **Test joining a group** (`POST /groups/{group_id}/join`): Verifies that a user can join a group. The test ensures the join action works as expected, and then the transaction is rolled back.
   - **Test fetching messages in a group** (`GET /groups/{group_id}/messages`): Verifies that messages in a specific group can be retrieved. The test inserts a message for the group and checks that it appears in the results.
   - **Test fetching messages for a non-existing group** (`GET /groups/{group_id}/messages`): Verifies that the API responds with a 404 status when trying to retrieve messages for a non-existing group.

3. **MessageRoutesTest.php**
   - **Test sending a message to a group** (`POST /groups/{group_id}/messages`): Verifies that a message can be sent to a group. The test inserts a group and then sends a message, verifying that the message is correctly stored.
   - **Test fetching all messages in a group** (`GET /groups/{group_id}/messages`): Verifies that messages in a specific group can be retrieved. The test inserts a message for the group and ensures it is the only message returned in the response.
   - **Test fetching messages for a non-existing group** (`GET /groups/{group_id}/messages`): Verifies that the API responds with a 404 status when trying to retrieve messages for a non-existing group.

