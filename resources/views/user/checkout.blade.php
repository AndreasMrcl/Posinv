<!DOCTYPE html>
<html lang="en">

<head>
    <title>Checkout</title>
    @include('user.layout.head')
    <script type="text/javascript" src="https://app.midtrans.com/snap/snap.js"
        data-client-key="Mid-client-KrOCGoZpRFFFE4Ey"></script>
</head>

<body class="font-poppins bg-gray-50">
    <div class='w-full sm:max-w-sm mx-auto h-screen'>
        <div class='sm:max-w-sm'>
            <!-- NAVBAR -->
            <div class="fixed top-0 left-0 right-0 z-50 w-full sm:max-w-sm mx-auto">
                <div class="p-6 bg-white shadow-xl space-y-4 rounded-b-[20px]">
                    <div class="flex items-center justify-center">
                        <div class="mx-auto">
                            <h1 class="text-center text-xl font-extralight">Checkout</h1>
                        </div>
                    </div>
                </div>
            </div>

            <div class="h-20"></div>

            <!-- BODY -->
            <div class="p-4 space-y-4">
                <div class="space-y-2">
                    <h1><span class="text-red-500">*</span> Detail Pembayaran</h1>
                    <p class="text-xs">Data digunakan dalam proses pemesanan. Pastikan data yang Anda masukkan
                        valid.</p>
                </div>
                <div class="grid grid-cols-2 gap-2">
                <div class="space-y-2">
                    <h1><span class="text-red-500">*</span> Pilih Layanan</h1>
                    <input class="border w-full rounded-xl p-2" type="text" id="layanan" name="layanan"
                        value="{{ $order->layanan }}" readonly>
                </div>
                <div class="space-y-2">
                    <h1><span class="text-red-500">*</span> Order Id</h1>
                    <input class="border w-full rounded-xl p-2" value="{{ $order->no_order }}" type="text"
                        id="no_order" name="no_order">
                </div>
                </div>
                <div class="grid grid-cols-2 gap-2">
                    <div class="space-y-2">
                        <h1><span class="text-red-500">*</span>Cabang</h1>
                        <input class="border w-full rounded-xl p-2" type="text" name="cabang"
                            value="{{ $order->cabang }}" readonly>
                    </div>
                    <div class="space-y-2">
                        <h1><span class="text-red-500">*</span>Tujuan</h1>
                        <input type="text" class="border w-full rounded-xl p-2" name="alamat"
                            value="{{ $order->alamat }}" readonly />
                    </div>
                    <div class="space-y-2">
                        <h1><span class="text-red-500">*</span> Atas Nama</h1>
                        <input class="border w-full rounded-xl p-2" type="text" name="atas_nama"
                            value="{{ $order->atas_nama }}" readonly>
                    </div>
                    <div class="space-y-2">
                        <h1><span class="text-red-500">*</span> Nomor Ponsel</h1>
                        <input class="border w-full rounded-xl p-2" id="no_telpon" name="no_telpon"
                            value="{{ $order->no_telpon }}" readonly>
                    </div>
                </div>
                <div class="space-y-2">
                    <h1><span class="text-red-500">*</span> Cart</h1>
                    <div class="space-y-2 border py-4 rounded-xl">
                        @foreach ($order->cart->cartMenus as $item)
                            <div class="grid grid-cols-3">
                                <div class="w-12 h-24 mx-auto">
                                    <img src="{{ asset('storage/img/' . basename($item->menu->img)) }}"
                                        alt="Product Image" class="mx-auto my-auto w-full h-full" />
                                </div>
                                <div>
                                    <div class="flex justify-between">
                                        <h1 class="font-bold">{{ $item->quantity }}</h1>
                                        <h1>x</h1>
                                        <h1 class="font-bold">{{ $item->menu->name }}</h1>
                                    </div>
                                    <p class="font-extralight">-{{ $item->notes }}</p>
                                </div>
                                <div class="text-center mx-auto">
                                    <h1 class="font-semibold text-lg">
                                        Rp.{{ number_format($item->subtotal, 0, ',', '.') }}
                                    </h1>
                                </div>
                            </div>
                        @endforeach

                        <div class="space-y-2">
                            <div class="border-t mx-2 p-2">
                                <div class="flex justify-between">
                                    <h1 class="font-semibold text-lg">Ongkos Kirim</h1>
                                    <input type="hidden" id="ongkir" name="ongkir">
                                    <h1 class="font-bold text-lg" id="ongkirAmount">
                                        Rp.{{ number_format($order->ongkir, 0, ',', '.') }}
                                    </h1>
                                </div>
                            </div>
                            <div class="border-t mx-2 p-2">
                                <div class="flex justify-between">
                                    <h1 class="font-semibold text-lg">Total</h1>
                                    <h1 class="font-bold text-lg">
                                        Rp.{{ number_format($order->cart->total_amount, 0, ',', '.') }}
                                    </h1>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- FOOTER -->
            <div class="h-20"></div>
            <div class="fixed bottom-4 sm:max-w-sm w-full p-2">
                <div class="flex flex-col items-center justify-center">
                    <button class="w-3/4" id="pay-button" type="button">
                        <h1
                            class="bg-black bg-opacity-90 font-bold text-white w-full mx-auto text-base p-3 rounded-full text-center">
                            Checkout
                        </h1>
                    </button>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        // Prevent back navigation
        window.history.pushState(null, null, window.location.href);
        window.onpopstate = function() {
            window.history.pushState(null, null, window.location.href);
        };

        // Warn before refreshing or leaving the page
        window.addEventListener('beforeunload', function(event) {
            event.preventDefault();
            event.returnValue = 'Are you sure you want to leave? Any unsaved changes will be lost.';
        });

        // Disable refresh shortcuts
        document.addEventListener('keydown', function(event) {
            if ((event.key === 'F5') ||
                (event.ctrlKey && event.key === 'r') ||
                (event.metaKey && event.key === 'r')) {
                event.preventDefault();
            }
        });

        var payButton = document.getElementById('pay-button');
        var buttonContent = payButton.querySelector('h1');

        payButton.addEventListener('click', function(event) {
            event.preventDefault();

            // Add loading state
            payButton.disabled = true;
            buttonContent.textContent = 'Processing...';
            buttonContent.classList.add('animate-pulse'); // Add animation for better UX

            @if (isset($snapToken))
                window.snap.pay('{{ $snapToken }}', {
                    onSuccess: function(result) {
                        console.log('Payment successful!', result);
                        window.location.href = '{{ route('user-home') }}'; // Redirect to dashboard
                    },
                    onPending: function(result) {
                        console.log('Payment pending', result);
                        window.location.href = '{{ route('user-home') }}'; // Redirect to dashboard
                    },
                    onError: function(result) {
                        console.error('Payment failed', result);
                        window.location.href = '{{ route('user-home') }}'; // Redirect to dashboard
                    },
                    onClose: function() {
                        console.log('Payment popup closed');
                        window.location.href = '{{ route('user-home') }}'; // Redirect to dashboard
                    }
                });
            @else
                console.error('Snap token is not set!');
                resetButton();
            @endif
        });

        function resetButton() {
            payButton.disabled = false;
            buttonContent.textContent = 'Checkout';
            buttonContent.classList.remove('animate-pulse');
        }
    </script>

</body>

</html>
