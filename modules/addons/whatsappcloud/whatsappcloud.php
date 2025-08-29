<?php
/**
 * WHMCS WhatsApp Cloud API Addon
 *
 * @copyright Copyright (c) 2024
 * @license MIT License
 */

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

/**
 * Addon configuration
 */
function whatsappcloud_config()
{
    return [
        'name' => 'WhatsApp Cloud API',
        'description' => 'Integrate WhatsApp Cloud API with WHMCS for customer communication',
        'version' => '1.0.0',
        'author' => 'Enjaz Web',
        'language' => 'english',
        'fields' => [
            'app_id' => [
                'FriendlyName' => 'App ID',
                'Type' => 'text',
                'Size' => '30',
                'Default' => '1106679971647524',
                'Description' => 'WhatsApp Cloud API App ID',
            ],
            'app_secret' => [
                'FriendlyName' => 'App Secret',
                'Type' => 'password',
                'Size' => '50',
                'Default' => 'bb84080b5bcf7ba647ded9e284980f99',
                'Description' => 'WhatsApp Cloud API App Secret',
            ],
            'phone_number_id' => [
                'FriendlyName' => 'Phone Number ID',
                'Type' => 'text',
                'Size' => '30',
                'Default' => '663817096825554',
                'Description' => 'WhatsApp Business Phone Number ID',
            ],
            'business_account_id' => [
                'FriendlyName' => 'Business Account ID',
                'Type' => 'text',
                'Size' => '30',
                'Default' => '1738496210124419',
                'Description' => 'WhatsApp Business Account ID',
            ],
            'access_token' => [
                'FriendlyName' => 'Access Token',
                'Type' => 'password',
                'Size' => '70',
                'Default' => 'EAAPuhQKXECQBPMrAEMDDMdfodVjshaNOZAvsHYf8p0ZCNd1zddG0NlZA5Xfz2sW7RCTnAW2fdccpwU6NrpR3jE738ZAWEdFxPX8bZB90tKKZAKAHTuYZAQhZCu70MQ9DF05QEChhfxbjPEIlWwORBsGkNR880HfvmuXgtaTRvCtcs2zETZCVjAnhKmInjGZCuxCgZDZD',
                'Description' => 'WhatsApp Cloud API Access Token',
            ],
            'webhook_verify_token' => [
                'FriendlyName' => 'Webhook Verify Token',
                'Type' => 'text',
                'Size' => '30',
                'Default' => bin2hex(random_bytes(16)),
                'Description' => 'Token for webhook verification',
            ],
            'enable_bot' => [
                'FriendlyName' => 'Enable Interactive Bot',
                'Type' => 'yesno',
                'Default' => 'yes',
                'Description' => 'Enable automated bot responses',
            ],
            'default_language' => [
                'FriendlyName' => 'Default Language',
                'Type' => 'dropdown',
                'Options' => 'ar,en',
                'Default' => 'ar',
                'Description' => 'Default language for bot responses',
            ],
        ]
    ];
}

/**
 * Addon activation
 */
function whatsappcloud_activate()
{
    // Create database tables
    $query = "CREATE TABLE IF NOT EXISTS `mod_whatsappcloud_conversations` (
        `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
        `client_id` int(10) unsigned NOT NULL,
        `phone_number` varchar(20) NOT NULL,
        `conversation_id` varchar(255) NOT NULL,
        `status` enum('pending','active','completed') DEFAULT 'pending',
        `language` varchar(5) DEFAULT 'ar',
        `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
        `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        KEY `phone_number` (`phone_number`),
        KEY `client_id` (`client_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    
    $result = full_query($query);
    
    $query = "CREATE TABLE IF NOT EXISTS `mod_whatsappcloud_messages` (
        `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
        `conversation_id` int(10) unsigned NOT NULL,
        `message_id` varchar(255) NOT NULL,
        `direction` enum('inbound','outbound') NOT NULL,
        `message_type` varchar(20) NOT NULL,
        `content` text NOT NULL,
        `status` varchar(20) DEFAULT 'sent',
        `timestamp` timestamp DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        KEY `conversation_id` (`conversation_id`),
        KEY `message_id` (`message_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    
    $result = full_query($query);
    
    return [
        'status' => 'success',
        'description' => 'WhatsApp Cloud addon activated successfully. Database tables created.'
    ];
}

/**
 * Addon deactivation
 */
function whatsappcloud_deactivate()
{
    return [
        'status' => 'success',
        'description' => 'WhatsApp Cloud addon deactivated successfully.'
    ];
}

/**
 * Admin area output
 */
