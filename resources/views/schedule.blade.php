<x-app-layout>

<div class="py-12"> 

<x-bladewind.notification position="top right" />
<x-bladewind.notification />

    <div class="w-11/12 mx-auto"> 

                            <div class="flex items-center  justify-center text:xs sm:justify-center ml-4 sm:ml-4"  >
                                <table class="text-sm w-full  border-separate border-spacing-y-2" >                
                                    <thead class="sr-only">
                                            <tr class="tr-class">
                                                <th class="text-indigo-400">PON</th>
                                                <th class="text-indigo-400">PON</th>
                                                <th class="text-indigo-400">PON</th>
                                                <th class="text-indigo-400">PON</th>
                                                <th class="text-indigo-400">PON</th>
                                            </tr>
                                    </thead>

                                    <tbody  id="pon_statistic_table-body">      
                                        <tr  class="tr-class">
                                            <td class="td-class text-indigo-400 text-center text:xs" style="color:#818cf8;background:#141c3d;letter-spacing: .1em;font-weight: 600;text-transform: uppercase;">COMMAND</th>
                                            <td class="td-class text-indigo-400 text-center text:xs" style="color:#818cf8;background:#141c3d;letter-spacing: .1em;font-weight: 600;text-transform: uppercase;">CRON</th>
                                            <td class="td-class text-indigo-400 text-center text:xs" style="color:#818cf8;background:#141c3d;letter-spacing: .1em;font-weight: 600;text-transform: uppercase;">OPTION</th>
                                            <td class="td-class text-indigo-400 text-center text:xs" style="color:#818cf8;background:#141c3d;letter-spacing: .1em;font-weight: 600;text-transform: uppercase;">timezone</th>
                                            <td class="td-class text-indigo-400 text-center text:xs" style="color:#818cf8;background:#141c3d;letter-spacing: .1em;font-weight: 600;text-transform: uppercase;">NEXT RUN</th>
                                            <td class="td-class text-indigo-400 text-center text:xs" style="color:#818cf8;background:#141c3d;letter-spacing: .1em;font-weight: 600;text-transform: uppercase;">COUNTDOWN</th>   
                                            <td class="td-class text-indigo-400 text-center text:xs" style="color:#818cf8;background:#141c3d;letter-spacing: .1em;font-weight: 600;text-transform: uppercase;">status</th>          
                                        </tr>
                                        
                                
                                
                                            @foreach($eventDetails as $item)
                                            <tr class="tr-class">
                                                <td class="td-class text:xs" style="text-align:center;background:#111b30;letter-spacing: .1em;font-weight: 600;text-transform: uppercase;">{{ $item['command'] }}</td>
                                                <td class="td-class text:xs" style="text-align:center;background:#111b30;letter-spacing: .1em;font-weight: 600;text-transform: uppercase;">{{ $item['expression'] }}</td>
                                                <td class="td-class text:xs" style="text-align:center;background:#111b30;letter-spacing: .1em;font-weight: 600;text-transform: uppercase;">{{ $item['readable'] }}</td>
                                                <td class="td-class text:xs" style="text-align:center;background:#111b30;letter-spacing: .1em;font-weight: 600;text-transform: uppercase;">{{ $item['timezone'] }}</td>
                                                <td class="td-class text:xs" style="text-align:center;background:#111b30;letter-spacing: .1em;font-weight: 600;text-transform: uppercase;">{{ $item['next_run'] }}</td>
                                                <td class="td-class text:xs" style="text-align:center;background:#111b30;letter-spacing: .1em;font-weight: 600;text-transform: uppercase;">{{ $item['time_until_next_run'] }}</td> 
                                                
                                                @if ($item['status'] == 1)                                              
                                                    <td class="td-class text:xs" style="text-align:center;background:#111b30;letter-spacing: .1em;font-weight: 600;text-transform: uppercase;">
                                                        <button onclick="stopScheduler('{{ $item['command'] }}')" type="button" class="inline-flex items-center px-4 py-2 h-8 ml-2  bg-gray-900 dark:bg-gray-900 border  border-indigo-400 rounded-md font-semibold text-xs text-white dark:text-indigo-400 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-indigo-400 dark:hover:text-gray-900  dark:active:text-indigo-400">
                                                            RUNNING
                                                        </button>
                                                    </td>  
                                                @else
                                                    <td class="td-class text:xs" style="text-align:center;background:#111b30;letter-spacing: .1em;font-weight: 600;text-transform: uppercase;">
                                                        <button onclick="startScheduler('{{ $item['command'] }}')" type="button" class="inline-flex items-center px-4 py-2 h-8 ml-2  bg-gray-900  dark:bg-gray-900 border  border-red-400 rounded-md font-semibold text-xs text-white dark:text-red-400 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-red-400 dark:hover:text-gray-900  dark:active:text-red-400">
                                                            PAUSED
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

    <script type="text/javascript">
        function stopScheduler(command)
        {
                        $.ajax({
                                    url: "/stopScheduler",
                                    type: "GET",
                                    data: {'command':command},
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                    },
                                    dataType: "json",
                                    success: function(result)
                                    {  
                                        location.reload();                               
                                    },
                                    error: function (xhr, status, error) 
                                    {
                                        showNotification("Error", error ,'warning',5);
                                    }
                                });
        }

        function startScheduler(command)
        {
            $.ajax({
                                    url: "/startScheduler",
                                    type: "GET",
                                    data: {'command':command},
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                    },
                                    dataType: "json",
                                    success: function(result)
                                    {  
                                        location.reload();                               
                                    },
                                    error: function (xhr, status, error) 
                                    {
                                        showNotification("Error", error ,'warning',5);
                                    }
                                });          
        }

    </script>

</x-app-layout>