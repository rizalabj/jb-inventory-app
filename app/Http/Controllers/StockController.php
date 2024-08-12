<?php

namespace App\Http\Controllers;

 
use App\DataTables\StockDataTable;
use App\Models\Stock;
use App\Models\Product;
use Spatie\Permission\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
Use Alert;

 
class StockController extends Controller
{
    public function history(StockDataTable $dataTable){
        $title = 'Delete User!';
        $text = "Are you sure you want to delete?";
        confirmDelete($title, $text);
        
        return $dataTable->render('stock.index');
    }

    public function search(Request $request)
    {
        $term = $request->input('term');

        $results = Product::with('productprices.productunit')
                    ->where('name', 'LIKE', '%' . $term . '%')
                    ->orWhere('code','LIKE','%'.$term.'%')
                    ->get();

        return response()->json($results);
    }

    public function store(Request $request){
        $validatedData = $request->validate([
            'type' => 'required',
            'product_id' => 'bail|required',
            'product_price_id' => 'bail|required',
            'quantity' => 'bail|required',
        ]);

        Stock::create([
            'type' => $request->type,
            'product_id' => $request->product_id,
            'product_price_id' => $request->product_price_id,
            'quantity' => $request->quantity,
            'notes' => $request->notes,
            'user_by' => Auth::id(),
        ]);

        Alert::success('Nice!', 'Product  Added!');
        return redirect()->route('product.index');

        
    }

    public function in(){
        return view('stock.create',['type'=>'in']);
    }
    
    public function out(){
        return view('stock.create',['type'=>'out']);
    }
}