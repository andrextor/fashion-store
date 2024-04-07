<?php

namespace Tests\Feature;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ListTest extends TestCase
{
    use RefreshDatabase;

    /** 
     * @test 
     */
    public function canSeeProductsList(): void
    {
        Product::factory()->count(5)->create();
            
        $response = $this->get(route('home'));
        $response->assertOk();
        $response->assertViewIs('products.index');
        $response->assertSee('Comprar');
        $this->assertCount(5, $response->viewData('products'));
    }

    /** 
     * @test 
     */
    public function cantSeeBuyButtonBecauseThereAreNotProducts(): void
    {            
        $response = $this->get(route('home'));
        $response->assertDontSee('Comprar');        
        $this->assertCount(0, $response->viewData('products'));
    }
}
