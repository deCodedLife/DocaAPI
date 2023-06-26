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

    public function __construct(
        $visitList
    ) {
        global $API;
        $this->Discount = new Discount();

        $visits = mysqli_query(
            $API->DB_connection,
            "SELECT * FROM visits WHERE ID IN (" . join( ",", $visitList ) . ")"
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