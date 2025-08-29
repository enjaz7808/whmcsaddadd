<?php
/**
 * Arabic Language File for WhatsApp Cloud Addon
 */

$_ADDON['whatsappcloud'] = [
    // General
    'title' => 'واتساب كلاود',
    'description' => 'ربط واتساب كلاود مع WHMCS',
    
    // Configuration
    'app_id' => 'معرف التطبيق',
    'app_secret' => 'كلمة مرور التطبيق',
    'phone_number_id' => 'معرف رقم الهاتف',
    'business_account_id' => 'معرف حساب الأعمال',
    'access_token' => 'رمز الوصول',
    'webhook_verify_token' => 'رمز التحقق من الويب هوك',
    'enable_bot' => 'تفعيل البوت التفاعلي',
    'default_language' => 'اللغة الافتراضية',
    
    // Status
    'connected' => 'متصل',
    'disconnected' => 'غير متصل',
    'active' => 'نشط',
    'pending' => 'معلق',
    'completed' => 'مكتمل',
    
    // Messages
    'welcome_message_ar' => 'أهلاً وسهلاً! نحن سعداء لانضمامك إلينا. يمكنك الآن التواصل معنا عبر واتساب للحصول على الدعم والمساعدة.',
    'welcome_message_en' => 'Welcome! We\'re happy to have you with us. You can now communicate with us via WhatsApp for support and assistance.',
    
    'approval_request' => 'هل توافق على تلقي الرسائل من نظامنا؟\nDo you agree to receive messages from our system?',
    'language_selection' => 'Please select your preferred language / الرجاء اختيار لغتك المفضلة',
    
    'help_message_ar' => 'كيف يمكننا مساعدتك؟\n\n🛍️ للمبيعات: اكتب \'مبيعات\'\n🎫 للدعم الفني: اكتب \'دعم\'\n📋 لعرض الفواتير: اكتب \'فواتير\'',
    'help_message_en' => 'How can we help you?\n\n🛍️ For sales: type \'sales\'\n🎫 For support: type \'support\'\n📋 For invoices: type \'invoices\'',
    
    'sales_message_ar' => 'مرحباً! أنا هنا لمساعدتك في الاستفسارات التجارية. كيف يمكنني مساعدتك اليوم؟',
    'sales_message_en' => 'Hello! I\'m here to help with sales inquiries. How can I assist you today?',
    
    'support_message_ar' => 'سيتم توصيلك بفريق الدعم الفني. يرجى وصف مشكلتك وسنتواصل معك قريباً.',
    'support_message_en' => 'You\'ll be connected to our technical support team. Please describe your issue and we\'ll get back to you soon.',
    
    'default_message_ar' => 'شكراً لرسالتك. سيتم الرد عليك في أقرب وقت ممكن.',
    'default_message_en' => 'Thank you for your message. We\'ll get back to you as soon as possible.',
    
    'decline_message' => 'نتفهم قرارك. يمكنك التواصل معنا في أي وقت.\nWe understand your decision. You can contact us anytime.',
    
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
        'details' => 'التفاصيل',
        'webhook' => 'ربط الويب هوك',
        'bot' => 'البوت التفاعلي',
        'chat' => 'الدردشة'
    ],
    
    // Success/Error Messages
    'message_sent_success' => 'تم إرسال الرسالة بنجاح!',
    'message_sent_error' => 'فشل في إرسال الرسالة',
    'webhook_test_success' => 'تم اختبار الويب هوك بنجاح!',
    'webhook_test_error' => 'فشل اختبار الويب هوك',
    'settings_saved' => 'تم حفظ الإعدادات بنجاح!',
    'connection_failed' => 'فشل الاتصال بواتساب كلاود',
    
    // Instructions
    'webhook_instructions' => [
        'step1' => 'اذهب إلى فيسبوك المطورين وسجل الدخول',
        'step2' => 'اختر تطبيقك أو أنشئ تطبيق جديد',
        'step3' => 'انتقل إلى WhatsApp > Configuration',
        'step4' => 'في قسم Webhooks، أدخل البيانات المطلوبة',
        'step5' => 'اشترك في الحقول المطلوبة',
        'step6' => 'انقر على Verify and Save'
    ]
];
?>