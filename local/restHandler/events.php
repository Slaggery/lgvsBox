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
        if (isset($arFields['UF_CRM_1595324006085'])) {
            if ($arFields['UF_CRM_1595324006085'] !== "") {
                $dealActivity = APIActivity::getActivityList(['OWNER_ID' => $arFields['ID'], 'SUBJECT' => 'Определить категорию товара']);
                $dealActivityUpdate = APIActivity::updateActivity($dealActivity[0]['ID'], ['COMPLETED' => 'Y']);
            }
        }
    }

    public static function disabler_ui()
    {
        $arJsConfig = array(
            'stages_disabler_ui' => array(
                'js' => '/local/js/stages_disabler.ui.js',
                'css' => '/local/css/hide_tasks_actions.css'
            ),
        );
        foreach ($arJsConfig as $ext => $arExt) {
            \CJSCore::RegisterExt($ext, $arExt);
        }
        CUtil::InitJSCore(array('stages_disabler_ui'));
    }
}