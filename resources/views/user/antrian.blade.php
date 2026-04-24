<!DOCTYPE html>
<html lang="en">

<head>
    <title>Antrian</title>
    @include('user.layout.head')
    <link href="//cdn.datatables.net/2.0.2/css/dataTables.dataTables.min.css" rel="stylesheet" />
</head>

<body class="font-poppins bg-gray-50">
    <div class='w-full sm:max-w-sm mx-auto h-screen '>
        <div class='sm:max-w-sm'>
            {{-- NAVBAR --}}
            <div class="fixed top-0 left-0 right-0 z-50 w-full sm:max-w-sm mx-auto">
                <div class="p-6 bg-white shadow-xl space-y-4 rounded-b-[20px]">
                    <div class="flex ">
                        <a href="{{ route('user-home') }}">
                            <div>
                                <img src="{{ asset('/img/home.svg') }}" alt="">
                            </div>
                        </a>
                        <div class="mx-auto">
                            <h1 class="text-center text-xl font-extralight">Antrian</h1>
                        </div>
                    </div>
                </div>
            </div>
            <div class="h-20"></div>

            {{-- BODY --}}
            <div class="p-4 ">
                <div class="">
                    <div class="overflow-auto">
                        <table id="myTable" class="bg-white border-2">
                            <thead class="w-fit">
                                <th>No</th>
                                <th>Nama</th>
                                <th>Order</th>
                                <th>Status</th>
                            </thead>
                            <tbody>
                                @php
                                    $no = 1;
                                @endphp
                                @foreach ($orders as $order)
                                    <tr class="border-2">
                                        <td>{{ $no++ }}</td>
                                        <td>{{ $order->atas_nama }}</td>
                                        <td>
                                            @foreach ($order->cart->cartMenus as $cartMenu)
                                                {{ $cartMenu->menu->name }} - {{ $cartMenu->quantity }} -
                                                {{ $cartMenu->notes }} <br />
                                            @endforeach
                                        </td>
                                        <td>{{ $order->status }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>

        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
        integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="//cdn.datatables.net/2.0.2/js/dataTables.min.js"></script>
    <script>
        $(document).ready(function() {
            let table = new DataTable('#myTable', {

            });
        });
    </script>
</body>

</html>
