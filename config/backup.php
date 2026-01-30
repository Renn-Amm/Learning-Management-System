<?php

return [
    'enabled' => env('BACKUP_ENABLED', true),
    'disk' => env('BACKUP_DISK', 's3'),
];
