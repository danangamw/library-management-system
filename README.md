<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## Overview

This is a Laravel-based application designed to [briefly describe the purpose of the application]. This README provides instructions on how to set up and run the project on your local machine.

## Prerequisites

Before you begin, ensure you have met the following requirements:

-   PHP (8.1 or later)
-   Composer (for managing PHP dependencies)
-   MySQL or another supported database
-   Redis (if caching is used)

## Getting Started

### 1. Clone the Repository

Clone repository project ke lokal komputer Anda:

```bash
git clone https://github.com/username/repository-name.git
cd repository-name
```

### 2. Install Dependencies

Install the PHP dependencies using Composer:

```bash
composer install
```

### 3.Set Up the Environment File

Duplicate the .env.example file and rename it to .env. This file contains environment-specific configurations.

```bash
cp .env.example .env
```

### 4. Generate the Application Key

Generate a new application key. This is used for encryption and should be done after copying the .env file:

```bash
php artisan key:generate
```

### 5. Configure the Database

Open the .env file and update the database configuration to match your local database setup. For example:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

### 6. Run Database Migration and Seeders

Run the migration to set up your database schema:

```bash
php artisan migrate
```

to populate the database with initial data, run:

```bash
php artisan db:seed
```

### 7. Set Up Caching (optional)

Install predis. Run the following command on your terminal to install Predis:

```bash
composer require predis/predis
```

Ensure Redis is installed and running. Update the .env file with your Redis configuration:

```env
REDIS_CLIENT=predis
REDIS_HOST=<Your_redis_host_ip>
REDIS_PASSWORD=<your_redis_password>
REDIS_PORT=<your_redis_port>
```

Since predis is being used, the Redis client will be changed to predis in the configuration file, config/database.php:

```env
'redis' => [

  'client' => env('REDIS_CLIENT', 'predis'),

  'options' => [
      'cluster' => env('REDIS_CLUSTER', 'redis'),
      'prefix' => env('REDIS_PREFIX', Str::slug(env('APP_NAME', 'laravel'), '_').'_database_'),
  ],

  'default' => [
      'url' => env('REDIS_URL'),
      'host' => env('REDIS_HOST', '127.0.0.1'),
      'password' => env('REDIS_PASSWORD', null),
      'port' => env('REDIS_PORT', '6379'),
      'database' => env('REDIS_DB', '0'),
  ],

],
```

### 8. Start the Development Server

You can now start the Laravel development server:

```bash
php artisan serve
```

By default, the application will be available at http://localhost:8000

### 9. Accessing the Application

Open your web browser and navigate to http://localhost:8000 to access the application.

## Running Tests

First, add scribe package via Composer:

```bash
composer require --dev knuckleswtf/scribe
```

Publish the config file by running:

```bash
php artisan vendor:publish --tag=scribe-config
```

Run the command to generate your docs.

```bash
php artisan scribe:generate
```

To run the tests provided with the application:

```bash
php artisan test
```

## Troubleshooting

If you encounter issues:

-   Ensure all dependencies are correctly installed.
-   Check the .env file for correct configuration.
-   Look into Laravel logs in storage/logs for detailed error messages.
-   Consult the Laravel documentation for common issues.
