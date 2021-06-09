<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Listens for Instant Payment Notification from razorpay
 *
 * This script waits for Payment notification from razorpay,
 * then double checks that data by sending it back to razorpay.
 * If razorpay verifies this then it sets up the enrolment for that
 * user.
 *
 * @package    enrol_razorpay
 * @copyright 2010 Eugene Venter
 * @author     Eugene Venter - based on code by others
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Disable moodle specific debug messages and any errors in output,
// comment out when debugging or better look into error log!
define('NO_DEBUG_DISPLAY', true);

// @codingStandardsIgnoreLine This script does not require login.
require("../../config.php");
require_once("lib.php");
require_once($CFG->libdir.'/enrollib.php');
require_once($CFG->libdir . '/filelib.php');
require_once($CFG->dirroot.'/user/lib.php');
global $CFG,$DB;
include_once('lib/razorpay/Razorpay.php'); 
use Razorpay\Api\Api;

echo"<pre>";print_r($_GET);die;
// razorpay does not like when we return error messages here,
// the custom handler just logs exceptions and stops.

// Make sure we are enabled in the first place.
if (!enrol_is_enabled('razorpay')) {
    http_response_code(503);
    throw new moodle_exception('errdisabled', 'enrol_razorpay');
}

/// Keep out casual intruders
if (empty($_POST) or !empty($_GET)) {
    http_response_code(400);
    throw new moodle_exception('invalidrequest', 'core_error');
}

/// Read all the data from razorpay and get it ready for later;
/// we expect only valid UTF-8 encoding, it is the responsibility
/// of user to set it up properly in razorpay business account,
/// it is documented in docs wiki.



$data = new stdClass();


if(isset($_REQUEST['error'])){
$metadata= json_decode($_POST['error']['metadata']);
        $data->razorpay_order_id=$metadata->order_id;
        $data->razorpay_payment_id=$metadata->payment_id;
}else{
    $data->razorpay_order_id=$_REQUEST['razorpay_order_id'];
    $data->razorpay_payment_id=$_REQUEST['razorpay_payment_id'];
    $data->razorpay_signature=$_REQUEST['razorpay_signature'];
}
if (empty($data->razorpay_order_id)) {
    throw new moodle_exception('invalidrequest', 'core_error', '', null, 'Missing Order id request');
}else if(empty($data->razorpay_payment_id)){
  throw new moodle_exception('invalidrequest', 'core_error', '', null, 'Missing Payment id request');  
}

$enrols = enrol_get_plugins(true);
$api_key = $enrols['razorpay']->get_config('key_id');
$api_secret = $enrols['razorpay']->get_config('key_secret');
$api = new Api($api_key, $api_secret);
$order  = $api->order->fetch($data->razorpay_order_id);
$payment  = $api->payment->fetch($data->razorpay_payment_id); 

$custom = explode('-', $order->receipt);
//unset($order->receipt);

if (empty($custom) || count($custom) < 3) {
    throw new moodle_exception('invalidrequest', 'core_error', '', null, 'Invalid value of the request param: custom');
}


$customerDatails->firstname=$payment->notes['firstname'];
$customerDatails->middlename=$payment->notes['middlename'];
$customerDatails->lastname=$payment->notes['lastname'];
$customerDatails->email=$payment->email;
$customerDatails->phone=$payment->contact;
$customerDatails->idnumber=$payment->id;
$customerDatails->address=$payment->notes['address'];
$customerDatails->dob=$payment->notes['dob'];
$customerDatails->country=$payment->notes['country'];
$customerDatails->state=$payment->notes['state'];
$customerDatails->city=$payment->notes['city'];
$customerDatails->pincode=$payment->notes['pincode'];
$customerDatails->status=$order->status;

$data->userid           = (int)$custom[0];
$data->courseid         = (int)$custom[1];
$data->instanceid       = (int)$custom[2];


if($record=$DB->get_record('user',array('username'=>$customerDatails->email))){
$user = $DB->get_record("user", array("id" => $record->id), "*", MUST_EXIST);
$data->userid =$user->id;
}else{
 $customer=user_obj($customerDatails);
 $user=razorpay_create_user($customer);
 $data->userid=$user->id;
}
$data->order_status=$order->status;
$data->payment_status=$payment->status;

$data->amount    = $payment->amount;
$data->payment_currency = $payment->currency;
$data->invoice_id = $payment->invoice_id;


$data->fee=$payment->fee;
$data->tax=$payment->tax;



if($data->courseid){
  $course = $DB->get_record("course", array("id" => $data->courseid), "*", MUST_EXIST);  
}
if($course->id){
$context = context_course::instance($course->id, MUST_EXIST); 
$PAGE->set_context($context);
}
$plugin_instance = $DB->get_record("enrol", array("id" => $data->instanceid, "enrol" => "razorpay", "status" => 0), "*", MUST_EXIST);
$plugin = enrol_get_plugin('razorpay');

