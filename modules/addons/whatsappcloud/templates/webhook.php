<?php
/**
 * Webhook Tab Template
 */

$webhookUrl = 'https://enjaz-web.com/billing/modules/addons/whatsappcloud/webhook.php';
$verifyToken = $vars['webhook_verify_token'] ?? bin2hex(random_bytes(16));
?>

<div class="webhook-tab">
    <h2>🔗 إعداد ويب هوك واتساب كلاود</h2>
    
    <!-- Webhook Configuration -->
    <div class="webhook-config" style="background: #f8f9fa; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
        <h3>⚙️ تكوين الويب هوك</h3>
        
        <div class="form-group">
            <label>🌐 رابط الويب هوك (Callback URL):</label>
            <input type="text" value="<?php echo $webhookUrl; ?>" readonly 
                   style="background: #e9ecef; cursor: pointer;" 
                   onclick="this.select(); document.execCommand('copy'); 
                           alert('تم نسخ الرابط إلى الحافظة!');">
            <small style="color: #6c757d; display: block; margin-top: 5px;">
                استخدم هذا الرابط في إعدادات الويب هوك في فيسبوك المطورين
            </small>
        </div>
        
        <div class="form-group">
            <label>🔐 رمز التحقق (Verify Token):</label>
            <input type="text" value="<?php echo $verifyToken; ?>" readonly 
                   style="background: #e9ecef; cursor: pointer;" 
                   onclick="this.select(); document.execCommand('copy'); 
                           alert('تم نسخ الرمز إلى الحافظة!');">
            <small style="color: #6c757d; display: block; margin-top: 5px;">
                استخدم هذا الرمز في حقل "تحقق من الرمز" في إعدادات الويب هوك
            </small>
        </div>
    </div>
    
    <!-- Webhook Test -->
    <div class="webhook-test" style="background: #ffffff; border: 1px solid #dee2e6; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
        <h3>🧪 اختبار الويب هوك</h3>
        
        <form method="post">
            <input type="hidden" name="tab" value="webhook">
            
            <div class="form-group">
                <label>اختبار الاتصال مع الويب هوك:</label>
                <button type="submit" name="test_webhook" value="1" class="btn">
                    🔄 اختبار الويب هوك
                </button>
            </div>
        </form>
    </div>
    
    <!-- Setup Instructions -->
    <div class="setup-instructions" style="background: #e3f2fd; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
        <h3>📋 خطوات الإعداد في فيسبوك المطورين</h3>
        
        <ol style="margin: 15px 0; padding-right: 20px;">
            <li style="margin-bottom: 10px;">
                <strong>اذهب إلى</strong> 
                <a href="https://developers.facebook.com/" target="_blank" style="color: #1976d2;">
                    فيسبوك المطورين
                </a> 
                وسجل الدخول
            </li>
            
            <li style="margin-bottom: 10px;">
                <strong>اختر تطبيقك</strong> أو أنشئ تطبيق جديد
            </li>
            
            <li style="margin-bottom: 10px;">
                <strong>انتقل إلى</strong> WhatsApp > Configuration
            </li>
            
            <li style="margin-bottom: 10px;">
                <strong>في قسم Webhooks، أدخل:</strong>
                <ul style="margin: 10px 0; padding-right: 20px;">
                    <li><strong>Callback URL:</strong> <code style="background: #f5f5f5; padding: 2px 6px;"><?php echo $webhookUrl; ?></code></li>
                    <li><strong>Verify Token:</strong> <code style="background: #f5f5f5; padding: 2px 6px;"><?php echo $verifyToken; ?></code></li>
                </ul>
            </li>
            
            <li style="margin-bottom: 10px;">
                <strong>اشترك في الحقول التالية:</strong>
                <div style="margin: 10px 0; padding: 10px; background: #f8f9fa; border-radius: 5px;">
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 10px;">
                        <div>✅ messages</div>
                        <div>✅ message_echoes</div>
                        <div>✅ messaging_handovers</div>
                        <div>✅ message_template_status_update</div>
                    </div>
                </div>
            </li>
            
            <li style="margin-bottom: 10px;">
                <strong>انقر على "Verify and Save"</strong>
            </li>
        </ol>
    </div>
    
    <!-- Webhook Fields Configuration -->
    <div class="webhook-fields" style="background: #ffffff; border: 1px solid #dee2e6; padding: 20px; border-radius: 8px;">
        <h3>🎛️ حقول الويب هوك المطلوبة</h3>
        
        <p style="margin-bottom: 15px; color: #6c757d;">
            تأكد من الاشتراك في الحقول التالية في إعدادات الويب هوك:
        </p>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 15px;">
            <!-- Essential Fields -->
            <div style="background: #d4edda; padding: 15px; border-radius: 6px; border-right: 4px solid #28a745;">
                <h4 style="margin: 0 0 10px 0; color: #155724;">🔴 حقول أساسية (مطلوبة)</h4>
                <ul style="margin: 0; padding-right: 20px; color: #155724;">
                    <li>messages - لاستقبال الرسائل</li>
                    <li>message_template_status_update - حالة القوالب</li>
                </ul>
            </div>
            
            <!-- Recommended Fields -->
            <div style="background: #fff3cd; padding: 15px; border-radius: 6px; border-right: 4px solid #ffc107;">
                <h4 style="margin: 0 0 10px 0; color: #856404;">🟡 حقول موصى بها</h4>
                <ul style="margin: 0; padding-right: 20px; color: #856404;">
                    <li>message_echoes - تتبع الرسائل المرسلة</li>
                    <li>messaging_handovers - تمرير المحادثات</li>
                    <li>account_alerts - تنبيهات الحساب</li>
                </ul>
            </div>
            
            <!-- Optional Fields -->
            <div style="background: #e2e3e5; padding: 15px; border-radius: 6px; border-right: 4px solid #6c757d;">
                <h4 style="margin: 0 0 10px 0; color: #495057;">⚪ حقول اختيارية</h4>
                <ul style="margin: 0; padding-right: 20px; color: #495057;">
                    <li>business_status_update - تحديثات الأعمال</li>
                    <li>phone_number_quality_update - جودة الرقم</li>
                    <li>security - التحديثات الأمنية</li>
                </ul>
            </div>
        </div>
    </div>
    
    <!-- Webhook Status -->
    <div class="webhook-status" style="margin-top: 20px; background: #f8f9fa; padding: 15px; border-radius: 8px;">
        <h4>📊 حالة الويب هوك الحالية</h4>
        
        <div style="display: flex; align-items: center; gap: 10px; margin-top: 10px;">
            <span class="status-indicator status-disconnected"></span>
            <span>لم يتم التحقق من الويب هوك بعد</span>
            
            <form method="post" style="margin-right: auto;">
                <input type="hidden" name="tab" value="webhook">
                <button type="submit" name="test_webhook" value="1" class="btn btn-secondary" style="padding: 5px 15px; font-size: 14px;">
                    🔄 إعادة اختبار
                </button>
            </form>
        </div>
        
        <small style="color: #6c757d; display: block; margin-top: 10px;">
            💡 <strong>نصيحة:</strong> بعد إعداد الويب هوك في فيسبوك، انقر على "اختبار الويب هوك" للتأكد من صحة الإعداد
        </small>
    </div>
</div>

<script>
// Auto-refresh webhook status every 30 seconds
setInterval(function() {
    // You can add AJAX call here to check webhook status
}, 30000);

// Copy to clipboard functionality
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(function() {
        alert('تم نسخ النص إلى الحافظة!');
    });
}
</script>