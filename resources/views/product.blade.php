<!DOCTYPE html>
<html lang="en">

<head>
    <title>Products</title>
    @include('layout.head')
    <!-- DataTables CSS -->
    <link href="//cdn.datatables.net/2.0.2/css/dataTables.dataTables.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        /* Override DataTables Style */
        .dataTables_wrapper .dataTables_length select {
            padding-right: 2rem;
            border-radius: 0.5rem;
        }

        .dataTables_wrapper .dataTables_filter input {
            padding: 0.5rem;
            border-radius: 0.5rem;
            border: 1px solid #d1d5db;
        }

        table.dataTable.no-footer {
            border-bottom: 1px solid #e5e7eb;
        }
    </style>
</head>

<body class="bg-gray-50 font-sans">
    @include('layout.sidebar')

    <main class="md:ml-64 xl:ml-72 2xl:ml-72">
        @include('layout.navbar')
        <div class="p-6 space-y-6">

            <!-- Header Section -->
            <div
                class="md:flex justify-between items-center bg-white p-5 rounded-xl shadow-sm border border-gray-100 space-y-2 md:space-y-0">
                <div>
                    <h1 class="font-bold text-2xl text-gray-800 flex items-center gap-2">
                        <i class="fas fa-tags text-red-500"></i>Product Management
                    </h1>
                    <p class="text-sm text-gray-500">Manage system products</p>
                </div>
                <div class="flex gap-2">
                    <button id="addBtn"
                        class="px-10 py-3 bg-red-500 text-white rounded-lg shadow-md hover:bg-red-600 transition font-semibold flex items-center gap-2">
                        <i class="fas fa-plus"></i> Add
                    </button>
                    <button id="addBtn"
                        class="px-10 py-3 bg-yellow-500 text-white rounded-lg shadow-md hover:bg-yellow-600 transition font-semibold flex items-center gap-2">
                        <i class="fa fa-wrench"></i> Set Ingridients
                    </button>
                </div>
            </div>

            @foreach ($category as $item)
                <div>
                    <h1 class="text-black text-xl font-bold">{{ $item->name }}</h1>
                </div>
                <div class="grid grid-cols-2 md:grid-cols-4 xl:grid-cols-6 gap-2 p-2">
                    @foreach ($item->menus as $menu)
                        <div class='w-full'>
                            <div class='p-2 rounded-xl relative bg-red-900 space-y-2'>
                                <div class='space-y-2'>
                                    <div class='bg-gray-100 p-2 rounded-xl '>
                                        <img src="{{ asset('storage/img/' . basename($menu->img)) }}" alt="Product Image"
                                            class='mx-auto my-auto w-14 h-17 rounded-xl relative' />
                                    </div>
                                    <div class='space-y-1'>
                                        <h1 class='font-extrabold text-sm text-white'>{{ $menu->name }}
                                        </h1>
                                        <p class='font-light text-sm text-white line-clamp-1'>
                                            {{ $menu->description }}
                                        </p>
                                        <div class='flex space-x-2'>
                                            <svg width="15" height="18" viewBox="0 0 13 12" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <g clipPath="url(#clip0_128_11)">
                                                    <path
                                                        d="M6.09426 7.25C5.62721 7.25 5.17929 7.43437 4.84904 7.76256C4.51879 8.09075 4.33325 8.53587 4.33325 9C4.33325 9.46413 4.51879 9.90925 4.84904 10.2374C5.17929 10.5656 5.62721 10.75 6.09426 10.75C6.56131 10.75 7.00922 10.5656 7.33948 10.2374C7.66973 9.90925 7.85526 9.46413 7.85526 9C7.85526 8.53587 7.66973 8.09075 7.33948 7.76256C7.00922 7.43437 6.56131 7.25 6.09426 7.25ZM5.33954 9C5.33954 8.80109 5.41906 8.61032 5.56059 8.46967C5.70213 8.32902 5.89409 8.25 6.09426 8.25C6.29442 8.25 6.48639 8.32902 6.62792 8.46967C6.76946 8.61032 6.84898 8.80109 6.84898 9C6.84897 9.19891 6.76946 9.38968 6.62792 9.53033C6.48639 9.67098 6.29442 9.75 6.09426 9.75C5.89409 9.75 5.70213 9.67098 5.56059 9.53033C5.41906 9.38968 5.33954 9.19891 5.33954 9Z"
                                                        fill="#42FF00" />
                                                    <path
                                                        d="M8.87468 3.55797L7.27518 1.32947L1.39392 5.99847L1.06788 5.99497V5.99997H0.811279V12H11.3773V5.99997H10.8933L9.93027 3.20047L8.87468 3.55797ZM9.83015 5.99997H4.78461L8.5426 4.72697L9.30839 4.48347L9.83015 5.99997ZM7.88046 3.89497L4.00122 5.20897L7.07342 2.76997L7.88046 3.89497ZM1.81757 10.0845V7.91447C2.02984 7.83972 2.22265 7.71883 2.38194 7.56062C2.54123 7.40241 2.66299 7.21087 2.73832 6.99997H9.45027C9.52558 7.21095 9.64731 7.40257 9.8066 7.56086C9.96589 7.71916 10.1587 7.84013 10.371 7.91497V10.085C10.1587 10.1598 9.96589 10.2808 9.8066 10.4391C9.64731 10.5974 9.52558 10.789 9.45027 11H2.73933C2.66395 10.7888 2.54208 10.597 2.38261 10.4387C2.22314 10.2803 2.0301 10.1593 1.81757 10.0845Z"
                                                        fill="#42FF00" />
                                                </g>
                                                <defs>
                                                    <clipPath id="clip0_128_11">
                                                        <rect width="12.0755" height="12" fill="white"
                                                            transform="translate(0.0565186)" />
                                                    </clipPath>
                                                </defs>
                                            </svg>
                                            <h2 class='font-bold text-sm my-auto text-white text-center rounded-xl'>
                                                {{ number_format($menu->price, 0, ',', '.') }}
                                            </h2>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex justify-between gap-2">
                                    <a href="{{ route('editproduct', ['id' => $menu->id]) }}"
                                        class="p-1 w-full text-white hover:text-black bg-blue-500 rounded-xl text-center">Edit</a>
                                    <form class="p-1 w-full text-white hover:text-black bg-red-500 rounded-xl text-center"
                                        method="post" action="{{ route('delproduct', ['id' => $menu->id]) }}">
                                        @csrf
                                        @method('delete')
                                        <button type="submit">Hapus</button>
                                    </form>
                                </div>

                            </div>
                        </div>
                    @endforeach
                </div>
            @endforeach


        </div>
    </main>

    <!-- ADD MODAL -->
    <div id="addModal"
        class="hidden fixed inset-0 bg-gray-900/60 backdrop-blur-sm flex items-center justify-center z-50 overflow-y-auto px-4 py-6">
        <div class="bg-white rounded-2xl p-8 w-full max-w-lg shadow-2xl relative transform transition-all scale-100">
            <button id="closeAddModal" class="absolute top-5 right-5 text-gray-400 hover:text-gray-600 transition">
                <i class="fas fa-times text-xl"></i>
            </button>
            <h2 class="text-2xl font-bold mb-6 text-gray-800 flex items-center gap-2">
                <i class="fas fa-tags text-red-500"></i> Add
            </h2>

            <form id="addForm" method="post" action="{{ route('postcategory') }}" enctype="multipart/form-data"
                class="space-y-5">
                @csrf @method('post')

                <div class="">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Name</label>
                        <input type="text" name="name"
                            class="w-full rounded-lg border-gray-300 shadow-sm p-2.5 border focus:ring-2 focus:ring-yellow-500"
                            required>
                    </div>
                </div>

                <div class="">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Description</label>
                        <textarea name="desc"
                            class="w-full rounded-lg border-gray-300 shadow-sm p-2.5 border focus:ring-2 focus:ring-yellow-500"
                            required></textarea>
                    </div>
                </div>

                <button type="submit"
                    class="w-full py-3 bg-red-500 text-white font-bold rounded-lg shadow-md hover:bg-yellow-600 transition flex justify-center items-center gap-2">
                    <i class="fas fa-check"></i> Save
                </button>
            </form>
        </div>
    </div>

    <!-- EDIT MODAL -->
    <div id="editModal"
        class="hidden fixed inset-0 bg-gray-900/60 backdrop-blur-sm flex items-center justify-center z-50 overflow-y-auto px-4 py-6">
        <div class="bg-white rounded-2xl p-8 w-full max-w-lg shadow-2xl relative transform transition-all scale-100">
            <button id="closeModal" class="absolute top-5 right-5 text-gray-400 hover:text-gray-600 transition">
                <i class="fas fa-times text-xl"></i>
            </button>
            <h2 class="text-2xl font-bold mb-6 text-gray-800 flex items-center gap-2">
                <i class="fas fa-edit text-blue-600"></i> Edit
            </h2>

            <form id="editForm" method="post" enctype="multipart/form-data" class="space-y-5">
                @csrf @method('put')

                <div class="">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Name</label>
                        <input type="text" id="editName" name="name"
                            class="w-full rounded-lg border-gray-300 shadow-sm p-2.5 border focus:ring-2 focus:ring-blue-500"
                            required>
                    </div>
                </div>

                <div class="">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Description</label>
                        <textarea type="text" id="editDesc" name="desc"
                            class="w-full rounded-lg border-gray-300 shadow-sm p-2.5 border focus:ring-2 focus:ring-blue-500"
                            required></textarea>
                    </div>
                </div>

                <button type="submit"
                    class="w-full py-3 bg-blue-600 text-white font-bold rounded-lg shadow-md hover:bg-blue-700 transition flex justify-center items-center gap-2">
                    <i class="fas fa-save"></i> Update
                </button>
            </form>
        </div>
    </div>

    <!-- SCRIPTS -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
        integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="//cdn.datatables.net/2.0.2/js/dataTables.min.js"></script>
    <script>
        $(document).ready(function () {
            // Init DataTable
            new DataTable('#myTable', {});

            // Modal Logic
            const addModal = $('#addModal');
            const editModal = $('#editModal');

            $('#addBtn').click(() => addModal.removeClass('hidden'));
            $('#closeAddModal').click(() => addModal.addClass('hidden'));

            // Edit Logic
            $(document).on('click', '.editBtn', function () {
                const btn = $(this);
                $('#editName').val(btn.data('name'));
                $('#editDesc').val(btn.data('desc'));

                $('#editForm').attr('action', `/category/${btn.data('id')}/update`);
                editModal.removeClass('hidden');
            });

            $('#closeModal').click(() => editModal.addClass('hidden'));

            $(window).click((e) => {
                if (e.target === addModal[0]) addModal.addClass('hidden');
                if (e.target === editModal[0]) editModal.addClass('hidden');
            });

            // Delete confirmation
            $(document).on('click', '.delete-confirm', function (e) {
                e.preventDefault();
                const form = $(this).closest('form');
                Swal.fire({
                    title: 'Hapus?',
                    text: "Data akan dihapus permanen.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Ya, Hapus',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) form.submit();
                });

            });
        });
    </script>

    @include('sweetalert::alert')

    @include('layout.loading')

</body>

</html>