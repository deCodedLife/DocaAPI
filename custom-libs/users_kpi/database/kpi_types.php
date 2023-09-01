<?php

mysqli_query(
    $API->DB_connection,
    "CREATE TABLE IF NOT EXISTS kpi_types (
    id INT NOT NULL AUTO_INCREMENT COMMENT 'ID',
    article VARCHAR(75) NOT NULL COMMENT 'Артикул',
    PRIMARY KEY (id) )"
);

mysqli_query(
    $API->DB_connection,
    "DELETE FROM kpi_types"
);

mysqli_query(
    $API->DB_connection,
    "INSERT INTO kpi_types (article) VALUES ('summary'), ('count')"
);