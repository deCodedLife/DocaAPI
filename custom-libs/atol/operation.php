<?php

namespace Atol;

require_once ( "operator.php" );
require_once ( "payment.php" );
require_once ( "product.php" );

/**
 * Операция
 * {
 *      "callbacks": {
 *          "resultUrl": "http://127.0.0.1:80/receive"
 *      }
 *      "request": {
 *          "items": [
 *              {
 *                  "amount": 10,
 *                  "name": "Прием (тест) 1",
 *                  "paymentObject": "service",
 *                  "piece": true,
 *                  "price": 10,
 *                  "quantity": 1,
 *                  "tax": {
 *                      "type": "none"
 *                  },
 *                  "type": "position"
 *              },
 *              {
 *                  "barcode": "90311017",
 *                  "barcodeType": "EAN8",
 *                  "total": 10,
 *                  "type": "barcode"
 *              }
 *          ],
 *          "operator": {
 *              "name": "Миннахматовна Э. Ц.",
 *              "vatin": "123654789507",
 *          },
 *          "payments": [
 *              {
 *                  "sum": 10,
 *                  "type": 1
 *              }
 *          ],
 *          "taxationType": "usnIncome",
 *          "total": 10,
 *          "type": "sellReturn"
 *      }
 *      "uuid": "sale-id-1001"
 * }
 */
class ITransaction
{
    public IProduct $items;
    public IOperator $operator;
    public IPayment $payment;
    public string $taxationType;
    public float $total;
    public string $type;
    public int $saleID;
}