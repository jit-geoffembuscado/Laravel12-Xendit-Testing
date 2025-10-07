<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Services\Checkout\CheckoutService as Service;

class CheckoutApiController extends BaseController
{
    //
    public function create(Request $request){
        $service = new Service();

        return $this->sendResponse($service->createInvoice($request->all()), 'Create Invoice');
    }
}
