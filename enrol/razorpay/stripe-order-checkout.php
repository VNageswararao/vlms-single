<?php 
include_once('../../config.php');
include_once('lib/stripe/init.php');
global $CFG;
$jsonData=file_get_contents('php://input');
$data= json_decode($jsonData);
$cost=(int)$data->cost;
//echo"<pre>";print_r($data->coursename);die;
  /***********Stripe Payment configuration**************/  
\Stripe\Stripe::setApiKey('sk_test_51HL8cLBlE3i5wB41G5f11w6d2YsrgmHDI49lWMyJTeYrPL2cOfIhtKdNzE9AWvUJ86sAR1mhfndpVLPC58Tcb4wD000M8NpDmM');
$checkout_session = \Stripe\Checkout\Session::create([
  'payment_method_types' => ['card'],
  'line_items' => [[
    'price_data' => [
      'currency' =>"$data->currency",
      'unit_amount' => $data->cost*100,
      'product_data' => [
        'name' => "Thank you for choosing $data->coursename Course",
        'images' => ["$CFG->wwwroot/enrol/razorpay/pix/stripe.jpg"],
      ],
    ],
    'quantity' => 1,
  ]],
  'mode' => 'payment',
  'success_url' => "$CFG->wwwroot/enrol/razorpay/stripe-callback.php",
  'cancel_url' => $CFG->wwwroot,
]);
header('Content-Type: application/json');
echo json_encode(['id' => $checkout_session->id]);


?>