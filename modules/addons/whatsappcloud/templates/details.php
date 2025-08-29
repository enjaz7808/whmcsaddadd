<?php
/**
 * Details Tab Template
 */

// Get connection status
$api = new WhatsAppAPI($vars);
$connectionTest = $api->testConnection();
$isConnected = $connectionTest['success'];
?>

<div class="details-tab">
    <h2>📊 تفاصيل التكامل مع WhatsApp Cloud</h2>
    
    <!-- Connection Status -->
    <div class="info-card">
        <h3>
            <span class="status-indicator <?php echo $isConnected ? 'status-connected' : 'status-disconnected'; ?>"></span>
            حالة الاتصال
        </h3>
        
        <?php if ($isConnected): ?>
            <div class="alert alert-success">
                ✅ متصل بنجاح مع WhatsApp Cloud API
            </div>
        <?php else: ?>
            <div class="alert alert-danger">
                ❌ غير متصل - يرجى التحقق من الإعدادات
                <?php if (isset($connectionTest['error'])): ?>
                    <br><small><?php echo htmlspecialchars($connectionTest['error']); ?></small>
                <?php endif; ?>
            </div>
        <?php endif; ?>
        
        <form method="post">
            <input type="hidden" name="tab" value="details">
            <button type="submit" name="test_connection" value="1" class="btn">
                🔄 اختبار الاتصال
            </button>
        </form>
    </div>
    
    <!-- API Configuration -->
    <div class="info-card">
        <h3>⚙️ إعدادات API</h3>
        
        <div class="config-grid">
            <div class="form-group">
                <label>🆔 معرف التطبيق:</label>
                <div class="copy-field">
                    <input type="text" value="<?php echo htmlspecialchars($vars['app_id']); ?>" readonly>
                    <button type="button" class="copy-btn" onclick="copyToClipboard(this)" title="نسخ">📋</button>
                </div>
            </div>
            
            <div class="form-group">
                <label>📱 معرف رقم الهاتف:</label>
                <div class="copy-field">
                    <input type="text" value="<?php echo htmlspecialchars($vars['phone_number_id']); ?>" readonly>
                    <button type="button" class="copy-btn" onclick="copyToClipboard(this)" title="نسخ">📋</button>
                </div>
            </div>
            
            <div class="form-group">
                <label>🏢 معرف حساب الأعمال:</label>
                <div class="copy-field">
                    <input type="text" value="<?php echo htmlspecialchars($vars['business_account_id']); ?>" readonly>
                    <button type="button" class="copy-btn" onclick="copyToClipboard(this)" title="نسخ">📋</button>
                </div>
            </div>
            
            <div class="form-group">
                <label>🔑 رمز الوصول:</label>
                <div class="copy-field">
                    <input type="password" value="<?php echo str_repeat('*', 50); ?>" readonly>
                    <button type="button" class="copy-btn" onclick="togglePassword(this)" title="عرض/إخفاء">👁️</button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Statistics -->
    <div class="info-card">
        <h3>📈 إحصائيات الاستخدام</h3>
        
        <?php
        // Get statistics from database
        $totalConversations = 0;
        $activeConversations = 0;
        $totalMessages = 0;
        $todayMessages = 0;
        
        try {
            $query = "SELECT COUNT(*) as total FROM mod_whatsappcloud_conversations";
            $result = full_query($query);
            if ($result) {
                $totalConversations = $result->fetch_assoc()['total'];
            }
            
            $query = "SELECT COUNT(*) as active FROM mod_whatsappcloud_conversations WHERE status = 'active'";
            $result = full_query($query);
            if ($result) {
                $activeConversations = $result->fetch_assoc()['active'];
            }
            
            $query = "SELECT COUNT(*) as total FROM mod_whatsappcloud_messages";
            $result = full_query($query);
            if ($result) {
                $totalMessages = $result->fetch_assoc()['total'];
            }
            
            $query = "SELECT COUNT(*) as today FROM mod_whatsappcloud_messages WHERE DATE(created_at) = CURDATE()";
            $result = full_query($query);
            if ($result) {
                $todayMessages = $result->fetch_assoc()['today'];
            }
        } catch (Exception $e) {
            // Handle database errors silently
        }
        ?>
        
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number"><?php echo number_format($totalConversations); ?></div>
                <div class="stat-label">إجمالي المحادثات</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-number"><?php echo number_format($activeConversations); ?></div>
                <div class="stat-label">المحادثات النشطة</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-number"><?php echo number_format($totalMessages); ?></div>
                <div class="stat-label">إجمالي الرسائل</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-number"><?php echo number_format($todayMessages); ?></div>
                <div class="stat-label">رسائل اليوم</div>
            </div>
        </div>
    </div>
    
    <!-- System Information -->
    <div class="info-card">
        <h3>🔧 معلومات النظام</h3>
        
        <div class="config-grid">
            <div>
                <label>📌 إصدار الإضافة:</label>
                <p><?php echo htmlspecialchars($version); ?></p>
            </div>
            
            <div>
                <label>🌐 رابط الويب هوك:</label>
                <p><code>https://enjaz-web.com/billing/modules/addons/whatsappcloud/webhook.php</code></p>
            </div>
            
            <div>
                <label>🤖 حالة البوت:</label>
                <p>
                    <?php if ($vars['enable_bot'] === 'on'): ?>
                        <span style="color: #28a745;">✅ مُفعل</span>
                    <?php else: ?>
                        <span style="color: #dc3545;">❌ معطل</span>
                    <?php endif; ?>
                </p>
            </div>
            
            <div>
                <label>🌍 اللغة الافتراضية:</label>
                <p>
                    <?php echo $vars['default_language'] === 'ar' ? '🇸🇦 العربية' : '🇺🇸 الإنجليزية'; ?>
                </p>
            </div>
        </div>
    </div>
    
    <!-- Quick Actions -->
    <div class="info-card">
        <h3>⚡ إجراءات سريعة</h3>
        
        <div style="display: flex; gap: 15px; flex-wrap: wrap;">
            <a href="<?php echo $modulelink; ?>&tab=webhook" class="btn">
                🔗 إعداد الويب هوك
            </a>
            
            <a href="<?php echo $modulelink; ?>&tab=bot" class="btn">
                🤖 إعدادات البوت
            </a>
            
            <a href="<?php echo $modulelink; ?>&tab=chat" class="btn">
                💬 إرسال رسالة
            </a>
            
            <a href="modules/addons/whatsappcloud/verify.php" target="_blank" class="btn btn-secondary">
                🔍 فحص التثبيت
            </a>
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

function togglePassword(button) {
    const input = button.parentElement.querySelector('input');
    const actualValue = '<?php echo htmlspecialchars($vars['access_token']); ?>';
    
    if (input.type === 'password') {
        input.type = 'text';
        input.value = actualValue;
        button.innerHTML = '🙈';
    } else {
        input.type = 'password';
        input.value = '<?php echo str_repeat('*', 50); ?>';
        button.innerHTML = '👁️';
    }
}
</script>