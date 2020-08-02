<?php

class Deal
{
    public static function whatDoDeal($query)
    {
        $dealList = APIDeal::getDealList(['STAGE_ID' => 'FINAL_INVOICE'], ['ID', 'UF_CRM_1582775986809', 'UF_CRM_1582775957638']);

        foreach ($dealList as $deal) {
            $invoice = APIInvoice::getInvoiceList(['UF_DEAL_ID' => $deal['ID'], 'STATUS_ID' => 'P', ['ID']]);

            if (count($invoice) !== 0) {
                if ($deal['UF_CRM_1582775986809'] !== "" && $deal['UF_CRM_1582775957638'] !== "") {
                    APIDeal::updateDeal($deal['ID'], ['STAGE_ID' => 'WON']);
                }
            }
        }
    }
}