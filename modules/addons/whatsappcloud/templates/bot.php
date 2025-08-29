<?php
/**
 * Bot Tab Template
 */

// Handle bot settings save
if (isset($_POST['save_bot_settings'])) {
    // Save bot configuration to database or config
}

// Get current bot settings
$botEnabled = $vars['enable_bot'] === 'on';
$defaultLanguage = $vars['default_language'] ?? 'ar';
?>

<div class="bot-tab">
    <h2>🤖 البوت التفاعلي المتقدم</h2>
    
    <!-- Bot Status -->
    <div class="info-card" style="background: <?php echo $botEnabled ? '#d4edda' : '#f8d7da'; ?>;">
        <h3>
            <span class="status-indicator <?php echo $botEnabled ? 'status-connected' : 'status-disconnected'; ?>"></span>
            حالة البوت
        </h3>
        
        <?php if ($botEnabled): ?>
            <div class="alert alert-success">
                ✅ البوت التفاعلي مُفعل ويعمل بشكل طبيعي
            </div>
        <?php else: ?>
            <div class="alert alert-warning">
                ⚠️ البوت التفاعلي معطل. قم بتفعيله من الإعدادات العامة للإضافة.
            </div>
        <?php endif; ?>
    </div>
    
    <!-- Bot Configuration -->
    <form method="post">
        <input type="hidden" name="tab" value="bot">
        
        <div class="info-card">
            <h3>⚙️ إعدادات البوت</h3>
            
            <div class="config-grid">
                <div class="form-group">
                    <label>🌐 اللغة الافتراضية:</label>
                    <select name="default_language" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                        <option value="ar" <?php echo $defaultLanguage === 'ar' ? 'selected' : ''; ?>>العربية 🇸🇦</option>
                        <option value="en" <?php echo $defaultLanguage === 'en' ? 'selected' : ''; ?>>English 🇺🇸</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>📞 ساعات العمل:</label>
                    <select name="working_hours" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                        <option value="24/7">24 ساعة</option>
                        <option value="business">ساعات العمل فقط</option>
                    </select>
                </div>
            </div>
            
            <div class="form-group" style="margin-top: 15px;">
                <button type="submit" name="save_bot_settings" value="1" class="btn">
                    💾 حفظ الإعدادات
                </button>
            </div>
        </div>
    </form>
    
    <!-- Bot Flow Preview -->
    <div class="info-card">
        <h3>🔄 سير عمل البوت التفاعلي</h3>
        
        <div class="flow-steps" style="margin-top: 15px;">
            <!-- Step 1: Initial Contact -->
            <div class="flow-step" style="border-right-color: #007bff;">
                <div class="flow-step-number" style="background: #007bff;">1</div>
                <div>
                    <h4 style="color: #007bff;">📨 استقبال الرسالة الأولى</h4>
                    <p>عندما يرسل العميل أول رسالة، يتم إرسال طلب الموافقة تلقائياً</p>
                    <div style="background: #e3f2fd; padding: 10px; border-radius: 4px; margin-top: 10px; font-family: monospace; font-size: 14px;">
                        "هل توافق على تلقي الرسائل من نظامنا؟<br>
                        Do you agree to receive messages from our system?"<br>
                        [موافق / Approve] [رفض / Decline]
                    </div>
                </div>
            </div>
            
            <!-- Step 2: Approval -->
            <div class="flow-step" style="border-right-color: #28a745;">
                <div class="flow-step-number" style="background: #28a745;">2</div>
                <div>
                    <h4 style="color: #28a745;">✅ الموافقة على التواصل</h4>
                    <p>إذا وافق العميل، يتم إرسال خيارات اللغة</p>
                    <div style="background: #d4edda; padding: 10px; border-radius: 4px; margin-top: 10px; font-family: monospace; font-size: 14px;">
                        "Please select your preferred language<br>
                        الرجاء اختيار لغتك المفضلة"<br>
                        [العربية] [English]
                    </div>
                </div>
            </div>
            
            <!-- Step 3: Language Selection -->
            <div class="flow-step" style="border-right-color: #17a2b8;">
                <div class="flow-step-number" style="background: #17a2b8;">3</div>
                <div>
                    <h4 style="color: #17a2b8;">🌍 اختيار اللغة</h4>
                    <p>بعد اختيار اللغة، يتم إرسال رسالة ترحيبية شاملة</p>
                    <div style="background: #d1ecf1; padding: 10px; border-radius: 4px; margin-top: 10px; font-family: monospace; font-size: 14px;">
                        "🎉 أهلاً وسهلاً!<br><br>
                        نحن سعداء لوجودك معنا...<br>
                        📞 للمساعدة: اكتب 'مساعدة'<br>
                        💬 للمبيعات: اكتب 'مبيعات'"
                    </div>
                </div>
            </div>
            
            <!-- Step 4: Commands -->
            <div class="flow-step" style="border-right-color: #ffc107;">
                <div class="flow-step-number" style="background: #ffc107; color: #000;">4</div>
                <div>
                    <h4 style="color: #856404;">⚡ الأوامر التفاعلية</h4>
                    <p>البوت يستجيب للأوامر المختلفة ويوجه العملاء للأقسام المناسبة</p>
                    <div style="background: #fff3cd; padding: 10px; border-radius: 4px; margin-top: 10px; font-family: monospace; font-size: 14px;">
                        • مساعدة / help - عرض قائمة الأوامر<br>
                        • مبيعات / sales - قسم المبيعات<br>
                        • دعم / support - الدعم الفني
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Bot Commands -->
    <div class="info-card">
        <h3>🎯 أوامر البوت المتاحة</h3>
        
        <div class="config-grid">
            <div>
                <h4>🆘 أوامر المساعدة</h4>
                <ul style="margin: 10px 0; padding-right: 20px;">
                    <li>مساعدة، help</li>
                    <li>قائمة، menu</li>
                    <li>خدمات، services</li>
                </ul>
            </div>
            
            <div>
                <h4>💼 أوامر المبيعات</h4>
                <ul style="margin: 10px 0; padding-right: 20px;">
                    <li>مبيعات، sales</li>
                    <li>شراء، buy</li>
                    <li>أسعار، prices</li>
                </ul>
            </div>
            
            <div>
                <h4>🎫 أوامر الدعم</h4>
                <ul style="margin: 10px 0; padding-right: 20px;">
                    <li>دعم، support</li>
                    <li>مشكلة، problem</li>
                    <li>تذكرة، ticket</li>
                </ul>
            </div>
            
            <div>
                <h4>ℹ️ أوامر المعلومات</h4>
                <ul style="margin: 10px 0; padding-right: 20px;">
                    <li>معلومات، info</li>
                    <li>عنا، about</li>
                    <li>اتصال، contact</li>
                </ul>
            </div>
        </div>
    </div>
    
    <!-- Bot Response Templates -->
    <div class="info-card">
        <h3>💬 قوالب الردود</h3>
        
        <div style="margin-top: 15px;">
            <div class="form-group">
                <label>🎉 رسالة الترحيب (عربي):</label>
                <textarea rows="4" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;" readonly>🎉 أهلاً وسهلاً! 

