<!DOCTYPE html>
<html lang="en">

<head>
    <title>Serve</title>
    @include('user.layout.head')
</head>

<body class="font-poppins bg-gray-50">
    <div class="w-full sm:max-w-sm mx-auto h-screen">
        <div class="sm:max-w-sm">
            <!-- NAVBAR -->
            <div class="fixed top-0 left-0 right-0 z-50 w-full sm:max-w-sm mx-auto">
                <div class="p-4 bg-white shadow-xl space-y-4 rounded-b-[20px]">
                    <div class="flex">
                        <a href="{{ route('user-home') }}">
                            <img src="{{ asset('/img/home.svg') }}" alt="Home">
                        </a>
                        <div class="mx-auto">
                            <h1 class="text-center text-xl font-extralight">Choose Serve</h1>
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
                                <h1 class="text-sm font-light">Payment</h1>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="h-32"></div>

            <!-- BODY -->
            <div class="p-4 space-y-4">
                <div class="space-y-2">
                    <h1><span class="text-red-500">*</span> Pilih Layanan</h1>
                    <div class="flex gap-2">
                        <!-- Dine In Button -->
                        <form class="w-full p-2 bg-white border-2 border-gray-100 rounded-md cursor-pointer text-black text-center" action="{{ route('user-postdineIn') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <button type="submit">
                                <div
                                    class="">
                                    <h1 class="text-center font-semibold">Dine In</h1>
                                </div>
                            </button>
                        </form>
                        <!-- Delivery Button -->
                        <form class="w-full p-2 bg-white border-2 border-gray-100 rounded-md cursor-pointer text-black text-center" action="{{ route('user-postdelivery') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <button type="submit">
                                <div
                                    class="">
                                    <h1 class="text-center font-semibold">Delivery</h1>
                                </div>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
