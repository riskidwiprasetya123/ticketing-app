<x-app-layout>

<div class="max-w-6xl mx-auto py-10 px-6">

    <h1 class="text-3xl font-bold mb-8">
        Pesanan Saya
    </h1>

    @forelse($orders as $order)

        <div class="card bg-base-100 shadow mb-6">

            <div class="card-body">

                <h2 class="text-xl font-bold">
                    {{ $order->event->judul }}
                </h2>

                <p>
                    Tanggal Order :
                    {{ $order->order_date }}
                </p>

                <p class="mb-4">
                    Total :
                    <span class="font-bold text-primary">
                        Rp {{ number_format($order->total_harga,0,',','.') }}
                    </span>
                </p>

                <table class="table">

                    <thead>

                        <tr>

                            <th>Tiket</th>
                            <th>Jumlah</th>
                            <th>Subtotal</th>

                        </tr>

                    </thead>

                    <tbody>

                        @foreach($order->detailOrders as $detail)

                            <tr>

                                <td>
                                    {{ ucfirst($detail->tiket->tipe) }}
                                </td>

                                <td>
                                    {{ $detail->jumlah }}
                                </td>

                                <td>
                                    Rp {{ number_format($detail->subtotal_harga,0,',','.') }}
                                </td>

                            </tr>

                        @endforeach

                    </tbody>

                </table>

            </div>

        </div>

    @empty

        <div class="alert">
            Belum ada pesanan.
        </div>

    @endforelse

</div>

</x-app-layout>