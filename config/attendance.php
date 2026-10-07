<?php

return [

    /*
    | A shift shorter than this many hours is marked "Half day" on the labourer's
    | daily work when they check out.
    */
    'half_day_hours' => (float) env('ATTENDANCE_HALF_DAY_HOURS', 4),

    /*
    | A second fingerprint within this many minutes of the last one is treated as
    | the same punch (people often press twice).
    */
    'duplicate_minutes' => (int) env('ATTENDANCE_DUPLICATE_MINUTES', 2),

    /*
    | Fingerprint devices.
    |
    | device_token   – secret for the generic punch endpoint (POST /attendance/device/punch).
    | device_serials – comma-separated serial numbers of ZKTeco-style devices allowed
    |                  to push to /iclock/cdata. Leave empty to refuse all devices.
    */
    'device_token' => env('ATTENDANCE_DEVICE_TOKEN'),

    'device_serials' => array_filter(array_map('trim', explode(',', (string) env('ATTENDANCE_DEVICE_SERIALS', '')))),

];
