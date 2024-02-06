<?php

if ( $requestData->document_id && $requestData->document_id == "17" ) {

    $API->DB->update( "clients" )
        ->set( "is_contract", 'Y' )
        ->where( "id", $requestData->client_id )
        ->execute();

}