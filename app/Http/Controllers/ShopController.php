<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Page;
use App\Models\Product;
use App\Models\ProductFamily;

class ShopController extends Controller
{
    public function home()
    {
        $families = ProductFamily::with(['products' => fn ($q) => $q->where('is_active', true)])
            ->where('is_active', true)->orderBy('sort_order')->get();

        $featured = Product::where('is_active', true)->where('is_featured', true)->take(6)->get();

        return view('shop.home', compact('families', 'featured'));
    }

    public function family(ProductFamily $family)
    {
        abort_unless($family->is_active, 404);
        $family->load(['products' => fn ($q) => $q->where('is_active', true)->orderBy('sort_order')]);

        return view('shop.family', compact('family'));
    }

    public function product(Product $product)
    {
        abort_unless($product->is_active, 404);
        $product->load(['family', 'dimensions', 'priceTiers', 'optionGroups.options']);

        return view('shop.product', compact('product'));
    }

    public function page(Page $page)
    {
        abort_unless($page->is_active, 404);

        return view('shop.page', compact('page'));
    }

    public function thankyou(string $order)
    {
        $order = Order::with('items')->where('order_number', $order)->firstOrFail();

        return view('shop.thankyou', compact('order'));
    }
}
