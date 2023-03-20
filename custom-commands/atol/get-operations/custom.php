<?php

$reportX = $API->DB->from( "atolOperations" )
    ->where( "type", "reportX" );

foreach ( $reportX as $report ) {

    mysqli_query($API->DB_connection, "DELETE FROM atolOperations");

    $request = [
        "request" => [
            "uuid" => "doca-" . $report[ "type" ] . "-id-" . $report[ "id" ],
            "type" => $report[ "type" ],
            "operator" => [
                "name" => "Миннахматовна Э. Ц.",
                "vatin" => "123654789507"
            ]
        ]
    ];

    $API->returnResponse( $request );

}

