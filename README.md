# Setting up WebSockets in Laravel

This guide will walk you through the steps to set up WebSockets in a Laravel application.
## Prerequisites

- Laravel >= 8.8
- PHP >= 8.1
- Composer (for package management)
- Node.js (for Laravel Echo)

## Installation
1. Run the composer install to setup necessary packages:

   ```bash
   composer install

2. Run the migrations:

   ```bash
   php artisan migrate
     
3. Run the seeders:

   ```bash
   php artisan db:seed 
   
  
4. Update your .env file with your Pusher credentials:
     ```bash
    PUSHER_APP_ID=test
    PUSHER_APP_KEY=test
    PUSHER_APP_SECRET=test
    PUSHER_APP_CLUSTER=mt1
    LARAVEL_WEBSOCKETS_HOST=127.0.0.1
    LARAVEL_WEBSOCKETS_SCHEME=http
    LARAVEL_WEBSOCKETS_PORT=6001
    ENCRYPTION_KEY=mrWOdOfva5jO2IxcVO7NU5FUk3ItgjDzHF46zwnIXAU

5. Starting the local Server
  Start the local server using the following Artisan command:
   ```bash
   php artisan serve   
6. Starting the WebSocket Server
  Start the WebSocket server using the following Artisan command:
   ```bash
   php artisan websockets:serve
7. Run the Queue using the following Artisan command:
    ```bash
    php artisan queue:work
    
    
8. Run the testcases using the following Artisan command :
    ```bash
   php artisan test

9. For Code coverage run the following Artisan command:
    ```bash
    vendor/bin/phpunit --coverage-html reports/
    
    
   
   
## Front-end setup

1. Install npm:
    ```bash
    npm install
  
2. Compile your assets :
    
     ```bash
     npm run dev
   

   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
