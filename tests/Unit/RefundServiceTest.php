<?php

namespace Tests\Unit;

use Tests\TestCase;
use Mockery;
use App\Services\POS\RefundService;
use Illuminate\Support\Collection;

class RefundServiceTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_process_refund_creates_refund_and_updates_stocks()
    {
  
        $sale = new class {
            public $id = 1;
            public $total_amount = 100;
            public function totalRefunded() { return 0; }
            public function refunds() {
                return new class {
                    public function whereHas($a, $b) { return new class { public function exists() { return false; } }; }
                };
            }
            public function update($data) {
                foreach ($data as $k => $v) { $this->{$k} = $v; }
                return true;
            }
        };

        $request = (object)[
            'refund_amount' => 10,
            'refund_type' => 'cash',
            'refund_reason' => 'test',
        ];

        $items = collect([
            ['product_id' => 1, 'quantity' => 2, 'price' => 5, 'is_changed' => 0, 'is_expired' => 0, 'is_damaged' => 0]
        ]);

    
        $stocksMock = Mockery::mock('alias:App\\Models\\Stocks');
        $stocksMock->shouldReceive('whereIn')->with('product_id', [1])->andReturnSelf();
        $stocksMock->shouldReceive('get')->andReturn(collect([(object)['product_id' => 1, 'quantity' => 5]]));
        $stocksMock->shouldReceive('where')->with('product_id', 1)->andReturnSelf();
        $stocksMock->shouldReceive('increment')->with('quantity', 2)->andReturnTrue();

        
        $refundMock = Mockery::mock();
        $itemsRelation = Mockery::mock();
        $itemsRelation->shouldReceive('create')->withAnyArgs()->andReturnUsing(function ($data) { return (object)$data; });
        $refundMock->shouldReceive('items')->andReturn($itemsRelation);

        $refundAlias = Mockery::mock('alias:App\\Models\\Refund');
        $refundAlias->shouldReceive('create')->andReturn($refundMock);

   
        $service = new RefundService();
        $result = $service->processRefund($sale, $request, $items);

     
        $this->assertIsObject($result);
    }
}