نحن سعداء لوجودك معنا. يمكنك الآن التواصل معنا عبر واتساب للحصول على الدعم والمساعدة.

📞 للمساعدة: اكتب 'مساعدة'
💬 للتحدث مع المبيعات: اكتب 'مبيعات'
🎫 لفتح تذكرة دعم: اكتب 'دعم'</textarea>
            </div>
            
            <div class="form-group">
                <label>🎉 رسالة الترحيب (English):</label>
                <textarea rows="4" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;" readonly>🎉 Welcome! 

We're happy to have you with us. You can now communicate with us via WhatsApp for support and assistance.

📞 For help: type 'help'
💬 To chat with sales: type 'sales'
🎫 To open support ticket: type 'support'</textarea>
            </div>
        </div>
    </div>
    
    <!-- Bot Analytics -->
    <div class="info-card">
        <h3>📊 إحصائيات البوت</h3>
        
        <?php
        // Get bot statistics
        $botStats = [
            'total_interactions' => 0,
            'approval_rate' => 0,
            'language_preferences' => ['ar' => 0, 'en' => 0],
            'popular_commands' => []
        ];
        
        try {
            // Get total conversations that went through bot
            $query = "SELECT COUNT(*) as total FROM mod_whatsappcloud_conversations WHERE status != 'pending'";
            $result = full_query($query);
            if ($result) {
                $botStats['total_interactions'] = $result->fetch_assoc()['total'];
            }
            
            // Calculate approval rate
            $query = "SELECT 
                        COUNT(CASE WHEN status = 'active' OR status = 'completed' THEN 1 END) as approved,
                        COUNT(*) as total
                      FROM mod_whatsappcloud_conversations";
            $result = full_query($query);
            if ($result) {
                $data = $result->fetch_assoc();
                $botStats['approval_rate'] = $data['total'] > 0 ? round(($data['approved'] / $data['total']) * 100, 1) : 0;
            }
            
            // Get language preferences
            $query = "SELECT language, COUNT(*) as count FROM mod_whatsappcloud_conversations GROUP BY language";
            $result = full_query($query);
            if ($result) {
                while ($row = $result->fetch_assoc()) {
                    $botStats['language_preferences'][$row['language']] = $row['count'];
                }
            }
        } catch (Exception $e) {
            // Handle database errors silently
        }
        ?>
        
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number"><?php echo number_format($botStats['total_interactions']); ?></div>
                <div class="stat-label">تفاعلات البوت</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-number"><?php echo $botStats['approval_rate']; ?>%</div>
                <div class="stat-label">معدل الموافقة</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-number"><?php echo number_format($botStats['language_preferences']['ar'] ?? 0); ?></div>
                <div class="stat-label">مستخدمو العربية</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-number"><?php echo number_format($botStats['language_preferences']['en'] ?? 0); ?></div>
                <div class="stat-label">English Users</div>
            </div>
        </div>
    </div>
    
    <!-- Test Bot -->
    <div class="info-card">
        <h3>🧪 اختبار البوت</h3>
        
        <p style="color: #6c757d; margin-bottom: 15px;">
            يمكنك اختبار البوت بإرسال رسالة تجريبية إلى رقم الواتساب المربوط
        </p>
        
        <div style="display: flex; gap: 15px; flex-wrap: wrap;">
            <button type="button" class="btn" onclick="simulateUserMessage('مرحبا')">
                💬 محاكاة رسالة "مرحبا"
            </button>
            
            <button type="button" class="btn" onclick="simulateUserMessage('مساعدة')">
                🆘 محاكاة أمر "مساعدة"
            </button>
            
            <button type="button" class="btn" onclick="simulateUserMessage('مبيعات')">
                💼 محاكاة أمر "مبيعات"
            </button>
        </div>
        
        <div id="simulation-result" style="margin-top: 15px; padding: 15px; border-radius: 6px; display: none;"></div>
    </div>
