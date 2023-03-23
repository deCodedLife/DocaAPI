<?php

namespace Сashbox;



define( "OBJECTS_CATEGORIES", [
    "target",
    "targetGroups",
    "clients"
] );




class IModifier
{

    public string $Type;

    public int $ObjectID;
    public bool $IsGroup;
    public bool $IsRequired;
    public bool $IsExcluded;


    /**
     * Конструктор класса
     *
     * @param int|null $objectID
     * @param bool|null $isGroup
     * @param bool|null $isRequired
     * @param bool|null $isExcluded
     */
    public function __construct(
        int    $objectID = null,
        bool   $isGroup = null,
        bool   $isRequired = null,
        bool   $isExcluded = null
    ) {

        $this->Type = OBJECTS_CATEGORIES[ 0 ];
        $this->ObjectID = $objectID ?? 0;

        $this->IsGroup = $isGroup ?? false;
        $this->IsRequired = $isRequired ?? false;
        $this->IsExcluded = $isExcluded ?? false;

    } // public function __construct

}