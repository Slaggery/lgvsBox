<?php
class Region
{
    const IDBLOCK = 18;

    public static function whatDoRegion($query){
        $regions = json_decode($query['mData'], true);

        Log::logFile('Количество регионов: ', count($regions), 'addRegion.log');

        $result = [];
        foreach ($regions as $region) {
            $el = APILists::getElement(['IBLOCK_ID' =>self::IDBLOCK, 'PROPERTY_GUID1C' =>$region['Guid1C']]);

            if (count($el) == 0) {
                $result[] = APILists::addElement(self::IDBLOCK, $region);
                Log::logFile('Регион добавлен в список: ', current($result), 'addRegion.log');
            } else {
                $idElement = current($el);
                $result[] = APILists::updateElement($idElement['ID'], $region);
                Log::logFile('Регион обновлен в списке: ', current($result), 'addRegion.log');
            }
        }

        return $result;
    }
}