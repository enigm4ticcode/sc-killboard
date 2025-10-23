<footer class="bg-gray-800 text-white w-full p-4">
    <div class="w-full mx-auto flex flex-col md:flex-row items-center justify-between">
        <div class="flex items-center space-x-4 mb-4 md:mb-0">
            <img src="{{ asset('img/logos/made_by_the_community.png') }}" alt="{{ __('Made by the Star Citizen Community') }}" class="w-12 h-auto rounded-full object-cover">
            <p class="text-sm px-2">
                Brought to you by <a href="https://robertsspaceindustries.com/citizens/ENIGM4" target="_blank">ENIGM4</a>
            </p>
        </div>
        <div class="flex space-x-8">
            <a href="{{ route('legal') }}#legal-disclaimer" class="text-sm hover:text-gray-300 transition duration-150">
                {{ __('Legal Disclaimer') }}
            </a>
            <a href="{{ route('legal') }}#cookie-policy" class="text-sm hover:text-gray-300 transition duration-150">
                {{ __('Cookie Policy') }}
            </a>
            <a href="{{ route('legal') }}#privacy-policy" class="text-sm hover:text-gray-300 transition duration-150">
                {{ __('Privacy Policy') }}
            </a>
            <a href="{{ route('api-documentation') }}" class="text-sm hover:text-gray-300 transition duration-150">
                {{ __('API Documentation') }}
            </a>
        </div>
    </div>
</footer>
