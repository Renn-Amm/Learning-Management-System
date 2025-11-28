<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #000;
            color: #fff;
            padding: 20px;
            text-align: center;
        }
        .content {
            background-color: #f9f9f9;
            padding: 30px;
            border: 1px solid #ddd;
        }
        .message-box {
            background-color: #fff;
            padding: 20px;
            border-left: 4px solid #000;
            margin: 20px 0;
        }
        .button {
            display: inline-block;
            padding: 12px 30px;
            background-color: #000;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
        }
        .footer {
            text-align: center;
            padding: 20px;
            color: #666;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>EduHub LMS</h1>
            <p>New Message Received</p>
        </div>
        
        <div class="content">
            <h2>Hello {{ $recipient->name }},</h2>
            
            <p>You have received a new message from <strong>{{ $sender->name }}</strong>.</p>
            
            <div class="message-box">
                <p><strong>Message:</strong></p>
                <p>{{ $userMessage->message_text }}</p>
            </div>
            
            <p><strong>Sent:</strong> {{ $userMessage->created_at->format('F j, Y \a\t g:i A') }}</p>
            
            <a href="{{ route('messages.conversation', $sender) }}" class="button">
                View Conversation
            </a>
            
            <p style="margin-top: 20px; color: #666; font-size: 14px;">
                Click the button above to view and reply to this message.
            </p>
        </div>
        
        <div class="footer">
            <p>&copy; {{ date('Y') }} EduHub LMS. All rights reserved.</p>
            <p>This is an automated email notification. Please do not reply to this email.</p>
        </div>
    </div>
</body>
</html>
