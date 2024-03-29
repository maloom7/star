<!DOCTYPE html>
<html>
   <head>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js" integrity="sha512-AA1Bzp5Q0K1KanKKmvN/4d3IRKVlv9PYgwFPvm32nPO6QS8yH1HO7LbgB1pgiOxPtfeg5zEn2ba64MUcqJx6CA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>



      <!-- Basic -->
      {{-- <base href="/public/home"> --}}
      <meta charset="utf-8" />
      <meta http-equiv="X-UA-Compatible" content="IE=edge" />
      <!-- Mobile Metas -->
      <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
      <!-- Site Metas -->
      <meta name="keywords" content="" />
      <meta name="description" content="" />
      <meta name="author" content="" />
      <link rel="shortcut icon" href="home/images/favicon.png" type="">
      <title>Mastore</title>
      <!-- bootstrap core css -->
      <link rel="stylesheet" type="text/css" href="{{asset('home/css/bootstrap.css')}}" />
      <!-- font awesome style -->
      <link href="{{asset('home/css/font-awesome.min.css')}}" rel="stylesheet" />
      <!-- Custom styles for this template -->
      <link href="{{asset('home/css/style.css')}}" rel="stylesheet" />
      <!-- responsive style -->
<link href="{{asset('home/css/responsive.css')}}" rel="stylesheet" />


      <style type="text/css">

      .center
      {
        margin:auto;
        width: 70%;
        text-align: center;
        padding: 30px;

      }
      table,th,td
      {
        border: 1px solid gray;
      }

      .th_deg
      {
        font-size: 20px;
        padding: 5px;
        background: skyblue;
      }

      .img_deg
      {
        height: 100px;
        width: 100px;
      }

      .total_deg
      {
        font-size: 20px;
        padding: 40px;
      }

      

      </style>


   </head>
   <body>

    {{-- Sweet Alerts --}}
    @include('sweetalert::alert')

      <div class="hero_area">
        @include('home.header')

        {{-- show successfully message --}}
        @if(session()->has('message'))
        <div class="alert alert-success">
      {{-- add the buttun that hide success message --}}
            <button type="button" class="close"
            data-dismiss="alert" aria-hidden="true">x</button>
           {{session()->get('message')}}
           
        </div>

        @endif
         


      <div class="center">
        <table>
            <tr>
                <th class="th_deg">Product Title</th>
                <th class="th_deg">Product Quantity</th>
                <th class="th_deg">Price</th>
                <th class="th_deg">Image</th>
                <th class="th_deg">Action</th>

            </tr>
            <?php $totalprice=null; ?>

            @foreach ($cart as $cart)
                
           

            <tr>
                <td>{{$cart->product_title}}</td>
                <td>{{$cart->quantity}}</td>
                <td>SAR {{$cart->price}}</td>
                <td><img class="img_deg" src="/public/product/{{$cart->image}}"></td>

                <td><a class="btn btn-danger" onclick="confirmation(event)" href="{{url('remove_cart',$cart->id)}}">Remove Product </a></td>

            </tr>
            <?php $totalprice=$totalprice + $cart->price ?>

            @endforeach


        </table>
        <div>

           <h1 class="total_deg">Total Price :  SAR {{$totalprice}}</h1> 

        </div>

        <div>

          <h1 style="font-size: 25px; padding-bottom:15px;">Proceed to Order</h1>

          <a href="{{url('cash_order')}}" class="btn btn-danger">Cash On Delivery</a>
          <a href="{{url('stripe',$totalprice)}}" class="btn btn-danger">Pay Using Card</a>


        </div>

      </div>

      <!-- footer start -->
      
      <!-- footer end -->

      <div class="cpy_">
         <p class="mx-auto">© 2021 All Rights Reserved By <a href="https://html.design/">Free Html Templates</a><br>
         
            Distributed By <a href="https://themewagon.com/" target="_blank">ThemeWagon</a>
         
         </p>
      </div>

      </div>

      {{-- delete confirmation alerts --}}
      <script>
        function confirmation(ev){
          ev.preventDefault();
          var urlToRedirect = ev.currentTarget.getAttribute('href');
          console.log(urlToRedirect);
          swal({
            title: "Are You Sure To Cancel This Product?",
            text: "You will note be able to revent this!",
            icon:"warning",
            buttons: true,
            dangerMode: true,
          })
          .then((willCancel) =>{
            if(willCancel){

              window.location.href = urlToRedirect;
            }
            
          });
        }

        </script>



      <!-- jQery -->
      <script src="home/js/jquery-3.4.1.min.js"></script>
      <!-- popper js -->
      <script src="home/js/popper.min.js"></script>
      <!-- bootstrap js -->
      <script src="home/js/bootstrap.js"></script>
      <!-- custom js -->
      <script src="home/js/custom.js"></script>
   </body>
</html>