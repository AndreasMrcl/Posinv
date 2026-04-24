<!DOCTYPE html>
<html lang="en">

<head>
    <title>Cart</title>
    @include('user.layout.head')
</head>

<body class="font-poppins bg-gray-50">
    <div class='w-full sm:max-w-sm mx-auto h-screen '>
        <div class='sm:max-w-sm'>
            {{-- NAVBAR --}}
            <div class="fixed top-0 left-0 right-0 z-50 w-full sm:max-w-sm mx-auto">
                <div class="p-4 bg-white shadow-xl space-y-4 rounded-b-[20px]">
                    <div class="flex ">
                        <a href="{{ route('user-home') }}">
                            <div>
                                <img src="{{ asset('/img/home.svg') }}" alt="">
                            </div>
                        </a>
                        <div class="mx-auto">
                            <h1 class="text-center text-xl font-extralight">Check Your Cart</h1>
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
                                    <h1 class="text-sm font-bold">Cart</h1>
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

            {{-- BODY --}}
            <div class="p-4 space-y-4">
                @foreach ($cart->cartMenus as $item)
                    <div class="grid grid-cols-3">
                        <div class="w-16 h-32 my-auto mx-auto">
                            <img src="{{ asset('storage/img/' . basename($item->menu->img)) }}" alt="Product Image"
                                class='mx-auto my-auto w-full h-full' />
                        </div>
                        <div class="space-y-6">
                            <div class="flex gap-2">
                                <h1 class="font-bold">{{ $item->quantity }}</h1>
                                <h1>X</h1>
                                <h1 class="font-bold">{{ $item->menu->name }}</h1>
                            </div>
                            <div>
                                <h1>-{{ $item->menu->notes }}</h1>
                            </div>
                            <div class="flex gap-16">
                                <h1 class="font-semibold text-lg">Subtotal</h1>
                                <h1 class="font-semibold text-lg">Rp.{{ number_format($item->subtotal, 0, ',', '.') }}
                                </h1>
                            </div>
                        </div>
                        <div class="flex flex-col items-end">
                            <div class="p-2 bg-red-800 w-fit rounded-md">
                                <form class="text-center" method="post"
                                    action="{{ route('user-removecart', ['id' => $item->id]) }}">
                                    @csrf
                                    @method('delete')
                                    <button type="submit" class="w-8 h-8 mx-auto">
                                        <img class="w-full h-full" src="{{ asset('/img/trash.svg') }}" alt="">
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- FOOTER --}}
            <div class="h-20"></div>
            <div class="flex flex-col items-center justify-center">
                <div class="fixed bottom-4 right-0 left-0 max-w-xs bg-white p-1 rounded-md mx-auto">
                    <div class="grid grid-cols-2">
                        <div class="mx-auto">
                            <h1 class="text-lg font-light">Total</h1>
                            <h1 class="font-extrabold text-xl">Rp.{{ number_format($cart->total_amount, 0, ',', '.') }}
                            </h1>
                        </div>
                        <div class="my-auto">
                            @if ($cart->total_amount > 0)
                                <a href="{{ route('user-serve') }}">
                                    <h1
                                        class="bg-black bg-opacity-90 font-bold text-white w-3/4 mx-auto text-base p-3 rounded-full text-center">
                                        Payment >
                                    </h1>
                                </a>
                            @else
                                <div
                                    class="bg-gray-400 font-bold text-white w-3/4 mx-auto text-base p-3 rounded-full text-center cursor-not-allowed">
                                    Payment >
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
