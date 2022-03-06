<?php
declare(strict_types=1);
$headers = [
    "User-Agent: DawSula",
    "Authorization: token TODO"
];
$ch = curl_init();

curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

return $ch;