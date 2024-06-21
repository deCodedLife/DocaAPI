<?php


if ( $API->isPublicAccount() ) {
    $advert = $API->DB->from("advertise")
        ->where( "title like :name", [ ":name" => "%сайт%" ] )
        ->fetch();

    $requestData->advertise_id = $advert[ "id" ];
}

/**
 * Подстановка геолокации
 */
if ( $requestData->address )  {

    try {

        /**
         * запроса к API
         */
        $apiRequest = curl_init( 'https://geocode-maps.yandex.ru/1.x/?apikey=99d27a37-a2bc-4a4a-ac4f-99ad602977f3&format=json&geocode=' . urlencode( $requestData->address ) );
        curl_setopt( $apiRequest, CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $apiRequest, CURLOPT_SSL_VERIFYPEER, false );
        curl_setopt( $apiRequest, CURLOPT_HEADER, false );
        $yandexResponse = curl_exec( $apiRequest );
        curl_close( $apiRequest );

        if ( $yandexResponse ) {

            /**
             * Извлечение координат
             */
            $yandexResponse = json_decode( $yandexResponse, true );

            if ( $yandexResponse['response']['GeoObjectCollection']['featureMember'][0]['GeoObject']['Point']['pos'] ) {

                $geolocation = $yandexResponse['response']['GeoObjectCollection']['featureMember'][0]['GeoObject']['Point']['pos'] ?? "";
                $requestData->geolocation = $geolocation;

            }


        }

    } catch ( Throwable $e ) {

        $API->returnResponse( "Гео код не подтянулся: $e", 500 );

    }

}

if ( !$requestData->phone && !$requestData->second_phone ) {

    $API->returnResponse( "Необходимо указать основной или дополнительный телефон", 400 );

}