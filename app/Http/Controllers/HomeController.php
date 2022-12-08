<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;

use App\Models\User;

use App\Models\Product;

use App\Models\Cart;

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
              return view('admin.home');  
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
                $cart->price=$product->discount_price * $request->quantity;

            }

            else

            {
                $cart->price=$product->price * $request->quantity;
                
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
}
