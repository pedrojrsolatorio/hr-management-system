<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800">Notifications</h2>
    </x-slot>
    <div class="py-8 px-6 max-w-3xl mx-auto space-y-3">
        @forelse(auth()->user()->notifications as $notification)
            <div class="bg-white rounded-xl border border-gray-100 p-4 flex items-start justify-between gap-4
                {{ $notification->read_at ? 'opacity-60' : '' }}">
                <div>
                    <p class="text-sm text-gray-800">{{ $notification->data['message'] ?? 'New notification' }}</p>
                    <p class="text-xs text-gray-400 mt-1">{{ $notification->created_at->diffForHumans() }}</p>
                </div>
                @unless($notification->read_at)
                    <form method="POST" action="{{ route('notifications.read', $notification->id) }}">
                        @csrf @method('PATCH')
                        <button class="text-xs text-indigo-600 hover:underline whitespace-nowrap">Mark read</button>
                    </form>
                @endunless
            </div>
        @empty
            <p class="text-center text-gray-400 text-sm py-12">No notifications.</p>
        @endforelse
    </div>
</x-app-layout>