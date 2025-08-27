<?php
header("Content-Type: application/json");

// Static values from TrackDrive instructions
$trackdrive_number = "+18882482658";
$traffic_source_id = "12281";

// Collect form data safely
$first_name  = $_POST['first_name'] ?? '';
$last_name   = $_POST['last_name'] ?? '';
$email       = $_POST['email'] ?? '';
$caller_id   = $_POST['caller_id'] ?? '';
$address     = $_POST['address'] ?? '';
$city        = $_POST['city'] ?? '';
$state       = $_POST['state'] ?? '';
$zip         = $_POST['zip'] ?? '';
$tcpa_opt_in = isset($_POST['tcpa_opt_in']) ? true : false;

// STEP 1: PING request
$ping_url = "https://kp-consulting-services-and-products.trackdrive.com/api/v1/inbound_webhooks/ping/check_for_available_buyers_on_fe_transfer?trackdrive_number=" . urlencode($trackdrive_number) . "&traffic_source_id=" . urlencode($traffic_source_id);

$ping_response = file_get_contents($ping_url);
$ping_data = json_decode($ping_response, true);

if (!$ping_data || !$ping_data['success']) {
    echo json_encode([
        "success" => false,
        "message" => "No buyer available. Please try again later.",
        "ping_response" => $ping_data
    ], JSON_PRETTY_PRINT);
    exit;
}

// Extract ping_id
$ping_id = $ping_data['try_all_buyers']['ping_id'] ?? null;
if (!$ping_id) {
    echo json_encode([
        "success" => false,
        "message" => "Ping ID missing in response.",
        "ping_response" => $ping_data
    ], JSON_PRETTY_PRINT);
    exit;
}

// STEP 2: POST request with required fields
$post_url = "https://kp-consulting-services-and-products.trackdrive.com/api/v1/inbound_webhooks/post/check_for_available_buyers_on_fe_transfer";

$post_fields = [
    "trackdrive_number" => $trackdrive_number,
    "traffic_source_id" => $traffic_source_id,
    "ping_id"           => $ping_id,
    "first_name"        => $first_name,
    "last_name"         => $last_name,
    "email"             => $email,
    "caller_id"         => $caller_id,
    "address"           => $address,
    "city"              => $city,
    "state"             => $state,
    "zip"               => $zip,
    "tcpa_opt_in"       => $tcpa_opt_in
];

// Use cURL for POST
$ch = curl_init($post_url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields);

$response = curl_exec($ch);
curl_close($ch);

// Return JSON response
echo $response;
