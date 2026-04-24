<!DOCTYPE html>
<html lang="en">

<head>
    <title>Payment</title>
    @include('user.layout.head')
</head>

<body class="font-poppins bg-gray-50">
    <div class='w-full sm:max-w-sm mx-auto h-screen'>
        <form action="{{ route('user-postorder') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class='sm:max-w-sm'>
                <!-- NAVBAR -->
                <div class="fixed top-0 left-0 right-0 z-50 w-full sm:max-w-sm mx-auto">
                    <div class="p-4 bg-white shadow-xl space-y-4 rounded-b-[20px]">
                        <div class="flex ">
                            <a href="{{ route('user-home') }}">
                                <div>
                                    <img src="{{ asset('/img/home.svg') }}" alt="">
                                </div>
                            </a>
                            <div class="mx-auto">
                                <h1 class="text-center text-xl font-extralight">Payment</h1>
                            </div>
                        </div>
                        <hr>
                        <div class="flex justify-between mx-10">
                            <a href="{{ route('user-product') }}">
                                <div class="flex space-x-1">
                                    <div class="bg-black p-1 rounded-md">
                                        <h1 class="text-xs font-light text-white px-1">1</h1>
                                    </div>
                                    <div class="my-auto">
                                        <h1 class="text-sm font-light">Product</h1>
                                    </div>
                                </div>
                            </a>
                            <a href="{{ route('user-cart') }}">
                                <div class="flex space-x-1">
                                    <div class="bg-black p-1 rounded-md">
                                        <h1 class="text-xs font-light text-white px-1">2</h1>
                                    </div>
                                    <div class="my-auto">
                                        <h1 class="text-sm font-light">Cart</h1>
                                    </div>
                                </div>
                            </a>
                            <div class="flex space-x-1">
                                <div class="bg-black p-1 rounded-md">
                                    <h1 class="text-xs font-light text-white px-1">3</h1>
                                </div>
                                <div class="my-auto">
                                    <h1 class="text-sm font-bold">Payment</h1>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="h-32"></div>

                <!-- BODY -->
                <div class="p-4 space-y-4">
                    <div class="space-y-2">
                        <h1><span class="text-red-500">*</span> Detail Pembayaran</h1>
                        <p class="text-xs">Data digunakan dalam proses pemesanan. Pastikan data yang Anda masukkan
                            valid.</p>
                    </div>
                    
                    <div class="space-y-2">
                        <h1><span class="text-red-500">*</span> Pilih Layanan</h1>
                        <input class="border w-full rounded-xl p-2" value="{{ $order->layanan }}" type="text"
                            id="layanan" name="layanan">
                    </div>

                    <div class="space-y-2">
                        <h1><span class="text-red-500">*</span> Cabang</h1>
                        <input class="border w-full rounded-xl p-2" id="cabang" type="text" name="cabang"
                            value="{{ $order->cabang }}" readonly>
                    </div>

                    <div class="space-y-2">
                        <h1><span class="text-red-500">*</span> Atas Nama</h1>
                        <input class="border w-full rounded-xl p-2" placeholder="John Doe" id="atas_nama" type="text"
                            name="atas_nama">
                    </div>

                    <div class="space-y-2">
                        <h1><span class="text-red-500">*</span> Nomor Ponsel</h1>
                        <input class="border w-full rounded-xl p-2" placeholder="08XXXXXXXX" id="no_telpon"
                            name="no_telpon">
                    </div>

                    <div class="space-y-2">
                        <h1><span class="text-red-500">*</span> Cart</h1>
                        <div class="space-y-2 border py-4 rounded-xl">
                            @foreach ($cart->cartMenus as $item)
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
                                        <h1 class="font-semibold text-lg">Sub Total</h1>
                                        <h1 class="font-bold text-lg">
                                            Rp.{{ number_format($cart->total_amount, 0, ',', '.') }}
                                        </h1>
                                    </div>
                                </div>
                                <div class="border-t mx-2 p-2">
                                    <div class="flex justify-between">
                                        <h1 class="font-semibold text-lg">Ongkos Kirim</h1>
                                        <h1 class="font-bold text-lg">
                                            Rp.{{ number_format($order->ongkir, 0, ',', '.') }}
                                        </h1>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- FOOTER -->
                <div class="h-20"></div>
                <div class="fixed bottom-4 sm:max-w-sm w-full z-50 p-2">
                    <div class="flex flex-col items-center justify-center">
                        <button class="w-3/4" type="submit">
                            <h1
                                class="bg-black bg-opacity-90 text-xl font-bold text-white w-full mx-auto text-base p-3 rounded-full text-center">
                                Payment >
                            </h1>
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</body>

</html>
