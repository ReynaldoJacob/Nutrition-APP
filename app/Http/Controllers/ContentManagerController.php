<?php

namespace App\Http\Controllers;

use App\Models\ContentItem;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ContentManagerController extends Controller
{
    public function index(Request $request)
    {
        if ($redirect = $this->proAccessRedirect($request)) {
            return $redirect;
        }

        $user = $request->user();

        $items = ContentItem::query()
            ->where('nutritionist_id', $user->id)
            ->latest('updated_at')
            ->get();

        $contentItems = $items->map(fn(ContentItem $item) => [
            'id' => $item->id,
            'title' => $item->title,
            'published_at' => $this->formatPublishedLabel($item),
            'category' => $item->type_label,
            'status' => $item->status_label,
            'reading_time_minutes' => $item->reading_time_minutes,
            'excerpt' => $item->excerpt,
            'image_url' => $item->cover_image_url,
        ])->values();

        return Inertia::render('ContentManager', [
            'stats' => [
                'total_items' => $items->count(),
                'published_items' => $items->where('status', 'published')->count(),
                'draft_items' => $items->where('status', 'draft')->count(),
                'scheduled_items' => $items->where('status', 'scheduled')->count(),
            ],
            'contentItems' => $contentItems,
        ]);
    }

    public function store(Request $request)
    {
        if ($redirect = $this->proAccessRedirect($request)) {
            return $redirect;
        }

        $user = $request->user();

        $data = $request->validate([
            'content_type' => ['required', 'in:recipe,article,news'],
            'reading_time_minutes' => ['required', 'integer', 'min:1', 'max:60'],
            'title' => ['required', 'string', 'max:180'],
            'body' => ['required', 'string', 'min:20'],
            'cover_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'status' => ['required', 'in:draft,published'],
        ]);

        $coverImagePath = $request->file('cover_image')
            ? $request->file('cover_image')->store('content-covers', 'public')
            : null;

        ContentItem::create([
            'nutritionist_id' => $user->id,
            'content_type' => $data['content_type'],
            'reading_time_minutes' => $data['reading_time_minutes'],
            'title' => $data['title'],
            'body' => trim($data['body']),
            'excerpt' => $this->buildExcerpt($data['body']),
            'cover_image_path' => $coverImagePath,
            'status' => $data['status'],
            'published_at' => $data['status'] === 'published' ? now() : null,
        ]);

        return redirect()
            ->route('content-manager.index')
            ->with('success', $data['status'] === 'published' ? 'Contenido publicado correctamente.' : 'Borrador guardado correctamente.');
    }

    private function proAccessRedirect(Request $request)
    {
        $user = $request->user();
        $profile = $user?->nutritionistProfile;
        $planKey = $profile?->effectivePlanKey() ?? 'free';

        abort_unless($user && $user->role_key === 'nutritionist', 403);

        if ($planKey !== 'pro') {
            return redirect()
                ->route('dashboard')
                ->with('warning', 'El Gestor de Contenido esta disponible solo para usuarios con plan Pro.');
        }

        return null;
    }

    private function buildExcerpt(string $body): string
    {
        return str($body)
            ->replace(["\r", "\n"], ' ')
            ->squish()
            ->limit(140)
            ->value();
    }

    private function formatPublishedLabel(ContentItem $item): string
    {
        if ($item->status === 'published' && $item->published_at) {
            return 'Publicado el ' . $item->published_at->translatedFormat('d \d\e F, Y');
        }

        if ($item->status === 'scheduled' && $item->published_at) {
            return 'Programado para el ' . $item->published_at->translatedFormat('d \d\e F, Y');
        }

        return 'Última edición ' . $item->updated_at->diffForHumans();
    }
}
