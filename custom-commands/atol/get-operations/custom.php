<?php

$reportX = $API->DB->from( "atolOperations" )
    ->where( "type", "closeShift" );

foreach ( $reportX as $report ) {

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

