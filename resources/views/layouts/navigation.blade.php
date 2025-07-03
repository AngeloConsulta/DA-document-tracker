<aside class="flex flex-col w-64 h-screen bg-gray-900 border-r border-gray-800 fixed">
    <div class="flex items-center h-16 px-6 border-b border-gray-800">
        <a href="{{ route('dashboard') }}" class="flex items-center space-x-2">
            <x-application-logo class="block h-9 w-auto fill-current text-white" />
            <span class="text-lg font-bold text-white tracking-wide">DARFO5-DTS</span>
        </a>
    </div>
    <nav class="flex-1 px-4 py-6 space-y-2 overflow-y-auto">
        <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" class="flex items-center space-x-3 px-3 py-2 rounded-md text-white hover:bg-gray-800 transition">
            <i class="fa-solid fa-gauge-high w-5"></i>
            <span>{{ __('Dashboard') }}</span>
        </x-nav-link>
        <x-nav-link :href="route('scanner.index')" :active="request()->routeIs('scanner.index')" class="flex items-center space-x-3 px-3 py-2 rounded-md text-white hover:bg-gray-800 transition">
            <i class="fa-solid fa-qrcode w-5"></i>
            <span>{{ __('QR Scanner') }}</span>
        </x-nav-link>
        <div x-data="{ openDocuments: {{ request()->routeIs('documents.*') ? 'true' : 'false' }} }">
            <button
                @click="openDocuments = !openDocuments"
                class="flex items-center w-full space-x-3 px-3 py-2 rounded-md text-white hover:bg-gray-800 transition focus:outline-none"
                :class="{ 'bg-gray-800': openDocuments }"
                type="button"
            >
                <i class="fa-solid fa-folder-open w-5"></i>
                <span class="text-xs font-semibold uppercase">{{ __('Documents') }}</span>
                <svg :class="{ 'rotate-90': openDocuments }" class="w-3 h-3 ml-auto transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </button>
            <div x-show="openDocuments" class="space-y-1 ml-2" x-cloak>
                <x-nav-link :href="route('documents.incoming.index')" :active="request()->routeIs('documents.incoming.*')" class="flex items-center space-x-3 px-3 py-2 rounded-md text-white hover:bg-gray-800 transition">
                    <i class="fa-solid fa-inbox w-5"></i>
                    <span>{{ __('Incoming Documents') }}</span>
                </x-nav-link>
                <x-nav-link :href="route('documents.outgoing.index')" :active="request()->routeIs('documents.outgoing.*')" class="flex items-center space-x-3 px-3 py-2 rounded-md text-white hover:bg-gray-800 transition">
                    <i class="fa-solid fa-paper-plane w-5"></i>
                    <span>{{ __('Outgoing Documents') }}</span>
                </x-nav-link>
                <x-nav-link :href="route('documents.index')" :active="request()->routeIs('documents.index')" class="flex items-center space-x-3 px-3 py-2 rounded-md text-white hover:bg-gray-800 transition">
                    <i class="fa-solid fa-folder-open w-5"></i>
                    <span>{{ __('All Documents') }}</span>
                </x-nav-link>
            </div>
        </div>
        @if(auth()->user()->isSuperadmin())
            <x-nav-link :href="route('users.index')" :active="request()->routeIs('users.index')" class="flex items-center space-x-3 px-3 py-2 rounded-md text-white hover:bg-gray-800 transition">
                <i class="fa-solid fa-users-gear w-5"></i>
                <span>{{ __('User Management') }}</span>
            </x-nav-link>
        @endif
        @if(auth()->user()->hasPermission('departments.view'))
            <x-nav-link :href="route('departments.index')" :active="request()->routeIs('departments.*')" class="flex items-center space-x-3 px-3 py-2 rounded-md text-white hover:bg-gray-800 transition">
                <i class="fa-solid fa-building w-5"></i>
                <span>{{ __('Departments') }}</span>
            </x-nav-link>
        @endif
        @if(auth()->user()->hasPermission('document_statuses.view'))
            <x-nav-link :href="route('document-statuses.index')" :active="request()->routeIs('document-statuses.*')" class="flex items-center space-x-3 px-3 py-2 rounded-md text-white hover:bg-gray-800 transition">
                <i class="fa-solid fa-clipboard-list w-5"></i>
                <span>{{ __('Document Statuses') }}</span>
            </x-nav-link>
        @endif
        @if(auth()->user()->hasPermission('document_types.view'))
            <x-nav-link :href="route('document-types.index')" :active="request()->routeIs('document_types.*')" class="flex items-center space-x-3 px-3 py-2 rounded-md text-white hover:bg-gray-800 transition">
                <i class="fa-solid fa-file-lines w-5"></i>
                <span>{{ __('Document Types') }}</span>
            </x-nav-link>
        @endif
    </nav>
    <div class="px-6 py-4 border-t border-gray-800 mt-auto">
        <a href="{{ route('profile.edit') }}" class="profile-card block group rounded-lg p-3 bg-gray-800 hover:bg-gray-700 transition mb-2">
            <div class="flex items-center space-x-3">
                <div class="h-12 w-12 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-xl font-bold text-white shadow">
                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                </div>
                <div>
                    <div class="font-semibold text-white group-hover:text-blue-300">{{ Auth::user()->name }}</div>
                    <div class="text-xs text-gray-400 group-hover:text-gray-200">{{ Auth::user()->email }}</div>
                </div>
            </div>
        </a>
        <div class="space-y-1">
            <x-nav-link :href="route('profile.edit')" class="flex items-center space-x-2 text-gray-300 hover:text-white">
                <i class="fa-solid fa-user-cog w-4"></i>
                <span>{{ __('Profile') }}</span>
            </x-nav-link>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <x-nav-link :href="route('logout')" class="flex items-center space-x-2 text-gray-300 hover:text-white"
                        onclick="event.preventDefault(); this.closest('form').submit();">
                    <i class="fa-solid fa-right-from-bracket w-4"></i>
                    <span>{{ __('Log Out') }}</span>
                </x-nav-link>
            </form>
        </div>
    </div>
</aside>
