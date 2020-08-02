<?php

class Invoices
{
    public static function whatDoInvoices($query)
    {
        $invoices = json_decode($query['mData']);

        $result = [];
        foreach ($invoices as $invoice) {
            Log::logFile('invoice: ', $invoice, 'addInvoice.log');
            if ($invoice->DeleteMark === "True") {
                $result[] = self::deleteInvoice($invoice);
                continue;
            }

            $products_rows = [];
            foreach ($invoice->products as $product) {
                $products_rows[] = array(
                    'PRODUCT_NAME' => $product->PRODUCT_NAME,
                    'QUANTITY' => $product->QUANTITY,
                    'PRICE' => ((($product->SUM - $product->NDS_sum + $product->DISCOUNT) * 1.20) / $product->QUANTITY) - ($product->DISCOUNT / $product->QUANTITY * 0.20),
                    'DISCOUNT_TYPE_ID' => 1,
                    'DISCOUNT_PRICE' => $product->DISCOUNT / $product->QUANTITY,
                    'VAT_RATE' => '0.' . $product->NDS_rate,
                    'VAT_INCLUDED' => "Y"
                );
            }

            $dealProduct = [];
            foreach($invoice->summedGroups as $summedGroup) {
                $dealProduct[] = [
                    'PRODUCT_NAME' => $summedGroup->name,
                    'QUANTITY' => 1,
                    'PRICE' => $summedGroup->summ,
                ];
            }

            $idCompany = APICompany::getCompanyList(['UF_GUID1C' => $invoice->Client], ['ID']);
            $idCompany = $idCompany[0]['ID'];

            $deal = APIDeal::getDealById($invoice->DEAL_ID);

            $innCompany = APICompany::getRequisiteList(['ENTITY_ID' => $idCompany], ['RQ_INN']);
            $innCompany = $innCompany[0]['RQ_INN'];


            $arRequisite = APIInvoice::getRequisites($innCompany);

            $invoiceProperties = [
                9 => $arRequisite['INN'],
                10 => $arRequisite['KPP'],
                11 => $arRequisite['NAME']
            ];

            $invoiceData = [
                'UF_COMPANY_ID' => $idCompany,
                'UF_MYCOMPANY_ID' => 0,
                'ACCOUNT_NUMBER' => $invoice->Number,
                'UF_DEAL_ID' => $invoice->DEAL_ID,
                'PERSON_TYPE_ID' => "1",
                'PRODUCT_ROWS' => $products_rows,
                'ORDER_TOPIC' => "Предложение",
                'STATUS_ID' => "N",
                'PAY_SYSTEM_ID' => "1",
                'RESPONSIBLE_ID' => $deal['ASSIGNED_BY_ID'],
                'INVOICE_PROPERTIES' => $invoiceProperties,
                'UF_GUID1C' => $invoice->invoiceGuid
            ];

            $idInvoice = APIInvoice::getInvoiceList(['UF_GUID1C' => $invoice->invoiceGuid], ['ID']);

            if (count($idInvoice) === 0) {
                $result[] = APIInvoice::addInvoice($invoiceData);
                $dealActivity = APIActivity::getActivityList(['OWNER_ID' => $invoice->DEAL_ID,
                    'SUBJECT' => 'Выставить счет в 1С',
                    'COMPLETED' => 'N']);

                APIActivity::updateActivity($dealActivity[0]['ID'], ['COMPLETED' => 'Y']);
                CCrmProductRow::SaveRows("D", $invoice->DEAL_ID, $dealProduct);
                APIDeal::updateDeal($invoice->DEAL_ID, ['STAGE_ID' => 'FINAL_INVOICE']);

                $arErrorsTmp = array();

                CBPDocument::StartWorkflow(
                    27,
                    array('crm', 'CCrmDocumentCompany', 'DEAL_' . $invoice->DEAL_ID),
                    array_merge(),
                    $arErrorsTmp
                );

                continue;
            }

            $result[] = APIInvoice::updateInvoice($idInvoice[0]['ID'], $invoiceData);
            CCrmProductRow::SaveRows('D', $invoice->DEAL_ID, $dealProduct);
        }

        return $result;
    }

    final static function deleteInvoice($invoice)
    {
        Log::logFile('deleteInvoice: ', $invoice, 'deleteInvoice.logдали доступ, мы работаем');
        return $invoice;
    }
}
