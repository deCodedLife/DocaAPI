<?php

/**
 * @file
 * Электронная очередь
 */


$resultVisits = [];

$start = date( "Y-m-d" ) . " 00:00:00";
$end = date( "Y-m-d" ) . " 23:59:59";


$visits = $API->DB->from( "visits" )
    ->where( [
        "store_id" => $requestData->store_id,
        "status" => "process",
        "start_at >= ?" => $start,
        "start_at <= ?" => $end,
        "is_active" => "Y"
    ] )
    ->orderBy( "start_at asc" )
    ->limit( 5 );


/**
 * Формирование списка посещений
 */

foreach ( $visits as $visit ) {

    $cabinetDetail = $API->DB->from( "cabinets" )
        ->where( "id", $visit[ "cabinet_id" ] )
        ->limit( 1 )
        ->fetch();


    /**
     * Повторное оповещение
     */
    $isAlert = false;
    if ( $visit[ "is_alert" ] == "Y" ) $isAlert = true;


    /**
     * Озвучка
     */

    $voice = null;

    if ( !$isAlert ) {

        /**
         * Получение настроек
         */
        $settings = $API->DB->from( "settings" )
            ->limit( 1 )
            ->fetch();


        /**
         * Формирование текста для озвучки
         */

        $synthText = substr( $visit[ "talon" ], 0, 2 ) . "," . substr( $visit[ "talon" ], 2 );
        $synthText = "Пациент $synthText, пройдите в кабинет " . $cabinetDetail[ "title" ];

        $talonFileName = str_replace( " ", "", $visit[ "talon" ] );
        $talonFileName = $talonFileName . "-" . $cabinetDetail[ "id" ];


        /**
         * Формирование пути к файлу озвучки
         */

        if ( !is_dir( $API::$configs[ "paths" ][ "company_uploads" ] ) )
            mkdir( $API::$configs[ "paths" ][ "company_uploads" ] );

        $talonFilePath = $API::$configs[ "paths" ][ "company_uploads" ] . "/talons";
        if ( !is_dir( $talonFilePath ) ) mkdir( $talonFilePath );


        if ( !file_exists( "$talonFilePath/$talonFileName.wav" ) ) {

            /**
             * Формирование аудио файла
             */

            shell_exec( "curl -X POST \
               -H \"Authorization: Bearer `/var/www/oxapi/data/yandex-cloud/bin/yc iam create-token`\" \
               --data-urlencode \"text=$synthText\" \\
               -d \"lang=ru-RU&voice=filipp&folderId=" . $settings[ "folder_id" ] . "&sampleRateHertz=48000&speed=0.8&format=lpcm\" \\
              \"https://tts.api.cloud.yandex.net/speech/v1/tts:synthesize\" > $talonFilePath/$talonFileName.raw" );

            chmod( "$talonFilePath/$talonFileName.raw", 777 );


            shell_exec("sox -r 48000 -b 16 -e signed-integer -c 1 $talonFilePath/$talonFileName.raw $talonFilePath/$talonFileName.wav" );
            unlink( "$talonFilePath/$talonFileName.raw" );

        } // if. !file_exists( $talonFilePath )


        $voice = substr( $talonFilePath, strpos( $talonFilePath, "/uploads" ) );
        $voice .= "/$talonFileName.raw";

    } // if. !$isAlert


    $resultVisits[] = [
        "is_alert" => $isAlert,
        "talon" => $visit[ "talon" ],
        "cabinet" => $cabinetDetail[ "title" ],
        "voice" => $voice
    ];

} // foreach. $visits as $visit


$API->returnResponse( $resultVisits );