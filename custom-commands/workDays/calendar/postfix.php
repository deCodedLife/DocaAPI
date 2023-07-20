<?php

/**
 * Сформированный график
 */
$generatedGraph = [];


foreach ( $response[ "data" ] as $eventDate => $events ) {

    foreach ( $events as $event ) {

        /**
         * Получение типа события (рабочий день / выходной)
         */

        $eventDetail = $API->DB->from( $API->request->object )
            ->where( "id", $event[ "id" ] )
            ->limit( 1 )
            ->fetch();

        if ( $eventDetail[ "is_weekend" ] == "Y" ) {

            $event[ "title" ] = "Отмена приема";
            $event[ "background" ] = "danger";

        } else {

            $event[ "background" ] = "success";

        } // if. $eventDetail[ "is_weekend" ] == "Y"


        $generatedGraph[ $eventDate ][] = $event;

    } // foreach. $events

} // foreach. $response[ "data" ]


$response[ "data" ] = $generatedGraph;
