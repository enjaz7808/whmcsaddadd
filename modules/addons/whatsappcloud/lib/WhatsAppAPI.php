<?php
/**
 * WhatsApp Cloud API Integration Class
 */

class WhatsAppAPI
{
    private $accessToken;
    private $phoneNumberId;
    private $businessAccountId;
    private $apiVersion = 'v18.0';
    private $baseUrl = 'https://graph.facebook.com/';
    
    public function __construct($config)
    {
        $this->accessToken = $config['access_token'];
        $this->phoneNumberId = $config['phone_number_id'];
        $this->businessAccountId = $config['business_account_id'];
    }
    
    /**
     * Send a text message
     */
    public function sendMessage($to, $message, $preview_url = false)
    {
        $url = $this->baseUrl . $this->apiVersion . '/' . $this->phoneNumberId . '/messages';
        
        $data = [
            'messaging_product' => 'whatsapp',
            'recipient_type' => 'individual',
            'to' => $this->formatPhoneNumber($to),
            'type' => 'text',
            'text' => [
                'preview_url' => $preview_url,
                'body' => $message
            ]
        ];
        
        return $this->makeRequest($url, $data);
    }
    
    /**
     * Send interactive message with buttons
     */
    public function sendInteractiveMessage($to, $bodyText, $buttons)
    {
        $url = $this->baseUrl . $this->apiVersion . '/' . $this->phoneNumberId . '/messages';
        
        $interactiveButtons = [];
        foreach ($buttons as $button) {
            $interactiveButtons[] = [
                'type' => 'reply',
                'reply' => [
                    'id' => $button['id'],
                    'title' => $button['title']
                ]
            ];
        }
        
        $data = [
            'messaging_product' => 'whatsapp',
            'recipient_type' => 'individual',
            'to' => $this->formatPhoneNumber($to),
            'type' => 'interactive',
            'interactive' => [
                'type' => 'button',
                'body' => [
                    'text' => $bodyText
                ],
                'action' => [
                    'buttons' => $interactiveButtons
                ]
            ]
        ];
        
        return $this->makeRequest($url, $data);
    }
    
    /**
     * Send language selection message
     */
    public function sendLanguageSelection($to)
    {
        $buttons = [
            ['id' => 'lang_ar', 'title' => 'العربية'],
            ['id' => 'lang_en', 'title' => 'English']
        ];
        
        return $this->sendInteractiveMessage(
            $to,
            "Please select your preferred language / الرجاء اختيار لغتك المفضلة",
            $buttons
        );
    }
    
    /**
     * Send approval request message
     */
    public function sendApprovalRequest($to)
    {
        $buttons = [
            ['id' => 'approve_yes', 'title' => 'موافق / Approve'],
            ['id' => 'approve_no', 'title' => 'رفض / Decline']
        ];
        
        return $this->sendInteractiveMessage(
            $to,
            "هل توافق على تلقي الرسائل من نظامنا؟\nDo you agree to receive messages from our system?",
            $buttons
        );
    }
    
    /**
     * Send welcome message
     */
    public function sendWelcomeMessage($to, $language = 'ar')
    {
        $messages = [
            'ar' => "🎉 أهلاً وسهلاً! \n\nنحن سعداء لوجودك معنا. يمكنك الآن التواصل معنا عبر واتساب للحصول على الدعم والمساعدة.\n\n📞 للمساعدة: اكتب 'مساعدة'\n💬 للتحدث مع المبيعات: اكتب 'مبيعات'\n🎫 لفتح تذكرة دعم: اكتب 'دعم'",
            'en' => "🎉 Welcome! \n\nWe're happy to have you with us. You can now communicate with us via WhatsApp for support and assistance.\n\n📞 For help: type 'help'\n💬 To chat with sales: type 'sales'\n🎫 To open support ticket: type 'support'"
        ];
        
        return $this->sendMessage($to, $messages[$language] ?? $messages['ar']);
    }
    
