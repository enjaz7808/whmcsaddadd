<?php
/**
 * WhatsApp Cloud Webhook Endpoint
 * 
 * This file handles incoming webhooks from WhatsApp Cloud API
 * URL: https://enjaz-web.com/billing/modules/addons/whatsappcloud/webhook.php
 */

// Include WHMCS configuration
require_once '../../../configuration.php';
require_once '../../../init.php';
require_once 'lib/WhatsAppAPI.php';

// Set response headers
header('Content-Type: application/json');

// Get the request method
$method = $_SERVER['REQUEST_METHOD'];

// Handle webhook verification (GET request)
if ($method === 'GET') {
    $mode = $_GET['hub_mode'] ?? '';
    $token = $_GET['hub_verify_token'] ?? '';
    $challenge = $_GET['hub_challenge'] ?? '';
    
    // Get the verify token from addon configuration
    $query = "SELECT value FROM tbladdonmodules WHERE module = 'whatsappcloud' AND setting = 'webhook_verify_token'";
    $result = full_query($query);
    
    if ($result && $result->num_rows > 0) {
        $expectedToken = $result->fetch_assoc()['value'];
        
        if ($mode === 'subscribe' && $token === $expectedToken) {
            // Webhook verification successful
            http_response_code(200);
            echo $challenge;
            exit;
        }
    }
    
    // Webhook verification failed
    http_response_code(403);
    echo json_encode(['error' => 'Forbidden']);
    exit;
}

// Handle webhook data (POST request)
if ($method === 'POST') {
    // Get the raw POST data
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);
    
    // Log the webhook for debugging (optional)
    logActivity('WhatsApp Webhook Received: ' . $input);
    
    // Verify the webhook signature (recommended for security)
    $signature = $_SERVER['HTTP_X_HUB_SIGNATURE_256'] ?? '';
    if ($signature) {
        // Get app secret from configuration
        $query = "SELECT value FROM tbladdonmodules WHERE module = 'whatsappcloud' AND setting = 'app_secret'";
        $result = full_query($query);
        
        if ($result && $result->num_rows > 0) {
            $appSecret = $result->fetch_assoc()['value'];
            $expectedSignature = 'sha256=' . hash_hmac('sha256', $input, $appSecret);
            
            if (!hash_equals($expectedSignature, $signature)) {
                http_response_code(403);
                echo json_encode(['error' => 'Invalid signature']);
                exit;
            }
        }
    }
    
    // Process the webhook data
    if ($data && isset($data['object']) && $data['object'] === 'whatsapp_business_account') {
        // Get addon configuration
        $config = [];
        $query = "SELECT setting, value FROM tbladdonmodules WHERE module = 'whatsappcloud'";
        $result = full_query($query);
        
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $config[$row['setting']] = $row['value'];
            }
        }
        
        // Initialize WhatsApp API
        $api = new WhatsAppAPI($config);
        
        // Process the webhook
        $processed = $api->processWebhook($data);
        
        if ($processed) {
            http_response_code(200);
            echo json_encode(['status' => 'success']);
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Failed to process webhook']);
        }
    } else {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid webhook data']);
    }
    
    exit;
}

// Unsupported method
http_response_code(405);
echo json_encode(['error' => 'Method not allowed']);
exit;
?>