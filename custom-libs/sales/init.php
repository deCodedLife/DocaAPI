<?php

/**
 * Создание баз данных
 */
mysqli_query(
    $API->DB_connection,
    "CREATE TABLE IF NOT EXISTS salesTypes(
        id INTEGER NOT NULL AUTO_INCREMENT COMMENT 'ID объекта',
        title VARCHAR(255) NOT NULL COMMENT 'Название',
        article VARCHAR(50) NOT NULL COMMENT 'Артикул',
        PRIMARY KEY (id)
    )"
);

mysqli_query(
    $API->DB_connection,
    "DELETE FROM salesTypes"
);

mysqli_query(
    $API->DB_connection,
    "INSERT INTO salesTypes (title, article) VALUES ('Наличные', 'cash'), ('Безналичные', 'card'), ('Раздельная', 'parts')"
);

mysqli_query(
    $API->DB_connection,
    "CREATE TABLE IF NOT EXISTS salesMethods(
        id INTEGER NOT NULL AUTO_INCREMENT COMMENT 'ID объекта',
        title VARCHAR(255) NOT NULL COMMENT 'Название',
        article VARCHAR(50) NOT NULL COMMENT 'Артикул',
        PRIMARY KEY (id)
    )"
);

mysqli_query(
    $API->DB_connection,
    "DELETE FROM salesMethods"
);

mysqli_query(
    $API->DB_connection,
    "INSERT INTO salesMethods (title, article) VALUES ('Продажа', 'sell'), ('Возврат', 'sellReturn'), ('Пополнение депозита', 'deposit')"
);

//$API->returnResponse( "WTF?" );

mysqli_query(
    $API->DB_connection,
    "CREATE TABLE IF NOT EXISTS salesList (
        id INTEGER NOT NULL AUTO_INCREMENT COMMENT 'ID объекта',
        status VARCHAR(15) NOT NULL DEFAULT 'waiting' COMMENT 'Статус оплаты',
        client_id INTEGER NOT NULL COMMENT 'ID клиента',
        store_id INTEGER NOT NULL COMMENT 'ID филиала',
        employee_id INTEGER NOT NULL COMMENT 'ID сотрудника',
        pay_type VARCHAR(15) NOT NULL COMMENT 'Тип оплаты',
        pay_method VARCHAR(15) NOT NULL DEFAULT 'cash' COMMENT 'Способ оплаты',
        sum_bonus FLOAT NOT NULL DEFAULT 0 COMMENT 'Бонусов к списанию',
        sum_deposit FLOAT NOT NULL DEFAULT 0 COMMENT 'Депозита к списанию',
        sum_card FLOAT NOT NULL DEFAULT 0 COMMENT 'Сумма списания с карты',
        sun_cash FLOAT NOT NULL DEFAULT 0 COMMENT 'Сумма списания наличными',
        terminal_code VARCHAR(75) NULL COMMENT 'Код отмены операции',
        created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Дата проведения операции',
        online_receipt CHAR(1) NOT NULL DEFAULT 'Y' COMMENT 'Отправить чек на почту',
        summary FLOAT NOT NULL DEFAULT 0 COMMENT 'Итого',
        error TEXT NULL COMMENT 'Текст ошибки',
        PRIMARY KEY (id) 
    );"
);

mysqli_query(
    $API->DB_connection,
    "CREATE TABLE IF NOT EXISTS salesProductTypes(
        id INTEGER NOT NULL AUTO_INCREMENT COMMENT 'ID объекта',
        title VARCHAR(255) NOT NULL COMMENT 'Название',
        article VARCHAR(50) NOT NULL COMMENT 'Артикул',
        PRIMARY KEY (id)
    )"
);

mysqli_query(
    $API->DB_connection,
    "DELETE FROM salesProductTypes"
);

mysqli_query(
    $API->DB_connection,
    "INSERT INTO salesProductTypes (tittle, article) VALUES ('Услуга', 'service'), ('Товар', 'product')"
);

mysqli_query(
    $API->DB_connection,
    "CREATE TABLE IF NOT EXISTS salesProductList (
        id INTEGER NOT NULL AUTO_INCREMENT COMMENT 'ID объекта',
        title VARCHAR(255) NOT NULL COMMENT 'Название',
        type VARCHAR(75) NOT NULL COMMENT 'Тип товара',
        cost FLOAT NOT NULL COMMENT 'Стоимость',
        amount INTEGER NOT NULL COMMENT 'Количество',
        product_id INTEGER NOT NULL COMMENT 'ID объекта продажи',
        sale_id INTEGER NOT NULL COMMENT 'ID продажи',
        PRIMARY KEY (id)
    )"
);


