<?php

use Carbon\Carbon;



function getDateOFLast60Days()
{
    return Carbon::now()->subDays(60)->format('Y-m-d');
}
function getLastDateOfMonthAfterThreeYears()
{
    return Carbon::now()->addYears(3)->month(12)->day(31)->format('Y-m-d');
}
