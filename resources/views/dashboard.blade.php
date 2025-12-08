<x-layouts.app :title="__('Dashboard')">
        <div class="py-5">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Panggil Komponen Livewire Disini -->
                @livewire('dashboard-manager')
            </div>
        </div>
</x-layouts.app>
