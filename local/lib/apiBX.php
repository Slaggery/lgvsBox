<?php

class APIContacts
{
    public static function addContact($data = [])
    {
        $CCrmContact = new CCrmContact;

        $result = $CCrmContact->Add($data);

        if ($result) {
            return $result;
        }

        return $CCrmContact->LAST_ERROR;
    }

    public static function getContactList($filter = [], $select = [])
    {
        $contactList = CCrmContact::GetList([], $filter, $select);
        $contact = [];
        while ($arContact = $contactList->Fetch()) {
            $contact[] = $arContact;
        }

        return $contact;
    }

    public static function getPhone($phones = [])
    {
        $phones = str_replace(" ", "", $phones);
        $phones = explode(",", $phones);

        $arPhone = [];
        foreach ($phones as $index => $phone) {
            $arPhone["n$index"]["VALUE"] = $phone;
            $arPhone["n$index"]["VALUE_TYPE"] = "WORK";
        }

        return $arPhone;
    }

    public static function getEmail($emails = [])
    {
        $emails = str_replace(" ", "", $emails);
        $emails = explode(",", $emails);

        $arEmail = [];
        foreach ($emails as $index => $email) {
            if (preg_match("/^[a-zA-Z0-9_\-.]+@[a-zA-Z0-9\-]+\.[a-zA-Z0-9\-.]+$/", $email)) {
                $arEmail["n$index"]["VALUE"] = $email;
                $arEmail["n$index"]["VALUE_TYPE"] = "WORK";
            }
        }
        return $arEmail;
    }

    public static function deleteContact($idCompany = null)
    {
        $contactsResMultiFields = CCrmFieldMulti::GetList(array(), array('ELEMENT_ID' => $idCompany));

        $CCrmFieldMulti = new CCrmFieldMulti();

        $result = [];
        while ($contact = $contactsResMultiFields->GetNext()) {
            $result[] = $CCrmFieldMulti->Delete($contact['ID']);
        }

        return $result;
    }

}

class APICompany
{
    public static function getCompanyList($filter = [], $select = [])
    {
        $arCompanyList = CCrmCompany::GetList([], $filter, $select);

        $company = [];
        while ($companyList = $arCompanyList->Fetch()) {
            $company[] = $companyList;
        }
        return $company;
    }

    public static function addCompany($data = [])
    {
        $addCompany = new CCrmCompany;

        $resultAddCompany = $addCompany->Add($data);

        if ($resultAddCompany) {
            return $resultAddCompany;
        }

        return $addCompany->LAST_ERROR;
    }

    public static function addRequisite($data = [])
    {
        $CCrmRequisites = new \Bitrix\Crm\EntityRequisite;

        $result = $CCrmRequisites->Add($data);

        if ($result->isSuccess()) {
            return $result->getId();
        }

        return $result->getErrorMessages;
    }

    public static function addBankRequisite($data = [])
    {
        $CCrmBankRequisite = new \Bitrix\Crm\EntityBankDetail;

        $result = $CCrmBankRequisite->Add($data);

        if ($result->isSuccess()) {
            return $result->getId();
        }

        return $result->getErrorMessages;
    }

    public static function deleteCompany($idCompany)
    {
        $CCrmCompany = new CCrmCompany;

        $result = $CCrmCompany->Delete($idCompany);

        if ($result) {
            return $result;
        }

        return $CCrmCompany->LAST_ERROR;
    }

    public static function getRequisiteList($filter = [], $select = [])
    {
        $filter['ENTITY_TYPE_ID'] = CCrmOwnerType::Company;

        $CCrmRequisites = new \Bitrix\Crm\EntityRequisite();

        $requisiteList = $CCrmRequisites->getList(['filter' => $filter, 'select' => $select]);

        $requisite = [];
        while ($requisites = $requisiteList->Fetch()) {
            $requisite[] = $requisites;
        }
        return $requisite;
    }

    public static function deleteRequisite($idRequisite = null)
    {
        $CCrmRequisites = new \Bitrix\Crm\EntityRequisite();

        $result = $CCrmRequisites->delete($idRequisite);

        if ($result->isSuccess()) return true;
        return $result->getErrorMessages();
    }

    public static function updateCompany($idCompany, $data = [])
    {
        $CCrmCompany = new CCrmCompany;

        $result = $CCrmCompany->Update($idCompany, $data);

        if ($result) return $result;

        return $CCrmCompany->LAST_ERROR;
    }
}

class APILists
{
    public static function updateElement($idElement = null, $arFields = [])
    {
        $el = new CIBlockElement;

        $data = [
            'NAME' => $arFields['Name'],
        ];

        $result = $el->Update($idElement, $data);

        if ($result) {
            return $idElement;
        }

        return $el->LAST_ERROR;
    }

    public static function addElement($idBlock = null, $arFields = [])
    {
        $el = new CIBlockElement;

        $data = [
            'IBLOCK_ID' => $idBlock,
            'NAME' => $arFields['Name'],
            'PROPERTY_VALUES' => [
                'GUID1C' => $arFields['Guid1C']
            ]
        ];

        $result = $el->Add($data, false, false, true);

        if ($result) {
            return $result;
        }

        return $el->LAST_ERROR;
    }

    public static function getElement($idBlock = null, $guid1C = null, $arSelect = [])
    {
        $elements = [];
        $order = ['SORT' => 'ASC'];
        $filter = ['IBLOCK_ID' => $idBlock, 'PROPERTY_GUID1C' => $guid1C];
        $rows = CIBlockElement::GetList($order, $filter, false, false, $arSelect);
        while ($row = $rows->fetch()) {
            $row['PROPERTIES'] = [];
            $elements[$row['ID']] =& $row;
            unset($row);
        }

        CIBlockElement::GetPropertyValuesArray($elements, $filter['IBLOCK_ID'], $filter);
        unset($rows, $filter, $order);

        return $elements;
    }
}

class Log
{
    public static function logFile($message = null, $data = [], $logName = null)
    {
        if (is_array($data)) {
            $data = json_encode($data, 256);
        }

        $file = '/home/bitrix/www/local/logs/' . $logName;
        $text = "=======================================================\n";
        $text .= $message . $data;
        $text .= "\n" . date('Y-m-d H:i:s') . "\n";
        $fOpen = fopen($file, 'a');
        fwrite($fOpen, $text);
        fclose($fOpen);
    }
}