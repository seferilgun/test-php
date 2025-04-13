```sql
CREATE DATABASE IF NOT EXISTS ornek_db;

USE ornek_db;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL
);

INSERT INTO users (username, password) VALUES ('ornek_kullanici', '$2y$10$WH4foQd9hv4qJqP/i0615.K6566m9Vq2W3qE1Qy67G57Bv5uGq8a');
```