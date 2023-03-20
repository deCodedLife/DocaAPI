<?php



namespace Сashbox;

require_once "modifiers.php";

class Discounts
{

    public string $DB;

    public array $SaleModifiers;
    public array $DiscountModifiers;

    public function __construct() {

        $this->DB = "promotions";
        $this->ActiveItemsDB = "promotionsServices";
        $this->ActiveGroupsDB =  "promotionsServicesGroups";
        $this->RequiredItemsDB = "promotionsRequiredServices";
        $this->ExcludedItemsDB = "promotionsExcludedServices";
        $this->RequiredGroupsDB = "promotionsRequiredServicesGroups";
        $this->ExcludedGroupsDB = "promotionsRequiredServicesGroups";
        $this->NecessaryClientsGroupsDB = "promotionsClientsGroups";

    }

//    private function getModifiers( $table,  )


    public function GetActiveDiscounts() : array {

        global $API;

        $returnDiscounts = [];
        $discountList = $API->DB->from( $this->DB )
            ->where( "is_active", "Y" );

        foreach ( $discountList as $discount ) {

            if ( !$discount[ "begin_at" ] ) $discount[ "begin_at" ] = date( 'Y-m-d' );

            if ( strtotime( $discount[ "begin_at" ] ) > strtotime( date( 'Y-m-d' ) ) )
                continue;

            if ( $discount[ "end_at" ] ) {

                if ( strtotime( $discount[ "end_at" ] ) < strtotime( date( 'Y-m-d' ) ) )
                    continue;

            }

            $returnDiscounts[] = $API->DB->from( $this->DB )->where( "id", $discount[ "id" ] )->fetch();

        }

        return $returnDiscounts;

    }


}