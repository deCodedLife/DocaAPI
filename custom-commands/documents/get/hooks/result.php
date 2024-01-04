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
