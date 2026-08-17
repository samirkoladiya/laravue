<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AdminFaqRequest;
use App\Models\Admin\AdminFaqModel;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AdminFaqController extends Controller
{
    public function index(Request $request): Response
    {
        $search = trim((string) $request->string('search'));

        $faqs = AdminFaqModel::query()
            ->when($search !== '', fn ($query) => $query->where('question', 'like', "%{$search}%"))
            ->orderBy('sort_order')
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString()
            ->through(fn (AdminFaqModel $faq) => [
                'id' => $faq->id,
                'question' => $faq->question,
                'answer' => $faq->answer,
                'sort_order' => $faq->sort_order,
                'status' => $faq->status,
            ]);

        return Inertia::render('Admin/Faq/Index', [
            'faqs' => $faqs,
            'filters' => ['search' => $search],
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Faq/Create');
    }

    public function store(AdminFaqRequest $request): RedirectResponse
    {
        if ($request->isSpam()) {
            return redirect()->route('admin.faq.index')->with('success', 'FAQ added successfully.');
        }

        AdminFaqModel::create($request->safe()->except('website'));

        return redirect()->route('admin.faq.index')->with('success', 'FAQ added successfully.');
    }

    public function edit(AdminFaqModel $faq): Response
    {
        return Inertia::render('Admin/Faq/Edit', [
            'faq' => [
                'id' => $faq->id,
                'question' => $faq->question,
                'answer' => $faq->answer,
                'sort_order' => $faq->sort_order,
                'status' => $faq->status,
            ],
        ]);
    }

    public function update(AdminFaqRequest $request, AdminFaqModel $faq): RedirectResponse
    {
        if ($request->isSpam()) {
            return redirect()->route('admin.faq.index')->with('success', 'FAQ updated successfully.');
        }

        $faq->update($request->safe()->except('website'));

        return redirect()->route('admin.faq.index')->with('success', 'FAQ updated successfully.');
    }

    public function destroy(AdminFaqModel $faq): RedirectResponse
    {
        $faq->delete();

        return back()->with('success', 'FAQ deleted successfully.');
    }
}
