<?php

namespace App\Enums;

enum MedicationType: string
{
    case Pill = 'pill';
    case Painkiller = 'painkiller';
    case Supplement = 'supplement';
}
