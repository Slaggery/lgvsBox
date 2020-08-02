<?php
header('Content-type:application/json;charset=utf-8');

class Events
{
    public static function updateRequisite($arFields)
    {
        $idRequisites = $arFields;

        $requisites = APICompany::getRequisitesById($idRequisites);

        if ($requisites['RQ_INN'] !== null
            || $requisites['RQ_INN'] !== ''
            || $requisites['RQ_COMPANY_NAME'] !== null
            || $requisites['RQ_COMPANY_NAME'] !== ''
        ) {
            $dealList = APIDeal::getDealList(['COMPANY_ID' => $requisites['ENTITY_ID'], 'STAGE_ID' => 'NEW']);

            $dealActivity = APIActivity::getActivityList(['OWNER_ID' => $dealList[0]['ID'], 'COMPLETED' => 'N']);
            $dealActivityUpdate = APIActivity::updateActivity($dealActivity[0]['ID'], ['COMPLETED' => 'Y']);
        }
    }

    public static function updateCompany(&$arFields)
    {
        if (!isset($arFields['UF_SYNCHRONIZE'])) {
            $arFields['UF_SYNCHRONIZE'] = "0";
        }
    }

    public static function updateDeal($arFields)
    {
        if (isset($arFields['UF_CRM_1595324006085']) && $arFields['UF_CRM_1595324006085'] !== "") {
            $dealActivity = APIActivity::getActivityList(['OWNER_ID' => $arFields['ID'],
                'SUBJECT' => 'Определить категорию товара',
                'COMPLETED' => 'N']);
            $dealActivityUpdate = APIActivity::updateActivity($dealActivity[0]['ID'], ['COMPLETED' => 'Y']);
        }

        if (isset($arFields['UF_CRM_1582775986809'])
        || isset($arFields['UF_CRM_1582775957638'])) {
            if ($arFields['UF_CRM_1582775986809'] !== "") {
                $dealActivity = APIActivity::getActivityList(['OWNER_ID' => $arFields['ID'],
                    'SUBJECT' => 'Заполнить  Запланированная дата отгрузки',
                    'COMPLETED' => 'N']);
                Log::logFile('Date:', $dealActivity, 'eventUpdateDeal.log');

                APIActivity::updateActivity($dealActivity[0]['ID'], ['COMPLETED' => 'Y']);
            }

            if ($arFields['UF_CRM_1582775957638'] !== "") {
                $dealActivity = APIActivity::getActivityList(['OWNER_ID' => $arFields['ID'],
                    'SUBJECT' => 'Заполнить Согласованная дата оплаты',
                    'COMPLETED' => 'N']);
                APIActivity::updateActivity($dealActivity[0]['ID'], ['COMPLETED' => 'Y']);
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