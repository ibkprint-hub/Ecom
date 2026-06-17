<?php

namespace Tests\Feature;

use App\Livewire\Configurator;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ConfiguratorTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\DatabaseSeeder::class);
    }

    public function test_quote_is_degressive_and_includes_fees(): void
    {
        $product = Product::where('slug', 'mailer-e-commerce')->first();

        $comp = Livewire::test(Configurator::class, ['product' => $product])
            ->set('quantity', 250)
            ->set('designMode', 'service')
            ->set('wilayaCode', '16');

        $quote = $comp->instance()->quote;

        // Prix de base 250 = 78 ; dimension "Petit" x1.0 ; option quadri +12 par défaut ? non, défaut = 1 couleur (0)
        $this->assertEquals(78.0, $quote['unit_price']);
        $this->assertEquals(78.0 * 250, $quote['subtotal']);
        $this->assertEquals(3000.0, $quote['design_fee']);   // service design
        $this->assertEquals(400.0, $quote['shipping_fee']);  // Alger domicile
        $this->assertEquals($quote['subtotal'] + 3000 + 400, $quote['total']);
    }

    public function test_order_is_created_on_submit(): void
    {
        $product = Product::where('slug', 'mailer-e-commerce')->first();

        Livewire::test(Configurator::class, ['product' => $product])
            ->set('quantity', 100)
            ->set('designMode', 'service')
            ->set('brief', 'Logo doré sur fond kraft.')
            ->set('wilayaCode', '31')
            ->set('shippingMethod', 'home')
            ->set('customerName', 'Test Client')
            ->set('customerPhone', '0555123456')
            ->set('address', '12 rue des Tests, Oran')
            ->call('submit')
            ->assertRedirect();

        $this->assertDatabaseCount('orders', 1);
        $order = Order::with('items')->first();
        $this->assertEquals('cod', $order->payment_method);
        $this->assertEquals('new', $order->status);
        $this->assertCount(1, $order->items);
        $this->assertEquals(100, $order->items->first()->quantity);
        $this->assertEquals('service', $order->items->first()->design_mode);
    }

    public function test_submit_requires_customer_fields(): void
    {
        $product = Product::where('slug', 'mailer-e-commerce')->first();

        Livewire::test(Configurator::class, ['product' => $product])
            ->call('submit')
            ->assertHasErrors(['customerName', 'customerPhone', 'wilayaCode', 'address']);

        $this->assertDatabaseCount('orders', 0);
    }
}
