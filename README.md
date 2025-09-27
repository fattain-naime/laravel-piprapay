# Laravel PipraPay Integration

A simple Laravel package for integrating the PipraPay payment gateway.

## Installation

You can install the package via composer:

```bash
composer require fattain-naime/laravel-piprapay
```

Next, publish the configuration file:

```bash
php artisan vendor:publish --provider="FattainNaime\PipraPay\PipraPayServiceProvider" --tag="config"
```

Finally, add your PipraPay credentials to your `.env` file:

```dotenv# Set to false to go live
PIPRAPAY_SANDBOX_MODE=false 

# The user's self-hosted PipraPay domain
PIPRAPAY_BASE_URL="https://pay.their-own-domain.com" 

PIPRAPAY_API_KEY="their-production-api-key"
```

## Usage

### 1. Creating a Payment and Redirecting

In your controller, you can create a charge and redirect the user to the payment page. The package simplifies this by providing a `getPaymentUrl` method.

```php
use FattainNaime\PipraPay\Facades\PipraPay;

public function createPayment()
{
    $chargeData = [
        'full_name' => 'John Doe',
        'email_mobile' => 'john.doe@example.com',
        'amount' => '100.00',
        'currency' => 'BDT',
        'redirect_url' => route('payment.success'),
        'cancel_url' => route('payment.cancel'),
        'webhook_url' => route('piprapay.webhook'), // The package handles this route
        'return_type' => 'POST',
        'metadata' => [
            'order_id' => 'ORD-12345',
        ],
    ];

    try {
        $paymentUrl = PipraPay::getPaymentUrl($chargeData);
        return redirect()->away($paymentUrl);
    } catch (\FattainNaime\PipraPay\Exceptions\PipraPayApiException $e) {
        // Handle API errors
        return back()->with('error', $e->getMessage());
    }
}
```

### 2. Verifying a Payment (Fallback)

After the user is redirected back to your `redirect_url`, you should verify the payment as a fallback in case the webhook is delayed.

```php
use Illuminate\Http\Request;
use FattainNaime\PipraPay\Facades\PipraPay;

public function paymentSuccess(Request $request)
{
    $pp_id = $request->input('pp_id'); 

    try {
        $paymentDetails = PipraPay::verifyPayment($pp_id);

        if ($paymentDetails['status'] === 'completed') {
            // Payment is successful. Update your order.
        } else {
            // Handle other statuses
        }

        return view('payment-success');
    } catch (\FattainNaime\PipraPay\Exceptions\PipraPayApiException $e) {
        // Handle verification failure
        return redirect()->route('payment.cancel')->with('error', 'Payment verification failed.');
    }
}
```

### 3. Handling Webhooks (Recommended)

Webhooks are the most reliable way to get payment status updates. This package fires an event when a payment is completed. You just need to create a listener for it.

First, create the listener:
```bash
php artisan make:listener ProcessPipraPayPayment
```

Then, register the event and listener in your `app/Providers/EventServiceProvider.php`:

```php
protected $listen = [
    \FattainNaime\PipraPay\Events\PipraPayPaymentCompleted::class => [
        \App\Listeners\ProcessPipraPayPayment::class,
    ],
];
```

Finally, add your logic to the listener's `handle` method:

```php
// app/Listeners/ProcessPipraPayPayment.php

namespace App\Listeners;

use App\Models\Order;
use FattainNaime\PipraPay\Events\PipraPayPaymentCompleted;

class ProcessPipraPayPayment
{
    public function handle(PipraPayPaymentCompleted $event): void
    {
        $payload = $event->payload;
        
        $order = Order::where('order_id', $payload['metadata']['order_id'])->first();

        if ($order && $order->status !== 'paid') {
            $order->status = 'paid';
            $order->transaction_id = $payload['pp_id'];
            $order->save();
        }
    }
}
```

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
