<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Config;

class SupplierPurchaseResource extends JsonResource
{
    public function toArray($request)
    {
        $supplier = $this->resource['supplier'] ?? null;
        $supplierPurchase = $this->resource['supplierPurchase'] ?? null;

        return [
            'id' => $supplier['id'] ?? null,
            'name' => $supplier['name'] ?? null,
            'phone_number' => $supplier['phone_number'] ?? null,
            'email' => $supplier['email'] ?? null,
            'company_name' => $supplier['company_name'] ?? null,
            'company_number' => $supplier['company_number'] ?? null,
            'location' => $supplier['location'] ?? null,
            'supplierPurchase' => $this->formatSupplierPurchases($supplierPurchase),
        ];
    }

    protected function formatSupplierPurchases($supplierPurchases)
    {
        if (!$supplierPurchases) {
            return null;
        }

        foreach ($supplierPurchases as &$purchase) {
            $documentFilename = $purchase['document'] ?? null;

            $documentLink = $documentFilename
                ? Config::get('app.url') . '/' . Config::get('imagepath.purchase') . '/' . $documentFilename
                : null;

            $purchase['document'] = $documentLink;
        }

        return $supplierPurchases;
    }
}
