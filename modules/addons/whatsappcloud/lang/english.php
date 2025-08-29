<?php
/**
 * English Language File for WhatsApp Cloud Addon
 */

$_ADDON['whatsappcloud'] = [
    // General
    'title' => 'WhatsApp Cloud',
    'description' => 'Integrate WhatsApp Cloud with WHMCS',
    
    // Configuration
    'app_id' => 'App ID',
    'app_secret' => 'App Secret',
    'phone_number_id' => 'Phone Number ID',
    'business_account_id' => 'Business Account ID',
    'access_token' => 'Access Token',
    'webhook_verify_token' => 'Webhook Verify Token',
    'enable_bot' => 'Enable Interactive Bot',
    'default_language' => 'Default Language',
    
    // Status
    'connected' => 'Connected',
    'disconnected' => 'Disconnected',
    'active' => 'Active',
    'pending' => 'Pending',
    'completed' => 'Completed',
    
    // Messages
    'welcome_message_ar' => 'أهلاً وسهلاً! نحن سعداء لانضمامك إلينا. يمكنك الآن التواصل معنا عبر واتساب للحصول على الدعم والمساعدة.',
    'welcome_message_en' => 'Welcome! We\'re happy to have you with us. You can now communicate with us via WhatsApp for support and assistance.',
    
    'approval_request' => 'Do you agree to receive messages from our system?\nهل توافق على تلقي الرسائل من نظامنا؟',
    'language_selection' => 'Please select your preferred language / الرجاء اختيار لغتك المفضلة',
    
    'help_message_ar' => 'كيف يمكننا مساعدتك؟\n\n🛍️ للمبيعات: اكتب \'مبيعات\'\n🎫 للدعم الفني: اكتب \'دعم\'\n📋 لعرض الفواتير: اكتب \'فواتير\'',
    'help_message_en' => 'How can we help you?\n\n🛍️ For sales: type \'sales\'\n🎫 For support: type \'support\'\n📋 For invoices: type \'invoices\'',
    
    'sales_message_ar' => 'مرحباً! أنا هنا لمساعدتك في الاستفسارات التجارية. كيف يمكنني مساعدتك اليوم؟',
    'sales_message_en' => 'Hello! I\'m here to help with sales inquiries. How can I assist you today?',
    
    'support_message_ar' => 'سيتم توصيلك بفريق الدعم الفني. يرجى وصف مشكلتك وسنتواصل معك قريباً.',
    'support_message_en' => 'You\'ll be connected to our technical support team. Please describe your issue and we\'ll get back to you soon.',
    
    'default_message_ar' => 'شكراً لرسالتك. سيتم الرد عليك في أقرب وقت ممكن.',
    'default_message_en' => 'Thank you for your message. We\'ll get back to you as soon as possible.',
    
    'decline_message' => 'We understand your decision. You can contact us anytime.\nنتفهم قرارك. يمكنك التواصل معنا في أي وقت.',
    
    // Bot Commands
    'commands' => [
        'help' => ['help', 'مساعدة'],
        'sales' => ['sales', 'مبيعات'],
        'support' => ['support', 'دعم'],
        'invoices' => ['invoices', 'فواتير'],
        'services' => ['services', 'خدمات'],
        'info' => ['info', 'معلومات']
    ],
    
    // Interface
    'tabs' => [
        'details' => 'Details',
        'webhook' => 'Webhook Setup',
        'bot' => 'Interactive Bot',
        'chat' => 'Chat'
    ],
    
    // Success/Error Messages
    'message_sent_success' => 'Message sent successfully!',
    'message_sent_error' => 'Failed to send message',
    'webhook_test_success' => 'Webhook test successful!',
    'webhook_test_error' => 'Webhook test failed',
    'settings_saved' => 'Settings saved successfully!',
    'connection_failed' => 'Failed to connect to WhatsApp Cloud',
    
    // Instructions
    'webhook_instructions' => [
        'step1' => 'Go to Facebook Developers and log in',
        'step2' => 'Select your app or create a new app',
        'step3' => 'Navigate to WhatsApp > Configuration',
        'step4' => 'In Webhooks section, enter the required data',
        'step5' => 'Subscribe to required fields',
        'step6' => 'Click Verify and Save'
    ]
];
?>