<?php
header('Content-type:application/json;charset=utf-8');

class Events
{
    public static function addContact($arFields)
    {
        $idContact = $arFields['ID'];
        $contact = APIContacts::getContactById($idContact);
        $idCompany = $contact['COMPANY_ID'];
        APICompany::updateCompany($idCompany, ['UF_SYNCHRONIZE' => 0]);
    }

    public static function updateContact($arFields)
    {
        $idContact = $arFields['ID'];
        $contact = APIContacts::getContactById($idContact);
        $idCompany = $contact['COMPANY_ID'];
        APICompany::updateCompany($idCompany, ['UF_SYNCHRONIZE' => 0]);
    }

    public static function updateRequisite($arFields)
    {
        $idRequisites = $arFields;

        $requisites = APICompany::getRequisitesById($idRequisites);

        Log::logFile('requisite: ', $requisites, 'eventUpdateRequisites.log');
        if ($requisites['RQ_INN'] !== null
            || $requisites['RQ_INN'] !== ''
        ) {
            APICompany::updateCompany($requisites['ENTITY_ID'], ['UF_SYNCHRONIZE' => 0]);

            $dealList = APIDeal::getDealList(['COMPANY_ID' => $requisites['ENTITY_ID'], 'STAGE_ID' => 'NEW']);
            //Log::logFile('dealList: ', $dealList, 'eventUpdateRequisites.log');

            $dealActivity = APIActivity::getActivityList(['OWNER_ID' => $dealList[0]['ID'], 'COMPLETED' => 'N', 'PROVIDER_ID' => 'TASKS']);
           // Log::logFile('dealActivity: ', $dealActivity, 'eventUpdateRequisites.log');
            $dealActivityUpdate = APIActivity::updateActivity($dealActivity[0]['ID'], ['COMPLETED' => 'Y']);
           // Log::logFile('dealActivityUpdate: ', $dealActivityUpdate, 'eventUpdateRequisites.log');
            //APIDeal::updateDeal($dealList[0]['ID'], ['STAGE_ID' => 'PREPARATION']);
        }
    }

    public static function updateCompany(&$arFields)
    {
        if (!isset($arFields['UF_SYNCHRONIZE'])) {
            $arFields['UF_SYNCHRONIZE'] = 0;
        }
        /*   Log::logFile('arFields: ', $arFields, 'eventUpdateCompany.log');

           if ($arFields['UF_SYNCHRONIZE'] != 1) {
               $isSynchronize = false;

               $company = APICompany::getCompanyList(['ID' => $arFields['ID']]);

               if (isset($arFields['TITLE'])) {
                   if ($arFields['TITLE'] != $company[0]['TITLE']) $isSynchronize = true;
                   Log::logFile('isSynchronizeTitle: ', $isSynchronize, 'eventUpdateCompany.log');
               }

               if (isset($arFields['FM'])) {
                   $contacts = APIContacts::getContacts(['ELEMENT_ID' => $arFields['ID']]);

                   $fm = [];
                   foreach ($contacts as $r => $contact) {
                       if ($contact['TYPE_ID'] === "PHONE") {
                           $fm['PHONE'][$contact['ID']] = [
                               'VALUE' => $contact['~VALUE'],
                               'VALUE_TYPE' => $contact['VALUE_TYPE']
                           ];
                       }

                       if ($contact['TYPE_ID'] === "EMAIL") {
                           $fm['EMAIL'][$contact['ID']] = [
                               'VALUE' => $contact['~VALUE'],
                               'VALUE_TYPE' => $contact['VALUE_TYPE']
                           ];
                       }
                   }

                   if ($arFields['FM'] != $fm) $isSynchronize = true;
                   Log::logFile('isSynchronizeFM: ', $isSynchronize, 'eventUpdateCompany.log');
               }

               if (isset($arFields['CONTACT_ID'])) {
                   $contactFace = APIContacts::getContactList(['COMPANY_ID' => $arFields['ID']]);

                   $contactArFields = [];
                   foreach ($contactFace as $item) {
                       $contactArFields[] = (int)$item['ID'];
                   }

                   if ($arFields['CONTACT_ID'] != $contactArFields) $isSynchronize = true;
                   Log::logFile('isSynchronizeCONTACT_ID: ', $isSynchronize, 'eventUpdateCompany.log');
               }

               if (isset($arFields['UF_REGION'])) {
                   if ($arFields['UF_REGION'] != $company[0]['UF_REGION']) $isSynchronize = true;
                   Log::logFile('isSynchronizeUF_REGION: ', $isSynchronize, 'eventUpdateCompany.log');
               }

               if (isset($arFields['COMMENTS'])) {
                   if ($arFields['COMMENTS'] != $company[0]['COMMENTS']) $isSynchronize = true;
                   Log::logFile('isSynchronizeCOMMENTS: ', $isSynchronize, 'eventUpdateCompany.log');
               }

               if (isset($arFields['ASSIGNED_BY_ID'])) {
                   $companyAssigned = APICompany::getCompanyById($arFields['ID']);
                   if ($arFields['ASSIGNED_BY_ID'] != $companyAssigned['ASSIGNED_BY_ID']) $isSynchronize = true;
                   Log::logFile('isSynchronizeASSIGNED_BY_ID: ', $isSynchronize, 'eventUpdateCompany.log');
               }

               if ($isSynchronize) {
                   $result = APICompany::updateCompany($company[0]['ID'], ['UF_SYNCHRONIZE' => '0']);
               }
           }*/
    }

