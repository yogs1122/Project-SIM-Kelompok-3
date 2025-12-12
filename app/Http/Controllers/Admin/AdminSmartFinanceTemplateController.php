<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SmartFinanceTemplate;
use Illuminate\Http\Request;

class AdminSmartFinanceTemplateController extends Controller
{
    public function index()
    {
        $templates = SmartFinanceTemplate::orderByDesc('updated_at')->paginate(20);
        return view('admin.smart_finance.templates.index', compact('templates'));
    }

    public function create()
    {
        return view('admin.smart_finance.templates.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'nullable|string|max:100',
            'body' => 'required|string|max:2000',
        ]);
        $data['created_by'] = auth()->id();
        SmartFinanceTemplate::create($data);
        return redirect()->route('admin.smartfinance.templates.index')->with('success', 'Template created');
    }

    public function edit(SmartFinanceTemplate $template)
    {
        return view('admin.smart_finance.templates.edit', compact('template'));
    }

    public function update(Request $request, SmartFinanceTemplate $template)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'nullable|string|max:100',
            'body' => 'required|string|max:2000',
        ]);
        $template->update($data);
        return redirect()->route('admin.smartfinance.templates.index')->with('success', 'Template updated');
    }

    public function destroy(SmartFinanceTemplate $template)
    {
        $template->delete();
        return back()->with('success', 'Template deleted');
    }
}
