# SIP POINT

sistem manejemen point mahasiswa

# How to use
1. Clone this repository
     ```
     URL
     ```
2. Install dependency
   
   ```
   npm install
   ```
   ```
   composer install
   ```
3. Setup Application     
   ```
    cp .env.example .env
   ```
   ```
    php artisan key:generate
   ```
   ```
    php artisan api:secret
   ```
   ```
    php artisan jwt:secret
   ```
   ```
    php artisan push-format
   ```
4. Setup Database
- create database example : `` sip_point_db ``
- fill database name in .env  with key ``DB_DATABASE`` ex :`` DB_DATABASE=sip_point_db``
- migrate database
  
    ```
    php artisan migrate
    ```
5. Build application into production

    ```
    npm run build
    ```
