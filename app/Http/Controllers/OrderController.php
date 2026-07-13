<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\DetailOrder;
use App\Models\Event;
use App\Models\Tiket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{

    public function index()
    {
        $orders = Order::with(['event', 'detailOrders.tiket'])
            ->where('user_id', Auth::id())
            ->latest()
            ->get();

        return view('orders.index', compact('orders'));
    }
    
   public function checkout(Event $event)
    {
        $event->load('tikets');

        return view('orders.checkout', compact('event'));
    }

    public function store(Request $request, Event $event)
    {
        DB::beginTransaction();

        try {

            $total = 0;

            // Buat Order
            $order = Order::create([
                'user_id'      => Auth::id(),
                'event_id'     => $event->id,
                'order_date'   => now(),
                'total_harga'  => 0,
            ]);

            foreach ($request->jumlah as $tiketId => $jumlah) {

                if ($jumlah <= 0) {
                    continue;
                }

                $tiket = Tiket::findOrFail($tiketId);

                // cek stok
                if ($jumlah > $tiket->stok) {
                    return back()->with('error', 'Stok tiket tidak mencukupi.');
                }

                $subtotal = $tiket->harga * $jumlah;

                DetailOrder::create([
                    'order_id'        => $order->id,
                    'tiket_id'        => $tiket->id,
                    'jumlah'          => $jumlah,
                    'subtotal_harga'  => $subtotal,
                ]);

                // kurangi stok
                $tiket->decrement('stok', $jumlah);

                $total += $subtotal;
            }

            $order->update([
                'total_harga' => $total
            ]);

            DB::commit();

            return redirect()
                ->route('home')
                ->with('success', 'Pesanan berhasil dibuat.');

        } catch (\Exception $e) {

            DB::rollBack();

            return back()->with('error', $e->getMessage());
        }
    }
}


