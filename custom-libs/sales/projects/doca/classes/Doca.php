<?php
ini_set( "display_errors", false );
//global $TABLE;
//if ( !$TABLE ) $TABLE = "visits";

class Doca
{
    public $Table = "visits";

    private function getVisitDetails( $visitID, $table = null ) {

        if ( !$table ) $table = $this->Table;

        global $API;
        return $API->DB->from( $table )
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

            if ( $this->Table === "equipmentVisits") {

                $visitService = $API->DB->from( "services" )
                    ->innerJoin( "equipmentVisits on equipmentVisits.service_id = services.id" )
                    ->fetch();

                $saleServices[] = $visitService;
                $allServices[] = end( $saleServices );

                continue;

            }

            $visitServices = $API->DB->from( "visits_services" )
                ->where( "visit_id", $visit[ "id" ] );

            foreach ( $visitServices as $visitService ) {

                $saleServices[] = $this->getServiceDetails( $visitService[ "service_id" ], $visit[ "user_id" ] );
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

    public function visitsFromTable( $table, $start_at, $end_at ): array {

        global $API, $requestData;

        $combinedVisits = $API->DB->from( $table );
        $filters = [
            "$table.start_at > ?" => $start_at,
            "$table.end_at < ?" => $end_at,
            "$table.store_id" => (int) $requestData->store_id,
            "$table.is_active" => "Y",
            "$table.is_payed" => "N"
        ];

        if ( $table == "visits" ) {

            $combinedVisits->innerJoin( "visits_clients ON visits_clients.visit_id = $table.id" );
            $filters[ "visits_clients.client_id" ] = $requestData->client_id;

        } else $filters[ "$table.client_id" ] = $requestData->client_id;

        $combinedVisits->where( $filters );

        foreach ( $combinedVisits as $combinedVisit )
            $allVisits[] = $this->getVisitDetails( $combinedVisit[ "id" ], $table );

        return $allVisits ?? [];

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
            $end_at = date( "Y-m-d 23:59:59" );

            /**
             * Получение всех, неоплаченных клиентом, посещений
             */

            $allVisits = array_merge(
                $this->visitsFromTable( "visits", $start_at, $end_at ),
                $this->visitsFromTable( "equipmentVisits", $start_at, $end_at ),
            );

            $API->returnResponse( $allVisits );

        } else {

            foreach ( !$isReturn ? [ $requestData->id ] : $requestData->visits_ids ?? [] as $visit_id )
                $allVisits[] = $this->getVisitDetails( $visit_id );

            foreach ( $allVisits as $visit )
                $saleVisits[] = $visit[ "id" ];

        } // if. $requestData->is_combined == 'Y'

    } // private function getVisits() {

}

$Doca = new Doca;