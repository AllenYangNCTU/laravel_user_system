<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Address;
use Illuminate\Http\Request;
use Hash;
use App\Enums\AddressTypeKeyEnum;
use App\Exports\MembersExport;
use Maatwebsite\Excel\Facades\Excel;


class MembersController extends Controller
{
    // 列表顯示所有會員（分頁）
    public function index()
    {
        $members = User::paginate(10); // 假設每頁顯示10條記錄
        return view('members.index', compact('members'));
    }

    // 查看特定會員詳細資訊，包括地址和文件
    public function show(User $member)
    {
        $residential_key = AddressTypeKeyEnum::RESIDENTIAL_ADDRESS;
        $correspondence_key = AddressTypeKeyEnum::CORRESPONDENCE_ADDRESS;

        $addresses = Address::where('user_id', $member->id)
            ->whereIn('address_type_id', [$residential_key, $correspondence_key])
            ->get();

        $residential = $addresses->where('address_type_id', $residential_key)->first();
        $correspondence = $addresses->where('address_type_id', $correspondence_key)->first();
        return view('members.show', compact('member', 'residential', 'correspondence'));
    }

    // 註冊新會員（顯示註冊表單）
    public function create()
    {
        return view('members.create');
    }

    // 儲存新會員
    public function store(Request $request)
    {
        $request->validate([
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email|unique:users,email',
            'birthdate' => 'required',
            'password' => 'required',
            'r_address' => 'required',
            'r_zipcode' => 'required',
            'r_city' => 'required',
            'r_state' => 'required',
            'r_country' => 'required',
            'c_address' => 'required',
            'c_zipcode' => 'required',
            'c_city' => 'required',
            'c_state' => 'required',
            'c_country' => 'required'
        ]);

        $user = User::create([
            'first_name' => $request->input('first_name'),
            'last_name' => $request->input('last_name'),
            'email' => $request->input('email'),
            'birthdate' => $request->input('birthdate'),
            'password' => Hash::make($request->input('password')),
        ]);

        $residential_address = new Address(
            [
                'user_id' => $user->id,
                'address_type_id' => AddressTypeKeyEnum::RESIDENTIAL_ADDRESS,
                'address' => $request->input('r_address'),
                'zipcode' => $request->input('r_zipcode'),
                'city' => $request->input('r_city'),
                'state' => $request->input('r_state'),
                'country' => $request->input('r_country')
            ]
        );
        $residential_address->save();
        $correspondence_address = new Address(
            [
                'user_id' => $user->id,
                'address_type_id' => AddressTypeKeyEnum::CORRESPONDENCE_ADDRESS,
                'address' => $request->input('c_address'),
                'zipcode' => $request->input('c_zipcode'),
                'city' => $request->input('c_city'),
                'state' => $request->input('c_state'),
                'country' => $request->input('c_country')
            ]
        );
        $correspondence_address->save();
        return redirect()->route('members.index')
            ->with('success', 'Member created successfully');

    }

    // 編輯現有會員詳細資訊（個人詳細資訊和地址）
    public function edit(User $member)
    {
        $residential_key = AddressTypeKeyEnum::RESIDENTIAL_ADDRESS;
        $correspondence_key = AddressTypeKeyEnum::CORRESPONDENCE_ADDRESS;

        $addresses = Address::where('user_id', $member->id)
            ->whereIn('address_type_id', [$residential_key, $correspondence_key])
            ->get();

        $residential = $addresses->where('address_type_id', $residential_key)->first();
        $correspondence = $addresses->where('address_type_id', $correspondence_key)->first();
        return view('members.edit', compact('member', 'residential', 'correspondence'));
    }

    // 更新會員資訊
    public function update(Request $request, User $member)
    {
        $request->validate([
            'email' => 'required|email|unique:users,email'
        ], [
                'email.required' => '邮箱不能为空',
                'email.email' => '邮箱格式不正确',
                'email.unique' => '该邮箱已被注册'
            ]);

        $request->validate([
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email|unique:users,email',
            'birthdate' => 'required|date',
            'r_address' => 'required',
            'r_zipcode' => 'required',
            'r_city' => 'required',
            'r_state' => 'required',
            'r_country' => 'required',
            'c_address' => 'required',
            'c_zipcode' => 'required',
            'c_city' => 'required',
            'c_state' => 'required',
            'c_country' => 'required',
        ]);

        $member->update([
            'first_name' => $request->input('first_name'),
            'last_name' => $request->input('last_name'),
            'email' => $request->input('email'),
            'birthdate' => $request->input('birthdate'),
        ]);

        $member->addresses()->where('address_type_id', AddressTypeKeyEnum::RESIDENTIAL_ADDRESS)->update([
            'address' => $request->input('r_address'),
            'zipcode' => $request->input('r_zipcode'),
            'city' => $request->input('r_city'),
            'state' => $request->input('r_state'),
            'country' => $request->input('r_country')
        ]);

        $member->addresses()->where('address_type_id', AddressTypeKeyEnum::CORRESPONDENCE_ADDRESS)->update([
            'address' => $request->input('c_address'),
            'zipcode' => $request->input('c_zipcode'),
            'city' => $request->input('c_city'),
            'state' => $request->input('c_state'),
            'country' => $request->input('c_country')
        ]);

        return redirect()->route('members.index')
            ->with('success', 'Member information updated successfully');
    }


    // 刪除會員
    public function destroy(User $member)
    {
        $member->addresses()->delete();
        $member->delete();

        return redirect()->route('members.index')
            ->with('success', 'Member deleted successfully');
    }
    public function export()
    {
        return Excel::download(new MembersExport, 'members.xlsx');
    }
}