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
     * Send a template message
     */
    public function sendTemplate($to, $templateName, $languageCode = 'ar', $parameters = [])
    {
        $url = $this->baseUrl . $this->apiVersion . '/' . $this->phoneNumberId . '/messages';
        
        $data = [
            'messaging_product' => 'whatsapp',
            'recipient_type' => 'individual',
            'to' => $this->formatPhoneNumber($to),
            'type' => 'template',
            'template' => [
                'name' => $templateName,
                'language' => [
                    'code' => $languageCode
                ]
            ]
        ];
        
        if (!empty($parameters)) {
            $data['template']['components'] = [
                [
                    'type' => 'body',
                    'parameters' => $parameters
                ]
            ];
        }
        
        return $this->makeRequest($url, $data);
    }
    
    /**
     * Send interactive message with buttons
     */
    public function sendInteractiveMessage($to, $bodyText, $buttons)
    {
        $url = $this->baseUrl . $this->apiVersion . '/' . $this->phoneNumberId . '/messages';
        
        $interactiveButtons = [];
        foreach ($buttons as $index => $button) {
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
            'ar' => "🎉 أهلاً وسهلاً! \n\nنحن سعداء لانضمامك إلينا. يمكنك الآن التواصل معنا عبر واتساب للحصول على الدعم والمساعدة.\n\n📞 للمساعدة: اكتب 'مساعدة'\n💬 للدردشة مع المبيعات: اكتب 'مبيعات'\n🎫 لفتح تذكرة دعم: اكتب 'دعم'",
            'en' => "🎉 Welcome! \n\nWe're happy to have you with us. You can now communicate with us via WhatsApp for support and assistance.\n\n📞 For help: type 'help'\n💬 To chat with sales: type 'sales'\n🎫 To open support ticket: type 'support'"
        ];
        
        return $this->sendMessage($to, $messages[$language] ?? $messages['ar']);
    }
    
    /**
     * Get webhook verification challenge
     */
    public function verifyWebhook($verifyToken, $challenge, $mode)
    {
        global $CONFIG;
        $expectedToken = $CONFIG['whatsappcloud_webhook_verify_token'] ?? '';
        
        if ($mode === 'subscribe' && $verifyToken === $expectedToken) {
            return $challenge;
        }
        
        return false;
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
                    "كيف يمكننا مساعدتك؟\n\n🛍️ للمبيعات: اكتب 'مبيعات'\n🎫 للدعم الفني: اكتب 'دعم'\n📋 لعرض الفواتير: اكتب 'فواتير'" :
                    "How can we help you?\n\n🛍️ For sales: type 'sales'\n🎫 For support: type 'support'\n📋 For invoices: type 'invoices'";
                $this->sendMessage($from, $helpMessage);
                break;
                
            case 'sales':
                $salesMessage = $language === 'ar' ?
                    "مرحباً! أنا هنا لمساعدتك في الاستفسارات التجارية. كيف يمكنني مساعدتك اليوم؟" :
                    "Hello! I'm here to help with sales inquiries. How can I assist you today?";
                $this->sendMessage($from, $salesMessage);
                break;
                
            case 'support':
                $supportMessage = $language === 'ar' ?
                    "سيتم توصيلك بفريق الدعم الفني. يرجى وصف مشكلتك وسنتواصل معك قريباً." :
                    "You'll be connected to our technical support team. Please describe your issue and we'll get back to you soon.";
                $this->sendMessage($from, $supportMessage);
                break;
                
            default:
                $defaultMessage = $language === 'ar' ?
                    "شكراً لرسالتك. سيتم الرد عليك في أقرب وقت ممكن." :
                    "Thank you for your message. We'll get back to you as soon as possible.";
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
            'X-Hub-Signature-256: sha256=' . hash_hmac('sha256', json_encode($testData), $CONFIG['whatsappcloud_app_secret'] ?? '')
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        return [
            'success' => $httpCode === 200,
            'error' => $httpCode !== 200 ? "HTTP $httpCode" : null
        ];
    }
    
    /**
     * Helper methods
     */
    private function formatPhoneNumber($number)
    {
        // Remove any non-digit characters
        $number = preg_replace('/[^0-9]/', '', $number);
        
        // Add country code if not present (assuming Saudi Arabia +966)
        if (!str_starts_with($number, '966') && !str_starts_with($number, '+966')) {
            if (str_starts_with($number, '0')) {
                $number = '966' . substr($number, 1);
            } else {
                $number = '966' . $number;
            }
        }
        
        return $number;
    }
    
    private function makeRequest($url, $data)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $this->accessToken,
            'Content-Type: application/json'
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        $decodedResponse = json_decode($response, true);
        
        return [
            'success' => $httpCode === 200,
            'data' => $decodedResponse,
            'error' => $httpCode !== 200 ? ($decodedResponse['error']['message'] ?? "HTTP $httpCode") : null
        ];
    }
    
    private function storeMessage($phone, $messageId, $direction, $messageData)
    {
        $query = "INSERT INTO mod_whatsappcloud_messages 
                  (conversation_id, message_id, direction, message_type, content) 
                  VALUES (?, ?, ?, ?, ?)";
        
        $conversationId = $this->getConversationId($phone);
        $messageType = $messageData['type'];
        $content = json_encode($messageData);
        
        full_query($query, [$conversationId, $messageId, $direction, $messageType, $content]);
    }
    
    private function getOrCreateConversation($phone, $contact)
    {
        $query = "SELECT * FROM mod_whatsappcloud_conversations WHERE phone_number = ?";
        $result = full_query($query, [$phone]);
        
        if ($result->num_rows > 0) {
            return $result->fetch_assoc();
        }
        
        // Create new conversation
        $clientId = $this->findClientByPhone($phone);
        $conversationId = uniqid('conv_');
        
        $query = "INSERT INTO mod_whatsappcloud_conversations 
                  (client_id, phone_number, conversation_id, status) 
                  VALUES (?, ?, ?, 'pending')";
        
        full_query($query, [$clientId, $phone, $conversationId]);
        
        return [
            'id' => mysql_insert_id(),
            'client_id' => $clientId,
            'phone_number' => $phone,
            'conversation_id' => $conversationId,
            'status' => 'pending',
            'language' => 'ar'
        ];
    }
    
    private function findClientByPhone($phone)
    {
        // Try to find client by phone number in WHMCS
        $query = "SELECT id FROM tblclients WHERE phonenumber = ? OR phonenumber = ? LIMIT 1";
        $cleanPhone = preg_replace('/[^0-9]/', '', $phone);
        $result = full_query($query, [$phone, $cleanPhone]);
        
        return $result->num_rows > 0 ? $result->fetch_assoc()['id'] : 0;
    }
    
    private function getConversationId($phone)
    {
        $query = "SELECT id FROM mod_whatsappcloud_conversations WHERE phone_number = ?";
        $result = full_query($query, [$phone]);
        
        return $result->num_rows > 0 ? $result->fetch_assoc()['id'] : 0;
    }
    
    private function updateConversationStatus($phone, $status)
    {
        $query = "UPDATE mod_whatsappcloud_conversations SET status = ? WHERE phone_number = ?";
        full_query($query, [$status, $phone]);
    }
    
    private function updateConversationLanguage($phone, $language)
    {
        $query = "UPDATE mod_whatsappcloud_conversations SET language = ? WHERE phone_number = ?";
        full_query($query, [$language, $phone]);
    }
    
    private function isBotEnabled()
    {
        global $CONFIG;
        return ($CONFIG['whatsappcloud_enable_bot'] ?? 'yes') === 'yes';
    }
    
    private function processMessageStatus($status)
    {
        // Update message status in database
        $messageId = $status['id'];
        $newStatus = $status['status'];
        
        $query = "UPDATE mod_whatsappcloud_messages SET status = ? WHERE message_id = ?";
        full_query($query, [$newStatus, $messageId]);
    }
}