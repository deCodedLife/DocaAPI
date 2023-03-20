<?php

const OBJECTS_CATEGORIES = [
    "target",
    "clients"
];



function sortObjects( $promotion, $type, $object ): array {

    if ( $object[ "is_group" ] == 'Y' ) $type .= "Groups";

    if ( $object[ "exclude" ] == 'Y' ) {
        $promotion[ "excluded" . $type ][] = $object;
        return $promotion;
    }

    if ( $object[ "is_required" ] == 'Y' ) {
        $promotion[ "required" . $type ][] = $object;
        return $promotion;
    }

    $promotion[ lcfirst( $type ) ][] = $object;
    return $promotion;

}



foreach ( $response[ "data" ] as $key => $promotion ) {

    $promotionObjects = $API->DB->from( "promotionObjects" )
        ->where( "promotion_id", $promotion[ "id" ] );

    foreach ( $promotionObjects as $promotionObject ) {



        if ( $promotionObject[ "type" ] == OBJECTS_CATEGORIES[ 0 ] )
            $promotion = sortObjects( $promotion, "Services", $promotionObject );

        if ( $promotionObject[ "type" ] == OBJECTS_CATEGORIES[ 2 ] )
            $promotion = sortObjects( $promotion, "Clients", $promotionObject );

        $response[ "data" ][ $key ] = $promotion;

    }

}

$API->returnResponse( $response );

if ( $requestData->context === "list" ) {

    $returnPromotions = [];

    /**
     * Формирование списка пользователей
     */
    foreach ( $response[ "data" ] as $promotion ) {

        $promotion[ "period" ] = "c " . ($promotion[ "begin_at" ] ?? "-") . " по " . ($promotion[ "end_at" ] ?? "-");
        $returnPromotions[] = $promotion;

    }

    $response[ "data" ] = $returnPromotions;

}