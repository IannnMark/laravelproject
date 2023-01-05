<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Stock;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Customer;
use Log;
use DB;
use Carbon;
use View;
use Storage;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return View::make('product.index');
    }


    public function getProduct(Request $request){

        $products = Product::orderBy('id','ASC')->get();
        return response()->json($products);

     }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $product = new Product();

        $product->brand = $request->brand;
        $product->description = $request->description;
        $product->cost_price = $request->cost_price;
        $product->sell_price = $request->sell_price;

        $files = $request->file('uploads');

        $product->product_image = 'images/'.$files->getClientOriginalName();
        $product->save();

        $stock = new Stock();

        $stock->product_id = $product->id;
        $stock->quantity = $request->quantity;

        $stock->save();

        Storage::put('public/images/'.$files->getClientOriginalName(), file_get_contents($files));
        return response()->json(["success" => "product created successfully.", "product" => $product, "status" => 200]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function show(Product $product)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $product = Product::find($id);
        return response()->json($product);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $product = Product::find($id);
        $input = $request->all();

        $product->brand = $request->brand;
        $product->description = $request->description;
        $product->cost_price = $request->cost_price;
        $product->sell_price = $request->sell_price;

        $files = $request->file('uploads');

        $product->product_image = 'images/'.$files->getClientOriginalName();
        Storage::put('public/images/'.$files->getClientOriginalName(), file_get_contents($files));
        
        // $product->update($input);
        $product->save($input);
        return response()->json(["success" => "product updated successfully.", "product" => $product, "status" => 200]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $product->stock()->delete();
        // return response()->json($product);
        return response()->json(["success" => "Product deleted successfully.", "product" => $product, "status" => 200]);
    }

    //check out function
    public function postCheckout(Request $request){

        $products = json_decode($request->getContent(),true);
        Log::info(print_r($products, true));
          try {
              DB::beginTransaction();

              $order = new Order();
              $order->date_placed = now();
              $customer =  Customer::find(1);

              $order->customer_id = $customer->customer_id;

               $order->save();
            //   $custid = -1;

            //   $customer =  Customer::find(1);

            //   $product = Product::find(1);

            //   $order->customer_id = $customer->id;

            //   $order->product_id = $product->id;
                          //   $customer->orders()->save($order);

            foreach($products as $product) {
               $id = $product['product_id'];
               $order->products()->attach($order->orderinfo_id,['quantity'=> $product['quantity'],'product_id'=>$id]);

               $stock = Stock::find($id);
               $stock->quantity = $stock->quantity - $product['quantity'];
               $stock->save();
            }
            
          }
          catch (\Exception $e) {
              DB::rollback();
              return response()->json(array('status' => 'Order failed','code'=>409,'error'=>$e->getMessage()));
              }
      
          DB::commit();
          return response()->json(array('status' => 'Order Success','code'=>200,'order id'=>$order->orderinfo_id));
      
          }//end postcheckout
}
