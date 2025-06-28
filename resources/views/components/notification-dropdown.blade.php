@props(['notifications', 'unreadNotifications'])

<div x-data="{ open: false }" @click.outside="open = false" @close.stop="open = false">
    <div @click="open = ! open">
        <button class="relative inline-flex items-center p-2 text-sm font-medium text-center text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none">
            <i class="fas fa-bell fa-lg"></i>
            <span class="sr-only">Notifications</span>
            @if(isset($unreadNotifications) && $unreadNotifications > 0)
                <div id="notification-count" class="absolute inline-flex items-center justify-center w-6 h-6 text-xs font-bold text-white bg-red-500 border-2 border-white rounded-full -top-2 -end-2 dark:border-gray-900">
                    {{ $unreadNotifications }}
                </div>
            @endif
        </button>
    </div>

    <div x-show="open"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-75"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            class="absolute z-50 mt-2 w-96 rounded-md shadow-lg origin-top-right right-0"
            style="display: none;"
            @click="open = false">
        <div class="rounded-md ring-1 ring-black ring-opacity-5 bg-white dark:bg-gray-800">
            <div class="block px-4 py-2 text-sm font-medium text-center text-gray-700 rounded-t-lg bg-gray-50 dark:bg-gray-700 dark:text-white">
                {{ __('Notifications') }}
            </div>
            <div class="divide-y divide-gray-100 dark:divide-gray-700 max-h-96 overflow-y-auto">
                @forelse($notifications as $notification)
                    <a href="#" class="flex px-4 py-3 hover:bg-gray-100 dark:hover:bg-gray-600 @if(!$notification->read_at) bg-gray-50 dark:bg-gray-900 @endif">
                        <div class="flex-shrink-0">
                            <i class="fas fa-file-alt fa-fw text-blue-500"></i>
                        </div>
                        <div class="w-full ps-3">
                            <div class="text-gray-500 text-sm mb-1.5 dark:text-gray-400">
                                <span class="font-semibold text-gray-900 dark:text-white">{{ $notification->title }}</span>: {{ $notification->message }}
                            </div>
                            <div class="text-xs text-blue-600 dark:text-blue-500">{{ $notification->created_at->diffForHumans() }}</div>
                        </div>
                    </a>
                @empty
                    <div class="px-4 py-3 text-center text-sm text-gray-500 dark:text-gray-400">
                        {{ __('No new notifications') }}
                    </div>
                @endforelse
            </div>
            <a href="#" class="block py-2 text-sm font-medium text-center text-gray-900 rounded-b-lg bg-gray-50 hover:bg-gray-100 dark:bg-gray-800 dark:hover:bg-gray-700 dark:text-white">
                <div class="inline-flex items-center ">
                    <svg class="w-4 h-4 me-2 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 14">
                        <path d="M10 0C4.612 0 0 5.336 0 7c0 1.742 3.546 7 10 7 6.454 0 10-5.258 10-7 0-1.664-4.612-7-10-7Zm0 10a3 3 0 1 1 0-6 3 3 0 0 1 0 6Z"/>
                    </svg>
                    {{ __('View all') }}
                </div>
            </a>
        </div>
    </div>
</div> 