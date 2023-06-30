<?php

mysqli_query(
    $API->DB_connection,
    "CREATE TABLE IF NOT EXISTS salesProductsList (
        id INTEGER NOT NULL AUTO_INCREMENT COMMENT 'ID объекта',
        title VARCHAR(255) NOT NULL COMMENT 'Название',
        type VARCHAR(75) NOT NULL COMMENT 'Тип товара',
        cost FLOAT NOT NULL COMMENT 'Стоимость',
        amount INTEGER NOT NULL COMMENT 'Количество',
        product_id INTEGER NOT NULL COMMENT 'ID объекта продажи',
        sale_id INTEGER NOT NULL COMMENT 'ID продажи',
        is_system CHAR(1) NOT NULL DEFAULT 'N' COMMENT 'Системное поле',
        PRIMARY KEY (id)
    )"
);