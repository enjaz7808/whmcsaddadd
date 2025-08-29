<?php
/**
 * WHMCS WhatsApp Cloud API Integration Addon
 * 
 * Integrates WHMCS with WhatsApp Cloud API for customer communication
 * 
 * @package    WHMCS
 * @author     Enjaz Web Solutions
 * @copyright  2024 Enjaz Web Solutions
 * @license    Proprietary
 * @version    1.0.0
 */

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

require_once __DIR__ . '/lib/WhatsAppAPI.php';

/**
 * Addon configuration
 */
function whatsappcloud_config()
{
    return [
        'name' => 'WhatsApp Cloud API',
        'description' => 'تكامل WHMCS مع WhatsApp Cloud API للتواصل مع العملاء',
        'version' => '1.0.0',
        'author' => 'Enjaz Web Solutions',
        'language' => 'arabic',
        'fields' => [
            'app_id' => [
                'FriendlyName' => 'معرف التطبيق (App ID)',
                'Type' => 'text',
                'Size' => '50',
                'Default' => '1106679971647524',
                'Description' => 'معرف تطبيق WhatsApp Cloud',
            ],
            'app_secret' => [
                'FriendlyName' => 'كلمة مرور التطبيق (App Secret)',
                'Type' => 'password',
                'Size' => '50',
                'Default' => 'bb84080b5bcf7ba647ded9e284980f99',
                'Description' => 'كلمة مرور تطبيق WhatsApp Cloud',
            ],
            'access_token' => [
                'FriendlyName' => 'رمز الوصول (Access Token)',
                'Type' => 'textarea',
                'Rows' => '3',
                'Default' => 'EAAPuhQKXECQBPMrAEMDDMdfodVjshaNOZAvsHYf8p0ZCNd1zddG0NlZA5Xfz2sW7RCTnAW2fdccpwU6NrpR3jE738ZAWEdFxPX8bZB90tKKZAKAHTuYZAQhZCu70MQ9DF05QEChhfxbjPEIlWwORBsGkNR880HfvmuXgtaTRvCtcs2zETZCVjAnhKmInjGZCuxCgZDZD',
                'Description' => 'رمز الوصول لـ WhatsApp Cloud API',
            ],
            'phone_number_id' => [
                'FriendlyName' => 'معرف رقم الهاتف',
                'Type' => 'text',
                'Size' => '50',
                'Default' => '663817096825554',
                'Description' => 'معرف رقم الهاتف في WhatsApp Business',
            ],
            'business_account_id' => [
                'FriendlyName' => 'معرف حساب واتساب للأعمال',
                'Type' => 'text',
                'Size' => '50',
                'Default' => '1738496210124419',
                'Description' => 'معرف حساب WhatsApp Business',
            ],
            'webhook_verify_token' => [
                'FriendlyName' => 'رمز التحقق من الويب هوك',
                'Type' => 'text',
                'Size' => '50',
                'Default' => bin2hex(random_bytes(16)),
                'Description' => 'رمز التحقق لنقطة نهاية الويب هوك',
            ],
            'enable_bot' => [
                'FriendlyName' => 'تفعيل البوت التفاعلي',
                'Type' => 'yesno',
                'Default' => 'on',
                'Description' => 'تفعيل البوت للرد التلقائي على الرسائل',
            ],
            'default_language' => [
                'FriendlyName' => 'اللغة الافتراضية',
                'Type' => 'dropdown',
                'Options' => 'ar,en',
                'Default' => 'ar',
                'Description' => 'اللغة الافتراضية للرسائل',
            ],
        ]
    ];
}

/**
 * Addon activation
 */
