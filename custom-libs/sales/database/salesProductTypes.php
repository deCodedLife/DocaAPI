<?php

mysqli_query(
    $API->DB_connection,
    "CREATE TABLE IF NOT EXISTS salesProductTypes(
        id INTEGER NOT NULL AUTO_INCREMENT COMMENT 'ID объекта',
        title VARCHAR(75) NOT NULL COMMENT 'Название',
        article VARCHAR(50) NOT NULL COMMENT 'Артикул',
        is_system CHAR(1) NOT NULL DEFAULT 'N' COMMENT 'Системное поле',
        PRIMARY KEY (id)
    )"
);

mysqli_query(
    $API->DB_connection,
    "DELETE FROM salesProductTypes"
);

mysqli_query(
    $API->DB_connection,
    "INSERT INTO salesProductTypes (title, article) VALUES ('Услуга', 'service'), ('Товар', 'product')"
);