<?php

class ContactsCloud
{
    public static function whatDoContacts($query)
    {
        include_once($_SERVER["DOCUMENT_ROOT"] . '/local/lib/contactsCloud.php');

        $result = [];
        foreach ($companies as $company) {

            $idCompany = APICompany::getCompanyList(['UF_GUID1C' => $company['Guid1C']], ['ID']);
            $idCompany = $idCompany[0];

            if ($idCompany != null) {
                APIContacts::deleteContacts($idCompany['ID'], 'COMPANY');

                $contactList = APIContacts::getContactList(['UF_GUID1C' => $company['ContactFace']['ContactFace_Guid1C']], ['ID']);
                $idContact = $contactList[0];

                if ($idContact['ID'] != null) {
                    APIContacts::deleteContacts($idContact['ID'], 'CONTACT');

                    $phones = APIContacts::collectPhone($company['ContactFace']['ContactFace_Phone']);
                    $emails = APIContacts::collectEmail($company['ContactFace']['ContactFace_Email']);

                    $dataContact = [
                        'FM' => [
                            'PHONE' => $phones,
                            'EMAIL' => $emails
                        ]
                    ];

                    APIContacts::updateContact($idContact['ID'], $dataContact);
                }

                $phoneCompany = APIContacts::collectPhone($company['Contact']['Contact_Phone']);
                $emailCompany = APIContacts::collectPhone($company['Contact']['Contact_Email']);

                $dataCompany = [
                    'FM' => [
                        'PHONE' => $phoneCompany,
                        'EMAIL' => $emailCompany
                    ],
                    'UF_SYNCHRONIZE' => '0'
                ];

                APICompany::updateCompany($idCompany['ID'], $dataCompany);

                $result[] = ['idCompany' => $idCompany['ID'],
                 //   'dataCompany' => $dataCompany,
                 //   'idContact' => $idContact['ID'],
                  //  'dataContact' => $dataContact
                ];
            }
        }

        return $result;
    }
}