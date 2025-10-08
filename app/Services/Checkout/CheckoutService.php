<?php
namespace App\Services\Checkout;

use Xendit\Configuration;
use Xendit\Invoice\InvoiceApi;
use Xendit\Invoice\CreateInvoiceRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use DateTime;

class CheckoutService
{
    protected InvoiceApi $invoiceApi;

    public function __construct()
    {
        // Use config('services.xendit.secret') for better environment management
        Configuration::setXenditKey(env('API_KEY'));
        $this->invoiceApi = new InvoiceApi();
    }

    /**
     * Create a Xendit invoice dynamically.
     *
     * @param array $data  Request data
     * @param string|null $forUserId  (Optional) Xendit "for-user-id" if using subaccounts
     */
    public function createInvoice(array $data, ?string $forUserId = null): array
    {
        $timestamp = now()->timestamp;

        // Default parameters
        $defaultParams = [
            'external_id' => 'lar12-checkout-' . $timestamp,
            'payer_email' => $data['payer_email'] ?? 'demo@xendit.co',
            'description' => $data['description'] ?? 'Laravel 12 Checkout Invoice',
            'amount' => $data['amount'] ?? 10000,
            'invoice_duration' => $data['invoice_duration'] ?? 172800, // 2 days
            'currency' => $data['currency'] ?? 'PHP',
            'reminder_time' => $data['reminder_time'] ?? 1,
            'success_redirect_url' => $data['redirect_url'] ?? null,
            'failure_redirect_url' => $data['redirect_url'] ?? null,
        ];

        // Prepare request object
        $request = new CreateInvoiceRequest($defaultParams);

        try {
            $result = $this->invoiceApi->createInvoice($request, $forUserId);

            Log::info('[Xendit] Invoice created successfully', [
                'external_id' => $defaultParams['external_id'],
                'invoice_id' => $result->getId() ?? null,
            ]);

            return [
                'success' => true,
                'message' => 'Invoice created successfully.',
                'data' => $result,
            ];
        } catch (\Throwable $e) {
            Log::error('[Xendit] Failed to create invoice', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return [
                'success' => false,
                'message' => 'Failed to create invoice: ' . $e->getMessage(),
                'data' => null,
            ];
        }
    }
}
