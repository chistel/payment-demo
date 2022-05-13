<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Payment demo</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com?plugins=forms,typography"></script>
    <style>
        body {
            font-family: 'Nunito', sans-serif;
        }
    </style>
</head>
<body class="antialiased">
<div class="max-w-3xl w-full mx-auto px-6 py-12">
    <form action="{{ route('billing.process') }}" method="post">
        @csrf
        <label class="block">
            <span class="text-gray-700">Amount</span>
            <input class="form-input mt-1 block w-full rounded-lg" placeholder="Amount" name="amount">
        </label>
        <label class="block">
            <span class="text-gray-700">Email address</span>
            <input class="form-input mt-1 block w-full rounded-lg" placeholder="Email address" name="email">
        </label>
        @if ($supportedPayments = supportedPaymentGateways())
            <div class="mt-4">
                <span class="text-gray-700">Payment methods</span>
                <div class="mt-2">
                    @foreach($supportedPayments as $supportedPaymentKey => $supportedPayment)
                        <div class="inline-flex items-center px-3">
                            <input type="radio" name="gateway" value="{{ $supportedPaymentKey }}"
                                   id="gateway_{{ $supportedPaymentKey }}"
                                   class="appearance-none rounded-full h-4 w-4 border border-gray-300 bg-white checked:bg-blue-600 checked:border-blue-600 focus:outline-none transition duration-200 mt-1 align-top bg-no-repeat bg-center bg-contain float-left mr-2 cursor-pointer">
                            <label class="inline-block text-gray-800"
                                   for="gateway_{{ $supportedPaymentKey }}">
                                {{ $supportedPayment['name'] }}
                            </label>
                        </div>
                    @endforeach
                </div>
            </div>

        @endif
        <div>
            <button type="submit"
                    class="inline-flex items-center text-center px-6 py-2 border border-blue-400 text-blue-400 text-sm font-medium rounded-full shadow-sm hover:text-white hover:bg-blue-400 focus:outline-none focus:ring-0 focus:ring-offset-2 focus:ring-base-default px-6 text-center py-3">
                Pay
            </button>
        </div>
    </form>
</div>
</body>
</html>
