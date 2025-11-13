<?php
return [
    'roles' => [
        'Admin' => ['1'],
        'Manager' => ['2', '5'],
        'User' => ['6', '7', '4', '3', '8', '9', '10', '11', '12', '13'],
    ],
    'departments' => [
        'All' => ['1', '3', '5'],
        'AESupp' => ['2'],
        'AE' => ['4'],
        'QC' => ['7'],
        'QA' => ['6'],
        'VIP' => [],
        'Other' => ['8', '9', '10', '11', '12', '13'],
    ],
    'layouts' => [
        'Admin' => 'layouts.admin',
        'Manager' => 'layouts.manager',
        'User' => 'layouts.user',
    ],
];