<?php

namespace App\Http\Controllers;

use App\Models\Position;
use Illuminate\Http\{RedirectResponse, Request};
use Illuminate\View\View;

class PositionController extends Controller
{
    public function index(Request $request): View
    {
        $positions = Position::withCount('employees')
            ->when(
                $request->filled('search'),
                fn($q) =>
                $q->where('title', 'like', '%' . $request->search . '%')
            )
            ->when(
                $request->filled('level'),
                fn($q) =>
                $q->where('level', $request->level)
            )
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('positions.index', compact('positions'));
    }

    public function create(): View
    {
        $levels = $this->levels();

        return view('positions.create', compact('levels'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255|unique:positions,title',
            'level' => 'required|in:junior,mid,senior,lead,manager,executive',
        ]);

        Position::create($validated);

        return redirect()->route('positions.index')
            ->with('success', 'Position created successfully.');
    }

    public function show(Position $position): View
    {
        $position->load('employees.user');
        $position->loadCount('employees');

        return view('positions.show', compact('position'));
    }

    public function edit(Position $position): View
    {
        $levels = $this->levels();

        return view('positions.edit', compact('position', 'levels'));
    }

    public function update(Request $request, Position $position): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255|unique:positions,title,' . $position->id,
            'level' => 'required|in:junior,mid,senior,lead,manager,executive',
        ]);

        $position->update($validated);

        return redirect()->route('positions.show', $position)
            ->with('success', 'Position updated successfully.');
    }

    public function destroy(Position $position): RedirectResponse
    {
        if ($position->employees()->exists()) {
            return back()->with('error', 'Cannot delete a position that has employees assigned.');
        }

        $position->delete();

        return redirect()->route('positions.index')
            ->with('success', 'Position deleted.');
    }

    private function levels(): array
    {
        return [
            'junior'    => 'Junior',
            'mid'       => 'Mid-level',
            'senior'    => 'Senior',
            'lead'      => 'Lead',
            'manager'   => 'Manager',
            'executive' => 'Executive',
        ];
    }
}
