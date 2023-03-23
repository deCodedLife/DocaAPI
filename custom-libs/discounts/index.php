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

        foreach ( $this->GetActiveDiscounts() as $discount )
            $this->getModifiers( "promotion_id",  $discount[ "id" ] );

    }

    private function getModifiers( $param, $promotion_id ) {

        global $API;

        $modifiers = $API->DB->from( $this->DB . "Objects" )
            ->where( $param, $promotion_id );

        foreach ( $modifiers as $modifier ) {
            $this->DiscountModifiers[] = new IModifier(
                $modifier[ "object_id" ],
                $modifier[ "is_group" ] == 'Y',
                $modifier[ "is_required" ] == 'Y',
                $modifier[ "is_excluded" ] == 'Y'
            );
        }

    }


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