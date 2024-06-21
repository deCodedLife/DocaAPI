<?php
$visitReport = $API->DB->from( "visitReports" )
    ->where( "id", $requestData->id )
    ->limit( 1 )
    ->fetch();
