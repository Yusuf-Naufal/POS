<!-- Navbar -->
<nav class="bg-white border-b border-gray-200 p-4 flex justify-between items-center shadow-md h-16 rounded-md">
    <div class="flex items-center space-x-4">
        <button id="toggle-sidebar" class="p-2 text-gray-500 rounded-lg hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-200">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"/>
            </svg>
        </button>
        <h1 class="text-2xl font-semibold text-center mt-1 text-gray-700">POS</h1>
    </div>
    <div class="flex items-center space-x-4">
        <div class="relative">
            <button class="relative z-10 block p-2 text-gray-600 rounded-full focus:outline-none" >
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-bell-fill" viewBox="0 0 16 16">
                    <path d="M8 16a2 2 0 0 0 2-2H6a2 2 0 0 0 2 2zm.86-14.824A2 2 0 0 0 6 2c-1.105 0-2 .673-2 1.5 0 .26.06.52.182.765a5.57 5.57 0 0 0-.401 2.017c0 2.223-.855 3.735-1.515 4.48C2.085 10.785 2 11 2 11v1h12v-1c0-.307-.09-.489-.266-.741-.67-.74-1.514-2.26-1.514-4.482a5.57 5.57 0 0 0-.401-2.017c.122-.245.182-.504.182-.765C10 2.673 9.105 2 8 2c-.858 0-1.57.403-1.86 1.176z"/>
                </svg>
            </button>
            <div class="absolute right-0 top-10 w-48 bg-white rounded-lg shadow-lg p-2 hidden" id="notifications">
                <!-- Add notification items here -->
            </div>
        </div>
        <div class="relative">
            <button class="relative z-10 block p-2 text-gray-600 rounded-full focus:outline-none" aria-expanded="false" data-dropdown-toggle="user-menu">
                <img src="{{ asset('assets/' . auth()->user()->foto ) }}" alt="User" class="rounded-full h-10 w-10">
            </button>
            <div class="z-50 hidden min-w-max text-base list-none bg-white divide-y divide-gray-100 rounded shadow dark:bg-gray-700 dark:divide-gray-600" id="user-menu">
                <div class="px-4 pt-3" role="none">
                    <p class="text-sm text-gray-900 dark:text-white" role="none">
                    <b>{{ auth()->user()->nama }}</b>
                    </p>
                    <p class="text-sm font-medium text-gray-900 truncate dark:text-gray-300" role="none">
                    {{ auth()->user()->email }}
                    </p>
                </div>
                <ul class="py-1" role="none">
                    @if(Auth::user()->role == 'Master')
                        <a href="{{ route('master.dashboard') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-600 dark:hover:text-white" role="menuitem">
                            Dashboard
                        </a>
                    @endif
                    <li>
                    <a href="{{ route('users.menu') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-600 dark:hover:text-white" role="menuitem">Ganti Outlet</a>
                    </li>
                    <li>
                    <a id="logout-button" href="{{ route('logout') }}" class="block px-4 py-2 text-sm text-red-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-600 dark:hover:text-white" role="menuitem">Sign out</a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</nav>