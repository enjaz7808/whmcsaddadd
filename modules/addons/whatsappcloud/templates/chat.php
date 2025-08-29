<?php
/**
 * Chat Tab Template
 */

// Get recent conversations
$conversations = [];
$messages = [];

try {
    $query = "SELECT c.*, COUNT(m.id) as message_count 
              FROM mod_whatsappcloud_conversations c 
              LEFT JOIN mod_whatsappcloud_messages m ON c.id = m.conversation_id 
              GROUP BY c.id 
              ORDER BY c.updated_at DESC 
              LIMIT 20";
    $result = full_query($query);
    
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $conversations[] = $row;
        }
    }
} catch (Exception $e) {
    // Handle database errors
}

// Get selected conversation messages
$selectedConversation = null;
$conversationId = $_GET['conversation_id'] ?? null;

if ($conversationId) {
    foreach ($conversations as $conv) {
        if ($conv['id'] == $conversationId) {
            $selectedConversation = $conv;
            break;
        }
    }
    
    if ($selectedConversation) {
        try {
            $query = "SELECT * FROM mod_whatsappcloud_messages 
                      WHERE conversation_id = ? 
                      ORDER BY created_at ASC";
            $stmt = mysqli_prepare($GLOBALS['dbh'], $query);
            mysqli_stmt_bind_param($stmt, 'i', $conversationId);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            
            if ($result) {
                while ($row = mysqli_fetch_assoc($result)) {
                    $messages[] = $row;
                }
            }
        } catch (Exception $e) {
            // Handle error
        }
    }
}
?>

