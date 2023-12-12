<?php

class Doca
{
    private static function getVisitDetails( $visitID ) {

        global $API;
        return $API->DB->from( "visits" )
            ->where( "id", $visitID )
            ->fetch();

    } // function. getVisitDetails $visitID

    public static function getServiceDetails( $serviceID, $employee ) {

        global $API;

        $service = $API->DB->from( "services" )
            ->where( "id", $serviceID )
            ->fetch();

        $personalDetails = $API->DB->from( "workingTime" )
            ->where( [
                "row_id" => $service[ "id" ],
                "user" => $employee
            ] )
            ->fetch();

        if ( $personalDetails )
            $service[ "price" ] = $personalDetails[ "price" ];

        return $service;

    } // function. getServiceDetails $serviceID


    /**
     * Получение информации обо всех услугах в посещениях
     */
    public function getServices( $visits, &$allServices, &$saleServices ) {

        global $API;

        foreach ( $visits as $visit ) {

            $visitServices = $API->DB->from( "visits_services" )
                ->where( "visit_id", $visit[ "id" ] );

            foreach ( $visitServices as $visitService ) {
                $saleServices[] = $this->getServiceDetails( $visitService["service_id"], $visit[ "user_id" ] );
                $allServices[] = end( $saleServices );
            }

        } // foreach. $saleVisits as $saleVisit

    } // public function getServices( $visits ) {

    public function getVisits( &$allVisits, &$saleVisits, $isReturn ): void {

        global $API, $requestData;

        /**
         * Формирование списка комбинированных посещений
         */

        
        if ( !$allVisits ) return;
        $allVisits = [];

        if ( ($requestData->is_combined ?? 'N') == 'Y' ) {

            /**
             * Получение всех, неоплаченных клиентом, посещений
             */

            $combinedVisits = $API->DB->from( "visits" )
                ->innerJoin( "visits_clients ON visits_clients.visit_id = visits.id" )
                ->where( [
                    "visits.store_id" => (int) $requestData->store_id,
                    "visits_clients.client_id" => $requestData->client_id,
                    "visits.is_active" => "Y",
                    "visits.is_payed" => "N"
                ] );

            foreach ( $combinedVisits as $combinedVisit )
                $allVisits[] = $this->getVisitDetails( $combinedVisit[ "id" ] );

        } else {

            foreach ( !$isReturn ? [ $requestData->id ] : $requestData->visits_ids ?? [] as $visit_id )
                $allVisits[] = $this->getVisitDetails( $visit_id );

            foreach ( $allVisits as $visit )
                $saleVisits[] = $visit[ "id" ];

        } // if. $requestData->is_combined == 'Y'

    } // private function getVisits() {

}

$Doca = new Doca;