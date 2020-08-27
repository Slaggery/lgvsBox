<?php

class APIInvoice
{
    public static function deleteInvoice($idInvoice = null)
    {
        $CCrmInvoice = new CCrmInvoice;

        $result = $CCrmInvoice->Delete($idInvoice);
        return $result;
    }

    public static function getInvoiceList($filter = [], $select = [])
    {
        $invoices = CCrmInvoice::GetList([], $filter, false, false, $select);

        $result = [];
        while ($arInvoices = $invoices->Fetch()) {
            $result[] = $arInvoices;
        }
        return $result;
    }

    public static function addInvoice($data = [])
    {
        $addInvoice = new CCrmInvoice;

        $resultAddInvoice = $addInvoice->Add($data);

        if ($resultAddInvoice) {
            return $resultAddInvoice;
        }

        return $addInvoice->LAST_ERROR;
    }

    public static function updateInvoice($idInvoice = null, $data = [])
    {
        $updateInvoice = new CCrmInvoice;

        $resultUpdateInvoice = $updateInvoice->Update($idInvoice, $data);

        if ($resultUpdateInvoice) {
            return $resultUpdateInvoice;
        }

        return $updateInvoice->LAST_ERROR;
    }

    public static function getInvoiceById($idInvoice = null)
    {
        $result = CCrmInvoice::GetById($idInvoice);
        return $result;
    }
}

class APIContacts
{
    public static function getContactById($idContact = null)
    {
        $result = CCrmContact::GetById($idContact);
        return $result;
    }

    public static function addContact($data = [])
    {
        $CCrmContact = new CCrmContact;

        $result = $CCrmContact->Add($data);

        if ($result) {
            return $result;
        }

        return $CCrmContact->LAST_ERROR;
    }

