<?php

ini_set( 'display_errors', 1 );

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
                $this->visits[] = $this->getVisitDetails( $combinedVisit[ "id" ] );

        } else {

            foreach ( !$this->isReturn ? [ $requestData->id ] : $requestData->visits_ids as $visit_id )
                $this->visits[] = $this->getVisitDetails( $visit_id );

            foreach ( $this->visits as $visit )
                $this->saleVisits[] = $visit[ "id" ];

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
    }

    /**
     * @return array
     */
    public function Hook() {

        global $API, $requestData;
        $this->getVisits();

        $this->isReturn = $API->DB->from( "visits" )
                ->where( "id", $this->saleVisits[ 0 ] )
                ->limit( 1 )
                ->fetch()[ "is_payed" ] == 'Y';

        foreach ( $this->visits as $visit ) {

            $Discount = new Discount();

            if ( $visit[ "discount_value" ] != 0 ) {

                $this->Discount->DiscountModifiers[] = new Modifier();

            }
        }

        /**
         * Получение информации обо всех услугах в посещениях
         */
        foreach ( $this->visits as $visit ) {

            $visitServices = $API->DB->from( "visits_services" )
                ->where( "visit_id", $visit[ "id" ] );

            foreach ( $visitServices as $visitService ) {
                $this->saleServices[] = $this->getServiceDetails( $visitService["service_id"] );
                $this->allServices[] = end( $this->saleServices );
            }

        } // foreach. $saleVisits as $saleVisit



        /**
         * Если тип операции - "возврат", тогда собирается информация только о
         * прикреплённых к данной операции услугах
         */

        if ( $this->isReturn ) {

            $this->saleServices = [];

            $soldSales = $API->DB->from( "saleProductsList" )
                ->where( [
                    "sale_id" => $requestData->id,
                    "type" => "service"
                ]);

            foreach ( $requestData->pay_object as $index => $saleID ) {

                $details = $this->getServiceDetails( $saleID );

                foreach ( $soldSales as $soldSale ) {

                    if ( $saleID != $soldSale[ "service_id" ] ) continue;
                    $details[ "price" ] = $soldSale[ "price" ];

                }

                $this->saleServices[] = $details;

            }

            foreach ( $this->allServices as $index => $sale ) {

                foreach ( $soldSales as $soldSale ) {

                    if ( $sale[ "id" ] != $soldSale[ "service_id" ] ) continue;
                    $sale[ "price" ] = $soldSale[ "price" ];
                    $this->allServices[ $index ] = $sale;

                }

            }

        } // if. $this->isReturn



        /**
         * Получение итоговой суммы продажи
         */

        foreach ( $this->visits as $visit ) {

            $visitPrice = $visit[ "price" ];

            if ( $visit[ "discount_type" ] == "fixed"   ) $visitPrice -= $visit[ "discount_value" ];
            if ( $visit[ "discount_type" ] == "percent" ) $visitPrice -= ($visitPrice / 100) * $visit[ "discount_value" ];

            $visitPrice = max( $visitPrice, 0 );
            $this->saleSummary += $visitPrice;

        } // foreach. $saleVisits as $visit

        if ( $this->isReturn ) {

            $saleDetails = $API->DB->from( "sales" )
                ->where( "id", $requestData->id )
                ->fetch();

            $this->saleSummary = $saleDetails[ "summary" ];

        }

        foreach ( Discount::GetActiveDiscounts( "promotions" ) as $discount ) {

            // При возврате не считаем скидки
            if ( $this->isReturn ) continue;



            $servicesGroups = [];
            $Discount->GetModifiers( "promotion_id", $discount[ "id" ] );



            /**
             * Добавляем услуги как участников акции
             */
            foreach ( $this->allServices as $service ) {

                $Discount->Subjects[] = new Subject(
                    "services",
                    $service[ "id" ],
                    $service[ "price" ],
                    Discount::getGroups( $service[ "category_id" ], "serviceGroups" )
                );

            } // foreach $allServices as $service



            /**
             * Не забываем про клиентов
             */
            foreach ( $API->DB->from( "clientsGroupsAssaciation" )->where( "client_id", $requestData->client_id ) as $group )
                $clientGroups[] = $group[ "clientGroup_id" ];

            $Discount->Subjects[] = new Subject(
                "clients",
                $requestData->client_id,
                0,
                $clientGroups ?? []
            );



            /**
             * Смотрим, подходит акция под наши условия
             */
            if ( !$Discount->IsValid() ) continue;
            $newSubjects = $Discount->Apply( $discount[ "id" ] );
            $discountSum = 0;

            foreach ( $newSubjects as $subject ) {

                foreach ( $this->allServices as $index => $service ) {

                    if (  $subject->Type == "services" && $service[ "id" ] == $subject->ID && $service[ "price" ] != $subject->Price ) {

                        $discountSum -= $subject->Price - $service[ "price" ];
                        $service[ "price" ] = $service[ "price" ] - $discountSum;
                        $this->allServices[ $index ] = $service;

                        // May cause error in sale return case
                        $this->saleServices[ $index ] = $service;

                    }

                }

            }



            $this->saleSummary -= $discountSum;

        } // foreach. Discount::GetActiveDiscounts( "promotions" ) as $discount

        $saleSummary = max( $this->saleSummary, 0 );

        /**
         * Вычет депозита и бонусов для расчёта сумм налички и карты
         */

        $amountOfPhysicalPayments = 0;
        $amountOfPhysicalPayments = $this->saleSummary - ( $requestData->sum_bonus + $requestData->sum_deposit );

        $saleServicesPrice = 0;
        $allServicesPrice = 0;

        /**
         * Подсчёт стоимости посещения без скидок
         */

        foreach ( $this->allServices as $service )
            $allServicesPrice += $service[ "price" ];

        foreach ( $this->saleServices as $service )
            $saleServicesPrice += $service[ "price" ];



        /**
         * Нахождение скидки для товаров по формуле (стоимость со скидками / стоимость без скидок)
         */

        $discountPerProduct = $amountOfPhysicalPayments / $allServicesPrice;



        /**
         * Нахождение суммы для налички и карты с учётом скидок
         */

        $amountOfPhysicalPayments = $saleServicesPrice * $discountPerProduct;
        $amountOfPhysicalPayments = round( $amountOfPhysicalPayments, 2 );

        $this->saleSummary = $amountOfPhysicalPayments;

//            if ( $requestData->return_bonuses == "Y" ) $saleSummary += $requestData->sum_bonus;
//            if ( $requestData->return_deposit == "Y" ) $saleSummary += $requestData->sum_deposit;

        if ( $this->isReturn ) {

            $saleServicesPrice = 0;
            $allServicesPrice = 0;

            /**
             * Подсчёт стоимости посещения без скидок
             */

            foreach ( $this->allServices as $service )
                $allServicesPrice += $service[ "price" ];

            foreach ( $this->saleServices as $service )
                $saleServicesPrice += $service[ "price" ];



            /**
             * Нахождение скидки для товаров по формуле (стоимость со скидками / стоимость без скидок)
             */

            $discountPerProduct = $amountOfPhysicalPayments / $allServicesPrice;



            /**
             * Нахождение суммы для налички и карты с учётом скидок
             */

            $amountOfPhysicalPayments = $saleServicesPrice * $discountPerProduct;
            $amountOfPhysicalPayments = round( $amountOfPhysicalPayments, 2 );

            $this->saleSummary = $amountOfPhysicalPayments;

            if ( $requestData->return_bonuses == "Y" ) $this->saleSummary += $requestData->sum_bonus;
            if ( $requestData->return_deposit == "Y" ) $this->saleSummary += $requestData->sum_deposit;

        } // if. $this->isReturn

        $formFieldsUpdate = [];

        if ( $this->sum_cash > $amountOfPhysicalPayments ) $this->sum_cash = $amountOfPhysicalPayments;
        if ( $this->sum_card > $amountOfPhysicalPayments ) $this->sum_card = $amountOfPhysicalPayments;



        /**
         * Подсчёт суммы списания с карты и наличными в зависимости от выбранного типа оплаты
         */

        if ( $requestData->pay_method == "card" ) {

            $formFieldsUpdate[ "sum_cash" ][ "is_visible" ] = false;
            $formFieldsUpdate[ "sum_card" ][ "is_visible" ] = true;
            $this->sum_card = $amountOfPhysicalPayments;
            $this->sum_cash = 0;

        } // if. $requestData->pay_method == "card"

        if ( $requestData->pay_method == "parts" ) {

            $formFieldsUpdate[ "sum_card" ][ "is_visible" ] = true;
            $formFieldsUpdate[ "sum_cash" ][ "is_visible" ] = true;
            $this->sum_card = $amountOfPhysicalPayments - $this->sum_cash;
            $this->sum_card = $this->sum_cash >= $amountOfPhysicalPayments ? 0 : $this->sum_card;

        } // if. $requestData->pay_method == "parts"

        if ( $requestData->pay_method == "cash" ) {

            $formFieldsUpdate[ "sum_card" ][ "is_visible" ] = false;
            $formFieldsUpdate[ "sum_cash" ][ "is_visible" ] = true;
            $this->sum_cash = $amountOfPhysicalPayments;
            $this->sum_card = 0;

        } // if. $requestData->pay_method == "cash"



        /**
         * Заполнение и отправка формы
         */

        $clientDetails = $API->DB->from( "clients" )
            ->where( "id", $requestData->client_id )
            ->fetch();


        foreach ( $this->visits as $visit )
            $formFieldsUpdate[ "visits_ids" ][ "value" ][] = $visit[ "id" ];

        foreach ( $this->saleServices as $service )
            $formFieldsUpdate[ "products" ][ "value" ][] = $service[ "id" ];

        $formFieldsUpdate[ "services" ][ "value" ] = $this->saleServices;



        $formFieldsUpdate[ "sum_cash" ][ "value" ] = max( $this->sum_cash, 0 );
        $formFieldsUpdate[ "sum_card" ][ "value" ] = max( $this->sum_card, 0 );
        $formFieldsUpdate[ "summary" ][ "value" ] = $saleSummary;

        $formFieldsUpdate[ "pay_type" ][ "is_visible" ] = false;
        $formFieldsUpdate[ "visits_ids" ][ "is_visible" ] = false;
        $formFieldsUpdate[ "store_id" ][ "is_visible" ] = false;
        $formFieldsUpdate[ "client_id" ][ "is_visible" ] = count(
                $API->DB->from( "visits_clients" )
                    ->where( "visit_id", $this->visits[ 0 ][ "id" ] )
            ) > 1;

        if ( $this->isReturn ) {

            $formFieldsUpdate[ "sum_deposit" ][ "is_visible" ] = false;
            $formFieldsUpdate[ "sum_bonus" ][ "is_visible" ] = false;

        }

        return $formFieldsUpdate;
    }
}