<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Table;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class MenuController extends Controller
{
    public function index()
    {
        $menus = Menu::where('is_available', true)
                    ->orderBy('category', 'asc')
                    ->get();

        $groupedMenus = $menus->groupBy('category');

        return view('home', compact('groupedMenus'));
    }

    public function getMenuStocks()
    {
        $menus = Menu::select('id', 'stock', 'is_available')->get();
        return response()->json(['success' => true, 'data' => $menus]);
    }

    // UPDATE: Tambahkan Request $request
    public function addToCart(Request $request, $id)
    {
        $menu = Menu::findOrFail($id);
        $cart = session()->get('cart', []);

        $currentQtyInCart = isset($cart[$id]) ? $cart[$id]['quantity'] : 0;

        // Cek Stok
        if (($currentQtyInCart + 1) > $menu->stock) {
            $msg = 'Maaf, stok tidak cukup! Sisa stok cuma ' . $menu->stock;
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $msg]);
            }
            return redirect()->back()->with('error', $msg);
        }

        if(isset($cart[$id])) {
            $cart[$id]['quantity']++;
        } else {
            $cart[$id] = [
                "name" => $menu->name,
                "quantity" => 1,
                "price" => $menu->price,
                "image" => $menu->image_path
            ];
        }

        session()->put('cart', $cart);
        session()->save(); // <--- PENTING: Paksa simpan session biar gak nge-bug

        // Hitung Total
        $totalQty = 0;
        $totalPrice = 0;
        foreach($cart as $item) { 
            $totalQty += $item['quantity']; 
            $totalPrice += $item['price'] * $item['quantity'];
        }

        if ($request->ajax()) {
            return response()->json([
                'success' => true, 
                'message' => 'Berhasil masuk keranjang!',
                'total_qty' => $totalQty,
                'cart' => $cart,
                'total_price' => $totalPrice
            ]);
        }

        return redirect()->back()->with('success', 'Menu berhasil ditambahkan!');
    }

    public function clearCart()
    {
        session()->forget('cart');
        return redirect()->back();
    }

    public function removeFromCart($id)
    {
        $cart = session()->get('cart');
        if(isset($cart[$id])) {
            unset($cart[$id]);
            session()->put('cart', $cart);
        }
        return redirect()->back()->with('success', 'Item berhasil dihapus!');
    }

    public function viewCart()
    {
        $tables = Table::where('status', 'available')->get();
        return view('cart', compact('tables'));
    }

    public function checkout(Request $request)
    {
        // ... (Kode Checkout Tetap Sama, Tidak Perlu Diubah) ...
        // Agar tidak kepanjangan, saya persingkat di sini karena fokus kita di addToCart
        // Gunakan kode checkout terakhir yang sudah ada validasi meja & stok
        
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'customer_name' => 'required|string|max:255',
            'order_type' => 'required|in:dine_in,take_away,booking',
            'customer_phone' => 'required_if:order_type,booking|nullable|string|max:15',
            'booking_time' => 'required_if:order_type,booking',
            'table_id' => 'required_if:order_type,dine_in',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput()->with('error', 'Mohon lengkapi data pesanan!'); 
        }

        $cart = session('cart');
        if(!$cart) return redirect()->back()->with('error', 'Keranjang kosong!');

        $totalAmount = 0;
        foreach($cart as $details) { $totalAmount += $details['price'] * $details['quantity']; }

        DB::beginTransaction();
        try {
            if ($request->order_type == 'dine_in') {
                $table = \App\Models\Table::lockForUpdate()->find($request->table_id); 
                if (!$table || $table->status != 'available') {
                    DB::rollBack();
                    return redirect()->route('view_cart')->withInput()->with('error', "Meja tidak tersedia.");
                }
                $table->update(['status' => 'occupied']);
            }

            foreach($cart as $id => $details) {
                $menu = Menu::lockForUpdate()->find($id);
                if(!$menu || $menu->stock < $details['quantity']) {
                    DB::rollBack();
                    return redirect()->route('view_cart')->with('error', 'Stok ' . $menu->name . ' habis!');
                }
            }

            $isStaff = Auth::check() && in_array(Auth::user()->role, ['cashier', 'admin', 'owner']);
            $initialStatus = $isStaff ? 'confirmed' : 'pending';

            $order = Order::create([
                'guest_token' => session()->getId(),
                'customer_name' => $request->customer_name,
                'customer_phone' => $request->customer_phone,
                'table_id' => $request->table_id,
                'total_amount' => $totalAmount,
                'order_type' => $request->order_type,
                'status' => $initialStatus,
                'booking_time' => $request->booking_time,
                'cashier_id' => $isStaff ? Auth::id() : null,
            ]);

            foreach($cart as $id => $details) {
                $menu = Menu::find($id);
                OrderItem::create([
                    'order_id' => $order->id,
                    'menu_id' => $id,
                    'quantity' => $details['quantity'],
                    'price_at_time' => $details['price'],
                    'subtotal' => $details['price'] * $details['quantity']
                ]);
                $menu->decrement('stock', $details['quantity']);
            }

            DB::commit();
            session()->forget('cart');

            if ($isStaff) return redirect()->route('dashboard')->with('success', 'Pesanan Walk-in Berhasil!');
            return redirect()->route('home')->with('success', 'Pesanan Berhasil!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('view_cart')->with('error', 'Error: ' . $e->getMessage());
        }
    }

     // FUNGSI BARU: Kurangi Item (-1)
    public function decreaseItem(Request $request, $id)
    {
        $cart = session()->get('cart');

        if(isset($cart[$id])) {
            // Jika jumlah lebih dari 1, kurangi
            if($cart[$id]['quantity'] > 1) {
                $cart[$id]['quantity']--;
            } else {
                // Jika sisa 1 dan dikurangi, maka hapus item
                unset($cart[$id]);
            }
            
            session()->put('cart', $cart);
            session()->save();
        }

        // Hitung ulang total untuk respon AJAX
        $totalQty = 0;
        $totalPrice = 0;
        if(session('cart')) {
            foreach(session('cart') as $item) { 
                $totalQty += $item['quantity']; 
                $totalPrice += $item['price'] * $item['quantity'];
            }
        }

        if ($request->ajax()) {
            return response()->json([
                'success' => true, 
                'message' => 'Item dikurangi!',
                'total_qty' => $totalQty,
                'cart' => session('cart'), // Kirim sisa keranjang
                'total_price' => $totalPrice
            ]);
        }

        return redirect()->back();
    }
}