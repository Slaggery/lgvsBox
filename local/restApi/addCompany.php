<?php

class Company
{
    const IDBLOCKUSER = 17;
    const IDBLOCKREGION = 18;

    public static function whatDoCompany($query)
    {
        $companies = json_decode($query['mData']);

        $result = [];
        foreach ($companies as $company) {
            Log::logFile('company: ', $company, 'addCompany.log');
            if ($company->DeleteMark === "True") {
                $result[] = self::deleteCompany($company->Guid1C);
            } else {
                $manager = current(APILists::getElement(['IBLOCK_ID' => self::IDBLOCKUSER, 'PROPERTY_GUID1C' => $company->Manager->Manager_Guid1C]));
                $idManager = $manager['PROPERTIES']['BXID']['VALUE'];
                if ($idManager == null) $idManager = 4;

                $region = current(APILists::getElement(['IBLOCK_ID' => self::IDBLOCKREGION, 'PROPERTY_GUID1C' => $company->Folder]));
                $idRegion = $region['ID'];

                if ($company->Folder === "") $idRegion = "";

                $headClient = [];
                if ($company->HeadClient !== null && $company->HeadClient !== "") {
                    $headClient = APICompany::getCompanyList(['UF_GUID1C' => $company->HeadClient], ['ID']);
                }

                $phones = APIContacts::collectPhone($company->Contact->Contact_Phone);
                $emails = APIContacts::collectEmail($company->Contact->Contact_Email);

                $idContact = [];
                if (count($company->ContactFace) !== 0) {
                    foreach ($company->ContactFace as $contactFace) {
                        if ($contactFace->ContactFace_Guid1C !== "") {
                            $contactList = APIContacts::getContactList(['UF_GUID1C' => $contactFace->ContactFace_Guid1C], ['ID']);

                            /*if (count($contactList) === 0) {
                                $contactList = APIContacts::getContactList(['NAME' => $contactFace->ContactFace_Name], ['ID']);
                            }*/
                            count($contactList) === 0 ?
                                $idContact[] = self::addContact($idManager, $contactFace)
                                : $idContact[] = self::updateContact($idManager, $contactList[0]['ID'], $contactFace);
                        }
                    }
                }

                $companyData = [
                    'TITLE' => $company->Name,
                    'UF_GUID1C' => $company->Guid1C,
                    'ASSIGNED_BY_ID' => $idManager,
                    'UF_HEADCLIENT' => $headClient[0]['ID'],
                    'COMMENTS' => $company->Comment,
                    'FM' => [
                        'PHONE' => $phones,
                        'EMAIL' => $emails
                    ],
                    'CONTACT_ID' => $idContact,
                    'UF_REGION' => $idRegion,
                    'UF_SYNCHRONIZE' => 1
                ];

                $requisiteData = [
                    'ENTITY_TYPE_ID' => CCrmOwnerType::Company,
                    'ENTITY_ID' => "",
                    'PRESET_ID' => $company->Type === "ЮЛ" ? 1 : 2, //1: юр.лицо, 2: ИП
                    'NAME' => "Реквизиты",
                    'XML_ID' => $company->Guid1C,
                    'ACTIVE' => "Y",
                    'RQ_COMPANY_NAME' => $company->Name,
                    'RQ_COMPANY_FULL_NAME' => $company->FullName,
                    'RQ_INN' => $company->INN,
                    'RQ_KPP' => $company->KPP,
                    'RQ_ADDR' => [
                        '1' => [
                            'ADDRESS_1' => $company->Contact->Contact_FactAdres
                        ],
                        '6' => [
                            'ADDRESS_1' => $company->Contact->Contact_LegalAdres
                        ]
                    ]
                ];

                $bankRequisiteData = [
                    'ENTITY_TYPE_ID' => CCrmOwnerType::Requisite,
                    "XML_ID" => $company->Bank->Bank_Guid1C,
                    'ENTITY_ID' => "",
                    'COUNTRY_ID' => 1,
                    'NAME' => "Банковские реквизиты",
                    'ACTIVE' => "Y",
                    'RQ_BANK_NAME' => $company->Bank->Bank_Name,
                    'RQ_BANK_ADDR' => $company->Bank->Bank_City,
                    'RQ_BIK' => $company->Bank->Bank_BIK,
                    'RQ_ACC_NUM' => $company->Bank->Bank_RaschSchet,
                    'RQ_COR_ACC_NUM' => $company->Bank->Bank_KorSchet
                ];

                if (isset($company->ID)) {
                    $companyList = APICompany::getCompanyList(['ID' => $company->ID]);
                } else {
                    $companyList = APICompany::getCompanyList(['UF_GUID1C' => $company->Guid1C]);
                }

                if (count($companyList) == 0) {
                    $result[] = self::addCompany($companyData, $requisiteData, $bankRequisiteData);
                } else {
                    $result[] = self::updateCompany($companyData, $requisiteData, $bankRequisiteData, $company->ID);
                }
            }
        }

        return $result;
    }

