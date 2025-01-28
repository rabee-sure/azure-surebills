<?php

namespace App\Http\Controllers\Api;

use Allam\Zatca\OnBoarding;
use App\Http\Controllers\Controller;
use App\Http\Requests\sendInvoiveToZatcaApi;
use App\Http\Requests\ZatcaOnboardingApi;
use App\Models\ZatcaInvoice;
use App\Models\ZatcaLog;
use App\Models\ZatcaMerchant;
use Illuminate\Support\Facades\DB;
use Allam\Zatca\Invoice\Client;
use Allam\Zatca\Invoice\Supplier;
use Allam\Zatca\Invoice\Delivery;
use Allam\Zatca\Invoice\PaymentType;
use Allam\Zatca\Invoice\PIH;
use Allam\Zatca\Invoice\ReturnReason;
use Allam\Zatca\Invoice\BillingReference;
use Allam\Zatca\Invoice\AdditionalDocumentReference;
use Allam\Zatca\Invoice\LegalMonetaryTotal;
use Allam\Zatca\Invoice\TaxesTotal;
use Allam\Zatca\Invoice\TaxSubtotal;
use Allam\Zatca\Invoice\LineTaxCategory;
use Allam\Zatca\Invoice\InvoiceLine;
use Allam\Zatca\Invoice\AllowanceCharge;
use Allam\Zatca\Invoice\InvoiceGenerator;
use stdClass;

class ZatcaController extends Controller
{
    public function onboarding(ZatcaOnboardingApi $request){
        //  Store merchant data to db in zatca_mercahnts table
        $merchant = ZatcaMerchant::firstOrCreate(
            [
                'uuid' => $request->merchant_uuid, 
                'email' => $request->merchant_email
            ],
            [
            'uuid' => $request->merchant_uuid,
            'email' => $request->merchant_email,
            'business_name_en' => $request->business_name_en,
            'vat_registration_number' => $request->vat_registration_number,
            'tin' => $request->tin,
            'crn' => $request->crn,
            'invoices_type' => $request->invoices_type,
            'business_category' => $request->business_category,
            'building_no' => $request->building_no,
            'street_name' => $request->street_name,
            'district' => $request->district,
            'city' => $request->city,
            'postal_code' => $request->postal_code,
            'additional_number' => $request->additional_no,
            'other_buyer_id' => $request->other_buyer_id,
            'otp' => $request->OTP,
        ]);

        $invoiceType = '1100';
        if($merchant->invoices_type == 'B2C'){
            $invoiceType = '0100';
        }elseif($merchant->invoices_type == 'B2B'){
            $invoiceType = '1000';
        }
            

        // Complaince CSID
        $onboardingResponse = (new OnBoarding())
        ->setZatcaEnv(config('zatca.enviroment'))
        ->setZatcaLang('en')
        ->setEmailAddress($merchant->email)
        ->setCommonName($merchant->business_name_en)
        ->setCountryCode('SA')
        ->setOrganizationUnitName($merchant->tin)
        ->setOrganizationName($merchant->business_name_en.'-SA')
        ->setEgsSerialNumber('1-'.$merchant->business_name_en.'|2-version2|3-'.$merchant->uuid)
        ->setVatNumber($merchant->vat_registration_number)
        ->setInvoiceType($invoiceType)
        ->setRegisteredAddress($merchant->city)
        ->setAuthOtp($request->OTP)
        ->setBusinessCategory($merchant->business_category)
        ->getAuthorization();

        $processId = ZatcaLog::where('parentable_type', 'merchant vat registration number')->where('parentable_id', $merchant->vat_registration_number)->orderBy('created_at', 'DESC')->pluck('uuid')->first();

        if(!$onboardingResponse['success']){
            return response()->json([
                "success" => false,
                "proccess_id" => $processId,
                "message" => $onboardingResponse['message']
            ]);
        }

        $merchant->fill($onboardingResponse['data']);
        $merchant->save();
        
        return response()->json([
			"success" => true,
			"proccess_id" => $processId,
  			"message" => "ISSUED"
		]);
    }

