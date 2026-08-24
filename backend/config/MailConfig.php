<?php
// backend/config/MailConfig.php
namespace App\Config;

class MailConfig {
    /**
     * Get Mail & SMTP Configuration
     * 
     * To activate real-world email delivery (e.g. via Gmail, Outlook, Mailtrap, or cPanel):
     * 1. Set 'enabled' => true
     * 2. Set 'username' => 'your_email@gmail.com'
     * 3. Set 'password' => 'your_16_char_gmail_app_password' (Use Google App Password for 2FA)
     */
    public static function get(): array {
        return [
            // Switch to true to send live emails over SMTP, false to operate in safe preview/logging mode
            'enabled' => false,
            
            // SMTP Server Connection
            'host'       => 'smtp.gmail.com',  // smtp.gmail.com, smtp.office365.com, mailtrap.io, or cPanel host
            'port'       => 587,               // 587 for TLS, 465 for SSL, 2525 for Mailtrap
            'encryption' => 'tls',             // 'tls' or 'ssl'
            'auth'       => true,
            
            // SMTP Credentials
            'username'   => 'admissions.jjkings@gmail.com',
            'password'   => '',                // Put your Gmail App Password or SMTP password here
            
            // Institutional Sender Information
            'from_email' => 'admissions@jjkingsbiringan.edu.ph',
            'from_name'  => 'JJKINGS Biringan School Admissions',
            'reply_to'   => 'admissions@jjkingsbiringan.edu.ph',
            
            // SMTP Debug level (0 = off, 1 = client commands, 2 = client + server)
            'debug'      => 0
        ];
    }
}
