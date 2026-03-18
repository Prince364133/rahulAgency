<?php
// Secure Backend for Google Sheets Integration
header('Content-Type: application/json');

// 1. Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Method Not Allowed']);
    exit;
}

require_once(__DIR__ . '/ip_utils.php');

// 2. Collection and Sanitization
// Handle both uppercase and lowercase keys efficiently
function get_post($key, $default = '') {
    return $_POST[strtoupper($key)] ?? $_POST[strtolower($key)] ?? $default;
}

$date = get_post('DATE', date('Y-m-d H:i:s'));
$rawName = get_post('NAME');
$rawPhone = get_post('PHONENO', get_post('phone'));
$rawAddress = get_post('ADDRESS', get_post('LOCATION', get_post('location')));

// Capture user metadata automatically
$ipAddress = get_real_ip_address();
$browser = get_post('BROWSER', 'UNKNOWN');
$platform = get_post('PLATFORM', 'UNKNOWN');
$screenResolution = get_post('SCREEN_RESOLUTION', 'UNKNOWN');
$language = get_post('LANGUAGE', 'UNKNOWN');
$timezone = get_post('TIMEZONE', 'UNKNOWN');
$referrer = get_post('REFERRER', 'UNKNOWN');

// Sanitize inputs
$name = htmlspecialchars(strip_tags(trim($rawName)), ENT_QUOTES, 'UTF-8');
$phone = preg_replace('/[^0-9]/', '', $rawPhone);
$address = htmlspecialchars(strip_tags(trim($rawAddress)), ENT_QUOTES, 'UTF-8');

// 3. Validation
if (empty($name) || strlen($phone) !== 10) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid data: Name is required and Phone must be 10 digits.']);
    exit;
}

// 4. Google Apps Script Web App URL
$googleScriptUrl = 'https://script.google.com/macros/s/AKfycbxn2aYubFbN8o8rQcF6_8Px4-VGJSPRaGk7dzBzck88jzvbNzrVM3hOto_5ttPsOPOj/exec';


if ($googleScriptUrl === 'YOUR_GOOGLE_SCRIPT_WEB_APP_URL_HERE' || empty($googleScriptUrl)) {
    echo json_encode(['status' => 'error', 'message' => 'Backend Error: Google Script URL is not set in api.php.']);
    exit;
}

// 5. Forward data to Google Sheets via cURL
$data = [
    'DATE' => $date,
    'NAME' => $name,
    'PHONENO' => $phone,
    'ADDRESS' => $address,
    'IP_ADDRESS' => $ipAddress,
    'BROWSER' => $browser,
    'PLATFORM' => $platform,
    'SCREEN_RESOLUTION' => $screenResolution,
    'LANGUAGE' => $language,
    'TIMEZONE' => $timezone,
    'REFERRER' => $referrer
];


$ch = curl_init($googleScriptUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_POSTREDIR, 3); // Maintain POST on 301/302 redirects
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
curl_setopt($ch, CURLOPT_TIMEOUT, 20);


$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

// 6. Return response to frontend
if ($error) {
    echo json_encode(['status' => 'error', 'message' => 'Connection Error: ' . $error]);
} else if ($httpCode >= 400) {
    echo json_encode(['status' => 'error', 'message' => 'Google Sheets API Error (HTTP ' . $httpCode . ')']);
} else {
    // If response is empty or not JSON, check it
    if (empty($response)) {
        echo json_encode(['status' => 'error', 'message' => 'Empty response from Google Sheets. Check your Apps Script deployment.']);
    } else {
        // Pass through the response (should be JSON from Apps Script)
        echo $response;
    }
}
?>
