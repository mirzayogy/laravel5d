<?php

namespace App\Enums;

enum CyclePhase: string
{
    case Menstruation = 'menstruation';
    case Follicular = 'follicular';
    case Ovulation = 'ovulation';
    case Luteal = 'luteal';
}