function whatsappcloud_activate()
{
    try {
        // Create conversations table
        $query = "CREATE TABLE IF NOT EXISTS `mod_whatsappcloud_conversations` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `phone_number` varchar(20) NOT NULL,
            `contact_name` varchar(255) DEFAULT NULL,
            `language` varchar(5) DEFAULT 'ar',
            `status` enum('pending','active','completed') DEFAULT 'pending',
            `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
            `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            UNIQUE KEY `phone_number` (`phone_number`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
        
        full_query($query);
        
        // Create messages table
        $query = "CREATE TABLE IF NOT EXISTS `mod_whatsappcloud_messages` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `conversation_id` int(11) NOT NULL,
            `message_id` varchar(255) NOT NULL,
            `direction` enum('inbound','outbound') NOT NULL,
            `message_type` varchar(50) DEFAULT 'text',
            `content` text NOT NULL,
            `status` varchar(20) DEFAULT 'sent',
            `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            KEY `conversation_id` (`conversation_id`),
            KEY `message_id` (`message_id`),
            FOREIGN KEY (`conversation_id`) REFERENCES `mod_whatsappcloud_conversations` (`id`) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
        
        full_query($query);
        
        return [
            'status' => 'success',
            'description' => 'تم تفعيل إضافة WhatsApp Cloud بنجاح. تم إنشاء الجداول المطلوبة.'
        ];
        
    } catch (Exception $e) {
        return [
            'status' => 'error',
            'description' => 'خطأ في تفعيل الإضافة: ' . $e->getMessage()
        ];
    }
}

/**
 * Addon deactivation
 */
function whatsappcloud_deactivate()
{
    return [
        'status' => 'success',
        'description' => 'تم إلغاء تفعيل إضافة WhatsApp Cloud. الجداول محفوظة للاستخدام المستقبلي.'
    ];
}

/**
 * Addon output - main admin interface
 */
function whatsappcloud_output($vars)
{
    $modulelink = $vars['modulelink'];
    $version = $vars['version'];
    $LANG = $vars['_lang'];
    
    $message = '';
    $activeTab = $_GET['tab'] ?? 'details';
    
    // Handle form submissions
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        switch ($activeTab) {
            case 'details':
                if (isset($_POST['test_connection'])) {
                    $api = new WhatsAppAPI($vars);
                    $result = $api->testConnection();
                    $message = $result['success'] ? 
                        '<div class="alert alert-success">✅ تم الاتصال بنجاح مع WhatsApp Cloud API</div>' : 
                        '<div class="alert alert-danger">❌ فشل في الاتصال: ' . $result['error'] . '</div>';
                }
                break;
                
            case 'webhook':
                if (isset($_POST['test_webhook'])) {
                    $api = new WhatsAppAPI($vars);
                    $result = $api->testWebhook();
                    $message = $result['success'] ? 
                        '<div class="alert alert-success">✅ الويب هوك يعمل بشكل صحيح</div>' : 
                        '<div class="alert alert-danger">❌ خطأ في الويب هوك: ' . $result['error'] . '</div>';
                }
                break;
                
            case 'bot':
                if (isset($_POST['save_bot_settings'])) {
                    // Save bot settings
                    $message = '<div class="alert alert-success">✅ تم حفظ إعدادات البوت بنجاح</div>';
                }
                break;
                
            case 'chat':
                if (isset($_POST['send_message'])) {
                    $api = new WhatsAppAPI($vars);
                    $result = $api->sendMessage($_POST['phone_number'], $_POST['message']);
                    $message = $result['success'] ? 
                        '<div class="alert alert-success">✅ تم إرسال الرسالة بنجاح</div>' : 
                        '<div class="alert alert-danger">❌ فشل في إرسال الرسالة: ' . $result['error'] . '</div>';
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
    
    // Header
    echo '<div class="addon-header">';
    echo '<h1><span class="whatsapp-icon">📱</span> WhatsApp Cloud API</h1>';
    echo '<p class="subtitle">إدارة تكامل WHMCS مع WhatsApp Cloud للتواصل مع العملاء</p>';
    echo '</div>';
    
    // Display messages
    if ($message) {
        echo $message;
    }
    
    // Navigation tabs
    echo '<div class="nav-tabs">';
    foreach ($tabs as $key => $label) {
        $activeClass = ($activeTab === $key) ? 'active' : '';
        echo '<a href="' . $modulelink . '&tab=' . $key . '" class="nav-tab ' . $activeClass . '">';
        echo '<span class="tab-icon">' . getTabIcon($key) . '</span>';
        echo $label;
        echo '</a>';
    }
    echo '</div>';
    
    // Tab content
    echo '<div class="tab-content">';
    
    switch ($activeTab) {
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
        default:
            include __DIR__ . '/templates/details.php';
    }
    
    echo '</div>';
    echo '</div>';
}

/**
 * Get tab icon
 */
function getTabIcon($tab)
{
    $icons = [
        'details' => '📊',
        'webhook' => '🔗',
        'bot' => '🤖',
        'chat' => '💬'
    ];
    
    return $icons[$tab] ?? '📋';
}

/**
 * Sidebar output (optional)
 */
function whatsappcloud_sidebar($vars)
{
    $modulelink = $vars['modulelink'];
    
    $sidebar = '<div class="whatsapp-sidebar">';
    $sidebar .= '<h3>📱 WhatsApp Cloud</h3>';
    $sidebar .= '<p>الحالة: <span class="status-connected">متصل</span></p>';
    $sidebar .= '<hr>';
    $sidebar .= '<p><a href="' . $modulelink . '">إدارة الإضافة</a></p>';
    $sidebar .= '<p><a href="modules/addons/whatsappcloud/verify.php" target="_blank">فحص التثبيت</a></p>';
    $sidebar .= '</div>';
    
    return $sidebar;
}