    public static function updateContact($idContact = null, $data = [])
    {
        $CCrmContact = new CCrmContact;

        $result = $CCrmContact->Update($idContact, $data);

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

    public static function collectPhone($phones = [])
    {
        $phones = explode(",", $phones);

        $arPhone = [];
        foreach ($phones as $index => $phone) {
            if ($phone != "") {
                $arPhone["n$index"]["VALUE"] = $phone;
                $arPhone["n$index"]["VALUE_TYPE"] = "WORK";
            }
        }

        return $arPhone;
    }

    public static function collectEmail($emails = [])
    {
        $emails = str_replace(" ", "", $emails);
        $emails = explode(",", $emails);

        $arEmail = [];
        foreach ($emails as $index => $email) {
            if ($email != "") {
                if (preg_match("/^[a-zA-Z0-9_\-.]+@[a-zA-Z0-9\-]+\.[a-zA-Z0-9\-.]+$/", $email)) {
                    $arEmail["n$index"]["VALUE"] = $email;
                    $arEmail["n$index"]["VALUE_TYPE"] = "WORK";
                }
            }
        }
        return $arEmail;
    }

    public static function deleteContacts($id = null, $entity_id = null)
    {
        $contactsResMultiFields = CCrmFieldMulti::GetList(array(), array('ELEMENT_ID' => $id, 'ENTITY_ID' => $entity_id));

        $CCrmFieldMulti = new CCrmFieldMulti();

        $result = [];
        while ($contact = $contactsResMultiFields->GetNext()) {
            $result[] = $CCrmFieldMulti->Delete($contact['ID']);
        }

        return $result;
    }

    public static function getContacts($filter = [])
    {
        $contacts = [];
        $contactsResMultiFields = CCrmFieldMulti::GetList([], $filter);
        while ($arContacts = $contactsResMultiFields->GetNext()) {
            $contacts[] = $arContacts;
        }

        return $contacts;
    }

    public static function deleteContact($idContact = null)
    {
        $CCrmContact = new CCrmContact;

        $result = $CCrmContact->Delete($idContact);

        if ($result) {
            return $result;
        }

        return $CCrmContact->LAST_ERROR;
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

    public static function getCompanyById($idCompany = null)
    {
        $result = CCrmCompany::GetById($idCompany);
        return $result;
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

    public static function deleteCompany($idCompany = null)
    {
        $CCrmCompany = new CCrmCompany;

        $result = $CCrmCompany->Delete($idCompany);

        if ($result) {
            return $result;
        }

        return $CCrmCompany->LAST_ERROR;
    }

    public static function getRequisiteList($filter = [], $select = ['*'])
    {
        $filter['ENTITY_TYPE_ID'] = CCrmOwnerType::Company;

        $CCrmRequisites = new \Bitrix\Crm\EntityRequisite();

        $requisiteList = $CCrmRequisites->getList(['order' => ["ID" => "ASC"], 'filter' => $filter, 'select' => $select]);

        $requisite = [];
        while ($requisites = $requisiteList->Fetch()) {
            $requisite[] = $requisites;
        }
        return $requisite;
    }

    public static function getBankRequisiteList($filter = [], $select = ['*'])
    {
        $CCrmRequisites = new \Bitrix\Crm\EntityBankDetail();

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

    public static function getAddress($idRequisite = null)
    {
        $result = \Bitrix\Crm\EntityRequisite::getAddresses($idRequisite);
        return $result;
    }

    public static function getRequisitesById($idRequisites = null)
    {
        $CCrmRequisites = new \Bitrix\Crm\EntityRequisite;

        $result = $CCrmRequisites->getById($idRequisites);
        return $result;
    }

    public static function getRequisites($innCompany)
    {
        $client = new \Bitrix\socialservices\properties\Client;
        $arRequisite = $client->getByInn($innCompany);
        if ($arRequisite) return $arRequisite;

        return $client->LAST_ERROR;
    }

    public static function updateRequisite($idRequisite = null, $data = [])
    {
        $CCrmRequisites = new \Bitrix\Crm\EntityRequisite;

        $result = $CCrmRequisites->Update((int)$idRequisite, $data);
        return $result;
    }
}

class APILists
{
    public static function updateElement($idElement = null, $arFields = [])
    {
        $el = new CIBlockElement;

        $data = [
            'NAME' => $arFields->Name,
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
            'NAME' => $arFields->Name,
            'PROPERTY_VALUES' => [
                'GUID1C' => $arFields->Guid1C
            ]
        ];

        $result = $el->Add($data, false, false, true);

        if ($result) {
            return $result;
        }

        return $el->LAST_ERROR;
    }

    public static function getElement($filter = [], $arSelect = [])
    {
        $elements = [];
        $order = ['SORT' => 'ASC'];
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

class APILead
{
    public static function addLead($data)
    {
        $CCrmLead = new CCrmLead;
        $result = $CCrmLead->Add($data);
        return $result;
    }
}

class APIActivity
{
    public static function addActivity($data)
    {
        $CCrmActivity = new CCrmActivity;

        $result = $CCrmActivity->Add($data);

        return $result;
    }

    public static function getActivityList($filter = [], $select = [])
    {
        $CCrmActivity = new CCrmActivity;

        $activityList = $CCrmActivity->GetList([], $filter, false, false, $select);

        $result = [];
        while ($arActivityList = $activityList->Fetch()) {
            $result[] = $arActivityList;
        }

        return $result;
    }

    public static function updateActivity($id = null, $data = [])
    {
        $CCrmActivity = new CCrmActivity;

        $result = $CCrmActivity->Update($id, $data);
        return $result;
    }
}

class APITasks
{
    public static function addTask($data)
    {
        $CTasks = new CTasks;
        $result = $CTasks->Add($data);

        if ($result) return $result;
        return $CTasks->GetErrors();
    }
}

class APIDeal
{
    public static function getDealList($filter = [], $select = [])
    {
        $dealList = CCrmDeal::GetList([], $filter, $select);

        $result = [];
        while ($arDealList = $dealList->Fetch()) {
            $result[] = $arDealList;
        }

        return $result;
    }

    public static function updateDeal($id = null, $data = [])
    {
        $CCrmDeal = new CCrmDeal;

        $result = $CCrmDeal->Update($id, $data);
        return $result;
    }

    public static function getDealById($idDeal = null)
    {
        $result = CCrmDeal::GetById($idDeal);

        return $result;
    }

    public static function addDeal($data = [])
    {
        $CCrmDeal = new CCrmDeal;

        $result = $CCrmDeal->Add($data);
        return $result;
    }
}

class Log
{
    public static function logFile($message = null, $data = [], $logName = null)
    {
        //if (is_array($data)) {
        $data = json_encode($data, 256);
        //}

        $file = '/home/bitrix/ext_www/default/local/logs/' . $logName;
        $text = "=======================================================\n";
        $text .= $message . $data;
        $text .= "\n" . date('Y-m-d H:i:s') . "\n";
        $fOpen = fopen($file, 'a');
        fwrite($fOpen, $text);
        fclose($fOpen);
    }
}