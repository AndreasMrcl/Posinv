<!DOCTYPE html>
<html lang="en">

<head>
    <title>Inventory</title>
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
                        <i class="fas fa-tags text-red-500"></i> Inventory Management
                    </h1>
                    <p class="text-sm text-gray-500">Organize system inventory</p>
                </div>
                <div class="flex gap-2">
                    <button id="receiveBtn"
                        class="px-6 py-3 bg-green-500 text-white rounded-lg shadow-md hover:bg-green-600 transition font-semibold flex items-center gap-2">
                        <i class="fas fa-truck-loading"></i> Terima Bahan
                    </button>
                    <button id="addBtn"
                        class="px-10 py-3 bg-red-500 text-white rounded-lg shadow-md hover:bg-red-600 transition font-semibold flex items-center gap-2">
                        <i class="fas fa-plus"></i> Add
                    </button>
                </div>
            </div>

            @php $lowStockCount = $invents->filter(fn($i) => $i->isLowStock())->count(); @endphp
            @if ($lowStockCount > 0)
                <div class="bg-yellow-50 border-l-4 border-yellow-500 p-4 rounded-lg flex items-center gap-3">
                    <i class="fas fa-exclamation-triangle text-yellow-500 text-xl"></i>
                    <span class="text-yellow-800 font-semibold">
                        {{ $lowStockCount }} bahan stoknya rendah, segera lakukan restock!
                    </span>
                </div>
            @endif

            <!-- Table Section -->
            <div class="w-full bg-white rounded-xl shadow-md border border-gray-100">
                <div class="p-5 overflow-auto">
                    <table id="myTable" class="w-full text-left">
                        <thead class="bg-gray-100 text-gray-600 text-sm leading-normal">
                            <tr>
                                <th class="p-4 font-bold text-center rounded-tl-lg" width="5%">No</th>
                                <th class="p-4 font-bold text-center" width="15%">Created at</th>
                                <th class="p-4 font-bold">Name</th>
                                <th class="p-4 font-bold">Stock</th>
                                <th class="p-4 font-bold">Min Stock</th>
                                <th class="p-4 font-bold">Unit</th>
                                <th class="p-4 font-bold text-center rounded-tr-lg" width="15%">Action</th>
                            </tr>
                        </thead>

                        <tbody class="text-gray-700 text-sm">
                            @php $no = 1; @endphp
                            @foreach ($invents as $item)
                                <tr class="hover:bg-gray-50 transition duration-150 {{ $item->isLowStock() ? 'bg-yellow-50' : '' }}">
                                    <td class="p-4 font-medium text-center">{{ $no++ }}</td>

                                    <td class="p-4 font-medium text-center">
                                        {{ \Carbon\Carbon::parse($item->created_at)->format('d M Y') }}
                                    </td>

                                    <td class="p-4">
                                        <span class="font-semibold text-gray-800">
                                            {{ $item->name }}
                                        </span>
                                    </td>

                                    <td class="p-4">
                                        <span class="font-semibold {{ $item->isLowStock() ? 'text-red-600' : 'text-gray-800' }}">
                                            {{ $item->stock }}
                                            @if ($item->isLowStock())
                                                <i class="fas fa-exclamation-triangle text-yellow-500 ml-1" title="Stok rendah"></i>
                                            @endif
                                        </span>
                                    </td>

                                    <td class="p-4">
                                        <span class="text-gray-600">
                                            {{ $item->min_stock > 0 ? $item->min_stock : '-' }}
                                        </span>
                                    </td>

                                    <td class="p-4">
                                        <span class="font-semibold text-gray-800">
                                            {{ $item->unit }}
                                        </span>
                                    </td>

                                    <td class="p-4">
                                        <div class="flex justify-center items-center gap-2">
                                            <button
                                                class="editBtn w-9 h-9 flex items-center justify-center bg-blue-500 text-white rounded-lg shadow hover:bg-blue-600 hover:scale-105 transition"
                                                data-id="{{ $item->id }}" data-name="{{ $item->name }}"
                                                data-stock="{{ $item->stock }}" data-min-stock="{{ $item->min_stock }}" data-unit="{{ $item->unit }}" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <form method="post" action="{{ route('delinvent', ['id' => $item->id]) }}"
                                                class="inline deleteForm">
                                                @csrf
                                                @method('delete')
                                                <button type="button"
                                                    class="delete-confirm w-9 h-9 flex items-center justify-center bg-red-500 text-white rounded-lg shadow hover:bg-red-600 hover:scale-105 transition"
                                                    title="Hapus">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
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

            <form id="addForm" method="post" action="{{ route('postinvent') }}" enctype="multipart/form-data"
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
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Stock</label>
                        <input type="number" name="stock"
                            class="w-full rounded-lg border-gray-300 shadow-sm p-2.5 border focus:ring-2 focus:ring-yellow-500"
                            required>
                    </div>
                </div>

                <div class="">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Min Stock <span class="text-gray-400 text-xs">(opsional, alert kalau stok di bawah ini)</span></label>
                        <input type="number" name="min_stock" min="0" value="0"
                            class="w-full rounded-lg border-gray-300 shadow-sm p-2.5 border focus:ring-2 focus:ring-yellow-500">
                    </div>
                </div>

                <div class="">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Unit</label>
                        <select name="unit"
                            class="w-full rounded-lg border-gray-300 shadow-sm p-2.5 border focus:ring-2 focus:ring-yellow-500"
                            required>
                            <option value="" disabled selected>Pilih Unit</option>
                            <option value="pcs">Pcs</option>
                            <option value="kg">Kg</option>
                            <option value="g">Gram</option>
                            <option value="mg">Miligram</option>
                            <option value="liter">Liter</option>
                            <option value="ml">Mililiter</option>
                        </select>
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
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Stock</label>
                        <input type="number" id="editStock" name="stock"
                            class="w-full rounded-lg border-gray-300 shadow-sm p-2.5 border focus:ring-2 focus:ring-blue-500"
                            required>
                    </div>
                </div>
                <div class="">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Min Stock <span class="text-gray-400 text-xs">(opsional)</span></label>
                        <input type="number" id="editMinStock" name="min_stock" min="0"
                            class="w-full rounded-lg border-gray-300 shadow-sm p-2.5 border focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>
                <div class="">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Unit</label>
                        <select id="editUnit" name="unit"
                            class="w-full rounded-lg border-gray-300 shadow-sm p-2.5 border focus:ring-2 focus:ring-blue-500"
                            required>
                            <option value="" disabled>Pilih Unit</option>
                            <option value="pcs">Pcs</option>
                            <option value="kg">Kg</option>
                            <option value="g">Gram</option>
                            <option value="mg">Miligram</option>
                            <option value="liter">Liter</option>
                            <option value="ml">Mililiter</option>
                        </select>
                    </div>
                </div>
                <button type="submit"
                    class="w-full py-3 bg-blue-600 text-white font-bold rounded-lg shadow-md hover:bg-blue-700 transition flex justify-center items-center gap-2">
                    <i class="fas fa-save"></i> Perbarui
                </button>
            </form>
        </div>
    </div>

    <!-- RECEIVE MODAL -->
    <div id="receiveModal"
        class="hidden fixed inset-0 bg-gray-900/60 backdrop-blur-sm flex items-center justify-center z-50 overflow-y-auto px-4 py-6">
        <div class="bg-white rounded-2xl p-8 w-full max-w-lg shadow-2xl relative transform transition-all scale-100">
            <button id="closeReceiveModal" class="absolute top-5 right-5 text-gray-400 hover:text-gray-600 transition">
                <i class="fas fa-times text-xl"></i>
            </button>
            <h2 class="text-2xl font-bold mb-6 text-gray-800 flex items-center gap-2">
                <i class="fas fa-truck-loading text-green-500"></i> Terima Bahan
            </h2>

            <form method="post" action="{{ route('receiveinvent') }}" class="space-y-5">
                @csrf

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Bahan</label>
                    <select name="invent_id"
                        class="w-full rounded-lg border-gray-300 shadow-sm p-2.5 border focus:ring-2 focus:ring-green-500"
                        required>
                        <option value="" disabled selected>Pilih Bahan</option>
                        @foreach ($invents as $item)
                            <option value="{{ $item->id }}">{{ $item->name }} (stok: {{ $item->stock }} {{ $item->unit }})</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Jumlah Diterima</label>
                    <input type="number" name="quantity" min="1"
                        class="w-full rounded-lg border-gray-300 shadow-sm p-2.5 border focus:ring-2 focus:ring-green-500"
                        required>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Catatan <span class="text-gray-400 text-xs">(opsional, mis: nama supplier)</span></label>
                    <input type="text" name="notes" maxlength="255"
                        class="w-full rounded-lg border-gray-300 shadow-sm p-2.5 border focus:ring-2 focus:ring-green-500">
                </div>

                <button type="submit"
                    class="w-full py-3 bg-green-500 text-white font-bold rounded-lg shadow-md hover:bg-green-600 transition flex justify-center items-center gap-2">
                    <i class="fas fa-check"></i> Catat Penerimaan
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
            const receiveModal = $('#receiveModal');

            $('#addBtn').click(() => addModal.removeClass('hidden'));
            $('#closeAddModal').click(() => addModal.addClass('hidden'));

            $('#receiveBtn').click(() => receiveModal.removeClass('hidden'));
            $('#closeReceiveModal').click(() => receiveModal.addClass('hidden'));

            // Edit Logic
            $(document).on('click', '.editBtn', function () {
                const btn = $(this);
                $('#editName').val(btn.data('name'));
                $('#editStock').val(btn.data('stock'));
                $('#editMinStock').val(btn.data('min-stock'));
                $('#editUnit').val(btn.data('unit'));

                $('#editForm').attr('action', `/invent/${btn.data('id')}/update`);
                editModal.removeClass('hidden');
            });

            $('#closeModal').click(() => editModal.addClass('hidden'));

            $(window).click((e) => {
                if (e.target === addModal[0]) addModal.addClass('hidden');
                if (e.target === editModal[0]) editModal.addClass('hidden');
                if (e.target === receiveModal[0]) receiveModal.addClass('hidden');
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