<?php

use Bitrix\Main\ArgumentException;
use Bitrix\Main\Loader;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\SystemException;
use Onlineconvertfree\Main\Orm\FilesFormatTable;

$_SERVER['DOCUMENT_ROOT'] = realpath(__DIR__);
require_once 'bitrix/modules/main/cli/bootstrap.php';

class Parsing
{
    public $formats = [];
    public $csv = [];

    /**
     * @throws ObjectPropertyException
     * @throws SystemException
     * @throws ArgumentException
     */
    public function __construct()
    {
        Loader::IncludeModule('highloadblock');

        $this->formats = $this->getFormats();
        $this->csv = $this->getCsv();
        $this->extendedFormats();
        $this->updateFormatTo();
    }

    /**
     * @throws ObjectPropertyException
     * @throws SystemException
     * @throws ArgumentException
     */
    public function getFormats(): array
    {
        $result = FilesFormatTable::query()
            ->setSelect(['*'])
            ->fetchAll();
        foreach ($result as $item) {
            $formats[$item['UF_FORMAT']] = $item;

        }
        return $formats ?? [];
    }

    /**
     * @return array
     */
    public function getCsv(): array
    {
        $id = '1t8A7zYEhB6osXWOWV8JxfOUaEYSvA8bp8Wu2s8rXbWg';
        //ссыль на документ после spreadsheets/d/
        $gid = '0';
        // $gid = id страницы

        $csv = file_get_contents('https://docs.google.com/spreadsheets/d/' . $id . '/export?format=csv&gid=' . $gid);

        $csv = explode("\r\n", $csv);

        return array_map('str_getcsv', $csv);
    }

    /**
     * @return void
     */
    public function extendedFormats(): void
    {
        foreach ($this->csv as $array) {
            if (!isset($this->formats[$array[7]])) {
                $result = FilesFormatTable::add(['UF_FORMAT' => $array[7]]);
                if ($result->isSuccess()) {
                    $this->formats[$array[7]] = [
                        'ID' => $result->getId(),
                        'UF_FORMAT' => $array[7],
                    ];
                } else {
                    echo "произошёл факап";
                    var_dump($result->getErrorMessages());
                }

            }
        }


    }


    public function updateFormatTo()
    {

        foreach ($this->csv as $array) {
            $idForUpdate = $this->formats[$array[7]]['ID'];
            $id = [$this->formats[$array[9]]['ID']];
            if (isset($id, $idForUpdate)) {

                $data = $this->formats[$array[7]]['UF_FORMAT_TO'];
                $data[] = $id[0];

                $result = FilesFormatTable::update($idForUpdate, ['UF_FORMAT_TO' => $data]);
                echo '<pre>';
                var_dump($result);
                die();
                echo '<pre>';
            } else {
                echo "";
            }
        }
    }


}

new Parsing();
