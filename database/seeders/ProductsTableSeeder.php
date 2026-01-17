<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Products;
use App\Models\Category;
use App\Models\Stocks;
use Faker\Factory as Faker;

class ProductsTableSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();
        
        echo "Starting product generation...\n\n";
        
        // Get existing categories or create some
        $categories = Category::all();
        
        if ($categories->isEmpty()) {
            echo "No categories found. Creating sample categories...\n";
            $categoryNames = [
                'Electronics', 
                'Clothing', 
                'Food & Beverages', 
                'Furniture', 
                'Books & Stationery', 
                'Toys & Games', 
                'Sports & Outdoors', 
                'Beauty & Personal Care',
                'Home & Kitchen',
                'Automotive'
            ];
            
            foreach ($categoryNames as $name) {
                Category::create(['category_name' => $name]);
            }
            
            $categories = Category::all();
            echo "Created " . $categories->count() . " categories.\n\n";
        }

        // Number of products to generate
        $totalProducts = 1000;
        
        echo "Generating {$totalProducts} products with stock...\n";
        echo str_repeat('=', 50) . "\n";
        
        $productNames = [
            'Laptop', 'Mouse', 'Keyboard', 'Monitor', 'Headphones',
            'T-Shirt', 'Jeans', 'Shoes', 'Hat', 'Jacket',
            'Coffee', 'Tea', 'Juice', 'Snacks', 'Bread',
            'Chair', 'Table', 'Lamp', 'Sofa', 'Desk',
            'Notebook', 'Pen', 'Pencil', 'Marker', 'Book',
            'Ball', 'Toy Car', 'Puzzle', 'Doll', 'Action Figure',
            'Bicycle', 'Tent', 'Backpack', 'Shoes', 'Helmet',
            'Shampoo', 'Soap', 'Lotion', 'Perfume', 'Makeup',
            'Plate', 'Glass', 'Fork', 'Spoon', 'Pot',
            'Tire', 'Oil', 'Battery', 'Tool Kit', 'Car Wash'
        ];
        
        for ($i = 1; $i <= $totalProducts; $i++) {
            // Create product
            $baseName = $productNames[array_rand($productNames)];
            $productName = $baseName . ' ' . $faker->word() . ' #' . $i;
            
            $product = Products::create([
                'product_name' => $productName,
                'category_id' => $categories->random()->id,
                'product_price' => $faker->randomFloat(2, 5, 999.99),
                'product_description' => $faker->sentence(10),
            
                'product_barcode' => $faker->ean13(),
            ]);

            // Create stock for the product
            $quantity = $faker->numberBetween(0, 150);
            
            Stocks::create([
                'product_id' => $product->id,
                'quantity' => $quantity,
                // Add any other fields your stocks table has
            ]);

            // Show progress every 100 products
            if ($i % 100 == 0) {
                $percentage = ($i / $totalProducts) * 100;
                echo "Progress: {$i}/{$totalProducts} ({$percentage}%)\n";
            }
        }

        echo str_repeat('=', 50) . "\n";
        echo "✓ Successfully created {$totalProducts} products with stocks!\n";
        echo "✓ Categories: " . $categories->count() . "\n";
        echo "✓ Total Products: " . Products::count() . "\n";
        echo "✓ Total Stocks: " . Stocks::count() . "\n";
        
        // Show some statistics
        $lowStock = Stocks::where('quantity', '<', 10)->count();
        echo "✓ Low stock items (< 10): {$lowStock}\n";
        
        echo "\nYou can now test your cart at: " . url('/cart') . "\n";
    }
}