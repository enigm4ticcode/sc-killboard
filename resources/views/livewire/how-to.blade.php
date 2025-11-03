<div class="card p-4 sm:p-6 lg:p-8 mx-4 sm:mx-0">
    <header class="pb-6 mb-6 border-b" style="border-color: rgb(var(--card-border));">
        <h1 class="text-3xl sm:text-4xl font-extrabold mb-2" style="color: rgb(var(--accent));">
            {{ __('app.how_it_works_title') }}
        </h1>
        <p class="text-base" style="color: rgb(var(--muted));">
            {!! __('app.how_it_works_subtitle', ['gamelog' => '<span class="font-semibold" style="color: rgb(var(--accent));">Game.log</span>']) !!}
        </p>
    </header>

    <section class="mb-8">
        <h2 class="text-2xl font-bold mb-3 flex items-center" style="color: rgb(var(--fg));">
            <span class="mr-3 text-3xl font-extrabold" style="color: rgb(var(--accent));">1.</span> {{ __('app.step_1_title') }}
        </h2>
        <div class="space-y-4" style="color: rgb(var(--fg));">
            <p>{!! __('app.step_1_p1', ['code' => '<code class="file-path">Game.log</code>']) !!}</p>
            <p>{!! __('app.step_1_p2', ['live' => '<span class="font-medium" style="color: rgb(var(--accent));">LIVE</span>']) !!}</p>

            <p class="text-lg font-mono p-3 rounded-lg shadow-inner overflow-x-auto" style="background-color: rgb(var(--bg)); color: rgb(var(--warning));">
                C:\Program Files\Roberts Space Industries\StarCitizen\LIVE\Game.log
            </p>

            <p>{{ __('app.step_1_p3') }}</p>
            <p class="font-semibold" style="color: rgb(var(--success));">
                {{ __('app.step_1_p4') }}
            </p>
            <p class="text-lg font-mono p-3 rounded-lg shadow-inner overflow-x-auto" style="background-color: rgb(var(--bg)); color: rgb(var(--warning));">
                C:\Program Files\Roberts Space Industries\StarCitizen\LIVE\LogBackups\
            </p>
            <p>{{ __('app.step_1_p5') }}</p>
        </div>
    </section>

    <div class="border-t my-6" style="border-color: rgb(var(--card-border));"></div>

    <section class="mb-8">
        <h2 class="text-2xl font-bold mb-3 flex items-center" style="color: rgb(var(--fg));">
            <span class="mr-3 text-3xl font-extrabold" style="color: rgb(var(--accent));">2.</span> {{ __('app.step_2_title') }}
        </h2>
        <div class="space-y-4" style="color: rgb(var(--fg));">
            <p>{{ __('app.step_2_p1') }}</p>
            <ol class="list-decimal list-inside space-y-2 ml-4">
                <li>{{ __('app.step_2_li1') }}</li>
                <li>{{ __('app.step_2_li2') }}</li>
                <li>{{ __('app.step_2_li3') }}</li>
                <li>{{ __('app.step_2_li4') }}</li>
            </ol>
        </div>
    </section>

    <div class="border-t my-6" style="border-color: rgb(var(--card-border));"></div>

    <section class="mb-8">
        <h2 class="text-2xl font-bold mb-3 flex items-center" style="color: rgb(var(--fg));">
            <span class="mr-3 text-3xl font-extrabold" style="color: rgb(var(--accent));">3.</span> {{ __('app.step_3_title') }}
        </h2>
        <div class="space-y-4" style="color: rgb(var(--fg));">
            <p>{{ __('app.step_3_p1') }}</p>
            <p class="text-sm italic" style="color: rgb(var(--muted));">
                {{ __('app.step_3_tip') }}
            </p>
        </div>
    </section>

    <section class="mb-8">
        <h2 class="text-2xl font-bold mb-3 flex items-center" style="color: rgb(var(--fg));">
            <span class="mr-3 text-3xl font-extrabold" style="color: rgb(var(--accent));">4.</span> {{ __('app.step_4_title') }}
        </h2>
        <div class="space-y-4" style="color: rgb(var(--fg));">
            <p>{{ __('app.step_4_p1') }}</p>
            <p class="text-sm italic" style="color: rgb(var(--muted));">
                {{ __('app.step_4_tip') }}
            </p>
        </div>
    </section>

    <div class="border-t my-6" style="border-color: rgb(var(--card-border));"></div>

    <section>
        <h2 class="text-2xl font-bold mb-3 flex items-center" style="color: rgb(var(--fg));">
            <span class="mr-3 text-3xl font-extrabold" style="color: rgb(var(--accent));">5.</span> {{ __('app.step_5_title') }}
        </h2>
        <div class="space-y-4" style="color: rgb(var(--fg));">
            <p>{{ __('app.step_5_p1') }}</p>
            <ol class="list-decimal list-inside space-y-2 ml-4">
                <li>{{ __('app.step_5_li1') }}</li>
                <li>{!! __('app.step_5_li2', ['code' => '<code class="file-path">Game.log</code>']) !!}</li>
                <li>{{ __('app.step_5_li3') }}</li>
            </ol>
            <p class="font-semibold" style="color: rgb(var(--success));">
                {{ __('app.step_5_success') }}
            </p>
        </div>
    </section>
</div>
