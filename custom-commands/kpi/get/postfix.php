<?php

global $API, $requestData;
ini_set( "display_errors", true );

$userDetails = $API->DB->from( "users" )
    ->where( "id", $requestData->user_id )
    ->fetch();

$kpi = [];

$publicApp = $API::$configs[ "paths" ][ "public_app" ];
require_once( "$publicApp/custom-libs/kpi/visits.php" );
require_once( "$publicApp/custom-libs/kpi/sales.php" );
require_once( "$publicApp/custom-libs/kpi/services.php" );
require_once( "$publicApp/custom-libs/kpi/promotions.php" );

$response[ "data" ] = $kpi;

function array_sort ( $array, $on, $order=SORT_ASC )
{
    $new_array = array();
    $sortable_array = array();

    if (count($array) > 0) {
        foreach ($array as $k => $v) {
            if (is_array($v)) {
                foreach ($v as $k2 => $v2) {
                    if ($k2 == $on) {
                        $sortable_array[$k] = $v2;
                    }
                }
            } else {
                $sortable_array[$k] = $v;
            }
        }

        switch ($order) {
            case SORT_ASC:
                asort($sortable_array);
                break;
            case SORT_DESC:
                arsort($sortable_array);
                break;
        }

        foreach ($sortable_array as $k => $v) {
            $new_array[$k] = $array[$k];
        }
    }

    return $new_array;
}

if ( $sort_by == "type" ) {

    if ( $sort_order == "desc" ) $response[ "data" ] = array_values( array_sort( $response[ "data" ], "type", SORT_DESC ) );
    if ( $sort_order == "asc" ) $response[ "data" ] = array_values( array_sort( $response[ "data" ], "type", SORT_ASC ) );

}

if ( $sort_by == "services_summary" ) {

    if ( $sort_order == "desc" ) $response[ "data" ] = array_values( array_sort( $response[ "data" ], "services_summary", SORT_DESC ) );
    if ( $sort_order == "asc" ) $response[ "data" ] = array_values( array_sort( $response[ "data" ], "services_summary", SORT_ASC ) );

}

if ( $sort_by == "percent" ) {

    if ( $sort_order == "desc" ) $response[ "data" ] = array_values( array_sort( $response[ "data" ], "percent", SORT_DESC ) );
    if ( $sort_order == "asc" ) $response[ "data" ] = array_values( array_sort( $response[ "data" ], "percent", SORT_ASC ) );

}

if ( $sort_by == "promotion" ) {

    if ( $sort_order == "desc" ) $response[ "data" ] = array_values( array_sort( $response[ "data" ], "promotion", SORT_DESC ) );
    if ( $sort_order == "asc" ) $response[ "data" ] = array_values( array_sort( $response[ "data" ], "promotion", SORT_ASC ) );

}

if ( $sort_by == "bonus" ) {

    if ( $sort_order == "desc" ) $response[ "data" ] = array_values( array_sort( $response[ "data" ], "bonus", SORT_DESC ) );
    if ( $sort_order == "asc" ) $response[ "data" ] = array_values( array_sort( $response[ "data" ], "bonus", SORT_ASC ) );

}

