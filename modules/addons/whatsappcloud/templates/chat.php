<?php
/**
 * Chat Tab Template
 */

// Handle sending messages
if (isset($_POST['send_message']) && !empty($_POST['phone_number']) && !empty($_POST['message'])) {
    $api = new WhatsAppAPI($vars);
    $result = $api->sendMessage($_POST['phone_number'], $_POST['message']);
    
    if ($result['success']) {
        $successMessage = 'تم إرسال الرسالة بنجاح! ✅';
    } else {
        $errorMessage = 'فشل في إرسال الرسالة: ' . $result['error'];
    }
}

// Get conversations with pagination
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

$query = "SELECT c.*, 
                 (SELECT COUNT(*) FROM mod_whatsappcloud_messages m WHERE m.conversation_id = c.id) as message_count,
                 (SELECT m.content FROM mod_whatsappcloud_messages m WHERE m.conversation_id = c.id ORDER BY m.timestamp DESC LIMIT 1) as last_message,
                 (SELECT m.timestamp FROM mod_whatsappcloud_messages m WHERE m.conversation_id = c.id ORDER BY m.timestamp DESC LIMIT 1) as last_message_time
          FROM mod_whatsappcloud_conversations c 
          ORDER BY c.updated_at DESC 
          LIMIT $limit OFFSET $offset";

$conversations = full_query($query);

// Get total count for pagination
$countQuery = "SELECT COUNT(*) as total FROM mod_whatsappcloud_conversations";
$countResult = full_query($countQuery);
$totalConversations = $countResult->fetch_assoc()['total'];
$totalPages = ceil($totalConversations / $limit);
?>

