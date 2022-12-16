<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Category;

use App\Models\Product;

use App\Models\Order;

use Illuminate\Support\Facades\Auth;

use PDF;

use Notification;

use App\Notifications\SendEmailNotification;



class AdminController extends Controller
{
    public function view_category()
    {
        if(Auth::id())
        {
            // showing and sending new category into the table
      $data=category::all();  
      return view('admin.category',compact('data'));

        }
        else
        {
            return redirect('login');
        }

    } 




// function of adding category with success message come from category.blade.php
    public function add_category(Request $request)
    {
     $data=new category; 
     
     $data->category_name=$request->category;

     $data->save();

     return redirect()->back()->with('message','Category Added Successfully');
    } 

    // Delete category  
    public function delete_category($id)
    {
        $data=category::find($id);
        $data->delete();
        return redirect()->back()->with('message','Category Deleted
        Successfully');

    }

// view product
    public function view_product()
    {
        // showing catagories when adding new product
        $category=category::all();
        return view('admin.product',compact('category'));
    }

// add new product...
    public function add_product(Request $request)
    {
   $product=new product;
//    $request will get all the data from product.blade.php
   $product->title=$request->title;
   
   $product->description=$request->description;
   
   $product->price=$request->price;
   
   $product->quantity=$request->quantity;
   
   $product->discount_price=$request->discount_price;
   
   $product->category=$request->category;
   
   $image=$request->image;

   $imagename=time().'.'.$image->getClientOriginalExtension();

   $request->image->move('product',$imagename);

   $product->image=$imagename;

    

   $product->save();

   return redirect()->back()->with('message','Product Added Successfully');
    }

    public function show_product()
    {
        // get the data from product table
        $product=product::all();
        return view('admin.show_product',compact('product'));
    }

    public function delete_product($id)
    {
        $product=product::find($id);

        $product->delete();

        return redirect()->back()->with('message',
        'Product Deleted Successfully');
    }
// <<<<Update product>>>>
    public function update_product($id)
    {
        // get product data
        $product=product::find($id);
        // get categories types to update page
        $category=category::all();

        return view('admin.update_product',compact('product','category'));

        }
        public function update_product_confirm(Request $request,$id)
        {
            if(Auth::id())
            {
                $product=product::find($id);

            $product->title=$request->title;
            
            $product->description=$request->description;
            
            $product->price=$request->price;
            
            $product->discount_price=$request->discount_price;
            
            $product->category=$request->category;
            
            $product->quantity=$request->quantity;
            
            $image=$request->image;
               // this if statement give you ability to update data without editting the image..
            if($image)
            {
                $imagename=time().'.'.$image->getClientOriginalExtension();

                $request->image->move('product',$imagename);
    
    
                $product->image=$imagename;
    

            }
            
           
            $product->save();

            return redirect()->back()->with('message','Product Updated Successfully');



            }

            else
            {
                return redirect('login');
            }

            

        }

        // showing orders into the admin dashboard

        public function order()
        {
            $order=order::all();

            return view('admin.order',compact('order'));
        }

        // showing delivery status
        public function delivered($id)
        {
            $order=order::find($id);

            $order->delivery_status="delivered";

            $order->payment_status='paid';

            $order->save();

            return redirect()->back();
        }

        // Print PDF
        public function print_pdf($id)
        {
            $order=order::find($id);

            $pdf=PDF::loadView('admin.pdf',compact('order'));

            return $pdf->download('order_details.pdf');

        }

        // Send Email notifications

        public function send_email($id)
        {
            $order=order::find($id);

            return view('admin.email_info',compact('order'));


        }

        // send email to the user

        public function send_user_email(Request $request, $id)
        {
            $order=order::find($id);
// ($details) comes from app\Notifications\SendEmailNotification.php
            $details = [
//  not we can send default email body by deleting:$request->body, and add the text in single qutation
                'greeting' => $request->greeting,

                'firstline' => $request->firstline,

                'body' => $request->body,

                'button' => $request->button,

                'url' => $request->url,

                'lastline' => $request->lastline,


            ];

            Notification::send($order,new SendEmailNotification($details));

            return redirect()->back();
        }



        // Searching data for orders
        public function searchdata(Request $request)

        {
            $searchText=$request->search;
     // searching by name or phone or any type
            $order=order::where('name','LIKE',"%$searchText%")->orWhere
            ('phone','LIKE',"%$searchText%")->orWhere
            ('product_title','LIKE',"%$searchText%")->get();

            return view('admin.order',compact('order'));
        }

}
