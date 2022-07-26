<?php

namespace App\Utils;


class XeroUtil
{
    /**
     * Sent Invoices
     *
     */
    public function sentInvoices($accessToken, $invoiceData)
    {
        // Configure OAuth2 access token for authorization: OAuth2
        $config = \XeroAPI\XeroPHP\Configuration::getDefaultConfiguration()->setAccessToken( $accessToken );       

        $apiInstance = new \XeroAPI\XeroPHP\Api\AccountingApi(
            new \GuzzleHttp\Client(),
            $config
        );
        $xeroTenantId = "e767fbb4-b70d-45ba-b6ff-fb4a185e210a";
        $summarizeErrors = true;
        $unitdp = 4;
        $contact = new \XeroAPI\XeroPHP\Models\Accounting\Contact;
        $contact->setContactID('0f2e8fa9-4894-4dcb-a68b-0a8695019933');
        
        $invoices = new \XeroAPI\XeroPHP\Models\Accounting\Invoices;
        $arr_invoices = [];

        foreach ($invoiceData as $key => $item) {
            $description = "Customer: {$item['name']}";
            $lineItem = new \XeroAPI\XeroPHP\Models\Accounting\LineItem;
            $lineItem->setDescription($description);
            $lineItem->setQuantity($item['total_items']);
            $lineItem->setUnitAmount($item['total_paid']);
            $lineItem->setAccountCode('260');
            $lineItems = [];
            array_push($lineItems, $lineItem);

            $dateValue = new \DateTime($item['transaction_date']);
            $dueDateValue = new \DateTime($item['sale_date']);
            
            $invoice = new \XeroAPI\XeroPHP\Models\Accounting\Invoice;
            $invoice->setType(\XeroAPI\XeroPHP\Models\Accounting\Invoice::TYPE_ACCREC);
            $invoice->setContact($contact);
            $invoice->setDate($dateValue);
            $invoice->setDueDate($dueDateValue);
            $invoice->setLineItems($lineItems);
            $invoice->setReference('Booking Service');
            $invoice->setStatus(\XeroAPI\XeroPHP\Models\Accounting\Invoice::STATUS_DRAFT);
            array_push($arr_invoices, $invoice);
        }

        $invoices->setInvoices($arr_invoices);

        try {
            $result = $apiInstance->createInvoices($xeroTenantId, $invoices, $summarizeErrors, $unitdp);
        } catch (\Exception $e) {
            echo 'Exception when calling AccountingApi->createInvoices: ', $e->getMessage(), PHP_EOL;
        }
    }
}
