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


**Note**: Ensure to include a user identifier (token, username, or ID) in the request headers or body as required by your application logic.

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
