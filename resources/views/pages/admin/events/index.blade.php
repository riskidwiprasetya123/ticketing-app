@extends('layouts.admin_layouts')

@section('title', 'Manajemen Event')

@section('content')

<div class="flex justify-between items-center mb-6">
    <h2 class="text-3xl font-bold text-gray-800">
        Manajemen Event
    </h2>

    <a href="{{ route('admin.events.create') }}"
       class="btn btn-primary">
        Tambah Event
    </a>
</div>

<form method="GET"
      action="{{ route('admin.events.index') }}"
      class="bg-white rounded-lg shadow p-4 mb-6">

    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">

        <!-- Search -->
        <input
            type="text"
            name="search"
            value="{{ request('search') }}"
            placeholder="Cari judul atau lokasi..."
            class="input input-bordered w-full">

        <!-- Filter Kategori -->
        <select name="kategori_id"
                class="select select-bordered w-full">

            <option value="">Semua Kategori</option>

            @foreach($kategoris as $kategori)
                <option value="{{ $kategori->id }}"
                    {{ request('kategori_id') == $kategori->id ? 'selected' : '' }}>
                    {{ $kategori->nama }}
                </option>
            @endforeach

        </select>

        <!-- Sorting -->
        <select name="sort"
                class="select select-bordered w-full">

            <option value="asc"
                {{ request('sort') == 'asc' ? 'selected' : '' }}>
                Terlama
            </option>

            <option value="desc"
                {{ request('sort') == 'desc' ? 'selected' : '' }}>
                Terbaru
            </option>

        </select>

        <!-- Tombol -->
        <button class="btn btn-primary w-full">
            Cari
        </button>

    </div>

</form>

<div class="bg-white rounded-lg shadow overflow-hidden">

    <table class="table w-full">

        <thead class="bg-gray-100">
            <tr>
                <th>Event</th>
                <th>Kategori</th>
                <th>Tanggal</th>
                <th>Status</th>
                <th>Tiket</th>
                <th>Aksi</th>
            </tr>
        </thead>

        <tbody>

        @forelse($events as $event)

            <tr>

                <td>
                    <div class="flex items-center gap-3">

                        <img
                            src="{{ $event->image_url }}"
                            class="w-20 h-16 rounded object-cover">

                        <div>
                            <div class="font-bold">
                                {{ $event->judul }}
                            </div>

                            <div class="text-sm text-gray-500">
                                {{ $event->lokasi }}
                            </div>
                        </div>

                    </div>
                </td>

                <td>
                    {{ $event->kategori->nama }}
                </td>

                <td>
                    {{ $event->tanggal_waktu->format('d M Y H:i') }}
                </td>

                <td>

                    @if($event->status == 'Upcoming')

                        <span class="badge badge-info">
                            Upcoming
                        </span>

                    @elseif($event->status == 'Ongoing')

                        <span class="badge badge-success">
                            Ongoing
                        </span>

                    @else

                        <span class="badge badge-neutral">
                            Completed
                        </span>

                    @endif

                </td>

                <td>

                    @foreach($event->tikets as $tiket)

                        <div class="text-sm">
                            {{ ucfirst($tiket->tipe) }}
                            -
                            Rp {{ number_format($tiket->harga,0,',','.') }}
                        </div>

                    @endforeach

                </td>

                <td>

                    <div class="flex gap-2">

                        <a
                            href="{{ route('admin.events.edit',$event) }}"
                            class="btn btn-warning btn-sm">
                            Edit
                        </a>

                        <form
                            action="{{ route('admin.events.destroy',$event) }}"
                            method="POST">

                            @csrf
                            @method('DELETE')

                            <button
                                onclick="return confirm('Yakin ingin menghapus event ini?')"
                                class="btn btn-error btn-sm">

                                Hapus

                            </button>

                        </form>

                    </div>

                </td>

            </tr>

        @empty

            <tr>

                <td colspan="6" class="text-center py-8">
                    Belum ada event.
                </td>

            </tr>

        @endforelse

        </tbody>

    </table>

</div>

<div class="mt-6">
    {{ $events->links() }}
</div>

@endsection