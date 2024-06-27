<?php

namespace Database\Seeders;

use App\Models\Sale;
use App\Models\Branch;
use App\Models\Account;
use App\Models\Product;
use App\Models\Customer;
use App\Models\Variation;
use App\Models\Transaction;
use App\Models\CourierShipping;
use Faker\Factory as Faker;
use App\Models\ProductStock;
use Illuminate\Database\Seeder;
use Carbon\Carbon;
use Picqer\Barcode\BarcodeGeneratorPNG;

class SaleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();

        // Set the current date
        $currentYear = Carbon::now()->year;
        $currentMonth = Carbon::now()->month;

        $saleFrom = ['facebook', 'store', 'ecommerce'];
        $discountTypeSubtotals = ['percentage', 'flat'];
        $shippingCharge = [50, 100];
        $deliveryMethod = ['cod'];

        $variationsCount = Variation::count();

        for ($i = 0; $i < 25; $i++) {

            // Generate some data for the current year and month
            $createdAt = $faker->dateTimeBetween("{$currentYear}-{$currentMonth}-01", 'now');

            // Generate some data for a few previous years and months
            if ($i % 5 === 0) {
                $previousYear = $currentYear - $faker->numberBetween(1, 5);
                $previousMonth = $faker->numberBetween(1, 12);
                $createdAt = $faker->dateTimeBetween("{$previousYear}-{$previousMonth}-01", "{$previousYear}-{$previousMonth}-28");
            }

            $customer = Customer::inRandomOrder()->first();
            $branch = Branch::inRandomOrder()->first();
            $courier = CourierShipping::inRandomOrder()->first();

            $randomVariationIds = [];
            $quantities = [];
            $unitPrices = [];
            $discountPercentages = [];
            $possibleDiscounts = [2, 5, 10, 20];

            $variationCount = rand(1, 3);

            for ($j = 0; $j < $variationCount; $j++) {
                $randomVariationId = rand(1, $variationsCount);
                $randomVariationIds[] = (string)$randomVariationId;

                $variation = Variation::find($randomVariationId);
                $unitPrices[] = (string)$variation->default_sell_price;
                $quantities[] = (string)rand(1, 5);

                $randomKey = array_rand($possibleDiscounts);
                $discountPercentages[] = (string) $possibleDiscounts[$randomKey];
            }
            // while (count($randomVariationIds) < $variationCount) {
            //     $randomVariationId = rand(1, $variationsCount);
            //     if (!in_array($randomVariationId, $randomVariationIds)) {
            //         $randomVariationIds[] = $randomVariationId;

            //         $variation = Variation::find($randomVariationId);
            //         $unitPrices[] = (string)$variation->default_sell_price;
            //         $quantities[] = (string)rand(1, 5);

            //         $randomKey = array_rand($possibleDiscounts);
            //         $discountPercentages[] = (string) $possibleDiscounts[$randomKey];
            //     }
            // }

            $invoiceNumber = str_pad($i + 1, 6, '0', STR_PAD_LEFT);
            $invoice = 'INV-SALE-' . $invoiceNumber;

            $barcodePath = public_path("barcodes/{$invoice}.png");

            $generator = new BarcodeGeneratorPNG();
            file_put_contents($barcodePath, $generator->getBarcode($invoice, $generator::TYPE_CODE_128));

            $sale = Sale::create([
                'invoice' => $invoice,
                'barcode_path' => "barcodes/{$invoice}.png",
                'customer_id' => $customer->id,
                'sale_from' => $saleFrom[array_rand($saleFrom)],
                'branch_id' => $branch->id,
                'variation_id' => json_encode($randomVariationIds),
                'quantity' => json_encode($quantities),
                'unit_price' => json_encode($unitPrices),
                'discount_percentage' => json_encode($discountPercentages),
                'discount_type_subtotal' => $discountTypeSubtotals[array_rand($discountTypeSubtotals)],
                'shipping_charge' => $shippingCharge[array_rand($shippingCharge)],
                'delivery_method' => $deliveryMethod[array_rand($deliveryMethod)],
                'courier_id' => $courier->id,
                'note' => 'Sample note for sale ' . ($i + 1),
                'created_by' => 1,
                'created_at' => $createdAt,
            ]);

            $barcodeData = $sale->invoice;
            $barcodePath = public_path('barcodes/' . $barcodeData . '.png');

            $generator = new BarcodeGeneratorPNG();
            file_put_contents($barcodePath, $generator->getBarcode($barcodeData, $generator::TYPE_CODE_128));

            $sale->barcode_path = 'barcodes/' . $barcodeData . '.png';

            /*____________________Discount on Per Product____________________*/

            $quantity = json_decode($sale->quantity);
            $unitPrices = json_decode($sale->unit_price);

            $discountAmounts = [];
            foreach ($quantity as $key => $qty) {
                $discountAmounts[$key] = ($qty * $unitPrices[$key] * $discountPercentages[$key]) / 100;
            }
            $sale->discount_amount = json_encode($discountAmounts);

            /*____________________Calculate SubTotal____________________*/

            $subTotal = 0;
            foreach ($quantity as $key => $qty) {
                $subTotal += ($qty * $unitPrices[$key]) - $discountAmounts[$key];
            }

            $sale->sub_totals = $subTotal;

            /*____________________Vat & Discount on Subtotal____________________*/

            $possibleDiscountOnSubtotal = [0, 5, 10, 20];
            $discountOnSubtotalAmount = 0;
            if ($sale->discount_type_subtotal === 'percentage') {
                $discountOnSubtotal = $possibleDiscountOnSubtotal[array_rand($possibleDiscountOnSubtotal)];
                $sale->discount_on_subtotal = $discountOnSubtotal;
                $discountOnSubtotalAmount = ($subTotal * $discountOnSubtotal) / 100;
            } elseif ($sale->discount_type_subtotal === 'flat') {
                $discountOnSubtotal = rand(0, 20);
                $sale->discount_on_subtotal = $discountOnSubtotal;
                $discountOnSubtotalAmount = $discountOnSubtotal;
            }
            $sale->discount_on_subtotal_amount = $discountOnSubtotalAmount;

            /*____________________Total Amount & Paid Amount & Payment Status____________________*/

            $totalAmount = $subTotal - $discountOnSubtotalAmount + $sale->shipping_charge;
            $sale->total_amount = $totalAmount;

            if ($sale->delivery_method == 'pp') {
                $cash = rand(0, $totalAmount);
                $bkash = rand(0, $totalAmount - $cash);
                $nagad = rand(0, $totalAmount - ($cash + $bkash));
                $rocket = rand(0, $totalAmount - ($cash + $bkash + $nagad));
                $bank = rand(0, $totalAmount - ($cash + $bkash + $nagad + $rocket));
                $cheque = rand(0, $totalAmount - ($cash + $bkash + $nagad + $rocket + $bank));

                $sale->cash = $cash;
                $sale->cash_number = '01675717825';
                $sale->bkash = $bkash;
                $sale->bkash_number = '01675717826';
                $sale->nagad = $nagad;
                $sale->nagad_number = '01675717827';
                $sale->rocket = $rocket;
                $sale->rocket_number = '01675717828';
                $sale->bank = $bank;
                $sale->bank_number = '01675717829';
                $sale->cheque = $cheque;
                $sale->cheque_number = '01675717830';

                $paidAmount = $cash + $bkash + $nagad + $rocket + $bank + $cheque;
                $sale->paid_amount = $paidAmount;

                if ($paidAmount == $totalAmount) {
                    $sale->due_amount = 0;
                    $sale->payment_status = 'paid';
                } else {
                    $sale->due_amount = $totalAmount - $paidAmount;
                    $sale->payment_status = ($sale->due_amount == 0) ? 'paid' : 'partial';
                }
            } else {
                $paidAmount = $sale->paid_amount = 0;
                $sale->due_amount = $totalAmount;
                $sale->payment_status = 'unpaid';
            }

            $sale->status = 'Processing';
            $sale->save();

            /*____________________Product Stock Qty Update____________________*/

            $variationIds = json_decode($sale->variation_id, true);
            $quantities = json_decode($sale->quantity, true);

            $stockIssues = [];
            foreach ($variationIds as $key => $variation) {

                $stockAmountVariation = Variation::find($variation);
                $getStockQuantity = $stockAmountVariation->stock_amount;
                $getSaleQuantity = $quantities[$key];

                if ($getStockQuantity < $getSaleQuantity) {
                    $productName = $stockAmountVariation ? $stockAmountVariation->name : 'Unknown';
                    $stockIssues[] = 'Product ' . $productName . ' Stock Not Available!';
                } else {
                    $currentQuantity = $stockAmountVariation->stock_amount;
                    $newQuantity = $currentQuantity - $getSaleQuantity;
                    $stockAmountVariation->stock_amount = max(0, $newQuantity);
                    $stockAmountVariation->save();
                }
            }

            if (!empty($stockIssues)) {
                foreach ($stockIssues as $issue) {
                    echo $issue . "\n";
                }
            }

            /*____________________ Account Balance ____________________*/

            $balance = Account::find($branch->id);
            if ($balance) {
                if (empty($balance->available_balance)) {
                    $balance->available_balance = $paidAmount;
                } else {
                    $balance->available_balance += $paidAmount;
                }
                $balance->save();
            } else {
                return response()->json(['message' => 'Branch is not found!'], 404);
            }
        }
    }
}
