<?php
$id = '********';
//звёздочки это ссыль на документ после spreadsheets/d/
$gid = '0';



$csv = file_get_contents('https://docs.google.com/spreadsheets/d/' . $id . '/export?format=csv&gid=' . $gid);

$csv = explode("\r\n", $csv);

$array = array_map('str_getcsv', $csv);



print_r($array);