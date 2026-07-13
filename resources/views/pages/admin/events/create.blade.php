@extends('layouts.admin_layouts')

@section('title', 'Tambah Event')

@section('content')

<div class="mb-6">
    <h2 class="text-3xl font-bold">
        Tambah Event
    </h2>

    <p class="text-gray-500 mt-1">
        Tambahkan event baru beserta tiketnya.
    </p>
</div>

<form
    action="{{ route('admin.events.store') }}"
    method="POST"
    enctype="multipart/form-data"
    class="space-y-6">

    @csrf

    <div class="bg-white rounded-lg shadow p-6">
        <div class="mb-5">

        <label class="font-semibold">
            Judul Event
        </label>

        <input
            type="text"
            name="judul"
            value="{{ old('judul') }}"
            class="input input-bordered w-full mt-2">

        @error('judul')
            <p class="text-red-500 text-sm mt-1">
                {{ $message }}
            </p>
        @enderror

    </div>

    <div class="mb-5">

    <label class="font-semibold">
        Deskripsi
    </label>

    <textarea
        name="deskripsi"
        rows="5"
        class="textarea textarea-bordered w-full mt-2">{{ old('deskripsi') }}</textarea>

    @error('deskripsi')
        <p class="text-red-500 text-sm mt-1">
            {{ $message }}
        </p>
    @enderror

</div>

<div class="mb-5">

    <label class="font-semibold">
        Lokasi
    </label>

    <input
        type="text"
        name="lokasi"
        value="{{ old('lokasi') }}"
        class="input input-bordered w-full mt-2">

    @error('lokasi')
        <p class="text-red-500 text-sm mt-1">
            {{ $message }}
        </p>
    @enderror

</div>

<div class="mb-5">

    <label class="font-semibold">
        Kategori
    </label>

    <select
        name="kategori_id"
        class="select select-bordered w-full mt-2">

        <option value="">-- Pilih Kategori --</option>

        @foreach($kategoris as $kategori)

            <option
                value="{{ $kategori->id }}"
                {{ old('kategori_id') == $kategori->id ? 'selected' : '' }}>

                {{ $kategori->nama }}

            </option>

        @endforeach

    </select>

    @error('kategori_id')
        <p class="text-red-500 text-sm mt-1">
            {{ $message }}
        </p>
    @enderror

</div>

<div class="mb-5">

    <label class="font-semibold">
        Tanggal & Waktu
    </label>

    <input
        type="datetime-local"
        name="tanggal_waktu"
        value="{{ old('tanggal_waktu') }}"
        class="input input-bordered w-full mt-2">

    @error('tanggal_waktu')
        <p class="text-red-500 text-sm mt-1">
            {{ $message }}
        </p>
    @enderror

</div>

<div class="mb-5">

    <label class="font-semibold">
        Gambar Event
    </label>

    <input
        type="file"
        name="gambar"
        class="file-input file-input-bordered w-full mt-2">

    @error('gambar')
        <p class="text-red-500 text-sm mt-1">
            {{ $message }}
        </p>
    @enderror

</div>
<hr class="my-8">

<h3 class="text-xl font-bold mb-4">
    Data Tiket
</h3>

<div class="border rounded-lg p-5 mb-5">

    <h4 class="font-semibold mb-4">
        Tiket Reguler
    </h4>

    <input type="hidden"
           name="tikets[0][tipe]"
           value="reguler">

    <div class="grid grid-cols-2 gap-4">

        <div>
            <label>Harga</label>

            <input
                type="number"
                name="tikets[0][harga]"
                value="{{ old('tikets.0.harga') }}"
                class="input input-bordered w-full">

                @error('tikets.0.harga')
                    <p class="text-red-500 text-sm mt-1">
                        {{ $message }}
                    </p>
                @enderror
        </div>

        <div>
            <label>Stok</label>

            <input
                type="number"
                name="tikets[0][stok]"
                value="{{ old('tikets.0.stok') }}"
                class="input input-bordered w-full">

                @error('tikets.0.stok')
                    <p class="text-red-500 text-sm mt-1">
                        {{ $message }}
                    </p>
                @enderror
        </div>

    </div>

</div>

<div class="border rounded-lg p-5 mb-5">

    <h4 class="font-semibold mb-4">
        Tiket Premium
    </h4>

    <input type="hidden"
           name="tikets[1][tipe]"
           value="premium">

    <div class="grid grid-cols-2 gap-4">

        <div>
            <label>Harga</label>

            <input
                type="number"
                name="tikets[1][harga]"
                value="{{ old('tikets.1.harga') }}"
                class="input input-bordered w-full">

                @error('tikets.1.harga')
    <p class="text-red-500 text-sm mt-1">
        {{ $message }}
    </p>
@enderror
        </div>

        <div>
            <label>Stok</label>

            <input
                type="number"
                name="tikets[1][stok]"
                value="{{ old('tikets.1.stok') }}"
                class="input input-bordered w-full">

                @error('tikets.1.stok')
    <p class="text-red-500 text-sm mt-1">
        {{ $message }}
    </p>
@enderror
        </div>

    </div>

</div>



    </div>
<div class="flex justify-end gap-3">

    <a href="{{ route('admin.events.index') }}"
       class="btn btn-outline">
        Kembali
    </a>

    <button
        type="submit"
        class="btn btn-primary">
        Simpan Event
    </button>

</div>
</form>

@endsection