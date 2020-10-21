<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\User;
use Exception;

class UserController extends Controller
{
    /**
     * Get User
     * 
     * @param String id
     * @return Array user
     */
    public function show($id)
    {
        $user = User::find($id);
        if(!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ], 404);
        }

        return response()->json([
            'success'   => true,
            'data'      => $user->toArray()
        ], 200);
    }

    /**
     * Create User
     * 
     * @param String name
     * @param Date birthday
     * @param String cpf
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name'      => 'required|max:255',
            'birthday'  => 'required|date_format:Y-m-d',
            'cpf'       => 'required|unique:users|max:11'
        ]);

        DB::beginTransaction();
        try {
            $user = new User([
                'name'      => $request->name,
                'birthday'  => $request->birthday,
                'cpf'       => $request->cpf
            ]);
            $user->save();

            DB::commit();
        } catch(Exception $e) {
            DB::rollback();

            return response()->json([
                'success'   => false,
                'message'   => $e->getMessage()
            ], 500);
        }

        return response()->json([
            'success'   => true,
            'message'   => 'User created!'
        ], 201);
    }

    /**
     * Update User
     * 
     * @param String name
     * @param Date birthday
     * @param String cpf
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name'      => 'nullable|max:255',
            'birthday'  => 'nullable|date_format:Y-m-d',
            'cpf'       => 'nullable|unique:users|max:11'
        ]);

        $user = User::find($id);
        if(!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ], 404);
        }

        DB::beginTransaction();
        try {
            $user->update($request->only(['name', 'birthday', 'cpf']));

            DB::commit();
        } catch (Exception $e) {
            DB::rollback();

            return response()->json([
                'success'   => false,
                'message'   => $e->getMessage()
            ], 500);
        }

        return response()->json([
            'success'   => true,
            'message'   => 'User updated!'
        ], 200);
    }

    /**
     * Get User
     * 
     * @param String id
     */
    public function delete($id)
    {
        $user = User::find($id);
        if(!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ], 404);
        }

        DB::beginTransaction();
        try {
            $user->delete();

            DB::commit();
        } catch(Exception $e) {
            DB::rollback();

            return response()->json([
                'success'   => false,
                'message'   => $e->getMessage()
            ], 500);
        }

        return response()->json([
            'success'   => true,
            'message'   => 'User deleted!'
        ], 200);
    }
}
