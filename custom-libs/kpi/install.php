<?php

/**
 * Инициализация переменных
 */
$publicAppPath = $API::$configs[ "paths" ][ "public_app" ];
$libPath = $publicAppPath . "/custom-libs/kpi";



/**
 * Создание списка KPI для сотрудников
 */
require_once $libPath . "/database/employees_kpi.php";

/**
 * Создание запросов для kpi
 */
require_once $libPath . "/database/kpi_queries.php";

/**
 * Создание типов kpi
 */
require_once  $libPath . "/database/kpi_types.php";



/**
 * Копирование схем объектов
 */

function moveFiles( $from, $to ) {

    /**
     * Получение списка файлов в папке
     */
    $files = array_diff( scandir( $from ), [ "..", "." ] );

    foreach ( $files as $file ) {

        $filePath = $from . DIRECTORY_SEPARATOR . $file;

        /**
         * Рекурсивный вызов функции, если это папка
         */
        if ( is_dir( $filePath ) ) {

            moveFiles( $filePath, $to . DIRECTORY_SEPARATOR . $file );
            continue;
            
        }

        /**
         * Копирование файла
         */
        $pathInfo = pathinfo( $to . DIRECTORY_SEPARATOR . $file );

        if ( !file_exists( $pathInfo[ 'dirname' ] ) )
            mkdir( $pathInfo[ 'dirname' ], 0777, true );

        copy( $filePath, $to . DIRECTORY_SEPARATOR . $file );

    }

} // function moveFiles( $root, $from, $to ): array {



moveFiles( "$libPath/object-schemes", "$publicAppPath/object-schemes" );
moveFiles( "$libPath/command-schemes", "$publicAppPath/command-schemes" );