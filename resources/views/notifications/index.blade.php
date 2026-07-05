<h2 class="text-xl font-bold mb-4">Notifikasi</h2>

@foreach($notifications as $notif)

<div class="p-3 border mb-2 rounded 
    {{ $notif->is_read ? 'bg-gray-100' : 'bg-white font-bold' }}">

    <a href="{{ route('notifications.read', $notif->id) }}">
        <div>{{ $notif->title }}</div>
        <div class="text-sm text-gray-500">{{ $notif->message }}</div>
    </a>

    <form method="POST" action="{{ route('notifications.delete', $notif->id) }}">
        @csrf
        @method('DELETE')

        <button class="text-red-500 text-sm mt-2">
            Hapus
        </button>
    </form>

</div>

@endforeach