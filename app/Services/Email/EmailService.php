<?php

namespace App\Services\Email;

use App\Notifications\InvoiceEmailNotification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use App\Traits\EmailCategoryTrait;
class EmailService
{
    use EmailCategoryTrait;
    /**
     * Send an invoice email with optional PDF attachment to specified recipients.
     *
     * @param array $data Invoice data including details like invoice_number, amount, etc.
     * @param string $subject Email subject line
     * @param array $recipientEmails Array of email addresses to send to
     * @param string|null $pdfPath Full path to PDF file to attach (optional)
     * @return bool Whether the email was sent successfully
     */
    public function sendInvoiceEmail(array $data, string $subject, array $recipientEmails, int $categoryId, ?string $pdfPath = null): bool
    {
        try {
            // Create the notification instance
            $emailConfig = $this->getEmailConfig($categoryId);
            $notification = new InvoiceEmailNotification(
                $data,
                $subject,
                $recipientEmails,
                $pdfPath,
                $emailConfig
            );
            // Send the notification to each recipient email
            Notification::route('mail', $recipientEmails)
                ->notify($notification);

            // Log successful sending
            Log::info('Invoice email sent successfully', [
                'recipients' => $recipientEmails,
                'subject' => $subject,
                'has_attachment' => !is_null($pdfPath)
            ]);

            return true;
        } catch (\Exception $e) {
            // Log any errors that occur
            Log::error('Failed to send invoice email', [
                'error' => $e->getMessage(),
                'recipients' => $recipientEmails,
                'subject' => $subject
            ]);

            return false;
        }
    }

    /**
     * Generate a PDF invoice and send it via email.
     * This method can be extended to generate PDFs dynamically if needed.
     *
     * @param array $invoiceData Invoice data for generating PDF
     * @param string $subject Email subject
     * @param array $recipientEmails Email recipients
     * @return bool Whether the email was sent successfully
     */
    public function generateAndSendInvoicePdf(array $invoiceData, string $subject, array $recipientEmails): bool
    {
        // Here you could implement PDF generation logic
        // For example, using a package like barryvdh/laravel-snappy to generate PDFs

        // Example implementation (commented out as it requires PDF generation logic):
        /*
        $pdf = PDF::loadView('pdf.invoice', ['invoice' => $invoiceData]);
        $pdfPath = storage_path('app/invoices/invoice_' . $invoiceData['invoice_number'] . '.pdf');
        $pdf->save($pdfPath);
        */

        // For demonstration, we'll assume the PDF already exists:
        $pdfPath = $invoiceData['pdf_path'] ?? null;

        return $this->sendInvoiceEmail(
            $invoiceData,
            $subject,
            $recipientEmails,
            $pdfPath
        );
    }
}
