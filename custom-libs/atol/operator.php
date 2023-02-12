<?php

namespace Atol;

/**
 * Оператора кассового аппарата
 * {
 *      "name": "Миннахматовна Э. Ц.",
 *      "vatin": "123654789507"
 * }
 * @var string $name  Имя Оператора
 * @var string $vatin ИНН Оператора
 */
class IOperator
{
    public string $name;
    public string $vatin;
    
    public function ToJSON(): array
    {
        
        return [
            "name" => $this->name,
            "vatin" => $this->vatin
        ];
        
    }
}