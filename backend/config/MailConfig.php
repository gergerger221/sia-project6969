<?php
// backend/config/MailConfig.php
namespace App\Config;

class MailConfig {
    /**
     * Get Mail & SMTP Configuration
     * Configured for Gmail SMTP with Google App Password
     */
    public static function get(): array {
        return [
            // Switch to true to send live emails over SMTP
            'enabled'    => true,
            
            // SMTP Server Connection
            'host'       => 'smtp.gmail.com',
            'port'       => 587,
            'encryption' => 'tls',
            'auth'       => true,
            
            // SMTP Credentials
            'username'   => 'ver.smtp221@gmail.com',
            'password'   => 'lwzonwviegzghidw',
            
            // Institutional Sender Information
            'from_email' => 'ver.smtp221@gmail.com',
            'from_name'  => 'JJKINGS Biringan School Admissions',
            'reply_to'   => 'ver.smtp221@gmail.com',
            
            // SMTP Debug level (0 = off, 1 = client commands, 2 = client + server)
            'debug'      => 0
        ];
    }
}
