<?php
class Invoice
{
    public static function whatDoInvoice($query) {
        $guidInvoice = $query['invoice_guid'];

        Log::logFile('invoice_guid: ', $guidInvoice, 'completionInvoice.log');

        $invoice = APIInvoice::getInvoiceList(['UF_GUID1C' => $guidInvoice], ['ID', 'UF_DEAL_ID']);
        $idInvoice = $invoice[0]['ID'];

        APIInvoice::updateInvoice($idInvoice, ['STATUS_ID' => 'P']);

        $result = [
            'STATUS INVOICE:' => 'UPDATE INVOICE COMPLETED',
            'BXIDINVOICE:' => $idInvoice
        ];

        $deal = APIDeal::getDealList(['ID' => $invoice[0]['UF_DEAL_ID']], ['UF_CRM_1582775986809', 'UF_CRM_1582775957638', 'ID']);
        $deal = $deal[0];

        if ($deal['UF_CRM_1582775986809'] !== "" && $deal['UF_CRM_1582775957638'] !== "") {
            APIDeal::updateDeal($deal['ID'], ['STAGE_ID' => 'WON']);

            $result['STATUS DEAL'] = 'UPDATE DEAL COMPLETED';
            $result['BXIDDEAL:'] = $deal['ID'];
        }

        return $result;
    }
}