    /**
     * Test API connection
     */
    public function testConnection()
    {
        $url = $this->baseUrl . $this->apiVersion . '/' . $this->businessAccountId;
        
        $headers = [
            'Authorization: Bearer ' . $this->accessToken,
            'Content-Type: application/json'
        ];
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);
        
        if ($error) {
            return ['success' => false, 'error' => 'cURL Error: ' . $error];
        }
        
        if ($httpCode === 200) {
            return ['success' => true, 'data' => json_decode($response, true)];
        } else {
            return ['success' => false, 'error' => 'HTTP ' . $httpCode . ': ' . $response];
        }
    }
    
    /**
     * Test webhook connection
     */
    public function testWebhook()
    {
        global $CONFIG;
        $webhookUrl = 'https://enjaz-web.com/billing/modules/addons/whatsappcloud/webhook.php';
        
        // Try to make a test request to the webhook
        $testData = [
            'object' => 'whatsapp_business_account',
            'entry' => []
        ];
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $webhookUrl);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($testData));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'User-Agent: WHMCS-WhatsApp-Test'
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);
        
        if ($error) {
            return ['success' => false, 'error' => 'cURL Error: ' . $error];
        }
        
        if ($httpCode === 200) {
            return ['success' => true, 'message' => 'Webhook is accessible and responding'];
        } else {
            return ['success' => false, 'error' => 'HTTP ' . $httpCode . ': ' . $response];
        }
    }
    
    /**
     * Process incoming webhook
     */
    public function processWebhook($data)
    {
        if (!isset($data['entry']) || !is_array($data['entry'])) {
            return false;
        }
        
        foreach ($data['entry'] as $entry) {
            if (!isset($entry['changes'])) continue;
            
            foreach ($entry['changes'] as $change) {
                if ($change['field'] !== 'messages') continue;
                
                $value = $change['value'];
                
                // Process incoming messages
                if (isset($value['messages'])) {
                    foreach ($value['messages'] as $message) {
                        $this->processIncomingMessage($message, $value['contacts'][0] ?? []);
                    }
                }
                
                // Process message status updates
                if (isset($value['statuses'])) {
                    foreach ($value['statuses'] as $status) {
                        $this->processMessageStatus($status);
                    }
                }
            }
        }
        
        return true;
    }
    
    /**
     * Process incoming message
     */
    private function processIncomingMessage($message, $contact)
    {
        $from = $message['from'];
        $messageId = $message['id'];
        $timestamp = $message['timestamp'];
        
        // Store message in database
        $this->storeMessage($from, $messageId, 'inbound', $message);
        
        // Get or create conversation
        $conversation = $this->getOrCreateConversation($from, $contact);
        
        // Process bot responses if enabled
        if ($this->isBotEnabled()) {
            $this->processBotResponse($message, $conversation);
        }
    }
    
    /**
     * Process bot response
     */
    private function processBotResponse($message, $conversation)
    {
        $messageType = $message['type'];
        $from = $message['from'];
        
        switch ($messageType) {
            case 'text':
                $text = strtolower(trim($message['text']['body']));
                
                if ($conversation['status'] === 'pending') {
                    // Send approval request
                    $this->sendApprovalRequest($from);
                } else {
                    $this->handleTextCommand($text, $from, $conversation);
                }
                break;
                
            case 'interactive':
                $this->handleInteractiveResponse($message['interactive'], $from, $conversation);
                break;
        }
    }
    
    /**
     * Handle text commands
     */
    private function handleTextCommand($text, $from, $conversation)
    {
        $language = $conversation['language'];
        
        $commands = [
            'help' => 'help',
            'مساعدة' => 'help',
            'sales' => 'sales',
            'مبيعات' => 'sales',
            'support' => 'support',
            'دعم' => 'support'
        ];
        
        $command = $commands[$text] ?? null;
        
        switch ($command) {
            case 'help':
                $helpMessage = $language === 'ar' ? 
                    "📋 قائمة الأوامر المتاحة:\n\n🆘 مساعدة - عرض هذه القائمة\n💼 مبيعات - التحدث مع فريق المبيعات\n🎫 دعم - فتح تذكرة دعم فني" :
                    "📋 Available Commands:\n\n🆘 help - Show this menu\n💼 sales - Chat with sales team\n🎫 support - Open support ticket";
                $this->sendMessage($from, $helpMessage);
                break;
                
            case 'sales':
                $salesMessage = $language === 'ar' ?
                    "💼 مرحباً بك في قسم المبيعات!\n\nسيتم توصيلك مع أحد ممثلي المبيعات قريباً. يرجى وصف استفسارك وسنعود إليك في أقرب وقت." :
                    "💼 Welcome to Sales!\n\nYou'll be connected with a sales representative soon. Please describe your inquiry and we'll get back to you shortly.";
                $this->sendMessage($from, $salesMessage);
                break;
                
            case 'support':
                $supportMessage = $language === 'ar' ?
                    "🎫 مرحباً بك في الدعم الفني!\n\nيرجى وصف المشكلة التي تواجهها بالتفصيل وسيتم فتح تذكرة دعم لك." :
                    "🎫 Welcome to Technical Support!\n\nPlease describe the issue you're facing in detail and a support ticket will be created for you.";
                $this->sendMessage($from, $supportMessage);
                break;
                
            default:
                $defaultMessage = $language === 'ar' ?
                    "شكراً لرسالتك! تم استلامها وسيتم الرد عليك قريباً.\n\nللمساعدة اكتب: مساعدة" :
                    "Thank you for your message! We have received it and will reply soon.\n\nFor help type: help";
                $this->sendMessage($from, $defaultMessage);
        }
    }
    
    /**
     * Handle interactive responses
     */
    private function handleInteractiveResponse($interactive, $from, $conversation)
    {
        $buttonReply = $interactive['button_reply'] ?? null;
        
        if (!$buttonReply) return;
        
        $buttonId = $buttonReply['id'];
        
        switch ($buttonId) {
            case 'approve_yes':
                // Update conversation status
                $this->updateConversationStatus($from, 'active');
                // Send language selection
                $this->sendLanguageSelection($from);
                break;
                
            case 'approve_no':
                $this->updateConversationStatus($from, 'completed');
                $declineMessage = "نتفهم قرارك. يمكنك التواصل معنا في أي وقت.\nWe understand your decision. You can contact us anytime.";
                $this->sendMessage($from, $declineMessage);
                break;
                
            case 'lang_ar':
                $this->updateConversationLanguage($from, 'ar');
                $this->sendWelcomeMessage($from, 'ar');
                break;
                
            case 'lang_en':
                $this->updateConversationLanguage($from, 'en');
                $this->sendWelcomeMessage($from, 'en');
                break;
        }
    }
    
    /**
     * Store message in database
     */
    private function storeMessage($phoneNumber, $messageId, $direction, $messageData)
    {
        $conversationId = $this->getConversationId($phoneNumber);
        
        if (!$conversationId) return false;
        
        $content = '';
        $messageType = $messageData['type'] ?? 'text';
        
        switch ($messageType) {
            case 'text':
                $content = $messageData['text']['body'] ?? '';
                break;
            case 'interactive':
                $content = json_encode($messageData['interactive']);
                break;
            default:
                $content = json_encode($messageData);
        }
        
        $query = "INSERT INTO mod_whatsappcloud_messages 
                  (conversation_id, message_id, direction, message_type, content) 
                  VALUES (?, ?, ?, ?, ?)";
        
        $stmt = mysqli_prepare($GLOBALS['dbh'], $query);
        mysqli_stmt_bind_param($stmt, 'issss', $conversationId, $messageId, $direction, $messageType, $content);
        
        return mysqli_stmt_execute($stmt);
    }
    
    /**
     * Get or create conversation
     */
    private function getOrCreateConversation($phoneNumber, $contact)
    {
        $query = "SELECT * FROM mod_whatsappcloud_conversations WHERE phone_number = ?";
        $stmt = mysqli_prepare($GLOBALS['dbh'], $query);
        mysqli_stmt_bind_param($stmt, 's', $phoneNumber);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if ($conversation = mysqli_fetch_assoc($result)) {
            return $conversation;
        }
        
        // Create new conversation
        $contactName = $contact['profile']['name'] ?? $phoneNumber;
        $query = "INSERT INTO mod_whatsappcloud_conversations (phone_number, contact_name) VALUES (?, ?)";
        $stmt = mysqli_prepare($GLOBALS['dbh'], $query);
        mysqli_stmt_bind_param($stmt, 'ss', $phoneNumber, $contactName);
        
        if (mysqli_stmt_execute($stmt)) {
            $conversationId = mysqli_insert_id($GLOBALS['dbh']);
            return [
                'id' => $conversationId,
                'phone_number' => $phoneNumber,
                'contact_name' => $contactName,
                'language' => 'ar',
                'status' => 'pending'
            ];
        }
        
        return null;
    }
    
    /**
     * Update conversation status
     */
    private function updateConversationStatus($phoneNumber, $status)
    {
        $query = "UPDATE mod_whatsappcloud_conversations SET status = ? WHERE phone_number = ?";
        $stmt = mysqli_prepare($GLOBALS['dbh'], $query);
        mysqli_stmt_bind_param($stmt, 'ss', $status, $phoneNumber);
        
        return mysqli_stmt_execute($stmt);
    }
    
    /**
     * Update conversation language
     */
    private function updateConversationLanguage($phoneNumber, $language)
    {
        $query = "UPDATE mod_whatsappcloud_conversations SET language = ? WHERE phone_number = ?";
        $stmt = mysqli_prepare($GLOBALS['dbh'], $query);
        mysqli_stmt_bind_param($stmt, 'ss', $language, $phoneNumber);
        
        return mysqli_stmt_execute($stmt);
    }
    
    /**
     * Get conversation ID
     */
    private function getConversationId($phoneNumber)
    {
        $query = "SELECT id FROM mod_whatsappcloud_conversations WHERE phone_number = ?";
        $stmt = mysqli_prepare($GLOBALS['dbh'], $query);
        mysqli_stmt_bind_param($stmt, 's', $phoneNumber);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if ($row = mysqli_fetch_assoc($result)) {
            return $row['id'];
        }
        
        return null;
    }
    
    /**
     * Check if bot is enabled
     */
    private function isBotEnabled()
    {
        $query = "SELECT value FROM tbladdonmodules WHERE module = 'whatsappcloud' AND setting = 'enable_bot'";
        $result = full_query($query);
        
        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            return $row['value'] === 'on';
        }
        
        return false;
    }
    
    /**
     * Process message status update
     */
    private function processMessageStatus($status)
    {
        $messageId = $status['id'];
        $statusValue = $status['status'];
        
        $query = "UPDATE mod_whatsappcloud_messages SET status = ? WHERE message_id = ?";
        $stmt = mysqli_prepare($GLOBALS['dbh'], $query);
        mysqli_stmt_bind_param($stmt, 'ss', $statusValue, $messageId);
        
        return mysqli_stmt_execute($stmt);
    }
    
    /**
     * Format phone number
     */
    private function formatPhoneNumber($phoneNumber)
    {
        // Remove any non-numeric characters except +
        $phoneNumber = preg_replace('/[^0-9+]/', '', $phoneNumber);
        
        // Add + if not present
        if (!str_starts_with($phoneNumber, '+')) {
            $phoneNumber = '+' . $phoneNumber;
        }
        
        return $phoneNumber;
    }
    
    /**
     * Make HTTP request to WhatsApp API
     */
    private function makeRequest($url, $data)
    {
        $headers = [
            'Authorization: Bearer ' . $this->accessToken,
            'Content-Type: application/json'
        ];
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);
        
        if ($error) {
            return ['success' => false, 'error' => 'cURL Error: ' . $error];
        }
        
        $responseData = json_decode($response, true);
        
        if ($httpCode === 200 || $httpCode === 201) {
            return ['success' => true, 'data' => $responseData];
        } else {
            $errorMessage = $responseData['error']['message'] ?? 'Unknown error';
            return ['success' => false, 'error' => 'HTTP ' . $httpCode . ': ' . $errorMessage];
        }
    }
}