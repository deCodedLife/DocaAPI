<?php

namespace Atol;

/**
 * Объект оплаты
 * {
 *      "sum": 10,
 *      "type": "cash"
 * }
 * @var float           $sum    Внесённые средства
 * @var string | int    $type   Тип оплаты
 */
class IPayment
{
    public float $sum;
    public string | int $type;

    public function ToJSON(): array
    {

        return [
            "sum" => $this->sum,
            "type" => $this->type
        ];

    }
}