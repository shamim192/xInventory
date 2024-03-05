<?php

namespace App\Http\Controllers\Admin\Payment;

use App\Models\Bank;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\IncomeRequest;
use App\Models\Income;
use App\Models\IncomeCategory;
use App\Models\IncomeItem;

class IncomeController extends Controller {

    public function index(Request $request)
    {
        $sql = Income::with([
            'items', 
            'items.bank', 
            'items.category'
        ])->orderBy('id', 'DESC');
        
        if ($request->q) {
            $sql->where(function ($q) use ($request) {
                $q->Where('note', 'LIKE','%'. $request->q . '%')
                ->orWhere('date', 'LIKE','%'. $request->q . '%')
                ->orWhere('total_amount', 'LIKE','%'. $request->q . '%');
            });
            $sql->orwhereHas('items.category', function($q) use($request) {
                $q->where('name', 'LIKE','%'. $request->q . '%');
            });
            $sql->orwhereHas('items.bank', function($q) use($request) {
                $q->where('name', 'LIKE','%'. $request->q . '%');
            });
        }

        if ($request->from) {
            $sql->where('date', '>=', dbDateFormat($request->from));
        }
        
        if ($request->to) {
            $sql->where('date', '<=', dbDateFormat($request->to));
        }

        $records = $sql->paginate(paginateLimit());
        $serial = pagiSerial($records);

        return view('admin.payment.income.index', compact('serial', 'records'));
    }
    
    public function create()
    {
        $banks = Bank::select('id','name')->where('status', 'Active')->get();
        $categories = IncomeCategory::select('id','name')->where('status', 'Active')->get();

        $items = [
            (object)[
                'id' => 0,
                'bank_id' => null,
                'income_category_id' => null,
                'amount' => null
            ]
        ];
        return view('admin.payment.income.create', compact('banks', 'categories','items'));
    }
    
    public function store(IncomeRequest $request)
    {
        $data = Income::create([
            'date' => dbDateFormat($request->date),
            'note' => $request->note,
            'total_amount' => $request->total_amount,
        ]);

        if ($request->only('income_item_id')) {
            $amount = 0;
            foreach ($request->income_item_id as $key => $eitem) {
               $item = IncomeItem::create([
                    'income_id' => $data->id,
                    'income_category_id' => $request->category_id[$key],
                    'bank_id' => $request->bank_id[$key],
                    'amount' => $request->amount[$key],
                ]);
                $amount += $request->amount[$key];
                if ($item) {
                    Transaction::create([
                        'type' => 'Received',
                        'flag' => 'Income',
                        'flagable_id' => $item->id,
                        'flagable_type' => IncomeItem::class,
                        'bank_id' => $item->bank_id,
                        'datetime' => now(),
                        'note' => $data->note,
                        'amount' => $item->amount,
                    ]);
                }
            }
            $data->update([
                'total_amount' => $amount
            ]);
        }
       
        if ($data) {
            session()->flash('successMessage', 'Income was successfully added.');
        } else {
            session()->flash('errorMessage', 'Income saving failed!');
        }

        return redirect()->action([self::class, 'create'], qArray());
    }
    
    public function show(Request $request, $id)
    {
        $data = Income::with([
            'items', 
            'items.bank', 
            'items.category'
        ])->find($id);
        
        return view('admin.payment.income.show', compact('data'));
    }
    
    public function edit(Request $request, $id)
    {
        $data = Income::find($id);

        if (count($data->items) > 0) {
            $items = $data->items;
        }else {
            $items = [
                (object)[
                    'id' => 0,
                    'bank_id' => null,
                    'income_category_id' => null,
                    'amount' => null
                ]
            ];
        }
        $banks = Bank::select('id','name')->where('status', 'Active')->get();
        $categories = IncomeCategory::select('id','name')->where('status', 'Active')->get();
        
        return view('admin.payment.income.edit', compact('data', 'banks', 'categories','items'));
    }
    
    public function update(IncomeRequest $request, $id)
    {        
        $data = Income::find($id);
        
      $updated =  $data->update([
            'date' => dbDateFormat($request->date),
            'note' => $request->note,
            'total_amount' => $request->total_amount,
        ]);

        if (count($request->income_item_id) > 0) {
            $amount = 0;
            IncomeItem::whereNotIn('income_category_id', $request->category_id)
            ->where('income_id', $data->id)
            ->delete();
            foreach ($request->income_item_id as $key => $eitem) {
                if ($eitem > 0) {
                        IncomeItem::where('id', $eitem)->update([
                            'income_id' => $data->id,
                            'income_category_id' => $request->category_id[$key],
                            'bank_id' => $request->bank_id[$key],
                            'amount' => $request->amount[$key],
                        ]);
                    $item = IncomeItem::find($eitem);
                } else {
                    $item = IncomeItem::create([
                        'income_id' => $data->id,
                        'income_category_id' => $request->category_id[$key],
                        'bank_id' => $request->bank_id[$key],
                        'amount' => $request->amount[$key],
                    ]);
                }
                $amount += $request->amount[$key];
                if ($item) {
                    Transaction::updateOrCreate([
                        'flagable_id' => $item->id,
                        'flagable_type' => IncomeItem::class,
                    ], [
                        'type' => 'Received',
                        'flag' => 'Income',
                        'bank_id' => $item->bank_id,
                        'datetime' => now(),
                        'note' => $data->note,
                        'amount' => $item->amount,
                    ]);
                }
            }
            $data->update([
                'total_amount' => $amount
            ]);
   
        }
       
        if ($updated) {
            session()->flash('successMessage', 'Income was successfully updated.');
        } else {
            session()->flash('errorMessage', 'Income update failed!');
        }

        return redirect()->action([self::class, 'index'], qArray());
    }
    
    public function destroy(Request $request, $id)
    {
        try {
            $data = Income::findOrFail($id);

            foreach ($data->items as $item) {
                Transaction::where('flagable_id', $item->id)->where('flagable_type', IncomeItem::class)->delete();
                $item->delete();
            }
            $data->delete();

            session()->flash('successMessage', 'Income was successfully deleted.');
        } catch (\Exception $e) {
            session()->flash('errorMessage', 'Income deleting failed! Reason: ' . $e->getMessage());
        }

        return redirect()->action([self::class, 'index'], qArray());
    }
}
