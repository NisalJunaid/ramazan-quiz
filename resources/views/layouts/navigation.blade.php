<nav x-data="{ open: false }" class="bg-white border-b border-gray-200/70 shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex h-16 items-center justify-between">

            <!-- Left side -->
            <div class="flex items-center gap-8">

                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('home') }}">
                        <x-application-logo class="block h-9 w-auto fill-current text-emerald-700" :theme-settings="$themeSettings ?? null" />
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden items-center gap-2 sm:flex">

                    <!-- Home -->
                    <x-nav-link :href="route('home')" :active="request()->routeIs('home')">
                        {{ text('navigation.home', 'Home') }}
                    </x-nav-link>

                    <!-- Leaderboard -->
                    @if ($canViewLeaderboard)
                        <x-nav-link :href="route('leaderboard')" :active="request()->routeIs('leaderboard')">
                            {{ text('navigation.leaderboard', 'Leaderboard') }}
                        </x-nav-link>
                    @endif

                    <!-- Admin (ONLY if admin) -->
                    @auth
                        @if(Auth::user()->role === 'admin')
                            <x-nav-link :href="route('admin.index')" :active="request()->routeIs('admin.*')">
                                {{ text('navigation.admin', 'Admin') }}
                            </x-nav-link>
                        @endif
                    @endauth

                </div>
            </div>

            <!-- Right side -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                @auth
                    <!-- User Dropdown -->
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button
                                class="inline-flex items-center rounded-md border border-transparent bg-white px-3 py-2 text-sm font-medium leading-4 text-gray-600 hover:text-gray-800 focus:outline-none transition ease-in-out duration-150">
                                <div>{{ Auth::user()->name }}</div>

                                <div class="ms-1">
                                    <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg"
                                         viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                              d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                              clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <x-dropdown-link :href="route('profile.edit')">
                                {{ text('navigation.profile', 'Profile') }}
                            </x-dropdown-link>

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault(); this.closest('form').submit();">
                                    {{ text('navigation.logout', 'Log Out') }}
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                @else
                    <div class="flex items-center gap-3">
                        <x-nav-link :href="route('login')" :active="request()->routeIs('login')">
                            {{ text('navigation.login', 'Login') }}
                        </x-nav-link>
                        <a
                            href="{{ route('register') }}"
                            class="inline-flex items-center px-4 py-2 bg-emerald-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-emerald-700 focus:bg-emerald-700 active:bg-emerald-800 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            {{ text('navigation.register', 'Register') }}
                        </a>
                    </div>
                @endauth
            </div>

            <!-- Mobile Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open"
                        class="inline-flex items-center justify-center p-2 rounded-md
                               text-gray-500 hover:text-gray-700 hover:bg-emerald-50">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }"
                              class="inline-flex" stroke-linecap="round"
                              stroke-linejoin="round" stroke-width="2"
                              d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }"
                              class="hidden" stroke-linecap="round"
                              stroke-linejoin="round" stroke-width="2"
                              d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">

            <x-responsive-nav-link :href="route('home')" :active="request()->routeIs('home')">
                {{ text('navigation.home', 'Home') }}
            </x-responsive-nav-link>

            @if ($canViewLeaderboard)
                <x-responsive-nav-link :href="route('leaderboard')" :active="request()->routeIs('leaderboard')">
                    {{ text('navigation.leaderboard', 'Leaderboard') }}
                </x-responsive-nav-link>
            @endif

            @auth
                @if(Auth::user()->role === 'admin')
                    <x-responsive-nav-link :href="route('admin.index')" :active="request()->routeIs('admin.*')">
                        {{ text('navigation.admin', 'Admin') }}
                    </x-responsive-nav-link>
                @endif
            @endauth

        </div>

        <div class="pt-4 pb-1 border-t border-gray-200">
            @auth
                <div class="px-4">
                    <div class="font-medium text-base text-gray-800">
                        {{ Auth::user()->name }}
                    </div>
                    <div class="font-medium text-sm text-gray-500">
                        {{ Auth::user()->email }}
                    </div>
                </div>

                <div class="mt-3 space-y-1">
                    <x-responsive-nav-link :href="route('profile.edit')">
                        {{ text('navigation.profile', 'Profile') }}
                    </x-responsive-nav-link>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault(); this.closest('form').submit();">
                            {{ text('navigation.logout', 'Log Out') }}
                        </x-responsive-nav-link>
                    </form>
                </div>
            @else
                <div class="mt-3 space-y-1">
                    <x-responsive-nav-link :href="route('login')">
                        {{ text('navigation.login', 'Login') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('register')">
                        {{ text('navigation.register', 'Register') }}
                    </x-responsive-nav-link>
                </div>
            @endauth
        </div>
    </div>
</nav>