</div>

<script>
function simulateUserMessage(message) {
    const resultDiv = document.getElementById('simulation-result');
    resultDiv.style.display = 'block';
    resultDiv.style.background = '#e3f2fd';
    resultDiv.style.border = '1px solid #2196F3';
    
    resultDiv.innerHTML = `
        <h5>🤖 استجابة البوت التفاعلي:</h5>
        <p><strong>الرسالة المرسلة:</strong> "${message}"</p>
        <p><strong>الرد المتوقع:</strong></p>
        <div style="background: white; padding: 10px; border-radius: 4px; margin-top: 10px;">
            ${getBotResponse(message)}
        </div>
    `;
}

function getBotResponse(message) {
    const msg = message.toLowerCase();
    
    if (msg.includes('مرحبا') || msg.includes('السلام') || msg.includes('hello')) {
        return `
            هل توافق على تلقي الرسائل من نظامنا؟<br>
            Do you agree to receive messages from our system?<br><br>
            <strong>[موافق / Approve] [رفض / Decline]</strong>
        `;
    } else if (msg.includes('مساعدة') || msg.includes('help')) {
        return `
            📋 قائمة الأوامر المتاحة:<br><br>
            🆘 مساعدة - عرض هذه القائمة<br>
            💼 مبيعات - التحدث مع فريق المبيعات<br>
            🎫 دعم - فتح تذكرة دعم فني
        `;
    } else if (msg.includes('مبيعات') || msg.includes('sales')) {
        return `
            💼 مرحباً بك في قسم المبيعات!<br><br>
            سيتم توصيلك مع أحد ممثلي المبيعات قريباً. يرجى وصف استفسارك وسنعود إليك في أقرب وقت.
        `;
    } else {
        return `
            شكراً لرسالتك! تم استلامها وسيتم الرد عليك قريباً.<br><br>
            للمساعدة اكتب: مساعدة
        `;
    }
}
</script>