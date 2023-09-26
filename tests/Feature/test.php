<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Product;
use App\Cart;
use App\Order;

class ECommerceController extends Controller
{
    public function index()
    {
        $products = Product::all();

        return view('welcome', ['products' => $products]);
    }

    public function addToCart(Request $request)
    {
        $product = Product::find($request->get('product_id'));

        $cart = Cart::find(session()->get('cart_id'));
        if (!$cart) {
            $cart = new Cart();
            session()->put('cart_id', $cart->id);
        }

        $cart->addItem($product);
        $cart->save();

        return redirect()->back()->with('success', 'Product added to cart successfully!');
    }

    public function viewCart()
    {
        $cart = Cart::find(session()->get('cart_id'));

        return view('cart', ['cart' => $cart]);
    }

    public function checkout()
    {
        $cart = Cart::find(session()->get('cart_id'));

        return view('checkout', ['cart' => $cart]);
    }

    public function placeOrder(Request $request)
    {
        $cart = Cart::find(session()->get('cart_id'));
        $customer = new Customer();
        $customer->name = $request->get('name');
        $customer->email = $request->get('email');
        $customer->save();

        $order = new Order();
        $order->customer_id = $customer->id;
        $order->cart_id = $cart->id;
        $order->save();

        $cart->items = [];
        $cart->save();

        session()->forget('cart_id');

        return redirect()->route('home')->with('success', 'Order placed successfully!');
    }
}
