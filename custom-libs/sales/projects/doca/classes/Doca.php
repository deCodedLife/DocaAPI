<?php

//global $TABLE;
//if ( !$TABLE ) $TABLE = "visits";

class Doca
{
    public $Table = "visits";

    private function getVisitDetails( $visitID ) {

        global $API;
        return $API->DB->from( $this->Table )
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

    } // public function getServices( $visits )

    public function getProducts ( &$allProducts, &$saleProducts ) {

        global $API;

        foreach ( $saleProducts as $product ) {

            $productDetails = $API->DB->from( "products" )
                ->where( "id", $product->id )
                ->fetch();

            $productDetails[ "amount" ] = $product->amount ?? 0;
            $allProducts[] = $productDetails;

        }

    }

    public function getVisits( &$allVisits, &$saleVisits, $isReturn ): void {

        global $API, $requestData, $TABLE;

        /**
         * Формирование списка комбинированных посещений
         */


        if ( !$allVisits ) return;
        $allVisits = [];

        if ( ( $requestData->is_combined ?? 'N') == 'Y' ) {

            $start_at = date( "Y-m-d 00:00:00" );
            $end_at = new DateTime();
            $end_at->modify( "+1 day" );
            $end_at = $end_at->format( "Y-m-d 23:59:59" );

            /**
             * Получение всех, неоплаченных клиентом, посещений
             */

            $combinedVisits = $API->DB->from( $this->Table )
                ->innerJoin( "visits_clients ON visits_clients.visit_id = visits.id" )
                ->where( [
                    "visits.start_at > ?" => $start_at,
                    "visits.end_at < ?" => $end_at,
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