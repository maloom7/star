<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;

use App\Models\User;

use App\Models\Product;

use App\Models\Cart;

use App\Models\Order;

use Session;

use Stripe;



class HomeController extends Controller
{
    // redirecting to user index
    public function index()
    {
        // getting all products data and yoc can get some data by changing (all) with paginate(numbers of products)
        $product=Product::paginate(10);
        return view('home.userpage',compact('product'));
    }


    // Redirecting admin to admin page and direct users to thier page
        public function redirect()
        {
            
            $usertype=Auth::user()->usertype;
// to admin dashboard
            if($usertype=='1')
            {
                // showing total reports for data  in the dashboard
             $total_product=product::all()->count();

             $total_order=order::all()->count();

             $total_user=user::all()->count();

    // counting total prices
             $order=order::all();

             $total_revenue=0;

             foreach($order as $order)

             {
                $total_revenue=$total_revenue + $order->price;
             }


            //  order dlivered reports
            $total_delivered=order::where('delivery_status','=','delivered')->get()->count();

            $total_processing=order::where('delivery_status','=','processing')->get()->count();



              return view('admin.home',compact('total_product','total_order','total_user','total_revenue','total_delivered','total_processing'));  
            }
            // to user page and dashboard
            else
            
            {
                $product=Product::paginate(10);
                return view('home.userpage',compact('product'));
            }

        }

    public function product_details($id)
    {
        $product=product::find($id);

       return view('home.product_details',compact('product')); 
    }
// add to cart and directing to login page
    public function add_cart(Request $request,$id)
    {
        if(Auth::id())
        {
            $user=Auth::user();

            $product=product::find($id);

            $cart=new cart;

            $cart->name=$user->name;

            $cart->email=$user->email;

            $cart->phone=$user->phone;

            $cart->address=$user->address;

            $cart->user_id=$user->id;

            $cart->product_title=$product->title;

            if($product->discount_price!=null)

            {
                $cart->price=$product->discount_price ;

            }

            else

            {
                $cart->price=$product->price ;
                
            }

            

            $cart->image=$product->image;

            $cart->Product_id=$product->id;

            $cart->quantity=$request->quantity;

            $cart->save();

            return redirect()->back();



            
    }

       else
       
       {
        return redirect('login');
       }
    }

    // show cart function
    public function show_cart()
    {
        // when clicking the CART without login in
        if(Auth::id())
        {

            $id=Auth::user()->id;

            $cart=cart::where('user_id','=',$id)->get();
            return view('home.showcart',compact('cart'));
        }

        else
        {
            return redirect ('login');
        }

       
    }
// delete a product from the CART
    public function remove_cart($id)
    {
       $cart=cart::find($id);
       
       $cart->delete();

       return redirect()->back();
    }

// move cart data to ORDER TABLE
    public function cash_order()
    {

        $user=Auth::user();

        $userid=$user->id;

        $data=cart::where('user_id','=',$userid)->get();

        foreach($data as $data)
        {
            $order=new order;
// user information data
            $order->name=$data->name;

            $order->email=$data->email;

            $order->phone=$data->phone;

            $order->address=$data->address;

            $order->user_id=$data->user_id;



// product information data

            $order->product_title=$data->product_title;

            $order->price=$data->price;

            $order->quantity=$data->quantity;

            $order->image=$data->image;

            $order->product_id=$data->Product_id;



            $order->payment_status='cash on delivery';
            
            $order->delivery_status='processing';


            $order->save();




            $cart_id=$data->id;

            $cart=cart::find($cart_id);

            $cart->delete();

        }

        return redirect()->back()->with('message', 'We have Recieved Your Order. We Will cConnect You Soon...');


    }

    // Payment Gatway

    public function stripe($totalprice)

    {
        return view('home.stripe',compact('totalprice'));
    }


    public function stripePost(Request $request,$totalprice)
    {
        
        Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
    
        Stripe\Charge::create ([
                "amount" => $totalprice * 100,
                "currency" => "usd",
                "source" => $request->stripeToken,
                "description" => "Thanks for Payment" 
        ]);


        $user=Auth::user();

        $userid=$user->id;

        $data=cart::where('user_id','=',$userid)->get();

        foreach($data as $data)
        {
            $order=new order;
// user information data
            $order->name=$data->name;

            $order->email=$data->email;

            $order->phone=$data->phone;

            $order->address=$data->address;

            $order->user_id=$data->user_id;



// product information data

            $order->product_title=$data->product_title;

            $order->price=$data->price;

            $order->quantity=$data->quantity;

            $order->image=$data->image;

            $order->product_id=$data->Product_id;



            $order->payment_status='Paid';
            
            $order->delivery_status='processing';


            $order->save();




            $cart_id=$data->id;

            $cart=cart::find($cart_id);

            $cart->delete();
        }
      
        Session::flash('success', 'Payment successful!');
              
        return back();
    }



}
