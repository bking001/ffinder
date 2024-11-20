<x-app-layout>

    <div class="py-12 w-11/12 mx-auto">

        <x-bladewind.notification position="top right" />
        <x-bladewind.notification />

        <form class="max-w-md mx-left" method="get" action="{{ route('TaskSearch') }}">
            @csrf
            <div class="flex">
                <div class="relative flex-1">
                    <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                        <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true"
                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
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

        <div class="w-11/12 mx-auto" style="max-height: 900px; overflow-y: auto;display:block;">
            <div class="flex items-center  justify-center text:xs sm:justify-center ml-4 sm:ml-4">
                <table class="text-sm w-full  border-separate border-spacing-y-2">
                    <thead class="sr-only">
                        <tr class="tr-class">
                            <th class="text-indigo-400">PON</th>
                            <th class="text-indigo-400">PON</th>
                            <th class="text-indigo-400">PON</th>
                            <th class="text-indigo-400">PON</th>
                            <th class="text-indigo-400">PON</th>
                            <th class="text-indigo-400">PON</th>
                        </tr>
                    </thead>

                    <tbody id="pon_statistic_table-body">
                        <tr class="tr-class">
                            <td class="td-class text-indigo-400 text-center text:xs"
                                style="color:#818cf8;background:#141c3d;letter-spacing: .1em;font-weight: 600;text-transform: uppercase;">
                                TASK ID</th>
                            <td class="td-class text-indigo-400 text-center text:xs"
                                style="color:#818cf8;background:#141c3d;letter-spacing: .1em;font-weight: 600;text-transform: uppercase;">
                                USER</th>
                            <td class="td-class text-indigo-400 text-center text:xs"
                                style="color:#818cf8;background:#141c3d;letter-spacing: .1em;font-weight: 600;text-transform: uppercase;">
                                CREATOR</th>
                            <td class="td-class text-indigo-400 text-center text:xs"
                                style="color:#818cf8;background:#141c3d;letter-spacing: .1em;font-weight: 600;text-transform: uppercase;">
                                DEVICE</th>
                            <td class="td-class text-indigo-400 text-center text:xs"
                                style="color:#818cf8;background:#141c3d;letter-spacing: .1em;font-weight: 600;text-transform: uppercase;">
                                ADDRESS</th>
                            <td class="td-class text-indigo-400 text-center text:xs"
                                style="color:#818cf8;background:#141c3d;letter-spacing: .1em;font-weight: 600;text-transform: uppercase;">
                                SAME</th>
                            <td class="td-class text-indigo-400 text-center text:xs"
                                style="color:#818cf8;background:#141c3d;letter-spacing: .1em;font-weight: 600;text-transform: uppercase;">
                                TYPE</th>
                            <td class="td-class text-indigo-400 text-center text:xs"
                                style="color:#818cf8;background:#141c3d;letter-spacing: .1em;font-weight: 600;text-transform: uppercase;">
                                TASK STATUS</th>
                            <td class="td-class text-indigo-400 text-center text:xs"
                                style="color:#818cf8;background:#141c3d;letter-spacing: .1em;font-weight: 600;text-transform: uppercase;">
                                UPDATED</th>
                            <td class="td-class text-indigo-400 text-center text:xs"
                                style="color:#818cf8;background:#141c3d;letter-spacing: .1em;font-weight: 600;text-transform: uppercase;">
                                HISTORY</th>
                            <td class="td-class text-indigo-400 text-center text:xs"
                                style="color:#818cf8;background:#141c3d;letter-spacing: .1em;font-weight: 600;text-transform: uppercase;">
                                ACTION</th>
                        </tr>

                        @foreach ($data as $item)
                            <tr class="tr-class">
                                <td class="td-class text:xs" id="buttontask{{ $item->task_id }}"
                                    onclick="TaskContentById({{ $item->task_id }},{{ $item->user_id }})"
                                    style="cursor: pointer;text-decoration-color: #818cf8 !important;text-decoration: underline;text-underline-offset: 4px;text-align:center;background:#111b30;letter-spacing: .1em;font-weight: 600;text-transform: uppercase;">
                                    {{ $item->task_id }}</td>
                                <td class="td-class text:xs" id="buttonclient{{ $item->task_id }}"
                                    style="text-decoration-color: #818cf8 !important;text-decoration: underline;text-underline-offset: 4px;text-align:center;background:#111b30;letter-spacing: .1em;font-weight: 600;text-transform: uppercase;">
                                    <a target="_blank"
                                        href="{{ url('/') . '/dashboard?id=' . $item->user_id }}">{{ $item->user_id }}</a>
                                </td>
                                <td class="td-class text:xs"
                                    style="text-align:center;background:#111b30;letter-spacing: .1em;font-weight: 600;text-transform: uppercase;">
                                    {{ $item->staff }}</td>
                                <td class="td-class text:xs"
                                    style="text-align:center;background:#111b30;letter-spacing: .1em;font-weight: 600;text-transform: uppercase;">
                                    {{ $item->oltType }}</td>
                                <td class="td-class text:xs"
                                    style="text-align:center;background:#111b30;letter-spacing: .1em;font-weight: 600;text-transform: uppercase;">
                                    {{ $item->oltName }}</td>
                                <td class="td-class text:xs"
                                    style="text-align:center;background:#111b30;letter-spacing: .1em;font-weight: 600;text-transform: uppercase;">
                                    <button onclick="Same('{{ url('/') . '/Same?oltName=' . $item->oltName }}')"
                                        class="inline-flex items-center px-4 py-2 h-8 ml-2  bg-gray-900 dark:bg-gray-900 border  border-indigo-400 rounded-md font-semibold text-xs text-white dark:text-indigo-400 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-indigo-400 dark:hover:text-gray-900  dark:active:text-indigo-400"
                                        type="button">
                                        @if(isset($item->count))
                                        {{ $item->count }}
                                        @else
                                            0
                                        @endif
                                    </button>
                                </td>
                                @if ($item->type == 157)
                                    <td class="td-class text:xs"
                                        style="text-align:center;background:#111b30;letter-spacing: .1em;font-weight: 600;text-transform: uppercase;">
                                        ONU / ONT არაა კავშირზე</td>
                                @elseif ($item->type == 158)
                                    <td class="td-class text:xs"
                                        style="text-align:center;background:#111b30;letter-spacing: .1em;font-weight: 600;text-transform: uppercase;">
                                        ანტენა არაა კავშირზე</td>
                                @elseif ($item->type == 159)
                                    <td class="td-class text:xs"
                                        style="text-align:center;background:#111b30;letter-spacing: .1em;font-weight: 600;text-transform: uppercase;">
                                        ლინკი არ დგება</td>
                                @elseif ($item->type == 160)
                                    <td class="td-class text:xs"
                                        style="text-align:center;background:#111b30;letter-spacing: .1em;font-weight: 600;text-transform: uppercase;">
                                        მაღალი dBm ONU-ზე (-27>)</td>
                                @elseif ($item->type == 161)
                                    <td class="td-class text:xs"
                                        style="text-align:center;background:#111b30;letter-spacing: .1em;font-weight: 600;text-transform: uppercase;">
                                        მაღალი dBm ანტენაზე (-70>)</td>
                                @elseif ($item->type == 162)
                                    <td class="td-class text:xs"
                                        style="text-align:center;background:#111b30;letter-spacing: .1em;font-weight: 600;text-transform: uppercase;">
                                        IP არ იღებს</td>
                                @else
                                    <td class="td-class text:xs"
                                        style="text-align:center;background:#111b30;letter-spacing: .1em;font-weight: 600;text-transform: uppercase;">
                                        {{ $item->type }}</td>
                                @endif

                                @if ($item->taskStatus == 1)
                                    <td class="td-class text:xs dark:text-red-400"
                                        style="text-align:center;background:#111b30;letter-spacing: .1em;font-weight: 600;text-transform: uppercase;">
                                        <span
                                            class="inline-flex items-center rounded-md bg-red-400 dark:bg-red-400/20 px-2 py-1 text-xs font-medium text-red-400 ring-1 ring-inset ring-red-600/20"
                                            style="background:rgba(248, 113, 113, .1);">
                                            NOT FIXED</span>
                                    </td>
                                @elseif ($item->taskStatus == 2)
                                    <td class="td-class text:xs dark:text-green-400"
                                        style="text-align:center;background:#111b30;letter-spacing: .1em;font-weight: 600;text-transform: uppercase;">
                                        <span
                                            class="inline-flex items-center rounded-md bg-green-400 dark:bg-green-400/20 px-2 py-1 text-xs font-medium text-green-400 ring-1 ring-inset ring-green-600/20"
                                            style="background:rgba(74, 222, 128, .1);">FIXED</span>
                                    </td>
                                @elseif ($item->taskStatus == 3)
                                    <td class="td-class text:xs "
                                        style="text-align:center;background:#111b30;color:#facc15;letter-spacing: .1em;font-weight: 600;text-transform: uppercase;">
                                        <span
                                            class="inline-flex items-center rounded-md bg-amber-400 dark:bg-amber-400/20 px-2 py-1 text-xs font-medium text-amber-400 ring-1 ring-inset ring-amber-600/20"
                                            style="background:rgba(251, 191, 36, .1);">
                                            ARCHIVED</span>
                                    </td>
                                @elseif ($item->taskStatus == 4)
                                    <td class="td-class text:xs "
                                        style="text-align:center;background:#111b30;color:#64748b;letter-spacing: .1em;font-weight: 600;text-transform: uppercase;">
                                        <span
                                            class="inline-flex items-center rounded-md bg-gray-400 dark:bg-gray-400/20 px-2 py-1 text-xs font-medium text-gray-400 ring-1 ring-inset ring-gray-600/20"
                                            style="background:rgba(156, 163, 175, .1);">
                                            OUTDATED</span>
                                    </td>
                                @elseif ($item->taskStatus == 9)
                                    <td class="td-class text:xs dark:text-indigo-400"
                                        style="text-align:center;background:#111b30;letter-spacing: .1em;font-weight: 600;text-transform: uppercase;">
                                        <span
                                            class="inline-flex items-center rounded-md bg-indigo-400 dark:bg-indigo-400/20 px-2 py-1 text-xs font-medium text-indigo-400 ring-1 ring-inset ring-indigo-600/20"
                                            style="background:rgba(129, 140, 248, .1);">
                                            CHANGED</span>
                                    </td>
                                @endif

                                <td class="td-class text:xs"
                                    style="text-align:center;background:#111b30;letter-spacing: .1em;font-weight: 600;text-transform: uppercase;">
                                    {{ $item->last_update }}</td>

                                <td class="td-class text:xs"
                                    style="text-align:center;background:#111b30;letter-spacing: .1em;font-weight: 600;text-transform: uppercase;">
                                    <button id="buttonStyle{{ $item->task_id }}"
                                        onclick="History('{{ $item->task_id }}')"
                                        class="inline-flex items-center px-4 py-2 h-8 ml-2  bg-gray-900 dark:bg-gray-900 border  border-indigo-400 rounded-md font-semibold text-xs text-white dark:text-indigo-400 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-indigo-400 dark:hover:text-gray-900  dark:active:text-indigo-400"
                                        type="button">
                                        History
                                    </button>
                                </td>

                                @if ($item->taskStatus == 1 || $item->taskStatus == 2 || $item->taskStatus == 9)
                                    <td class="td-class text:xs"
                                        style="text-align:center;background:#111b30;letter-spacing: .1em;font-weight: 600;text-transform: uppercase;">
                                        <button onclick="DisableFirst('{{ $item->task_id }}')"
                                            class="inline-flex items-center px-4 py-2 h-8 ml-2  bg-gray-900  dark:bg-gray-900 border  border-red-400 rounded-md font-semibold text-xs text-white dark:text-red-400 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-red-400 dark:hover:text-gray-900  dark:active:text-red-400"
                                            type="button">
                                            STOP
                                        </button>
                                    </td>
                                @elseif ($item->taskStatus == 3 || $item->taskStatus == 4)
                                    <td class="td-class text:xs"
                                        style="text-align:center;background:#111b30;letter-spacing: .1em;font-weight: 600;text-transform: uppercase;">
                                        <button onclick="RestoreFirst('{{ $item->task_id }}')"
                                            class="inline-flex items-center px-4 py-2 h-8 ml-2  bg-gray-900 dark:bg-gray-900 border  border-indigo-400 rounded-md font-semibold text-xs text-white dark:text-indigo-400 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-indigo-400 dark:hover:text-gray-900  dark:active:text-indigo-400"
                                            type="button">
                                            RESTORE
                                        </button>
                                    </td>
                                @endif

                            </tr>
                        @endforeach

                    </tbody>
                </table>
            </div>
            {{ $data->appends(request()->query())->links() }}
        </div>


    </div>

    <x-bladewind.modal size="large" name="custom-History" show_close_icon="true" blur_backdrop="false"
        show_action_buttons="false">
        <div id="DivData" class="flex items-center justify-center"
            style="max-height: 700px; overflow-y: auto;display:block;"></div>
    </x-bladewind.modal>

    <input id="task_id" value="" hidden />

    <x-bladewind.modal size="medium" center_action_buttons="true" type="warning"
        title="Confirm Disable Task From Monitoring" :ok_button_action="'Disable()'" close_after_action="true"
        name="Disable-Confirm" show_close_icon="true" blur_backdrop="false" ok_button_label="Yes, Stop"
        cancel_button_label="Don't Stop">
        Are you sure you want to Stop Task Monitoring ? This action cannot be undone
    </x-bladewind.modal>

    <x-bladewind.modal size="medium" center_action_buttons="true" type="warning"
        title="Confirm Restore Task From Monitoring" :ok_button_action="'Restore()'" close_after_action="true"
        name="Restore-Confirm" show_close_icon="true" blur_backdrop="false" ok_button_label="Yes, Restore"
        cancel_button_label="Don't Restore">
        Are you sure you want to Restore Task Monitoring ? This action cannot be undone
    </x-bladewind.modal>



    <x-bladewind::modal type="info" title="Tasks" name="task-modal" show_close_icon="true" size="xl"
        backdrop_can_close="true" show_action_buttons="true" cancel_button_label="" ok_button_label=""
        blur_backdrop="false">

        <div style="max-height: 700px; overflow-y: auto;">

            <p id="taskidforlabel"></p>
            <div class="flex items-center sm:justify-center ml-4 sm:ml-0" style="width: 100%;">
                <table class="text-sm border-separate" style="width: 100%;">
                    <thead>
                        <tr class="tr-class sticky top-0 z-10">
                            <th class="td-class text-indigo-400 text-center text:xs uppercase tracking-widest"
                                style="color:#818cf8;background:#141c3d;letter-spacing: .1em;font-weight: 600;">თარიღი
                            </th>
                            <th class="td-class text-indigo-400 text-center text:xs uppercase tracking-widest"
                                style="color:#818cf8;background:#141c3d;letter-spacing: .1em;font-weight: 600;">ტიპი
                            </th>
                            <th class="td-class text-indigo-400 text-center text:xs uppercase tracking-widest"
                                style="color:#818cf8;background:#141c3d;letter-spacing: .1em;font-weight: 600;">
                                კომენტარი</th>
                        </tr>
                    </thead>
                    <tbody id="task_table-body">

                    </tbody>
                </table>
            </div>

        </div>

    </x-bladewind::modal>

    <script>
        function TaskContentById(Taskid, client) {
            var button = document.getElementById('buttonStyle' + Taskid);
            if (button) {
                button.style.color = '#64748b';
                button.style.borderColor = '#64748b';
            }

            var clientside = document.getElementById('buttontask' + Taskid);
            if (clientside) {
                clientside.style.color = '#64748b';
            }

            var taskside = document.getElementById('buttonclient' + Taskid);
            if (taskside) {
                taskside.style.color = '#64748b';
            }


            document.getElementById("taskidforlabel").innerHTML = 'Task id : ' + Taskid;
            document.getElementById("task_table-body").innerHTML = `
            <tr class="tr-class">
            <td class="td-class" style="justify-content: center;background:rgb(24, 31, 47);">
                <div style="display: flex !important;justify-content: center;">
                    <div style="flex-shrink: 0 !important;">
                        <svg   fill="#5c6bc0" class="mr-2 animate-spin  w-5 h-5" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg">
                            <path d="M526 1394q0 53-37.5 90.5t-90.5 37.5q-52 0-90-38t-38-90q0-53 37.5-90.5t90.5-37.5 90.5 37.5 37.5 90.5zm498 206q0 53-37.5 90.5t-90.5 37.5-90.5-37.5-37.5-90.5 37.5-90.5 90.5-37.5 90.5 37.5 37.5 90.5zm-704-704q0 53-37.5 90.5t-90.5 37.5-90.5-37.5-37.5-90.5 37.5-90.5 90.5-37.5 90.5 37.5 37.5 90.5zm1202 498q0 52-38 90t-90 38q-53 0-90.5-37.5t-37.5-90.5 37.5-90.5 90.5-37.5 90.5 37.5 37.5 90.5zm-964-996q0 66-47 113t-113 47-113-47-47-113 47-113 113-47 113 47 47 113zm1170 498q0 53-37.5 90.5t-90.5 37.5-90.5-37.5-37.5-90.5 37.5-90.5 90.5-37.5 90.5 37.5 37.5 90.5zm-640-704q0 80-56 136t-136 56-136-56-56-136 56-136 136-56 136 56 56 136zm530 206q0 93-66 158.5t-158 65.5q-93 0-158.5-65.5t-65.5-158.5q0-92 65.5-158t158.5-66q92 0 158 66t66 158z">
                            </path>
                        </svg>
                    </div>
                </div>
            </td>

            <td class="td-class" style="justify-content: center;background:rgb(24, 31, 47);">
                <div style="display: flex !important;justify-content: center;">
                    <div style="flex-shrink: 0 !important;">
                        <svg   fill="#5c6bc0" class="mr-2 animate-spin  w-5 h-5" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg">
                            <path d="M526 1394q0 53-37.5 90.5t-90.5 37.5q-52 0-90-38t-38-90q0-53 37.5-90.5t90.5-37.5 90.5 37.5 37.5 90.5zm498 206q0 53-37.5 90.5t-90.5 37.5-90.5-37.5-37.5-90.5 37.5-90.5 90.5-37.5 90.5 37.5 37.5 90.5zm-704-704q0 53-37.5 90.5t-90.5 37.5-90.5-37.5-37.5-90.5 37.5-90.5 90.5-37.5 90.5 37.5 37.5 90.5zm1202 498q0 52-38 90t-90 38q-53 0-90.5-37.5t-37.5-90.5 37.5-90.5 90.5-37.5 90.5 37.5 37.5 90.5zm-964-996q0 66-47 113t-113 47-113-47-47-113 47-113 113-47 113 47 47 113zm1170 498q0 53-37.5 90.5t-90.5 37.5-90.5-37.5-37.5-90.5 37.5-90.5 90.5-37.5 90.5 37.5 37.5 90.5zm-640-704q0 80-56 136t-136 56-136-56-56-136 56-136 136-56 136 56 56 136zm530 206q0 93-66 158.5t-158 65.5q-93 0-158.5-65.5t-65.5-158.5q0-92 65.5-158t158.5-66q92 0 158 66t66 158z">
                            </path>
                        </svg>
                    </div>
                </div>
            </td>

            <td class="td-class" style="justify-content: center;background:rgb(24, 31, 47);">
                <div style="display: flex !important;justify-content: center;">
                    <div style="flex-shrink: 0 !important;">
                        <svg   fill="#5c6bc0" class="mr-2 animate-spin  w-5 h-5" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg">
                            <path d="M526 1394q0 53-37.5 90.5t-90.5 37.5q-52 0-90-38t-38-90q0-53 37.5-90.5t90.5-37.5 90.5 37.5 37.5 90.5zm498 206q0 53-37.5 90.5t-90.5 37.5-90.5-37.5-37.5-90.5 37.5-90.5 90.5-37.5 90.5 37.5 37.5 90.5zm-704-704q0 53-37.5 90.5t-90.5 37.5-90.5-37.5-37.5-90.5 37.5-90.5 90.5-37.5 90.5 37.5 37.5 90.5zm1202 498q0 52-38 90t-90 38q-53 0-90.5-37.5t-37.5-90.5 37.5-90.5 90.5-37.5 90.5 37.5 37.5 90.5zm-964-996q0 66-47 113t-113 47-113-47-47-113 47-113 113-47 113 47 47 113zm1170 498q0 53-37.5 90.5t-90.5 37.5-90.5-37.5-37.5-90.5 37.5-90.5 90.5-37.5 90.5 37.5 37.5 90.5zm-640-704q0 80-56 136t-136 56-136-56-56-136 56-136 136-56 136 56 56 136zm530 206q0 93-66 158.5t-158 65.5q-93 0-158.5-65.5t-65.5-158.5q0-92 65.5-158t158.5-66q92 0 158 66t66 158z">
                            </path>
                        </svg>
                    </div>
                </div>
            </td>
            </tr>
        `;

            showModal('task-modal');

            $.ajax({
                url: "/airsoft-search",
                type: "GET",
                data: {
                    'ab_nom': client
                },
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                dataType: "json",
                success: function(data) {

                    let result = JSON.parse(data);
                    if (result.id !== '') {
                        $.ajax({
                            url: "/airsoft-tasks",
                            type: "GET",
                            data: {
                                'ClientID': result.id
                            },
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            dataType: "json",
                            success: function(Response) {
                                document.getElementById("task_table-body").innerHTML = '';

                                var table = document.getElementById('task_table-body');
                                let response = JSON.parse(Response);



                                response.info.forEach(item => {



                                    if (Taskid == item.task_id) {
                                        var row = table.insertRow();
                                        var cell1 = row.insertCell(0);
                                        var cell2 = row.insertCell(1);
                                        var cell3 = row.insertCell(2);

                                        let type = '';
                                        if (item.type == 37) type =
                                            'AirLink: აბონენტის ჩართვა';
                                        else if (item.type == 38) type =
                                            'AirLink: გასარკვევია ადგილზე';
                                        else if (item.type == 39) type =
                                            'AirLink: ხაზის დაზიანება';
                                        else if (item.type == 40) type =
                                            'AirLink: სიჩქარის პრობლემა';
                                        else if (item.type == 41) type =
                                            'AirLink: ანტენის შეცვლა';
                                        else if (item.type == 42) type =
                                            'AirLink: გაუქმება';
                                        else if (item.type == 43) type =
                                            'AirLink: Wi-Fi ინსტალაცია';
                                        else if (item.type == 44) type = 'AirLink: სხვა';
                                        else if (item.type == 45) type =
                                            'AirLink: ანტენის შეცვლა/გადატანა';
                                        else if (item.type == 46) type =
                                            'AirLink: ფასიანი გამოძახება';
                                        else if (item.type == 47) type =
                                            'AirLink: ხელახალი აქტივაციის საფასური';
                                        else if (item.type == 48) type =
                                            'AirLink: აპარატურის მოხსნა';
                                        else if (item.type == 49) type =
                                            'AirLink: არ დგება კავშირზე';
                                        else if (item.type == 50) type =
                                            'AirLink: ანტენა იჭერს ცუდად';
                                        else if (item.type == 51) type =
                                            'AirLink: ინტერნეტის აღდგენა';
                                        else if (item.type == 52) type =
                                            'AirLink: ჯეკის დაზიანება';
                                        else if (item.type == 53) type =
                                            'AirLink: ტელევიზიის ინსტალაცია';
                                        else if (item.type == 54) type =
                                            'AirLink: თანხის ანაზღაურება';
                                        else if (item.type == 55) type =
                                            'ინტერნეტის გადატანა';
                                        else if (item.type == 56) type = 'თანხის ჩამოჭრა';
                                        else if (item.type == 57) type =
                                            'AirLink: დასაბრუნებელია კვების ბლოკი';
                                        else if (item.type == 58) type = 'WiFi მოხსნა';
                                        else if (item.type == 121) type =
                                            'ოპტიკაზე გადასვლა / ინსტალაცია';
                                        else if (item.type == 131) type =
                                            'TV BOX-ის მოხსნა';
                                        else if (item.type == 134) type =
                                            'TVBOX ინსტალაცია';
                                        else if (item.type == 135) type = 'TVBOX-სხვა';
                                        else if (item.type == 137) type = 'მონიტორინგი';
                                        else if (item.type == 152) type = 'WiFi ღირებულება';
                                        else if (item.type == 157) type =
                                            'Network: ONU/ONT არაა კავშირზე';
                                        else if (item.type == 158) type =
                                            'Network: ანტენა არაა კავშირზე';
                                        else if (item.type == 159) type =
                                            'Network: ლინკი არ დგება';
                                        else if (item.type == 160) type =
                                            'Network: მაღალი dBm ONU-ზე (-27>)';
                                        else if (item.type == 161) type =
                                            'Network: მაღალი dBm ანტენაზე (-70>)';
                                        else if (item.type == 162) type =
                                            'Network: IP არ იღებს';




                                        cell1.textContent = item.created;
                                        cell2.innerHTML = `<strong>${type}</strong>`;
                                        cell3.innerHTML =
                                            `<div><strong>${item.user}  ${item.created}</strong></div><div style="padding:5px 0; ">${item.comment}</div>`;

                                        cell1.style.border = '1px solid white';
                                        cell2.style.border = '1px solid white';
                                        cell3.style.border = '1px solid white';

                                        row.classList.add('tr-class');

                                        cell1.classList.add('td-class');
                                        cell2.classList.add('td-class');
                                        cell3.classList.add('td-class');

                                        cell1.style.backgroundColor = '#1e293b';
                                        cell2.style.backgroundColor = '#1e293b';
                                        cell3.style.backgroundColor = '#1e293b';


                                        if (item.status == 1) {
                                            cell1.style.backgroundColor = '#bfffa8';
                                            cell1.style.color = 'black';

                                            cell2.style.backgroundColor = '#bfffa8';
                                            cell2.style.color = 'black';

                                            cell3.style.backgroundColor = '#bfffa8';
                                            cell3.style.color = 'black';
                                        } else if (item.status == 0) {



                                            cell1.style.backgroundColor =
                                                'rgba(247,162,165,1)';
                                            cell1.style.color = 'black';

                                            cell2.style.backgroundColor =
                                                'rgba(247,162,165,1)';
                                            cell2.style.color = 'black';

                                            cell3.style.backgroundColor =
                                                'rgba(247,162,165,1)';
                                            cell3.style.color = 'black';
                                        } else if (item.status == 2 || item.status == 3) {
                                            cell1.style.backgroundColor =
                                                'rgba(204, 204, 0, 1)';
                                            cell1.style.color = 'black';

                                            cell2.style.backgroundColor =
                                                'rgba(204, 204, 0, 1)';
                                            cell2.style.color = 'black';

                                            cell3.style.backgroundColor =
                                                'rgba(204, 204, 0, 1)';
                                            cell3.style.color = 'black';
                                        }

                                        if (item.Attacked.attachedComponents && Array
                                            .isArray(item.Attacked.attachedComponents)) {
                                            item.Attacked.attachedComponents.forEach(
                                                items => {
                                                    var row = table.insertRow();
                                                    var cell1 = row.insertCell(0);
                                                    var cell2 = row.insertCell(1);
                                                    var cell3 = row.insertCell(2);


                                                    let type = '';
                                                    if (items.type == 37) type =
                                                        'AirLink: აბონენტის ჩართვა';
                                                    else if (items.type == 38) type =
                                                        'AirLink: გასარკვევია ადგილზე';
                                                    else if (items.type == 39) type =
                                                        'AirLink: ხაზის დაზიანება';
                                                    else if (items.type == 40) type =
                                                        'AirLink: სიჩქარის პრობლემა';
                                                    else if (items.type == 41) type =
                                                        'AirLink: ანტენის შეცვლა';
                                                    else if (items.type == 42) type =
                                                        'AirLink: გაუქმება';
                                                    else if (items.type == 43) type =
                                                        'AirLink: Wi-Fi ინსტალაცია';
                                                    else if (items.type == 44) type =
                                                        'AirLink: სხვა';
                                                    else if (items.type == 45) type =
                                                        'AirLink: ანტენის შეცვლა/გადატანა';
                                                    else if (items.type == 46) type =
                                                        'AirLink: ფასიანი გამოძახება';
                                                    else if (items.type == 47) type =
                                                        'AirLink: ხელახალი აქტივაციის საფასური';
                                                    else if (items.type == 48) type =
                                                        'AirLink: აპარატურის მოხსნა';
                                                    else if (items.type == 49) type =
                                                        'AirLink: არ დგება კავშირზე';
                                                    else if (items.type == 50) type =
                                                        'AirLink: ანტენა იჭერს ცუდად';
                                                    else if (items.type == 51) type =
                                                        'AirLink: ინტერნეტის აღდგენა';
                                                    else if (items.type == 52) type =
                                                        'AirLink: ჯეკის დაზიანება';
                                                    else if (items.type == 53) type =
                                                        'AirLink: ტელევიზიის ინსტალაცია';
                                                    else if (items.type == 54) type =
                                                        'AirLink: თანხის ანაზღაურება';
                                                    else if (items.type == 55) type =
                                                        'ინტერნეტის გადატანა';
                                                    else if (items.type == 56) type =
                                                        'თანხის ჩამოჭრა';
                                                    else if (items.type == 57) type =
                                                        'AirLink: დასაბრუნებელია კვების ბლოკი';
                                                    else if (items.type == 58) type =
                                                        'WiFi მოხსნა';
                                                    else if (items.type == 121) type =
                                                        'ოპტიკაზე გადასვლა / ინსტალაცია';
                                                    else if (items.type == 131) type =
                                                        'TV BOX-ის მოხსნა';
                                                    else if (items.type == 134) type =
                                                        'TVBOX ინსტალაცია';
                                                    else if (items.type == 135) type =
                                                        'TVBOX-სხვა';
                                                    else if (items.type == 137) type =
                                                        'მონიტორინგი';
                                                    else if (items.type == 152) type =
                                                        'WiFi ღირებულება';


                                                    cell1.textContent = '';
                                                    cell2.innerHTML =
                                                        `<strong>${type}</strong><div style="color:#ff0000;">${items.amount}</div>`;
                                                    cell3.textContent =
                                                        `${items.comment}`;

                                                    cell1.style.border =
                                                        '1px solid white';
                                                    cell2.style.border =
                                                        '1px solid white';
                                                    cell3.style.border =
                                                        '1px solid white';


                                                    row.classList.add('tr-class');

                                                    cell1.classList.add('td-class');
                                                    cell2.classList.add('td-class');
                                                    cell3.classList.add('td-class');


                                                    if (item.status == 1) {
                                                        cell1.style.backgroundColor =
                                                            '#bfffa8';
                                                        cell1.style.color = 'black';

                                                        cell2.style.backgroundColor =
                                                            '#bfffa8';
                                                        cell2.style.color = 'black';

                                                        cell3.style.backgroundColor =
                                                            '#bfffa8';
                                                        cell3.style.color = 'black';
                                                    } else if (item.status == 0) {
                                                        cell1.style.backgroundColor =
                                                            'rgba(247,162,165,1)';
                                                        cell1.style.color = 'black';

                                                        cell2.style.backgroundColor =
                                                            'rgba(247,162,165,1)';
                                                        cell2.style.color = 'black';

                                                        cell3.style.backgroundColor =
                                                            'rgba(247,162,165,1)';
                                                        cell3.style.color = 'black';
                                                    } else if (item.status == 2 || item
                                                        .status == 3) {
                                                        cell1.style.backgroundColor =
                                                            'rgba(204, 204, 0, 1)';
                                                        cell1.style.color = 'black';

                                                        cell2.style.backgroundColor =
                                                            'rgba(204, 204, 0, 1)';
                                                        cell2.style.color = 'black';

                                                        cell3.style.backgroundColor =
                                                            'rgba(204, 204, 0, 1)';
                                                        cell3.style.color = 'black';
                                                    }


                                                });
                                        }

                                    }







                                });


                            }
                        });
                    }

                }
            });

        }


        function History(task_id) {
            document.getElementById("DivData").innerHTML = '';

            var button = document.getElementById('buttonStyle' + task_id);
            if (button) {
                button.style.color = '#64748b';
                button.style.borderColor = '#64748b';
            }

            var clientside = document.getElementById('buttontask' + task_id);
            if (clientside) {
                clientside.style.color = '#64748b';
            }

            var taskside = document.getElementById('buttonclient' + task_id);
            if (taskside) {
                taskside.style.color = '#64748b';
            }


            showModal('custom-History');

            $.ajax({
                url: "/Task-History",
                type: "get",
                data: {
                    'task_id': task_id
                },
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                dataType: "json",
                success: function(result) {
                    let tableRow = '';

                    for (var key in result) {
                        let ResData = '';
                        if (result[key].onu_status == 'Online') {
                            ResData =
                                `<td class="td-class" style="text-align:center;color:#4ade80;"><span  class="inline-flex items-center rounded-md bg-green-400 dark:bg-green-400/20 px-2 py-1 text-xs font-medium text-green-400 ring-1 ring-inset ring-green-600/20"  style="background:rgba(74, 222, 128, .1);">${result[key].onu_status}</span></td>`;
                        } else if (result[key].onu_status == 'Offline') {
                            ResData =
                                `<td class="td-class" style="text-align:center;color:#f87171;"><span class="inline-flex items-center rounded-md bg-red-400 dark:bg-red-400/20 px-2 py-1 text-xs font-medium text-red-400 ring-1 ring-inset ring-red-600/20" style="background:rgba(248, 113, 113, .1);">${result[key].onu_status}</span></td>`;
                        } else if (result[key].onu_status == 'Link Down') {
                            ResData =
                                `<td class="td-class" style="text-align:center;color:#f87171;"><span class="inline-flex items-center rounded-md bg-red-400 dark:bg-red-400/20 px-2 py-1 text-xs font-medium text-red-400 ring-1 ring-inset ring-red-600/20" style="background:rgba(248, 113, 113, .1);">${result[key].onu_status}</span></td>`;
                        } else if (result[key].onu_status == 'Half-10' || result[key].onu_status == 'Full-10' ||
                            result[key].onu_status == 'Half-100' || result[key].onu_status == 'Full-100' ||
                            result[key].onu_status == 'Full-1000') {
                            ResData =
                                `<td class="td-class" style="text-align:center;color:#4ade80;"><span  class="inline-flex items-center rounded-md bg-green-400 dark:bg-green-400/20 px-2 py-1 text-xs font-medium text-green-400 ring-1 ring-inset ring-green-600/20"  style="background:rgba(74, 222, 128, .1);">${result[key].onu_status}</span></td>`;
                        } else if (result[key].onu_status == 'Bound') {
                            ResData =
                                `<td class="td-class" style="text-align:center;color:#4ade80;"><span  class="inline-flex items-center rounded-md bg-green-400 dark:bg-green-400/20 px-2 py-1 text-xs font-medium text-green-400 ring-1 ring-inset ring-green-600/20"  style="background:rgba(74, 222, 128, .1);">${result[key].onu_status}</span></td>`;
                        } else if (result[key].onu_status == 'Link Up') {
                            ResData =
                                `<td class="td-class" style="text-align:center;color:#4ade80;"><span  class="inline-flex items-center rounded-md bg-green-400 dark:bg-green-400/20 px-2 py-1 text-xs font-medium text-green-400 ring-1 ring-inset ring-green-600/20"  style="background:rgba(74, 222, 128, .1);">${result[key].onu_status}</span></td>`;
                        } else if (result[key].onu_status == 'Waiting') {
                            ResData =
                                `<td class="td-class" style="text-align:center;color:#f87171;"><span class="inline-flex items-center rounded-md bg-red-400 dark:bg-red-400/20 px-2 py-1 text-xs font-medium text-red-400 ring-1 ring-inset ring-red-600/20" style="background:rgba(248, 113, 113, .1);">${result[key].onu_status}</span></td>`;
                        } else {
                            ResData =
                                `<td class="td-class" style="text-align:center;">${result[key].onu_status}</td>`;
                        }

                        let Dbm = '';
                        if (result[key].dbm > -27) {
                            Dbm = `<td class="td-class" style="text-align:center;">${result[key].dbm}</td>`;
                        } else if (result[key].dbm < -27 && result[key].dbm > -50) {
                            Dbm =
                                `<td class="td-class" style="color:#fef08a;text-align:center;"><span class="inline-flex items-center rounded-md bg-amber-400 dark:bg-amber-400/20 px-2 py-1 text-xs font-medium text-amber-400 ring-1 ring-inset ring-amber-600/20"  style="background:rgba(251, 191, 36, .1);">${result[key].dbm}</span></td>`;
                        } else if (result[key].dbm < -70) {
                            Dbm =
                                `<td class="td-class" style="color:#fef08a;text-align:center;"><span class="inline-flex items-center rounded-md bg-amber-400 dark:bg-amber-400/20 px-2 py-1 text-xs font-medium text-amber-400 ring-1 ring-inset ring-amber-600/20"  style="background:rgba(251, 191, 36, .1);">${result[key].dbm}</span></td>`;
                        } else {
                            Dbm = `<td class="td-class" style="text-align:center;">${result[key].dbm}</td>`;
                        }

                        tableRow += `
                                <tr class="tr-class">      
                                    <td class="td-class" style="text-align:center;">${result[key].task_id}</td>
                                    <td class="td-class" style="text-align:center;">${result[key].user_id}</td>
                                    <td class="td-class" style="text-align:center;">${result[key].oltName}</td>
                                    ${ResData}
                                    ${Dbm}
                                    <td class="td-class" style="text-align:center;">${result[key].last_update}</td>
                                </tr>
                                `;
                    }

                    document.getElementById("DivData").innerHTML = `
                            <fieldset class="border border-gray-800 p-3 rounded" style="width: 100%;">
                                <legend class="text-sm text-gray-600 font-semibold uppercase tracking-widest">CRON HISTORY</legend>
                                    <div class="flex items-center sm:justify-center ml-4 sm:ml-0" style="width: 100%;">
                                        <table class="text-sm border-separate border-spacing-y-2" style="width: 100%;">
                                            <thead>
                                            <tr  class="tr-class sticky top-0  z-10">
                                                <th class="td-class text-indigo-400 text-center uppercase" style="color:#818cf8;background:#141c3d;letter-spacing: .1em;font-weight: 600;">TASK ID</th>
                                                <th class="td-class text-indigo-400 text-center uppercase" style="color:#818cf8;background:#141c3d;letter-spacing: .1em;font-weight: 600;">USER</th>
                                                <th class="td-class text-indigo-400 text-center uppercase" style="color:#818cf8;background:#141c3d;letter-spacing: .1em;font-weight: 600;">ADDRESS</th>
                                                <th class="td-class text-indigo-400 text-center uppercase" style="color:#818cf8;background:#141c3d;letter-spacing: .1em;font-weight: 600;">RESULT</th>
                                                <th class="td-class text-indigo-400 text-center uppercase" style="color:#818cf8;background:#141c3d;letter-spacing: .1em;font-weight: 600;">Data</th>
                                                <th class="td-class text-indigo-400 text-center uppercase" style="color:#818cf8;background:#141c3d;letter-spacing: .1em;font-weight: 600;">UPDATED</th>
                                            </tr>
                                            </thead>
                                            <tbody>             
                                                ${tableRow}
                                            </tbody>
                                        </table>
                                    </div>
                            </fieldset> 
                        `;

                },
                error: function(xhr, status, error) {
                    showNotification("Error", error, 'warning', 5);
                }
            });
        }

        function DisableFirst(task_id) {
            document.getElementById("task_id").value = task_id;
            showModal('Disable-Confirm');
        }

        function Disable() {
            let task_id = document.getElementById("task_id").value;

            $.ajax({
                url: "/Task-Stop",
                type: "get",
                data: {
                    'task_id': task_id
                },
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                dataType: "json",
                success: function(result) {
                    if (result.error) {
                        showNotification("Error", result.error, 'warning', 5);
                    } else {
                        location.reload();
                    }

                },
                error: function(xhr, status, error) {
                    showNotification("Error", error, 'warning', 5);
                }
            });
        }

        function RestoreFirst(task_id) {
            document.getElementById("task_id").value = task_id;
            showModal('Restore-Confirm');
        }

        function Restore() {
            let task_id = document.getElementById("task_id").value;

            $.ajax({
                url: "/Task-Restore",
                type: "get",
                data: {
                    'task_id': task_id
                },
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                dataType: "json",
                success: function(result) {
                    if (result.error) {
                        showNotification("Error", result.error, 'warning', 5);
                    } else {
                        location.reload();
                    }
                },
                error: function(xhr, status, error) {
                    showNotification("Error", error, 'warning', 5);
                }
            });
        }

        function Same(url) {
            window.open(url, '_blank');
        }
    </script>

</x-app-layout>