    public static function updateDeal($arFields)
    {
        //Log::logFile('arFields', $arFields, 'eventUpdateDeal.log');
        if (isset($arFields['UF_CRM_1595324006085']) && $arFields['UF_CRM_1595324006085'] !== "") {
            Log::logFile('', $arFields, 'eventUpdateDeal.log');
            $dealActivity = APIActivity::getActivityList(['OWNER_ID' => $arFields['ID'],
                'SUBJECT' => 'Определить категорию товара',
                'COMPLETED' => 'N']);

            if (count($dealActivity) != 0) {
                $dealActivityUpdate = APIActivity::updateActivity($dealActivity[0]['ID'], ['COMPLETED' => 'Y']);
            } else {
                $updateDeal = APIDeal::updateDeal($arFields['ID'], ['STAGE_ID' => 'EXECUTING']);
            }
            //Log::logFile('updateDeal: ', $updateDeal, 'eventUpdateDeal.log');
        }

        if (isset($arFields['COMPANY_ID'])) {
            $requisites = APICompany::getRequisiteList(['ENTITY_ID' => $arFields['COMPANY_ID']]);

            if ($requisites[0]['RQ_INN'] != null || $requisites[0]['RQ_INN' != ""]) {
                $dealList = APIDeal::getDealList(['COMPANY_ID' => $arFields['COMPANY_ID'], 'STAGE_ID' => 'NEW']);
               // Log::logFile('dealListCompany: ', $dealList, 'eventUpdateDeal.log');
                $dealActivity = APIActivity::getActivityList(['OWNER_ID' => $dealList[0]['ID'], 'COMPLETED' => 'N', 'PROVIDER_ID' => 'TASKS', 'SUBJECT' => 'Заполнить название/инн в компании.']);
               // Log::logFile('dealActivityCompany: ', $dealActivity, 'eventUpdateDeal.log');
                $dealActivityUpdate = APIActivity::updateActivity($dealActivity[0]['ID'], ['COMPLETED' => 'Y']);
               // Log::logFile('dealActivityUpdateCompany: ', $dealActivityUpdate, 'eventUpdateDeal.log');
            }
        }
    }

    public static function disabler_ui()
    {
        $arJsConfig = array(
            'stages_disabler_ui' => array(
                'js' => '/local/js/stages_disabler.ui.js',
            ),
        );
        foreach ($arJsConfig as $ext => $arExt) {
            \CJSCore::RegisterExt($ext, $arExt);
        }
        CUtil::InitJSCore(array('stages_disabler_ui'));
    }
}