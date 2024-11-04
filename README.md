# Chat Application Backend

A simple chat application backend implemented in PHP using the Slim framework and Doctrine ORM. Users can create chat groups, join them, and send messages. This application uses an SQLite database for data storage.

## Table of Contents

- [Features](#features)
- [Requirements](#requirements)
- [Installation](#installation)
- [Usage](#usage)
- [API Endpoints](#api-endpoints)
- [Running Tests](#running-tests)
- [Code Standards](#code-standards)
- [License](#license)

## Features

- Create chat groups
- Join existing chat groups
- Send messages within groups
- List all messages in a group
- Lightweight and easy to set up

## Requirements

- PHP 8.3 or higher
- Composer
- Docker (optional, for containerized setup)
- SQLite (included in the project)

## Installation

1. **Clone the repository:**
   ```bash
   git clone https://github.com/antevgen/chat.git
   cd chat
   ```

2. **Install dependencies using Composer:**
   ```bash
   composer install
   ```

3. **Set up the environment configuration:**
   Copy the example environment file:
   ```bash
   cp .env.example .env
   ```

   Adjust the `.env` file according to your local configuration if necessary.

4. **Start the application using Docker (optional):**
   If you have Docker installed, you can start the application and its dependencies using Docker Compose:
   ```bash
   docker-compose up -d --build
   ```

5. **Access the application:**
   Open your browser and navigate to `http://chat.local:8082` (or the configured APP_URL).

## Usage

To interact with the API, you can use tools like [Postman](https://www.postman.com/) or [curl](https://curl.se/).

### API Endpoints

Here are the available API endpoints for the chat application:

#### 1. Create a New Group

- **URL**: `POST /groups`
- **Description**: Creates a new chat group.
- **Request Body**:
  ```json
  {
    "name": "Group Name",
    "user_id": 123
  }
  ```
- **Responses**:
   - `201 Created`: Group created successfully.
   - `422 Unprocessable Entity`: Validation error details.

#### 2. List Groups

- **URL**: `GET /groups`
- **Description**: Retrieves a paginated list of chat groups.
- **Query Parameters**:
   - `page` (optional): Page number for pagination, default is `1`.
   - `limit` (optional): Number of items per page, default is `10`.
- **Responses**:
   - `200 OK`: List of groups with pagination.

#### 3. Join a Group

- **URL**: `POST /groups/{id}/members`
- **Description**: Joins a specified group by group ID.
- **Request Body**:
  ```json
  {
    "user_id": 123
  }
  ```
- **Responses**:
   - `200 OK`: Successfully joined group.
   - `409 Conflict`: User is already a member.
   - `404 Not Found`: Group not found.

#### 4. Send a Message in a Group

- **URL**: `POST /groups/{id}/messages`
- **Description**: Sends a message to a specified group by group ID.
- **Request Body**:
  ```json
  {
    "user_id": 123,
    "message": "Hello, everyone!"
  }
  ```
- **Responses**:
   - `201 Created`: Message sent successfully.
   - `404 Not Found`: Group or user not found.

#### 5. List Messages in a Group

- **URL**: `GET /groups/{id}/messages`
- **Description**: Retrieves all messages in a specified group by group ID.
- **Query Parameters**:
   - `page` (optional): Page number for pagination, default is `1`.
   - `limit` (optional): Number of items per page, default is `10`.
- **Responses**:
   - `200 OK`: List of messages in the group with pagination.


## Running Tests

To run the tests for the application, use the following command:

```bash
composer test
```

Ensure you have PHPUnit installed as a development dependency.

## Code Standards

This project adheres to the PSR-12 coding standard. You can check the code quality using PHP_CodeSniffer:

```bash
composer code-style
```

For static analysis, you can use PHPStan:

```bash
composer stan
```

## License

This project is open-source and available under the [MIT License](LICENSE).
