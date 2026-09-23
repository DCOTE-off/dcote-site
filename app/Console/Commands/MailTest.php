<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Throwable;

class MailTest extends Command
{
    protected $signature = 'mail:test {to? : Recipient address, defaults to BACKUP_MAIL_TO}';

    protected $description = 'Send a test email to verify the outgoing mail configuration';

    public function handle(): int
    {
        $mailer = config('mail.default');
        $host = config("mail.mailers.{$mailer}.host");
        $port = config("mail.mailers.{$mailer}.port");
        $from = config('mail.from.address');
        $to = $this->argument('to') ?: config('backup.notifications.mail.to');

        $this->line("Mailer: {$mailer}");
        $this->line('Host: '.($host ? "{$host}:{$port}" : '-'));
        $this->line("From: {$from}");
        $this->line("To: {$to}");

        try {
            Mail::raw(
                'Проверка почты DCOTE: '.now()->toDateTimeString(),
                fn ($message) => $message->to($to)->subject('DCOTE: проверка почты'),
            );
        } catch (Throwable $e) {
            $this->error('ОШИБКА: '.$e->getMessage());

            return self::FAILURE;
        }

        $this->info("Готово: тестовое письмо отправлено на {$to}");

        return self::SUCCESS;
    }
}
