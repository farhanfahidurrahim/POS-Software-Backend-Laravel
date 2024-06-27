<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;
use App\Models\VariationTemplate;
use App\Models\VariationValueTemplate;
use App\Models\Variation;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Category = 1 | Salwar Kameez ||

        try {
            $product                     = new Product();
            $product->name               = 'Redwine Embroidery Cotton';
            $product->detail             = 'This is Zigzag Pastel';
            $product->sku                = 'zigzag-pastel-sky';
            $product->unit_id            = 1;
            $product->type               = 'single';
            $product->sub_unit_ids       = '1';
            $product->brand_id           = 1;
            $product->category_id        = 1;
            $product->image              = "/salwar1.webp";
            $product->created_by         = 1;
            $product->status             = 'active';
            $product->save();

            $variationTemplateId = 2;
            $variationValueId    = 2;

            $variationTemplate = VariationTemplate::find($variationTemplateId);
            $variationValue    = VariationValueTemplate::find($variationValueId);

            $variantData              = new Variation();
            $variantData->product_id  = $product->id;
            $variantData->brand_id    = $product->brand_id;
            $variantData->category_id = $product->category_id;
            $variantData->name        = $product->name . '-' . $variationValue->name;
            $variantData->sub_sku     = 'ce7wmkl-sub-sku';
            $variantData->default_purchase_price = 80;
            $variantData->profit_percent = 10;
            $profit = $variantData->default_purchase_price * ($variantData->profit_percent / 100);
            $variantData->default_sell_price = $variantData->default_purchase_price + $profit;
            $variantData->stock_amount = 500;
            $variantData->alert_quantity = 15;
            $variantData->variation_value_id = $variationValueId;
            $variantData->variation_template_id = $variationTemplateId;
            $variantData->product_barcode = Str::random(10);
            $variantData->images = "/salwar1.webp";
            $variantData->save();

            $this->command->info('Single product seeded successfully!');
        } catch (\Exception $e) {
            $this->command->error('Error seeding single product: ' . $e->getMessage());
        }

        try {
            $product = new Product();
            $product->name = 'Jorjet Embroidery';
            $product->detail = '256GB variant';
            $product->sku = 'i1pm2v';
            $product->unit_id = 1;
            $product->type = 'variable';
            $product->sub_unit_ids = '1';
            $product->brand_id = 1;
            $product->category_id = 1;
            $product->image = "/salwar2.webp";
            $product->created_by = 1;
            $product->status = 'active';
            $product->save();

            $variationTemplates = [1, 2];
            $variationValues = [1, 2];
            $defaultPurchasePrices = [100, 120];
            $profitPercents = [10, 5];
            $defaultSellPrices = [120, 140];
            $stockAmounts = [600, 600];
            $alertQuantity = [20, 15];

            $variantImages = [
                ["/salwar2.webp"],
                ["/salwar3.webp"]
            ];

            for ($x = 0; $x < count($variationTemplates); $x++) {
                $variationTemplate = VariationTemplate::find($variationTemplates[$x]);
                $variationValue = VariationValueTemplate::find($variationValues[$x]);

                $variantData = new Variation();
                $variantData->product_id = $product->id;
                $variantData->brand_id = $product->brand_id;
                $variantData->category_id = $product->category_id;
                $variantData->name = $product->name . '-' . $variationValue->name;
                $variantData->sub_sku = 'i1pm2v-sub-sku';
                $variantData->default_purchase_price = $defaultPurchasePrices[$x];
                $variantData->profit_percent = $profitPercents[$x];
                $profit = $variantData->default_purchase_price * ($variantData->profit_percent / 100);
                $variantData->default_sell_price = $variantData->default_purchase_price + $profit;
                $variantData->stock_amount = $stockAmounts[$x];
                $variantData->alert_quantity = $alertQuantity[$x];
                $variantData->variation_value_id = $variationValues[$x];
                $variantData->variation_template_id = $variationTemplates[$x];
                $variantData->product_barcode = Str::random(10);
                $variantData->images = $variantImages[$x][0];
                $variantData->save();
            }

            $this->command->info('Variable product seeded successfully!');
        } catch (\Exception $e) {
            $this->command->error('Error seeding variable product: ' . $e->getMessage());
        }

        try {
            $product = new Product();
            $product->name = 'Garara Stitch';
            $product->detail = 'Introducing our exclusive collection of Code T-shirts for all programmers and developers!';
            $product->sku = 'ctsioe';
            $product->unit_id = 1;
            $product->type = 'variable';
            $product->sub_unit_ids = '1';
            $product->brand_id = 1;
            $product->category_id = 1;
            $product->image = "/salwar4.webp";
            $product->created_by = 1;
            $product->status = 'active';
            $product->save();

            $variationTemplates = [1, 1];
            $variationValues = [1, 1];
            $defaultPurchasePrices = [100, 100];
            $profitPercents = [10, 20];
            $defaultSellPrices = [175, 175];
            $stockAmounts = [800, 800];
            $alertQuantity = [20, 15];

            $variantImages = [
                ["/salwar4.webp"],
                ["/salwar5.webp"]
            ];

            for ($x = 0; $x < count($variationTemplates); $x++) {
                $variationTemplate = VariationTemplate::find($variationTemplates[$x]);
                $variationValue = VariationValueTemplate::find($variationValues[$x]);

                $variantData = new Variation();
                $variantData->product_id = $product->id;
                $variantData->brand_id = $product->brand_id;
                $variantData->category_id = $product->category_id;
                $variantData->name = $product->name . '-' . $variationValue->name;
                $variantData->sub_sku = 'r5p6v-sub-sku';
                $variantData->default_purchase_price = $defaultPurchasePrices[$x];
                $variantData->profit_percent = $profitPercents[$x];
                $profit = $variantData->default_purchase_price * ($variantData->profit_percent / 100);
                $variantData->default_sell_price = $variantData->default_purchase_price + $profit;
                $variantData->stock_amount = $stockAmounts[$x];
                $variantData->alert_quantity = $alertQuantity[$x];
                $variantData->variation_value_id = $variationValues[$x];
                $variantData->variation_template_id = $variationTemplates[$x];
                $variantData->product_barcode = Str::random(10);
                $variantData->images = $variantImages[$x][0];
                $variantData->save();
            }

            $this->command->info('Variable product seeded successfully!');
        } catch (\Exception $e) {
            $this->command->error('Error seeding variable product: ' . $e->getMessage());
        }

        try {
            $product = new Product();
            $product->name = 'Embroidered Viscose Cotton';
            $product->detail = '256GB variant';
            $product->sku = 'j-e-s';
            $product->unit_id = 1;
            $product->type = 'variable';
            $product->sub_unit_ids = '1';
            $product->brand_id = 1;
            $product->category_id = 1;
            $product->image = "/salwar6.webp";
            $product->created_by = 1;
            $product->status = 'active';
            $product->save();

            $variationTemplates = [2, 2];
            $variationValues = [2, 2];
            $defaultPurchasePrices = [100, 120];
            $profitPercents = [10, 5];
            $defaultSellPrices = [120, 140];
            $stockAmounts = [600, 600];
            $alertQuantity = [20, 15];

            $variantImages = [
                ["/salwar6.webp"],
                ["/salwar7.webp"]
            ];

            for ($x = 0; $x < count($variationTemplates); $x++) {
                $variationTemplate = VariationTemplate::find($variationTemplates[$x]);
                $variationValue = VariationValueTemplate::find($variationValues[$x]);

                $variantData = new Variation();
                $variantData->product_id = $product->id;
                $variantData->brand_id = $product->brand_id;
                $variantData->category_id = $product->category_id;
                $variantData->name = $product->name . '-' . $variationValue->name;
                $variantData->sub_sku = 'i1pm2v-sub-sku';
                $variantData->default_purchase_price = $defaultPurchasePrices[$x];
                $variantData->profit_percent = $profitPercents[$x];
                $profit = $variantData->default_purchase_price * ($variantData->profit_percent / 100);
                $variantData->default_sell_price = $variantData->default_purchase_price + $profit;
                $variantData->stock_amount = $stockAmounts[$x];
                $variantData->alert_quantity = $alertQuantity[$x];
                $variantData->variation_value_id = $variationValues[$x];
                $variantData->variation_template_id = $variationTemplates[$x];
                $variantData->product_barcode = Str::random(10);
                $variantData->images = $variantImages[$x][0];
                $variantData->save();
            }

            $this->command->info('Variable product seeded successfully!');
        } catch (\Exception $e) {
            $this->command->error('Error seeding variable product: ' . $e->getMessage());
        }




        // Category = 2 | Saree ||

        try {
            $product = new Product();
            $product->name = 'Pure Cotton';
            $product->detail = '64GB variant';
            $product->sku = 'r5p6v';
            $product->unit_id = 1;
            $product->type = 'variable';
            $product->sub_unit_ids = '1';
            $product->brand_id = 1;
            $product->category_id = 2;
            $product->image = "/saree1.webp";
            $product->created_by = 1;
            $product->status = 'active';
            $product->save();

            $variationTemplates = [1, 1];
            $variationValues = [1, 1];
            $defaultPurchasePrices = [80, 75];
            $profitPercents = [20, 10];
            $defaultSellPrices = [95, 98];
            $stockAmounts = [700, 700];
            $alertQuantity = [20, 15];

            $variantImages = [
                ["/saree1.webp"],
                ["/saree2.webp"]
            ];

            for ($x = 0; $x < count($variationTemplates); $x++) {
                $variationTemplate = VariationTemplate::find($variationTemplates[$x]);
                $variationValue = VariationValueTemplate::find($variationValues[$x]);

                $variantData = new Variation();
                $variantData->product_id = $product->id;
                $variantData->brand_id = $product->brand_id;
                $variantData->category_id = $product->category_id;
                $variantData->name = $product->name . '-' . $variationValue->name;
                $variantData->sub_sku = 'r5p6v-sub-sku';
                $variantData->default_purchase_price = $defaultPurchasePrices[$x];
                $variantData->profit_percent = $profitPercents[$x];
                $profit = $variantData->default_purchase_price * ($variantData->profit_percent / 100);
                $variantData->default_sell_price = $variantData->default_purchase_price + $profit;
                $variantData->stock_amount = $stockAmounts[$x];
                $variantData->alert_quantity = $alertQuantity[$x];
                $variantData->variation_value_id = $variationValues[$x];
                $variantData->variation_template_id = $variationTemplates[$x];
                $variantData->product_barcode = Str::random(10);
                $variantData->images = $variantImages[$x][0];
                $variantData->save();
            }

            $this->command->info('Variable product seeded successfully!');
        } catch (\Exception $e) {
            $this->command->error('Error seeding variable product: ' . $e->getMessage());
        }

        try {
            $product = new Product();
            $product->name = 'Georgette Wear';
            $product->detail = 'Introducing our exclusive collection of Code T-shirts for all programmers and developers!';
            $product->sku = 'ctsioe';
            $product->unit_id = 1;
            $product->type = 'variable';
            $product->sub_unit_ids = '1';
            $product->brand_id = 1;
            $product->category_id = 2;
            $product->image = "/saree3.webp";
            $product->created_by = 1;
            $product->status = 'active';
            $product->save();

            $variationTemplates = [1, 1];
            $variationValues = [1, 1];
            $defaultPurchasePrices = [100, 100];
            $profitPercents = [10, 20];
            $defaultSellPrices = [175, 175];
            $stockAmounts = [800, 800];
            $alertQuantity = [20, 15];

            $variantImages = [
                ["/saree3.webp"],
                ["/saree4.webp"]
            ];

            for ($x = 0; $x < count($variationTemplates); $x++) {
                $variationTemplate = VariationTemplate::find($variationTemplates[$x]);
                $variationValue = VariationValueTemplate::find($variationValues[$x]);

                $variantData = new Variation();
                $variantData->product_id = $product->id;
                $variantData->brand_id = $product->brand_id;
                $variantData->category_id = $product->category_id;
                $variantData->name = $product->name . '-' . $variationValue->name;
                $variantData->sub_sku = 'r5p6v-sub-sku';
                $variantData->default_purchase_price = $defaultPurchasePrices[$x];
                $variantData->profit_percent = $profitPercents[$x];
                $profit = $variantData->default_purchase_price * ($variantData->profit_percent / 100);
                $variantData->default_sell_price = $variantData->default_purchase_price + $profit;
                $variantData->stock_amount = $stockAmounts[$x];
                $variantData->alert_quantity = $alertQuantity[$x];
                $variantData->variation_value_id = $variationValues[$x];
                $variantData->variation_template_id = $variationTemplates[$x];
                $variantData->product_barcode = Str::random(10);
                $variantData->images = $variantImages[$x][0];
                $variantData->save();
            }

            $this->command->info('Variable product seeded successfully!');
        } catch (\Exception $e) {
            $this->command->error('Error seeding variable product: ' . $e->getMessage());
        }

        try {
            $product = new Product();
            $product->name = 'Printed Silk';
            $product->detail = 'Introducing our exclusive collection of Code T-shirts for all programmers and developers!';
            $product->sku = 'ctsioe';
            $product->unit_id = 1;
            $product->type = 'variable';
            $product->sub_unit_ids = '1';
            $product->brand_id = 1;
            $product->category_id = 2;
            $product->image = "/saree5.webp";
            $product->created_by = 1;
            $product->status = 'active';
            $product->save();

            $variationTemplates = [2, 2];
            $variationValues = [2, 2];
            $defaultPurchasePrices = [100, 100];
            $profitPercents = [10, 20];
            $defaultSellPrices = [175, 175];
            $stockAmounts = [800, 800];
            $alertQuantity = [20, 15];

            $variantImages = [
                ["/saree5.webp"],
                ["/saree6.webp"],
            ];

            for ($x = 0; $x < count($variationTemplates); $x++) {
                $variationTemplate = VariationTemplate::find($variationTemplates[$x]);
                $variationValue = VariationValueTemplate::find($variationValues[$x]);

                $variantData = new Variation();
                $variantData->product_id = $product->id;
                $variantData->brand_id = $product->brand_id;
                $variantData->category_id = $product->category_id;
                $variantData->name = $product->name . '-' . $variationValue->name;
                $variantData->sub_sku = 'r5p6v-sub-sku';
                $variantData->default_purchase_price = $defaultPurchasePrices[$x];
                $variantData->profit_percent = $profitPercents[$x];
                $profit = $variantData->default_purchase_price * ($variantData->profit_percent / 100);
                $variantData->default_sell_price = $variantData->default_purchase_price + $profit;
                $variantData->stock_amount = $stockAmounts[$x];
                $variantData->alert_quantity = $alertQuantity[$x];
                $variantData->variation_value_id = $variationValues[$x];
                $variantData->variation_template_id = $variationTemplates[$x];
                $variantData->product_barcode = Str::random(10);
                $variantData->images = $variantImages[$x][0];
                $variantData->save();
            }

            $this->command->info('Variable product seeded successfully!');
        } catch (\Exception $e) {
            $this->command->error('Error seeding variable product: ' . $e->getMessage());
        }

        try {
            $product                     = new Product();
            $product->name               = 'Jamdani Half Silk';
            $product->detail             = 'This is Jamdani Half Silk';
            $product->sku                = 'zigzag-pastel-sky-salwar';
            $product->unit_id            = 1;
            $product->type               = 'single';
            $product->sub_unit_ids       = '1';
            $product->brand_id           = 1;
            $product->category_id        = 2;
            $product->image              = "/saree7.webp";
            $product->created_by         = 1;
            $product->status             = 'active';
            $product->save();

            $variationTemplateId = 1;
            $variationValueId    = 1;

            $variationTemplate = VariationTemplate::find($variationTemplateId);
            $variationValue    = VariationValueTemplate::find($variationValueId);

            $variantData              = new Variation();
            $variantData->product_id  = $product->id;
            $variantData->brand_id    = $product->brand_id;
            $variantData->category_id = $product->category_id;
            $variantData->name        = $product->name . '-' . $variationValue->name;
            $variantData->sub_sku     = 'ce7wmkl-sub-sku';
            $variantData->default_purchase_price = 80;
            $variantData->profit_percent = 10;
            $profit = $variantData->default_purchase_price * ($variantData->profit_percent / 100);
            $variantData->default_sell_price = $variantData->default_purchase_price + $profit;
            $variantData->stock_amount = 500;
            $variantData->alert_quantity = 15;
            $variantData->variation_value_id = $variationValueId;
            $variantData->variation_template_id = $variationTemplateId;
            $variantData->product_barcode = Str::random(10);
            $variantData->images = "/saree7.webp";
            $variantData->save();

            $this->command->info('Single product seeded successfully!');
        } catch (\Exception $e) {
            $this->command->error('Error seeding single product: ' . $e->getMessage());
        }

        try {
            $product = new Product();
            $product->name = 'Premium Embroidered';
            $product->detail = '64GB variant';
            $product->sku = 'r5p6v';
            $product->unit_id = 1;
            $product->type = 'variable';
            $product->sub_unit_ids = '1';
            $product->brand_id = 1;
            $product->category_id = 2;
            $product->image = "/saree8.webp";
            $product->created_by = 1;
            $product->status = 'active';
            $product->save();

            $variationTemplates = [1, 1];
            $variationValues = [1, 1];
            $defaultPurchasePrices = [80, 75];
            $profitPercents = [20, 10];
            $defaultSellPrices = [95, 98];
            $stockAmounts = [700, 700];
            $alertQuantity = [20, 15];

            $variantImages = [
                ["/saree8.webp"],
                ["/saree9.webp"]
            ];

            for ($x = 0; $x < count($variationTemplates); $x++) {
                $variationTemplate = VariationTemplate::find($variationTemplates[$x]);
                $variationValue = VariationValueTemplate::find($variationValues[$x]);

                $variantData = new Variation();
                $variantData->product_id = $product->id;
                $variantData->brand_id = $product->brand_id;
                $variantData->category_id = $product->category_id;
                $variantData->name = $product->name . '-' . $variationValue->name;
                $variantData->sub_sku = 'r5p6v-sub-sku';
                $variantData->default_purchase_price = $defaultPurchasePrices[$x];
                $variantData->profit_percent = $profitPercents[$x];
                $profit = $variantData->default_purchase_price * ($variantData->profit_percent / 100);
                $variantData->default_sell_price = $variantData->default_purchase_price + $profit;
                $variantData->stock_amount = $stockAmounts[$x];
                $variantData->alert_quantity = $alertQuantity[$x];
                $variantData->variation_value_id = $variationValues[$x];
                $variantData->variation_template_id = $variationTemplates[$x];
                $variantData->product_barcode = Str::random(10);
                $variantData->images = $variantImages[$x][0];
                $variantData->save();
            }

            $this->command->info('Variable product seeded successfully!');
        } catch (\Exception $e) {
            $this->command->error('Error seeding variable product: ' . $e->getMessage());
        }

        // Category = 3 | Lehenga ||

        try {
            $product                     = new Product();
            $product->name               = 'Zigzag Pastel';
            $product->detail             = 'This is Zigzag Pastel';
            $product->sku                = 'zigzag-pastel-sky';
            $product->unit_id            = 1;
            $product->type               = 'single';
            $product->sub_unit_ids       = '1';
            $product->brand_id           = 1;
            $product->category_id        = 1;
            $product->image              = "/lehenga1.webp";
            $product->created_by         = 1;
            $product->status             = 'active';
            $product->save();

            $variationTemplateId = 2;
            $variationValueId    = 2;

            $variationTemplate = VariationTemplate::find($variationTemplateId);
            $variationValue    = VariationValueTemplate::find($variationValueId);

            $variantData              = new Variation();
            $variantData->product_id  = $product->id;
            $variantData->brand_id    = $product->brand_id;
            $variantData->category_id = $product->category_id;
            $variantData->name        = $product->name . '-' . $variationValue->name;
            $variantData->sub_sku     = 'ce7wmkl-sub-sku';
            $variantData->default_purchase_price = 80;
            $variantData->profit_percent = 10;
            $profit = $variantData->default_purchase_price * ($variantData->profit_percent / 100);
            $variantData->default_sell_price = $variantData->default_purchase_price + $profit;
            $variantData->stock_amount = 500;
            $variantData->alert_quantity = 15;
            $variantData->variation_value_id = $variationValueId;
            $variantData->variation_template_id = $variationTemplateId;
            $variantData->product_barcode = Str::random(10);
            $variantData->images = "/lehenga1.webp";
            $variantData->save();

            $this->command->info('Single product seeded successfully!');
        } catch (\Exception $e) {
            $this->command->error('Error seeding single product: ' . $e->getMessage());
        }

        try {
            $product = new Product();
            $product->name = 'Indian Soft Georgette';
            $product->detail = 'Introducing our exclusive collection of Code T-shirts for all programmers and developers!';
            $product->sku = 'ctsioe';
            $product->unit_id = 1;
            $product->type = 'variable';
            $product->sub_unit_ids = '1';
            $product->brand_id = 1;
            $product->category_id = 1;
            $product->image = "/lehenga2.webp";
            $product->created_by = 1;
            $product->status = 'active';
            $product->save();

            $variationTemplates = [2, 2];
            $variationValues = [2, 2];
            $defaultPurchasePrices = [100, 100];
            $profitPercents = [10, 20];
            $defaultSellPrices = [175, 175];
            $stockAmounts = [800, 800];
            $alertQuantity = [20, 15];

            $variantImages = [
                ["/lehenga2.webp"],
                ["/lehenga3.webp"]
            ];

            for ($x = 0; $x < count($variationTemplates); $x++) {
                $variationTemplate = VariationTemplate::find($variationTemplates[$x]);
                $variationValue = VariationValueTemplate::find($variationValues[$x]);

                $variantData = new Variation();
                $variantData->product_id = $product->id;
                $variantData->brand_id = $product->brand_id;
                $variantData->category_id = $product->category_id;
                $variantData->name = $product->name . '-' . $variationValue->name;
                $variantData->sub_sku = 'r5p6v-sub-sku';
                $variantData->default_purchase_price = $defaultPurchasePrices[$x];
                $variantData->profit_percent = $profitPercents[$x];
                $profit = $variantData->default_purchase_price * ($variantData->profit_percent / 100);
                $variantData->default_sell_price = $variantData->default_purchase_price + $profit;
                $variantData->stock_amount = $stockAmounts[$x];
                $variantData->alert_quantity = $alertQuantity[$x];
                $variantData->variation_value_id = $variationValues[$x];
                $variantData->variation_template_id = $variationTemplates[$x];
                $variantData->product_barcode = Str::random(10);
                $variantData->images = $variantImages[$x][0];
                $variantData->save();
            }

            $this->command->info('Variable product seeded successfully!');
        } catch (\Exception $e) {
            $this->command->error('Error seeding variable product: ' . $e->getMessage());
        }
    }
}