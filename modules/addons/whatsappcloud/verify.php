<?php
/**
 * WhatsApp Cloud Addon Installation Verification Script
 * 
 * Run this script to verify the addon installation and configuration
 * Access: https://yourdomain.com/modules/addons/whatsappcloud/verify.php
 */

// Include WHMCS configuration
$whmcsPath = '../../../';
if (file_exists($whmcsPath . 'configuration.php')) {
    require_once $whmcsPath . 'configuration.php';
    require_once $whmcsPath . 'init.php';
} else {
    die('❌ WHMCS configuration not found. Please check the path.');
}

// Start output
echo '<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WhatsApp Cloud Addon - Installation Verification</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f8f9fa; }
        .container { max-width: 1200px; margin: 0 auto; }
        .header { background: linear-gradient(135deg, #25D366 0%, #128C7E 100%); color: white; padding: 20px; border-radius: 8px; margin-bottom: 20px; text-align: center; }
        .section { background: white; padding: 20px; margin-bottom: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .check { color: #28a745; font-weight: bold; }
        .error { color: #dc3545; font-weight: bold; }
        .warning { color: #ffc107; font-weight: bold; }
        .info { color: #17a2b8; font-weight: bold; }
        .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; }
        .status-card { padding: 15px; border-radius: 6px; }
        .status-success { background: #d4edda; border: 1px solid #c3e6cb; }
        .status-error { background: #f8d7da; border: 1px solid #f5c6cb; }
        .status-warning { background: #fff3cd; border: 1px solid #ffeaa7; }
        .code { background: #f8f9fa; padding: 10px; border-radius: 4px; font-family: monospace; font-size: 14px; }
        h1 { margin: 0; font-size: 24px; }
        h2 { color: #495057; border-bottom: 2px solid #25D366; padding-bottom: 10px; }
        h3 { color: #6c757d; }
    </style>
</head>
<body>
<div class="container">';

echo '<div class="header">
<h1>📱 WhatsApp Cloud Addon</h1>
<p>فحص التثبيت والتكوين</p>
</div>';

// 1. Check WHMCS version and compatibility
echo '<div class="section">
<h2>🔧 فحص توافق النظام</h2>';

$whmcsVersion = '';
if (defined('WHMCS_VERSION')) {
    $whmcsVersion = WHMCS_VERSION;
    echo '<p class="check">✅ إصدار WHMCS: ' . $whmcsVersion . '</p>';
} else {
    echo '<p class="error">❌ لا يمكن تحديد إصدار WHMCS</p>';
}

$phpVersion = PHP_VERSION;
echo '<p class="check">✅ إصدار PHP: ' . $phpVersion . '</p>';

if (extension_loaded('curl')) {
    echo '<p class="check">✅ مكتبة cURL متوفرة</p>';
} else {
    echo '<p class="error">❌ مكتبة cURL غير متوفرة (مطلوبة)</p>';
}

if (extension_loaded('mysqli')) {
    echo '<p class="check">✅ مكتبة MySQLi متوفرة</p>';
} else {
    echo '<p class="error">❌ مكتبة MySQLi غير متوفرة (مطلوبة)</p>';
}

echo '</div>';

// 2. Check addon installation
echo '<div class="section">
<h2>📦 فحص تثبيت الإضافة</h2>';

try {
    $query = "SELECT * FROM tbladdonmodules WHERE module = 'whatsappcloud'";
    $result = full_query($query);
    
    if ($result && $result->num_rows > 0) {
        echo '<p class="check">✅ الإضافة مُثبتة في WHMCS</p>';
        
        echo '<h3>⚙️ إعدادات الإضافة:</h3>';
        echo '<div class="grid">';
        
        while ($row = $result->fetch_assoc()) {
            $statusClass = !empty($row['value']) ? 'status-success' : 'status-warning';
            $statusIcon = !empty($row['value']) ? '✅' : '⚠️';
            
            echo '<div class="status-card ' . $statusClass . '">';
            echo '<strong>' . $statusIcon . ' ' . htmlspecialchars($row['setting']) . '</strong><br>';
            
            if ($row['setting'] === 'access_token' || $row['setting'] === 'app_secret') {
                echo '<code>' . (!empty($row['value']) ? str_repeat('*', 20) . ' (مُعين)' : 'غير مُعين') . '</code>';
            } else {
                echo '<code>' . htmlspecialchars($row['value'] ?: 'غير مُعين') . '</code>';
            }
            echo '</div>';
        }
        echo '</div>';
        
    } else {
        echo '<p class="error">❌ الإضافة غير مُثبتة في WHMCS</p>';
        echo '<p>يرجى تفعيل الإضافة من: الإعدادات → وحدات الإضافة → WhatsApp Cloud API</p>';
    }
} catch (Exception $e) {
    echo '<p class="error">❌ خطأ في قاعدة البيانات: ' . $e->getMessage() . '</p>';
}
echo '</div>';

// 3. Check database tables
echo '<div class="section">
<h2>🗄️ فحص جداول قاعدة البيانات</h2>';

$tables = [
    'mod_whatsappcloud_conversations' => 'جدول المحادثات',
    'mod_whatsappcloud_messages' => 'جدول الرسائل'
];

foreach ($tables as $table => $description) {
    echo '<p>';
    try {
        $query = "SHOW TABLES LIKE '$table'";
        $result = full_query($query);
        
        if ($result && $result->num_rows > 0) {
            echo '<span class="check">✅ ' . $description . ' موجود</span>';
            
            // Check table structure
            $query = "DESCRIBE $table";
            $result = full_query($query);
            if ($result) {
                echo ' (' . $result->num_rows . ' عمود)';
            }
        } else {
            echo '<span class="error">❌ ' . $description . ' مفقود</span>';
        }
    } catch (Exception $e) {
        echo '<span class="error">❌ خطأ في فحص ' . $description . '</span>';
    }
    echo '</p>';
}
echo '</div>';

// 4. Check webhook endpoint
echo '<div class="section">
<h2>🌐 فحص نقطة نهاية الويب هوك</h2>';

$webhookUrl = 'https://enjaz-web.com/billing/modules/addons/whatsappcloud/webhook.php';
echo '<p><strong>رابط الويب هوك:</strong> <code>' . $webhookUrl . '</code></p>';

// Test webhook accessibility
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $webhookUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_USERAGENT, 'WHMCS-WhatsApp-Verification');

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

if ($error) {
    echo '<p class="error">❌ خطأ في الاتصال: ' . $error . '</p>';
} elseif ($httpCode === 405) {
    echo '<p class="check">✅ الويب هوك متاح ويستجيب (HTTP 405 - Method Not Allowed كما هو متوقع)</p>';
} elseif ($httpCode === 200) {
    echo '<p class="info">ℹ️ الويب هوك متاح (HTTP 200)</p>';
} else {
    echo '<p class="warning">⚠️ الويب هوك يستجيب بكود HTTP ' . $httpCode . '</p>';
}

echo '</div>';

// 5. Check file permissions
echo '<div class="section">
<h2>📁 فحص صلاحيات الملفات</h2>';

$addonPath = __DIR__;
$files = [
    'whatsappcloud.php' => 'الملف الرئيسي للإضافة',
    'webhook.php' => 'نقطة نهاية الويب هوك',
    'lib/WhatsAppAPI.php' => 'مكتبة API',
    'assets/style.css' => 'ملف التصميم'
];

foreach ($files as $file => $description) {
    $filepath = $addonPath . '/' . $file;
    echo '<p>';
    
    if (file_exists($filepath)) {
        if (is_readable($filepath)) {
            echo '<span class="check">✅ ' . $description . ' - قابل للقراءة</span>';
        } else {
            echo '<span class="error">❌ ' . $description . ' - غير قابل للقراءة</span>';
        }
    } else {
        echo '<span class="error">❌ ' . $description . ' - الملف مفقود</span>';
    }
    
    echo '</p>';
}

echo '</div>';

// 6. Test WhatsApp API connection
echo '<div class="section">
<h2>🔗 فحص الاتصال مع WhatsApp API</h2>';

try {
    require_once 'lib/WhatsAppAPI.php';
    
    // Get configuration
    $config = [];
    $query = "SELECT setting, value FROM tbladdonmodules WHERE module = 'whatsappcloud'";
    $result = full_query($query);
    
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $config[$row['setting']] = $row['value'];
        }
    }
    
    if (!empty($config['access_token']) && !empty($config['business_account_id'])) {
        $api = new WhatsAppAPI($config);
        $testResult = $api->testConnection();
        
        if ($testResult['success']) {
            echo '<p class="check">✅ الاتصال مع WhatsApp Cloud API ناجح</p>';
            if (isset($testResult['data']['name'])) {
                echo '<p><strong>اسم الحساب:</strong> ' . htmlspecialchars($testResult['data']['name']) . '</p>';
            }
        } else {
            echo '<p class="error">❌ فشل الاتصال مع WhatsApp Cloud API</p>';
            echo '<p><strong>الخطأ:</strong> ' . htmlspecialchars($testResult['error']) . '</p>';
        }
    } else {
        echo '<p class="warning">⚠️ لم يتم تكوين بيانات الاتصال بـ WhatsApp API</p>';
        echo '<p>يرجى إدخال Access Token و Business Account ID في إعدادات الإضافة</p>';
    }
    
} catch (Exception $e) {
    echo '<p class="error">❌ خطأ في اختبار الاتصال: ' . $e->getMessage() . '</p>';
}

echo '</div>';

// 7. Statistics and summary
echo '<div class="section">
<h2>📊 إحصائيات سريعة</h2>';

$stats = [
    'conversations' => 0,
    'messages' => 0,
    'active_conversations' => 0
];

try {
    $query = "SELECT COUNT(*) as count FROM mod_whatsappcloud_conversations";
    $result = full_query($query);
    if ($result) {
        $stats['conversations'] = $result->fetch_assoc()['count'];
    }
    
    $query = "SELECT COUNT(*) as count FROM mod_whatsappcloud_messages";
    $result = full_query($query);
    if ($result) {
        $stats['messages'] = $result->fetch_assoc()['count'];
    }
    
    $query = "SELECT COUNT(*) as count FROM mod_whatsappcloud_conversations WHERE status = 'active'";
    $result = full_query($query);
    if ($result) {
        $stats['active_conversations'] = $result->fetch_assoc()['count'];
    }
} catch (Exception $e) {
    // Ignore database errors for stats
}

echo '<div class="grid">';
echo '<div class="status-card status-success">';
echo '<h3>💬 إجمالي المحادثات</h3>';
echo '<p style="font-size: 24px; font-weight: bold; margin: 0;">' . number_format($stats['conversations']) . '</p>';
echo '</div>';

echo '<div class="status-card status-success">';
echo '<h3>📨 إجمالي الرسائل</h3>';
echo '<p style="font-size: 24px; font-weight: bold; margin: 0;">' . number_format($stats['messages']) . '</p>';
echo '</div>';

echo '<div class="status-card status-success">';
echo '<h3>🟢 المحادثات النشطة</h3>';
echo '<p style="font-size: 24px; font-weight: bold; margin: 0;">' . number_format($stats['active_conversations']) . '</p>';
echo '</div>';
echo '</div>';

echo '</div>';

// 8. Action items
echo '<div class="section">
<h2>🚀 خطوات للبدء</h2>';

echo '<ol style="line-height: 2;">
<li>تأكد من تفعيل الإضافة في لوحة تحكم WHMCS</li>
<li>أدخل بيانات WhatsApp Cloud API في إعدادات الإضافة</li>
<li>قم بإعداد الويب هوك في لوحة تحكم Facebook Developers</li>
<li>اختبر البوت التفاعلي بإرسال رسالة إلى رقم الواتساب</li>
<li>راجع المحادثات والرسائل من تبويب الدردشة</li>
</ol>';

echo '<p style="margin-top: 20px;">
<a href="../../../admin/configaddonmods.php" style="background: #25D366; color: white; padding: 10px 20px; text-decoration: none; border-radius: 4px;">⚙️ إعدادات الإضافة</a>
<a href="?" style="background: #6c757d; color: white; padding: 10px 20px; text-decoration: none; border-radius: 4px; margin-right: 10px;">🔄 إعادة الفحص</a>
</p>';

echo '</div>';

echo '<div class="section" style="background: #e3f2fd; text-align: center;">
<h3>📞 هل تحتاج مساعدة؟</h3>
<p>في حالة مواجهة أي مشاكل، يرجى مراجعة وثائق الإضافة أو التواصل مع الدعم الفني.</p>
<p><strong>إصدار الإضافة:</strong> 1.0.0 | <strong>تاريخ الفحص:</strong> ' . date('Y-m-d H:i:s') . '</p>
</div>';

echo '</div>
</body>
</html>';
?>