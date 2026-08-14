<?php
/**
 * MailService — PHP 7.4 compatible
 *
 * Lightweight SMTP mailer using PHP socket streams.
 * No external dependencies — works with Gmail App Passwords on port 587 (STARTTLS).
 *
 * Usage:
 *   $mailer = MailService::fromConfig();
 *   $mailer->send('recipient@example.com', 'Subject', $htmlBody);
 */

declare(strict_types=1);

namespace App\Services;

class MailService
{
    private string $host;
    private int    $port;
    private string $user;
    private string $pass;
    private string $from;
    private string $fromName;

    public function __construct(array $config)
    {
        $this->host     = $config['host']      ?? 'smtp.gmail.com';
        $this->port     = (int)($config['port'] ?? 587);
        $this->user     = $config['user']      ?? '';
        $this->pass     = $config['pass']      ?? '';
        $this->from     = $config['from']      ?? $this->user;
        $this->fromName = $config['from_name'] ?? 'CamLingua';
    }

    /**
     * Create an instance from config.php.
     */
    public static function fromConfig(): self
    {
        $config = require ROOT_PATH . '/app/config/config.php';
        return new self($config['mail']);
    }

    /**
     * Send an HTML email.
     *
     * @param  string      $to       Recipient email
     * @param  string      $subject  Email subject
     * @param  string      $html     HTML body
     * @param  string|null $toName   Optional recipient display name
     * @throws \RuntimeException on SMTP failure
     */
    public function send(string $to, string $subject, string $html, ?string $toName = null): void
    {
        // Plain-text fallback by stripping HTML tags
        $text = wordwrap(strip_tags(preg_replace('/<br\s*\/?>/i', "\n", $html)), 76, "\n", true);

        $boundary = '----=_Part_' . md5(uniqid('', true));
        $toHeader = $toName ? ('"' . addslashes($toName) . '" <' . $to . '>') : $to;

        // Build MIME message
        $headers  = "From: \"{$this->fromName}\" <{$this->from}>\r\n";
        $headers .= "To: {$toHeader}\r\n";
        $headers .= "Subject: =?UTF-8?B?" . base64_encode($subject) . "?=\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: multipart/alternative; boundary=\"{$boundary}\"\r\n";
        $headers .= "X-Mailer: CamLingua/1.0\r\n";
        $headers .= "Date: " . date('r') . "\r\n";

        $body  = "--{$boundary}\r\n";
        $body .= "Content-Type: text/plain; charset=UTF-8\r\n";
        $body .= "Content-Transfer-Encoding: base64\r\n\r\n";
        $body .= chunk_split(base64_encode($text)) . "\r\n";

        $body .= "--{$boundary}\r\n";
        $body .= "Content-Type: text/html; charset=UTF-8\r\n";
        $body .= "Content-Transfer-Encoding: base64\r\n\r\n";
        $body .= chunk_split(base64_encode($html)) . "\r\n";

        $body .= "--{$boundary}--\r\n";

        $this->smtpSend($to, $headers . "\r\n" . $body);
    }

    // ── SMTP socket implementation ────────────────────────────────────────────

    private function smtpSend(string $to, string $rawMessage): void
    {
        $errno = 0; $errstr = '';
        $socket = fsockopen('tcp://' . $this->host, $this->port, $errno, $errstr, 15);
        if (!$socket) {
            throw new \RuntimeException("MailService: cannot connect to {$this->host}:{$this->port} — {$errstr}");
        }

        try {
            $this->expect($socket, 220, 'CONNECT');
            $this->cmd($socket, 'EHLO ' . gethostname(), 250);
            $this->cmd($socket, 'STARTTLS', 220);

            // Upgrade to TLS
            if (!stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT)) {
                throw new \RuntimeException('MailService: STARTTLS negotiation failed.');
            }

            $this->cmd($socket, 'EHLO ' . gethostname(), 250);
            $this->cmd($socket, 'AUTH LOGIN', 334);
            $this->cmd($socket, base64_encode($this->user), 334);
            $this->cmd($socket, base64_encode($this->pass), 235);
            $this->cmd($socket, 'MAIL FROM:<' . $this->from . '>', 250);
            $this->cmd($socket, 'RCPT TO:<' . $to . '>', 250);
            $this->cmd($socket, 'DATA', 354);

            // Send message body; escape leading dots
            fwrite($socket, str_replace("\n.", "\n..", $rawMessage) . "\r\n.\r\n");
            $this->expect($socket, 250, 'DATA body');

            $this->cmd($socket, 'QUIT', 221);
        } finally {
            fclose($socket);
        }
    }

    /**
     * Send an SMTP command and verify the expected reply code.
     */
    private function cmd($socket, string $cmd, int $expectedCode): string
    {
        fwrite($socket, $cmd . "\r\n");
        return $this->expect($socket, $expectedCode, $cmd);
    }

    /**
     * Read SMTP response and assert the code.
     */
    private function expect($socket, int $expectedCode, string $context): string
    {
        $response = '';
        while ($line = fgets($socket, 512)) {
            $response .= $line;
            // A line without a dash after the code is the last line of the response
            if (substr($line, 3, 1) !== '-') break;
        }
        $code = (int)substr($response, 0, 3);
        if ($code !== $expectedCode) {
            throw new \RuntimeException(
                "MailService SMTP error at [{$context}]: expected {$expectedCode}, got {$code} — {$response}"
            );
        }
        return $response;
    }
}
