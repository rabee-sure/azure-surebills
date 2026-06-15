<?php

namespace App\Http\Requests\Concerns;

use Illuminate\Validation\Validator;

trait ValidatesMerchantDropzoneDocuments
{
    /**
     * Strip empty document[] entries so "max:5" applies to real files only.
     */
    protected function prepareMerchantDropzoneDocuments(): void
    {
        $docs = $this->input('document');
        if (! is_array($docs)) {
            return;
        }

        $filtered = array_values(array_filter($docs, function ($d) {
            return $d !== null && $d !== '' && (string) $d !== 'undefined';
        }));

        $this->merge(['document' => $filtered]);
    }

    /**
     * Enforce at most five distinct files (basename) per submission.
     */
    protected function withMerchantDropzoneDocumentDistinctValidator(Validator $validator): void
    {
        $validator->after(function (Validator $v) {
            $docs = $this->input('document', []);
            if (! is_array($docs) || count($docs) === 0) {
                return;
            }

            $basenames = array_map(function ($p) {
                return strtolower(basename(str_replace('\\', '/', (string) $p)));
            }, $docs);

            if (count($basenames) !== count(array_unique($basenames))) {
                $v->errors()->add('document', __('Duplicate documents are not allowed.'));
            }
        });
    }
}
