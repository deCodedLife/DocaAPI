<?php

/**
 * @file
 * Проверка дедлайнов задач
 */


$overdueTasks = $API->DB->from( "tasks" )
    ->where( [
        "status" => "set",
        "deadline <= ?" => date( "Y-m-d H:i:s" ),
        "is_active" => "Y"
    ] );


foreach ( $overdueTasks as $overdueTask )
    $API->DB->update( "tasks" )
        ->set( [
            "status" => "overdue"
        ] )
        ->where( [
            "id" => $overdueTask[ "id" ]
        ] )
        ->execute();