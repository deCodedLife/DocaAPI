<?php

if ( $requestData->is_combined == 'N' && $requestData->object ) {

    foreach ( $requestData->visits_ids as $object => $visits_id  ) {

        foreach ( $visits_id as $index => $visit_id ) {

            if ( $visit_id == $requestData->id ) continue;
            unset( $visits_id[ $index ] );

        }

        $requestData->visits_ids->$object = array_values( $visits_id );

    }

}

$publicAppPath = $API::$configs[ "paths" ][ "public_app" ];
require_once $publicAppPath . '/custom-libs/sales/business_logic.php' ;
require_once "update-products.php";
require_once "sum-fields-update.php";

$API->returnResponse( $formFieldsUpdate );