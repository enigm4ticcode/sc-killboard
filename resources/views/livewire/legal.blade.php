<div id="legal-info-wrapper" class="card p-4 sm:p-6 lg:p-8 mx-4 sm:mx-0" style="color: rgb(var(--fg));">
    <header class="pb-6 mb-6 border-b" style="border-color: rgb(var(--card-border));">
        <h1 class="text-3xl sm:text-4xl font-extrabold mb-2" style="color: rgb(var(--accent));">{{ __('app.legal_information') }}</h1>
        <p class="text-base" style="color: rgb(var(--muted));">{{ __('app.last_updated', ['date' => date('F j, Y')]) }}</p>
    </header>

    <section id="privacy-policy" class="space-y-8">
        <h2 class="text-3xl font-bold border-b-2 pb-2" style="color: rgb(var(--fg)); border-color: rgb(var(--accent));">1. {{ __('app.privacy_policy') }}</h2>
        <p class="text-lg">
            {{ __('app.privacy_policy_intro') }}
        </p>

        <h3 class="text-xl font-semibold mt-6 mb-3" style="color: rgb(var(--accent));">1.1. {{ __('app.data_controller') }}</h3>
        <p>{{ __('app.data_controller_text') }}</p>
        <ul class="list-disc list-inside ml-4 space-y-1">
            <li>**Name/Company:** Star Citizen Killboard</li>
            <li>**Contact Email for Privacy:** zaedra.eve [at] gmail.com</li>
        </ul>

        <h3 class="text-xl font-semibold mt-6 mb-3" style="color: rgb(var(--accent));">1.2. {{ __('app.data_collected_and_purpose') }}</h3>
        <p>{{ __('app.data_collected_intro') }}</p>

        <div class="overflow-x-auto rounded-lg shadow-md">
            <table class="min-w-full divide-y" style="divide-color: rgb(var(--card-border));">
                <thead style="background-color: rgb(var(--table-header));">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider" style="color: rgb(var(--muted));">{{ __('app.data_category') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider" style="color: rgb(var(--muted));">{{ __('app.purpose') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider" style="color: rgb(var(--muted));">{{ __('app.legal_basis') }}</th>
                </tr>
                </thead>
                <tbody class="divide-y" style="background-color: rgb(var(--card)); divide-color: rgb(var(--card-border));">
                <tr class="transition-colors" onmouseover="this.style.backgroundColor='rgb(var(--table-hover))'" onmouseout="this.style.backgroundColor='rgb(var(--card))'">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium" style="color: rgb(var(--fg));">{{ __('app.discord_nickname') }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm" style="color: rgb(var(--muted));">{{ __('app.identification_purpose') }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm" style="color: rgb(var(--muted));">{{ __('app.contract_legitimate_interest') }}</td>
                </tr>
                <tr class="transition-colors" onmouseover="this.style.backgroundColor='rgb(var(--table-hover))'" onmouseout="this.style.backgroundColor='rgb(var(--card))'">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium" style="color: rgb(var(--fg));">{{ __('app.discord_global_name') }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm" style="color: rgb(var(--muted));">{{ __('app.identification_purpose') }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm" style="color: rgb(var(--muted));">{{ __('app.contract_legitimate_interest') }}</td>
                </tr>
                <tr class="transition-colors" onmouseover="this.style.backgroundColor='rgb(var(--table-hover))'" onmouseout="this.style.backgroundColor='rgb(var(--card))'">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium" style="color: rgb(var(--fg));">{{ __('app.email_address') }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm" style="color: rgb(var(--muted));">{{ __('app.email_purpose') }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm" style="color: rgb(var(--muted));">{{ __('app.contract_providing_service') }}</td>
                </tr>
                </tbody>
            </table>
        </div>
        <p class="pt-2 text-sm italic" style="color: rgb(var(--muted));">
            {{ __('app.no_sensitive_data_note') }}
        </p>

        <h3 class="text-xl font-semibold mt-8 mb-3" style="color: rgb(var(--accent));">1.3. {{ __('app.data_retention_security') }}</h3>
        <p>
            {{ __('app.data_retention_text') }}
        </p>

        <h3 class="text-xl font-semibold mt-8 mb-3" style="color: rgb(var(--accent));">1.4. {{ __('app.your_data_rights') }}</h3>
        <p>
            {{ __('app.data_rights_text') }}
        </p>
    </section>

    <hr class="my-10 border-t-2" style="border-color: rgb(var(--accent));">

    <section id="cookie-policy" class="space-y-8">
        <h2 class="text-3xl font-bold border-b-2 pb-2" style="color: rgb(var(--fg)); border-color: rgb(var(--accent));">2. {{ __('app.cookie_policy') }}</h2>

        <p class="text-lg">
            {{ __('app.cookie_policy_intro') }}
        </p>

        <h3 class="text-xl font-semibold mt-6 mb-3" style="color: rgb(var(--accent));">2.1. {{ __('app.what_are_cookies') }}</h3>
        <p>
            {{ __('app.what_are_cookies_text') }}
        </p>

        <h3 class="text-xl font-semibold mt-6 mb-3" style="color: rgb(var(--accent));">2.2. {{ __('app.cookies_we_use') }}</h3>
        <p>{{ __('app.cookies_we_use_intro') }}</p>
        <ul class="list-disc list-inside ml-4 space-y-1">
            <li>{{ __('app.session_cookies') }}</li>
            <li>{{ __('app.preference_cookies') }}</li>
            <li>{{ __('app.security_cookies') }}</li>
        </ul>

        <h3 class="text-xl font-semibold mt-6 mb-3" style="color: rgb(var(--accent));">2.3. {{ __('app.compliance_note') }}</h3>
        <p>
            {{ __('app.compliance_note_text') }}
        </p>
        <p class="mt-4 p-3 border-l-4 rounded-md text-sm" style="background-color: rgb(var(--danger)); border-color: rgb(var(--danger)); color: rgb(var(--accent-fg));">
            {{ __('app.compliance_important') }}
        </p>

        <h3 class="text-xl font-semibold mt-6 mb-3" style="color: rgb(var(--accent));">2.4. {{ __('app.managing_cookies') }}</h3>
        <p>
            {{ __('app.managing_cookies_text') }}
        </p>
    </section>

    <hr class="my-10 border-t-2" style="border-color: rgb(var(--accent));">

    <section id="legal-disclaimer" class="space-y-8">
        <h2 class="text-3xl font-bold border-b-2 pb-2" style="color: rgb(var(--fg)); border-color: rgb(var(--accent));">3. {{ __('app.legal_disclaimer') }}</h2>

        <div class="p-6 border-l-4 rounded-lg" style="background-color: rgb(var(--warning)); border-color: rgb(var(--warning)); color: rgb(var(--accent-fg));">
            <h3 class="text-2xl font-bold mb-4">{{ __('app.official_notice') }}</h3>

            <p class="leading-relaxed">
                {{ __('app.fansite_notice_1') }}
            </p>

            <p class="leading-relaxed mt-3">
                {{ __('app.fansite_notice_2') }}
            </p>

            <p class="leading-relaxed mt-3">
                {{ __('app.fansite_notice_3') }}
            </p>

            <p class="leading-relaxed mt-3">
                {{ __('app.fansite_notice_4') }}
            </p>

            <p class="leading-relaxed mt-3">
                {{ __('app.fansite_notice_5') }}
            </p>
        </div>
    </section>
</div>
