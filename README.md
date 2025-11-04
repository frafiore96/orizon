# Orizon

A professional RESTful JSON API built with PHP and MySQL for managing sustainable travel destinations and trip offerings for Orizon, a travel agency focused on expanding people's horizons through sustainable travel experiences.

## 📋 Table of Contents

- [Features](#features)
- [Tech Stack](#tech-stack)
- [Architecture](#architecture)
- [Installation](#installation)
- [API Documentation](#api-documentation)
- [Testing](#testing)
- [Project Structure](#project-structure)

---

## ✨ Features

- **RESTful Architecture** - Follows REST principles with proper HTTP methods and status codes
- **Central Router** - Single entry point that dispatches requests to appropriate controllers
- **Countries Management** - Full CRUD operations for travel destinations
- **Trips Management** - Create and manage trips with multiple countries
- **Advanced Filtering** - Filter trips by countries and available seats
- **Environment Configuration** - Secure credential management with `.env` file
- **Database Transactions** - Atomic operations for data integrity
- **Prepared Statements** - SQL injection protection with PDO
- **JSON Responses** - Standardized response format for all endpoints
- **CORS Support** - Ready for frontend integration

---

## 🛠 Tech Stack

- **Backend:** PHP 8.x
- **Database:** MySQL 8.0 / MariaDB 10.x
- **Server:** Apache 2.4 with mod_rewrite
- **Architecture:** MVC pattern with central router
- **Authentication:** Environment-based configuration

---

## 🏗 Architecture

The project follows a clean MVC architecture with a central router:

```
┌─────────────┐
│   Client    │
└──────┬──────┘
       │ HTTP Request
       ▼
┌─────────────────┐
│  .htaccess      │  ◄── Rewrites all requests to index.php
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│   index.php     │  ◄── Central Router
│  (Router)       │      • Parses HTTP method and path
└────────┬────────┘      • Extracts URL parameters
         │               • Loads appropriate controller
         │               • Calls the correct action
         ▼
┌─────────────────┐
│  Controller     │  ◄── Business Logic
│  • Country      │      • Validates input
│  • Trip         │      • Calls model methods
└────────┬────────┘      • Returns JSON responses
         │
         ▼
┌─────────────────┐
│    Model        │  ◄── Data Layer
│  • Country      │      • Database queries
│  • Trip         │      • CRUD operations
└────────┬────────┘      • Data validation
         │
         ▼
┌─────────────────┐
│   MySQL DB      │  ◄── Persistence
│  • countries    │
│  • trips        │
│  • trip_countries│
└─────────────────┘
```

### Key Architectural Principles

**1. Central Router Pattern**
- Single entry point (`index.php`) handles all HTTP requests
- Routes defined in `routes/api.php` as configuration
- Same URL, different HTTP verbs (e.g., `GET /trips` vs `POST /trips`)
- Automatic parameter extraction from URLs

**2. Separation of Concerns**
- **Routes:** Define URL patterns and map to controllers
- **Controllers:** Handle HTTP logic and validation
- **Models:** Manage database operations
- **Utils:** Provide shared functionality (Response helper)

**3. RESTful Design**
- Resources as nouns (`/countries`, `/trips`)
- HTTP verbs for actions (GET, POST, PUT, DELETE)
- Proper status codes (200, 201, 400, 404, 500)
- JSON for all responses

---

## 📦 Installation

### Prerequisites

- PHP >= 8.0
- MySQL >= 8.0 or MariaDB >= 10.2
- Apache with mod_rewrite enabled
- Homebrew (for macOS)

### Step 1: Install Dependencies (macOS)

```bash
# Install MySQL
brew install mysql

# Install Apache
brew install httpd

# Install PHP
brew install php

# Start services
brew services start mysql
brew services start httpd
brew services start php
```

### Step 2: Configure Apache

Edit `/opt/homebrew/etc/httpd/httpd.conf`:

```apache
# 1. Enable mod_rewrite (find and uncomment)
LoadModule rewrite_module lib/httpd/modules/mod_rewrite.so

# 2. Change DocumentRoot to your project parent directory
DocumentRoot "/Users/yourusername/dev"
<Directory "/Users/yourusername/dev">
    Options Indexes FollowSymLinks
    AllowOverride All
    Require all granted
</Directory>

# 3. Enable PHP (add at the end of file)
LoadModule php_module /opt/homebrew/opt/php/lib/httpd/modules/libphp.so

<FilesMatch \.php$>
    SetHandler application/x-httpd-php
</FilesMatch>

<IfModule dir_module>
    DirectoryIndex index.php index.html
</IfModule>

AddType application/x-httpd-php .php
AddType application/x-httpd-php-source .phps
```

Restart Apache:
```bash
brew services restart httpd
```

### Step 3: Clone/Download Project

```bash
cd ~/dev
# Place the 'orizon' folder here
```

### Step 4: Configure Environment

Edit the `.env` file in the project root:

```env
DB_HOST=localhost
DB_NAME=orizon_db
DB_USERNAME=root
DB_PASSWORD=
```

### Step 5: Create Database

```bash
cd ~/dev/orizon
mysql -u root < migrations.sql
```

Or use phpMyAdmin to import `migrations.sql`.

### Step 6: Test Installation

Open in browser:
```
http://localhost/orizon/test.html
```

Or test with curl:
```bash
curl http://localhost/orizon/countries
```

---

## 📚 API Documentation

### Base URL

```
http://localhost/orizon
```

### Response Format

**Success Response:**
```json
{
  "success": true,
  "message": "Operation successful",
  "data": { ... }
}
```

**Error Response:**
```json
{
  "success": false,
  "message": "Error description"
}
```

### HTTP Status Codes

- `200 OK` - Request successful
- `201 Created` - Resource created successfully
- `400 Bad Request` - Invalid data provided
- `404 Not Found` - Resource not found
- `405 Method Not Allowed` - HTTP method not supported
- `409 Conflict` - Duplicate resource (e.g., country name)
- `500 Internal Server Error` - Server error

---

## 🌍 Countries Endpoints

### List All Countries

```http
GET /countries
```

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "name": "Italy",
      "created_at": "2024-11-03 10:30:00",
      "updated_at": "2024-11-03 10:30:00"
    }
  ]
}
```

### Get Single Country

```http
GET /countries/{id}
```

**Example:** `GET /countries/1`

### Create Country

```http
POST /countries
Content-Type: application/json

{
  "name": "Germany"
}
```

**Response (201):**
```json
{
  "success": true,
  "message": "Paese creato con successo",
  "data": {
    "id": 7,
    "name": "Germany"
  }
}
```

### Update Country

```http
PUT /countries/{id}
Content-Type: application/json

{
  "name": "Austria"
}
```

### Delete Country

```http
DELETE /countries/{id}
```

---

## ✈️ Trips Endpoints

### List All Trips

```http
GET /trips
```

**Optional Query Parameters:**
- `countries` - Filter by country IDs (comma-separated): `?countries=1,2,3`
- `min_seats` - Minimum available seats: `?min_seats=10`
- `max_seats` - Maximum available seats: `?max_seats=20`

**Examples:**
```http
GET /trips?countries=1,2
GET /trips?min_seats=10
GET /trips?countries=1,3&min_seats=10&max_seats=25
```

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "available_seats": 15,
      "created_at": "2024-11-03 12:00:00",
      "updated_at": "2024-11-03 12:00:00",
      "countries": [
        {
          "id": 1,
          "name": "Italy"
        },
        {
          "id": 2,
          "name": "France"
        }
      ]
    }
  ]
}
```

### Get Single Trip

```http
GET /trips/{id}
```

### Create Trip

```http
POST /trips
Content-Type: application/json

{
  "country_ids": [1, 2, 3],
  "available_seats": 20
}
```

**Response (201):**
```json
{
  "success": true,
  "message": "Viaggio creato con successo",
  "data": {
    "id": 4,
    "available_seats": 20,
    "created_at": "2024-11-03 13:00:00",
    "updated_at": "2024-11-03 13:00:00",
    "countries": [...]
  }
}
```

### Update Trip

```http
PUT /trips/{id}
Content-Type: application/json
```

**Update seats only:**
```json
{
  "available_seats": 25
}
```

**Update countries only:**
```json
{
  "country_ids": [1, 5, 6]
}
```

**Update both:**
```json
{
  "country_ids": [2, 3, 4],
  "available_seats": 18
}
```

### Delete Trip

```http
DELETE /trips/{id}
```

---

## 🧪 Testing

### Using the Web Interface

Open `test.html` in your browser:
```
http://localhost/orizon/test.html
```

This provides a graphical interface to test all API endpoints.

### Using cURL

```bash
# List countries
curl http://localhost/orizon/countries

# Create a country
curl -X POST http://localhost/orizon/countries \
  -H "Content-Type: application/json" \
  -d '{"name":"Germany"}'

# Create a trip
curl -X POST http://localhost/orizon/trips \
  -H "Content-Type: application/json" \
  -d '{"country_ids":[1,2],"available_seats":15}'

# Filter trips
curl "http://localhost/orizon/trips?min_seats=10&max_seats=20"

# Update a trip
curl -X PUT http://localhost/orizon/trips/1 \
  -H "Content-Type: application/json" \
  -d '{"available_seats":30}'

# Delete a country
curl -X DELETE http://localhost/orizon/countries/7
```

### Using Postman

1. Import the collection or create requests manually
2. Set base URL: `http://localhost/orizon`
3. For POST/PUT requests: Body → raw → JSON
4. Add your JSON data and send

---

## 📁 Project Structure

```
orizon/
├── config/
│   └── database.php          # Database connection & .env loader
├── controllers/
│   ├── CountryController.php # Countries CRUD logic
│   └── TripController.php    # Trips CRUD logic
├── models/
│   ├── Country.php           # Countries data layer
│   └── Trip.php              # Trips data layer
├── routes/
│   └── api.php               # Route definitions
├── utils/
│   └── Response.php          # JSON response helper
├── .env                      # Environment configuration
├── .env.example              # Environment template
├── .htaccess                 # Apache rewrite rules
├── .gitignore                # Git ignore rules
├── index.php                 # Central router entry point
├── migrations.sql            # Database schema
├── test.html                 # Web testing interface
└── README.md                 # This file
```

---

## 🔒 Security Considerations

- **Prepared Statements:** All SQL queries use PDO prepared statements to prevent SQL injection
- **Input Validation:** All user inputs are validated before processing
- **Environment Variables:** Sensitive data (DB credentials) stored in `.env` file
- **CORS Configuration:** Can be restricted to specific origins in production
- **File Access Control:** `.htaccess` prevents direct access to `.sql` files

### For Production

1. Change database credentials in `.env`
2. Use HTTPS instead of HTTP
3. Implement authentication (JWT, OAuth)
4. Add rate limiting
5. Enable error logging instead of displaying errors
6. Remove or restrict access to `test.html`

---

## 🐛 Troubleshooting

### 404 on all endpoints

**Solution:** Ensure `mod_rewrite` is enabled and `AllowOverride All` is set in Apache config.

```bash
# Check if mod_rewrite is loaded
httpd -M | grep rewrite

# Restart Apache
brew services restart httpd
```

### Database connection error

**Solution:** 
1. Verify MySQL is running: `brew services list | grep mysql`
2. Check credentials in `.env`
3. Verify database exists: `mysql -u root -e "SHOW DATABASES;"`

### PHP code displayed as text

**Solution:** PHP module not loaded in Apache. Add to `httpd.conf`:

```apache
LoadModule php_module /opt/homebrew/opt/php/lib/httpd/modules/libphp.so

<FilesMatch \.php$>
    SetHandler application/x-httpd-php
</FilesMatch>
```

Then restart Apache.

---

## 📄 License

This project was created for Orizon Travel Agency.

---

## 👨‍💻 Author

Developed for the Orizon project - Sustainable Travel API

---

## 🎯 Quick Reference

### Useful Commands

```bash
# Start services
brew services start mysql
brew services start httpd

# Restart Apache
brew services restart httpd

# View Apache logs
tail -f /opt/homebrew/var/log/httpd/error_log

# Test API
curl http://localhost/orizon/countries

# Import database
mysql -u root < migrations.sql
```

### Main URLs

- **API Base:** `http://localhost/orizon`
- **Test Interface:** `http://localhost/orizon/test.html`
- **phpMyAdmin:** `http://localhost/phpmyadmin` (if installed)