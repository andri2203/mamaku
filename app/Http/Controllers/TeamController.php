<?php

namespace App\Http\Controllers;

use App\Models\Team;
use Illuminate\Http\Request;

class TeamController extends Controller
{
    public function index()
    {
        // get Team's Data with total users in each team
        $teams = Team::withCount('users')->get();

        $data = [
            'title' => 'Kelola Team',
            'teams' => $teams,
        ];

        return view('teams.index', $data);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:teams,name',
        ]);

        $team = Team::create($validated);

        return redirect()->route('team.index')->with('success', 'Team : ' . $team->name . ' berhasil dibuat.');
    }

    public function update(Request $request, $id)
    {
        $team = Team::findOrFail($id);

        if (!$team) {
            return redirect()->route('team.index')->with('error', 'Team tidak ditemukan.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:teams,name,' . $team->id,
        ]);

        $team->update($validated);

        return redirect()->route('team.index')->with('success', 'Team : ' . $team->name . ' berhasil diupdate.');
    }

    public function destroy($id)
    {
        $team = Team::with('users')->findOrFail($id);

        if (!$team) {
            return redirect()->route('team.index')->with('error', 'Team tidak ditemukan.');
        }

        if ($team->users()->count() > 0) {
            return redirect()->route('team.index')->with('error', 'Team : ' . $team->name . ' tidak dapat dihapus karena sudah memiliki anggota.');
        }

        $teamName = $team->name;
        $team->delete();

        return redirect()->route('team.index')->with('success', 'Team : ' . $teamName . ' berhasil dihapus.');
    }
}
