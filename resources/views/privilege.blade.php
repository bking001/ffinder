<x-app-layout>


    <div class="py-12">

        <x-bladewind.notification position="top right" />
        <x-bladewind.notification />

            <div class="w-11/12 mx-auto">
            <form class="max-w-md mx-left"   method="post" action="{{ route('Privilege.search') }}" >
            @csrf
                <div class="flex">
                    <div class="relative flex-1">
                            <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                                <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"/>
                            </svg>
                        </div>
                        <input type="search" name="default_search_priv" class="block w-full p-2 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-gray-800 dark:bg-gray-900  dark:border-gray-600  dark:placeholder-gray-400 dark:text-white  dark:focus:ring-gray-900 dark:focus:border-gray-600" placeholder="Search..." required />
                    </div>
                    <button type="submit" class="inline-flex items-center px-4 py-2 ml-2 bg-gray-900 dark:bg-gray-900 border  border-indigo-400 rounded-md font-semibold text-xs text-white dark:text-indigo-400 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-indigo-400  dark:hover:text-gray-900  dark:active:text-indigo-400">
                    SEARCH</button>
                </div>
            </form><br>

            @if (session('status'))
            <div class="flex flex-col justify-center items-center text-sm text-red-600 dark:text-green-400 space-y-1 mt-2 mx-auto" x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)">
                {{  session('status') }}  
            </div>
            @else (session('error'))
                <div class="flex flex-col justify-center items-center text-sm text-red-600 dark:text-red-400 space-y-1 mt-2 mx-auto">
                    {{ session('error') }} 
                </div>
            @endif


            <div class="flex items-center  justify-center text:xs sm:justify-center ml-4 sm:ml-4"  >
                <table class="text-sm  w-full border-separate border-spacing-y-2" >                
                    <thead class="sr-only">
                            <tr class="tr-class">
                                <th class="text-indigo-400">PON</th>
                                <th class="text-indigo-400">PON</th>
                                <th class="text-indigo-400">PON</th>
                                <th class="text-indigo-400">PON</th>
                                <th class="text-indigo-400">PON</th>
                                <th class="text-indigo-400">PON</th>
                                <th class="text-indigo-400">PON</th>
                                <th class="text-indigo-400">PON</th>
                                <th class="text-indigo-400">PON</th>
                                <th class="text-indigo-400">PON</th>
                            </tr>
                     </thead>

                     <tbody  id="pon_statistic_table-body">      
                        <tr  class="tr-class">
                            <td class="td-class text-indigo-400 text-center text:xs" style="color:#818cf8;background:#141c3d;letter-spacing: .1em;font-weight: 600;text-transform: uppercase;">Display Name</th>
                            <td class="td-class text-indigo-400 text-center text:xs" style="color:#818cf8;background:#141c3d;letter-spacing: .1em;font-weight: 600;text-transform: uppercase;">Access</th>
                            <td class="td-class text-indigo-400 text-center text:xs" style="color:#818cf8;background:#141c3d;letter-spacing: .1em;font-weight: 600;text-transform: uppercase;">Devices</th>    
                            <td class="td-class text-indigo-400 text-center text:xs" style="color:#818cf8;background:#141c3d;letter-spacing: .1em;font-weight: 600;text-transform: uppercase;">Onu</th>                  
                            <td class="td-class text-indigo-400 text-center text:xs" style="color:#818cf8;background:#141c3d;letter-spacing: .1em;font-weight: 600;text-transform: uppercase;">Pon</th> 
                            <td class="td-class text-indigo-400 text-center text:xs" style="color:#818cf8;background:#141c3d;letter-spacing: .1em;font-weight: 600;text-transform: uppercase;">Uplink</th>
                            <td class="td-class text-indigo-400 text-center text:xs" style="color:#818cf8;background:#141c3d;letter-spacing: .1em;font-weight: 600;text-transform: uppercase;">Vlan</th>
                            <td class="td-class text-indigo-400 text-center text:xs" style="color:#818cf8;background:#141c3d;letter-spacing: .1em;font-weight: 600;text-transform: uppercase;">Install</th>    
                            <td class="td-class text-indigo-400 text-center text:xs" style="color:#818cf8;background:#141c3d;letter-spacing: .1em;font-weight: 600;text-transform: uppercase;">Board</th>                  
                            <td class="td-class text-indigo-400 text-center text:xs" style="color:#818cf8;background:#141c3d;letter-spacing: .1em;font-weight: 600;text-transform: uppercase;">Logs</th>                                   
                        </tr>
                        
                        @foreach($data as $item)
                        <tr class="tr-class">                                    
                            <td class="td-class  text:xs" style="text-align:center;background:#111b30;letter-spacing: .1em;font-weight: 600;text-transform: uppercase;">
                                {{ $item['dn'] }}
                            </td>   

                            <td class="td-class text:xs" style="text-align:center;background:#111b30;letter-spacing: .1em;font-weight: 600;text-transform: uppercase;">
                                {!! $item['access'] === 1 ? '<input onclick="Disabled_Enabled(\'' . $item['username'] . '\',\'' . $item['dn'] . '\', \'access\')" type="checkbox" class="shadow-Finder shrink-0 mt-0.5 border-gray-200 rounded text-indigo-600 focus:ring-indigo-500 disabled:opacity-50 dark:bg-gray-800 dark:border-gray-700 dark:checked:bg-indigo-700 dark:checked:border-indigo-700 dark:focus:ring-offset-indigo-700" id="access_' . $item['username'] . '" checked>' : '<input onclick="Disabled_Enabled(\'' . $item['username'] . '\',\'' . $item['dn'] . '\', \'access\')" type="checkbox" class="shrink-0 mt-0.5 border-gray-200 rounded text-indigo-600 focus:ring-indigo-500 disabled:opacity-50 dark:bg-gray-800 dark:border-gray-700 dark:checked:bg-indigo-700 dark:checked:border-indigo-700 dark:focus:ring-offset-indigo-700" id="access_' . $item['username'] . '">' !!}
                            </td>  

                            <td class="td-class text:xs" style="text-align:center;background:#111b30;letter-spacing: .1em;font-weight: 600;text-transform: uppercase;">
                                {!! $item['Devices'] === 1 ? '<input onclick="Disabled_Enabled(\'' . $item['username'] . '\',\'' . $item['dn'] . '\', \'Devices\')" type="checkbox" class="shadow-Finder shrink-0 mt-0.5 border-gray-200 rounded text-indigo-600 focus:ring-indigo-500 disabled:opacity-50 dark:bg-gray-800 dark:border-gray-700 dark:checked:bg-indigo-700 dark:checked:border-indigo-700 dark:focus:ring-offset-indigo-700" id="Devices_' . $item['username'] . '" checked>' : '<input onclick="Disabled_Enabled(\'' . $item['username'] . '\',\'' . $item['dn'] . '\', \'Devices\')" type="checkbox" class="shrink-0 mt-0.5 border-gray-200 rounded text-indigo-600 focus:ring-indigo-500 disabled:opacity-50 dark:bg-gray-800 dark:border-gray-700 dark:checked:bg-indigo-700 dark:checked:border-indigo-700 dark:focus:ring-offset-indigo-700" id="Devices_' . $item['username'] . '">' !!}
                            </td>  

                            <td class="td-class text:xs" style="text-align:center;background:#111b30;letter-spacing: .1em;font-weight: 600;text-transform: uppercase;">
                                {!! $item['Priv_Onu'] === 1 ? '<input onclick="Disabled_Enabled(\'' . $item['username'] . '\',\'' . $item['dn'] . '\', \'Priv_Onu\')" type="checkbox" class="shadow-Finder shrink-0 mt-0.5 border-gray-200 rounded text-indigo-600 focus:ring-indigo-500 disabled:opacity-50 dark:bg-gray-800 dark:border-gray-700 dark:checked:bg-indigo-700 dark:checked:border-indigo-700 dark:focus:ring-offset-indigo-700" id="Priv_Onu_' . $item['username'] . '" checked>' : '<input onclick="Disabled_Enabled(\'' . $item['username'] . '\',\'' . $item['dn'] . '\',\'Priv_Onu\')" type="checkbox" class="shrink-0 mt-0.5 border-gray-200 rounded text-indigo-600 focus:ring-indigo-500 disabled:opacity-50 dark:bg-gray-800 dark:border-gray-700 dark:checked:bg-indigo-700 dark:checked:border-indigo-700 dark:focus:ring-offset-indigo-700" id="Priv_Onu_' . $item['username'] . '">' !!}
                            </td>  

                            <td class="td-class text:xs" style="text-align:center;vertical-align: baseline;background:#111b30;letter-spacing: .1em;font-weight: 600;text-transform: uppercase;">
                                {!! $item['Priv_Pon'] === 1 ? '<input onclick="Disabled_Enabled(\'' . $item['username'] . '\',\'' . $item['dn'] . '\', \'Priv_Pon\')" type="checkbox" class="shadow-Finder shrink-0 mt-0.5 border-gray-200 rounded text-indigo-600 focus:ring-indigo-500 disabled:opacity-50 dark:bg-gray-800 dark:border-gray-700 dark:checked:bg-indigo-700 dark:checked:border-indigo-700 dark:focus:ring-offset-indigo-700" id="Priv_Pon_' . $item['username'] . '" checked>' : '<input onclick="Disabled_Enabled(\'' . $item['username'] . '\',\'' . $item['dn'] . '\',\'Priv_Pon\')" type="checkbox" class="shrink-0 mt-0.5 border-gray-200 rounded text-indigo-600 focus:ring-indigo-500 disabled:opacity-50 dark:bg-gray-800 dark:border-gray-700 dark:checked:bg-indigo-700 dark:checked:border-indigo-700 dark:focus:ring-offset-indigo-700" id="Priv_Pon_' . $item['username'] . '">' !!}
                            </td>    

                            <td class="td-class text:xs" style="text-align:center;vertical-align: baseline;background:#111b30;letter-spacing: .1em;font-weight: 600;text-transform: uppercase;">
                                {!! $item['Priv_Uplink'] === 1 ? '<input onclick="Disabled_Enabled(\'' . $item['username'] . '\',\'' . $item['dn'] . '\', \'Priv_Uplink\')" type="checkbox" class="shadow-Finder shrink-0 mt-0.5 border-gray-200 rounded text-indigo-600 focus:ring-indigo-500 disabled:opacity-50 dark:bg-gray-800 dark:border-gray-700 dark:checked:bg-indigo-700 dark:checked:border-indigo-700 dark:focus:ring-offset-indigo-700" id="Priv_Uplink_' . $item['username'] . '" checked>' : '<input onclick="Disabled_Enabled(\'' . $item['username'] . '\',\'' . $item['dn'] . '\',\'Priv_Uplink\')" type="checkbox" class="shrink-0 mt-0.5 border-gray-200 rounded text-indigo-600 focus:ring-indigo-500 disabled:opacity-50 dark:bg-gray-800 dark:border-gray-700 dark:checked:bg-indigo-700 dark:checked:border-indigo-700 dark:focus:ring-offset-indigo-700" id="Priv_Uplink_' . $item['username'] . '">' !!}
                            </td>

                            <td class="td-class text:xs" style="text-align:center;vertical-align: baseline;background:#111b30;letter-spacing: .1em;font-weight: 600;text-transform: uppercase;">
                                {!! $item['Priv_Vlan'] === 1 ? '<input onclick="Disabled_Enabled(\'' . $item['username'] . '\',\'' . $item['dn'] . '\', \'Priv_Vlan\')" type="checkbox" class="shadow-Finder shrink-0 mt-0.5 border-gray-200 rounded text-indigo-600 focus:ring-indigo-500 disabled:opacity-50 dark:bg-gray-800 dark:border-gray-700 dark:checked:bg-indigo-700 dark:checked:border-indigo-700 dark:focus:ring-offset-indigo-700" id="Priv_Vlan_' . $item['username'] . '" checked>' : '<input onclick="Disabled_Enabled(\'' . $item['username'] . '\',\'' . $item['dn'] . '\',\'Priv_Vlan\')" type="checkbox" class="shrink-0 mt-0.5 border-gray-200 rounded text-indigo-600 focus:ring-indigo-500 disabled:opacity-50 dark:bg-gray-800 dark:border-gray-700 dark:checked:bg-indigo-700 dark:checked:border-indigo-700 dark:focus:ring-offset-indigo-700" id="Priv_Vlan_' . $item['username'] . '">' !!}
                            </td>

                            <td class="td-class text:xs" style="text-align:center;vertical-align: baseline;background:#111b30;letter-spacing: .1em;font-weight: 600;text-transform: uppercase;">
                                {!! $item['Priv_Install'] === 1 ? '<input onclick="Disabled_Enabled(\'' . $item['username'] . '\',\'' . $item['dn'] . '\', \'Priv_Install\')" type="checkbox" class="shadow-Finder shrink-0 mt-0.5 border-gray-200 rounded text-indigo-600 focus:ring-indigo-500 disabled:opacity-50 dark:bg-gray-800 dark:border-gray-700 dark:checked:bg-indigo-700 dark:checked:border-indigo-700 dark:focus:ring-offset-indigo-700" id="Priv_Install_' . $item['username'] . '" checked>' : '<input onclick="Disabled_Enabled(\'' . $item['username'] . '\',\'' . $item['dn'] . '\',\'Priv_Install\')" type="checkbox" class="shrink-0 mt-0.5 border-gray-200 rounded text-indigo-600 focus:ring-indigo-500 disabled:opacity-50 dark:bg-gray-800 dark:border-gray-700 dark:checked:bg-indigo-700 dark:checked:border-indigo-700 dark:focus:ring-offset-indigo-700" id="Priv_Install_' . $item['username'] . '">' !!}
                            </td>

                            <td class="td-class text:xs" style="text-align:center;vertical-align: baseline;background:#111b30;letter-spacing: .1em;font-weight: 600;text-transform: uppercase;">
                                {!! $item['Priv_Board'] === 1 ? '<input onclick="Disabled_Enabled(\'' . $item['username'] . '\',\'' . $item['dn'] . '\', \'Priv_Board\')" type="checkbox" onclick- class="shadow-Finder shrink-0 mt-0.5 border-gray-200 rounded text-indigo-600 focus:ring-indigo-500 disabled:opacity-50 dark:bg-gray-800 dark:border-gray-700 dark:checked:bg-indigo-700 dark:checked:border-indigo-700 dark:focus:ring-offset-indigo-700" id="Priv_Board_' . $item['username'] . '" checked>' : '<input onclick="Disabled_Enabled(\'' . $item['username'] . '\',\'' . $item['dn'] . '\', \'Priv_Board\')" type="checkbox" class="shrink-0 mt-0.5 border-gray-200 rounded text-indigo-600 focus:ring-indigo-500 disabled:opacity-50 dark:bg-gray-800 dark:border-gray-700 dark:checked:bg-indigo-700 dark:checked:border-indigo-700 dark:focus:ring-offset-indigo-700" id="Priv_Board_' . $item['username'] . '">' !!}
                            </td>

                            <td class="td-class text:xs" style="text-align:center;vertical-align: baseline;background:#111b30;letter-spacing: .1em;font-weight: 600;text-transform: uppercase;">
                                {!! $item['Priv_Log'] === 1 ? '<input onclick="Disabled_Enabled(\'' . $item['username'] . '\',\'' . $item['dn'] . '\', \'Priv_Log\')" type="checkbox" onclick- class="shadow-Finder shrink-0 mt-0.5 border-gray-200 rounded text-indigo-600 focus:ring-indigo-500 disabled:opacity-50 dark:bg-gray-800 dark:border-gray-700 dark:checked:bg-indigo-700 dark:checked:border-indigo-700 dark:focus:ring-offset-indigo-700" id="Priv_Board_' . $item['username'] . '" checked>' : '<input onclick="Disabled_Enabled(\'' . $item['username'] . '\',\'' . $item['dn'] . '\', \'Priv_Log\')" type="checkbox" class="shrink-0 mt-0.5 border-gray-200 rounded text-indigo-600 focus:ring-indigo-500 disabled:opacity-50 dark:bg-gray-800 dark:border-gray-700 dark:checked:bg-indigo-700 dark:checked:border-indigo-700 dark:focus:ring-offset-indigo-700" id="Priv_Log_' . $item['username'] . '">' !!}
                            </td>

                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
       

                    {{ $data->appends(request()->query())->links() }}
            </div>
        </div>



    <script type="text/javascript">
        function Disabled_Enabled(username,dn,column)
        {
            var data = { 'username': username, 'dn':dn,'column': column };   

            $.ajax({
                        url: "/privilegeOfforon",
                        type: "POST",
                        data: JSON.stringify(data),
                        dataType: "html",
                        headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                },
                        async :true,
                        success: function(result)
                        {
                            showNotification('Privilages', dn + ' updated successfully','success',1); 
                        },
                        error: function (xhr, status, error) 
                        {
                            showNotification("Can't Update Data", error ,'warning',5);
                        }
                    });
        }
    </script>

</x-app-layout>

