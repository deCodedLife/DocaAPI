<?php

/**
 * Получение общих документов
 */

if ( $requestData->context->block == "print" ) {

    $publicDocuments = $API->DB->from( "documents" )
        ->where( [
            "owner_id" => null
        ] );

    foreach ( $publicDocuments as $publicDocument ) {

        $publicDocument[ "id" ] = (int) $publicDocument[ "id" ];

        $response[ "data" ][] = $publicDocument;

    } // foreach. $publicDocuments

} // if. $requestData->context->block == "print"