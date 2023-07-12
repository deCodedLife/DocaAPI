<?php

/**
 * Получение общих документов
 */

if ( $requestData->context->block == "print" ) {

    $response[ "data" ] = [];

    $publicDocuments = $API->DB->from( "documents" )
        ->where( "is_active", "Y" );

    $documents_users = $API->DB->from( "documents_users" )
        ->where( "user_id", (int)$API::$userDetail->id );



    foreach ( $publicDocuments as $publicDocument ) {

        if ( $publicDocument[ "is_general" ] == "Y" ) {

            $publicDocument[ "id" ] = (int) $publicDocument[ "id" ];

            $response[ "data" ][] = $publicDocument;

        } else {


            $output = false;

            foreach ( $documents_users as $document_user ) {

                if ( $document_user[ "document_id" ] == $publicDocument[ "id" ] ) {

                    $output = true;

                }

            }
            if ( $output == true ) {

                $publicDocument[ "id" ] = (int) $publicDocument[ "id" ];

                $response[ "data" ][] = $publicDocument;

            }

        }



    } // foreach. $publicDocuments

} // if. $requestData->context->block == "print"
