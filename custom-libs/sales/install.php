<?php

/**
 * Создание баз данных
 */

$publicAppPath = $API::$configs[ "paths" ][ "public_app" ];
$libPath = $publicAppPath . "/custom-libs/sales";

//require_once ( "./database/salesActions.php" );
//require_once ( "./database/salesPayMethods.php" );
//require_once ( "./database/salesProductTypes.php" );
//require_once ( "./database/salesProductsList.php" );
//require_once ( "./database/salesList.php" );

/**
 * Копирование объектов
 */

function moveFiles( $filesList, $from, $destination ) {

    foreach ( $filesList as $file )
        copy( $from . $file, $destination . $file);

}

$objectsList = array_diff( scandir( $libPath . "/object-schemes" ), [ "..", "." ] );
moveFiles( $objectsList, $libPath . "/object-schemes/", $publicAppPath . "/object-schemes/" );

$commandsList =  array_diff( scandir( $libPath . "/command-schemes" ), [ "..", "." ] );
moveFiles( $commandsList, $libPath . "/command-schemes/", $publicAppPath . "/command-schemes/" );


