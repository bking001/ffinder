<nav x-data="{ open: false }" class="bg-gray-200 dark:bg-slate-800  shadow-lg  border-gray-100 dark:border-gray-700">


    <!-- Primary Navigation Menu  border-indigo-400 -->
    <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8r">
        <div class="mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between 2xl:justify-center items-center h-14">
                
                    <!-- Logo -->
                    <div class="shrink-0 flex" style="float: left;">
                        <a href="{{ route('dashboard') }}">
                            <x-application-logo
                                class="block h-9 w-auto fill-indigo-400 text-gray-800 dark:text-gray-200" />
                        </a>
                    </div>

                    <!-- Navigation Links -->
                    <div class="hidden 2xl:flex space-x-4 sm:-my-px sm:ms-10">
                        <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" style="font-size:12px;">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 16 20"
                                stroke-width="1.5" stroke="currentColor" class="w-5 h-5 mr-1">
                                <path
                                    d="M6.5 14.5v-3.505c0-.245.25-.495.5-.495h2c.25 0 .5.25.5.5v3.5a.5.5 0 0 0 .5.5h4a.5.5 0 0 0 .5-.5v-7a.5.5 0 0 0-.146-.354L13 5.793V2.5a.5.5 0 0 0-.5-.5h-1a.5.5 0 0 0-.5.5v1.293L8.354 1.146a.5.5 0 0 0-.708 0l-6 6A.5.5 0 0 0 1.5 7.5v7a.5.5 0 0 0 .5.5h4a.5.5 0 0 0 .5-.5" />
                            </svg>
                            <span>{{ __('Clients') }}</span>
                        </x-nav-link>
                    </div>

                    <div class="hidden 2xl:flex space-x-4 sm:-my-px sm:ms-10">
                        <x-nav-link :href="route('OLT')" :active="request()->routeIs('OLT')" style="font-size:12px;">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke-width="1.5" stroke="currentColor" class="w-5 h-5 mr-1">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M9 3.75H6.912a2.25 2.25 0 0 0-2.15 1.588L2.35 13.177a2.25 2.25 0 0 0-.1.661V18a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18v-4.162c0-.224-.034-.447-.1-.661L19.24 5.338a2.25 2.25 0 0 0-2.15-1.588H15M2.25 13.5h3.86a2.25 2.25 0 0 1 2.012 1.244l.256.512a2.25 2.25 0 0 0 2.013 1.244h3.218a2.25 2.25 0 0 0 2.013-1.244l.256-.512a2.25 2.25 0 0 1 2.013-1.244h3.859M12 3v8.25m0 0-3-3m3 3 3-3" />
                            </svg>
                            {{ __('OLT') }}
                        </x-nav-link>
                    </div>

                    <div class="hidden 2xl:flex space-x-4 sm:-my-px sm:ms-10">
                        <x-nav-link :href="route('GlobalSearch')" :active="request()->routeIs('GlobalSearch')" style="font-size:12px;">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke-width="1.5" stroke="currentColor" class="w-5 h-5 mr-1">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                            </svg>
                            {{ __('Search') }}
                        </x-nav-link>
                    </div>

                    @if (!empty($PrivData) && $PrivData['Install'] == '1')
                        <div class="hidden 2xl:flex space-x-4 sm:-my-px sm:ms-10">
                            <x-nav-link :href="route('Options')" :active="request()->routeIs('Options')" style="font-size:12px;">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="w-5 h-5 mr-1">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M13.19 8.688a4.5 4.5 0 0 1 1.242 7.244l-4.5 4.5a4.5 4.5 0 0 1-6.364-6.364l1.757-1.757m13.35-.622 1.757-1.757a4.5 4.5 0 0 0-6.364-6.364l-4.5 4.5a4.5 4.5 0 0 0 1.242 7.244" />
                                </svg>
                                {{ __('Install') }}
                            </x-nav-link>
                        </div>
                    @endif

                    @if (!empty($PrivData) && $PrivData['Install'] == '1')
                        <div class="hidden 2xl:flex space-x-4 sm:-my-px sm:ms-10">
                            <x-nav-link :href="route('duplicatedGet')" :active="request()->routeIs('duplicatedGet')" class="whitespace-nowrap"
                                style="font-size:12px;">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="w-5 h-5 mr-1">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                                </svg>
                                {{ __('Clones') }}
                                @if (!empty($count))
                                    <span
                                        class="inline-flex ml-2 items-center rounded-md bg-indigo-400 dark:bg-indigo-400/20 px-2 py-1 text-xs font-medium text-indigo-400 ring-1 ring-inset ring-indigo-600"
                                        style="background:rgba(17, 24, 39, 1)">
                                        {{ $count }}</span>
                                @endif
                            </x-nav-link>
                        </div>
                    @endif

                    @if (!empty($PrivData) && $PrivData['Install'] == '1')
                        <div class="hidden 2xl:flex space-x-4 sm:-my-px sm:ms-10">
                            <x-nav-link :href="route('naOntGet')" :active="request()->routeIs('naOntGet')" class="whitespace-nowrap"
                                style="font-size:12px;">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="w-5 h-5 mr-1">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M6.429 9.75 2.25 12l4.179 2.25m0-4.5 5.571 3 5.571-3m-11.142 0L2.25 7.5 12 2.25l9.75 5.25-4.179 2.25m0 0L21.75 12l-4.179 2.25m0 0 4.179 2.25L12 21.75 2.25 16.5l4.179-2.25m11.142 0-5.571 3-5.571-3" />
                                </svg>
                                {{ __('N/A') }}
                                @if (!empty($NAcount))
                                    <span
                                        class="inline-flex ml-2 items-center rounded-md bg-indigo-400 dark:bg-indigo-400/20 px-2 py-1 text-xs font-medium text-indigo-400 ring-1 ring-inset ring-indigo-600"
                                        style="background:rgba(17, 24, 39, 1)">
                                        {{ $NAcount }}</span>
                                @endif
                            </x-nav-link>
                        </div>
                    @endif


                    <div class="hidden 2xl:flex space-x-4 sm:-my-px sm:ms-10">
                        <x-nav-link :href="route('airsoft.Monitoring_View')" :active="request()->routeIs('airsoft.Monitoring_View')" style="font-size:12px;">

                            @if (!empty($FixedTask))
                                <x-bladewind::bell size="small" color="red" animate_dot="true" />
                            @else
                                <x-bladewind::bell size="small" show_dot="false" />
                            @endif

                            {{ __('Tasks') }}
                        </x-nav-link>
                    </div>



                    <div class="hidden 2xl:flex space-x-4 sm:-my-px sm:ms-10">
                        <x-nav-link :href="route('PonStats')" :active="request()->routeIs('PonStats')" class="whitespace-nowrap"
                            style="font-size:12px;">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke-width="1.5" stroke="currentColor" class="w-5 h-5 mr-1">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 0 1 3 19.875v-6.75ZM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V8.625ZM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125Z" />
                            </svg>
                            {{ __('Pons') }}
                        </x-nav-link>
                    </div>


                    <div class="hidden 2xl:flex space-x-4 sm:-my-px sm:ms-10">
                        <x-nav-link :href="route('OnuStats')" :active="request()->routeIs('OnuStats')" class="whitespace-nowrap"
                            style="font-size:12px;">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke-width="1.5" stroke="currentColor" class="w-5 h-5 mr-1">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M7.5 14.25v2.25m3-4.5v4.5m3-6.75v6.75m3-9v9M6 20.25h12A2.25 2.25 0 0 0 20.25 18V6A2.25 2.25 0 0 0 18 3.75H6A2.25 2.25 0 0 0 3.75 6v12A2.25 2.25 0 0 0 6 20.25Z" />
                            </svg>
                            {{ __('Stats') }}
                        </x-nav-link>
                    </div>


                    <div class="hidden 2xl:flex space-x-4 sm:-my-px sm:ms-10">
                        <x-nav-link :href="route('bug-data')" :active="request()->routeIs('bug-data')" style="font-size:12px;">
                            @if (!empty($switch) && $switch == '1')
                                <x-bladewind::bell size="small" color="purple" animate_dot="true" />
                            @else
                                <x-bladewind::bell size="small" show_dot="false" />
                            @endif
                            {{ __('Bug Report') }}
                        </x-nav-link>
                    </div>

                    @if (!empty($PrivData) && $PrivData['Priv_Log'] == '1')
                        <div class="hidden 2xl:flex space-x-4 sm:-my-px sm:ms-10">
                            <x-nav-link :href="url('/log-viewer')" :active="request()->routeIs('log-viewer')" style="font-size:12px;">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="w-5 h-5 mr-1">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M12 21a9.004 9.004 0 0 0 8.716-6.747M12 21a9.004 9.004 0 0 1-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 0 1 7.843 4.582M12 3a8.997 8.997 0 0 0-7.843 4.582m15.686 0A11.953 11.953 0 0 1 12 10.5c-2.998 0-5.74-1.1-7.843-2.918m15.686 0A8.959 8.959 0 0 1 21 12c0 .778-.099 1.533-.284 2.253m0 0A17.919 17.919 0 0 1 12 16.5c-3.162 0-6.133-.815-8.716-2.247m0 0A9.015 9.015 0 0 1 3 12c0-1.605.42-3.113 1.157-4.418" />
                                </svg>
                                {{ __('Logs') }}
                            </x-nav-link>
                        </div>
                    @endif



                    @if (!empty($PrivData) && $PrivData['Devices'] == '1')
                        <div x-data="{ open: false }"
                            class="hidden  cursor-pointer sm:items-center space-x-4 sm:-my-px sm:ms-10 2xl:flex">
                            <div class="relative">
                                <x-nav-link @click="open = !open" style="font-size:12px;">
                                    {{ __('More') }}
                                    <div class="ms-1">
                                        <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg"
                                            viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                </x-nav-link>

                                <!-- Dropdown Menu -->
                                <div x-show="open" @click.away="open = false"
                                    class="absolute left-0 z-10 mt-2 w-48 bg-white  border border-slate-700 dark:bg-gray-700 shadow-lg rounded-md overflow-hidden"
                                    style="z-index:1001;background: #141c3d;display: none;">

                                    @if (!empty($PrivData) && $PrivData['Devices'] == '1')
                                        <a href="{{ route('Devices') }}"
                                            class="flex items-center block px-4 py-2 text-sm text-gray-800 dark:text-gray-400 dark:hover:text-gray-200 dark:hover:bg-slate-900">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                                class="w-5 h-5 mr-1">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M21.75 17.25v-.228a4.5 4.5 0 0 0-.12-1.03l-2.268-9.64a3.375 3.375 0 0 0-3.285-2.602H7.923a3.375 3.375 0 0 0-3.285 2.602l-2.268 9.64a4.5 4.5 0 0 0-.12 1.03v.228m19.5 0a3 3 0 0 1-3 3H5.25a3 3 0 0 1-3-3m19.5 0a3 3 0 0 0-3-3H5.25a3 3 0 0 0-3 3m16.5 0h.008v.008h-.008v-.008Zm-3 0h.008v.008h-.008v-.008Z" />
                                            </svg>
                                            {{ __('Devices') }}
                                        </a>

                                        <a href="{{ route('mast_table') }}"
                                            class="flex items-center block px-4 py-2 text-sm text-gray-800 dark:text-gray-400 dark:hover:text-gray-200 dark:hover:bg-slate-900">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                                class="w-5 h-5 mr-1">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M4.5 12a7.5 7.5 0 0 0 15 0m-15 0a7.5 7.5 0 1 1 15 0m-15 0H3m16.5 0H21m-1.5 0H12m-8.457 3.077 1.41-.513m14.095-5.13 1.41-.513M5.106 17.785l1.15-.964m11.49-9.642 1.149-.964M7.501 19.795l.75-1.3m7.5-12.99.75-1.3m-6.063 16.658.26-1.477m2.605-14.772.26-1.477m0 17.726-.26-1.477M10.698 4.614l-.26-1.477M16.5 19.794l-.75-1.299M7.5 4.205 12 12m6.894 5.785-1.149-.964M6.256 7.178l-1.15-.964m15.352 8.864-1.41-.513M4.954 9.435l-1.41-.514M12.002 12l-3.75 6.495" />
                                            </svg>
                                            {{ __('Masts') }}
                                        </a>
                                    @endif

                                    @if (!empty($PrivData) && $PrivData['admin'] == '1')
                                        <a href="{{ route('Privilege.logs') }}"
                                            class="flex items-center block px-4 py-2 text-sm text-gray-800 dark:text-gray-400 dark:hover:text-gray-200 dark:hover:bg-slate-900">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                                class="w-5 h-5 mr-1">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M7.864 4.243A7.5 7.5 0 0 1 19.5 10.5c0 2.92-.556 5.709-1.568 8.268M5.742 6.364A7.465 7.465 0 0 0 4.5 10.5a7.464 7.464 0 0 1-1.15 3.993m1.989 3.559A11.209 11.209 0 0 0 8.25 10.5a3.75 3.75 0 1 1 7.5 0c0 .527-.021 1.049-.064 1.565M12 10.5a14.94 14.94 0 0 1-3.6 9.75m6.633-4.596a18.666 18.666 0 0 1-2.485 5.33" />
                                            </svg>
                                            {{ __('Privileges') }}
                                        </a>


                                        <a href="{{ route('schedule') }}"
                                            class="flex items-center block px-4 py-2 text-sm text-gray-800 dark:text-gray-400 dark:hover:text-gray-200 dark:hover:bg-slate-900">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                                class="w-5 h-5 mr-1">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M15.042 21.672 13.684 16.6m0 0-2.51 2.225.569-9.47 5.227 7.917-3.286-.672Zm-7.518-.267A8.25 8.25 0 1 1 20.25 10.5M8.288 14.212A5.25 5.25 0 1 1 17.25 10.5" />
                                            </svg>
                                            {{ __('Scheduler') }}
                                        </a>

                                        <a href="{{ route('parameters') }}"
                                            class="flex items-center block px-4 py-2 text-sm text-gray-800 dark:text-gray-400 dark:hover:text-gray-200 dark:hover:bg-slate-900">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                                class="w-5 h-5 mr-1">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.325.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 0 1 1.37.49l1.296 2.247a1.125 1.125 0 0 1-.26 1.431l-1.003.827c-.293.241-.438.613-.43.992a7.723 7.723 0 0 1 0 .255c-.008.378.137.75.43.991l1.004.827c.424.35.534.955.26 1.43l-1.298 2.247a1.125 1.125 0 0 1-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.47 6.47 0 0 1-.22.128c-.331.183-.581.495-.644.869l-.213 1.281c-.09.543-.56.94-1.11.94h-2.594c-.55 0-1.019-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 0 1-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 0 1-1.369-.49l-1.297-2.247a1.125 1.125 0 0 1 .26-1.431l1.004-.827c.292-.24.437-.613.43-.991a6.932 6.932 0 0 1 0-.255c.007-.38-.138-.751-.43-.992l-1.004-.827a1.125 1.125 0 0 1-.26-1.43l1.297-2.247a1.125 1.125 0 0 1 1.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.086.22-.128.332-.183.582-.495.644-.869l.214-1.28Z" />
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                            </svg>
                                            {{ __('Options') }}
                                        </a>


                                        <a href="{{ route('server.monitor') }}"
                                            class="flex items-center block px-4 py-2 text-sm text-gray-800 dark:text-gray-400 dark:hover:text-gray-200 dark:hover:bg-slate-900">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                                class="w-5 h-5 mr-1">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M9 17.25v1.007a3 3 0 0 1-.879 2.122L7.5 21h9l-.621-.621A3 3 0 0 1 15 18.257V17.25m6-12V15a2.25 2.25 0 0 1-2.25 2.25H5.25A2.25 2.25 0 0 1 3 15V5.25m18 0A2.25 2.25 0 0 0 18.75 3H5.25A2.25 2.25 0 0 0 3 5.25m18 0V12a2.25 2.25 0 0 1-2.25 2.25H5.25A2.25 2.25 0 0 1 3 12V5.25" />
                                            </svg>

                                            {{ __('Server') }}
                                        </a>
                                    @endif

                                </div>
                            </div>
                        </div>
                    @endif




               

                <div class="hidden sm:-my-px sm:ms-10 2xl:flex">
                    <input type="checkbox" name="light-switch" class="light-switch sr-only" />
                    <label class="relative cursor-pointer p-2" for="light-switch" onclick="toggleDarkMode()">
                        <svg class="dark:hidden" width="16" height="16" xmlns="http://www.w3.org/2000/svg">
                            <path class="fill-slate-300"
                                d="M7 0h2v2H7zM12.88 1.637l1.414 1.415-1.415 1.413-1.413-1.414zM14 7h2v2h-2zM12.95 14.433l-1.414-1.413 1.413-1.415 1.415 1.414zM7 14h2v2H7zM2.98 14.364l-1.413-1.415 1.414-1.414 1.414 1.415zM0 7h2v2H0zM3.05 1.706 4.463 3.12 3.05 4.535 1.636 3.12z" />
                            <path class="fill-slate-400" d="M8 4C5.8 4 4 5.8 4 8s1.8 4 4 4 4-1.8 4-4-1.8-4-4-4Z" />
                        </svg>
                        <svg class="hidden dark:block" width="16" height="16"
                            xmlns="http://www.w3.org/2000/svg">
                            <path class="fill-slate-400"
                                d="M6.2 1C3.2 1.8 1 4.6 1 7.9 1 11.8 4.2 15 8.1 15c3.3 0 6-2.2 6.9-5.2C9.7 11.2 4.8 6.3 6.2 1Z" />
                            <path class="fill-slate-500"
                                d="M12.5 5a.625.625 0 0 1-.625-.625 1.252 1.252 0 0 0-1.25-1.25.625.625 0 1 1 0-1.25 1.252 1.252 0 0 0 1.25-1.25.625.625 0 1 1 1.25 0c.001.69.56 1.249 1.25 1.25a.625.625 0 1 1 0 1.25c-.69.001-1.249.56-1.25 1.25A.625.625 0 0 1 12.5 5Z" />
                        </svg>
                    </label>
                </div>

                <!-- Settings Dropdown -->
                <div class="hidden    2xl:flex sm:items-center  sm:ms-6">
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button
                                class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-gray-400   dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none transition ease-in-out duration-150">
                                <img src="user-avatar.png" alt="Admin" loading="lazy"
                                    class="rounded-full w-8 h-8 object-cover dark:br-gray-100">
                                <div class="dark:text-indigo-400">{{ Auth::user()->name }}</div>
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
                            <!-- Authentication -->
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                    this.closest('form').submit();">
                                    {{ __('Exit') }}
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                </div>

                <!-- Hamburger -->
                <div class="me-2 flex items-center 2xl:hidden">
                    <button @click="open = ! open"
                        class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 dark:text-gray-500 hover:text-gray-500 dark:hover:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-900 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-900 focus:text-gray-500 dark:focus:text-gray-400 transition duration-150 ease-in-out">
                        <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                            <path :class="{ 'hidden': open, 'inline-flex': !open }" class="inline-flex"
                                stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16" />
                            <path :class="{ 'hidden': !open, 'inline-flex': open }" class="hidden"
                                stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>



    <!-- Responsive Navigation Menu -->
    <div :class="{ 'block': open, 'hidden': !open }" class="hidden 2xl:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Clients') }}
            </x-responsive-nav-link>
        </div>

        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('OLT')" :active="request()->routeIs('OLT')">
                {{ __('OLT') }}
            </x-responsive-nav-link>
        </div>

        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('GlobalSearch')" :active="request()->routeIs('GlobalSearch')">
                {{ __('Global Search') }}
            </x-responsive-nav-link>
        </div>
        @if (!empty($PrivData) && $PrivData['Install'] == '1')
            <div class="pt-2 pb-3 space-y-1">
                <x-responsive-nav-link :href="route('Options')" :active="request()->routeIs('Options')">
                    {{ __('Install') }}
                </x-responsive-nav-link>
            </div>
        @endif

        @if (!empty($PrivData) && $PrivData['Install'] == '1')
            <div class="pt-2 pb-3 space-y-1">
                <x-responsive-nav-link :href="route('duplicatedGet')" :active="request()->routeIs('duplicatedGet')">
                    {{ __('Clones') }}
                </x-responsive-nav-link>
            </div>
        @endif

        @if (!empty($PrivData) && $PrivData['Install'] == '1')
            <div class="pt-2 pb-3 space-y-1">
                <x-responsive-nav-link :href="route('naOntGet')" :active="request()->routeIs('naOntGet')">
                    {{ __('N/A') }}
                </x-responsive-nav-link>
            </div>
        @endif

        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('PonStats')" :active="request()->routeIs('PonStats')">
                {{ __('Pons') }}
            </x-responsive-nav-link>
        </div>


        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('OnuStats')" :active="request()->routeIs('OnuStats')">
                {{ __('Stats') }}
            </x-responsive-nav-link>
        </div>


        @if (!empty($PrivData) && $PrivData['Devices'] == '1')
            <div class="pt-2 pb-3 space-y-1">
                <x-responsive-nav-link :href="route('Devices')" :active="request()->routeIs('Devices')">
                    {{ __('Devices') }}
                </x-responsive-nav-link>
            </div>
        @endif

        @if (!empty($PrivData) && $PrivData['Devices'] == '1')
            <div class="pt-2 pb-3 space-y-1">
                <x-responsive-nav-link :href="route('mast_table')" :active="request()->routeIs('mast_table')">
                    {{ __('Masts') }}
                </x-responsive-nav-link>
            </div>
        @endif

        @if (!empty($PrivData) && $PrivData['admin'] == '1')
            <div class="pt-2 pb-3 space-y-1">
                <x-responsive-nav-link :href="route('Privilege.logs')" :active="request()->routeIs('Privilege.logs')">
                    {{ __('Privileges') }}
                </x-responsive-nav-link>
            </div>
        @endif

        @if (!empty($PrivData) && $PrivData['admin'] == '1')
            <div class="pt-2 pb-3 space-y-1">
                <x-responsive-nav-link :href="route('schedule')" :active="request()->routeIs('schedule')">
                    {{ __('Scheduler') }}
                </x-responsive-nav-link>
            </div>
        @endif

 
        @if (!empty($PrivData) && $PrivData['admin'] == '1') 
            <div class="pt-2 pb-3 space-y-1">
                <x-responsive-nav-link :href="route('server.monitor')" :active="request()->routeIs('server.monitor')">
                    {{ __('Server') }}
                </x-responsive-nav-link>
            </div>
        @endif


        @if (!empty($PrivData) && $PrivData['admin'] == '1')
            <div class="pt-2 pb-3 space-y-1">
                <x-responsive-nav-link :href="route('parameters')" :active="request()->routeIs('parameters')">
                    {{ __('Options') }}
                </x-responsive-nav-link>
            </div>
        @endif

        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('bug-data')" :active="request()->routeIs('bug-data')">
                {{ __('Bug Report') }}
            </x-responsive-nav-link>
        </div>

        @if (!empty($PrivData) && $PrivData['Priv_Log'] == '1')
            <div class="pt-2 pb-3 space-y-1">
                <x-responsive-nav-link :href="url('/log-viewer')" :active="request()->routeIs('log-viewer')">
                    {{ __('Logs') }}
                </x-responsive-nav-link>
            </div>
        @endif



        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200 dark:border-gray-600">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800 dark:text-gray-200">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->username }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')"
                        onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
