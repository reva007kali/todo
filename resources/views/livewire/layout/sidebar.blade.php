<flux:navlist wire:poll.keep-alive variant="outline">
    <flux:navlist.group :heading="__('Platform')" class="grid">

        <!-- DASHBOARD ITEM (Dengan Realtime Badge) -->
        <flux:navlist.item icon="home" :href="route('dashboard')" :current="request()->routeIs('dashboard')"
            wire:navigate>
            <div class="flex items-center w-full">
                <span>{{ __('Dashboard') }}</span>

                <!-- Mengakses Computed Property -->
                @if ($this->sharedCount > 0)
                    <span
                        class="ml-auto flex h-5 w-5 items-center justify-center rounded-full bg-indigo-600 text-[10px] font-bold text-white shadow-sm dark:bg-indigo-500 transition-all duration-300 transform scale-100">
                        {{ $this->sharedCount }}
                    </span>
                @endif
            </div>
        </flux:navlist.item>

        <flux:navlist.item icon="list-bullet" :href="route('tasks.index')" :current="request()->routeIs('tasks.index')"
            wire:navigate>{{ __('Task List') }}
        </flux:navlist.item>

        <flux:navlist.item icon="megaphone" :href="route('summary.index')" :current="request()->routeIs('summary.index')"
            wire:navigate>{{ __('Task Summary') }}
        </flux:navlist.item>

        <!-- Menu Admin Group -->
        @if (auth()->check() && auth()->user()->role === 'admin')
            <flux:navlist.item icon="lock-closed" :href="route('admin.dashboard')"
                :current="request()->routeIs('admin.dashboard')" wire:navigate>
                {{ __('Admin Panel') }}
            </flux:navlist.item>
        @endif

    </flux:navlist.group>
</flux:navlist>
