<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Address;
use Illuminate\Http\Request;
use Hash;
use App\Enums\AddressTypeKeyEnum;
use App\Exports\MembersExport;
use Maatwebsite\Excel\Facades\Excel;
use Exception;
use DB;
use Illuminate\Database\QueryException;


class MembersController extends Controller
{
    // 列表顯示所有會員（分頁）
    public function index()
    {
        $members = DB::table('users')
            ->select(
                'id',
                'first_name',
                'last_name',
                'email',
                'birthdate',
                DB::raw("(CASE 
                WHEN status = 1 THEN 'pending' 
                WHEN status = 2 THEN 'approved' 
                WHEN status = 3 THEN 'rejected' 
                WHEN status = 4 THEN 'terminated' 
                ELSE 'status error' 
                END) as status")
            )
            ->paginate(10); //假設每頁顯示10條記錄
        return view('members.index', compact('members'));
    }

    // 查看特定會員詳細資訊，包括地址和文件
    public function show(User $member)
    {
        try {
            $residential_key = AddressTypeKeyEnum::RESIDENTIAL_ADDRESS;
            $correspondence_key = AddressTypeKeyEnum::CORRESPONDENCE_ADDRESS;

            $addresses = Address::where('user_id', $member->id)
                ->whereIn('address_type_id', [$residential_key, $correspondence_key])
                ->get();

            $residential = $addresses->where('address_type_id', $residential_key)->first();
            $correspondence = $addresses->where('address_type_id', $correspondence_key)->first();
            if (!is_object($residential) || !is_object($correspondence)) {
                throw new Exception("address data error", 9998);
            }
            return view('members.show', compact('member', 'residential', 'correspondence'));
        } catch (QueryException $e) {
            $info = $e->errorInfo;
            return redirect()->route('members.index')
                ->with($info[0], $info[2]);
        } catch (Exception $e) {
            return redirect()->route('members.index')
                ->with($e->getCode(), $e->getMessage());
        }
    }

    // 註冊新會員（顯示註冊表單）
    public function create()
    {
        try {
            return view('members.create');
        } catch (QueryException $e) {
            $info = $e->errorInfo;
            return redirect()->route('members.index')
                ->with($info[0], $info[2]);
        } catch (Exception $e) {
            return redirect()->route('members.index')
                ->with($e->getCode(), $e->getMessage());
        }
    }

    // 儲存新會員
    public function store(Request $request)
    {
        try {
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
                'c_country' => 'required',
            ]);
            DB::beginTransaction();
            $user = User::create([
                'first_name' => $request->input('first_name'),
                'last_name' => $request->input('last_name'),
                'email' => $request->input('email'),
                'birthdate' => $request->input('birthdate'),
                'password' => Hash::make($request->input('password')),
            ]);
            if (!$user) {
                throw new Exception("create member error", 9999);
            }

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
            if (!$residential_address) {
                throw new Exception("create residential address error", 9999);
            }

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
            if (!$residential_address) {
                throw new Exception("create correspondence address error", 9999);
            }

