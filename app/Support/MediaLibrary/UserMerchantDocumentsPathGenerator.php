<?php

namespace App\Support\MediaLibrary;

use App\Models\User;
use App\Support\Storage\ExportStoragePaths;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\MediaLibrary\Support\PathGenerator\DefaultPathGenerator;
use Spatie\MediaLibrary\Support\PathGenerator\PathGenerator;

/**
 * User KYC files on the same object keys as admin:
 *   {prefix}shared/merchants/business_documents/{userId}/
 *   {prefix}shared/merchants/bank_documents/{userId}/
 * where userId is the Spatie parent model id (merchant User id).
 */
class UserMerchantDocumentsPathGenerator implements PathGenerator
{
    /** @var DefaultPathGenerator */
    protected $default;

    public function __construct()
    {
        $this->default = new DefaultPathGenerator;
    }

    public function getPath(Media $media): string
    {
        $base = $this->merchantCollectionBasePath($media);

        return $base === null ? $this->default->getPath($media) : $base.'/';
    }

    public function getPathForConversions(Media $media): string
    {
        $base = $this->merchantCollectionBasePath($media);

        return $base === null ? $this->default->getPathForConversions($media) : $base.'/conversions/';
    }

    public function getPathForResponsiveImages(Media $media): string
    {
        $base = $this->merchantCollectionBasePath($media);

        return $base === null ? $this->default->getPathForResponsiveImages($media) : $base.'/responsive-images/';
    }

    /**
     * @return string|null
     */
    protected function merchantCollectionBasePath(Media $media): ?string
    {
        if (! is_a($media->model_type, User::class, true)) {
            return null;
        }

        $userId = (int) $media->model_id;

        if ($media->collection_name === 'business_documents') {
            return ExportStoragePaths::merchantBusinessDocumentUserPrefix($userId);
        }

        if ($media->collection_name === 'bank_documents') {
            return ExportStoragePaths::merchantBankDocumentUserPrefix($userId);
        }

        return null;
    }
}