<div class="chat-tab">
    <h2>💬 إدارة المحادثات والدردشة</h2>
    
    <div style="display: grid; grid-template-columns: 300px 1fr; gap: 20px; height: 700px;">
        
        <!-- Conversations List -->
        <div class="info-card" style="margin-bottom: 0;">
            <h3>📋 المحادثات</h3>
            
            <div style="max-height: 600px; overflow-y: auto;">
                <?php if (empty($conversations)): ?>
                    <div style="text-align: center; color: #6c757d; padding: 20px;">
                        📭 لا توجد محادثات حتى الآن<br>
                        <small>ستظهر هنا المحادثات عند استقبال رسائل</small>
                    </div>
                <?php else: ?>
                    <?php foreach ($conversations as $conv): ?>
                        <div class="conversation-item" 
                             style="padding: 12px; border-bottom: 1px solid #eee; cursor: pointer; border-radius: 6px; margin-bottom: 5px; <?php echo $selectedConversation && $selectedConversation['id'] == $conv['id'] ? 'background: #e3f2fd; border-color: #2196F3;' : ''; ?>"
                             onclick="selectConversation(<?php echo $conv['id']; ?>)">
                            
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <div class="contact-avatar" style="width: 40px; height: 40px; background: #25D366; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold;">
                                    <?php echo strtoupper(substr($conv['contact_name'] ?? 'U', 0, 1)); ?>
                                </div>
                                
                                <div style="flex: 1; min-width: 0;">
                                    <div style="font-weight: 600; margin-bottom: 2px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                        <?php echo htmlspecialchars($conv['contact_name'] ?? $conv['phone_number']); ?>
                                    </div>
                                    
                                    <div style="font-size: 12px; color: #6c757d; display: flex; justify-content: space-between;">
                                        <span><?php echo htmlspecialchars($conv['phone_number']); ?></span>
                                        <span class="status-badge status-<?php echo $conv['status']; ?>" style="padding: 2px 6px; border-radius: 10px; font-size: 10px;">
                                            <?php 
                                            $statusLabels = [
                                                'pending' => 'في الانتظار',
                                                'active' => 'نشط',
                                                'completed' => 'مكتمل'
                                            ];
                                            echo $statusLabels[$conv['status']] ?? $conv['status'];
                                            ?>
                                        </span>
                                    </div>
                                    
                                    <div style="font-size: 11px; color: #999; margin-top: 2px;">
                                        💬 <?php echo $conv['message_count']; ?> رسالة | 
                                        🌍 <?php echo $conv['language'] === 'ar' ? 'عربي' : 'English'; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Chat Interface -->
        <div class="chat-container">
            <?php if ($selectedConversation): ?>
                <!-- Chat Header -->
                <div class="chat-header">
                    <div class="contact-avatar" style="width: 35px; height: 35px; background: rgba(255,255,255,0.2); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold;">
                        <?php echo strtoupper(substr($selectedConversation['contact_name'] ?? 'U', 0, 1)); ?>
                    </div>
                    
                    <div>
                        <div style="font-weight: 600;">
                            <?php echo htmlspecialchars($selectedConversation['contact_name'] ?? $selectedConversation['phone_number']); ?>
                        </div>
                        <div style="font-size: 12px; opacity: 0.9;">
                            📞 <?php echo htmlspecialchars($selectedConversation['phone_number']); ?> | 
                            🌍 <?php echo $selectedConversation['language'] === 'ar' ? 'عربي' : 'English'; ?>
                        </div>
                    </div>
                    
                    <div style="margin-right: auto;">
                        <span class="status-badge status-<?php echo $selectedConversation['status']; ?>" style="padding: 4px 8px; border-radius: 12px; font-size: 11px; background: rgba(255,255,255,0.2);">
                            <?php 
                            $statusLabels = [
                                'pending' => 'في الانتظار',
                                'active' => 'نشط', 
                                'completed' => 'مكتمل'
                            ];
                            echo $statusLabels[$selectedConversation['status']] ?? $selectedConversation['status'];
                            ?>
                        </span>
                    </div>
                </div>
                
                <!-- Messages Area -->
                <div class="chat-messages" id="chat-messages">
                    <?php if (empty($messages)): ?>
                        <div style="text-align: center; color: #6c757d; padding: 40px;">
                            💬 لا توجد رسائل في هذه المحادثة<br>
                            <small>ابدأ محادثة بإرسال رسالة أدناه</small>
                        </div>
                    <?php else: ?>
                        <?php foreach ($messages as $msg): ?>
                            <div class="message <?php echo $msg['direction']; ?>">
                                <div class="message-content">
                                    <?php 
                                    if ($msg['message_type'] === 'text') {
                                        echo nl2br(htmlspecialchars($msg['content']));
                                    } else {
                                        echo '<em>رسالة تفاعلية أو ملف</em>';
                                    }
                                    ?>
                                </div>
                                <div class="message-time">
                                    <?php echo date('H:i', strtotime($msg['created_at'])); ?>
                                    <?php if ($msg['direction'] === 'outbound'): ?>
                                        <span style="margin-right: 5px;">
                                            <?php 
                                            $statusIcons = [
                                                'sent' => '✓',
                                                'delivered' => '✓✓',
                                                'read' => '✓✓',
                                                'failed' => '❌'
                                            ];
                                            echo $statusIcons[$msg['status']] ?? '⏳';
                                            ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                
                <!-- Message Input -->
                <form method="post" class="chat-input">
                    <input type="hidden" name="tab" value="chat">
                    <input type="hidden" name="phone_number" value="<?php echo htmlspecialchars($selectedConversation['phone_number']); ?>">
                    
                    <input type="text" name="message" placeholder="اكتب رسالتك هنا..." required 
                           style="flex: 1; padding: 12px; border: 1px solid #ddd; border-radius: 20px; margin-bottom: 0;">
                    
                    <button type="submit" name="send_message" value="1" class="btn" style="border-radius: 50%; width: 45px; height: 45px; padding: 0; display: flex; align-items: center; justify-content: center;">
                        📤
                    </button>
                </form>
                
            <?php else: ?>
                <!-- No Conversation Selected -->
                <div style="display: flex; align-items: center; justify-content: center; height: 100%; color: #6c757d; text-align: center;">
                    <div>
                        <div style="font-size: 48px; margin-bottom: 15px;">💬</div>
                        <h4>اختر محادثة للبدء</h4>
                        <p>حدد محادثة من القائمة لعرض الرسائل والتواصل مع العميل</p>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Quick Send -->
    <div class="info-card" style="margin-top: 20px;">
        <h3>⚡ إرسال سريع</h3>
        
        <form method="post" style="display: flex; gap: 15px; align-items: end; flex-wrap: wrap;">
            <input type="hidden" name="tab" value="chat">
            
            <div class="form-group" style="flex: 1; min-width: 200px; margin-bottom: 0;">
                <label>📱 رقم الواتساب:</label>
                <input type="text" name="phone_number" placeholder="+966xxxxxxxxx" required 
                       style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
            </div>
            
            <div class="form-group" style="flex: 2; min-width: 300px; margin-bottom: 0;">
                <label>💬 الرسالة:</label>
                <input type="text" name="message" placeholder="اكتب رسالتك هنا..." required 
                       style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
            </div>
            
            <button type="submit" name="send_message" value="1" class="btn">
                📤 إرسال
            </button>
        </form>
    </div>
    
    <!-- Message Templates -->
    <div class="info-card">
        <h3>📝 قوالب الرسائل</h3>
        
        <div class="config-grid">
            <div class="template-group">
                <h4>🎉 رسائل ترحيبية</h4>
                <div class="template-item" onclick="useTemplate(this)" style="background: #f8f9fa; padding: 10px; border-radius: 6px; cursor: pointer; margin-bottom: 8px; border: 1px solid #dee2e6;">
                    مرحباً بك! نحن هنا لمساعدتك. كيف يمكننا خدمتك؟
                </div>
                <div class="template-item" onclick="useTemplate(this)" style="background: #f8f9fa; padding: 10px; border-radius: 6px; cursor: pointer; margin-bottom: 8px; border: 1px solid #dee2e6;">
                    شكراً لتواصلك معنا. سيتم الرد عليك في أقرب وقت ممكن.
                </div>
            </div>
            
            <div class="template-group">
                <h4>💼 رسائل مبيعات</h4>
                <div class="template-item" onclick="useTemplate(this)" style="background: #f8f9fa; padding: 10px; border-radius: 6px; cursor: pointer; margin-bottom: 8px; border: 1px solid #dee2e6;">
                    لدينا عروض خاصة حالياً! هل تود معرفة المزيد؟
                </div>
                <div class="template-item" onclick="useTemplate(this)" style="background: #f8f9fa; padding: 10px; border-radius: 6px; cursor: pointer; margin-bottom: 8px; border: 1px solid #dee2e6;">
                    يمكنك الاطلاع على جميع خدماتنا من خلال موقعنا الإلكتروني.
                </div>
            </div>
            
            <div class="template-group">
                <h4>🎫 رسائل دعم فني</h4>
                <div class="template-item" onclick="useTemplate(this)" style="background: #f8f9fa; padding: 10px; border-radius: 6px; cursor: pointer; margin-bottom: 8px; border: 1px solid #dee2e6;">
                    تم فتح تذكرة دعم برقم #{{ticket_id}}. سيتم التواصل معك قريباً.
                </div>
                <div class="template-item" onclick="useTemplate(this)" style="background: #f8f9fa; padding: 10px; border-radius: 6px; cursor: pointer; margin-bottom: 8px; border: 1px solid #dee2e6;">
                    تم حل المشكلة بنجاح. هل تحتاج لأي مساعدة إضافية؟
                </div>
            </div>
            
            <div class="template-group">
                <h4>📞 رسائل متابعة</h4>
                <div class="template-item" onclick="useTemplate(this)" style="background: #f8f9fa; padding: 10px; border-radius: 6px; cursor: pointer; margin-bottom: 8px; border: 1px solid #dee2e6;">
                    كيف كانت تجربتك معنا؟ نحن نقدر ملاحظاتك.
                </div>
                <div class="template-item" onclick="useTemplate(this)" style="background: #f8f9fa; padding: 10px; border-radius: 6px; cursor: pointer; margin-bottom: 8px; border: 1px solid #dee2e6;">
                    شكراً لاختيارك خدماتنا. نتطلع لخدمتك مرة أخرى.
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function selectConversation(conversationId) {
    const currentUrl = new URL(window.location);
    currentUrl.searchParams.set('conversation_id', conversationId);
    window.location.href = currentUrl.toString();
}

