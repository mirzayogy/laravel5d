<?php

namespace App\Enums;

enum ReminderType: string
{
    case PeriodSoon = 'period_soon';
    case Fertile = 'fertile';
    case Pill = 'pill';
    case DailyLog = 'daily_log';
}
