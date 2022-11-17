<?php
namespace chistiy;
class parsing {
    public function parsing() {
$id = '1t8A7zYEhB6osXWOWV8JxfOUaEYSvA8bp8Wu2s8rXbWg';
//звёздочки это ссыль на документ после spreadsheets/d/
$gid = '0';



$csv = file_get_contents('https://docs.google.com/spreadsheets/d/' . $id . '/export?format=csv&gid=' . $gid);

$csv = explode("\r\n", $csv);

$array = array_map('str_getcsv', $csv);



print_r($array);
    }
}