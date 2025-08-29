# WHMCS WhatsApp Cloud API Addon

A comprehensive WHMCS addon that integrates WhatsApp Cloud API for automated customer communication, interactive bot responses, and seamless message management.

## 🌟 Features

- **Complete WhatsApp Cloud Integration** - Connect your WHMCS with WhatsApp Business API
- **Interactive Bot** - Automated customer responses with language selection
- **Professional UI** - Clean, Arabic/English interface with elegant design
- **Real-time Messaging** - Send and receive messages directly from WHMCS
- **Webhook Management** - Easy webhook setup and verification
- **Conversation Tracking** - Full conversation history and analytics
- **Multi-language Support** - Arabic and English language support
- **Customer Approval Flow** - Professional opt-in process for customers

## 📋 Requirements

- WHMCS 7.0 or higher
- PHP 7.4 or higher
- MySQL 5.7 or higher
- WhatsApp Business Account
- Facebook Developer App with WhatsApp Business API access

## 🚀 Installation

1. **Upload Files**
   ```bash
   # Extract the addon to your WHMCS directory
   /path/to/whmcs/modules/addons/whatsappcloud/
   ```

2. **Activate Addon**
   - Go to WHMCS Admin → Setup → Addon Modules
   - Find "WhatsApp Cloud API" and click Configure
   - Configure your WhatsApp Cloud API credentials
   - Click "Activate"

3. **Configure Settings**
   - **App ID**: Your Facebook App ID (1106679971647524)
   - **App Secret**: Your Facebook App Secret
   - **Phone Number ID**: Your WhatsApp Business Phone Number ID (663817096825554)
   - **Business Account ID**: Your WhatsApp Business Account ID (1738496210124419)
   - **Access Token**: Your WhatsApp API Access Token
   - **Webhook Verify Token**: Auto-generated token for webhook verification

## ⚙️ Webhook Setup

