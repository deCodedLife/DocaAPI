<?php

$alreadyExisits = $API->DB->from( "documents" )
    ->where( "title", $requestData->title )
    ->fetch();


if ( $alreadyExisits )
    $API->returnResponse( "Документ с таким названием уже существует", 500 );