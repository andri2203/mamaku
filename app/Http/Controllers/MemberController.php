<?php

namespace App\Http\Controllers;

use App\Models\Member;
use Illuminate\Http\Request;

class MemberController extends Controller
{
    // define function to show Member data
    public function index()
    {
        // get all member data order by name asc
        $members = Member::orderBy('name', 'asc')->get();

        // define variable for view
        $data = [
            'title' => 'Member',
            'members' => $members,
        ];

        return view('member.index', $data);
    }

    // define function to show form create member
    public function create()
    {
        // define variable for view
        $data = [
            'title' => 'Tambah Member',
        ];

        return view('member.create', $data);
    }

    // define function to store member data
    public function store(Request $request)
    {
        // Validate request data
        $validated = $request->validate([
            'name' => 'required|max:255',
            'address' => 'required|max:255',
            'phone' => 'required|max:255',
        ]);

        try {
            Member::create([
                'name' => $validated['name'],
                'address' => $validated['address'],
                'phone' => $validated['phone'],
            ]);

            return redirect()->route('member.index')->with('success', 'Berhasil menambahkan ' . $validated['name'] . ' sebagai member.');
        } catch (\Throwable $th) {
            return back()->with('error', 'Terjadi Kesalahan: ' . $th->getMessage());
        }
    }

    // define function to show form create member
    public function edit($id)
    {
        // get member data by id
        $member = Member::findOrFail($id);

        // check member data is find or fail
        if (!$member) {
            return redirect()->route('member.index')->with('error', 'Data member tidak ditemukan');
        }

        // define variable for view
        $data = [
            'title' => 'Tambah Member',
            'id' => $id,
            'member' => $member
        ];

        return view('member.edit', $data);
    }

    public function update(Request $request, $id)
    {
        // get member data by id
        $member = Member::findOrFail($id);

        // check member data is find or fail
        if (!$member) {
            return redirect()->route('member.index')->with('error', 'Data member tidak ditemukan');
        }

        // Validate request data
        $validated = $request->validate([
            'name' => 'required|max:255',
            'address' => 'required|max:255',
            'phone' => 'required|max:255',
        ]);


        try {
            $member->update([
                'name' => $validated['name'],
                'address' => $validated['address'],
                'phone' => $validated['phone'],
            ]);

            return redirect()->route('member.index')->with('success', 'Berhasil mengubah data member.');
        } catch (\Throwable $th) {
            return back()->with('error', 'Terjadi Kesalahan: ' . $th->getMessage());
        }
    }

    public function destroy($id)
    {
        // get member data by id
        $member = Member::with('transactions')->findOrFail($id);

        // check member data is find or fail
        if (!$member) {
            return redirect()->route('member.index')->with('error', 'Data member tidak ditemukan');
        }

        try {
            if ($member->transactions()->exists()) {
                return redirect()->route('member.index')->with('error', 'member ' . $member->name . ' telah memiliki Transaksi, tidak bisa di hapus.');
            }

            $member->delete();

            return redirect()->route('member.index')->with('success', 'Berhasil menghapus ' . $member->name . ' sebagai member.');
        } catch (\Throwable $th) {
            return back()->with('error', 'Terjadi Kesalahan: ' . $th->getMessage());
        }
    }
}
