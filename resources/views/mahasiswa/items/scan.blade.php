@extends('layouts.mahasiswa')

@push('styles')
<style>
    #qr-reader {
        width: 100%;
        max-width: 500px;
        margin: 0 auto;
    }
    #qr-reader video {
        border-radius: 12px;
    }
</style>
@endpush

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-xl shadow-md overflow-hidden">
        {{-- Header --}}
        <div class="bg-gradient-to-r from-green-600 to-green-700 p-6 text-center">
            <div class="flex justify-center mb-4">
                <div class="bg-white bg-opacity-20 p-4 rounded-full">
                    <i data-lucide="scan" class="w-12 h-12 text-white"></i>
                </div>
            </div>
            <h2 class="text-2xl font-bold text-white">Scan QR Code</h2>
            <p class="text-green-100 mt-1">Arahkan kamera ke QR Code barang</p>
        </div>

        <div class="p-6">
            {{-- Scanner Container --}}
            <div id="qr-reader" class="mb-6"></div>

            {{-- Result Container --}}
            <div id="scan-result" class="hidden">
                <div class="bg-green-50 border border-green-200 rounded-lg p-6">
                    <div class="flex items-center justify-center mb-4">
                        <div class="bg-green-100 p-3 rounded-full">
                            <i data-lucide="check-circle" class="w-8 h-8 text-green-600"></i>
                        </div>
                    </div>
                    <h3 class="text-lg font-bold text-center text-gray-900 mb-4">Barang Ditemukan!</h3>
                    
                    <div id="item-details" class="space-y-3">
                        {{-- Item details will be inserted here --}}
                    </div>

                    <div class="mt-6 flex space-x-3">
                        <button onclick="resetScanner()" class="flex-1 bg-gray-200 text-gray-700 py-3 rounded-lg hover:bg-gray-300 transition font-semibold">
                            Scan Lagi
                        </button>
                        <a id="view-detail-btn" href="#" class="flex-1 bg-green-600 text-white py-3 rounded-lg hover:bg-green-700 transition font-semibold text-center">
                            Lihat Detail
                        </a>
                    </div>
                </div>
            </div>

            {{-- Error Container --}}
            <div id="scan-error" class="hidden">
                <div class="bg-red-50 border border-red-200 rounded-lg p-6 text-center">
                    <div class="flex items-center justify-center mb-4">
                        <div class="bg-red-100 p-3 rounded-full">
                            <i data-lucide="x-circle" class="w-8 h-8 text-red-600"></i>
                        </div>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-2">QR Code Tidak Valid</h3>
                    <p id="error-message" class="text-gray-600 mb-4">Barang tidak ditemukan</p>
                    <button onclick="resetScanner()" class="bg-red-600 text-white px-6 py-2 rounded-lg hover:bg-red-700 transition font-semibold">
                        Coba Lagi
                    </button>
                </div>
            </div>

            {{-- Manual Input --}}
            <div class="mt-6 pt-6 border-t border-gray-200">
                <h4 class="font-semibold text-gray-900 mb-3">Atau masukkan kode barang manual:</h4>
                <form action="{{ route('mahasiswa.items.index') }}" method="GET" class="flex space-x-3">
                    <input 
                        type="text" 
                        name="search"
                        placeholder="Masukkan kode barang (ITM00001)"
                        class="flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500"
                    >
                    <button type="submit" class="bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700 transition font-semibold">
                        Cari
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
{{-- HTML5 QR Code Scanner Library --}}
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>

<script>
    let html5QrcodeScanner;

    function onScanSuccess(decodedText, decodedResult) {
        // Stop scanning
        html5QrcodeScanner.clear();
        
        // Send to server for validation
        fetch('{{ route("mahasiswa.scan.result") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ qr_data: decodedText })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showResult(data.item, data.redirect_url);
            } else {
                showError(data.message);
            }
        })
        .catch(error => {
            showError('Terjadi kesalahan saat memproses QR Code');
        });
    }

    function showResult(item, redirectUrl) {
        document.getElementById('qr-reader').style.display = 'none';
        document.getElementById('scan-error').classList.add('hidden');
        document.getElementById('scan-result').classList.remove('hidden');
        
        const statusBadge = item.status === 'tersedia' 
            ? '<span class="px-2 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">Tersedia</span>'
            : '<span class="px-2 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800">' + item.status + '</span>';

        document.getElementById('item-details').innerHTML = `
            <div class="flex justify-between items-center py-2 border-b">
                <span class="text-gray-600">Kode</span>
                <span class="font-mono font-semibold">${item.item_code}</span>
            </div>
            <div class="flex justify-between items-center py-2 border-b">
                <span class="text-gray-600">Nama</span>
                <span class="font-semibold">${item.name}</span>
            </div>
            <div class="flex justify-between items-center py-2 border-b">
                <span class="text-gray-600">Kategori</span>
                <span class="font-semibold">${item.category}</span>
            </div>
            <div class="flex justify-between items-center py-2 border-b">
                <span class="text-gray-600">Status</span>
                ${statusBadge}
            </div>
            <div class="flex justify-between items-center py-2">
                <span class="text-gray-600">Jumlah</span>
                <span class="font-semibold">${item.quantity} unit</span>
            </div>
        `;

        document.getElementById('view-detail-btn').href = redirectUrl;
    }

    function showError(message) {
        document.getElementById('qr-reader').style.display = 'none';
        document.getElementById('scan-result').classList.add('hidden');
        document.getElementById('scan-error').classList.remove('hidden');
        document.getElementById('error-message').textContent = message;
    }

    function resetScanner() {
        document.getElementById('scan-result').classList.add('hidden');
        document.getElementById('scan-error').classList.add('hidden');
        document.getElementById('qr-reader').style.display = 'block';
        
        startScanner();
    }

    function startScanner() {
        html5QrcodeScanner = new Html5QrcodeScanner(
            "qr-reader",
            { 
                fps: 10, 
                qrbox: { width: 250, height: 250 },
                rememberLastUsedCamera: true,
                supportedScanTypes: [Html5QrcodeScanType.SCAN_TYPE_CAMERA]
            }
        );
        html5QrcodeScanner.render(onScanSuccess);
    }

    // Start scanner when page loads
    document.addEventListener('DOMContentLoaded', function() {
        startScanner();
    });
</script>
@endpush
@endsection