    private static function addContact($assignedById = null, $contact = [])
    {
        $phones = APIContacts::collectPhone($contact->ContactFace_Phone);
        $emails = APIContacts::collectEmail($contact->ContactFace_Email);

        $dataContact = [
            'NAME' => $contact->ContactFace_Name,
            'ASSIGNED_BY_ID' => $assignedById,
            'UF_GUID1C' => $contact->ContactFace_Guid1C,
            'FM' => [
                'PHONE' => $phones,
                'EMAIL' => $emails
            ]
        ];

        return APIContacts::addContact($dataContact);
    }

    private static function updateContact($assignedById = null, $idContact = null, $data = [])
    {
        $phones = APIContacts::collectPhone($data->ContactFace_Phone);
        $emails = APIContacts::collectEmail($data->ContactFace_Email);

        APIContacts::deleteContacts($idContact, 'CONTACT');

        $dataContact = [
            'NAME' => $data->ContactFace_Name,
            'ASSIGNED_BY_ID' => $assignedById,
            'UF_GUID1C' => $data->ContactFace_Guid1C,
            'FM' => [
                'PHONE' => $phones,
                'EMAIL' => $emails
            ]
        ];

        APIContacts::updateContact($idContact, $dataContact);
        return $idContact;
    }

    private static function addCompany($companyData = [], $requisiteData = [], $bankRequisiteData = [])
    {
        $idCompany = APICompany::addCompany($companyData);

        $requisiteData['ENTITY_ID'] = $idCompany;
        $idRequisite = APICompany::addRequisite($requisiteData);

        $bankRequisiteData['ENTITY_ID'] = $idRequisite;

        $idBankRequisite = APICompany::addBankRequisite($bankRequisiteData);

        if (is_numeric($idCompany)) {
            if ((is_numeric($idRequisite))) {
                if (is_numeric($idBankRequisite)) {
                    Log::logFile("Add company complete. BXID: ", $idCompany, 'addCompany.log');
                    return [
                        'STATUS:' => "ADD COMPANY COMPLETE",
                        'BXID:' => $idCompany
                    ];
                }

                $error = [
                    'STATUS:' => 'ADD BANK REQUISITE ERROR',
                    'ERROR DESCRIPTION:' => $idBankRequisite,
                    'idCompany' => $idCompany,
                    'idRequisite' => $idRequisite];

                Log::logFile("ERROR: ", $error, 'addCompany.log');
                return $error;
            }

            $error = [
                'STATUS:' => 'ADD REQUISITE ERROR',
                'ERROR DESCRIPTION:' => $idRequisite,
                'idCompany' => $idCompany,
            ];

            Log::logFile("ERROR: ", $error, 'addCompany.log');
            return $error;
        }

        Log::logFile("Add company error. ", $idCompany, 'addCompany.log');
        return [
            'STATUS:' => 'ADD COMPANY ERROR',
            'ERROR DESCRIPTION:' => $idCompany,
        ];
    }

