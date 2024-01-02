# PHP CRUD MVC Tracing

## Requirements
- **php**
- **composer**
- **docker**
- **mysql**

## Steps to Run
1. Start Zipkin for tracing:
    ```bash
    docker run -p 9411:9411 -d openzipkin/zipkin
    ```

2. Install dependencies:
    ```bash
    composer install
    ```

3. Run the whole app:
    ```bash
    php -S localhost:8000
    ```

4. Run only the backend API:
    ```bash
    php -S localhost:8080 backend.php
    ```

## Access Points
- **db**: Please update the config.php file with your MySQL credentials.
- **Traces**: `localhost:9411` for trace details.
- **Logs**: `/services/logs.log` (generated automatically).
  - *Note: Logs can be exported to other third-party services, but currently only exported to a file.*
