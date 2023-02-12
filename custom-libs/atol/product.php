<?php

namespace Atol;

/**
 * Продукт
 * {
 *      "amount": 10,
 *      "name": "Прием (тест) 1",
 *      "paymentObject": "service",
 *      "piece": true,
 *      "price": 10,
 *      "quantity": 1,
 *      "tax": {
 *          "type": "none"
 *      },
 *      "type": "position"
 * },
 * @var int     $amount         Сумма (Цена * Количество)
 * @var string  $name           Название товара / услуги
 * @var string  $paymentObject  Тип (товар, услуга, депозит...)
 * @var bool    $piece          Штучный товар
 * @var float   $price          Стоимость за единицу товара
 * @var int     $quantity       Количество товара
 * @var array   $taxes          Налоги
 * @var string  $type           Тип документа
 */
class IProduct
{
    public int $amount;
    public string $name;
    public string $paymentObject;
    public bool $piece;
    public float $price;
    public int $quantity;
    public array $taxes;
    public string $type;

    // FOR BARCODES
    private string $barcode;
    private string $barcodeType;
    private float $total;



    /**
     * Конвертирует продукт в JSON
     * @return array
     */
    public function ToJSON() {

        return [
            "amount" => $this->amount,
            "name" => $this->name,
            "paymentObject" => $this->paymentObject,
            "piece" => $this->quantity == 1,
            "price" => $this->price,
            "quantity" => $this->quantity,
            "tax" => $this->tax,
            "type" => $this->type
        ];

    } // public function ToJSON



    public function __construct() {

        $this->type = "position";

    }



    /**
     * Добавление QR кода в ленту чека
     *
     * @param $barcode
     * @param $barcodeType
     * @param $total
     * @return void
     */
    public function SetBarcode (
        $barcode,
        $barcodeType,
        $total
    ) {

        $this->barcode = $barcode;
        $this->barcodeType = $barcodeType;
        $this->total= $total;
        $this->type = "barcode";

    } // public function SetBarcode
}