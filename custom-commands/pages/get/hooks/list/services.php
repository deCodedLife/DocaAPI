<?php

$resultBlockFieldList = [];


foreach ( $blockField[ "list" ] as $blockFieldProperty ) {

    /**
     * Получение группы услуг
     */
    $serviceDetail = $API->DB->from( "services" )
        ->where( "id", $blockFieldProperty[ "value" ] )
        ->limit( 1 )
        ->fetch();


    /**
     * Получение исполнителей группы услуг
     */

    $serviceGroupUsers = $API->DB->from( "serviceGroupEmployees" )
        ->where( "groupID", $serviceDetail[ "category_id" ] );

    foreach ( $serviceGroupUsers as $serviceGroupUser )
        $blockFieldProperty[ "joined_field_value" ][] = $serviceGroupUser[ "employeeID" ];

    $filteredBlockFieldProperties = array_unique( $blockFieldProperty[ "joined_field_value" ] );


    $blockFieldProperty[ "joined_field_value" ] = [];

    foreach ( $filteredBlockFieldProperties as $filteredBlockFieldProperty )
        $blockFieldProperty[ "joined_field_value" ][] = $filteredBlockFieldProperty;

    $resultBlockFieldList[] = $blockFieldProperty;

} // foreach. $blockField[ "list" ]


$blockField[ "list" ] = $resultBlockFieldList;