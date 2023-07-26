<?php

class Doca
{
    private array $visits;
    private bool $isReturn;
    private array $saleVisits;
    private array $saleServices;
    private array $allServices;
    private float $saleSummary;
    private float $sum_cash;
    private float $sum_card;

    private static function getVisitDetails( $visitID ) {

        global $API;
        return $API->DB->from( "visits" )
            ->where( "id", $visitID )
            ->fetch();

    } // function. getVisitDetails $visitID

    private static function getServiceDetails( $serviceID ) {

        global $API;
        return $API->DB->from( "services" )
            ->where( "id", $serviceID )
            ->fetch();

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
                $saleServices[] = $this->getServiceDetails( $visitService["service_id"] );
                $allServices[] = end( $saleServices );
            }

        } // foreach. $saleVisits as $saleVisit

    } // public function getServices( $visits ) {

    public function getVisits( &$allVisits, &$saleVisits ): void {

        global $API, $requestData;

        /**
         * Формирование списка комбинированных посещений
         */

        if ( $requestData->is_combined == 'Y' ) {

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

            foreach ( !$this->isReturn ? [ $requestData->id ] : $requestData->visits_ids as $visit_id )
                $allVisits[] = $this->getVisitDetails( $visit_id );

            foreach ( $this->visits as $visit )
                $saleVisits[] = $visit[ "id" ];

        } // if. $requestData->is_combined == 'Y'

    } // private function getVisits() {

    public function __construct( ) {
        global $API, $requestData;
        $this->saleVisits = $requestData->visits_ids;
        $this->allServices = [];
        $this->saleServices = [];
        $this->visits = [];
        $this->isReturn = false;
        $this->saleSummary = 0;
        $this->sum_cash = $requestData->sum_cash;
        $this->sum_card = $requestData->sum_card;
    } // public function __construct

}

$Doca = new Doca;