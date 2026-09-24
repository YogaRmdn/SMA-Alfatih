@props(['route', 'label' => 'Hapus Semua', 'message' => null])

<form id="delete-all-form" method="POST" action="{{ route($route) }}" class="inline-block w-full sm:w-auto">
    @csrf
    @method('DELETE')
    <button type="button"
            onclick="confirmDelete('delete-all-form', {{ Js::from($message ?? 'Semua data akan dihapus secara permanen dan tidak dapat dikembalikan. Lanjutkan?') }})"
            class="inline-flex w-full items-center justify-center gap-2 rounded-lg border border-red-200 bg-white px-4 py-2 text-sm font-semibold text-red-600 transition hover:border-red-300 hover:bg-red-50 sm:w-auto">
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
        {{ $label }}
    </button>
</form>