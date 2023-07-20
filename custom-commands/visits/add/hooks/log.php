<?php

/**
 * Получение детальной информации о сотруднике
 */

$userDetail = $API->DB->from( "users" )
    ->where( "id", $requestData->users_id[ 0 ] )
    ->limit( 1 )
    ->fetch();

$userName = $userDetail[ "last_name" ] . " " . $userDetail[ "first_name" ] . " " . $userDetail[ "patronymic" ];


$logDescription = "Добавлена запись к врачу $userName на $requestData->start_at";