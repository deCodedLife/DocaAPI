<?php

$pageScheme[ "structure" ][ 1 ][ "settings" ][ 0 ][ "body" ][ 0 ][ "settings" ][ "filters" ] = [
    [
        "property" => "start_at",
        "value" => date( 'Y-m-d' ) . " 00:00:00"
    ],
    [
        "property" => "end_at",
        "value" => date( 'Y-m-d' ) . " 23:59:59"
    ]
];

$pageScheme[ "structure" ][ 1 ][ "settings" ][ 1 ][ "body" ][ 0 ][ "settings" ][ "filters" ] = [
    [
        "property" => "start_at",
        "value" => date("Y-m-d", strtotime("-1 months")) . " 00:00:00"
    ],
    [
        "property" => "end_at",
        "value" => date( 'Y-m-d' ) . " 23:59:59"
    ]
];

$pageScheme[ "structure" ][ 1 ][ "settings" ][ 2 ][ "body" ][ 0 ][ "settings" ][ "filters" ] = [
    [
        "property" => "start_at",
        "value" => date("Y-m-d", strtotime("-1 months")) . " 00:00:00"
    ],
    [
        "property" => "end_at",
        "value" => date( 'Y-m-d' ) . " 23:59:59"
    ]
];

$pageScheme[ "structure" ][ 1 ][ "settings" ][ 3 ][ "body" ][ 0 ][ "settings" ][ "filters" ] = [
    [
        "property" => "start_at",
        "value" => date("Y-m-d", strtotime("-1 months")) . " 00:00:00"
    ],
    [
        "property" => "end_at",
        "value" => date( 'Y-m-d' ) . " 23:59:59"
    ]
];

$pageScheme[ "structure" ][ 1 ][ "settings" ][ 4 ][ "body" ][ 0 ][ "settings" ][ "filters" ] = [
    [
        "property" => "begin_at",
        "value" => date("Y-m-d", strtotime("-1 months")) . " 00:00:00"
    ],
    [
        "property" => "end_at",
        "value" => date( 'Y-m-d' ) . " 23:59:59"
    ]
];

$pageScheme[ "structure" ][ 1 ][ "settings" ][ 5 ][ "body" ][ 0 ][ "settings" ][ "filters" ] = [
    [
        "property" => "start_at",
        "value" => date("Y-m-d", strtotime("-1 months")) . " 00:00:00",
    ],
    [
        "property" => "end_at",
        "value" => date( 'Y-m-d' ) . " 23:59:59"
    ]
];

$pageScheme[ "structure" ][ 1 ][ "settings" ][ 7 ][ "body" ][ 0 ][ "settings" ][ "filters" ] = [
    [
        "property" => "start_at",
        "value" => date("Y-m-d", strtotime("-1 months")) . " 00:00:00"
    ],
    [
        "property" => "end_at",
        "value" => date( 'Y-m-d' ) . " 23:59:59"
    ]
];

$pageScheme[ "structure" ][ 1 ][ "settings" ][ 8 ][ "body" ][ 0 ][ "settings" ][ "filters" ] = [
    [
        "property" => "start_at",
        "value" => date("Y-m-d", strtotime("-1 months")) . " 00:00:00"
    ],
    [
        "property" => "end_at",
        "value" => date( 'Y-m-d' ) . " 23:59:59"
    ]
];

$pageScheme[ "structure" ][ 1 ][ "settings" ][ 9 ][ "body" ][ 0 ][ "settings" ][ "filters" ] = [
    [
        "property" => "cancelledDate_start",
        "value" => date("Y-m-d", strtotime("-1 months"))
    ],
    [
        "property" => "cancelledDate_end",
        "value" => date( 'Y-m-d' )
    ]
];
