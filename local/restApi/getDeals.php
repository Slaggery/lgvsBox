<?php
class Deals {
    public static function whatDoDeals($query)
    {
        $guidCompany = ($_REQUEST['guid']);

        $company = APICompany::getCompanyList(['UF_GUID' => $guidCompany], ['ID']);

        $dealsList = APIDeal::getDealList(['COMPANY_ID' => $company[0]['ID'], 'STAGE_ID' => 'EXECUTING'], ['ID', 'TITLE', 'DATE_CREATE']);
        return $dealsList;
    }
}