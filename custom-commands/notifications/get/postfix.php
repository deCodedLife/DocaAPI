<?php

usort( $response[ "data" ], function ( $nextUser, $previousUser ) {
    return $nextUser[ "id" ] < $previousUser[ "id" ];
} );
