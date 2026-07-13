<x-app-layout>

    <div class="max-w-5xl mx-auto py-10 px-6">

        <h1 class="text-3xl font-bold mb-8">
            Checkout Tiket
        </h1>

        <div class="card bg-base-100 shadow-xl">

            <div class="card-body">

                <h2 class="text-2xl font-bold">
                    {{ $event->judul }}
                </h2>

                <p class="text-gray-500">
                    {{ $event->lokasi }}
                </p>

                <p class="mb-6">
                    {{ $event->tanggal_waktu->format('d M Y H:i') }}
                </p>

                <form
                    action="{{ route('orders.store', $event) }}"
                    method="POST">

                    @csrf

                    @foreach($event->tikets as $tiket)

                        <div class="border rounded-lg p-5 mb-5">

                            <div class="flex justify-between">

                                <div>

                                    <h3 class="font-bold">
                                        Tiket {{ ucfirst($tiket->tipe) }}
                                    </h3>

                                    <p>
                                        Rp {{ number_format($tiket->harga,0,',','.') }}
                                    </p>

                                </div>

                                <div>

                                    <input
                                        type="number"
                                        name="jumlah[{{ $tiket->id }}]"
                                        min="0"
                                        max="{{ $tiket->stok }}"
                                        value="0"
                                        class="input input-bordered w-24">

                                </div>

                            </div>

                        </div>

                    @endforeach

                    <button
                        type="submit"
                        class="btn btn-primary w-full">

                        Pesan Sekarang

                    </button>

                </form>

            </div>

        </div>

    </div>

</x-app-layout>