<x-layouts.app.sidebar :title="$title ?? null">
    <flux:main>
        {{ $slot }}
    </flux:main>
    @auth
        <x-push-toggle />
    @endauth
</x-layouts.app.sidebar>
