<?php

namespace Tests\Feature\Orders;

use App\Models\Product;
use App\Services\PlaceToPay;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Fakes\PlaceToPayFake;
use Tests\TestCase;

class CreateTest extends TestCase
{
    use RefreshDatabase;

    /** 
     * @test 
     */
    public function cantSeePayFormWithOneProduct(): void
    {
        $this->app->bind(PlaceToPay::class, function () {
            return new PlaceToPayFake();
        
        });

        $product =  Product::factory()->create();        
        $response = $this->get(route('order.create', $product));
        $response->assertOk();
        $response->assertViewIs('order.create');
        $response->assertSee('Pagar');
        $response->assertViewHas('product');
        $this->assertEquals($product->id, $response->viewData('product')->id);
    }

}
