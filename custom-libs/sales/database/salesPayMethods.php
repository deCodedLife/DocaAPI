<?php

mysqli_query(
    $API->DB_connection,
    "CREATE TABLE IF NOT EXISTS salesPayMethods(
        id INTEGER NOT NULL AUTO_INCREMENT COMMENT 'ID объекта',
        title VARCHAR(255) NOT NULL COMMENT 'Название',
        article VARCHAR(50) NOT NULL COMMENT 'Артикул',
        is_system CHAR(1) NOT NULL DEFAULT 'N' COMMENT 'Системное поле',
        PRIMARY KEY (id)
    )"
);

mysqli_query(
    $API->DB_connection,
    "DELETE FROM salesPayMethods"
);
mysqli_query(
    $API->DB_connection,
    "INSERT INTO salesPayMethods (title, article) VALUES ('Наличные', 'cash'), ('Безналичные', 'card'), ('Раздельная', 'parts')"
);