<?php

mysqli_query(
  $API->DB_connection,
  "CREATE TABLE IF NOT EXISTS kpi_queries (
    id INT NOT NULL AUTO_INCREMENT COMMENT 'ID',
    kpi_id INT NOT NULL COMMENT 'ID kpi',
    property VARCHAR(255) NOT NULL COMMENT 'Запрашиваемое значение',
    value VARCHAR(255) COMMENT 'Значение',
    priority INT(3) NOT NULL DEFAULT 0 COMMENT 'Приоритет выполнения',
    related CHAR(1) DEFAULT 'N' COMMENT 'Зависимое',
    PRIMARY KEY (id))"
);