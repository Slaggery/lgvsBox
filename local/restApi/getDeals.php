<?php
header('Content-Type: text/html; charset=utf-8');

class Deals
{
    public static function whatDoDeals($query)
    {
        $guidCompany = ($_REQUEST['guid']);

        $company = APICompany::getCompanyList(['UF_GUID1C' => $guidCompany], ['ID']);

        $dealsList = APIDeal::getDealList(['COMPANY_ID' => $company[0]['ID'], 'STAGE_ID' => 'EXECUTING'], ['ID', 'TITLE', 'DATE_CREATE']);

        $result = [];
        foreach ($dealsList as $deal) {
            unset($deal['COMPANY_ID']);
            unset($deal['COMPANY_TITLE']);
            $result[] = $deal;
        }
        return $result;
    }
}