            DB::commit();
            return redirect()->route('members.index')
                ->with('success', 'Member created successfully');
        } catch (QueryException $e) {
            DB::rollback();
            $info = $e->errorInfo;
            return redirect()->route('members.index')
                ->with($info[0], $info[2]);
        } catch (Exception $e) {
            DB::rollback();
            return redirect()->route('members.index')
                ->with($e->getCode(), $e->getMessage());
        }

    }

    // 編輯現有會員詳細資訊（個人詳細資訊和地址）
    public function edit(User $member)
    {
        try {
            $residential_key = AddressTypeKeyEnum::RESIDENTIAL_ADDRESS;
            $correspondence_key = AddressTypeKeyEnum::CORRESPONDENCE_ADDRESS;

            $addresses = Address::where('user_id', $member->id)
                ->whereIn('address_type_id', [$residential_key, $correspondence_key])
                ->get();

            $residential = $addresses->where('address_type_id', $residential_key)->first();
            $correspondence = $addresses->where('address_type_id', $correspondence_key)->first();
            if (!is_object($residential) || !is_object($correspondence)) {
                throw new Exception("address data error", 9998);
            }
            return view('members.edit', compact('member', 'residential', 'correspondence'));
        } catch (QueryException $e) {
            $info = $e->errorInfo;
            return redirect()->route('members.index')
                ->with($info[0], $info[2]);
        } catch (Exception $e) {
            return redirect()->route('members.index')
                ->with($e->getCode(), $e->getMessage());
        }
    }

    // 更新會員資訊
    public function update(Request $request, User $member)
    {
        try {
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

            DB::beginTransaction();
            $result = $member->update([
                'first_name' => $request->input('first_name'),
                'last_name' => $request->input('last_name'),
                'email' => $request->input('email'),
                'birthdate' => $request->input('birthdate'),
            ]);

            if (!$result) {
                throw new Exception("member update error", 9997);
            }

            $result = $member->addresses()->where('address_type_id', AddressTypeKeyEnum::RESIDENTIAL_ADDRESS)->update([
                'address' => $request->input('r_address'),
                'zipcode' => $request->input('r_zipcode'),
                'city' => $request->input('r_city'),
                'state' => $request->input('r_state'),
                'country' => $request->input('r_country')
            ]);

            if (!$result) {
                throw new Exception("r_addresses update error", 9997);
            }

            $result = $member->addresses()->where('address_type_id', AddressTypeKeyEnum::CORRESPONDENCE_ADDRESS)->update([
                'address' => $request->input('c_address'),
                'zipcode' => $request->input('c_zipcode'),
                'city' => $request->input('c_city'),
                'state' => $request->input('c_state'),
                'country' => $request->input('c_country')
            ]);

            if ($result) {
                throw new Exception("c_addresses update error", 9997);
            }
            DB::commit();

            return redirect()->route('members.index')
                ->with('success', 'Member information updated successfully');

        } catch (QueryException $e) {
            DB::rollback();
            $info = $e->errorInfo;
            return redirect()->route('members.index')
                ->with($info[0], $info[2]);
        } catch (Exception $e) {
            DB::rollback();
            return redirect()->route('members.index')
                ->with($e->getCode(), $e->getMessage());
        }
    }


    // 刪除會員
    public function destroy(User $member)
    {
        try {
            DB::beginTransaction();
            $addresses = $member->addresses()->delete();
            $user = $member->delete();
            if (!$addresses || !$user) {
                throw new Exception("delete data error", 9996);
            }
            DB::commit();
            return redirect()->route('members.index')
                ->with('success', 'Member deleted successfully');
        } catch (QueryException $e) {
            DB::rollback();
            $info = $e->errorInfo;
            return redirect()->route('members.index')
                ->with($info[0], $info[2]);
        } catch (Exception $e) {
            DB::rollback();
            return redirect()->route('members.index')
                ->with($e->getCode(), $e->getMessage());
        }

    }
    public function export(Request $request)
    {
        try {
            // 解析成員ＩＤ
            $memberIds = explode(',', $request->input('members'));

            // 獲取成員對象
            $members = DB::table('users')
                ->whereIn('id', $memberIds)
                ->select(
                    'id',
                    'first_name',
                    'last_name',
                    'email',
                    'birthdate',
                    DB::raw("(CASE 
                    WHEN status = 1 THEN 'pending' 
                    WHEN status = 2 THEN 'approved' 
                    WHEN status = 3 THEN 'rejected' 
                    WHEN status = 4 THEN 'terminated' 
                    ELSE 'status error' 
                    END) as status")
                )
                ->get();
            return Excel::download(new MembersExport($members), 'members.xlsx');
        } catch (QueryException $e) {
            $info = $e->errorInfo;
            return redirect()->route('members.index')
                ->with($info[0], $info[2]);
        } catch (Exception $e) {
            return redirect()->route('members.index')
                ->with($e->getCode(), $e->getMessage());
        }
    }

    public function search(Request $request, User $members)
    {
        try {
            $query = User::query();

            if ($request->filled('name') && $request->filled('email')) {
                $name = $request->input('name');
                $email = $request->input('email');
                $query->where(function ($query) use ($name, $email) {
                    $query->where('first_name', 'like', '%' . $name . '%')
                        ->orWhere('last_name', 'like', '%' . $name . '%');
                })
                    ->where('email', 'like', '%' . $email . '%');
            } elseif ($request->filled('name')) {
                // 只有姓名搜索條件
                $name = $request->input('name');
                $query->where('first_name', 'like', '%' . $name . '%')
                    ->orWhere('last_name', 'like', '%' . $name . '%');
            } elseif ($request->filled('email')) {
                // 只有電子郵件搜索條件
                $email = $request->input('email');
                $query->where('email', 'like', '%' . $email . '%');
            }

            $members = $query->paginate(10); // 假設每頁10筆

            return view('members.index', compact('members'));
        } catch (QueryException $e) {
            $info = $e->errorInfo;
            return redirect()->route('members.index')
                ->with($info[0], $info[2]);
        } catch (Exception $e) {
            return redirect()->route('members.index')
                ->with($e->getCode(), $e->getMessage());
        }
    }
}