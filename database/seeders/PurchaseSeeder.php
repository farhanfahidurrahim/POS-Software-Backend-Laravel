<?php

namespace Database\Seeders;

use Carbon\Carbon;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Supplier;
use App\Models\Variation;
use App\Models\Transaction;
use Illuminate\Database\Seeder;
use App\Models\SupplierTransaction;

class PurchaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $supplierCount = Supplier::count();
        $productCount = Product::count();

        $purchaseStatuses = ['receive', 'processing'];
        $paymentMethods = ['cash', 'bkash', 'bank', 'cheque'];

        $variationsCount = Variation::count();

        for ($i = 0; $i < 10; $i++) { // 10 dummy purchases
            $supplier = Supplier::inRandomOrder()->first();
            $product = Product::inRandomOrder()->first();

            $randomVariationIds = [];
            $quantities = [];
            $unitPrices = [];

            $variationCount = rand(1, 3);

            for ($j = 0; $j < $variationCount; $j++) {
                $randomVariationId = rand(1, $variationsCount);
                $randomVariationIds[] = (string) $randomVariationId;

                $quantities[] = (string) rand(1, 5);
                $unitPrices[] = (string) rand(50, 200);
            }

            //$purchaseDate = Carbon::now()->subDays(rand(1, 365))->toDateString();
            $purchaseDate = Carbon::now()->subDays(rand(1, 365))->format('d M Y');
            $purchase = Purchase::create([
                'purchase_date' => $purchaseDate,
                'invoice' => 'INV-PURCHASE-' . str_pad($i + 1, 6, '0', STR_PAD_LEFT),
                'supplier_id' => $supplier->id,
                'product_id' => $product->id,
                // 'reference_no' => 'REF' . rand(1000, 9999),
                'variation_id' => json_encode($randomVariationIds),
                'quantity' => json_encode($quantities),
                'unit_price' => json_encode($unitPrices),
                'purchase_status' => $purchaseStatuses[array_rand($purchaseStatuses)],
                'payment_method' => $paymentMethods[array_rand($paymentMethods)],
                'shipping_amount' => rand(10, 50),
                'note' => 'Sample note for purchase ' . ($i + 1),
                'created_by' => 1,
            ]);

            $quantity = json_decode($purchase->quantity);
            $unitPrices = json_decode($purchase->unit_price);
            $subTotals = [];

            foreach ($quantity as $key => $qty) {
                $subTotal = (string) ($qty * $unitPrices[$key]);
                $subTotals[] = $subTotal;
            }

            $purchase->sub_total = json_encode($subTotals);
            $subTotalAmount = $purchase->sub_total_amount = array_sum($subTotals);

            $totalAmount = $subTotalAmount + $purchase->shipping_amount;
            $purchase->total_amount = $totalAmount;
            $paidAmount = rand(50, $totalAmount);
            $purchase->paid_amount = $paidAmount;
            $dueAmount = $totalAmount - $paidAmount;
            $purchase->due_amount = $dueAmount;

            if ($totalAmount == $paidAmount) {
                $purchase->payment_status = 'paid';
            } elseif ($paidAmount > 0 && $paidAmount < $totalAmount) {
                $purchase->payment_status = 'partial';
            } elseif ($paidAmount == 0) {
                $purchase->payment_status = 'unpaid';
            }

            $purchase->save();

            // Adjusting stock if purchase is received
            if ($purchase->purchase_status === 'receive') {
                $variation = Variation::find($randomVariationId);
                if ($variation) {
                    $variation->default_sell_price = rand(50, 200);
                    $variation->stock_amount += rand(1, 5);
                    $variation->save();
                }
            }

            if ($purchase) {
                $supplierId = $purchase->supplier_id;

                $sumTotalAmount = Purchase::where('supplier_id', $supplierId)->sum('total_amount');
                $sumPaidAmount = Purchase::where('supplier_id', $supplierId)->sum('paid_amount');
                $sumDueAmount = Purchase::where('supplier_id', $supplierId)->sum('due_amount');

                $existingTransaction = SupplierTransaction::where('supplier_id', $supplierId)->first();

                if ($existingTransaction) {
                    $existingTransaction->total_amount += $sumTotalAmount;
                    $existingTransaction->paid_amount += $sumPaidAmount;
                    $existingTransaction->due_amount += $sumDueAmount;
                    $existingTransaction->save();
                } else {
                    $supplierTransaction = new SupplierTransaction();
                    $supplierTransaction->supplier_id = $supplierId;
                    $supplierTransaction->total_amount = $sumTotalAmount;
                    $supplierTransaction->paid_amount = $sumPaidAmount;
                    $supplierTransaction->due_amount = $sumDueAmount;
                    $supplierTransaction->save();
                }
            }
        }
    }
}