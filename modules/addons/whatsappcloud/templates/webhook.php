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
    <div class="info-card">
        <h3>⚙️ تكوين الويب هوك</h3>
        
        <div class="form-group">
            <label>🌐 رابط الويب هوك (Callback URL):</label>
            <div class="copy-field">
                <input type="text" value="<?php echo $webhookUrl; ?>" readonly 
                       style="background: #e9ecef; cursor: pointer;" 
                       onclick="this.select()">
                <button type="button" class="copy-btn" onclick="copyToClipboard(this)" title="نسخ الرابط">📋</button>
            </div>
            <small style="color: #6c757d; margin-top: 5px; display: block;">
                استخدم هذا الرابط في إعدادات الويب هوك في لوحة تحكم WhatsApp Cloud
            </small>
        </div>
        
        <div class="form-group">
            <label>🔐 رمز التحقق (Verify Token):</label>
            <div class="copy-field">
                <input type="text" value="<?php echo $verifyToken; ?>" readonly 
                       style="background: #e9ecef; cursor: pointer;" 
                       onclick="this.select()">
                <button type="button" class="copy-btn" onclick="copyToClipboard(this)" title="نسخ الرمز">📋</button>
            </div>
            <small style="color: #6c757d; margin-top: 5px; display: block;">
                استخدم هذا الرمز في حقل "تحقق من الرمز" في إعدادات الويب هوك
            </small>
        </div>
    </div>
    
    <!-- Webhook Test -->
    <div class="info-card">
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
    
    <!-- Required Webhook Fields -->
    <div class="info-card">
        <h3>🎛️ حقول الويب هوك المطلوبة</h3>
        
        <p style="margin-bottom: 15px; color: #6c757d;">
            تأكد من الاشتراك في الحقول التالية في إعدادات الويب هوك:
        </p>
        
        <div class="webhook-fields">
            <!-- Essential Fields -->
            <div class="field-group essential">
                <h4>🔴 حقول أساسية (مطلوبة)</h4>
                <ul class="field-list">
                    <li>✅ messages - لاستقبال الرسائل</li>
                    <li>✅ message_template_status_update - حالة القوالب</li>
                    <li>✅ account_alerts - تنبيهات الحساب</li>
                    <li>✅ business_status_update - حالة الأعمال</li>
                </ul>
            </div>
            
            <!-- Optional Fields -->
            <div class="field-group optional">
                <h4>🟡 حقول اختيارية (موصى بها)</h4>
                <ul class="field-list">
                    <li>⚪ phone_number_quality_update - جودة الرقم</li>
                    <li>⚪ account_review_update - مراجعة الحساب</li>
                    <li>⚪ flows - التدفقات التفاعلية</li>
                    <li>⚪ security - الأمان</li>
                </ul>
            </div>
        </div>
        
        <div class="alert alert-info" style="margin-top: 20px;">
            💡 <strong>نصيحة:</strong> تأكد من تعيين جميع الحقول إلى الإصدار <code>v23.0</code> أو أحدث للحصول على أفضل أداء.
        </div>
    </div>
    
    <!-- Setup Instructions -->
    <div class="info-card">
        <h3>📋 خطوات الإعداد</h3>
        
        <div style="margin-top: 15px;">
            <div class="flow-step">
                <div class="flow-step-number">1</div>
                <div>
                    <h4>🌐 انتقل إلى لوحة تحكم WhatsApp Cloud</h4>
                    <p>قم بزيارة <a href="https://developers.facebook.com/apps" target="_blank">Facebook for Developers</a> وحدد تطبيقك</p>
                </div>
            </div>
            
            <div class="flow-step">
                <div class="flow-step-number">2</div>
                <div>
                    <h4>⚙️ اذهب إلى إعدادات الويب هوك</h4>
                    <p>في القائمة الجانبية، اختر WhatsApp → Configuration → Webhooks</p>
                </div>
            </div>
            
            <div class="flow-step">
                <div class="flow-step-number">3</div>
                <div>
                    <h4>📝 أدخل بيانات الويب هوك</h4>
                    <p>انسخ الرابط والرمز من الأعلى وضعهما في الحقول المطلوبة</p>
                </div>
            </div>
            
            <div class="flow-step">
                <div class="flow-step-number">4</div>
                <div>
                    <h4>✅ اختر الحقول المطلوبة</h4>
                    <p>اشترك في جميع الحقول الأساسية المذكورة أعلاه</p>
                </div>
            </div>
            
            <div class="flow-step">
                <div class="flow-step-number">5</div>
                <div>
                    <h4>🧪 اختبر الاتصال</h4>
                    <p>استخدم زر "اختبار الويب هوك" أعلاه للتأكد من عمل الاتصال</p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Security Settings -->
    <div class="info-card">
        <h3>🔒 إعدادات الأمان</h3>
        
        <div class="alert alert-warning">
            ⚠️ <strong>هام:</strong> للحصول على أمان إضافي، تأكد من تفعيل التحقق من التوقيع في إعدادات الويب هوك.
        </div>
        
        <div style="margin-top: 15px;">
            <label>🔐 App Secret للتحقق من التوقيع:</label>
            <div class="copy-field">
                <input type="password" value="<?php echo str_repeat('*', 32); ?>" readonly>
                <button type="button" class="copy-btn" onclick="toggleSecret(this)" title="عرض/إخفاء">👁️</button>
            </div>
            <small style="color: #6c757d; margin-top: 5px; display: block;">
                يتم استخدام هذا للتحقق من صحة الرسائل الواردة من WhatsApp
            </small>
        </div>
    </div>
    
    <!-- Webhook Logs -->
    <div class="info-card">
        <h3>📊 سجل الويب هوك</h3>
        
        <div style="background: #f8f9fa; padding: 15px; border-radius: 6px; font-family: monospace; font-size: 14px; max-height: 300px; overflow-y: auto;">
            <?php
            // Get recent webhook logs from activity log
            try {
                $query = "SELECT * FROM tblactivitylog WHERE description LIKE '%WhatsApp Webhook%' ORDER BY date DESC LIMIT 10";
                $result = full_query($query);
                
                if ($result && $result->num_rows > 0) {
                    while ($log = $result->fetch_assoc()) {
                        echo '<div style="margin-bottom: 10px; padding: 8px; background: white; border-radius: 4px;">';
                        echo '<strong>' . date('Y-m-d H:i:s', strtotime($log['date'])) . '</strong><br>';
                        echo htmlspecialchars($log['description']);
                        echo '</div>';
                    }
                } else {
                    echo '<div style="text-align: center; color: #6c757d; padding: 20px;">';
                    echo '📋 لا توجد سجلات ويب هوك حتى الآن<br>';
                    echo '<small>ستظهر هنا الرسائل الواردة والصادرة</small>';
                    echo '</div>';
                }
            } catch (Exception $e) {
                echo '<div style="color: #dc3545;">❌ خطأ في تحميل السجلات</div>';
            }
            ?>
        </div>
        
        <div style="margin-top: 10px;">
            <button type="button" class="btn btn-secondary" onclick="refreshLogs()">
                🔄 تحديث السجلات
            </button>
        </div>
    </div>
</div>

<script>
function copyToClipboard(button) {
    const input = button.parentElement.querySelector('input');
    input.select();
    document.execCommand('copy');
    
    const originalText = button.innerHTML;
    button.innerHTML = '✅';
    setTimeout(() => {
        button.innerHTML = originalText;
    }, 2000);
}

function toggleSecret(button) {
    const input = button.parentElement.querySelector('input');
    const actualValue = '<?php echo htmlspecialchars($vars['app_secret'] ?? ''); ?>';
    
    if (input.type === 'password') {
        input.type = 'text';
        input.value = actualValue;
        button.innerHTML = '🙈';
    } else {
        input.type = 'password';
        input.value = '<?php echo str_repeat('*', 32); ?>';
        button.innerHTML = '👁️';
    }
}

function refreshLogs() {
    location.reload();
}
</script>