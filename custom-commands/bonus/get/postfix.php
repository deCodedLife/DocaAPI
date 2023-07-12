<?php

if ( $requestData->context->block === "list" ) {

    /**
     * Подстановка периода и типа акции
     */

    $returnRows = [];

    foreach ( $response[ "data" ] as $row ) {

        /**
         * Детальная информация
         */
        $detailPromotion = $API->DB->from( "promotions" )
            ->where( "id", $row[ "id" ] )
            ->limit( 1 )
            ->fetch();


        $row[ "period" ] = "c " . ( $row[ "start" ] ?? "-" ) . " по " . ( $row[ "end" ] ?? "-" );

        if ( $detailPromotion[ "promotion_type" ] == "percent" )
            $row[ "value" ] =  $row[ "value" ] . "%";
        else if ( $row[ "promotion_type" ] == "fixed" )
            $row[ "value" ] =  $row[ "value" ] . "₽";


        $returnRows[] = $row;

    } // foreach. $response[ "data" ]

    $response[ "data" ] = $returnRows;

} // if. $requestData->context->block === "list"
