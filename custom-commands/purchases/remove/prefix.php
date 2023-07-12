<?php

$purchaseDetail = $API->DB->from( "purchases" )
    ->where( "id", $requestData->id )
    ->limit( 1 )
    ->fetch();

if ( $purchaseDetail[ "purchaseType" ] == "consumables" ) {

    $purchases_consumables = $API->DB->from( "purchases_consumables" )
        ->where( "row_id", $requestData->id );

    foreach ( $purchases_consumables as $purchase_consumable ){

        $consumableActive = $API->DB->from( "warehouses" )
            ->where( [
                "consumable_id" => $purchase_consumable[ "consumable_id" ],
                "store_id" => $purchaseDetail[ "store_id" ]
            ] )
            ->limit( 1 )
            ->fetch();

        $API->DB->update( "warehouses" )
            ->set([
                "count" =>  (int)$consumableActive[ "count" ] - (int)$purchase_consumable[ "count" ]
            ])
            ->where( [
                "consumable_id" => $purchase_consumable[ "consumable_id" ],
                "store_id" => $purchaseDetail[ "store_id" ]
            ] )
            ->execute();

    }

}
