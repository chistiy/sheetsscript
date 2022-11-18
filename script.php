<?php

use Bitrix\Main\ArgumentException;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\SystemException;
use Onlineconvertfree\Main\Orm\FilesFormatTable;

$_SERVER['DOCUMENT_ROOT'] = realpath(__DIR__);
require_once 'bitrix/modules/main/cli/bootstrap.php';

class Parsing
{
    /**
     * @throws ObjectPropertyException
     * @throws SystemException
     * @throws ArgumentException
     */
    public function __construct()
    {
        CModule::IncludeModule('highloadblock');

        print_r($this->getFormats(), true);
    }

    /**
     * @throws ObjectPropertyException
     * @throws SystemException
     * @throws ArgumentException
     */
    public function getFormats(): array
    {
        return FilesFormatTable::query()
            ->setSelect(['*'])
            ->fetchAll();
    }

    public function getCsv(): array
    {
        $id = '***********';
//ссыль на документ после spreadsheets/d/
        $gid = '0';
// $gid = id страницы


        $csv = file_get_contents('https://docs.google.com/spreadsheets/d/' . $id . '/export?format=csv&gid=' . $gid);

        $csv = explode("\r\n", $csv);

        $array = array_map('str_getcsv', $csv);
        return $array;

        print_r($array);
    }
//    public function find()
//    {
//       return FilesFormatTable::query()
//    }
}

echo '<pre>';
var_dump(Parsing::getCsv($array));
echo '<pre>';