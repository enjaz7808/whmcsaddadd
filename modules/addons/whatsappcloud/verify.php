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
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .check { color: #28a745; font-weight: bold; }
        .error { color: #dc3545; font-weight: bold; }
        .warning { color: #ffc107; font-weight: bold; }
        .section { margin: 20px 0; padding: 15px; border-right: 4px solid #25d366; background: #f8f9fa; }
        .header { text-align: center; color: #25d366; margin-bottom: 30px; }
        table { width: 100%; border-collapse: collapse; margin: 15px 0; }
        th, td { padding: 10px; border: 1px solid #dee2e6; text-align: right; }
        th { background: #e9ecef; }
        .btn { background: #25d366; color: white; padding: 8px 16px; border: none; border-radius: 4px; cursor: pointer; }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1>🔍 فحص تثبيت إضافة واتساب كلاود</h1>
        <p>WhatsApp Cloud Addon Installation Verification</p>
    </div>';

// Function to display check result
function checkResult($condition, $successText, $errorText) {
    if ($condition) {
        echo '<span class="check">✅ ' . $successText . '</span>';
        return true;
    } else {
        echo '<span class="error">❌ ' . $errorText . '</span>';
        return false;
    }
}

// 1. Check file structure
echo '<div class="section">
<h3>📁 فحص هيكل الملفات</h3>
<table>
<tr><th>الملف</th><th>الحالة</th></tr>';

$requiredFiles = [
    'whatsappcloud.php' => 'الملف الرئيسي للإضافة',
    'webhook.php' => 'نقطة نهاية الويب هوك',
    'lib/WhatsAppAPI.php' => 'مكتبة API واتساب',
    'templates/details.php' => 'قالب التفاصيل',
    'templates/webhook.php' => 'قالب الويب هوك',
    'templates/bot.php' => 'قالب البوت',
    'templates/chat.php' => 'قالب الدردشة',
    'lang/arabic.php' => 'ملف اللغة العربية',
    'lang/english.php' => 'ملف اللغة الإنجليزية',
    'assets/style.css' => 'ملف التنسيق'
];

$allFilesExist = true;
foreach ($requiredFiles as $file => $description) {
    echo '<tr><td>' . $description . '</td><td>';
    if (file_exists(__DIR__ . '/' . $file)) {
        echo '<span class="check">✅ موجود</span>';
    } else {
        echo '<span class="error">❌ مفقود</span>';
        $allFilesExist = false;
    }
    echo '</td></tr>';
}
echo '</table></div>';

// 2. Check WHMCS integration
echo '<div class="section">
<h3>🔧 فحص تكامل WHMCS</h3>';

try {
    // Check if addon is activated
    $query = "SELECT * FROM tbladdonmodules WHERE module = 'whatsappcloud'";
    $result = full_query($query);
    
    if ($result && $result->num_rows > 0) {
        echo '<p class="check">✅ الإضافة مُثبتة ومُفعلة في WHMCS</p>';
        
        // Display configuration
        echo '<h4>⚙️ الإعدادات الحالية:</h4>
        <table>
        <tr><th>الإعداد</th><th>القيمة</th></tr>';
        
        while ($row = $result->fetch_assoc()) {
            $value = $row['value'];
            if (in_array($row['setting'], ['app_secret', 'access_token', 'webhook_verify_token'])) {
                $value = substr($value, 0, 8) . '...';
            }
            echo '<tr><td>' . $row['setting'] . '</td><td>' . htmlspecialchars($value) . '</td></tr>';
        }
        echo '</table>';
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
<h3>🗄️ فحص جداول قاعدة البيانات</h3>';

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
<h3>🌐 فحص نقطة نهاية الويب هوك</h3>';

$webhookUrl = 'https://enjaz-web.com/billing/modules/addons/whatsappcloud/webhook.php';
echo '<p><strong>رابط الويب هوك:</strong> <code>' . $webhookUrl . '</code></p>';

// Test webhook accessibility
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $webhookUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

if ($httpCode === 200 || $httpCode === 405) {
    echo '<p class="check">✅ الويب هوك قابل للوصول (HTTP ' . $httpCode . ')</p>';
} else {
    echo '<p class="error">❌ الويب هوك غير قابل للوصول (HTTP ' . $httpCode . ')</p>';
    if ($error) {
        echo '<p class="error">خطأ: ' . $error . '</p>';
    }
}
echo '</div>';

// 5. PHP requirements check
echo '<div class="section">
<h3>🐘 فحص متطلبات PHP</h3>
<table>
<tr><th>المتطلب</th><th>الحالة</th></tr>';

$phpChecks = [
    'PHP Version >= 7.4' => version_compare(PHP_VERSION, '7.4.0', '>='),
    'cURL Extension' => extension_loaded('curl'),
    'JSON Extension' => extension_loaded('json'),
    'MySQLi Extension' => extension_loaded('mysqli'),
    'OpenSSL Extension' => extension_loaded('openssl')
];

foreach ($phpChecks as $check => $result) {
    echo '<tr><td>' . $check . '</td><td>';
    if ($result) {
        echo '<span class="check">✅ متوفر</span>';
    } else {
        echo '<span class="error">❌ مفقود</span>';
    }
    echo '</td></tr>';
}
echo '</table></div>';

// 6. Quick actions
echo '<div class="section">
<h3>⚡ إجراءات سريعة</h3>
<p>
    <a href="../../../admin/configaddons.php" class="btn">🔧 إعدادات الإضافة</a>
    <a href="../../../admin/addonmodules.php?module=whatsappcloud" class="btn">📊 لوحة التحكم</a>
</p>
</div>';

// Summary
echo '<div class="section">
<h3>📋 ملخص التحقق</h3>';

if ($allFilesExist) {
    echo '<p class="check">✅ جميع الملفات المطلوبة موجودة</p>';
} else {
    echo '<p class="error">❌ بعض الملفات مفقودة</p>';
}

echo '<p><strong>التاريخ:</strong> ' . date('Y-m-d H:i:s') . '</p>
<p><strong>خادم الويب:</strong> ' . $_SERVER['SERVER_SOFTWARE'] . '</p>
<p><strong>إصدار PHP:</strong> ' . PHP_VERSION . '</p>
</div>';

echo '</div>
</body>
</html>';
?>