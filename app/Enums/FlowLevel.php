<?php

namespace App\Enums;

enum FlowLevel: string
{
    case Spotting = 'spotting';
    case Light = 'light';
    case Medium = 'medium';
    case Heavy = 'heavy';
}
