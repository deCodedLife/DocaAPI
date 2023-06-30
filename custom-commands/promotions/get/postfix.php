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

    $returnPromotions = [];

    /**
     * Формирование списка акций
     */
    foreach ( $response[ "data" ] as $promotion ) {

        /**
         * Детальная информация
         */
        $detailPromotion = $API->DB->from( "promotions" )
            ->where( "id", $promotion[ "id" ] )
            ->limit( 1 )
            ->fetch();

        if ( $detailPromotion[ "promotion_type" ] == "percent" ) {

            $promotion[ "value" ] =  $promotion[ "value" ] . "%";

        } else if ( $detailPromotion[ "promotion_type" ] == "fixed" ) {

            $promotion[ "value" ] =  $promotion[ "value" ] . "₽";

        }
        $promotion[ "period" ] = "c " . ($promotion[ "begin_at" ] ?? "-") . " по " . ($promotion[ "end_at" ] ?? "-");
        $returnPromotions[] = $promotion;

    }

    $response[ "data" ] = $returnPromotions;

} // if. $requestData->context->block === "list"
