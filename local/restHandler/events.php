<?php
header('Content-type:application/json;charset=utf-8');

class Events
{
    public static function updateCompany(&$arFields)
    {
        if (!isset($arFields['UF_SYNCHRONIZE'])) {
            $arFields['UF_SYNCHRONIZE'] = "0";
        }
    }
}