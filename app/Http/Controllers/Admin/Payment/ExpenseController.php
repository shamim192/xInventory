<?php

namespace App\Http\Controllers\Admin\Payment;

use App\Models\Bank;
use App\Models\Expense;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Models\ExpenseCategory;
use App\Http\Controllers\Controller;
use App\Http\Requests\ExpenseRequest;
use App\Models\ExpenseItem;

class ExpenseController extends Controller
{

    public function index(Request $request)
    {
        $sql = Expense::with([
            'items',
            'items.bank',
            'items.category'
        ])->orderBy('id', 'DESC');

        if ($request->q) {
            $sql->where(function ($q) use ($request) {
                $q->Where('note', 'LIKE', '%' . $request->q . '%')
                    ->orWhere('date', 'LIKE', '%' . $request->q . '%')
                    ->orWhere('total_amount', 'LIKE', '%' . $request->q . '%');
            });
            $sql->orwhereHas('items.category', function ($q) use ($request) {
                $q->where('name', 'LIKE', '%' . $request->q . '%');
            });
            $sql->orwhereHas('items.bank', function ($q) use ($request) {
                $q->where('name', 'LIKE', '%' . $request->q . '%');
            });
        }

        if ($request->from) {
            $sql->where('date', '>=', dbDateFormat($request->from));
        }

        if ($request->to) {
            $sql->where('date', '<=', dbDateFormat($request->to));
        }

        $records = $sql->latest('id')->paginate(paginateLimit());
        $serial = pagiSerial($records);

        return view('admin.payment.expense.index', compact('records','serial'));
    }

    public function create()
    {
        $banks = Bank::select('id', 'name')->where('status', 'Active')->get();
        $categories = ExpenseCategory::select('id', 'name')->where('status', 'Active')->get();

        $items = [
            (object)[
                'id' => 0,
                'bank_id' => null,
                'expense_category_id' => null,
                'amount' => null
            ]
        ];
        return view('admin.payment.expense.create', compact('banks', 'categories', 'items'));
    }

    public function store(ExpenseRequest $request)
    {
        $data = Expense::create([
            'date' => dbDateFormat($request->date),
            'note' => $request->note,
            'total_amount' => $request->total_amount,
        ]);

        if ($request->only('expense_item_id')) {
            $amount = 0;
            foreach ($request->expense_item_id as $key => $eitem) {
                $item = ExpenseItem::create([
                    'expense_id' => $data->id,
                    'expense_category_id' => $request->category_id[$key],
                    'bank_id' => $request->bank_id[$key],
                    'amount' => $request->amount[$key],
                ]);
                $amount += $request->amount[$key];
                if ($item) {
                    Transaction::create([
                        'type' => 'Payment',
                        'flag' => 'Expense',
                        'flagable_id' => $item->id,
                        'flagable_type' => ExpenseItem::class,
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
            session()->flash('successMessage', 'Expense was successfully added.');
        } else {
            session()->flash('errorMessage', 'Expense saving failed!');
        }

        return redirect()->action([self::class, 'create'], qArray());
    }

    public function show(Request $request, $id)
    {
        $data = Expense::with([
            'items',
            'items.bank',
            'items.category'
        ])->find($id);

        return view('admin.payment.expense.show', compact('data'));
    }

    public function edit(Request $request, $id)
    {
        $data = Expense::find($id);
       
        if (count($data->items) > 0) {
            $items = $data->items;
        } else {
            $items = [
                (object)[
                    'id' => 0,
                    'bank_id' => null,
                    'expense_category_id' => null,
                    'amount' => null
                ]
            ];
        }
        $banks = Bank::select('id', 'name')->where('status', 'Active')->get();
        $categories = ExpenseCategory::select('id', 'name')->where('status', 'Active')->get();

        return view('admin.payment.expense.edit', compact('data', 'banks', 'categories', 'items'));
    }

    public function update(ExpenseRequest $request, $id)
    {
        $data = Expense::find($id);

        $updated = $data->update([
            'date' => dbDateFormat($request->date),
            'note' => $request->note,
            'total_amount' => $request->total_amount,
        ]);

        if (count($request->expense_item_id) > 0) {
            $amount = 0;
            ExpenseItem::whereNotIn('expense_category_id', $request->category_id)
                ->where('expense_id', $data->id)
                ->delete();
            foreach ($request->expense_item_id as $key => $eitem) {
                if ($eitem > 0) {
                    ExpenseItem::where('id', $eitem)->update([
                        'expense_id' => $data->id,
                        'expense_category_id' => $request->category_id[$key],
                        'bank_id' => $request->bank_id[$key],
                        'amount' => $request->amount[$key],
                    ]);
                    $item = ExpenseItem::find($eitem);
                } else {
                    $item = ExpenseItem::create([
                        'expense_id' => $data->id,
                        'expense_category_id' => $request->category_id[$key],
                        'bank_id' => $request->bank_id[$key],
                        'amount' => $request->amount[$key],
                    ]);
                }
                $amount += $request->amount[$key];
                if ($item) {
                    Transaction::updateOrCreate([
                        'flagable_id' => $item->id,
                        'flagable_type' => ExpenseItem::class,
                    ], [
                        'type' => 'Payment',
                        'flag' => 'Expense',
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
            session()->flash('successMessage', 'Expense was successfully updated.');
        } else {
            session()->flash('errorMessage', 'Expense update failed!');
        }
        
        return redirect()->action([self::class, 'index'], qArray());
    }

    public function destroy($id)
    {
        try {
            $data = Expense::findOrFail($id);

            foreach ($data->items as $item) {
                Transaction::where('flagable_id', $item->id)->where('flagable_type', ExpenseItem::class)->delete();
                $item->delete();
            }
            $data->delete();

            session()->flash('successMessage', 'Expense was successfully deleted.');
        } catch (\Exception $e) {
            session()->flash('errorMessage', 'Expense deleting failed! Reason: ' . $e->getMessage());
        }

        return redirect()->action([self::class, 'index'], qArray());
    }
}
