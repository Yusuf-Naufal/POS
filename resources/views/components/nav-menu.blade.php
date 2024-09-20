<nav class="bg-purple-600 border-b border-purple-700 dark:bg-purple-800 dark:border-purple-900 fixed w-full -mt-1 z-50">
    <div class="max-w-screen-xl mx-auto flex flex-wrap items-center justify-between p-4">
        <a href="{{ route('users.menu') }}" class="flex items-center space-x-3 rtl:space-x-reverse">
            <img src="{{ asset('assets/logo.png') }}" class="h-8" alt="POS Logo" />
            <span class="self-center text-2xl font-semibold text-white">POS</span>
        </a>
        <div class="flex items-center space-x-3 md:space-x-6 rtl:space-x-reverse">
            <!-- User Profile Button -->
            <button type="button" class="relative flex items-center text-sm bg-purple-700 rounded-full focus:ring-4 focus:ring-purple-300 dark:focus:ring-purple-500" id="user-menu-button" aria-expanded="false" data-dropdown-toggle="user-dropdown" data-dropdown-placement="bottom">
                <span class="sr-only">Open user menu</span>
                <img class="w-8 h-8 rounded-full ring-2 ring-white dark:ring-purple-700" src="{{ asset('storage/assets/' . auth()->user()->foto ) }}" alt="User photo">
            </button>
            <!-- Dropdown menu -->
            <div class="z-50 hidden my-4 text-base list-none bg-white divide-y divide-purple-100 rounded-lg shadow-lg dark:bg-purple-700 dark:divide-purple-600" id="user-dropdown">
                <div class="px-4 py-3">
                    <span class="block text-sm font-medium text-gray-900 dark:text-white">{{ auth()->user()->nama }}</span>
                    <span class="block text-sm text-gray-500 truncate dark:text-gray-300">{{ auth()->user()->email }}</span>
                </div>
                <ul class="py-2" aria-labelledby="user-menu-button">
                    <li>
                        <a href="{{ route('logout') }}" class="block px-4 py-2 text-sm text-red-700 hover:bg-purple-100 dark:hover:bg-purple-600 dark:text-gray-200 dark:hover:text-white">Sign out</a>
                    </li>
                </ul>
            </div>
            <!-- Mobile menu button -->
            <button data-collapse-toggle="navbar-user" type="button" class="inline-flex items-center p-2 w-10 h-10 justify-center text-sm text-purple-200 rounded-lg md:hidden hover:bg-purple-500 focus:outline-none focus:ring-2 focus:ring-purple-300 dark:text-purple-400 dark:hover:bg-purple-700 dark:focus:ring-purple-600" aria-controls="navbar-user" aria-expanded="false">
                <span class="sr-only">Open main menu</span>
                <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 17 14" aria-hidden="true">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 1h15M1 7h15M1 13h15"/>
                </svg>
            </button>
        </div>
        <div class="hidden w-full md:flex md:w-auto md:order-1" id="navbar-user">
            <ul class="flex flex-col p-4 mt-4 bg-purple-50 border border-purple-200 rounded-lg md:flex-row md:space-x-8 md:mt-0 md:bg-transparent md:border-0 dark:bg-purple-800 md:dark:bg-transparent dark:border-purple-600">
                <li>
                    <a href="#" class="block py-2 px-3 text-white bg-purple-700 rounded md:bg-transparent md:text-purple-200 md:p-0 dark:text-white md:dark:text-purple-300" aria-current="page">Menu</a>
                </li>
            </ul>
        </div>
    </div>
</nav>
