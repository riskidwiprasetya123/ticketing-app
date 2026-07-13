<?php

namespace App\Http\Controllers;

use App\Http\Requests\EventFormRequest;
use App\Models\Event;
use App\Models\Kategori;
use App\Models\Tiket;
use App\Models\EventStatusHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class EventController extends Controller
{
    /**
     * Display the specified event.
     */

    public function index(Request $request)
    {
        $events = Event::with(['kategori', 'tikets']);

        // Filter kategori
        if ($request->filled('kategori_id')) {
            $events->where('kategori_id', $request->kategori_id);
        }

        // Search judul atau lokasi
        if ($request->filled('search')) {
            $events->where(function ($query) use ($request) {
                $query->where('judul', 'like', '%' . $request->search . '%')
                    ->orWhere('lokasi', 'like', '%' . $request->search . '%');
            });
        }

        // Sorting
        $sort = $request->get('sort', 'asc');
        $events->orderBy('tanggal_waktu', $sort);

        // Pagination
        $events = $events->paginate(10);

        // Ambil kategori untuk dropdown filter
        $kategoris = Kategori::all();

        return view('pages.admin.events.index', compact('events', 'kategoris'));
    }

    public function create()
    {
        $kategoris = Kategori::all();

        return view('pages.admin.events.create', compact('kategoris'));
    }

    public function store(EventFormRequest $request)
    {
        // Handle upload gambar
        if ($request->hasFile('gambar')) {
            $gambar = $request->file('gambar')->store('events', 'public');
        } else {
            $gambar = 'konser.jpg';
        }

        // Simpan event
        $event = Event::create([
            'user_id' => Auth::id(),
            'kategori_id' => $request->kategori_id,
            'judul' => $request->judul,
            'deskripsi' => $request->deskripsi,
            'lokasi' => $request->lokasi,
            'gambar' => $gambar,
            'tanggal_waktu' => $request->tanggal_waktu,
        ]);

        // Simpan tiket
        foreach ($request->tikets as $tiket) {
            $event->tikets()->create([
                'tipe' => $tiket['tipe'],
                'harga' => $tiket['harga'],
                'stok' => $tiket['stok'],
            ]);
        }

        return redirect()
            ->route('admin.events.index')
            ->with('success', 'Event berhasil ditambahkan.');
    }

    public function edit(Event $event)
    {
        $event->load('tikets', 'statusHistories');

        $currentStatus = $event->status;

        $lastHistory = $event->statusHistories()
            ->latest('changed_at')
            ->first();

        if (!$lastHistory) {

            EventStatusHistory::create([
                'event_id'   => $event->id,
                'old_status' => $currentStatus,
                'new_status' => $currentStatus,
                'changed_at' => now(),
            ]);

        } elseif ($lastHistory->new_status != $currentStatus) {

            EventStatusHistory::create([
                'event_id'   => $event->id,
                'old_status' => $lastHistory->new_status,
                'new_status' => $currentStatus,
                'changed_at' => now(),
            ]);
        }

        // reload supaya history terbaru ikut terbaca
        $event->load('statusHistories');

        $kategoris = Kategori::all();

        $hasSales = $event->hasSales();

        return view('pages.admin.events.edit', compact(
            'event',
            'kategoris',
            'hasSales'
        ));
    }

    public function update(EventFormRequest $request, Event $event)
    {
        // Cek apakah event sudah memiliki penjualan
        if ($event->hasSales() && $request->tanggal_waktu != $event->tanggal_waktu->format('Y-m-d\TH:i')) {
            return back()
            ->withErrors([
                'tanggal_waktu' => 'Tanggal dan waktu tidak dapat diubah karena event sudah memiliki penjualan.'
            ])
            ->withInput();
        }

        // Handle upload gambar
        if ($request->hasFile('gambar')) {

            // Hapus gambar lama jika bukan gambar default
            if ($event->gambar !== 'konser.jpg' && Storage::disk('public')->exists($event->gambar)) {
                Storage::disk('public')->delete($event->gambar);
            }

            $gambar = $request->file('gambar')->store('events', 'public');

        } else {
            $gambar = $event->gambar;
        }
    

        // Update event
        $event->update([
            'kategori_id' => $request->kategori_id,
            'judul' => $request->judul,
            'deskripsi' => $request->deskripsi,
            'lokasi' => $request->lokasi,
            'gambar' => $gambar,
            'tanggal_waktu' => $request->tanggal_waktu,
        ]);

        // ID tiket yang dikirim dari form
        $ticketIds = [];

        // Update atau tambah tiket
        foreach ($request->tikets as $tiketData) {

            if (!empty($tiketData['id'])) {

                $tiket = Tiket::find($tiketData['id']);

                if ($tiket) {

                    $tiket->update([
                        'tipe' => $tiketData['tipe'],
                        'harga' => $tiketData['harga'],
                        'stok' => $tiketData['stok'],
                    ]);

                    $ticketIds[] = $tiket->id;
                }

            } else {

                $tiket = $event->tikets()->create([
                    'tipe' => $tiketData['tipe'],
                    'harga' => $tiketData['harga'],
                    'stok' => $tiketData['stok'],
                ]);

                $ticketIds[] = $tiket->id;
            }
        }

        // Hapus tiket yang dihilangkan dari form
        if (!$event->hasSales()) {
            $event->tikets()
                ->whereNotIn('id', $ticketIds)
                ->delete();
        }

            return redirect()
                ->route('admin.events.index')
                ->with('success', 'Event berhasil diperbarui.');
        }

    public function destroy(Event $event)
    {
        // Cek apakah event sudah memiliki penjualan
        if ($event->hasSales()) {
            return redirect()
                ->route('admin.events.index')
                ->with('error', 'Event tidak dapat dihapus karena sudah memiliki penjualan.');
        }

        // Hapus gambar jika bukan gambar default
        if (
            $event->gambar !== 'konser.jpg' &&
            Storage::disk('public')->exists($event->gambar)
        ) {
            Storage::disk('public')->delete($event->gambar);
        }

        // Hapus event
        $event->delete();

        return redirect()
            ->route('admin.events.index')
            ->with('success', 'Event berhasil dihapus.');
    }

    public function show(Event $event)
    {
        $event->load(['kategori', 'tikets']);

        $relatedEvents = Event::with('kategori')
            ->where('kategori_id', $event->kategori_id)
            ->where('id', '!=', $event->id)
            ->upcoming()
            ->take(4)
            ->get();

        return view('events.show', [
            'event' => $event,
            'relatedEvents' => $relatedEvents,
        ]);
    }

}