    public function sendInvoiveToZatca(sendInvoiveToZatcaApi $request){
        // Store invoice data to db in zatca_invoices table
        $merchant = ZatcaMerchant::where('uuid', $request->merchant_uuid)->first();
        if(!$merchant){
            return response()->json([
                "success" => false,
                "message" => "Merchant not found"
            ]);
        }

        $billType = 'bill';
        $refrenceBillId = null;
        $tax_value = null;
        $discount = isset($request->bill['discount']) ? $request->bill['discount'] : 0;
        $mainBill = null;
        $status = $request->bill['status'];
        $paid_at = null;

        if($request->bill['bill_type'] == 'Credit Note' ||  $request->bill['bill_type'] == 'Debit Note'){
            $mainBill = ZatcaInvoice::where('uuid', $request->bill['BillingRefrence'])->first();
            if(!$mainBill){
                return response()->json([
                    "success" => false,
                    "message" => "Refrence bill not found"
                ]);
            }

            $refrenceBillId = $mainBill->id;
            $tax_value = $mainBill->tax_value;

            if($request->bill['bill_type'] == 'Credit Note'){
                $billType = 'credit_note';
                $status = 'refunded';
            }elseif($request->bill['bill_type'] == 'Debit Note'){
                if($mainBill->refrence_bill_id){
                    return response()->json([
                        "success" => false,
                        "message" => "Refrence bill is Debit Note you can't create Debit Note"
                    ]);
                }
                $billType = 'debit_note';
            }
        }

        if($request->bill['bill_type'] == 'Bill'){
            $tax_value = $request->bill['tax_value'];
        }

        if($request->bill['bill_type'] == 'Bill' || $request->bill['bill_type'] == 'Debit Note'){
            $paid_at = $request->bill['paid_at'];
        }

        $invoice = ZatcaInvoice::where('uuid', $request->bill['bill_uuid'])->first();
        if(!$invoice){
            $invoice = DB::transaction(function () use ($request, $merchant, $billType, $tax_value, $discount, $status, $paid_at, $refrenceBillId, $mainBill){   
                $vat = $request->bill['sub_total'] * $tax_value / 100;
                $total = $request->bill['sub_total'] + $vat - $discount;

                $invoice = ZatcaInvoice::create( [
                    'uuid' => $request->bill['bill_uuid'],
                    'number' => $request->bill['number'],
                    'status' => $status,
                    'bill_type' => $billType,
                    'refrence_bill_id' => $refrenceBillId,
                    'merchant_id' => $merchant->id,
                    'merchant_name' => $merchant->business_name_en,
                    'merchant_email' => $merchant->email,
                    'merchant_vat_registration_number' => $merchant->vat_registration_number,
                    'merchant_crn' => $merchant->crn,
                    'merchant_tin' => $merchant->tin,
                    'merchant_building_no' => $merchant->building_no,
                    'merchant_street_name' => $merchant->street_name,
                    'merchant_district' => $merchant->district,
                    'merchant_city' => $merchant->city,
                    'merchant_postal_code' => $merchant->postal_code,
                    'customer_name' => $mainBill ? $mainBill->customer_name : $request->customer['name'],
                    'customer_vat_registration_number' => $mainBill ? $mainBill->customer_vat_registration_number : $request->customer['vat_registration_number'],
                    'customer_building_no' => $mainBill ? $mainBill->customer_building_no : $request->customer['building_no'],
                    'customer_street_name' => $mainBill ? $mainBill->customer_street_name : $request->customer['street_name'],
                    'customer_district' => $mainBill ? $mainBill->customer_district : $request->customer['district'],
                    'customer_city' => $mainBill ? $mainBill->customer_city : $request->customer['city'],
                    'customer_postal_code' => $mainBill ? $mainBill->customer_postal_code : $request->customer['postal_code'],
                    'bill_amount' => $request->bill['sub_total'],
                    'tax_value' => $tax_value,
                    'vat' => $vat,
                    'discount' => $discount,
                    'total' => $total,
                    'invoice_date' => $request->bill['created_at'],
                    'paid_at' => $paid_at ? $paid_at : null,
                ]);
        
                if(isset($request->bill['items']) && count($request->bill['items']) > 0){
                    $billItems = [];
                    foreach($request->bill['items'] as $item){
                        $billItems[] = [
                            'product_name' => $item['name'],
                            'product_price' => $item['price'],
                            'quantity' => $item['quantity'],
                            'total' => $item['price'] * $item['quantity'],
                        ];
                    }
                    $invoice->items()->createMany($billItems);
                }
        

                return $invoice;
            });
        }else{
            if($invoice->zatca_qr_code != null){
                return response()->json([
                    "success" => false,
                    "message" => "This bill already sent to Zatca",
                    "qr_image" => $invoice->zatca_qr_code
                ]);
            }
        }

        // Send invoice data to Zatca
        $refrencedBill = $invoice->refranceBill;
        if($invoice->bill_type == 'credit_note'){
            $invoiceTypeCode = '381';


            $customerData['customer_vat_registration_number'] = $refrencedBill->customer_vat_registration_number;
            $customerData['customer_street_name'] = $refrencedBill->customer_street_name;
            $customerData['customer_buliding_no'] = $refrencedBill->customer_building_no;
            $customerData['customer_district'] = $refrencedBill->customer_district;
            $customerData['customer_city'] = $refrencedBill->customer_city;
            $customerData['customer_postal_code'] = $refrencedBill->customer_postal_code;
            $customerData['customer_name'] = $refrencedBill->customer_name;

            $merchantData['business_crn'] = $refrencedBill->merchant_crn;
            $merchantData['business_buliding_no'] = $refrencedBill->merchant_building_no;
            $merchantData['business_street_name'] = $refrencedBill->merchant_street_name;
            $merchantData['business_district'] = $refrencedBill->merchant_district;
            $merchantData['business_city'] = $refrencedBill->merchant_city;
            $merchantData['business_postal_code'] = $refrencedBill->merchant_postal_code;
            $merchantData['business_vat_registration_number'] = $refrencedBill->merchant_vat_registration_number;
            $merchantData['business_name'] = $refrencedBill->merchant_name;

            $BillingRefrence = $refrencedBill->number;

            $sub_total = $invoice->bill_amount;
            $tax_value = $refrencedBill->tax_value;
            $vat = $invoice->vat;
            $TaxExclusiveAmount = $sub_total;
            $TaxInclusiveAmount = $sub_total + $vat;

            $discount = $refrencedBill->discount;

            $item = new stdClass;
            $items = [];

            $item->quantity = 1;
            $item->product_price = $sub_total;
            $item->product_name = 'Returned Item';

            $items[] = $item;
        }else{
            $invoiceTypeCode = '388';

            $BillingRefrence = null;

            $customerData['customer_vat_registration_number'] = $invoice->customer_vat_registration_number;
            $customerData['customer_buliding_no'] = $invoice->customer_building_no;
            $customerData['customer_street_name'] = $invoice->customer_street_name;
            $customerData['customer_district'] = $invoice->customer_district;
            $customerData['customer_city'] = $invoice->customer_city;
            $customerData['customer_postal_code'] = $invoice->customer_postal_code;
            $customerData['customer_name'] = $invoice->customer_name;

            $merchantData['business_crn'] = $invoice->merchant_crn;
            $merchantData['business_buliding_no'] = $invoice->merchant_building_no;
            $merchantData['business_street_name'] = $invoice->merchant_street_name;
            $merchantData['business_district'] = $invoice->merchant_district;
            $merchantData['business_city'] = $invoice->merchant_city;
            $merchantData['business_postal_code'] = $invoice->merchant_postal_code;
            $merchantData['business_vat_registration_number'] = $invoice->merchant_vat_registration_number;
            $merchantData['business_name'] = $invoice->merchant_name;

            $sub_total = $invoice->bill_amount;
            $tax_value = $invoice->tax_value;
            $vat = $invoice->vat;
            $TaxExclusiveAmount = $sub_total - $invoice->discount;
            $TaxInclusiveAmount = $TaxExclusiveAmount + $vat;

            $discount = $invoice->discount;

            $items = $invoice->items;

            if($invoice->refrence_bill_id){
                $invoiceTypeCode = '383';

                $BillingRefrence = $invoice->refranceBill->number;
            }
        }

        
        if($customerData['customer_vat_registration_number'] == null){
            $invoiceTypecodeName = '0200000';
        }else{
            $invoiceTypecodeName = '0100000';
        }

        $invoiceMerchant = $invoice->merchant;

        $client = (new Client())
        ->setVatNumber($customerData['customer_vat_registration_number'])
        ->setStreetName($customerData['customer_street_name'])
        ->setBuildingNumber($customerData['customer_buliding_no'])
        ->setPlotIdentification($customerData['customer_buliding_no'])
        ->setSubDivisionName($customerData['customer_district'])
        ->setCityName($customerData['customer_city'])
        ->setPostalNumber($customerData['customer_postal_code'])
        ->setCountryName('SA')
        ->setClientName($customerData['customer_name']);

        $supplier = (new Supplier())
        ->setCrn($merchantData['business_crn'])
        ->setStreetName($merchantData['business_street_name'])
        ->setBuildingNumber($merchantData['business_buliding_no'])
        ->setPlotIdentification($merchantData['business_buliding_no'])
        ->setSubDivisionName($merchantData['business_district'])
        ->setCityName($merchantData['business_city'])
        ->setPostalNumber($merchantData['business_postal_code'])
        ->setCountryName('SA')
        ->setVatNumber($merchantData['business_vat_registration_number'])
        ->setVatName($merchantData['business_name']);

        $delivery = (new Delivery())
        ->setDeliveryDateTime($this->getDeliveryDateTime($invoice));

        $paymentType = (new PaymentType())
        ->setPaymentType($this->getPaymentMeanCode($invoice, $refrencedBill));

        $returnReason = (new ReturnReason())
        ->setReturnReason('SET_RETURN_REASON');

        $previous_hash = (new PIH())
        ->setPIH($this->getPIH($invoiceMerchant));  // note this value it from step 3 , 4

        if($BillingRefrence){
            $billingReference = (new BillingReference())
            ->setBillingReference($BillingRefrence); // note this used when type credit or debit this value of parent invoice id
        }

        $additionalDocumentReference = (new AdditionalDocumentReference())
        ->setInvoiceID($invoice->number); // note this value it from step 1

        $legalMonetaryTotal = (new LegalMonetaryTotal())
        ->setTotalCurrency('SAR')
        ->setLineExtensionAmount($sub_total)
        ->setTaxExclusiveAmount($TaxExclusiveAmount)
        ->setTaxInclusiveAmount($TaxInclusiveAmount)
        ->setAllowanceTotalAmount($discount)
        ->setPrepaidAmount(0)
        ->setPayableAmount($TaxInclusiveAmount);

        $taxesTotal = (new TaxesTotal())
        ->setTaxCurrencyCode('SAR')
        ->setTaxTotal($vat);

        $taxSubtotal = (new TaxSubtotal())
        ->setTaxCurrencyCode('SAR')
        ->setTaxableAmount($TaxExclusiveAmount)
        ->setTaxAmount($vat)
        ->setTaxCategory('S')
        ->setTaxPercentage($tax_value)
        ->getElement();

        $itemTaxCategory = (new LineTaxCategory())
        ->setTaxCategory('S')
        ->setTaxPercentage($tax_value)
        ->getElement();

        foreach($items as $item){
            $LineExtensionAmount = $item->quantity * $item->product_price;
            $LineTaxAmount = $LineExtensionAmount * ($tax_value / 100);
            $RoundingAmount = $LineExtensionAmount + $LineTaxAmount;

            $invoiceLine = (new InvoiceLine())
            ->setLineID('1')
            ->setLineName($item->product_name)
            ->setLineCurrency('SAR')
            ->setLinePrice($item->product_price)
            ->setLineQuantity($item->quantity)
            ->setLineSubTotal($LineExtensionAmount)
            ->setLineTaxTotal($LineTaxAmount)
            ->setLineNetTotal($RoundingAmount)
            ->setLineTaxCategories($itemTaxCategory)
            ->setLineDiscountReason('reason')
            ->setLineDiscountAmount(0)
            ->getElement();
        }

        $allowanceCharge = (new AllowanceCharge())
        ->setAllowanceChargeCurrency('SAR')
        ->setAllowanceChargeIndex('1')
        ->setAllowanceChargeAmount($discount)
        ->setAllowanceChargeTaxCategory('S')
        ->setAllowanceChargeTaxPercentage($tax_value)
        ->getElement();

        $response = (new InvoiceGenerator())
        ->setZatcaEnv(config('zatca.enviroment'))
        ->setZatcaLang('en')
        ->setInvoiceNumber($invoice->number)
        ->setInvoiceUuid($invoice->uuid) // this value from step 6
        ->setInvoiceIssueDate(date('Y-m-d', strtotime($invoice->invoice_date)))
        ->setInvoiceIssueTime(date('H:i:s', strtotime($invoice->invoice_date)))
        ->setInvoiceType($invoiceTypecodeName,$invoiceTypeCode)
        ->setInvoiceCurrencyCode('SAR')
        ->setInvoiceTaxCurrencyCode('SAR')
        ->setInvoiceAdditionalDocumentReference($additionalDocumentReference)
        ->setInvoicePIH($previous_hash)
        ->setInvoiceSupplier($supplier)
        ->setInvoiceClient($client)
        ->setInvoiceDelivery($delivery)
        ->setInvoicePaymentType($paymentType)
        ->setInvoiceLegalMonetaryTotal($legalMonetaryTotal)
        ->setInvoiceTaxesTotal($taxesTotal)
        ->setInvoiceTaxSubTotal($taxSubtotal)
        ->setInvoiceAllowanceCharges($allowanceCharge)
        ->setInvoiceLines($invoiceLine)
        ->setCertificateEncoded($invoiceMerchant->productionCertificate)
        ->setPrivateKeyEncoded($invoiceMerchant->privateKey)
        ->setCertificateSecret($invoiceMerchant->productionCertificateSecret);

        if(in_array($invoiceTypeCode, ['381', '383'])){
            $response = $response->setInvoiceBillingReference($billingReference)  //use this when document type is credit or debit
            ->setInvoiceReturnReason($returnReason); //use this when document type is credit or debit
        }
        
        $response = $response->sendDocument(true); // when you use production certifiacte for (simulation , core) dont forget set sendDocument(true)

        $invoice->zatca_qr_code = $response['qrImage'];
        $invoice->save();
        
        $invoiceMerchant->zatca_pih = $response['hash'];
        $invoiceMerchant->save();

        // $processId = ZatcaLog::where('parentable_type', 'merchant vat registration number')->where('parentable_id', $merchant->vat_registration_number)->orderBy('created_at', 'DESC')->pluck('uuid')->first();

        if(!$response['success']){
            return response()->json([
                "success" => false,
                "proccess_id" => "processId",
                "message" => $response['message']
            ]);
        }

        $invoiceStatus = null;
        if(isset($response['response']->clearanceStatus)){
            $invoiceStatus = $response['response']->clearanceStatus;
        }elseif($invoiceStatus = $response['response']->reportingStatus){
            $invoiceStatus = $response['response']->reportingStatus;
        }
        
        return response()->json([
			"success" => true,
			"proccess_id" => "processId",
  			"response" => $response['response']->validationResults,
			"invoice_status" => $invoiceStatus,
			"qr_image" => $response['qrImage'],
		]);
    }