function useTemplate(element) {
    const templateText = element.textContent.trim();
    const messageInputs = document.querySelectorAll('input[name="message"]');
    
    messageInputs.forEach(input => {
        input.value = templateText;
        input.focus();
    });
    
    // Highlight the selected template briefly
    element.style.background = '#25D366';
    element.style.color = 'white';
    setTimeout(() => {
        element.style.background = '#f8f9fa';
        element.style.color = '';
    }, 500);
}

// Auto-scroll to bottom of messages
document.addEventListener('DOMContentLoaded', function() {
    const messagesContainer = document.getElementById('chat-messages');
    if (messagesContainer) {
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    }
});

// Auto-refresh messages every 30 seconds
setInterval(function() {
    if (window.location.href.includes('conversation_id=')) {
        // Only refresh if a conversation is selected
        location.reload();
    }
}, 30000);
</script>

<style>
.status-pending {
    background: #fff3cd;
    color: #856404;
}

.status-active {
    background: #d4edda;
    color: #155724;
}

.status-completed {
    background: #d1ecf1;
    color: #0c5460;
}

.conversation-item:hover {
    background: #f8f9fa !important;
}

.template-item:hover {
    background: #e9ecef !important;
    border-color: #25D366 !important;
}

.message.outbound .message-time {
    color: rgba(255,255,255,0.8);
}

.message.inbound .message-time {
    color: #6c757d;
}
</style>