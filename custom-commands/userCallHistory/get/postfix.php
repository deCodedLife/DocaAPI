<?php

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

if ( $sort_by == "duration" ) {

    if ( $sort_order == "desc" ) $response[ "data" ] = array_values( array_sort( $response[ "data" ], "duration", SORT_DESC ) );
    if ( $sort_order == "asc" ) $response[ "data" ] = array_values( array_sort( $response[ "data" ], "duration", SORT_ASC ) );

}

if ( $sort_by == "created_at" ) {

    if ( $sort_order == "desc" ) $response[ "data" ] = array_values( array_sort( $response[ "data" ], "created_at", SORT_DESC ) );
    if ( $sort_order == "asc" ) $response[ "data" ] = array_values( array_sort( $response[ "data" ], "created_at", SORT_ASC ) );

}
