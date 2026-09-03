<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreIncidentRequest;
use App\Http\Requests\UpdateIncidentRequest;
use App\Models\Category;
use App\Models\Incident;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class IncidentController extends Controller
{
    use AuthorizesRequests;

    /**
     * عرض قائمة البلاغات.
     */
    public function index(): View
    {
        $this->authorize('viewAny', Incident::class);

        $incidents = Incident::with(['category', 'user'])
            ->latest()
            ->paginate(10);

        return view('incidents.index', compact('incidents'));
    }

    /**
     * عرض نموذج إنشاء بلاغ.
     */
    public function create(): View
    {
        $this->authorize('create', Incident::class);

        $categories = Category::orderBy('name')->get();

        return view('incidents.create', compact('categories'));
    }

    /**
     * حفظ بلاغ جديد.
     */
    public function store(StoreIncidentRequest $request): RedirectResponse
    {
        $this->authorize('create', Incident::class);

        $validated = $request->validated();

        $incident = Incident::create([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'latitude' => $validated['latitude'] ?? null,
            'longitude' => $validated['longitude'] ?? null,
            'category_id' => $validated['category_id'],
            'priority' => $validated['priority'] ?? 'Moyenne',
            'user_id' => auth()->id(),
            'status' => 'En attente',
        ]);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('incidents', 'public');

            $incident->images()->create([
                'image_path' => $path,
            ]);
        }

        return redirect()
            ->route('incidents.show', $incident)
            ->with('success', 'Incident signalé avec succès !');
    }

    /**
     * عرض تفاصيل البلاغ.
     */
    public function show(Incident $incident): View
    {
        $this->authorize('view', $incident);

        $incident->load([
            'category',
            'user',
            'images',
            'comments.user',
            'affectations.technicien',
        ]);

        // جلب التقنيين فقط
        $techniciens = User::whereHas('roles', function ($query) {
            $query->where('name', 'technicien');
        })->orderBy('name')->get();

        return view('incidents.show', compact('incident', 'techniciens'));
    }

    /**
     * عرض نموذج تعديل البلاغ.
     */
    public function edit(Incident $incident): View
    {
        $this->authorize('update', $incident);

        $categories = Category::orderBy('name')->get();

        return view('incidents.edit', compact('incident', 'categories'));
    }

    /**
     * تحديث البلاغ وتعديل الصورة.
     */
    public function update(
        UpdateIncidentRequest $request,
        Incident $incident
    ): RedirectResponse {
        $this->authorize('update', $incident);

        $incident->update($request->validated());

        // معالجة رفع/تحديث الصورة
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('incidents', 'public');

            // حذف الصورة القديمة من الـ Storage والـ Database إن وجدت
            if ($incident->images->isNotEmpty()) {
                foreach ($incident->images as $oldImage) {
                    Storage::disk('public')->delete($oldImage->image_path);
                    $oldImage->delete();
                }
            }

            // إنشاء سجل الصورة الجديدة
            $incident->images()->create([
                'image_path' => $path,
            ]);
        }

        return redirect()
            ->route('incidents.show', $incident)
            ->with('success', 'Incident modifié avec succès !');
    }

    /**
     * حذف البلاغ.
     */
    public function destroy(Incident $incident): RedirectResponse
    {
        $this->authorize('delete', $incident);

        $incident->delete();

        return redirect()
            ->route('incidents.index')
            ->with('success', 'Incident supprimé avec succès !');
    }
}