<div class="chat-tab">
    <h2>💬 إدارة المحادثات والرسائل</h2>
    
    <!-- Success/Error Messages -->
    <?php if (isset($successMessage)): ?>
    <div class="message-success"><?php echo $successMessage; ?></div>
    <?php endif; ?>
    
    <?php if (isset($errorMessage)): ?>
    <div class="message-error"><?php echo $errorMessage; ?></div>
    <?php endif; ?>
    
    <!-- Quick Send Message -->
    <div class="quick-send" style="background: #ffffff; border: 1px solid #dee2e6; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
        <h3>📤 إرسال رسالة سريعة</h3>
        
        <form method="post" style="margin-top: 15px;">
            <input type="hidden" name="tab" value="chat">
            
            <div style="display: grid; grid-template-columns: 200px 1fr 150px; gap: 15px; align-items: end;">
                <div class="form-group" style="margin-bottom: 0;">
                    <label>رقم الهاتف:</label>
                    <input type="text" name="phone_number" placeholder="966xxxxxxxxx" required
                           style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                </div>
                
                <div class="form-group" style="margin-bottom: 0;">
                    <label>الرسالة:</label>
                    <textarea name="message" rows="3" placeholder="اكتب رسالتك هنا..." required
                              style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; resize: vertical;"></textarea>
                </div>
                
                <div>
                    <button type="submit" name="send_message" value="1" class="btn" style="width: 100%; height: 76px;">
                        📤 إرسال
                    </button>
                </div>
            </div>
        </form>
    </div>
    
    <!-- Quick Message Templates -->
    <div class="message-templates" style="background: #f8f9fa; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
        <h4>📋 قوالب الرسائل السريعة</h4>
        
        <div style="display: flex; gap: 10px; margin-top: 10px; flex-wrap: wrap;">
            <button class="template-btn" onclick="insertTemplate('مرحباً! كيف يمكننا مساعدتك اليوم؟')" 
                    style="background: #e3f2fd; border: 1px solid #2196f3; padding: 5px 10px; border-radius: 15px; cursor: pointer; font-size: 12px;">
                🤝 ترحيب
            </button>
            
            <button class="template-btn" onclick="insertTemplate('شكراً لتواصلك معنا. سيتم الرد عليك قريباً.')" 
                    style="background: #e8f5e8; border: 1px solid #4caf50; padding: 5px 10px; border-radius: 15px; cursor: pointer; font-size: 12px;">
                🙏 شكر
            </button>
            
            <button class="template-btn" onclick="insertTemplate('للمزيد من المعلومات، يرجى زيارة موقعنا الإلكتروني.')" 
                    style="background: #fff3e0; border: 1px solid #ff9800; padding: 5px 10px; border-radius: 15px; cursor: pointer; font-size: 12px;">
                ℹ️ معلومات
            </button>
            
            <button class="template-btn" onclick="insertTemplate('تم حل المشكلة. هل تحتاج لأي مساعدة أخرى؟')" 
                    style="background: #f3e5f5; border: 1px solid #9c27b0; padding: 5px 10px; border-radius: 15px; cursor: pointer; font-size: 12px;">
                ✅ حل المشكلة
            </button>
        </div>
    </div>
    
    <!-- Conversations List -->
    <div class="conversations-list" style="background: #ffffff; border: 1px solid #dee2e6; border-radius: 8px;">
        <div style="padding: 20px; border-bottom: 1px solid #dee2e6; background: #f8f9fa; border-radius: 8px 8px 0 0;">
            <h3 style="margin: 0;">📋 قائمة المحادثات (<?php echo number_format($totalConversations); ?> محادثة)</h3>
        </div>
        
        <?php if ($conversations && $conversations->num_rows > 0): ?>
        <div class="conversations-table">
            <?php while ($conversation = $conversations->fetch_assoc()): ?>
            <?php
                $lastMessage = $conversation['last_message'] ? json_decode($conversation['last_message'], true) : null;
                $lastMessageText = '';
                
                if ($lastMessage) {
                    if ($lastMessage['type'] === 'text') {
                        $lastMessageText = substr($lastMessage['text']['body'] ?? '', 0, 50) . '...';
                    } else {
                        $lastMessageText = '📎 ' . ucfirst($lastMessage['type']);
                    }
                }
            ?>
            
            <div class="conversation-item" style="padding: 15px 20px; border-bottom: 1px solid #f0f0f0; display: flex; align-items: center; hover:background-color: #f8f9fa;" 
                 onmouseover="this.style.backgroundColor='#f8f9fa'" onmouseout="this.style.backgroundColor='white'">
                
                <!-- Status Indicator -->
                <div style="margin-left: 15px;">
                    <div class="status-indicator <?php echo $conversation['status'] === 'active' ? 'status-connected' : ($conversation['status'] === 'pending' ? '' : 'status-disconnected'); ?>" 
                         style="width: 12px; height: 12px;"></div>
                </div>
                
                <!-- Contact Info -->
                <div style="flex: 1;">
                    <div style="display: flex; align-items: center; margin-bottom: 5px;">
                        <strong style="margin-left: 10px;">📞 <?php echo htmlspecialchars($conversation['phone_number']); ?></strong>
                        
                        <span style="background: <?php echo $conversation['status'] === 'active' ? '#d4edda' : ($conversation['status'] === 'pending' ? '#fff3cd' : '#f8d7da'); ?>; 
                                     color: <?php echo $conversation['status'] === 'active' ? '#155724' : ($conversation['status'] === 'pending' ? '#856404' : '#721c24'); ?>; 
                                     padding: 2px 8px; border-radius: 10px; font-size: 11px;">
                            <?php 
                            $statusLabels = ['active' => 'نشط', 'pending' => 'معلق', 'completed' => 'مكتمل'];
                            echo $statusLabels[$conversation['status']] ?? $conversation['status'];
                            ?>
                        </span>
                        
                        <span style="margin-right: 10px; font-size: 12px; color: #6c757d;">
                            <?php echo $conversation['language'] === 'ar' ? '🇸🇦 عربي' : '🇺🇸 English'; ?>
                        </span>
                    </div>
                    
                    <div style="color: #6c757d; font-size: 14px;">
                        <?php if ($lastMessageText): ?>
                            💬 <?php echo htmlspecialchars($lastMessageText); ?>
                        <?php else: ?>
                            لا توجد رسائل بعد
                        <?php endif; ?>
                    </div>
                    
                    <div style="font-size: 12px; color: #999; margin-top: 3px;">
                        📊 <?php echo number_format($conversation['message_count']); ?> رسالة
                        <?php if ($conversation['last_message_time']): ?>
                            • آخر نشاط: <?php echo date('Y-m-d H:i', strtotime($conversation['last_message_time'])); ?>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Action Buttons -->
                <div style="display: flex; gap: 5px;">
                    <button onclick="viewConversation(<?php echo $conversation['id']; ?>, '<?php echo htmlspecialchars($conversation['phone_number']); ?>')" 
                            class="btn btn-secondary" style="padding: 5px 10px; font-size: 12px;">
                        👁️ عرض
                    </button>
                    
                    <button onclick="quickReply('<?php echo htmlspecialchars($conversation['phone_number']); ?>')" 
                            class="btn" style="padding: 5px 10px; font-size: 12px;">
                        💬 رد
                    </button>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
        
        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>
        <div style="padding: 15px 20px; border-top: 1px solid #dee2e6; background: #f8f9fa; display: flex; justify-content: center; align-items: center; gap: 10px;">
            <?php if ($page > 1): ?>
                <a href="?module=whatsappcloud&tab=chat&page=<?php echo $page - 1; ?>" class="btn btn-secondary" style="padding: 5px 10px; font-size: 12px;">⬅️ السابق</a>
            <?php endif; ?>
            
            <span style="margin: 0 15px; color: #6c757d;">
                صفحة <?php echo $page; ?> من <?php echo $totalPages; ?>
            </span>
            
            <?php if ($page < $totalPages): ?>
                <a href="?module=whatsappcloud&tab=chat&page=<?php echo $page + 1; ?>" class="btn btn-secondary" style="padding: 5px 10px; font-size: 12px;">التالي ➡️</a>
            <?php endif; ?>
        </div>
        <?php endif; ?>
        
        <?php else: ?>
        <div style="padding: 40px; text-align: center; color: #6c757d;">
            <div style="font-size: 48px; margin-bottom: 15px;">💬</div>
            <h4>لا توجد محادثات بعد</h4>
            <p>سيتم عرض المحادثات هنا عندما يبدأ العملاء بالتواصل معك</p>
        </div>
        <?php endif; ?>
    </div>
    
    <!-- Chat Statistics -->
    <div class="chat-stats" style="margin-top: 20px; background: #e3f2fd; padding: 20px; border-radius: 8px;">
        <h3>📊 إحصائيات المحادثات</h3>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 15px; margin-top: 15px;">
            <?php
            $statsQuery = "SELECT 
                COUNT(*) as total,
                COUNT(CASE WHEN status = 'active' THEN 1 END) as active,
                COUNT(CASE WHEN status = 'pending' THEN 1 END) as pending,
                COUNT(CASE WHEN language = 'ar' THEN 1 END) as arabic,
                COUNT(CASE WHEN language = 'en' THEN 1 END) as english,
                COUNT(CASE WHEN DATE(created_at) = CURDATE() THEN 1 END) as today
                FROM mod_whatsappcloud_conversations";
            $statsResult = full_query($statsQuery);
            $chatStats = $statsResult->fetch_assoc();
            ?>
            
            <div class="stat-card" style="background: white; padding: 15px; border-radius: 6px; text-align: center;">
                <div style="font-size: 20px; font-weight: bold; color: #007bff;">
                    <?php echo number_format($chatStats['total'] ?? 0); ?>
                </div>
                <div style="color: #6c757d; font-size: 14px;">إجمالي المحادثات</div>
            </div>
            
            <div class="stat-card" style="background: white; padding: 15px; border-radius: 6px; text-align: center;">
                <div style="font-size: 20px; font-weight: bold; color: #28a745;">
                    <?php echo number_format($chatStats['active'] ?? 0); ?>
                </div>
                <div style="color: #6c757d; font-size: 14px;">المحادثات النشطة</div>
            </div>
            
            <div class="stat-card" style="background: white; padding: 15px; border-radius: 6px; text-align: center;">
                <div style="font-size: 20px; font-weight: bold; color: #ffc107;">
                    <?php echo number_format($chatStats['pending'] ?? 0); ?>
                </div>
                <div style="color: #6c757d; font-size: 14px;">محادثات معلقة</div>
            </div>
            
            <div class="stat-card" style="background: white; padding: 15px; border-radius: 6px; text-align: center;">
                <div style="font-size: 20px; font-weight: bold; color: #dc3545;">
                    <?php echo number_format($chatStats['today'] ?? 0); ?>
                </div>
                <div style="color: #6c757d; font-size: 14px;">محادثات اليوم</div>
            </div>
        </div>
    </div>
