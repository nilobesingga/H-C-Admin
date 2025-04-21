<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InvoiceEmailNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected array $data;
    protected string $subject;
    protected array $recipientEmails;
    protected ?string $pdfPath;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        array $data,
        string $subject,
        array $recipientEmails = [],
        ?string $pdfPath = null
    ) {
        $this->data = $data;
        $this->subject = $subject;
        $this->recipientEmails = $recipientEmails;
        $this->pdfPath = $pdfPath;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $mail = (new MailMessage)
                    ->subject($this->subject)
                    ->greeting('Hello!')
                    ->line('Please find attached invoice details.');

        // Add any custom lines from data
        if (isset($this->data['message'])) {
            $mail->line($this->data['message']);
        }

        // Add invoice details if provided
        if (isset($this->data['invoice_number'])) {
            $mail->line('Invoice Number: ' . $this->data['invoice_number']);
        }

        if (isset($this->data['invoice_date'])) {
            $mail->line('Invoice Date: ' . $this->data['invoice_date']);
        }

        if (isset($this->data['amount'])) {
            $mail->line('Amount: ' . $this->data['amount']);
        }

        // Add a default closing line
        $mail->line('Thank you for your business!');

        // Attach PDF if available
        if ($this->pdfPath && file_exists($this->pdfPath)) {
            $mail->attach($this->pdfPath, [
                'as' => basename($this->pdfPath),
                'mime' => 'application/pdf',
            ]);
        }

        return $mail;
    }

    /**
     * Get recipient email addresses.
     */
    public function getRecipientEmails(): array
    {
        return $this->recipientEmails;
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return $this->data;
    }
}
