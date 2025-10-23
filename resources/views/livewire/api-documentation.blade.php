<div class="max-w-4xl mx-auto bg-white dark:bg-gray-800 shadow-2xl rounded-xl p-6 sm:p-10 transition-colors duration-300">
    <header class="pb-6 border-b border-gray-200 dark:border-gray-700 mb-8">
        <h1 class="text-3xl sm:text-4xl font-extrabold text-indigo-600 dark:text-indigo-400 mb-2">
            API Documentation
        </h1>
        <p class="text-gray-600 dark:text-gray-400">
            Comprehensive guide to integrating with the killboard API.
        </p>
    </header>

    <section class="mb-10">
        <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-200 mb-4">Submit a New Kill</h2>
        <p class="mb-4 text-gray-700 dark:text-gray-300">This endpoint allows authorized clients to submit details about a kill event to the database.</p>

        <div class="bg-gray-50 dark:bg-gray-900 p-6 rounded-lg shadow-sm mb-4 border border-gray-200 dark:border-gray-700">
            <p class="mb-2"><strong class="font-mono text-gray-900 dark:text-gray-100">Endpoint:</strong> <code class="text-indigo-600 dark:text-indigo-400">/api/v1/kills</code></p>
            <p><strong class="font-mono text-gray-900 dark:text-gray-100">Method:</strong> <span class="text-green-600 dark:text-green-400 font-bold px-2 py-0.5 bg-green-100 dark:bg-green-900/50 rounded-full text-sm">POST</span></p>
        </div>

        <p class="text-gray-700 dark:text-gray-300">
            <strong>Authentication:</strong> Requires a valid Bearer token in the <code class="text-sm bg-gray-200 dark:bg-gray-700 px-1 rounded">Authorization</code> header. The API key can be found on
            <a href="{{ route('profile.edit') }}" class="text-indigo-600 dark:text-indigo-400 hover:underline font-medium">your Profile</a> page after you've verified your account via RSI.
        </p>
    </section>

    <div class="border-t border-gray-200 dark:border-gray-700 my-8"></div>

    <section class="mb-10">
        <h3 class="text-xl font-bold text-gray-800 dark:text-gray-200 mb-4">Request Headers</h3>
        <div class="overflow-x-auto shadow ring-1 ring-black ring-opacity-5 md:rounded-lg">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-100 dark:bg-gray-700">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Header</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Value</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Required</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Description</th>
                </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700 text-gray-700 dark:text-gray-300">
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap"><code>Authorization</code></td>
                    <td class="px-6 py-4 whitespace-nowrap"><code>Bearer &lt;api-key&gt;</code></td>
                    <td class="px-6 py-4 whitespace-nowrap"><span class="text-green-600 dark:text-green-400 font-medium">Yes</span></td>
                    <td class="px-6 py-4">The API key for authentication.</td>
                </tr>
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap"><code>Content-Type</code></td>
                    <td class="px-6 py-4 whitespace-nowrap"><code>application/json</code></td>
                    <td class="px-6 py-4 whitespace-nowrap"><span class="text-green-600 dark:text-green-400 font-medium">Yes</span></td>
                    <td class="px-6 py-4">Specifies the format of the request body.</td>
                </tr>
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap"><code>Accept</code></td>
                    <td class="px-6 py-4 whitespace-nowrap"><code>application/json</code></td>
                    <td class="px-6 py-4 whitespace-nowrap"><span class="text-green-600 dark:text-green-400 font-medium">Yes</span></td>
                    <td class="px-6 py-4">Indicates the client accepts a JSON response.</td>
                </tr>
                </tbody>
            </table>
        </div>
    </section>

    <div class="border-t border-gray-200 dark:border-gray-700 my-8"></div>

    <section class="mb-10">
        <h3 class="text-xl font-bold text-gray-800 dark:text-gray-200 mb-4">Request Body Parameters</h3>
        <div class="overflow-x-auto shadow ring-1 ring-black ring-opacity-5 md:rounded-lg">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-100 dark:bg-gray-700">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Parameter</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Type</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Required</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Validation Rules</th>
                </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700 text-gray-700 dark:text-gray-300">
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
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap"><code>{{ $param[0] }}</code></td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $param[1] }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="{{ $param[2] === 'Yes' ? 'text-green-600 dark:text-green-400 font-medium' : 'text-gray-500 dark:text-gray-400' }}">{{ $param[2] }}</span>
                        </td>
                        <td class="px-6 py-4"><code>{{ $param[3] }}</code></td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        <p class="text-gray-700 dark:text-gray-300 py-4">
            <strong>Please note:</strong> The <code class="text-sm bg-gray-200 dark:bg-gray-700 px-1 rounded">killer</code>, <code class="text-sm bg-gray-200 dark:bg-gray-700 px-1 rounded">victim</code> and <code class="text-sm bg-gray-200 dark:bg-gray-700 px-1 rounded">vehicle</code> fields will undergo additional validation via RSI.
        </p>
    </section>

    <div class="border-t border-gray-200 dark:border-gray-700 my-8"></div>

    <section class="mb-10">
        <h3 class="text-xl font-bold text-gray-800 dark:text-gray-200 mb-4">Example Request Body</h3>
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

    <div class="border-t border-gray-200 dark:border-gray-700 my-8"></div>

    <section>
        <h3 class="text-xl font-bold text-gray-800 dark:text-gray-200 mb-4">Response Codes</h3>
        <ul class="list-disc pl-5 space-y-3 text-gray-700 dark:text-gray-300">
            <li class="mb-1"><strong class="text-green-500"><code>201 Created</code></strong>: The kill event was successfully recorded.</li>
            <li class="mb-1"><strong class="text-red-500"><code>401 Unauthorized</code></strong>: The <code>Authorization</code> header is missing or the API Key is invalid.</li>
            <li class="mb-1"><strong class="text-yellow-500"><code>422 Unprocessable Entity</code></strong>: Validation failed for one or more input parameters. The response body will contain specific error messages.</li>
            <li class="mb-1"><strong class="text-gray-500 dark:text-gray-400"><code>500 Internal Server Error</code></strong>: An unexpected server error occurred.</li>
        </ul>
    </section>
</div>
