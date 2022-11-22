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
    private $formats = [];
    private $csv = [];

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
    private function getFormats(): array
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
        $id = '*****';
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
        if (!isset($this->formats[$this->csv[7]])) {
            //создавалка 1

            $id = FilesFormatTable::add(['UF_FORMAT' => $this->formats[$this->csv[9]]]);
            if ($id) {
                $this->formats[$this->csv[7]] = [
                    'ID' => $id,
                ];
            }
            unset($id);
        }

        if (!isset($this->formats[$this->csv[9]])) {
            //создавалка 1
            $id = FilesFormatTable::add(['UF_FORMAT' => $this->formats[$this->csv[9]]]);
            if ($id) {
                $this->formats[$this->csv[9]] = [
                    'ID' => $id,
                ];
            }
            unset($id);
        }
    }

    private function updateFormatTo()
    {
        $id = $this->formats[$this->csv[9]]['ID'];
        $idForUpdate = $this->formats[$this->csv[7]]['ID'];
        FilesFormatTable::update($idForUpdate, ['UF_FORMAT_TO' => [$id]]);
    }
}

echo __FILE__ . ' @ ' . __LINE__ . '<pre>' . print_r(Parsing::extendedFormats(), true) . '</pre>';

