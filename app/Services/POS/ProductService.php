<?php
namespace App\Services\POS;

use App\Models\Products;
use App\Models\Stocks;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\DB;
use Exception;

class ProductService
{
    /**
     * Create a new product with optional initial stock
     * 
     * @param array $data Product data from request
     * @return Products
     * @throws Exception
     */
    public function createProduct(array $data): Products
    {
        DB::beginTransaction();
        try {
            $product = Products::create([
                'product_name' => $data['product_name'],
                'product_description' => $data['product_description'] ?? null,
                'product_price' => $data['product_price'],
                'product_barcode' => $data['product_barcode'] ?? null,
                'category_id' => $data['category_id'] ?? null,
            ]);

            $this->logActivity("Created Product", [
                "product_id" => $product->id,
                "name" => $product->product_name
            ]);


            if ($this->hasValidStock($data['initial_stock'] ?? null)) {
                $this->createInitialStock($product->id, $data['initial_stock']);
            }

            DB::commit();
            return $product;

        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Create initial stock for a product
     * 
     * @param int $productId
     * @param int $quantity
     * @return Stocks
     */
    private function createInitialStock(int $productId, int $quantity): Stocks
    {
        $stock = Stocks::create([
            'product_id' => $productId,
            'quantity' => (int)$quantity,
        ]);

        $this->logActivity("Initial Stock Set", [
            "product_id" => $productId,
            "quantity" => (int)$quantity,
        ]);

        return $stock;
    }

    /**
     * Check if stock value is valid
     * 
     * @param mixed $stock
     * @return bool
     */
    private function hasValidStock($stock): bool
    {
        return is_numeric($stock) && (int)$stock > 0;
    }

    /**
     * Log activity to ActivityLog table
     * 
     * @param string $action
     * @param array $details
     * @return void
     */
    private function logActivity(string $action, array $details): void
    {
        try {
            ActivityLog::create([
                'action' => $action,
                'details' => json_encode($details),
                'user_id' => auth()->id(),
                'created_at' => now(),
            ]);
        } catch (Exception $e) {
        }
    }
}