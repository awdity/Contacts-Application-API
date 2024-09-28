
# Contacts Application API

## Table of Contents

- [Introduction](#introduction)
- [Features](#features)
- [Technologies Used](#technologies-used)
- [Installation](#installation)
- [API Endpoints](#api-endpoints)
- [Authentication](#authentication)
- [Testing](#testing)
- [License](#license)

## Introduction

The Contacts Application API is a simple RESTful API designed to manage user contacts. It provides functionalities for user authentication, contact management, and contacts export/import features. This API allows users to create, read, update, and delete their contacts efficiently.

## Features

- User registration and authentication
- Contact management (CRUD operations)
- Contact data export (CSV, JSON)
- Contact data import (CSV)
- Refresh token functionality for long-lived sessions

## Technologies Used

- **Laravel**: PHP framework for building the application
- **MySQL**: Database for storing user and contact information
- **JWT (JSON Web Tokens)**: For user authentication
- **PHPUnit**: For testing the application

## Installation

Follow these steps to set up the Contacts Application API on your local machine:

1. Clone the repository:

   ```bash
   git clone https://github.com/awdity/Contacts-Application.git
   ```

2. Navigate to the project directory:

   ```bash
   cd Contacts-Application
   ```

3. Install the dependencies:

   ```bash
   composer install
   ```

4. Copy the `.env.example` file to `.env` and set up your environment variables:

   ```bash
   cp .env.example .env
   ```

5. Generate an application key:

   ```bash
   php artisan key:generate
   ```

6. Set up your database in the `.env` file:

   ```plaintext
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=your_database_name
   DB_USERNAME=your_database_user
   DB_PASSWORD=your_database_password
   ```

7. Run migrations to set up the database tables:

   ```bash
   php artisan migrate
   ```

8. Start the local development server:

   ```bash
   php artisan serve
   ```

## API Endpoints

### Authentication

- **POST** `/api/v1/users` - Register a new user
- **POST** `/api/v1/token/auth` - Log in an existing user
- **POST** `/api/v1/auth/refresh` - Refresh JWT token

### Contacts Management

- **GET** `/api/v1/contacts` - Retrieve all contacts for the authenticated user (with pagination)
- **POST** `/api/v1/contacts` - Create a new contact
- **GET** `/api/v1/contacts/{id}` - Retrieve a specific contact by ID
- **PATCH** `/api/v1/contacts/{id}` - Update an existing contact
- **DELETE** `/api/v1/contacts/{id}` - Delete a specific contact by ID
- **GET** `/api/v1/contacts/export` - Export contacts to a file (CSV or JSON)
- **POST** `/api/v1/contacts/import` - Import contacts from a file (CSV)

## Authentication

This API uses JWT for authentication. To access protected routes, you need to include the JWT in the `Authorization` header as follows:

```
Authorization: Bearer {token}
```

## Testing

To run the test suite for this application, use the following command:

```bash
php artisan test
```

This will execute all tests, including unit and feature tests, ensuring that your application is functioning as expected.

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.