</div>

<!-- Conversation Modal -->
<div id="conversationModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000;">
    <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); background: white; width: 80%; max-width: 800px; height: 80%; border-radius: 8px; display: flex; flex-direction: column;">
        <!-- Modal Header -->
        <div style="padding: 20px; border-bottom: 1px solid #dee2e6; display: flex; justify-content: space-between; align-items: center;">
            <h3 style="margin: 0;">💬 محادثة مع <span id="modalPhoneNumber"></span></h3>
            <button onclick="closeConversationModal()" style="background: none; border: none; font-size: 24px; cursor: pointer;">✕</button>
        </div>
        
        <!-- Messages Area -->
        <div id="messagesArea" style="flex: 1; padding: 20px; overflow-y: auto; background: #f8f9fa;">
            <!-- Messages will be loaded here -->
        </div>
        
        <!-- Reply Area -->
        <div style="padding: 20px; border-top: 1px solid #dee2e6; background: white;">
            <form onsubmit="sendReply(event)">
                <div style="display: flex; gap: 10px;">
                    <input type="text" id="replyMessage" placeholder="اكتب ردك هنا..." required
                           style="flex: 1; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
                    <button type="submit" class="btn">📤 إرسال</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function insertTemplate(template) {
    const messageTextarea = document.querySelector('textarea[name="message"]');
    if (messageTextarea) {
        messageTextarea.value = template;
        messageTextarea.focus();
    }
}

