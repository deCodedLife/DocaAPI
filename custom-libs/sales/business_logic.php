<?php

require_once "index.php";
require_once "promotions/index.php";

use Sales\Sales;
use Sales\Product;
use Sales\Discount;
use Sales\Subject;
use Sales\Modifier;

class Doca
{
    private array $visits;
    private bool $isReturn;

    private static function getVisitDetails( $visitID ) {

        global $API;
        return $API->DB->from( "visits" )
            ->where( "id", $visitID )
            ->fetch();

    } // function. getVisitDetails $visitID

    private function getVisits() {

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
                $this->visits[] = getVisitDetails( $combinedVisit[ "id" ] );

        } else {

            foreach ( !$this->isReturn ? [ $requestData->id ] : $requestData->visits_ids as $visit_id )
                $this->visits[] = getVisitDetails( $visit_id );

        } // if. $requestData->is_combined == 'Y'

    } // private function getVisits() {

    public function __construct( ) {
        global $API;

        $this->visits = [];
        $this->getVisits();

        $API->returnResponse( json_encode( $this->visits ), 500 );

        $visits = mysqli_query(
            $API->DB_connection,
            "SELECT * FROM visits WHERE ID IN (" . join( ",", $this->visits ) . ")"
        );

        foreach ( $visits as $visit ) {

            $Discount = new Discount();

            if ( $visit[ "discount_value" ] != 0 ) {

                $this->Discount->DiscountModifiers[] = new Modifier();

            }

            $serviceList = mysqli_query(
              $API->DB_connection,
              "SELECT * FROM visits_services WHERE visit_id = {$visit[ "id" ]}"
            );

            foreach ( $serviceList as $index => $service ) {



            }

        }
    }
}