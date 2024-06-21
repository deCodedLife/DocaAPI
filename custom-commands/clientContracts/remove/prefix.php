<?php
$clientContract = $API->DB->from( "clientContracts" )
    ->where( "id", $requestData->id )
    ->limit( 1 )
    ->fetch();