    private function getPaymentMeanCode($invoice, $refrencedBill){
        $PaymentMeansCode = "1";

        if($invoice->bill_type == 'credit_note'){
            $status = $refrencedBill->status;
        }else{
            $status = $invoice->status;
        }

        switch ($status) {
            case 'pending':
                $PaymentMeansCode = "1";
                break;
            case 'paid':
                $PaymentMeansCode = "48";
                break;
            case 'paid_cash':
                $PaymentMeansCode = "10";
                break;
            case 'paid_bank_transfer':
                $PaymentMeansCode = "42";
                break;
            case 'paid_machine':
                $PaymentMeansCode = "10";
                break;

            default:
                # code...
                break;
        }

        return $PaymentMeansCode;
    }

    private function getDeliveryDateTime($invoice){
        if(in_array($invoice->status, ['paid', 'paid_cash', 'paid_bank_transfer', 'paid_machine'])){
            $ActualDeliveryDate = date('Y-m-d', strtotime($invoice->paid_at));
        }else{
            $ActualDeliveryDate = date('Y-m-d', strtotime($invoice->created_at));
        }

        return $ActualDeliveryDate;
    }

    private function getPIH($invoiceMerchant){
        $pih = $invoiceMerchant->zatca_pih ?? 'X+zrZv/IbzjZUnhsbWlsecLbwjndTpG0ZynXOif7V+k=';

        return $pih;
    }
}
