<?php

class Company
{
    const IDBLOCKREGION = 18;
    const IDBLOCKUSER = 17;

    public static function getCompany()
    {
        $companyList = APICompany::getCompanyList(['UF_SYNCHRONIZE' => 0]);

        if (count($companyList) === 0) return [];

        $result = [];
        foreach ($companyList as $company) {
            Log::logFile('', $company['ID'], 'companyId.log');
            $requisites = APICompany::getRequisiteList(['ENTITY_ID' => $company['ID']]);

            if ($company['UF_REGION'] === "" || $company['UF_REGION'] === null) continue;

            if ($requisites[0]['RQ_INN'] === null
                || $requisites[0]['RQ_INN'] === "") continue;

            $bankRequisites = APICompany::getBankRequisiteList(['ENTITY_ID' => $requisites[0]['ID']]);

            $region = current(APILists::getElement(['IBLOCK_ID' => self::IDBLOCKREGION, 'ID' => $company['UF_REGION']]));
            $guidRegion = $region['PROPERTIES']['GUID1C']['VALUE'];

            $dataCompany = APICompany::getCompanyById($company['ID']);
            $assignedById = $dataCompany['ASSIGNED_BY_ID'];

            $manager = current(APILists::getElement(['IBLOCK_ID' => self::IDBLOCKUSER, 'PROPERTY_BXID' => $assignedById]));
            $idManager = $manager['PROPERTIES']['GUID1C']['VALUE'];

            $contactFaceList = APIContacts::getContactList(['COMPANY_ID' => $company['ID']]);

            $arContactFace = [];
            if (count($contactFaceList) == !0) {
                foreach ($contactFaceList as $r => $contactFace) {
                    $arContactFace[$r]['ContactFace_Name'] = $contactFace['FULL_NAME'];
                    $arContactFace[$r]['ContactFace_Guid1C'] = $contactFace['UF_GUID1C'];

                    $dataContactFace = APIContacts::getContacts(['ELEMENT_ID' => $contactFace['ID']]);
                    $contactFacePhone = [];
                    $contactFaceEmail = [];

                    foreach ($dataContactFace as $faceData) {
                        if ($faceData['ENTITY_ID'] === "CONTACT") {
                            if ($faceData['TYPE_ID'] === "PHONE") {
                                $contactFacePhone[] = $faceData['~VALUE'];
                            }

                            if ($faceData['TYPE_ID'] === "EMAIL") {
                                $contactFaceEmail[] = $faceData['~VALUE'];
                            }
                        }
                    }

                    if (count($contactFacePhone) == 0) $contactFacePhone = '-';
                    if (count($contactFaceEmail) == 0) $contactFaceEmail = '-';

                    $arContactFace[$r]['ContactFace_Phone'] = $contactFacePhone;
                    $arContactFace[$r]['ContactFace_Email'] = $contactFaceEmail;
                }
            }

            $dataContact = APIContacts::getContacts(['ELEMENT_ID' => $company['ID']]);

            $contactPhone = [];
            $contactEmail = [];
            foreach ($dataContact as $contact) {
                if ($contact['ENTITY_ID'] == 'COMPANY') {
                    if ($contact['TYPE_ID'] === "PHONE") {
                        $contactPhone[] = $contact['~VALUE'];
                    }

                    if ($contact['TYPE_ID'] === "EMAIL") {
                        $contactEmail[] = $contact['~VALUE'];
                    }
                }
            }

            if (count($contactPhone) == 0) $contactPhone = '-';
            if (count($contactEmail) == 0) $contactEmail = '-';

            $address = APICompany::getAddress($requisites[0]['ID']);

            $company['COMMENTS'] === "" ? $comments = "-" : $comments = $company['COMMENTS'];
            $result[] = [
                'BXID' => $company['ID'],
                'Guid1C' => $company['UF_GUID1C'],
                'Folder' => $guidRegion,
                'Name' => $company['TITLE'],
                'FullName' => $requisites[0]['RQ_COMPANY_FULL_NAME'],
                'Type' => $requisites[0]['PRESET_ID'] === "1" ? 'ЮЛ' : 'ФЛ',
                'INN' => $requisites[0]['RQ_INN'],
                'Manager_Guid1C' => $idManager,
                'Comment' => $comments,
                'ContactFace' => $arContactFace,
                'Contact' => [
                    'Contact_Phone' => $contactPhone,
                    'Contact_Email' => $contactEmail,
                    'Contact_FactAddress' => $address[1]['ADDRESS_1'],
                    'Contact_LegalAddress' => $address[6]['ADDRESS_1']
                ],
                'Bank' => [
                    'Bank_Guid1C' => $bankRequisites[0]['XML_ID'],
                    'Bank_Name' => $bankRequisites[0]['RQ_BANK_NAME'],
                    'Bank_RaschSchet' => $bankRequisites[0]['RQ_ACC_NUM'],
                    'Bank_BIK' => $bankRequisites[0]['RQ_BIK'],
                    'Bank_KorSchet' => $bankRequisites[0]['RQ_COR_ACC_NUM'],
                    'Bank_City' => $bankRequisites[0]['RQ_BANK_ADDR'],
                ]
            ];
        }

        return $result;
    }
}