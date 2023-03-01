<?php

use Bitrix\Main\ArgumentException;
use Bitrix\Main\Loader;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\SystemException;
use free\Main\Orm\FilesFormatTable;

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
        $id = '****
        ';
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

        $updateFormats = [];
        foreach ($this->csv as $array) {
            $formatFrom = $array[7];
            $formatTo = $array[9];
            $idFormatFrom = $this->formats[$formatFrom]['ID'];
            $idFormatTo = $this->formats[$formatTo]['ID'];
            if (isset($idFormatTo, $idFormatFrom)) {

                if (in_array($idFormatTo, $this->formats[$formatFrom]['UF_FORMAT_TO'])) {
                    continue;
                }
                $this->formats[$formatFrom]['UF_FORMAT_TO'][] = $idFormatTo;
                $updateFormats[] = $formatFrom;
//                $result = FilesFormatTable::update($idForUpdate, ['UF_FORMAT_TO' => $this->formats[$array[7]]['UF_FORMAT_TO']]);
                echo '<pre>';
                var_dump($idFormatTo);
                echo '<pre>';
            } else {
                echo "";
            }
        }

        var_dump($updateFormats);
        $updateFormats = array_unique($updateFormats);
        foreach ($updateFormats as $format) {
            $id = $this->formats[$format]['ID'];
            $result = FilesFormatTable::update($id, ['UF_FORMAT_TO' => $this->formats[$format]['UF_FORMAT_TO']]);
        }

    }


}

new Parsing();
