<?php
include_once('../../config.php');
include_once('lib/stripe/init.php');
global $CFG;
\Stripe\Stripe::setApiKey('sk_test_51HL8cLBlE3i5wB41G5f11w6d2YsrgmHDI49lWMyJTeYrPL2cOfIhtKdNzE9AWvUJ86sAR1mhfndpVLPC58Tcb4wD000M8NpDmM');
// You can find your endpoint's secret in your webhook settings
$endpoint_secret = 'whsec_...';

$payload = @file_get_contents('php://input');
$sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'];
$event = null;

try {
    $event = \Stripe\Webhook::constructEvent(
        $payload, $sig_header, $endpoint_secret
    );
} catch(\UnexpectedValueException $e) {
    // Invalid payload
    http_response_code(400);
    exit();
} catch(\Stripe\Exception\SignatureVerificationException $e) {
    // Invalid signature
    http_response_code(400);
    exit();
}

if ($event->type == "payment_intent.succeeded") {
    $intent = $event->data->object;
    printf("Succeeded: %s", $intent->id);
    http_response_code(200);
    exit();
} elseif ($event->type == "payment_intent.payment_failed") {
    $intent = $event->data->object;
    $error_message = $intent->last_payment_error ? $intent->last_payment_error->message : "";
    printf("Failed: %s, %s", $intent->id, $error_message);
    http_response_code(200);
} 
?>