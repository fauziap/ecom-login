<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use App\Models\Customer;
use App\Models\Produk;
use App\Models\Order;
use App\Models\OrderItem;

class OrderController extends Controller
{
    public function addToCart($id)
    {
        $customer = Customer::where('user_id', Auth::id())->first();

        if (!$customer) {
            return back()->with('error', 'Customer tidak ditemukan');
        }

        $produk = Produk::findOrFail($id);

        $order = Order::firstOrCreate(
            [
                'customer_id' => $customer->id,
                'status' => 'pending'
            ],
            [
                'total_harga' => 0
            ]
        );

        $orderItem = OrderItem::where('order_id', $order->id)
            ->where('produk_id', $produk->id)
            ->first();

        if ($orderItem) {
            $orderItem->quantity += 1;
            $orderItem->save();
        } else {
            OrderItem::create([
                'order_id' => $order->id,
                'produk_id' => $produk->id,
                'quantity' => 1,
                'harga' => $produk->harga
            ]);
        }

        $order->total_harga += $produk->harga;
        $order->save();

        return redirect()->route('order.cart')->with('success', 'Produk ditambahkan ke keranjang');
    }

    public function viewCart()
    {
        $customer = Customer::where('user_id', Auth::id())->first();

        if (!$customer) {
            return back()->with('error', 'Customer tidak ditemukan');
        }

        $order = Order::where('customer_id', $customer->id)
            ->where('status', 'pending')
            ->first();

        if ($order) {
            $order->load('orderItems.produk');
        }

        return view('v_order.cart', compact('order'));
    }

    public function updateCart(Request $request, $id)
    {
        $customer = Customer::where('user_id', Auth::id())->first();

        $order = Order::where('customer_id', $customer->id)
            ->where('status', 'pending')
            ->first();

        if ($order) {
            $item = $order->orderItems()->where('id', $id)->first();

            if ($item) {
                if ($request->quantity > $item->produk->stok) {
                    return back()->with('error', 'Stok tidak cukup');
                }

                $order->total_harga -= ($item->harga * $item->quantity);

                $item->quantity = $request->quantity;
                $item->save();

                $order->total_harga += ($item->harga * $item->quantity);
                $order->save();
            }
        }

        return back()->with('success', 'Keranjang diperbarui');
    }

    public function removeFromCart($id)
    {
        $customer = Customer::where('user_id', Auth::id())->first();

        $order = Order::where('customer_id', $customer->id)
            ->where('status', 'pending')
            ->first();

        if ($order) {
            $item = OrderItem::where('order_id', $order->id)
                ->where('produk_id', $id)
                ->first();

            if ($item) {
                $order->total_harga -= ($item->harga * $item->quantity);
                $item->delete();

                if ($order->total_harga <= 0) {
                    $order->delete();
                } else {
                    $order->save();
                }
            }
        }

        session()->forget('shipping');

        return back()->with('success', 'Produk dihapus');
    }

    public function getProvinces()
    {
        $response = Http::withHeaders([
            'key' => env('RAJAONGKIR_API_KEY')
        ])->get(env('RAJAONGKIR_BASE_URL') . '/destination/province');

        return response()->json($response->json());
    }

    public function getCities(Request $request)
    {
        $response = Http::withHeaders([
            'key' => env('RAJAONGKIR_API_KEY')
        ])->get(env('RAJAONGKIR_BASE_URL') . '/destination/city/' . $request->province_id);

        return response()->json($response->json());
    }

    public function getCost(Request $request)
    {
        $response = Http::asForm()
            ->withHeaders([
                'key' => env('RAJAONGKIR_API_KEY')
            ])
            ->post(env('RAJAONGKIR_BASE_URL') . '/calculate/domestic-cost', [
                'origin' => 39,
                'destination' => $request->destination,
                'weight' => $request->weight,
                'courier' => $request->courier
            ]);

        return response()->json($response->json());
    }

    public function saveShipping(Request $request)
    {
        session([
            'shipping' => [
                'service' => $request->service,
                'cost' => $request->cost,
                'etd' => $request->etd
            ]
        ]);

        return response()->json([
            'success' => true
        ]);
    }

    public function checkout()
    {
        $customer = Customer::where('user_id', Auth::id())->first();

        if (!$customer) {
            return back()->with('error', 'Customer tidak ditemukan');
        }

        $order = Order::where('customer_id', $customer->id)
            ->where('status', 'pending')
            ->first();

        if (!$order) {
            return back()->with('error', 'Tidak ada pesanan');
        }

        $shipping = session('shipping');

        if (!$shipping) {
            return back()->with('error', 'Pilih pengiriman dulu');
        }

        $order->total_harga = $order->total_harga + $shipping['cost'];
        $order->status = 'proses';
        $order->save();

        session()->forget('shipping');

        return redirect()->route('order.history')
            ->with('success', 'Pesanan berhasil dibuat');
    }

    public function history()
    {
        $customer = Customer::where('user_id', Auth::id())->first();

        $orders = Order::where('customer_id', $customer->id)
            ->where('status', '!=', 'pending')
            ->latest()
            ->get();

        return view('v_order.history', compact('orders'));
    }
}
