<?php
/**
 * Details Tab Template
 */

$api = new WhatsAppAPI($vars);

// Test connection
$connectionTest = $api->testWebhook();
$isConnected = $connectionTest['success'];

// Get statistics
$query = "SELECT 
    COUNT(*) as total_conversations,
    COUNT(CASE WHEN status = 'active' THEN 1 END) as active_conversations,
    COUNT(CASE WHEN status = 'pending' THEN 1 END) as pending_conversations
    FROM mod_whatsappcloud_conversations";
$result = full_query($query);
$stats = $result->fetch_assoc();

$query = "SELECT COUNT(*) as total_messages FROM mod_whatsappcloud_messages";
$result = full_query($query);
$messageStats = $result->fetch_assoc();
?>

<div class="details-tab">
    <h2>📊 حالة الاتصال والإحصائيات</h2>
    
    <!-- Connection Status -->
    <div class="status-card" style="background: <?php echo $isConnected ? '#d4edda' : '#f8d7da'; ?>; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
        <h3>
            <span class="status-indicator <?php echo $isConnected ? 'status-connected' : 'status-disconnected'; ?>"></span>
            حالة الاتصال: <?php echo $isConnected ? 'متصل ✅' : 'غير متصل ❌'; ?>
        </h3>
        
        <?php if ($isConnected): ?>
            <p style="margin: 10px 0 0 0; color: #155724;">تم الاتصال بواتساب كلاود بنجاح!</p>
        <?php else: ?>
            <p style="margin: 10px 0 0 0; color: #721c24;">فشل في الاتصال بواتساب كلاود. يرجى التحقق من الإعدادات.</p>
        <?php endif; ?>
    </div>
    
    <!-- Configuration Details -->
    <div class="config-details" style="background: #f8f9fa; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
        <h3>🔧 تفاصيل التكوين</h3>
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-top: 15px;">
            <div>
                <strong>معرف التطبيق:</strong><br>
                <code style="background: #e9ecef; padding: 2px 6px; border-radius: 3px;">
                    <?php echo substr($vars['app_id'], 0, 8) . '...'; ?>
                </code>
            </div>
            
            <div>
                <strong>معرف رقم الهاتف:</strong><br>
                <code style="background: #e9ecef; padding: 2px 6px; border-radius: 3px;">
                    <?php echo substr($vars['phone_number_id'], 0, 8) . '...'; ?>
                </code>
            </div>
            
            <div>
                <strong>معرف حساب الأعمال:</strong><br>
                <code style="background: #e9ecef; padding: 2px 6px; border-radius: 3px;">
                    <?php echo substr($vars['business_account_id'], 0, 8) . '...'; ?>
                </code>
            </div>
            
            <div>
                <strong>البوت التفاعلي:</strong><br>
                <span style="color: <?php echo $vars['enable_bot'] === 'on' ? '#28a745' : '#dc3545'; ?>;">
                    <?php echo $vars['enable_bot'] === 'on' ? 'مفعل ✅' : 'معطل ❌'; ?>
                </span>
            </div>
        </div>
    </div>
    
    <!-- Statistics -->
    <div class="statistics" style="background: #ffffff; border: 1px solid #dee2e6; padding: 20px; border-radius: 8px;">
        <h3>📈 الإحصائيات</h3>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin-top: 15px;">
            <div class="stat-card" style="background: #e3f2fd; padding: 15px; border-radius: 6px; text-align: center;">
                <div style="font-size: 24px; font-weight: bold; color: #1976d2;">
                    <?php echo number_format($stats['total_conversations'] ?? 0); ?>
                </div>
                <div style="color: #424242; margin-top: 5px;">إجمالي المحادثات</div>
            </div>
            
            <div class="stat-card" style="background: #e8f5e8; padding: 15px; border-radius: 6px; text-align: center;">
                <div style="font-size: 24px; font-weight: bold; color: #2e7d32;">
                    <?php echo number_format($stats['active_conversations'] ?? 0); ?>
                </div>
                <div style="color: #424242; margin-top: 5px;">المحادثات النشطة</div>
            </div>
            
            <div class="stat-card" style="background: #fff3e0; padding: 15px; border-radius: 6px; text-align: center;">
                <div style="font-size: 24px; font-weight: bold; color: #f57c00;">
                    <?php echo number_format($stats['pending_conversations'] ?? 0); ?>
                </div>
                <div style="color: #424242; margin-top: 5px;">المحادثات المعلقة</div>
            </div>
            
            <div class="stat-card" style="background: #f3e5f5; padding: 15px; border-radius: 6px; text-align: center;">
                <div style="font-size: 24px; font-weight: bold; color: #7b1fa2;">
                    <?php echo number_format($messageStats['total_messages'] ?? 0); ?>
                </div>
                <div style="color: #424242; margin-top: 5px;">إجمالي الرسائل</div>
            </div>
        </div>
    </div>
    
    <!-- Quick Actions -->
    <div class="quick-actions" style="margin-top: 20px;">
        <h3>⚡ إجراءات سريعة</h3>
        
        <div style="display: flex; gap: 10px; margin-top: 15px; flex-wrap: wrap;">
            <a href="?module=whatsappcloud&tab=webhook" class="btn">
                🔗 إعداد الويب هوك
            </a>
            
            <a href="?module=whatsappcloud&tab=bot" class="btn btn-secondary">
                🤖 إعداد البوت
            </a>
            
            <a href="?module=whatsappcloud&tab=chat" class="btn btn-secondary">
                💬 عرض المحادثات
            </a>
            
            <form method="post" style="display: inline;">
                <input type="hidden" name="test_connection" value="1">
                <button type="submit" class="btn btn-secondary">
                    🔄 اختبار الاتصال
                </button>
            </form>
        </div>
    </div>
    
    <!-- Recent Activity -->
    <?php
    $query = "SELECT c.phone_number, c.status, c.language, c.created_at, 
                     (SELECT COUNT(*) FROM mod_whatsappcloud_messages m WHERE m.conversation_id = c.id) as message_count
              FROM mod_whatsappcloud_conversations c 
              ORDER BY c.updated_at DESC 
              LIMIT 5";
    $result = full_query($query);
    ?>
    
    <?php if ($result && $result->num_rows > 0): ?>
    <div class="recent-activity" style="margin-top: 20px; background: #ffffff; border: 1px solid #dee2e6; padding: 20px; border-radius: 8px;">
        <h3>🕒 النشاط الأخير</h3>
        
        <div style="margin-top: 15px;">
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background: #f8f9fa;">
                        <th style="padding: 10px; text-align: right; border: 1px solid #dee2e6;">رقم الهاتف</th>
                        <th style="padding: 10px; text-align: center; border: 1px solid #dee2e6;">الحالة</th>
                        <th style="padding: 10px; text-align: center; border: 1px solid #dee2e6;">اللغة</th>
                        <th style="padding: 10px; text-align: center; border: 1px solid #dee2e6;">عدد الرسائل</th>
                        <th style="padding: 10px; text-align: center; border: 1px solid #dee2e6;">تاريخ الإنشاء</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td style="padding: 10px; border: 1px solid #dee2e6;">
                            <?php echo htmlspecialchars($row['phone_number']); ?>
                        </td>
                        <td style="padding: 10px; text-align: center; border: 1px solid #dee2e6;">
                            <span style="background: <?php echo $row['status'] === 'active' ? '#d4edda' : ($row['status'] === 'pending' ? '#fff3cd' : '#f8d7da'); ?>; 
                                         color: <?php echo $row['status'] === 'active' ? '#155724' : ($row['status'] === 'pending' ? '#856404' : '#721c24'); ?>; 
                                         padding: 2px 8px; border-radius: 12px; font-size: 12px;">
                                <?php 
                                $statusLabels = ['active' => 'نشط', 'pending' => 'معلق', 'completed' => 'مكتمل'];
                                echo $statusLabels[$row['status']] ?? $row['status'];
                                ?>
                            </span>
                        </td>
                        <td style="padding: 10px; text-align: center; border: 1px solid #dee2e6;">
                            <?php echo $row['language'] === 'ar' ? '🇸🇦 عربي' : '🇺🇸 English'; ?>
                        </td>
                        <td style="padding: 10px; text-align: center; border: 1px solid #dee2e6;">
                            <?php echo number_format($row['message_count']); ?>
                        </td>
                        <td style="padding: 10px; text-align: center; border: 1px solid #dee2e6;">
                            <?php echo date('Y-m-d H:i', strtotime($row['created_at'])); ?>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?>
</div>