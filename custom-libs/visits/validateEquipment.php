<?php

$requestData->objectTable = "equipmentVisits";
require_once "validate.php";

/**
 * Проверка на занятость оборудования
 */
if ( $objectTable === "equipmentVisits" ) {

    foreach ( $existingVisits as $visit ) {

        if ( $visit[ "equipment_id" ] == $visitDetail[ "equipment_id" ] )
            $API->returnResponse( "Оборудование занято", 500 );

    }

}