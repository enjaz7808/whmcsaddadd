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
    <div class="bot-status" style="background: <?php echo $botEnabled ? '#d4edda' : '#f8d7da'; ?>; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
        <h3>
            <span class="status-indicator <?php echo $botEnabled ? 'status-connected' : 'status-disconnected'; ?>"></span>
            حالة البوت: <?php echo $botEnabled ? 'مفعل ومتصل ✅' : 'معطل ❌'; ?>
        </h3>
        
        <p style="margin: 10px 0 0 0;">
            <?php if ($botEnabled): ?>
                البوت التفاعلي يعمل الآن ويرد تلقائياً على رسائل العملاء
            <?php else: ?>
                البوت معطل حالياً. قم بتفعيله من إعدادات الإضافة
            <?php endif; ?>
        </p>
    </div>
    
    <!-- Bot Configuration -->
    <form method="post">
        <input type="hidden" name="tab" value="bot">
        
        <div class="bot-config" style="background: #ffffff; border: 1px solid #dee2e6; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
            <h3>⚙️ إعدادات البوت</h3>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-top: 15px;">
                <div class="form-group">
                    <label>🌐 اللغة الافتراضية:</label>
                    <select name="default_language" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                        <option value="ar" <?php echo $defaultLanguage === 'ar' ? 'selected' : ''; ?>>العربية 🇸🇦</option>
                        <option value="en" <?php echo $defaultLanguage === 'en' ? 'selected' : ''; ?>>English 🇺🇸</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>⏱️ وقت الاستجابة (ثواني):</label>
                    <input type="number" name="response_delay" value="2" min="0" max="10" 
                           style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                </div>
                
                <div class="form-group">
                    <label>🔄 إعادة المحاولة التلقائية:</label>
                    <select name="auto_retry" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                        <option value="yes">مفعل</option>
                        <option value="no">معطل</option>
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
    <div class="bot-flow" style="background: #f8f9fa; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
        <h3>🔄 سير عمل البوت التفاعلي</h3>
        
        <div class="flow-steps" style="margin-top: 15px;">
            <!-- Step 1: Initial Contact -->
            <div class="flow-step" style="display: flex; align-items: flex-start; margin-bottom: 20px; padding: 15px; background: white; border-radius: 6px; border-right: 4px solid #007bff;">
                <div style="background: #007bff; color: white; border-radius: 50%; width: 30px; height: 30px; display: flex; align-items: center; justify-content: center; margin-left: 15px; font-weight: bold;">1</div>
                <div>
                    <h4 style="margin: 0 0 8px 0; color: #007bff;">📨 استقبال الرسالة الأولى</h4>
                    <p style="margin: 0; color: #6c757d;">عندما يرسل العميل أول رسالة، يتم إرسال طلب الموافقة تلقائياً</p>
                    <div style="background: #e3f2fd; padding: 10px; border-radius: 4px; margin-top: 10px; font-family: monospace; font-size: 14px;">
                        "هل توافق على تلقي الرسائل من نظامنا؟<br>
                        Do you agree to receive messages from our system?"<br>
                        [موافق / Approve] [رفض / Decline]
                    </div>
                </div>
            </div>
            
            <!-- Step 2: Approval -->
            <div class="flow-step" style="display: flex; align-items: flex-start; margin-bottom: 20px; padding: 15px; background: white; border-radius: 6px; border-right: 4px solid #28a745;">
                <div style="background: #28a745; color: white; border-radius: 50%; width: 30px; height: 30px; display: flex; align-items: center; justify-content: center; margin-left: 15px; font-weight: bold;">2</div>
                <div>
                    <h4 style="margin: 0 0 8px 0; color: #28a745;">✅ الموافقة على التواصل</h4>
                    <p style="margin: 0; color: #6c757d;">إذا وافق العميل، يتم إرسال خيارات اللغة</p>
                    <div style="background: #d4edda; padding: 10px; border-radius: 4px; margin-top: 10px; font-family: monospace; font-size: 14px;">
                        "Please select your preferred language<br>
                        الرجاء اختيار لغتك المفضلة"<br>
                        [العربية] [English]
                    </div>
                </div>
            </div>
            
            <!-- Step 3: Language Selection -->
            <div class="flow-step" style="display: flex; align-items: flex-start; margin-bottom: 20px; padding: 15px; background: white; border-radius: 6px; border-right: 4px solid #ffc107;">
                <div style="background: #ffc107; color: white; border-radius: 50%; width: 30px; height: 30px; display: flex; align-items: center; justify-content: center; margin-left: 15px; font-weight: bold;">3</div>
                <div>
                    <h4 style="margin: 0 0 8px 0; color: #ffc107;">🌐 اختيار اللغة</h4>
                    <p style="margin: 0; color: #6c757d;">بعد اختيار اللغة، يتم إرسال رسالة ترحيبية مخصصة</p>
                    <div style="background: #fff3cd; padding: 10px; border-radius: 4px; margin-top: 10px; font-family: monospace; font-size: 14px;">
                        <strong>باللغة العربية:</strong><br>
                        "🎉 أهلاً وسهلاً!<br>
                        نحن سعداء لانضمامك إلينا..."<br><br>
                        <strong>باللغة الإنجليزية:</strong><br>
                        "🎉 Welcome!<br>
                        We're happy to have you with us..."
                    </div>
                </div>
            </div>
            
            <!-- Step 4: Interactive Commands -->
            <div class="flow-step" style="display: flex; align-items: flex-start; margin-bottom: 20px; padding: 15px; background: white; border-radius: 6px; border-right: 4px solid #6f42c1;">
                <div style="background: #6f42c1; color: white; border-radius: 50%; width: 30px; height: 30px; display: flex; align-items: center; justify-content: center; margin-left: 15px; font-weight: bold;">4</div>
                <div>
                    <h4 style="margin: 0 0 8px 0; color: #6f42c1;">🎛️ الأوامر التفاعلية</h4>
                    <p style="margin: 0; color: #6c757d;">البوت يتفاعل مع أوامر العملاء ويقدم المساعدة المناسبة</p>
                    <div style="background: #f3e5f5; padding: 10px; border-radius: 4px; margin-top: 10px;">
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; font-size: 14px;">
                            <div><strong>العربية:</strong> مساعدة، مبيعات، دعم</div>
                            <div><strong>English:</strong> help, sales, support</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Quick Response Templates -->
    <div class="response-templates" style="background: #ffffff; border: 1px solid #dee2e6; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
        <h3>📝 قوالب الردود السريعة</h3>
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-top: 15px;">
            <!-- Arabic Templates -->
            <div>
                <h4 style="color: #007bff;">🇸🇦 القوالب العربية</h4>
                
                <div class="template-item" style="background: #f8f9fa; padding: 12px; border-radius: 6px; margin-bottom: 10px;">
                    <strong>رسالة الترحيب:</strong>
                    <div style="font-size: 14px; color: #6c757d; margin-top: 5px;">
                        🎉 أهلاً وسهلاً! نحن سعداء لانضمامك إلينا...
                    </div>
                </div>
                
                <div class="template-item" style="background: #f8f9fa; padding: 12px; border-radius: 6px; margin-bottom: 10px;">
                    <strong>رد المساعدة:</strong>
                    <div style="font-size: 14px; color: #6c757d; margin-top: 5px;">
                        كيف يمكننا مساعدتك؟ 🛍️ للمبيعات: اكتب 'مبيعات'...
                    </div>
                </div>
                
                <div class="template-item" style="background: #f8f9fa; padding: 12px; border-radius: 6px; margin-bottom: 10px;">
                    <strong>رد المبيعات:</strong>
                    <div style="font-size: 14px; color: #6c757d; margin-top: 5px;">
                        مرحباً! أنا هنا لمساعدتك في الاستفسارات التجارية...
                    </div>
                </div>
            </div>
            
            <!-- English Templates -->
            <div>
                <h4 style="color: #28a745;">🇺🇸 English Templates</h4>
                
                <div class="template-item" style="background: #f8f9fa; padding: 12px; border-radius: 6px; margin-bottom: 10px;">
                    <strong>Welcome Message:</strong>
                    <div style="font-size: 14px; color: #6c757d; margin-top: 5px;">
                        🎉 Welcome! We're happy to have you with us...
                    </div>
                </div>
                
                <div class="template-item" style="background: #f8f9fa; padding: 12px; border-radius: 6px; margin-bottom: 10px;">
                    <strong>Help Response:</strong>
                    <div style="font-size: 14px; color: #6c757d; margin-top: 5px;">
                        How can we help you? 🛍️ For sales: type 'sales'...
                    </div>
                </div>
                
                <div class="template-item" style="background: #f8f9fa; padding: 12px; border-radius: 6px; margin-bottom: 10px;">
                    <strong>Sales Response:</strong>
                    <div style="font-size: 14px; color: #6c757d; margin-top: 5px;">
                        Hello! I'm here to help with sales inquiries...
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Bot Analytics -->
    <div class="bot-analytics" style="background: #e3f2fd; padding: 20px; border-radius: 8px;">
        <h3>📊 إحصائيات البوت</h3>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 15px; margin-top: 15px;">
            <div class="stat-card" style="background: white; padding: 15px; border-radius: 6px; text-align: center;">
                <div style="font-size: 20px; font-weight: bold; color: #007bff;">24</div>
                <div style="color: #6c757d; font-size: 14px;">ردود تلقائية اليوم</div>
            </div>
            
            <div class="stat-card" style="background: white; padding: 15px; border-radius: 6px; text-align: center;">
                <div style="font-size: 20px; font-weight: bold; color: #28a745;">18</div>
                <div style="color: #6c757d; font-size: 14px;">موافقات جديدة</div>
            </div>
            
            <div class="stat-card" style="background: white; padding: 15px; border-radius: 6px; text-align: center;">
                <div style="font-size: 20px; font-weight: bold; color: #ffc107;">12</div>
                <div style="color: #6c757d; font-size: 14px;">استفسارات مبيعات</div>
            </div>
            
            <div class="stat-card" style="background: white; padding: 15px; border-radius: 6px; text-align: center;">
                <div style="font-size: 20px; font-weight: bold; color: #dc3545;">3</div>
                <div style="color: #6c757d; font-size: 14px;">طلبات دعم</div>
            </div>
        </div>
        
        <div style="margin-top: 15px; text-align: center;">
            <button class="btn btn-secondary" onclick="refreshAnalytics()">
                🔄 تحديث الإحصائيات
            </button>
        </div>
    </div>
</div>

<script>
function refreshAnalytics() {
    // Add AJAX call to refresh analytics
    alert('سيتم تحديث الإحصائيات...');
}

// Real-time bot status monitoring
setInterval(function() {
    // Check bot status via AJAX
}, 30000);
</script>