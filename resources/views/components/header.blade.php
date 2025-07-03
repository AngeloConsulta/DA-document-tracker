<div class="sticky top-0 left-0 right-0 h-16 bg-gray-900/95 backdrop-blur-md flex items-center px-6 shadow-lg z-30 border-b border-gray-800">
    <form action="{{ route('documents.index') }}" method="GET" class="flex-1 max-w-lg">
        <label for="header-search" class="sr-only">Search</label>
        <div class="relative">
            <input id="header-search" name="search" type="text" placeholder="Search documents..." class="w-full pl-10 pr-4 py-2 rounded-lg bg-gray-800/80 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-gray-700 transition" />
            <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                <i class="fa-solid fa-magnifying-glass text-gray-400"></i>
            </span>
        </div>
    </form>
    <div class="flex items-center space-x-6 ml-6">
        <!-- Notification Bell Dropdown -->
        <div class="relative">
            <button id="notification-bell" class="relative focus:outline-none hover:text-gray-300 transition-colors" onclick="document.getElementById('notification-dropdown').classList.toggle('hidden')">
                <i class="fa-solid fa-bell text-xl text-gray-300"></i>
                <span id="notification-count" class="absolute -top-1 -right-1 inline-flex items-center justify-center px-1.5 py-0.5 text-xs font-bold leading-none text-white bg-red-500 rounded-full" style="display: none;">0</span>
            </button>
            <div id="notification-dropdown" class="hidden absolute right-0 mt-2 w-80 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-lg z-50 overflow-hidden">
                <div class="px-4 py-2 border-b border-gray-200 dark:border-gray-700 font-semibold text-gray-700 dark:text-gray-200">Notifications</div>
                <div id="notifications-list" class="max-h-80 overflow-y-auto divide-y divide-gray-100 dark:divide-gray-700">
                    <div class="px-4 py-3 text-gray-500 dark:text-gray-400 text-sm">No new notifications.</div>
                </div>
            </div>
        </div>
        <!-- User Avatar -->
        <div class="h-10 w-10 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-lg font-bold text-white shadow border-2 border-blue-400">
            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
        </div>
    </div>
</div> 