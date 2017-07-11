<?php
    use Spatie\Analytics\Period;

    $analyticsData = Analytics::fetchEvents(Period::days(7));
    dd($analyticsData);
