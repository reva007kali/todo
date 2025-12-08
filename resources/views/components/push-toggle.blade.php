<div x-data="pushNotification()" class="fixed bottom-4 right-4 z-50">
    <!-- Tombol hanya muncul jika support SW dan belum subscribe -->
    <button x-show="isSupported && !isSubscribed" 
            @click="subscribe"
            class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-3 rounded-full shadow-lg flex items-center gap-2 transition transform hover:scale-105">
        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
        </svg>
        <span>Aktifkan Reminder</span>
    </button>
</div>

<script>
    function pushNotification() {
        return {
            isSupported: 'serviceWorker' in navigator && 'PushManager' in window,
            isSubscribed: false,
            vapidKey: '{{ env("VAPID_PUBLIC_KEY") }}',

            async init() {
                if(this.isSupported) {
                    const reg = await navigator.serviceWorker.ready;
                    const sub = await reg.pushManager.getSubscription();
                    if(sub) this.isSubscribed = true;
                }
            },

            async subscribe() {
                const reg = await navigator.serviceWorker.ready;
                
                try {
                    const sub = await reg.pushManager.subscribe({
                        userVisibleOnly: true,
                        applicationServerKey: this.urlBase64ToUint8Array(this.vapidKey)
                    });

                    // Kirim ke Backend Laravel
                    await axios.post('/api/push-subscribe', sub);
                    
                    this.isSubscribed = true;
                    alert('Notifikasi berhasil diaktifkan!');
                } catch (e) {
                    console.error('Gagal subscribe:', e);
                    alert('Gagal mengaktifkan notifikasi. Pastikan izin diberikan.');
                }
            },

            // Helper convert VAPID Key
            urlBase64ToUint8Array(base64String) {
                const padding = '='.repeat((4 - base64String.length % 4) % 4);
                const base64 = (base64String + padding).replace(/\-/g, '+').replace(/_/g, '/');
                const rawData = window.atob(base64);
                const outputArray = new Uint8Array(rawData.length);
                for (let i = 0; i < rawData.length; ++i) {
                    outputArray[i] = rawData.charCodeAt(i);
                }
                return outputArray;
            }
        }
    }
</script>