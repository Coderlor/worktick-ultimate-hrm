<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Account;
use App\Models\Deposit;
use App\Models\DepositCategory;
use App\Models\PaymentMethod;
use DateTime;
use Carbon\Carbon;

class DepositController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user_auth = auth()->user();
		if ($user_auth->can('deposit_view')){

            $deposits = Deposit::where('deleted_at', '=', null)
            ->orderBy('id', 'desc')
            ->get();

            return view('accounting.deposit.deposit_list',compact('deposits'));

        }
        return abort('403', __('You are not authorized'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $user_auth = auth()->user();
		if ($user_auth->can('deposit_add')){

            $accounts = Account::where('deleted_at', '=', null)
            ->orderBy('id', 'desc')
            ->get(['id','account_name']);

            $categories = DepositCategory::where('deleted_at', '=', null)
            ->orderBy('id', 'desc')
            ->get(['id','title']);

            $payment_methods = PaymentMethod::where('deleted_at', '=', null)
            ->orderBy('id', 'desc')
            ->get(['id','title']);

            return view('accounting.deposit.create_deposit', compact('accounts','categories','payment_methods'));

        }
        return abort('403', __('You are not authorized'));

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $user_auth = auth()->user();
		if ($user_auth->can('deposit_add')){

            \DB::transaction(function () use ($request) {

                $request->validate([
                    'deposit_ref'           => 'required|string|max:255',
                    'account_id'            => 'required',
                    'deposit_category_id'   => 'required',
                    'amount'                => 'required|numeric',
                    'payment_method_id'     => 'required',
                    'date'                  => 'required',
                    'attachment'           => 'nullable|max:2048',
                ]);

                if ($request->hasFile('attachment')) {

                    $image = $request->file('attachment');
                    $filename = time().'.'.$image->extension();  
                    $image->move(public_path('/assets/images/deposits'), $filename);

                } else {
                    $filename = Null;
                }

                
                Deposit::create([
                    'deposit_ref'            => $request['deposit_ref'],
                    'account_id'             => $request['account_id'],
                    'deposit_category_id'    => $request['deposit_category_id'],
                    'amount'                 => $request['amount'],
                    'payment_method_id'      => $request['payment_method_id'],
                    'date'                   => $request['date'],
                    'attachment'            => $filename,
                    'description'            => $request['description'],
                ]);
                    
                $account = Account::findOrFail($request['account_id']);
                $account->update([
                    'initial_balance' => $account->initial_balance + $request['amount'],
                ]);


            }, 10);

            return response()->json(['success' => true]);

        }
        return abort('403', __('You are not authorized'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $user_auth = auth()->user();
		if ($user_auth->can('deposit_edit')){

            $deposit = Deposit::where('deleted_at', '=', null)->findOrFail($id);
            $accounts = Account::where('deleted_at', '=', null)->orderBy('id', 'desc')->get(['id','account_name']);
            $categories = DepositCategory::where('deleted_at', '=', null)->orderBy('id', 'desc')->get(['id','title']);
            $payment_methods = PaymentMethod::where('deleted_at', '=', null)->orderBy('id', 'desc')->get(['id','title']);

            return view('accounting.deposit.edit_deposit', compact('deposit','accounts','categories','payment_methods'));

        }
        return abort('403', __('You are not authorized'));

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $user_auth = auth()->user();
		if ($user_auth->can('deposit_edit')){

            \DB::transaction(function () use ($request , $id) {
                $request->validate([
                    'deposit_ref'           => 'required|string|max:255',
                    'account_id'            => 'required',
                    'deposit_category_id'   => 'required',
                    'amount'                => 'required|numeric',
                    'payment_method_id'     => 'required',
                    'date'                  => 'required',
                    'attachment'            => 'nullable|max:2048',
                ]);

                $deposit = Deposit::findOrFail($id);

                $Current_attachment = $deposit->attachment;
                if ($request->attachment != 'null') {
                    if ($request->attachment != $Current_attachment) {

                        $image = $request->file('attachment');
                        $filename = time().'.'.$image->extension();  
                        $image->move(public_path('/assets/images/deposits'), $filename);
                        $path = public_path() . '/assets/images/deposits';
                        $attachment = $path . '/' . $Current_attachment;
                        if (file_exists($attachment)) {
                                @unlink($attachment);
                        }
                    } else {
                        $filename = $Current_attachment;
                    }
                }else{
                    $filename = $Current_attachment;
                }

                Deposit::whereId($id)->update([
                    'deposit_ref'            => $request['deposit_ref'],
                    'account_id'             => $request['account_id'],
                    'deposit_category_id'    => $request['deposit_category_id'],
                    'amount'                 => $request['amount'],
                    'payment_method_id'      => $request['payment_method_id'],
                    'date'                   => $request['date'],
                    'attachment'             => $filename,
                    'description'            => $request['description'],
                ]);

                $account = Account::findOrFail($request['account_id']);
                $balance = $account->initial_balance - $deposit->amount;
                $account->update([
                    'initial_balance' => $balance + $request['amount'],
                ]);

            }, 10);

            return response()->json(['success' => true]);

        }
        return abort('403', __('You are not authorized'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user_auth = auth()->user();
		if ($user_auth->can('deposit_delete')){

            Deposit::whereId($id)->update([
                'deleted_at' => Carbon::now(),
            ]);
            return response()->json(['success' => true]);

        }
        return abort('403', __('You are not authorized'));
    }

     //-------------- Delete by selection  ---------------\\

     public function delete_by_selection(Request $request)
     {
        $user_auth = auth()->user();
        if($user_auth->can('deposit_delete')){
            $selectedIds = $request->selectedIds;
    
            foreach ($selectedIds as $deposit_id) {
                Deposit::whereId($deposit_id)->update([
                    'deleted_at' => Carbon::now(),
                ]);
            }
            return response()->json(['success' => true]);
        }
        return abort('403', __('You are not authorized'));
     }
}
