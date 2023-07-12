<?php

mysqli_query(
    $API->DB_connection,
    "CREATE TABLE IF NOT EXISTS salesList (
        id INTEGER NOT NULL AUTO_INCREMENT COMMENT 'ID объекта',
        status VARCHAR(15) NOT NULL DEFAULT 'waiting' COMMENT 'Статус оплаты',
        client_id INTEGER NOT NULL COMMENT 'ID клиента',
        store_id INTEGER NOT NULL COMMENT 'ID филиала',
        employee_id INTEGER NOT NULL COMMENT 'ID сотрудника',
        action VARCHAR(15) NOT NULL COMMENT 'Тип действия',
        pay_method VARCHAR(15) NOT NULL DEFAULT 'cash' COMMENT 'Способ оплаты',
        sum_bonus FLOAT NOT NULL DEFAULT 0 COMMENT 'Бонусов к списанию',
        sum_deposit FLOAT NOT NULL DEFAULT 0 COMMENT 'Депозита к списанию',
        sum_card FLOAT NOT NULL DEFAULT 0 COMMENT 'Сумма списания с карты',
        sum_cash FLOAT NOT NULL DEFAULT 0 COMMENT 'Сумма списания наличными',
        terminal_code VARCHAR(75) NULL COMMENT 'Код отмены операции',
        created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Дата проведения операции',
        online_receipt CHAR(1) NOT NULL DEFAULT 'Y' COMMENT 'Отправить чек на почту',
        summary FLOAT NOT NULL DEFAULT 0 COMMENT 'Итого',
        error TEXT NULL COMMENT 'Текст ошибки',
        is_system CHAR(1) NOT NULL DEFAULT 'N' COMMENT 'Системное поле',
        PRIMARY KEY (id) 
    );"
);