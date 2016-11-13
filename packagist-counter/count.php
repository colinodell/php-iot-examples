<?php

$json = json_decode(file_get_contents('https://packagist.org/packages/league/commonmark.json'), true);

header('Content-Type: text/plain');
echo $json['package']['downloads']['total'];
