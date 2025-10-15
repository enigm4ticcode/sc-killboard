<x-guest-layout>
    <div class="mx-auto max-w-7xl p-6 lg:p-10">
        <div class="rounded-xl border border-gray-200/60 bg-white/70 p-8 shadow-sm backdrop-blur dark:border-white/10 dark:bg-gray-900/40">
            <h1 class="text-2xl font-semibold tracking-tight text-gray-900 dark:text-gray-100">
                {{ config('app.name') }}
            </h1>

            <div class="mt-6 flex flex-wrap gap-3">
                <a href="{{ url('/admin') }}" class="inline-flex items-center rounded-lg bg-amber-600 px-4 py-2 text-sm font-medium text-white shadow hover:bg-amber-700 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:ring-offset-2 dark:focus:ring-offset-gray-950">
                    Upload Game.log
                </a>
            </div>
        </div>
    </div>
</x-guest-layout>
