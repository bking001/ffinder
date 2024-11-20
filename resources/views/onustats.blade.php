<x-app-layout>

    <div class="py-12">

        <x-bladewind.notification position="top right" />
        <x-bladewind.notification />


        <div class="w-11/12 mx-auto">



            <div class="flex flex-wrap gap-4 justify-center items-center">

                <div
                    class="bw-statistic bg-white dark:bg-slate-800 dark:border dark:border-slate-700/50 p-6 rounded-md relative shadow shadow-gray-200/40 dark:shadow-slate-900  ">
                    <div class="flex">
                        <div class="grow number">
                            <div class="uppercase tracking-wider text-xs text-gray-400 mb-1 label">Total Online</div>
                            <div class="text-3xl text-gray-500/90 font-light">
                                <span class="figure tracking-wider dark:text-indigo-400 font-semibold"
                                    style="color:#4ade80;">
                                    <a href="{{ url('/') . '/OnuStatsAllOnline' }}"> {{ $Online }}</a>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <div
                    class="bw-statistic bg-white dark:bg-slate-800 dark:border dark:border-slate-700/50 p-6 rounded-md relative shadow shadow-gray-200/40 dark:shadow-slate-900  ">
                    <div class="flex">
                        <div class="grow number">
                            <div class="uppercase tracking-wider text-xs text-gray-400 mb-1 label">Total Offline</div>
                            <div class="text-3xl text-gray-500/90 font-light">
                                <span class="figure tracking-wider dark:text-red-400 font-semibold ">
                                    <a href="{{ url('/') . '/OnuStatsAllOffline' }}"> {{ $Offline }}</a>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>


                <div
                    class="bw-statistic bg-white dark:bg-slate-800 dark:border dark:border-slate-700/50 p-6 rounded-md relative shadow shadow-gray-200/40 dark:shadow-slate-900  ">
                    <div class="flex">
                        <div class="grow number">
                            <div class="uppercase tracking-wider text-xs text-gray-400 mb-1 label">High Rx Dbm</div>
                            <div class="text-3xl text-gray-500/90 font-light">
                                <span class="figure tracking-wider  font-semibold " style="color:#fdba74;">
                                    <a href="{{ url('/') . '/OnuStatsAllHighDbm' }}"> {{ $highDbm }}</a>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <div
                    class="bw-statistic bg-white dark:bg-slate-800 dark:border dark:border-slate-700/50 p-6 rounded-md relative shadow shadow-gray-200/40 dark:shadow-slate-900  ">
                    <div class="flex">
                        <div class="grow number">
                            <div class="uppercase tracking-wider text-xs text-gray-400 mb-1 label">Total LOS</div>
                            <div class="text-3xl text-gray-500/90 font-light">
                                <span class="figure tracking-wider dark:text-indigo-400  font-semibold">
                                    <a href="{{ url('/') . '/OnuStatsAllLos' }}"> {{ $los }}</a>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>


            </div>


            <div class="flex justify-end">
                <form class="max-w-md mx-left" method="get" action="{{ route('OnuStatsExel') }}" id="exportForm">
                    @csrf
                    <input type="hidden" name="query" value="{{ $query }}">
                    <button
                        class="inline-flex items-center px-4 py-2 ml-2 bg-gray-900 dark:bg-slate-900  rounded-md font-semibold text-xs text-white dark:text-indigo-400 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-gray-900  dark:hover:text-gray-900  dark:active:text-indigo-400 ">
                        <x-ri-file-excel-2-fill class="w-5 h-7 dark:text-green-400 dark:hover:text-gray-400" />
                    </button>
                </form>


                <form class="max-w-md mx-left" method="get" action="{{ route('OnuStatsCsv') }}" id="exportFormCsv">
                    @csrf
                    <input type="hidden" name="queryCsv" value="{{ $query }}">
                    <button
                        class="inline-flex items-center px-4 py-2 ml-2 bg-gray-900 dark:bg-slate-900  rounded-md font-semibold text-xs text-white dark:text-indigo-400 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-gray-900  dark:hover:text-gray-900  dark:active:text-indigo-400 ">
                        <x-fas-file-csv class="w-5 h-7 dark:text-red-400 dark:hover:text-gray-400" />
                    </button>
                </form>
            </div>



            <form class="max-w-md mx-left" method="get" action="{{ route('OnuStatsSearch') }}">
                @csrf
                <div class="flex">
                    <div class="relative flex-1">
                        <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                            <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true"
                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
                            </svg>
                        </div>
                        <input type="search" name="default_search"
                            class="block w-full p-2 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-gray-800 dark:bg-gray-900  dark:border-gray-600  dark:placeholder-gray-400 dark:text-white  dark:focus:ring-gray-900 dark:focus:border-gray-600"
                            placeholder="Search..." required />
                    </div>
                    <button type="submit"
                        class="inline-flex items-center px-4 py-2 ml-2 bg-gray-900 dark:bg-gray-900 border  border-indigo-400 rounded-md font-semibold text-xs text-white dark:text-indigo-400 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-indigo-400  dark:hover:text-gray-900  dark:active:text-indigo-400 ">
                        SEARCH
                    </button>

                </div>
            </form>
            <br>
            <form class="max-w-full mx-auto px-4 sm:px-6 lg:px-8" method="get"
                action="{{ route('OnuStatsAdvancedSearch') }}">
                @csrf
                <div class="flex">

                    <div class="relative flex-1">
                        <select name="type_search"
                            class="block w-full p-2   text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-gray-800 dark:bg-gray-900 dark:border-gray-600 dark:placeholder-gray-200 dark:text-gray-400 dark:focus:ring-gray-900 dark:focus:border-gray-600">
                            <option value="" disabled selected>Select Type</option>
                            @if (isset($type_search) && $type_search == 'BDCOM')
                                <option value="BDCOM" selected>BDCOM</option>
                                <option value="HUAWEI">HUAWEI</option>
                                <option value="ZTE">ZTE</option>
                                <option value="HSGQ">HSGQ</option>
                                <option value="VSOLUTION">VSOLUTION</option>
                            @elseif (isset($type_search) && $type_search == 'HUAWEI')
                                <option value="BDCOM">BDCOM</option>
                                <option value="HUAWEI" selected>HUAWEI</option>
                                <option value="ZTE">ZTE</option>
                                <option value="HSGQ">HSGQ</option>
                                <option value="VSOLUTION">VSOLUTION</option>
                            @elseif (isset($type_search) && $type_search == 'ZTE')
                                <option value="BDCOM">BDCOM</option>
                                <option value="HUAWEI">HUAWEI</option>
                                <option value="ZTE" selected>ZTE</option>
                                <option value="HSGQ">HSGQ</option>
                                <option value="VSOLUTION">VSOLUTION</option>
                            @elseif (isset($type_search) && $type_search == 'HSGQ')
                                <option value="BDCOM">BDCOM</option>
                                <option value="HUAWEI">HUAWEI</option>
                                <option value="ZTE">ZTE</option>
                                <option value="HSGQ"selected>HSGQ</option>
                                <option value="VSOLUTION">VSOLUTION</option>
                            @elseif (isset($type_search) && $type_search == 'VSOLUTION')
                                <option value="BDCOM">BDCOM</option>
                                <option value="HUAWEI">HUAWEI</option>
                                <option value="ZTE">ZTE</option>
                                <option value="HSGQ">HSGQ</option>
                                <option value="VSOLUTION"selected>VSOLUTION</option>
                            @else
                                <option value="BDCOM">BDCOM</option>
                                <option value="HUAWEI">HUAWEI</option>
                                <option value="ZTE">ZTE</option>
                                <option value="HSGQ">HSGQ</option>
                                <option value="VSOLUTION">VSOLUTION</option>
                            @endif

                        </select>
                    </div>
                    &nbsp;&nbsp;


                    <div class="relative flex-1">
                        <select name="status_search"
                            class="block w-full p-2   text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-gray-800 dark:bg-gray-900 dark:border-gray-600 dark:placeholder-gray-200 dark:text-gray-400 dark:focus:ring-gray-900 dark:focus:border-gray-600">
                            <option value=""disabled selected>Select Status</option>
                            @if (isset($status_search) && $status_search == 'Online')
                                <option value="Online" selected style="color:#4ade80;">Online</option>
                                <option value="Offline" class="dark:text-red-400">Offline</option>
                            @elseif (isset($status_search) && $status_search == 'Offline')
                                <option value="Online" style="color:#4ade80;">Online</option>
                                <option value="Offline" selected class="dark:text-red-400">Offline</option>
                            @else
                                <option value="Online" style="color:#4ade80;">Online</option>
                                <option value="Offline" class="dark:text-red-400">Offline</option>
                            @endif

                        </select>
                    </div>
                    &nbsp;&nbsp;

                    <div class="relative flex-1">
                        <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                            <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true"
                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
                            </svg>
                        </div>
                        <input type="search" value="{{ isset($address_search) ? $address_search : '' }}"
                            name="address_search"
                            class="block w-full p-2 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-gray-800 dark:bg-gray-900  dark:border-gray-600  dark:placeholder-gray-400 dark:text-white  dark:focus:ring-gray-900 dark:focus:border-gray-600"
                            placeholder="Address.." />
                    </div>

                    &nbsp;&nbsp;
                    <div class="relative flex-1 ">
                        <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                            <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true"
                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
                            </svg>
                        </div>
                        <input type="search" value="{{ isset($hihgdbm_search) ? $hihgdbm_search : '' }}"
                            name="hihgdbm_search"
                            class="block w-full p-2 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-gray-800 dark:bg-gray-900  dark:border-gray-600  dark:placeholder-gray-400 dark:text-white  dark:focus:ring-gray-900 dark:focus:border-gray-600"
                            placeholder="Dbm > .." />
                    </div>
                    &nbsp;&nbsp;
                    <div class="relative flex-1">
                        <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                            <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true"
                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
                            </svg>
                        </div>
                        <input type="search" value="{{ isset($lowdbm_search) ? $lowdbm_search : '' }}"
                            name="lowdbm_search"
                            class="block w-full p-2 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-gray-800 dark:bg-gray-900  dark:border-gray-600  dark:placeholder-gray-400 dark:text-white  dark:focus:ring-gray-900 dark:focus:border-gray-600"
                            placeholder="Dbm < .." />
                    </div>

                    &nbsp;&nbsp;
                    <div class="relative flex-1">
                        <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                            <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true"
                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
                            </svg>
                        </div>
                        <input type="search" value="{{ isset($high_distance_search) ? $high_distance_search : '' }}"
                            name="high_distance_search"
                            class="block w-full p-2 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-gray-800 dark:bg-gray-900  dark:border-gray-600  dark:placeholder-gray-400 dark:text-white  dark:focus:ring-gray-900 dark:focus:border-gray-600"
                            placeholder="Distance > .." />
                    </div>


                    &nbsp;&nbsp;
                    <div class="relative flex-1">
                        <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                            <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true"
                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
                            </svg>
                        </div>
                        <input type="search" value="{{ isset($low_distance_search) ? $low_distance_search : '' }}"
                            name="low_distance_search"
                            class="block w-full p-2 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-gray-800 dark:bg-gray-900  dark:border-gray-600  dark:placeholder-gray-400 dark:text-white  dark:focus:ring-gray-900 dark:focus:border-gray-600"
                            placeholder="Distance < .." />
                    </div>


                    <button type="submit"
                        class="inline-flex items-center px-4 py-2 ml-2 bg-gray-900 dark:bg-gray-900 border  border-indigo-400 rounded-md font-semibold text-xs text-white dark:text-indigo-400 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-indigo-400  dark:hover:text-gray-900  dark:active:text-indigo-400 ">
                        FILTERS SEARCH
                    </button>

                </div>
            </form>

            <div class="flex items-center  justify-center text:xs sm:justify-center ml-4 sm:ml-4">
                <table class="text-sm w-full  border-separate border-spacing-y-2">
                    <thead class="">
                        <tr class="tr-class">
                            <td class="td-class text-indigo-400 text-center text:xs"
                                style="color:#818cf8;background:#141c3d;letter-spacing: .1em;font-weight: 600;text-transform: uppercase;">
                                TYPE</th>
                            <td class="td-class text-indigo-400 text-center text:xs"
                                style="color:#818cf8;background:#141c3d;letter-spacing: .1em;font-weight: 600;text-transform: uppercase;">
                                ADDRESS</th>
                            <td class="td-class text-indigo-400 text-center text:xs"
                                style="color:#818cf8;background:#141c3d;letter-spacing: .1em;font-weight: 600;text-transform: uppercase;">
                                NAME</th>
                            <td class="td-class text-indigo-400 text-center text:xs"
                                style="color:#818cf8;background:#141c3d;letter-spacing: .1em;font-weight: 600;text-transform: uppercase;">
                                USER</th>
                            <td class="td-class text-indigo-400 text-center text:xs"
                                style="color:#818cf8;background:#141c3d;letter-spacing: .1em;font-weight: 600;text-transform: uppercase;">
                                PON PORT</th>
                            <td class="td-class text-indigo-400 text-center text:xs"
                                style="color:#818cf8;background:#141c3d;letter-spacing: .1em;font-weight: 600;text-transform: uppercase;">
                                MAC</th>
                            <td class="td-class text-indigo-400 text-center text:xs"
                                style="color:#818cf8;background:#141c3d;letter-spacing: .1em;font-weight: 600;text-transform: uppercase;">
                                STATUS</th>
                            <td class="td-class text-indigo-400 text-center text:xs"
                                style="color:#818cf8;background:#141c3d;letter-spacing: .1em;font-weight: 600;text-transform: uppercase;">
                                REASON</th>
                            <td class="td-class text-indigo-400 text-center text:xs"
                                style="color:#818cf8;background:#141c3d;letter-spacing: .1em;font-weight: 600;text-transform: uppercase;">
                                DISTANCE</th>
                            <td class="td-class text-indigo-400 text-center text:xs"
                                style="color:#818cf8;background:#141c3d;letter-spacing: .1em;font-weight: 600;text-transform: uppercase;">
                                DBM RX</th>
                            <td class="td-class text-indigo-400 text-center text:xs"
                                style="color:#818cf8;background:#141c3d;letter-spacing: .1em;font-weight: 600;text-transform: uppercase;">
                                UPDATE</th>
                        </tr>
                    </thead>

                    <tbody id="pon_statistic_table-body">
                        @foreach ($data as $item)
                            <tr class="tr-class">
                                <td class="td-class text:xs"
                                    style="text-align:center;background:#111b30;letter-spacing: .1em;font-weight: 600;text-transform: uppercase;">
                                    {{ $item->Type }}</td>
                                <td class="td-class text:xs"
                                    style="text-align:center;background:#111b30;letter-spacing: .1em;font-weight: 600;text-transform: uppercase;">
                                    {{ $item->olt }}</td>
                                <td class="td-class text:xs"
                                    style="text-align:center;background:#111b30;letter-spacing: .1em;font-weight: 600;text-transform: uppercase;">
                                    {{ $item->device_name }}</td>
                                <td class="td-class text:xs" onclick="ont_history({{ $item->descr }})"
                                    style="text-align:center;background:#111b30;letter-spacing: .1em;font-weight: 600;text-transform: uppercase;text-decoration: #818cf8 underline;cursor:pointer;">
                                    {{ $item->descr }}</td>
                                <td class="td-class text:xs"
                                    style="text-align:center;background:#111b30;letter-spacing: .1em;font-weight: 600;text-transform: uppercase;">
                                    {{ $item->ponPort }}</td>
                                <td class="td-class text:xs"
                                    style="text-align:center;background:#111b30;letter-spacing: .1em;font-weight: 600;text-transform: uppercase;">
                                    {{ $item->onuMac }}</td>

                                @if ($item->onuStatus !== 'Offline')
                                    <td class="td-class text:xs"
                                        style="color:#86efac;text-align:center;background:#111b30;letter-spacing: .1em;font-weight: 600;text-transform: uppercase;">
                                        <span
                                            class="inline-flex items-center rounded-md bg-green-400 dark:bg-green-400/20 px-2 py-1 text-xs font-medium text-green-400 ring-1 ring-inset ring-green-600/20"
                                            style="background:rgba(74, 222, 128, .1);">
                                            {{ $item->onuStatus }}</span>
                                    </td>
                                @else
                                    <td class="td-class text:xs"
                                        style="color:#f87171;text-align:center;background:#111b30;letter-spacing: .1em;font-weight: 600;text-transform: uppercase;">

                                        <span
                                            class="inline-flex items-center rounded-md bg-red-400 dark:bg-red-400/20 px-2 py-1 text-xs font-medium text-red-400 ring-1 ring-inset ring-red-600/20"
                                            style="background:rgba(248, 113, 113, .1);">
                                            {{ $item->onuStatus }}</span>
                                    </td>
                                @endif

                                <td class="td-class text:xs"
                                    style="text-align:center;background:#111b30;letter-spacing: .1em;font-weight: 600;text-transform: uppercase;">
                                    {{ $item->reason }}</td>
                                <td class="td-class text:xs"
                                    style="text-align:center;background:#111b30;letter-spacing: .1em;font-weight: 600;text-transform: uppercase;">
                                    {{ $item->distance }}</td>

                                @if ($item->dbmRX < -27)
                                    <td class="td-class text:xs"
                                        style="color:#fde68a;text-align:center;background:#111b30;letter-spacing: .1em;font-weight: 600;text-transform: uppercase;">
                                        <span
                                            class="inline-flex items-center rounded-md bg-amber-400 dark:bg-amber-400/20 px-2 py-1 text-xs font-medium text-amber-400 ring-1 ring-inset ring-amber-600/20"
                                            style="background:rgba(251, 191, 36, .1);">
                                            {{ $item->dbmRX }}</span>
                                    </td>
                                @else
                                    <td class="td-class text:xs"
                                        style="text-align:center;background:#111b30;letter-spacing: .1em;font-weight: 600;text-transform: uppercase;">
                                        {{ $item->dbmRX }}</td>
                                @endif
                                <td class="td-class text:xs"
                                    style="text-align:center;background:#111b30;letter-spacing: .1em;font-weight: 600;text-transform: uppercase;">
                                    {{ $item->last_update }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div>
                {{ $data->appends(request()->query())->links() }}
            </div>

        </div>



        <x-bladewind.modal size="omg" name="all-ont-History" show_close_icon="true" blur_backdrop="false"
            show_action_buttons="false">





            <x-bladewind.datepicker name="history_date_picker" default_date="" type="range" format="yyyy-mm-dd"
                label="Time" placeholder="Select a date" required="false" with_time="false" hours_as="12"
                time_format="hh:mm" show_seconds="false" />

            <script>
                function changeStylesByClassName(className, styles) {
                    const elements = document.getElementsByClassName(className);
                    for (let i = 0; i < elements.length; i++) {
                        for (const [key, value] of Object.entries(styles)) {
                            elements[i].style[key] = value;
                        }
                    }
                }

                // Change background color of specific div elements
                changeStylesByClassName('bg-white dark:bg-dark-600 mt-12 p-4 absolute top-0 left-0 z-50 shadow-md rounded-lg', {
                    backgroundColor: '#020617'
                });

                // Change text color of specific div elements
                changeStylesByClassName('text-lg text-white/90 dark:text-gray-400 cursor-default', {
                    color: '#e5e7eb'
                });

                // Function to change styles of elements by name
                function changeStylesByName(name, styles) {
                    const elements = document.getElementsByName(name);
                    for (let i = 0; i < elements.length; i++) {
                        for (const [key, value] of Object.entries(styles)) {
                            elements[i].style[key] = value;
                        }
                    }
                }

                // Change styles of start date element
                changeStylesByName('start_time', {
                    fontSize: '0.75rem',
                    color: '#9ca3af',
                    borderRadius: '0.5rem',
                    borderColor: '#334155',
                    backgroundColor: '#111827'
                });

                // Change styles of end date element
                changeStylesByName('end_time', {
                    fontSize: '0.75rem',
                    color: '#9ca3af',
                    borderRadius: '0.5rem',
                    borderColor: '#334155',
                    backgroundColor: '#111827'
                });
            </script>

            <div style="max-height: 700px; overflow-y: auto;display:block;">

                <div id="all-ont-button" class="flex items-center justify-center"></div>
                <p id="datepickererror"></p>
                <br>

                <div id="all-ont-graph" class="flex items-center justify-center"></div>

                <br>
                <div id="all-ont-data" class="flex items-center justify-center"></div>

            </div>


        </x-bladewind.modal>


    </div>

 

</x-app-layout>
