<?php

mysqli_query(
    $API->DB_connection,
    "DROP TABLE employees_kpi"
);

mysqli_query(
  $API->DB_connection,
  "CREATE TABLE IF NOT EXISTS employees_kpi 
    ( id INT AUTO_INCREMENT NOT NULL COMMENT 'ID', 
     user_id INT NOT NULL COMMENT 'Сотрудник', 
     type VARCHAR(75) NULL COMMENT 'Тип KPI',
     target_row VARCHAR(255) NOT NULL COMMENT 'Значение таблицы', 
     required_value INT COMMENT 'Необходимое значение',
     kpi_value FLOAT NOT NULL COMMENT 'KPI', 
     PRIMARY KEY (id) )"
);