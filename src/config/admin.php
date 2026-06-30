<?php

$emails = array_filter(array_map(
    fn (string $email): string => strtolower(trim($email)),
    explode(',', (string) env('ADMIN_EMAILS', env('ADMIN_EMAIL', ''))),
));

return [
    'emails' => array_values(array_unique($emails)),
];