    private static function updateCompany($companyData = [], $requisiteData = [], $bankRequisiteData = [], $bxid = null)
    {
        if ($bxid == null) {
            $idCompany = APICompany::getCompanyList(['UF_GUID1C' => $companyData['UF_GUID1C']]);
            $idRequisites = APICompany::getRequisiteList(['XML_ID' => $companyData['UF_GUID1C']], ['ID']);
        } else {
            $idCompany = APICompany::getCompanyList(['ID' => $bxid]);;
            $idRequisites = APICompany::getRequisiteList(['ENTITY_ID' => $bxid], ['ID']);
        }

        $delContact = APIContacts::deleteContacts($idCompany[0]['ID'], 'COMPANY');

        $delRequisite = true;
        if (count($idRequisites) !== 0) {
            $delRequisite = APICompany::deleteRequisite($idRequisites[0]['ID']);
        }

        if ($delRequisite) {
            $updateCompany = APICompany::updateCompany($idCompany[0]['ID'], $companyData);

            if ($updateCompany) {
                $requisiteData['ENTITY_ID'] = $idCompany[0]['ID'];
                $updateRequisite = APICompany::addRequisite($requisiteData);

                if (is_numeric($updateRequisite)) {
                    $bankRequisiteData['ENTITY_ID'] = $updateRequisite;
                    $updateBankRequisite = APICompany::addBankRequisite($bankRequisiteData);

                    if (is_numeric($updateBankRequisite)) {
                        Log::logFile("Update company complete. BXID: ", $idCompany[0]['ID'], 'updateCompany.log');
                        return [
                            'STATUS:' => 'UPDATE COMPANY COMPLETE',
                            'BXID:' => $idCompany[0]['ID']
                        ];
                    }

                    $error = [
                        'STATUS:' => 'ADD BANK REQUISITE ERROR',
                        'ERROR DESCRIPTION:' => $updateBankRequisite,
                        'idCompany' => $idCompany[0]['ID'],
                        'idRequisite' => $updateRequisite];

                    Log::logFile("ERROR: ", $error, 'updateCompany.log');
                    return $error;
                }

                $error = [
                    'STATUS:' => 'ADD REQUISITE ERROR',
                    'ERROR DESCRIPTION:' => $updateRequisite,
                    'idCompany' => $idCompany[0]['ID'],
                ];

                Log::logFile("ERROR: ", $error, 'updateCompany.log');
                return $error;
            }

            Log::logFile("Update company error. ", $updateCompany, 'updateCompany.log');
            return [
                'STATUS:' => "UPDATE COMPANY ERROR",
                'ERROR DESCRIPTION:' => $updateCompany
            ];
        }

        Log::logFile("Delete requisite error. ", $delRequisite, 'updateCompany.log');
        return [
            'STATUS:' => "DELETE REQUISITE ERROR",
            'ERROR DESCRIPTION' => $delRequisite
        ];
    }

    private static function deleteCompany($guidCompany = null)
    {
        $company = APICompany::getCompanyList(['UF_GUID1C' => $guidCompany], ['ID']);

        if (count($company) === 0) {
            Log::logFile("Company not found. Guid company: ", $guidCompany, 'deleteCompany.log');
            return [
                'STATUS:' => "ERROR",
                'ERROR DESCRIPTION' => "COMPANY NOT FOUND"
            ];
        }

        $result = APICompany::deleteCompany($company[0]['ID']);

        if ($result) {
            Log::logFile("Delete company complete. BXID: ", $company[0]['ID'], 'deleteCompany.log');
            return [
                'STATUS:' => "DELETE COMPANY COMPLETE",
                'BXID:' => $company[0]['ID']
            ];
        }
        Log::logFile("Company delete error. ", $result);
        return [
            'STATUS:' => "DELETE COMPANY ERROR",
            'ERROR DESCRIPTION:' => $result
        ];
    }
}