<?php

/**
 * Вывод шапки документа
 */

if ( $requestData->article ) {

    /**
     * Исключения
     */
    if ( $requestData->article != "talon" ) {

        /**
         * Сформированный документ
         */
        $resultDocument = "";


        /**
         * Получение шапки документа
         */

        $documentHeaderDetail = $API->DB->from( "documents" )
            ->where( "article", "header" )
            ->limit( 1 )
            ->fetch();

        $documentHeaderBlocks = $API->DB->from( "documentBlocks" )
            ->where( "document_id", $documentHeaderDetail[ "id" ] )
            ->orderBy( "block_position asc" );

        foreach ( $documentHeaderBlocks as $documentBlock ) {

            /**
             * Получение детальной информации о блоке документа
             */
            $documentBlockDetail = $API->DB->from( "documents_" . $documentBlock[ "block_type" ] )
                ->where( "id", $documentBlock[ "block_id" ] )
                ->limit( 1 )
                ->fetch();


            /**
             * Добавление блока документа в структуру
             */
            $resultDocument = $documentBlockDetail[ "document_body" ];

        } // foreach. $documentHeaderBlocks


        $response[ "data" ][ 0 ][ "structure" ][ 0 ][ "settings" ][ "document_body" ] = $resultDocument . $response[ "data" ][ 0 ][ "structure" ][ 0 ][ "settings" ][ "document_body" ];

    } // if. $requestData->article != "talon"

} // if. $requestData->article


/**
 * Получение общих документов
 */

if ( $requestData->context->block == "print" ) {

    $response[ "data" ] = [];

    $publicDocuments = $API->DB->from( "documents" )
        ->where( "is_active", "Y" );


    $filters = [];
    if ( $requestData->owner_id ) $filters[] = [ "user_id" => $requestData->owner_id ];

    
    $documents_users = $API->DB->from( "documents_users" )
        ->where( $filters );


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
