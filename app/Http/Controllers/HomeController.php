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

use App\Models\Comment;

use App\Models\Reply;

use App\Models\Contact;

use RealRashid\SweetAlert\Facades\Alert;





class HomeController extends Controller
{
    // redirecting to user index
    public function index()
    {
        // getting all products data and yoc can get some data by changing (all) with paginate(numbers of products)
        $product=Product::paginate(10);

        $comment=comment::orderby('id','desc')->get();

        $reply=reply::all();
        
        return view('home.userpage',compact('product','comment','reply'));
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

                $comment=comment::orderby('id','desc')->get();

                $reply=reply::all();

                return view('home.userpage',compact('product','comment','reply'));
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

            $userid=$user->id;

            $product=product::find($id);

            $product_exist_id=cart::where('Product_id','=',$id)->where('user_id','=',$userid)->get('id')->first();


    // Add existing product to the cart function
            if($product_exist_id)
            {
                $cart=cart::find($product_exist_id)->first;

                $quantity=$cart->quantity;

                $cart->quantity=$quantity + $request->quantity;

                if($product->discount_price!=null)

            {
                $cart->price=$product->discount_price * $cart->quantity ;

            }

            else

            {
                $cart->price=$product->price * $cart->quantity;
                
            }

            

                $cart->save();

                Alert::success('Product Added Successfully','We have added product to the cart');

                return redirect()->back();



            }

            else
            {

                $cart=new cart;

            $cart->name=$user->name;

            $cart->email=$user->email;

            $cart->phone=$user->phone;

            $cart->address=$user->address;

            $cart->user_id=$user->id;

            $cart->product_title=$product->title;

            if($product->discount_price!=null)

            {
                $cart->price=$product->discount_price * $request->quantity ;

            }

            else

            {
                $cart->price=$product->price * $request->quantity;
                
            }

            

            $cart->image=$product->image;

            $cart->Product_id=$product->id;

            $cart->quantity=$request->quantity;

            $cart->save();

            Alert::success('Product Added Successfully','We have added product to the cart');

            return redirect()->back();

            }

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

            Alert::success('Product Added Successfully','We have added product to the cart');

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

       Alert::success('Product Deleted Successfully','We have deleted product from the cart');
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

    public function show_order()
    {
        // when user click order in the menu without login it direct him to the log in page
        // and must add Auth; to the packages
        // view order dashboard for the users to edit or cancel

        if(Auth::id())
        {

            $user=Auth::user();

            $userid=$user->id;

            $order=order::where('user_id','=',$userid)->get();

            return view('home.order',compact('order'));
        }

        else
        {
            return redirect('login');
        }
    }


    // Cancels Orders for users
    public function cancel_order($id)
    {

        $order=order::find($id);

        $order->delivery_status='You Canceled The Order';

        $order->save();

        return redirect()->back();
    }

    // Comments and Replies

    public function add_comment(Request $request)
    {
        if(Auth::id())
        {
            $comment=new comment;

            $comment->name=Auth::user()->name;

            $comment->user_id=Auth::user()->id;
//  next comment word comes from nam:"comment" from the comment part in user.php of home
            $comment->comment=$request->comment;
            
            $comment->save();

            return redirect()->back();

        }

        else
        {
          
            
            return redirect('login');
        }
    }

    // Add reply on the comments
    public function add_reply(Request $request)
    {
        if(Auth::id())
        {

            $reply=new reply;

            $reply->name=Auth::user()->name;

            $reply->user_id=Auth::user()->id;

            $reply->comment_id=$request->commentId;

            $reply->reply=$request->reply;

            $reply->save();

            return redirect()->back();



        }

        else
        {

            return redirect('login');
        }


    }




    // product searching
    public function product_search(Request $request)
    {
        $comment=comment::orderby('id','desc')->get();

        $reply=reply::all();

        $search_text=$request->search;

        $product=product::where('title','LIKE',"%$search_text%")->orWhere
        ('category','LIKE',"$search_text")->paginate(10);

        return view('home.userpage',compact('product','comment','reply'));
    }
    

    // direct (Product) in the menu bar to the products page
    public function product()
    {
        
         $product=Product::paginate(10);

        $comment=comment::orderby('id','desc')->get();

        $reply=reply::all();
        
        return view('home.all_product',compact('product','comment','reply'));
    }


    // product searching (search_product)
    public function search_product(Request $request)
    {
        $comment=comment::orderby('id','desc')->get();

        $reply=reply::all();

        $search_text=$request->search;

        $product=product::where('title','LIKE',"%$search_text%")->orWhere
        ('category','LIKE',"$search_text")->paginate(10);

        return view('home.all_product',compact('product','comment','reply'));
    }


}
