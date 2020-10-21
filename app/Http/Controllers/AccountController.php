<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Account;
use App\Models\TypeAccount;
use App\Models\TransactionToken;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Exception;
use DateTime;

class AccountController extends Controller
{
    /**
     * Create Account
     * 
     * @param User user_id
     * @param String password
     * @param TypeAccount type
     */
    public function store(Request $request) {
        $this->validate($request, [
            'user_id'   => 'required|exists:users,id',
            'password'  => 'required|max:4',
            'type'      => 'required|exists:type_accounts,id'
        ]);

        DB::beginTransaction();
        try{
            $account_number = mt_rand(100000, 999999);

            $account = new Account([
                'user_id'           => $request->user_id,
                'account_number'    => $account_number,
                'password'          => Hash::make($request->password),
                'balance'           => 0.00,
                'type'              => $request->type
            ]);
            $account->save();

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
            'message'   => 'Account created!',
            'data'      => [
                'account_number'    => $account->account_number,
            ]
        ], 201);
    }

    /**
     * Login ATM
     * 
     * @param Int account_number
     * @param String password
     * 
     * @return token
     */
    public function login(Request $request)
    {
        $this->validate($request, [
            'account_number'    => 'required|max:6',
            'password'          => 'required|max:4'
        ]);

        $account = Account::where('account_number', $request->account_number)
            ->first();
        
        if(!$account || !Hash::check($request->password, $account->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Account or password is incorrect!'
            ], 404);
        }

        DB::beginTransaction();
        try{
            $token = $this->generateToken();
            $user_id = $account->user->id;
            $expiration_date = new Datetime(date('Y-m-d h:i:s'));
            $expiration_date->modify('+1 minutes');
            
            $transaction_token = new TransactionToken([
                'token' => $token,
                'expiration_date' => $expiration_date,
                'status' => 1,
                'user_id' => $user_id
            ]);
            $transaction_token->save();

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
            'message'   => 'Logged!',
            'data'      => [
                'token' => $token
            ]
        ], 200);
    }

    private function defineNote($value) {
        $notes = [100,50,20];
        foreach($notes as $index => $note) {
            if($value >= $note) {
                if($value == 80 && $note == 50){
                    continue;
                }
                
                if($value == 60 && $note == 50){
                    continue;
                }
                return $note;
            }
        }
    }

    private function notes($value, $notes_delivered)
    {
        $note = $this->defineNote($value);
        if($value > 0 && !is_null($note)) {
            if($value % $note == 0) {
                $notes_delivered[$note] += $value / $note;
    
                return $notes_delivered;
            } else {
                $notes_delivered[$note] += floor($value / $note);
                $value = $value % $note;
    
                return $this->notes($value, $notes_delivered);
            }
        }

        return false;
    }

    /**
     * Withdraw ATM
     * 
     * @param Int account_number
     * @param String token
     * @param Double value
     * 
     * @return String account_number
     * @return Double balance
     * @return Array notes_delivered
     */
    public function withdraw(Request $request)
    {
        $this->validate($request, [
            'account_number'    => 'required|exists:accounts,account_number',
            'token'             => 'required',
            'value'             => 'required|numeric'
        ]);

        $account = Account::where('account_number', $request->account_number)
            ->first();
        
        $date = new Datetime(date('Y-m-d h:i:s'));
        $token_transaction = TransactionToken::where('token', $request->token)
            ->where('status', 1)
            ->where('expiration_date', '>', $date)
            ->first();

        if(!$token_transaction) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired token'
            ], 404);
        }

        $notes_delivered = ['100' => 0, '50' => 0, '20' => 0];
        $notes_delivered = $this->notes($request->value, $notes_delivered);
        if(!$notes_delivered) {
            return response()->json([
                'success' => false,
                'message' => 'Unavailable notes!'
            ], 404);
        }

        if($request->value > $account->balance) {
            return response()->json([
                'success' => false,
                'message' => 'Insufficient funds!'
            ], 404);
        }

        DB::beginTransaction();
        try{
            $balance = $account->balance - $request->value;
            $token_transaction->update(['status' => 0]);
            $account->update(['balance' => $balance]);

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
            'message'   => 'Deposit made!',
            'data'      => [
                'account_number'    => $account->account_number,
                'balance'           => $account->balance,
                'notes_delivered'   => $notes_delivered
            ]
        ], 200);
    }

    /**
     * Withdraw ATM
     * 
     * @param Int account_number
     * @param String token
     * @param Double balance
     * 
     * @return String account_number
     * @return Double balance
     * @return Array notes_delivered
     */
    public function deposit(Request $request)
    {
        $this->validate($request, [
            'account_number'    => 'required|exists:accounts,account_number',
            'token'             => 'required',
            'balance'           => 'required|numeric'
        ]);

        $account = Account::where('account_number', $request->account_number)
            ->first();
        
        $date = new Datetime(date('Y-m-d h:i:s'));
        $token_transaction = TransactionToken::where('token', $request->token)
            ->where('status', 1)
            ->where('expiration_date', '>', $date)
            ->first();

        if(!$token_transaction) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired token'
            ], 404);
        }

        DB::beginTransaction();
        try{
            $balance = round($request->balance) + $account->balance;
            $token_transaction->update(['status' => 0]);
            $account->update(['balance' => $balance]);

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
            'message'   => 'Deposit made!',
            'data'      => [
                'account_number'    => $account->account_number,
                'balance'           => $account->balance
            ]
        ], 200);
    }

    private function generateToken()
    {
        return md5(rand(1, 10) . microtime());
    }
}