1. **Facebook Developer Console**
   - Go to [Facebook Developers](https://developers.facebook.com/)
   - Select your app → WhatsApp → Configuration

2. **Webhook Configuration**
   - **Callback URL**: `https://enjaz-web.com/billing/modules/addons/whatsappcloud/webhook.php`
   - **Verify Token**: Copy from the addon's webhook tab
   - Subscribe to these fields:
     - ✅ messages
     - ✅ message_echoes  
     - ✅ messaging_handovers
     - ✅ message_template_status_update

3. **Test Connection**
   - Use the "Test Webhook" button in the addon
   - Verify successful connection

## 🤖 Bot Flow

### Customer Interaction Flow:
1. **Initial Message** → Customer sends first message
2. **Approval Request** → System asks for communication consent
3. **Language Selection** → Customer chooses Arabic or English
4. **Welcome Message** → Personalized welcome in selected language
5. **Interactive Commands** → Bot responds to customer commands

### Available Commands:
- **Arabic**: مساعدة، مبيعات، دعم، فواتير
- **English**: help, sales, support, invoices

## 📊 Interface Tabs

### 1. Details (التفاصيل)
- Connection status and health check
- Configuration overview
- Real-time statistics
- Quick actions

### 2. Webhook Setup (ربط الويب هوك)
- Webhook URL and verify token
- Setup instructions
- Connection testing
- Field subscription guide

### 3. Interactive Bot (البوت التفاعلي)
- Bot configuration and settings
- Response templates management
- Flow preview and analytics
- Language preferences

### 4. Chat (الدردشة)
- Live conversation management
- Quick message sending
- Message templates
- Conversation history

## 🛠️ Technical Details

### Database Tables
- `mod_whatsappcloud_conversations` - Stores conversation data
- `mod_whatsappcloud_messages` - Stores individual messages

### API Integration
- WhatsApp Cloud API v18.0
- Secure webhook verification
- Message status tracking
- Interactive button support

### Security Features
- Webhook signature verification
- Token-based authentication
- SQL injection protection
- XSS prevention

## 🎨 Styling

The addon features a modern, responsive design with:
- WhatsApp green color scheme (#25d366)
- Arabic RTL support
- Mobile-friendly interface
- Bootstrap-compatible styling

## 📱 Message Types Supported

- ✅ Text messages
- ✅ Interactive button messages
- ✅ Template messages
- ✅ Message reactions
- ✅ Message status updates

## 🔧 Configuration Options

- **Enable/Disable Bot** - Toggle automated responses
- **Default Language** - Set Arabic or English as default
- **Response Delay** - Configure bot response timing
- **Working Hours** - Set 24/7 or business hours only
- **Auto Retry** - Enable automatic message retry

## 📈 Analytics & Reporting

- Total conversations count
- Active vs pending conversations
- Message volume statistics
- Bot interaction metrics
- Language preference analytics

## 🌐 Multi-language Support

### Arabic (العربية)
- Full RTL interface support
- Arabic message templates
- Localized bot responses
- Arabic help documentation

### English
- Complete English interface
- Professional message templates
- English bot responses
- English help documentation

## 🔐 Security & Compliance

- Webhook signature verification using HMAC-SHA256
- Secure token storage
- Customer data protection
- GDPR-compliant opt-in process

## 📞 Support Commands

### Customer Self-Service:
- **Help/مساعدة** - Show available commands
- **Sales/مبيعات** - Connect to sales team
- **Support/دعم** - Create support ticket
- **Invoices/فواتير** - View account invoices

## 🚀 Performance Features

- Asynchronous message processing
- Database query optimization
- Caching for frequently accessed data
- Efficient webhook handling

## 🔄 Auto-Updates

- Real-time conversation status
- Live message delivery status
- Automatic bot response triggers
- Dynamic interface updates

## 📋 Installation Checklist

- [ ] Upload addon files to `/modules/addons/whatsappcloud/`
- [ ] Activate addon in WHMCS admin
- [ ] Configure WhatsApp API credentials
- [ ] Set up webhook in Facebook Developer Console
- [ ] Test webhook connection
- [ ] Configure bot settings
- [ ] Test message sending/receiving
- [ ] Verify customer flow

## 🎯 Use Cases

- **Customer Support** - Automated first-level support
- **Sales Inquiries** - Direct sales team connection
- **Service Notifications** - Automated service updates
- **Invoice Reminders** - Payment due notifications
- **Welcome Messages** - New customer onboarding

## 📊 Business Benefits

- **Improved Response Time** - Instant automated responses
- **24/7 Availability** - Round-the-clock customer service
- **Cost Reduction** - Reduced manual support workload
- **Better Engagement** - WhatsApp's high open rates
- **Professional Image** - Branded, consistent communication

## 🔧 Troubleshooting

### Common Issues:
1. **Webhook Not Receiving** - Check URL and verify token
2. **Messages Not Sending** - Verify access token and phone number ID
3. **Bot Not Responding** - Check bot enable status and configuration
4. **Database Errors** - Verify table creation and permissions

### Debug Mode:
Enable WHMCS debug mode to see detailed webhook logs and API responses.

## 📝 Changelog

### Version 1.0.0
- ✅ Initial release
- ✅ Complete WhatsApp Cloud integration
- ✅ Interactive bot with language selection
- ✅ Professional admin interface
- ✅ Webhook management
- ✅ Multi-language support
- ✅ Real-time messaging
- ✅ Analytics and reporting

## 🤝 Contributing

This addon is designed for professional WhatsApp Business integration with WHMCS. For customizations or enhancements, ensure compatibility with WHMCS standards and WhatsApp API requirements.

## 📄 License

MIT License - See LICENSE file for details.

---

**Developed by**: Enjaz Web  
**Version**: 1.0.0  
**Compatibility**: WHMCS 7.0+  
**Support**: WhatsApp Cloud API v18.0+