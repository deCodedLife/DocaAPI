<?php

/**
 * @file Интеграция с кассой Атол
 */

namespace Atol;

define( "TAXATION_TYPES", [
    "osn",              // общая
    "usnIncome",        // упрощенная (Доход)
    "usnIncomeOutcome", // упрощенная (Доход минус Расход)
    "esn",              // единый сельскохозяйственный налог
    "patent"            // патентная система налогообложения
] );

class Atol
{
    public IProduct $items;
    public IOperator $operator;
    public IPayment $payments;
    public string $taxationType;
    public float $total;
    public string $type;
    public int $saleID;



    /**
     * Добавление продукта / услуги в чек
     * @param IProduct $product
     * @return void
     */
    public function addProduct( IProduct $product ): void
    {

        $this->items[] = $product;

    }


    
    /**
     * @return float
     */
    private function getItemsSummary(): float
    {

        $summary = 0.0;

        foreach ( $this->items as $item )
            $summary += $item->amount;


        return $summary;
    }


    /**
     * @return array
     */
    private function itemsToJSON(): array
    {

        $jsonItems = [];

        foreach ( $this->items as $item )
            $jsonItems[] = $item->ToJSON();

        return $jsonItems;

    }


    /**
     * Возвращает полный объект для печати чека
     * @return array
     */
    public function ToJSON(): array
    {

        $barcode = new IProduct;
        $barcode->SetBarcode( "90311017", "EAN8", $this->getItemsSummary() );

        $this->addProduct( $barcode );

        return [
            "request" => [
                "items" => $this->itemsToJSON(),
                "operator" => $this->operator->ToJSON(),
                "payments" => $this->payments->ToJSON(),
                "taxationType" => $this->taxationType,
                "total" => $this->getItemsSummary(),
                "type" => $this->type
            ],
            "uuid" => "sell-id-" . $this->saleID
        ];
    }


    /**
     * Задать форму налогообложения
     *
     * @param string $type
     * @return bool|string
     */
    public function setTaxationType( string $type ): bool | string
    {

        if ( !in_array( $type, TAXATION_TYPES ) )
            return "Данный тип налогообложения не поддерживается";

        $this->taxationType = $type;
        return true;

    } // public function setTaxationType
}