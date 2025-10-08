<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Checkout\CheckoutService;
use Illuminate\Http\Request;

class PaymentApiController extends Controller
{
    protected CheckoutService $checkoutService;

    public function __construct(CheckoutService $checkoutService)
    {
        $this->checkoutService = $checkoutService;
    }

    /**
     * Handle invoice creation request.
     */
    public function createInvoice(Request $request)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:1',
            'description' => 'nullable|string|max:255',
            'redirect_url' => 'nullable|url',
            'payer_email' => 'required|email'
        ]);

        $response = $this->checkoutService->createInvoice($validated);

        if ($response['success']) {
            return response()->json([
                'status' => 'success',
                'message' => $response['message'],
                'invoice' => $response['data'],
            ]);
        }

        return response()->json([
            'status' => 'error',
            'message' => $response['message'],
        ], 500);
    }

}