function quickReply(phoneNumber) {
    const phoneInput = document.querySelector('input[name="phone_number"]');
    const messageTextarea = document.querySelector('textarea[name="message"]');
    
    if (phoneInput && messageTextarea) {
        phoneInput.value = phoneNumber;
        messageTextarea.focus();
        messageTextarea.scrollIntoView({ behavior: 'smooth' });
    }
}

function viewConversation(conversationId, phoneNumber) {
    document.getElementById('modalPhoneNumber').textContent = phoneNumber;
    document.getElementById('conversationModal').style.display = 'block';
    
    // Load messages via AJAX (placeholder)
    document.getElementById('messagesArea').innerHTML = `
        <div style="text-align: center; padding: 40px; color: #6c757d;">
            <div style="font-size: 24px; margin-bottom: 10px;">📱</div>
            <p>جاري تحميل الرسائل...</p>
        </div>
    `;
    
    // Simulate loading messages
    setTimeout(() => {
        document.getElementById('messagesArea').innerHTML = `
            <div style="margin-bottom: 15px;">
                <div style="background: #e3f2fd; padding: 10px; border-radius: 8px; max-width: 70%; margin-bottom: 5px;">
                    مرحباً، أحتاج مساعدة في خدماتكم
                </div>
                <div style="font-size: 12px; color: #6c757d;">العميل • منذ ساعتين</div>
            </div>
            
            <div style="margin-bottom: 15px; text-align: left;">
                <div style="background: #25d366; color: white; padding: 10px; border-radius: 8px; max-width: 70%; margin-bottom: 5px; margin-right: auto;">
                    مرحباً! سعداء بتواصلك معنا. كيف يمكننا مساعدتك؟
                </div>
                <div style="font-size: 12px; color: #6c757d;">أنت • منذ ساعة</div>
            </div>
        `;
    }, 1000);
}

function closeConversationModal() {
    document.getElementById('conversationModal').style.display = 'none';
}

function sendReply(event) {
    event.preventDefault();
    const message = document.getElementById('replyMessage').value;
    const phoneNumber = document.getElementById('modalPhoneNumber').textContent;
    
    if (message.trim()) {
        // Add message to chat (placeholder)
        const messagesArea = document.getElementById('messagesArea');
        messagesArea.innerHTML += `
            <div style="margin-bottom: 15px; text-align: left;">
                <div style="background: #25d366; color: white; padding: 10px; border-radius: 8px; max-width: 70%; margin-bottom: 5px; margin-right: auto;">
                    ${message}
                </div>
                <div style="font-size: 12px; color: #6c757d;">أنت • الآن</div>
            </div>
        `;
        
        document.getElementById('replyMessage').value = '';
        messagesArea.scrollTop = messagesArea.scrollHeight;
        
        // Here you would send the actual message via AJAX
        alert('تم إرسال الرسالة! (هذا مثال فقط)');
    }
}

// Close modal when clicking outside
document.getElementById('conversationModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeConversationModal();
    }
});

// Auto-refresh conversations every 30 seconds
setInterval(function() {
    // Add AJAX call to refresh conversations list
}, 30000);
</script>