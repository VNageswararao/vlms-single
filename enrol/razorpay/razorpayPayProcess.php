<?php
require("../../config.php");
require_once("lib.php");
include_once('lib/razorpay/Razorpay.php'); 
require_once($CFG->libdir.'/enrollib.php');
use Razorpay\Api\Api;
//echo"<pre>";print_r($_POST);die;
$receipt=$_POST['custom'];
$amount=(float)$_POST['amount']*100; 
$currency='INR';//$_POST['currency'];
$callback_url=$_POST['callback_url'];
$return=$_POST['return'];
$cancel_return=$_POST['cancel_return'];

$coursefullname=$_POST['coursefullname'];
$courseshortname=$_POST['courseshortname'];
$userid=$_POST['userid'];
$username=$_POST['username'];

$firstname=$_POST['firstname'];
$middlename=$_POST['middlename'];
$lastname=$_POST['lastname'];
$email=$_POST['email'];
$phone=$_POST['phone'];
$dob=$_POST['dob'];
$address=$_POST['address'];
$country=$_POST['country'];
$state=$_POST['state'];
$city=$_POST['city'];
$pincode=$_POST['pincode'];

$enrols = enrol_get_plugins(true);
$razorpayemail=$enrols['razorpay']->get_config('razorpayemail');
$api_key = $enrols['razorpay']->get_config('key_id');
$api_secret = $enrols['razorpay']->get_config('key_secret');
$api = new Api($api_key, $api_secret);
$order  = $api->order->create(
array(
    'receipt' =>$receipt, 
    'amount' =>$amount, 
    'currency' =>$currency)); // Creates order
//$customer = $api->customer->create(array('name' =>$username, 'email' =>$email)); // Creates customer
//echo"<pre>";print_r($order);die;
?>

<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
var options = {
    "key": "<?php echo $api_key; ?>", // Enter the Key ID generated from the Dashboard
    "amount": "<?php echo $order->amount; ?>", // Amount is in currency subunits. Default currency is INR. Hence, 50000 refers to 50000 paise
    "currency": "<?php echo $currency;  ?>",
    "name": "<?php echo "Payment for ".$coursefullname." Course"; ?>",
    "description": "<?php echo "Thank you for choosing for ".$coursefullname." Course"; ?>",
    "image": "<?php echo $CFG->wwwroot; ?>/enrol/razorpay/pix/icon.png",
    "order_id": "<?php echo $order->id; ?>", //This is a sample Order ID. Pass the `id` obtained in the response of Step 1
    "handler": function (response){
        alert(response.razorpay_payment_id);
        alert(response.razorpay_order_id);
        alert(response.razorpay_signature)
    },
    "prefill": {
        "name": "<?php echo $username; ?>",
        "email": "<?php echo $email; ?>",
        "contact": "<?php echo $phone; ?>"
    },
    "notes": {
        "firstname":"<?php echo $firstname; ?>",
        "middlename":"<?php echo $middlename; ?>",
        "lastname":"<?php echo $lastname; ?>",
        "address":"<?php echo $address; ?>",
        "dob":"<?php echo $dob; ?>",
        "country":"<?php echo $country; ?>",
        "state":"<?php echo $state; ?>",
        "city":"<?php echo $city; ?>",
        "pincode":"<?php echo $pincode; ?>",
    },
    "theme": {
        "color": "#3399cc"
    },
    callback_url:"<?php echo $callback_url; ?>",
    redirect:false,
    //customer_id:"<?php echo $customer; ?>",
    send_sms_hash:true
};
var rzp1 = new Razorpay(options);
rzp1.on('payment.failed', function (response){
        alert(response.error.code);
        alert(response.error.description);
        alert(response.error.source);
        alert(response.error.step);
        alert(response.error.reason);
        alert(response.error.metadata.order_id);
        alert(response.error.metadata.payment_id);
});


window.onload = function() {
rzp1.open();
    e.preventDefault();
};
</script>
