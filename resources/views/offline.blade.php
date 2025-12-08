<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Offline - Task Manager</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-zinc-50 flex items-center justify-center h-screen text-center px-4">
    <div>
        <div class="mb-6 flex justify-center">
            <div class="bg-zinc-200 p-4 rounded-full">
                <svg class="w-12 h-12 text-zinc-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" /> <!-- Icon slash wifi ibaratnya -->
                </svg>
            </div>
        </div>
        <h1 class="text-2xl font-bold text-zinc-900">Anda sedang Offline</h1>
        <p class="text-zinc-500 mt-2 mb-6">Koneksi internet terputus. Beberapa fitur mungkin tidak tersedia, tapi data lokal tetap aman.</p>
        <button onclick="window.location.reload()" class="bg-indigo-600 text-white px-6 py-2 rounded-lg font-medium hover:bg-indigo-700 transition">
            Coba Lagi
        </button>
    </div>
</body>
</html>