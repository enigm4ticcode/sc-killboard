<div class="card p-4 sm:p-6 lg:p-8 mx-4 sm:mx-0">
    <header class="pb-6 mb-6 border-b" style="border-color: rgb(var(--card-border));">
        <h1 class="text-3xl sm:text-4xl font-extrabold mb-2" style="color: rgb(var(--accent));">
            {{ __('app.api_documentation') }}
        </h1>
        <p class="text-base" style="color: rgb(var(--muted));">
            {{ __('app.api_docs_subtitle') }}
        </p>
    </header>

    <section class="mb-10">
        <h2 class="text-2xl font-bold mb-4" style="color: rgb(var(--fg));">{{ __('app.submit_new_kill') }}</h2>
        <p class="mb-4" style="color: rgb(var(--fg));">{{ __('app.submit_kill_description') }}</p>

        <div class="p-6 rounded-lg shadow-sm mb-4 border" style="background-color: rgb(var(--bg)); border-color: rgb(var(--card-border));">
            <p class="mb-2"><strong class="font-mono" style="color: rgb(var(--fg));">{{ __('app.endpoint') }}:</strong> <code style="color: rgb(var(--accent));">/api/v1/kills</code></p>
            <p><strong class="font-mono" style="color: rgb(var(--fg));">{{ __('app.method') }}:</strong> <span style="color: rgb(var(--success));" class="font-bold px-2 py-0.5 rounded-full text-sm">POST</span></p>
        </div>

        <p style="color: rgb(var(--fg));">
            <strong>{{ __('app.authentication') }}:</strong> {!! __('app.authentication_description', ['code' => '<code class="text-sm px-1 rounded" style="background-color: rgb(var(--bg));">Authorization</code>']) !!}
            <a href="{{ route('profile.edit') }}" class="hover:underline font-medium" style="color: rgb(var(--accent));">{{ __('app.your_profile') }}</a> {{ __('app.after_verification') }}
        </p>
    </section>

    <div class="border-t my-8" style="border-color: rgb(var(--card-border));"></div>

    <section class="mb-10">
        <h3 class="text-xl font-bold mb-4" style="color: rgb(var(--fg));">{{ __('app.request_headers') }}</h3>
        <div class="overflow-x-auto shadow md:rounded-lg">
            <table class="min-w-full divide-y" style="divide-color: rgb(var(--card-border));">
                <thead style="background-color: rgb(var(--table-header));">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider" style="color: rgb(var(--muted));">{{ __('app.header') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider" style="color: rgb(var(--muted));">{{ __('app.value') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider" style="color: rgb(var(--muted));">{{ __('app.required') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider" style="color: rgb(var(--muted));">{{ __('app.description') }}</th>
                </tr>
                </thead>
                <tbody class="divide-y" style="background-color: rgb(var(--card)); divide-color: rgb(var(--card-border)); color: rgb(var(--fg));">
                <tr class="transition-colors" onmouseover="this.style.backgroundColor='rgb(var(--table-hover))'" onmouseout="this.style.backgroundColor='rgb(var(--card))'">
                    <td class="px-6 py-4 whitespace-nowrap"><code>Authorization</code></td>
                    <td class="px-6 py-4 whitespace-nowrap"><code>Bearer &lt;api-key&gt;</code></td>
                    <td class="px-6 py-4 whitespace-nowrap"><span style="color: rgb(var(--success));" class="font-medium">{{ __('app.yes') }}</span></td>
                    <td class="px-6 py-4">{{ __('app.api_key_authentication') }}</td>
                </tr>
                <tr class="transition-colors" onmouseover="this.style.backgroundColor='rgb(var(--table-hover))'" onmouseout="this.style.backgroundColor='rgb(var(--card))'">
                    <td class="px-6 py-4 whitespace-nowrap"><code>Content-Type</code></td>
                    <td class="px-6 py-4 whitespace-nowrap"><code>application/json</code></td>
                    <td class="px-6 py-4 whitespace-nowrap"><span style="color: rgb(var(--success));" class="font-medium">{{ __('app.yes') }}</span></td>
                    <td class="px-6 py-4">{{ __('app.content_type_description') }}</td>
                </tr>
                <tr class="transition-colors" onmouseover="this.style.backgroundColor='rgb(var(--table-hover))'" onmouseout="this.style.backgroundColor='rgb(var(--card))'">
                    <td class="px-6 py-4 whitespace-nowrap"><code>Accept</code></td>
                    <td class="px-6 py-4 whitespace-nowrap"><code>application/json</code></td>
                    <td class="px-6 py-4 whitespace-nowrap"><span style="color: rgb(var(--success));" class="font-medium">{{ __('app.yes') }}</span></td>
                    <td class="px-6 py-4">{{ __('app.accept_description') }}</td>
                </tr>
                </tbody>
            </table>
        </div>
    </section>

    <div class="border-t my-8" style="border-color: rgb(var(--card-border));"></div>

    <section class="mb-10">
        <h3 class="text-xl font-bold mb-4" style="color: rgb(var(--fg));">{{ __('app.request_body_parameters') }}</h3>
        <div class="overflow-x-auto shadow md:rounded-lg">
            <table class="min-w-full divide-y" style="divide-color: rgb(var(--card-border));">
                <thead style="background-color: rgb(var(--table-header));">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider" style="color: rgb(var(--muted));">{{ __('app.parameter') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider" style="color: rgb(var(--muted));">{{ __('app.type') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider" style="color: rgb(var(--muted));">{{ __('app.required') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider" style="color: rgb(var(--muted));">{{ __('app.validation_rules') }}</th>
                </tr>
                </thead>
                <tbody class="divide-y" style="background-color: rgb(var(--card)); divide-color: rgb(var(--card-border)); color: rgb(var(--fg));">
                @foreach ([
                    ['username', 'String', 'Yes', '`required`, `string`, `exists:users,username`'],
                    ['timestamp', 'String', 'Yes', '`required`, `string`, `date_format:Y-m-d\TH:i:s.u\Z`'],
                    ['kill_type', 'String', 'Yes', '`required`, `string`, `min:2`, Must be one of: `vehicle`, `fps`'],
                    ['location', 'String', 'Yes', '`required`, `string`, `min:4`'],
                    ['killer', 'String', 'Yes', '`required`, `string`, `min:3`'],
                    ['victim', 'String', 'Yes', '`required`, `string`, `min:3`'],
                    ['weapon', 'String', 'Yes', '`required`, `string`, `min:3`'],
                    ['vehicle', 'String', 'No', '`sometimes`, `string`, `min:4`'],
                ] as $param)
                    <tr class="transition-colors" onmouseover="this.style.backgroundColor='rgb(var(--table-hover))'" onmouseout="this.style.backgroundColor='rgb(var(--card))'">
                        <td class="px-6 py-4 whitespace-nowrap"><code>{{ $param[0] }}</code></td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $param[1] }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span style="color: {{ $param[2] === 'Yes' ? 'rgb(var(--success))' : 'rgb(var(--muted))' }};" class="{{ $param[2] === 'Yes' ? 'font-medium' : '' }}">{{ $param[2] === 'Yes' ? __('app.yes') : __('app.no') }}</span>
                        </td>
                        <td class="px-6 py-4"><code>{{ $param[3] }}</code></td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        <p class="py-4" style="color: rgb(var(--fg));">
            {!! __('app.additional_validation_note', [
                'killer' => '<code class="text-sm px-1 rounded" style="background-color: rgb(var(--bg));">killer</code>',
                'victim' => '<code class="text-sm px-1 rounded" style="background-color: rgb(var(--bg));">victim</code>',
                'vehicle' => '<code class="text-sm px-1 rounded" style="background-color: rgb(var(--bg));">vehicle</code>'
            ]) !!}
        </p>
    </section>

    <div class="border-t my-8" style="border-color: rgb(var(--card-border));"></div>

    <section class="mb-10">
        <h3 class="text-xl font-bold mb-4" style="color: rgb(var(--fg));">{{ __('app.example_request_body') }}</h3>
        <pre class="bg-gray-900 text-gray-200 p-5 rounded-lg overflow-x-auto shadow-inner">
        <code class="language-json"><span class="text-pink-400">{</span>
            <span class="text-blue-400">"username"</span>: <span class="text-yellow-400">"discord_username"</span>,
            <span class="text-blue-400">"timestamp"</span>: <span class="text-yellow-400">"2025-10-23T10:30:00.123456Z"</span>,
            <span class="text-blue-400">"kill_type"</span>: <span class="text-yellow-400">"vehicle"</span>,
            <span class="text-blue-400">"location"</span>: <span class="text-yellow-400">"OOC_Stanton_3a"</span>,
            <span class="text-blue-400">"killer"</span>: <span class="text-yellow-400">"ENIGM4"</span>,
            <span class="text-blue-400">"victim"</span>: <span class="text-yellow-400">"Carebear_69420"</span>,
            <span class="text-blue-400">"weapon"</span>: <span class="text-yellow-400">"MXOX_Neutronrepeater_S3"</span>,
            <span class="text-blue-400">"vehicle"</span>: <span class="text-yellow-400">"DRAK_Corsair"</span>
        <span class="text-pink-400">}</span></code></pre>
    </section>

    <div class="border-t my-8" style="border-color: rgb(var(--card-border));"></div>

    <section>
        <h3 class="text-xl font-bold mb-4" style="color: rgb(var(--fg));">{{ __('app.response_codes') }}</h3>
        <ul class="list-disc pl-5 space-y-3" style="color: rgb(var(--fg));">
            <li class="mb-1"><strong style="color: rgb(var(--success));"><code>201 Created</code></strong>: {{ __('app.response_201') }}</li>
            <li class="mb-1"><strong style="color: rgb(var(--danger));"><code>401 Unauthorized</code></strong>: {!! __('app.response_401', ['code' => '<code>Authorization</code>']) !!}</li>
            <li class="mb-1"><strong style="color: rgb(var(--warning));"><code>422 Unprocessable Entity</code></strong>: {{ __('app.response_422') }}</li>
            <li class="mb-1"><strong style="color: rgb(var(--muted));"><code>500 Internal Server Error</code></strong>: {{ __('app.response_500') }}</li>
        </ul>
    </section>
</div>
