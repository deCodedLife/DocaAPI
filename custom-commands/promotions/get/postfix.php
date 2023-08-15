<?php

const OBJECTS_CATEGORIES = [
    "services",
    "clients"
];


function sortObjects( $promotion, $type, $object ): array {

    if ( $object[ "is_group" ] == 'Y' ) $type .= "Groups";

    if ( $object[ "is_excluded" ] == 'Y' ) {
        $promotion[ "excluded" . $type ][] = (int) $object[ "object_id" ];
        return $promotion;
    }

    if ( $object[ "is_required" ] == 'Y' ) {
        $promotion[ "required" . $type ][] = (int) $object[ "object_id" ];
        return $promotion;
    }

    $promotion[ lcfirst( $type ) ][] = (int) $object[ "object_id" ];
    return $promotion;

}



foreach ( $response[ "data" ] as $key => $promotion ) {

    $promotionObjects = $API->DB->from( "promotionObjects" )
        ->where( "promotion_id", $promotion[ "id" ] );

    $promotion[ "services" ] = [];

    foreach ( $promotionObjects as $promotionObject ) {

        $promotion = sortObjects( $promotion, ucfirst( $promotionObject[ "type" ] ), $promotionObject );
        $response[ "data" ][ $key ] = $promotion;

    }

}

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


        $row[ "period" ] = date( 'Y-m-d H:i', strtotime( $row[ "begin_at" ] ) ) . " - " . date( 'Y-m-d H:i', strtotime( $row[ "end_at" ] ) );

        if ( $detailPromotion[ "promotion_type" ] == "percent" )
            $row[ "value" ] =  $row[ "value" ] . "%";
        else if ( $row[ "promotion_type" ] == "fixed" )
            $row[ "value" ] =  $row[ "value" ] . "₽";


        $returnRows[] = $row;

    } // foreach. $response[ "data" ]

    $response[ "data" ] = $returnRows;

} // if. $requestData->context->block === "list"
