<?php

/**
 * Получение записей по коду
 */

if ( ctype_digit( $requestData->search[ 0 ] ) ) {

    $servicesByArticle = [];
    $rows = mysqli_query( $API->DB_connection, "SELECT * FROM services WHERE article LIKE '$requestData->search%'" );

    foreach ( $rows as $row ) {

        $isDuplicate = false;

        foreach ( $response[ "data" ] as $currentRow )
            if ( $currentRow[ "article" ] == $row[ "article" ] ) $isDuplicate = true;

        if ( $isDuplicate ) continue;


        $servicesByArticle[] = $row;

    } // foreach. $rows


    $servicesByArticle = $API->getResponseBuilder( $servicesByArticle, $objectScheme, $requestData->context );

    foreach ( $servicesByArticle as $serviceByArticle )
        $response[ "data" ][] = $serviceByArticle;

} // if. ctype_digit( $requestData->search[ 0 ] )