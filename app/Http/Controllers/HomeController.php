<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;

use App\Models\User;

use App\Models\Product;

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
    
}
