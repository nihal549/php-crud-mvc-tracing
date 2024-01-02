# PHP CRUD MVC Tracing

## Requirements
- **php**
- **composer**
- **docker**
- **mysql**


### Table Structure
The structure of the table used for container management is as follows:
```sql
CREATE TABLE details (
    id INT,
    name VARCHAR(255),
    location VARCHAR(255)
);
```
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
## Backend API Testing Documentation


### Default Endpoint
- **URL:** `http://localhost:8080/containers/`
- **Method:** GET
- **Purpose:** Fetches containers.

### Insert Endpoint
- **URL:** `http://localhost:8080/containers/add`
- **Method:** POST
- **Purpose:** Adds a new container.
- **Request Body Example:**
    ```json
    {  
        "name": "test",
        "location": "test"
    }
    ```

### Update Endpoint
- **URL:** `http://localhost:8080/containers/update`
- **Method:** PUT
- **Purpose:** Updates a container.
- **Request Body Example:**
    ```json
    {   
        "id": "25",
        "name": "test",
        "location": "test"
    }
    ```

### Delete Endpoint
- **URL:** `http://localhost:8080/containers/delete/id`
- **Method:** DELETE
- **Purpose:** Deletes a container.
- **Note:** Replace `id` in the URL with the ID of the container to be deleted.
    

## Access Points
- **db**: Please update the config.php file with your MySQL credentials.
- **Traces**: `localhost:9411` for trace details.
- **Logs**: `/services/logs.log` (generated automatically).
  - *Note: Logs can be exported to other third-party services, but currently only exported to a file.*