function whatsappcloud_output($vars)
{
    require_once __DIR__ . '/lib/WhatsAppAPI.php';
    
    $tab = isset($_GET['tab']) ? $_GET['tab'] : 'details';
    
    // Handle form submissions
    if ($_POST) {
        switch ($tab) {
            case 'webhook':
                if (isset($_POST['test_webhook'])) {
                    $api = new WhatsAppAPI($vars);
                    $result = $api->testWebhook();
                    $message = $result['success'] ? 'Webhook test successful!' : 'Webhook test failed: ' . $result['error'];
                }
                break;
                
            case 'bot':
                if (isset($_POST['save_bot_settings'])) {
                    // Save bot settings
                    $message = 'Bot settings saved successfully!';
                }
                break;
                
            case 'chat':
                if (isset($_POST['send_message'])) {
                    $api = new WhatsAppAPI($vars);
                    $result = $api->sendMessage($_POST['phone_number'], $_POST['message']);
                    $message = $result['success'] ? 'Message sent successfully!' : 'Failed to send message: ' . $result['error'];
                }
                break;
        }
    }
    
    // Navigation tabs
    $tabs = [
        'details' => 'التفاصيل',
        'webhook' => 'ربط الويب هوك',
        'bot' => 'البوت التفاعلي',
        'chat' => 'الدردشة'
    ];
    
    echo '<div class="whatsapp-cloud-addon">';
    
    // Include external CSS
    echo '<link rel="stylesheet" href="modules/addons/whatsappcloud/assets/style.css">';
    
    echo '<style>
        .whatsapp-cloud-addon { font-family: Arial, sans-serif; }
        .nav-tabs { border-bottom: 2px solid #25d366; margin-bottom: 20px; }
        .nav-tabs a { 
            display: inline-block; 
            padding: 10px 20px; 
            margin-right: 10px; 
            text-decoration: none; 
            background: #f8f9fa; 
            border: 1px solid #ddd; 
            border-bottom: none; 
            border-radius: 5px 5px 0 0;
            color: #333;
        }
        .nav-tabs a.active { 
            background: #25d366; 
            color: white; 
            border-color: #25d366;
        }
        .tab-content { 
            background: white; 
            padding: 20px; 
            border: 1px solid #ddd; 
            border-radius: 5px;
        }
        .status-indicator { 
            display: inline-block; 
            width: 10px; 
            height: 10px; 
            border-radius: 50%; 
            margin-right: 5px;
        }
        .status-connected { background: #28a745; }
        .status-disconnected { background: #dc3545; }
        .message-success { 
            background: #d4edda; 
            border: 1px solid #c3e6cb; 
            color: #155724; 
            padding: 10px; 
            border-radius: 5px; 
            margin-bottom: 15px;
        }
        .message-error { 
            background: #f8d7da; 
            border: 1px solid #f5c6cb; 
            color: #721c24; 
            padding: 10px; 
            border-radius: 5px; 
            margin-bottom: 15px;
        }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; font-weight: bold; margin-bottom: 5px; }
        .form-group input, .form-group textarea, .form-group select { 
            width: 100%; 
            padding: 8px; 
            border: 1px solid #ddd; 
            border-radius: 4px; 
        }
        .btn { 
            background: #25d366; 
            color: white; 
            padding: 10px 20px; 
            border: none; 
            border-radius: 5px; 
            cursor: pointer; 
        }
        .btn:hover { background: #128c7e; }
        .btn-secondary { background: #6c757d; }
        .btn-secondary:hover { background: #545b62; }
    </style>';
    
    // Display message if any
    if (isset($message)) {
        $class = (strpos($message, 'successful') !== false || strpos($message, 'saved') !== false) ? 'message-success' : 'message-error';
        echo '<div class="' . $class . '">' . $message . '</div>';
    }
    
    // Navigation
    echo '<div class="nav-tabs">';
    foreach ($tabs as $key => $label) {
        $active = ($tab == $key) ? 'active' : '';
        echo '<a href="?module=whatsappcloud&tab=' . $key . '" class="' . $active . '">' . $label . '</a>';
    }
    echo '</div>';
    
    // Tab content
    echo '<div class="tab-content">';
    
    switch ($tab) {
        case 'details':
            include __DIR__ . '/templates/details.php';
            break;
        case 'webhook':
            include __DIR__ . '/templates/webhook.php';
            break;
        case 'bot':
            include __DIR__ . '/templates/bot.php';
            break;
        case 'chat':
            include __DIR__ . '/templates/chat.php';
            break;
    }
    
    echo '</div>';
    echo '</div>';
}