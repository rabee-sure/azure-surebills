<?php

namespace App\Support\Storage;

/**
 * Merchant document object keys on the "public" disk (aligned with admin export paths).
 * Keys are relative to the disk root, no leading slash. Optional bucket prefix comes from OCI_BUCKET_PREFIX.
 */
final class ExportStoragePaths
{
    public const MERCHANT_BUSINESS_DOCUMENTS = 'shared/merchants/business_documents';

    public const MERCHANT_BANK_DOCUMENTS = 'shared/merchants/bank_documents';

    public const MERCHANT_BILLS_BACKGROUNDS = 'shared/merchants/bills_backgrounds';

    public const MERCHANT_BILLS_EXPORTS = 'shared/exports/merchants/bills';

    public const TRANSFER_BILLS_EXPORTS = 'shared/exports/transfers/bills';

    public static function merchantBusinessDocumentsRoot(): string
    {
        return \oci_bucket_object_prefix().self::MERCHANT_BUSINESS_DOCUMENTS;
    }

    public static function merchantBankDocumentsRoot(): string
    {
        return \oci_bucket_object_prefix().self::MERCHANT_BANK_DOCUMENTS;
    }

    /**
     * Directory prefix for one merchant user (numeric User id): …/business_documents/{userId}
     */
    public static function merchantBusinessDocumentUserPrefix(int $userId): string
    {
        return self::merchantBusinessDocumentsRoot().'/'.$userId;
    }

    /**
     * Directory prefix: …/bank_documents/{userId}
     */
    public static function merchantBankDocumentUserPrefix(int $userId): string
    {
        return self::merchantBankDocumentsRoot().'/'.$userId;
    }

    public static function merchantBillsBackgroundsRoot(): string
    {
        return \oci_bucket_object_prefix().self::MERCHANT_BILLS_BACKGROUNDS;
    }

    /**
     * Bill payment page background images: …/bills_backgrounds/{userId}
     */
    public static function merchantBillsBackgroundUserPrefix(int $userId): string
    {
        return self::merchantBillsBackgroundsRoot().'/'.$userId;
    }

    public static function merchantBillsExportsRoot(): string
    {
        return \oci_bucket_object_prefix().self::MERCHANT_BILLS_EXPORTS;
    }

    public static function transferBillsExportsRoot(): string
    {
        return \oci_bucket_object_prefix().self::TRANSFER_BILLS_EXPORTS;
    }
}
