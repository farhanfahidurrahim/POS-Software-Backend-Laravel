<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Variation;
use App\Models\TransferStock;
use App\Models\ProductStock;

class TransferStockSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        ////////////////////Branch 1 Transfer///////////////////////
        try {
            $variationIds = [1];
            $variationQtys = [250];
            $numItems = count($variationIds);

            for ($i = 0; $i < $numItems; $i++) {
                $variationId = $variationIds[$i];
                $requestedQty = $variationQtys[$i];
                $variation = Variation::find($variationId);

                if (!$variation) {
                    $this->command->error("Variation $variationId not found!");
                    continue;
                }

                if ($variation->stock_ammount < $requestedQty) {
                    $this->command->warn("Insufficient stock for variation $variationId!");
                    continue;
                }

                $variation->stock_ammount -= $requestedQty;
                $variation->save();

                $transferstock = new TransferStock();
                $transferstock->product_id = $variation->product_id;
                $transferstock->variation_id = $variationId;
                $unitPrice = $transferstock->unit_price = $variation->sell_price_inc_tax;
                $transferstock->reference = 'fahidur';
                $transferstock->branch_id = 1;
                $transferstock->variation_qty = $requestedQty;
                $transferstock->total_amount = $unitPrice * $requestedQty;
                $transferstock->note = 'This is note...';
                $transferstock->status = 'active';
                $transferstock->created_by = 1;
                $transferstock->save();

                $productstock = ProductStock::where('branch_id', $transferstock->branch_id)
                    ->where('variation_id', $variationId)
                    ->first();

                if ($productstock) {
                    $productstock->qty += $requestedQty;
                    $productstock->save();
                } else {
                    $productstock = new ProductStock();
                    $productstock->branch_id = $transferstock->branch_id;
                    $productstock->product_id = $transferstock->product_id;
                    $productstock->variation_id = $variationId;
                    $productstock->qty = $requestedQty;
                    $productstock->save();
                }
            }

            $this->command->info('Transfer stocks seeded successfully!');
        } catch (\Exception $e) {
            $this->command->error('Error seeding transfer stocks: ' . $e->getMessage());
        }


        try {
            $variationIds = [2, 3];
            $variationQtys = [200, 200];
            $numItems = count($variationIds);

            for ($i = 0; $i < $numItems; $i++) {
                $variationId = $variationIds[$i];
                $requestedQty = $variationQtys[$i];
                $variation = Variation::find($variationId);

                if (!$variation) {
                    $this->command->error("Variation $variationId not found!");
                    continue;
                }

                if ($variation->stock_ammount < $requestedQty) {
                    $this->command->warn("Insufficient stock for variation $variationId!");
                    continue;
                }

                $variation->stock_ammount -= $requestedQty;
                $variation->save();

                $transferstock = new TransferStock();
                $transferstock->product_id = $variation->product_id;
                $transferstock->variation_id = $variationId;
                $unitPrice = $transferstock->unit_price = $variation->sell_price_inc_tax;
                $transferstock->reference = 'fahidur';
                $transferstock->branch_id = 1;
                $transferstock->variation_qty = $requestedQty;
                $transferstock->total_amount = $unitPrice * $requestedQty;
                $transferstock->note = 'This is note...';
                $transferstock->status = 'active';
                $transferstock->created_by = 1;
                $transferstock->save();

                $productstock = ProductStock::where('branch_id', $transferstock->branch_id)
                    ->where('variation_id', $variationId)
                    ->first();

                if ($productstock) {
                    $productstock->qty += $requestedQty;
                    $productstock->save();
                } else {
                    $productstock = new ProductStock();
                    $productstock->branch_id = $transferstock->branch_id;
                    $productstock->product_id = $transferstock->product_id;
                    $productstock->variation_id = $variationId;
                    $productstock->qty = $requestedQty;
                    $productstock->save();
                }
            }

            $this->command->info('Transfer stocks seeded successfully!');
        } catch (\Exception $e) {
            $this->command->error('Error seeding transfer stocks: ' . $e->getMessage());
        }

        try {
            $variationIds = [4, 5];
            $variationQtys = [250, 250];
            $numItems = count($variationIds);

            for ($i = 0; $i < $numItems; $i++) {
                $variationId = $variationIds[$i];
                $requestedQty = $variationQtys[$i];
                $variation = Variation::find($variationId);

                if (!$variation) {
                    $this->command->error("Variation $variationId not found!");
                    continue;
                }

                if ($variation->stock_ammount < $requestedQty) {
                    $this->command->warn("Insufficient stock for variation $variationId!");
                    continue;
                }

                $variation->stock_ammount -= $requestedQty;
                $variation->save();

                $transferstock = new TransferStock();
                $transferstock->product_id = $variation->product_id;
                $transferstock->variation_id = $variationId;
                $unitPrice = $transferstock->unit_price = $variation->sell_price_inc_tax;
                $transferstock->reference = 'fahidur';
                $transferstock->branch_id = 1;
                $transferstock->variation_qty = $requestedQty;
                $transferstock->total_amount = $unitPrice * $requestedQty;
                $transferstock->note = 'This is note...';
                $transferstock->status = 'active';
                $transferstock->created_by = 1;
                $transferstock->save();

                $productstock = ProductStock::where('branch_id', $transferstock->branch_id)
                    ->where('variation_id', $variationId)
                    ->first();

                if ($productstock) {
                    $productstock->qty += $requestedQty;
                    $productstock->save();
                } else {
                    $productstock = new ProductStock();
                    $productstock->branch_id = $transferstock->branch_id;
                    $productstock->product_id = $transferstock->product_id;
                    $productstock->variation_id = $variationId;
                    $productstock->qty = $requestedQty;
                    $productstock->save();
                }
            }

            $this->command->info('Transfer stocks seeded successfully!');
        } catch (\Exception $e) {
            $this->command->error('Error seeding transfer stocks: ' . $e->getMessage());
        }

        try {
            $variationIds = [6, 7];
            $variationQtys = [300, 300];
            $numItems = count($variationIds);

            for ($i = 0; $i < $numItems; $i++) {
                $variationId = $variationIds[$i];
                $requestedQty = $variationQtys[$i];
                $variation = Variation::find($variationId);

                if (!$variation) {
                    $this->command->error("Variation $variationId not found!");
                    continue;
                }

                if ($variation->stock_ammount < $requestedQty) {
                    $this->command->warn("Insufficient stock for variation $variationId!");
                    continue;
                }

                $variation->stock_ammount -= $requestedQty;
                $variation->save();

                $transferstock = new TransferStock();
                $transferstock->product_id = $variation->product_id;
                $transferstock->variation_id = $variationId;
                $unitPrice = $transferstock->unit_price = $variation->sell_price_inc_tax;
                $transferstock->reference = 'fahidur';
                $transferstock->branch_id = 1;
                $transferstock->variation_qty = $requestedQty;
                $transferstock->total_amount = $unitPrice * $requestedQty;
                $transferstock->note = 'This is note...';
                $transferstock->status = 'active';
                $transferstock->created_by = 1;
                $transferstock->save();

                $productstock = ProductStock::where('branch_id', $transferstock->branch_id)
                    ->where('variation_id', $variationId)
                    ->first();

                if ($productstock) {
                    $productstock->qty += $requestedQty;
                    $productstock->save();
                } else {
                    $productstock = new ProductStock();
                    $productstock->branch_id = $transferstock->branch_id;
                    $productstock->product_id = $transferstock->product_id;
                    $productstock->variation_id = $variationId;
                    $productstock->qty = $requestedQty;
                    $productstock->save();
                }
            }

            $this->command->info('Transfer stocks seeded successfully!');
        } catch (\Exception $e) {
            $this->command->error('Error seeding transfer stocks: ' . $e->getMessage());
        }

        ////////////////////Branch 2 Transfer///////////////////////

        // try {
        //     $variationIds = [1];
        //     $variationQtys = [45];
        //     $numItems = count($variationIds);

        //     for ($i = 0; $i < $numItems; $i++) {
        //         $variationId = $variationIds[$i];
        //         $requestedQty = $variationQtys[$i];
        //         $variation = Variation::find($variationId);

        //         if (!$variation) {
        //             $this->command->error("Variation $variationId not found!");
        //             continue;
        //         }

        //         if ($variation->stock_ammount < $requestedQty) {
        //             $this->command->warn("Insufficient stock for variation $variationId!");
        //             continue;
        //         }

        //         $variation->stock_ammount -= $requestedQty;
        //         $variation->save();

        //         $transferstock = new TransferStock();
        //         $transferstock->product_id = $variation->product_id;
        //         $transferstock->variation_id = $variationId;
        //         $unitPrice = $transferstock->unit_price = $variation->sell_price_inc_tax;
        //         $transferstock->reference = 'fahidur';
        //         $transferstock->branch_id = 2;
        //         $transferstock->variation_qty = $requestedQty;
        //         $transferstock->total_amount = $unitPrice * $requestedQty;
        //         $transferstock->note = 'This is note...';
        //         $transferstock->status = 'active';
        //         $transferstock->created_by = 1;
        //         $transferstock->save();

        //         $productstock = ProductStock::where('branch_id', $transferstock->branch_id)
        //             ->where('variation_id', $variationId)
        //             ->first();

        //         if ($productstock) {
        //             $productstock->qty += $requestedQty;
        //             $productstock->save();
        //         } else {
        //             $productstock = new ProductStock();
        //             $productstock->branch_id = $transferstock->branch_id;
        //             $productstock->product_id = $transferstock->product_id;
        //             $productstock->variation_id = $variationId;
        //             $productstock->qty = $requestedQty;
        //             $productstock->save();
        //         }
        //     }

        //     $this->command->info('Transfer stocks seeded successfully!');
        // } catch (\Exception $e) {
        //     $this->command->error('Error seeding transfer stocks: ' . $e->getMessage());
        // }

        // try {
        //     $variationIds = [2, 3];
        //     $variationQtys = [150, 150];
        //     $numItems = count($variationIds);

        //     for ($i = 0; $i < $numItems; $i++) {
        //         $variationId = $variationIds[$i];
        //         $requestedQty = $variationQtys[$i];
        //         $variation = Variation::find($variationId);

        //         if (!$variation) {
        //             $this->command->error("Variation $variationId not found!");
        //             continue;
        //         }

        //         if ($variation->stock_ammount < $requestedQty) {
        //             $this->command->warn("Insufficient stock for variation $variationId!");
        //             continue;
        //         }

        //         $variation->stock_ammount -= $requestedQty;
        //         $variation->save();

        //         $transferstock = new TransferStock();
        //         $transferstock->product_id = $variation->product_id;
        //         $transferstock->variation_id = $variationId;
        //         $unitPrice = $transferstock->unit_price = $variation->sell_price_inc_tax;
        //         $transferstock->reference = 'fahidur';
        //         $transferstock->branch_id = 2;
        //         $transferstock->variation_qty = $requestedQty;
        //         $transferstock->total_amount = $unitPrice * $requestedQty;
        //         $transferstock->note = 'This is note...';
        //         $transferstock->status = 'active';
        //         $transferstock->created_by = 1;
        //         $transferstock->save();

        //         $productstock = ProductStock::where('branch_id', $transferstock->branch_id)
        //             ->where('variation_id', $variationId)
        //             ->first();

        //         if ($productstock) {
        //             $productstock->qty += $requestedQty;
        //             $productstock->save();
        //         } else {
        //             $productstock = new ProductStock();
        //             $productstock->branch_id = $transferstock->branch_id;
        //             $productstock->product_id = $transferstock->product_id;
        //             $productstock->variation_id = $variationId;
        //             $productstock->qty = $requestedQty;
        //             $productstock->save();
        //         }
        //     }

        //     $this->command->info('Transfer stocks seeded successfully!');
        // } catch (\Exception $e) {
        //     $this->command->error('Error seeding transfer stocks: ' . $e->getMessage());
        // }

        // try {
        //     $variationIds = [4, 5];
        //     $variationQtys = [200, 200];
        //     $numItems = count($variationIds);

        //     for ($i = 0; $i < $numItems; $i++) {
        //         $variationId = $variationIds[$i];
        //         $requestedQty = $variationQtys[$i];
        //         $variation = Variation::find($variationId);

        //         if (!$variation) {
        //             $this->command->error("Variation $variationId not found!");
        //             continue;
        //         }

        //         if ($variation->stock_ammount < $requestedQty) {
        //             $this->command->warn("Insufficient stock for variation $variationId!");
        //             continue;
        //         }

        //         $variation->stock_ammount -= $requestedQty;
        //         $variation->save();

        //         $transferstock = new TransferStock();
        //         $transferstock->product_id = $variation->product_id;
        //         $transferstock->variation_id = $variationId;
        //         $unitPrice = $transferstock->unit_price = $variation->sell_price_inc_tax;
        //         $transferstock->reference = 'fahidur';
        //         $transferstock->branch_id = 2;
        //         $transferstock->variation_qty = $requestedQty;
        //         $transferstock->total_amount = $unitPrice * $requestedQty;
        //         $transferstock->note = 'This is note...';
        //         $transferstock->status = 'active';
        //         $transferstock->created_by = 1;
        //         $transferstock->save();

        //         $productstock = ProductStock::where('branch_id', $transferstock->branch_id)
        //             ->where('variation_id', $variationId)
        //             ->first();

        //         if ($productstock) {
        //             $productstock->qty += $requestedQty;
        //             $productstock->save();
        //         } else {
        //             $productstock = new ProductStock();
        //             $productstock->branch_id = $transferstock->branch_id;
        //             $productstock->product_id = $transferstock->product_id;
        //             $productstock->variation_id = $variationId;
        //             $productstock->qty = $requestedQty;
        //             $productstock->save();
        //         }
        //     }

        //     $this->command->info('Transfer stocks seeded successfully!');
        // } catch (\Exception $e) {
        //     $this->command->error('Error seeding transfer stocks: ' . $e->getMessage());
        // }

        // try {
        //     $variationIds = [6, 7];
        //     $variationQtys = [250, 250];
        //     $numItems = count($variationIds);

        //     for ($i = 0; $i < $numItems; $i++) {
        //         $variationId = $variationIds[$i];
        //         $requestedQty = $variationQtys[$i];
        //         $variation = Variation::find($variationId);

        //         if (!$variation) {
        //             $this->command->error("Variation $variationId not found!");
        //             continue;
        //         }

        //         if ($variation->stock_ammount < $requestedQty) {
        //             $this->command->warn("Insufficient stock for variation $variationId!");
        //             continue;
        //         }

        //         $variation->stock_ammount -= $requestedQty;
        //         $variation->save();

        //         $transferstock = new TransferStock();
        //         $transferstock->product_id = $variation->product_id;
        //         $transferstock->variation_id = $variationId;
        //         $unitPrice = $transferstock->unit_price = $variation->sell_price_inc_tax;
        //         $transferstock->reference = 'fahidur';
        //         $transferstock->branch_id = 2;
        //         $transferstock->variation_qty = $requestedQty;
        //         $transferstock->total_amount = $unitPrice * $requestedQty;
        //         $transferstock->note = 'This is note...';
        //         $transferstock->status = 'active';
        //         $transferstock->created_by = 1;
        //         $transferstock->save();

        //         $productstock = ProductStock::where('branch_id', $transferstock->branch_id)
        //             ->where('variation_id', $variationId)
        //             ->first();

        //         if ($productstock) {
        //             $productstock->qty += $requestedQty;
        //             $productstock->save();
        //         } else {
        //             $productstock = new ProductStock();
        //             $productstock->branch_id = $transferstock->branch_id;
        //             $productstock->product_id = $transferstock->product_id;
        //             $productstock->variation_id = $variationId;
        //             $productstock->qty = $requestedQty;
        //             $productstock->save();
        //         }
        //     }

        //     $this->command->info('Transfer stocks seeder created successfully!');
        // } catch (\Exception $e) {
        //     $this->command->error('Error seeding transfer stocks: ' . $e->getMessage());
        // }
    }
}




