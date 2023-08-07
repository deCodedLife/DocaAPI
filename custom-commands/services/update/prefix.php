<?php

$servicesUsers = $API->DB->from( "services_users" )
    ->where( "service_id", $requestData->id );

//if ( $requestData->users_id == [] ) {
//
//    $API->returnResponse( "Укажите исполнителя", 500 );
//
//}