$data->item_name = $course->fullname;
$data->fee = $payment->fee;
$data->tax = $payment->tax;
$data->error_code = $payment->error_code;
$data->error_description = $payment->error_description;
$data->error_description = $payment->error_description;
$data->notes=json_encode($customerDatails);
$data->card_id = $payment->card_id;
$data->customerid ='';
$data->timeupdated      = time();
$data->method = $payment->method;
$data->amount_refunded = $payment->amount_refunded;
$data->refund_status = $payment->refund_status;
$data->customer_contact = $payment->contact;
$data->customer_email = $payment->email;
$data->created_at = $payment->created_at;
if(!isset($_REQUEST['error']) && $order->status=='paid'){
 $attributes  = array('razorpay_signature'  => $data->razorpay_signature,  'razorpay_payment_id'  => $data->razorpay_payment_id ,  'razorpay_order_id' => $data->razorpay_order_id);
$validate  = $api->utility->verifyPaymentSignature($attributes);

// Make sure this transaction doesn't exist already.
     if ($existing = $DB->get_record("enrol_razorpay", array("razorpay_order_id" => $order->id), "*", IGNORE_MULTIPLE)) {
         \enrol_razorpay\util::message_razorpay_error_to_admin("Transaction $payment->id is being repeated!",$data);
         die;
     }
        if (!$user = $DB->get_record('user',array('id'=>$data->userid))) {   // Check that user exists
            \enrol_razorpay\util::message_razorpay_error_to_admin("User $data->userid doesn't exist", $data);
            die;
        }
        if (!$course = $DB->get_record('course', array('id'=>$data->courseid))) { // Check that course exists
         \enrol_razorpay\util::message_razorpay_error_to_admin("Course $data->courseid doesn't exist", $data);
         die;
        }
        
        $coursecontext = context_course::instance($course->id, IGNORE_MISSING);
       
        // Check that amount paid is the correct amount
        if ( (float) $plugin_instance->cost <= 0 ) {
            $cost = (float) $plugin->get_config('cost');
        } else {
            $cost = (float) $plugin_instance->cost;
        }

        // Use the same rounding of floats as on the enrol form.
        $cost = format_float($cost, 2, false);

        if ($data->amount < $cost) {
            \enrol_razorpay\util::message_razorpay_error_to_admin("Amount paid is not enough ($data->amount < $cost))", $data);
            die;

        }
    
$DB->insert_record("enrol_razorpay", $data);

 if ($plugin_instance->enrolperiod) {
            $timestart = time();
            $timeend   = $timestart + $plugin_instance->enrolperiod;
        } else {
            $timestart = 0;
            $timeend   = 0;
        }

        // Enrol user
        $plugin->enrol_user($plugin_instance, $user->id, $plugin_instance->roleid, $timestart, $timeend);

        // Pass $view=true to filter hidden caps if the user cannot see them
        if ($users = get_users_by_capability($context, 'moodle/course:update', 'u.*', 'u.id ASC',
                                             '', '', '', '', false, true)) {
            $users = sort_by_roleassignment_authority($users, $context);
            $teacher = array_shift($users);
        } else {
            $teacher = false;
        }

        $mailstudents = $plugin->get_config('mailstudents');
        $mailteachers = $plugin->get_config('mailteachers');
        $mailadmins   = $plugin->get_config('mailadmins');
        $shortname = format_string($course->shortname, true, array('context' => $context));

        if (!empty($mailstudents)) {
            $a = new stdClass();
            $a->coursename = format_string($course->fullname, true, array('context' => $coursecontext));
            $a->profileurl = "$CFG->wwwroot/user/view.php?id=$user->id";
            $eventdata = new \core\message\message();
            $eventdata->courseid          = $course->id;
            $eventdata->modulename        = 'moodle';
            $eventdata->component         = 'enrol_razorpay';
            $eventdata->name              = 'razorpay_enrolment';
            $eventdata->userfrom          = empty($teacher) ? core_user::get_noreply_user() : $teacher;
            $eventdata->userto            = $user;
            $eventdata->subject           = get_string("enrolmentnew", 'enrol', $shortname);
            $eventdata->fullmessage       = get_string('welcometocoursetext', '', $a);
            $eventdata->fullmessageformat = FORMAT_PLAIN;
            $eventdata->fullmessagehtml   = '';
            $eventdata->smallmessage      = '';
            message_send($eventdata);
        }  
     
        if (!empty($mailteachers) && !empty($teacher)) {
            $a->course = format_string($course->fullname, true, array('context' => $coursecontext));
            $a->user = fullname($user);

            $eventdata = new \core\message\message();
            $eventdata->courseid          = $course->id;
            $eventdata->modulename        = 'moodle';
            $eventdata->component         = 'enrol_razorpay';
            $eventdata->name              = 'razorpay_enrolment';
            $eventdata->userfrom          = $user;
            $eventdata->userto            = $teacher;
            $eventdata->subject           = get_string("enrolmentnew", 'enrol', $shortname);
            $eventdata->fullmessage       = get_string('enrolmentnewuser', 'enrol', $a);
            $eventdata->fullmessageformat = FORMAT_PLAIN;
            $eventdata->fullmessagehtml   = '';
            $eventdata->smallmessage      = '';
            message_send($eventdata);
        }

        if (!empty($mailadmins)) {
            $a->course = format_string($course->fullname, true, array('context' => $coursecontext));
            $a->user = fullname($user);
            $admins = get_admins();
            foreach ($admins as $admin) {
                $eventdata = new \core\message\message();
                $eventdata->courseid          = $course->id;
                $eventdata->modulename        = 'moodle';
                $eventdata->component         = 'enrol_razorpay';
                $eventdata->name              = 'razorpay_enrolment';
                $eventdata->userfrom          = $user;
                $eventdata->userto            = $admin;
                $eventdata->subject           = get_string("enrolmentnew", 'enrol', $shortname);
                $eventdata->fullmessage       = get_string('enrolmentnewuser', 'enrol', $a);
                $eventdata->fullmessageformat = FORMAT_PLAIN;
                $eventdata->fullmessagehtml   = '';
                $eventdata->smallmessage      = '';
                message_send($eventdata);
            }
        }
   
      redirect($CFG->wwwroot.'/course/view.php?id='.$course->id);  
}else{
if($payment->status=='failed'){  
 $plugin->unenrol_user($plugin_instance, $data->userid); 
\enrol_razorpay\util::message_razorpay_error_to_admin("Status not completed or pending. User unenrolled from course",$data);
}
if ($payment->currency != $plugin_instance->currency) {
      // If currency is incorrectly set then someone maybe trying to cheat the system
\enrol_razorpay\util::message_razorpay_error_to_admin("Currency does not match course settings, received: ".$payment->currency,$data);die;
}

        if(isset($payment->error_code)){
        // If status is fails and reason is other than echeck then we are on hold until further notice
        // Email user to let them know. Email admin.
            $eventdata = new \core\message\message();
            $eventdata->courseid          = empty($data->courseid) ? SITEID : $data->courseid;
            $eventdata->modulename        = 'moodle';
            $eventdata->component         = 'enrol_razorpay';
            $eventdata->name              = 'razorpay_enrolment';
            $eventdata->userfrom          = get_admin();
            $eventdata->userto            = $user;
            $eventdata->subject           = "Moodle: razorpay payment";
            $eventdata->fullmessage       = "Your razorpay payment is pending.";
            $eventdata->fullmessageformat = FORMAT_PLAIN;
            $eventdata->fullmessagehtml   = '';
            $eventdata->smallmessage      = '';
            message_send($eventdata);
            \enrol_razorpay\util::message_razorpay_error_to_admin("Payment Fails", $data);
 
}
   if ($existing = $DB->get_record("enrol_razorpay", array("razorpay_order_id" => $order->id), "*", IGNORE_MULTIPLE)) {
         \enrol_razorpay\util::message_razorpay_error_to_admin("Transaction $payment->id is being repeated!",$data);
     }else{
        $DB->insert_record("enrol_razorpay", $data); 
     }
    redirect($CFG->wwwroot); 

}
    
function razorpay_create_user($userdata=array()){
    global $DB;
  if (isset($userdata->id)) {
        user_update_user($userdata, false, false);
        $userid = $userdata->id;
        } else {          
        $userid = user_create_user($userdata, false, true);
        }
        $user = $DB->get_record('user', array('id' => $userid));
        return $user;
}


function user_obj($user){
        global $CFG; 
        $userdata = new stdClass();
        $password="Pass@123";
        $userdata->auth = 'manual';
        $userdata->username =$user->email;
        $userdata->password=hash_internal_user_password($password);
        $userdata->firstname =$user->firstname?$user->firstname:"user";
        $userdata->lastname =$user->lastname?$user->lastname:"01";
        $userdata->phone1 = $user->phone;
        $userdata->email =$user->email;
        $userdata->idnumber =$user->idnumber;
        $userdata->country=$user->country?$user->country:'IN';
        $userdata->city=$user->city;
         if($user->status=='paid'){
        $userdata->confirmed=1;  
        }else{
        $userdata->confirmed=0;  
        }
        $userdata->lang = 'en';
        $userdata->mnethostid = $CFG->mnet_localhost_id; // Always local user.
        $userdata->timecreated = time();
        $userdata->lastlogin = time();
        return $userdata;
       
   }