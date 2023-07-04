<?php

if ( $requestData->purchases_consumables ) {

    foreach ( $requestData->purchases_consumables as $consumableKey => $consumable ) {

        $API->returnResponse($consumable);

    } // foreach. $requestData->purchases_consumables

} // if. $requestData->purchases_consumables
