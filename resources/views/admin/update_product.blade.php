<!DOCTYPE html>
<html lang="en">
  <head>
    {{-- <base href="/public/home"> --}}
    <!-- Required meta tags -->
    @include ('admin.css')
    <style type="text/css">
    .div_center
    {
     text-align: center;
     padding-top: 40px;

    }

    .font_size
    {
        font-size: 40px;
        padding-bottom: 40px;
    }

    .text_color
    {
        color: black;
        padding-bottom: 20px;
    }

    label
    {
        display: inline-block;
        width: 200px;
    }

    .div_design
    {
        padding-bottom: 15px;
    }

    </style>
  </head>
  <body>
    <div class="container-scroller">
      <!-- partial:partials/_sidebar.html -->
      @include ('admin.sidebar')
      <!-- partial -->
      <div class="container-fluid page-body-wrapper">

        <!-- partial:partials/_navbar.html -->
        @include ('admin.header')

        <!-- partial -->
        <div class="main-panel">
            <div class="content-wrapper">
                {{-- show successfully message --}}
                @if(session()->has('message'))
                <div class="alert alert-success">
              {{-- add the buttun that hide success message --}}
                    <button type="button" class="close"
                    data-dismiss="alert" aria-hidden="true">x</button>
                   {{session()->get('message')}}
                </div>

                @endif 
                <div class="div_center">

                <h1 class="font_size">Update Product</h1>

                <form action="{{url('/update_product_confirm',$product->id)}}" method="POST" enctype="multipart/form-data">
                <div class="div_design">

                    @csrf 

                <label>Product Title</label>
                <input class="text_color" type="text" name="title" 
                placeholder="Write a title" required=""
                {{-- get the product title to update it --}}
                 value="{{$product->title}}">
                </div>

                <div class="div_design">

                    <label>Product Description</label>
                    <input class="text_color" type="text" name="description" 
                    placeholder="Write a description" required=""
                    value="{{$product->description}}">
                    </div>

                    <div class="div_design">

                        <div class="div_design">

                            <label>Product Quantity</label>
                            <input class="text_color" type="number" min="0" name="quantity" 
                            placeholder="Write a quantity" required=""
                            value="{{$product->quantity}}">
                            </div>


                        <label>Product Price</label>
                        <input class="text_color" type="number" name="price" 
                        placeholder="Write a price" required=""
                        value="{{$product->price}}">
                        </div>

                        
                            <div class="div_design">

                                <label>Discount Price</label>
                                <input class="text_color" type="number" name="discount_price" 
                                placeholder="Write a Discount is app" 
                                value="{{$product->discount_price}}">
                                </div>

                                <div class="div_design">

                                    <label>Product Category</label>
                                    <select class="text_color" name="category" required="">
                                        <option value="{{$product->category}}" selected="">
                                            {{$product->category}}</option>


                                           {{-- show category types in the update page --}}
                                            @foreach($category as $category)

                                        <option value="{{$category->category_name}}">
                                            {{$category->category_name}}</option>

                                        @endforeach

                                      
                                    </select>


                                    <div class="div_design">

                                        <label>Current Product Image</label>
                                        <img style="margin: auto;" height="100" width="100" src="/public/product/{{$product->image}}">
                                       
                                        </div>
                                    

                                    <div class="div_design">

                                        <label>Change Product Image</label>
                                        <input type="file" name="image" >
                                        </div>
                                    <div class="div_design">

                                        
                                        <input type="submit" value="Update Product" 
                                        class="btn btn-primary">
                                        </div>
                                    </form>


                </div>



            </div>
        </div>
       

    <!-- container-scroller -->

    <!-- plugins:js -->
    @include ('admin.script')

   <!-- End custom js for this page -->
  </body>
</html> 