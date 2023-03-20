<?php

$API->DB->deleteFrom( "services" )
    ->where( "id", 23 )
    ->execute();

$serviceID = $API->DB->insertInto( "services" )
    ->values( [
        "title" => $requestData->title,
        "article" => $requestData->article,
        "description" => $requestData->description,
        "price" => $requestData->price,
        "take_minutes" => $requestData->take_minutes,
        "category_id" => $requestData->category_id
    ] )
    ->execute();




foreach ( $requestData->users_id as $user_id ) {

    $API->DB->insertInto( "services_users" )
        ->values( [
            "service_id" => $serviceID,
            "user_id" => $user_id
        ] )
        ->execute();

}



$API->DB->insertInto( "servicesCost" )
    ->values( [
        "service_id" => $serviceID,
        "price" => $requestData->price,
        "begin_from" => "2022-01-01"
    ] )
    ->execute();



$API->returnResponse();