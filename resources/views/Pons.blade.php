<x-app-layout>

<div class="py-12">

    <x-bladewind.notification position="top right" />
    <x-bladewind.notification />

            <div class="w-11/12 mx-auto" style="max-height: 1200px; overflow-y: auto;display:block;"> 

                         
                        <form class="max-w-md mx-left"   method="get" action="{{ route('PonArraySearch') }}" >
                        @csrf
                        <div class="flex">
                            <div class="relative flex-1">
                                    <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                                        <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"/>
                                        </svg>
                                    </div>

                                    @if(isset($param))
                                        <input type="search" value="{{$param}}" name="default_search" class="block w-full p-2 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-gray-800 dark:bg-gray-900  dark:border-gray-600  dark:placeholder-gray-400 dark:text-gray-400  dark:focus:ring-gray-900 dark:focus:border-gray-600" placeholder="Search..." required />
                                    @else   
                                        <input type="search" name="default_search" class="block w-full p-2 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-gray-800 dark:bg-gray-900  dark:border-gray-600  dark:placeholder-gray-400 dark:text-gray-400  dark:focus:ring-gray-900 dark:focus:border-gray-600" placeholder="Search..." required />
                                    @endif
                                    
                                </div>  
                                <button type="submit" class="inline-flex items-center px-4 py-2 ml-2 bg-gray-900 dark:bg-gray-900 border  border-indigo-400 rounded-md font-semibold text-xs text-white dark:text-indigo-400 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-indigo-400  dark:hover:text-gray-900  dark:active:text-indigo-400 ">
                                    SEARCH
                                </button>                               
                            </div>
                        </form>

                        <br>
                        <div class="w-1/2 mx-left"> 
                            <select  id="PonsArraySelect" onchange="MastOrder()"  class="mx-auto sm:w-1/2 dark:bg-gray-900 text-center dark:text-gray-400 text-xs overflow-hidden  sm:rounded-lg   h-9  border border-slate-700 uppercase tracking-widest"> 
                            </select>
                        </div>
                        
                        <div class="flex items-center  justify-center text:xs sm:justify-center ml-4 sm:ml-4"  >
                            <table class="text-sm w-full  border-separate border-spacing-y-2" >                
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

                                 <tbody  id="pon_statistic_table-body">      
                                    <tr  class="tr-class">
                                        <td class="td-class text-indigo-400 text-center text:xs" style="color:#818cf8;background:#141c3d;letter-spacing: .1em;font-weight: 600;text-transform: uppercase;">TYPE</th>
                                        <td class="td-class text-indigo-400 text-center text:xs" style="color:#818cf8;background:#141c3d;letter-spacing: .1em;font-weight: 600;text-transform: uppercase;">ADDRESS</th>
                                        <td class="td-class text-indigo-400 text-center text:xs" style="color:#818cf8;background:#141c3d;letter-spacing: .1em;font-weight: 600;text-transform: uppercase;">NAME</th>
                                        <td class="td-class text-indigo-400 text-center text:xs" style="color:#818cf8;background:#141c3d;letter-spacing: .1em;font-weight: 600;text-transform: uppercase;">POINT</th>
                                        <td class="td-class text-indigo-400 text-center text:xs" style="color:#818cf8;background:#141c3d;letter-spacing: .1em;font-weight: 600;text-transform: uppercase;">TOTAL PONS</th>
                                        <td class="td-class text-indigo-400 text-center text:xs" style="color:#818cf8;background:#141c3d;letter-spacing: .1em;font-weight: 600;text-transform: uppercase;">USED PON</th>
                                        <td class="td-class text-indigo-400 text-center text:xs" style="color:#818cf8;background:#141c3d;letter-spacing: .1em;font-weight: 600;text-transform: uppercase;">FREE PON</th>
                                        <td class="td-class text-indigo-400 text-center text:xs" style="color:#818cf8;background:#141c3d;letter-spacing: .1em;font-weight: 600;text-transform: uppercase;">ONU</th>
                                        <td class="td-class text-indigo-400 text-center text:xs" style="color:#818cf8;background:#141c3d;letter-spacing: .1em;font-weight: 600;text-transform: uppercase;">UPDATED</th> 
                                        <td class="td-class text-indigo-400 text-center text:xs" style="color:#818cf8;background:#141c3d;letter-spacing: .1em;font-weight: 600;text-transform: uppercase;">details</th>    
                                        <td class="td-class text-indigo-400 text-center text:xs" style="color:#818cf8;background:#141c3d;letter-spacing: .1em;font-weight: 600;text-transform: uppercase;">go</th>                    
                                    </tr>
                                    
                                    @foreach($data as $item)
                                    <tr class="tr-class">                                  
                                        <td class="td-class text:xs" style="text-align:center;background:#111b30;letter-spacing: .1em;font-weight: 600;text-transform: uppercase;">{{ $item->Type }}</td>   
                                        <td class="td-class text:xs" style="text-align:center;background:#111b30;letter-spacing: .1em;font-weight: 600;text-transform: uppercase;">{{ $item->Address }}</td>   
                                        <td class="td-class text:xs" style="text-align:center;background:#111b30;letter-spacing: .1em;font-weight: 600;text-transform: uppercase;">{{ $item->device_name }}</td>   
                                        <td class="td-class text:xs" style="text-align:center;background:#111b30;letter-spacing: .1em;font-weight: 600;text-transform: uppercase;">{{ $item->mast }}</td>   
                                        <td class="td-class text:xs" style="text-align:center;background:#111b30;letter-spacing: .1em;font-weight: 600;text-transform: uppercase;">{{ $item->PonCount}}</td>   
                                        <td class="td-class text:xs" style="color:#f87171; text-align:center;background:#111b30;letter-spacing: .1em;font-weight: 600;text-transform: uppercase;">{{ $item->ActivePon}}</td> 
                                        <td class="td-class text:xs" style="color:#86efac; text-align:center;background:#111b30;letter-spacing: .1em;font-weight: 600;text-transform: uppercase;">{{ $item->FreePon}}</td> 
                                        <td class="td-class text:xs" style="text-align:center;background:#111b30;letter-spacing: .1em;font-weight: 600;text-transform: uppercase;">{{ $item->TotalOnu}}</td>   
                                        <td class="td-class text:xs" style="text-align:center;background:#111b30;letter-spacing: .1em;font-weight: 600;text-transform: uppercase;">{{ $item->last_update}}</td> 
                                        <td class="td-class text:xs" style="text-align:center;background:#111b30;letter-spacing: .1em;font-weight: 600;text-transform: uppercase;">
                                            <button onclick="Details('{{ $item->id }}')" type="button" class="inline-flex items-center px-4 py-2 h-8 ml-2  bg-gray-900 dark:bg-gray-900 border  border-indigo-400 rounded-md font-semibold text-xs text-white dark:text-indigo-400 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-indigo-400 dark:hover:text-gray-900  dark:active:text-indigo-400">
                                                details
                                            </button>
                                        </td>   
                                        <td class="td-class text:xs" style="text-align:center;background:#111b30;letter-spacing: .1em;font-weight: 600;text-transform: uppercase;">
                                            <button onclick="Go('{{ $item->Address }}')" type="button" class="inline-flex items-center px-4 py-2 h-8 ml-2  bg-gray-900  dark:bg-gray-900 border  border-red-400 rounded-md font-semibold text-xs text-white dark:text-red-400 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-red-400 dark:hover:text-gray-900  dark:active:text-red-400">
                                                go
                                            </button>
                                        </td> 
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div>
                            {{$data->appends(request()->query())->links()}}
                        </div>
                         
            </div> 
</div>  

 
                    <x-bladewind.modal
                        size="xl"                
                        name="custom-Details"
                        show_close_icon="true"
                        blur_backdrop="false"
                        show_action_buttons="false">
                        <div id="DivData" class="flex items-center justify-center" style="max-height: 700px; overflow-y: auto;display:block;"></div>
                    </x-bladewind.modal>

<script type="text/javascript">

    $(document).ready(function() 
    {
        $.ajax({
                    url: "/getPonsSelect",
                    type: "get",
                    headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                             },
                    dataType: "json",
                    success: function(result)
                    {

                        let SelectData = '<option value=0>აირჩიე ანძა</option>';
                        for (var key in result[0]) 
                        {   
                            SelectData += `<option value="${result[0][key]}">${result[0][key]} </option>`;
                        }

                        document.getElementById("PonsArraySelect").innerHTML = `
                            <select  id="PonsArraySelect"  style="width:100% !important;height:100% !important;" class="w-full mx-auto sm:w-1/2 dark:bg-gray-900 text-center dark:text-gray-400 text-xs overflow-hidden  sm:rounded-lg   h-9  border border-slate-700 uppercase tracking-widest">
                                ${SelectData} 
                            </select>
                        `;

                        $('#PonsArraySelect').select2();
                        $('#PonsArraySelect').select2('close');
                    },
                    error: function (xhr, status, error) 
                    {
                        showNotification("Error", error ,'warning',5);
                    }
            });  
    });

    function MastOrder()
    {
        let mast = $('#PonsArraySelect').val();
        let newUrl = `/MastOrder?mast=${encodeURIComponent(mast)}`;
        window.location.href = newUrl;
    }

    function Go(ip)
    {
        const currentUrl = window.location.origin + '/OLT';
        window.open(`${currentUrl}?id=${ip}`, '_blank');
    }

    function Details(id)
    {
        showModal('custom-Details');

        $.ajax({
                    url: "/PonArray",
                    type: "get",
                    data: {'id':id},
                    headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                             },
                    dataType: "json",
                    success: function(result)
                    {      
                        let tableRow = '';
                        var ponsArray = JSON.parse(result.PonsArray);
 
                        // Sort the array based on the numeric part of ifDescr
                        ponsArray[0].sort((a, b) => {
                            // Extract the numbers from the ifDescr property
                            var numA = parseInt(a.ifDescr.split('/')[1]);
                            var numB = parseInt(b.ifDescr.split('/')[1]);
                            return numA - numB;
                        });
 
                        for (var Zkey in ponsArray[0]) 
                        {
                            var item = ponsArray[0][Zkey];

                          
                            let sfpStyle,volt,curr,tx,temp,Admin;

                            if((item.SFP  == 'Present' || item.SFP == '') && item.AdminState == 'UP')
                            {
                                sfpStyle = `<td class="td-class" style="text-align:center;color:#86efac;">${item.SFP}</td>`;
                                Admin = `<td class="td-class" style="text-align:center;">${item.AdminState}</td>`;

                                if(item.status == 'active')
                                {
                                    volt  = `<td class="td-class" style="text-align:center;">${item.Volt}</td>`;
                                    curr  = `<td class="td-class" style="text-align:center;">${item.Current}</td>`;
                                    tx    = `<td class="td-class" style="text-align:center;">${item.Olt_TX}</td>`;
                                    temp  = `<td class="td-class" style="text-align:center;">${item.PonTemp}</td>`;
                                }
                                else if(result.Type == 'ZTE' || result.Type == 'HUAWEI')
                                {
                                    volt  = `<td class="td-class" style="text-align:center;">${item.Volt}</td>`;
                                    curr  = `<td class="td-class" style="text-align:center;">${item.Current}</td>`;
                                    tx    = `<td class="td-class" style="text-align:center;">${item.Olt_TX}</td>`;
                                    temp  = `<td class="td-class" style="text-align:center;">${item.PonTemp}</td>`;
                                }
                                else if(result.Type == 'BDCOM')
                                {
                                    volt  = `<td class="td-class" style="text-align:center;">-</td>`;
                                    curr  = `<td class="td-class" style="text-align:center;">-</td>`;
                                    tx    = `<td class="td-class" style="text-align:center;">${item.Olt_TX}</td>`;
                                    temp  = `<td class="td-class" style="text-align:center;">-</td>`;
                                }
                                else if(result.Type == 'HSGQ')
                                {
                                    volt  = `<td class="td-class" style="text-align:center;">${item.Volt}</td>`;
                                    curr  = `<td class="td-class" style="text-align:center;">${item.Current}</td>`;
                                    tx    = `<td class="td-class" style="text-align:center;">${item.Olt_TX}</td>`;
                                    temp  = `<td class="td-class" style="text-align:center;">${item.PonTemp}</td>`;
                                }
                                else
                                {
                                    volt = `<td class="td-class" style="text-align:center;">-</td>`;
                                    curr = `<td class="td-class" style="text-align:center;">-</td>`;
                                    tx   = `<td class="td-class" style="text-align:center;">-</td>`;
                                    temp = `<td class="td-class" style="text-align:center;">-</td>`;
                                }
                       
                            }                           
                            else
                            {
                                sfpStyle = `<td class="td-class" style="text-align:center;color:#f87171;">${item.SFP}</td>`;
                                volt = `<td class="td-class" style="text-align:center;">-</td>`;
                                curr = `<td class="td-class" style="text-align:center;">-</td>`;
                                tx   = `<td class="td-class" style="text-align:center;">-</td>`;
                                temp = `<td class="td-class" style="text-align:center;">-</td>`;

                                if(item.AdminState == 'UP')
                                {
                                    Admin = `<td class="td-class" style="text-align:center;">${item.AdminState}</td>`;
                                }
                                else
                                {
                                    Admin = `<td class="td-class" style="text-align:center;color:#f87171;">${item.AdminState}</td>`;
                                }

                            }

                            let status = '';
                            if(item.status !== 'active')
                            {
                                status = `
                                        <td class="td-class text-center justify-center">
                                            <center>
                                                <div class="flex text-center justify-center">                                                  
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="#86efac" class="w-5 h-5">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                                                    </svg> 
                                                    &nbsp; FREE  
                                                </div>
                                            </center>
                                        </td>`;
                            }
                            else
                            {
                                status = `
                                        <td class="td-class text-center justify-center">
                                            <center>
                                                <div class="flex text-center justify-center">                                                
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="#f87171" class="w-5 h-5">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M17.25 6.75 22.5 12l-5.25 5.25m-10.5 0L1.5 12l5.25-5.25m7.5-3-4.5 16.5" />
                                                    </svg> 
                                                    &nbsp; USED  
                                                </div>
                                            </center>
                                        </td>`;
                            }

                            tableRow += `
                                <tr class="tr-class">      
                                    <td class="td-class" style="text-align:center;">${item.ifDescr}</td>
                                    ${status}
                                    <td class="td-class" style="text-align:center;">${item.value}</td>
                                    ${Admin}
                                    ${sfpStyle}
                                    ${tx}
                                    ${temp}
                                    ${volt}
                                    ${curr}
                                </tr>
                            `;
                        }
 

                        document.getElementById("DivData").innerHTML = `
                        <x-bladewind::input required="false"  value="${result.device_name}"
                                    class="text-center  uppercase font-semibold  dark:bg-slate-900 dark:text-indigo-400 text-sm overflow-hidden shadow-sm sm:rounded-lg w-full h-8  border border-slate-900" disabled/>

                            <fieldset class="border border-gray-800 p-3 rounded" style="width: 100%;">
                                <legend class="text-sm text-gray-600 font-semibold uppercase tracking-widest">PONS</legend>
                                    <div class="flex items-center sm:justify-center ml-4 sm:ml-0" style="width: 100%;">
                                        <table class="text-sm border-separate border-spacing-y-2" style="width: 100%;">
                                            <thead>
                                            <tr  class="tr-class sticky top-0  z-10">
                                                <th class="td-class text-indigo-400 text-center uppercase" style="color:#818cf8;background:#141c3d;letter-spacing: .1em;font-weight: 600;">PON</th>
                                                <th class="td-class text-indigo-400 text-center uppercase" style="color:#818cf8;background:#141c3d;letter-spacing: .1em;font-weight: 600;">STATUS</th>
                                                <th class="td-class text-indigo-400 text-center uppercase" style="color:#818cf8;background:#141c3d;letter-spacing: .1em;font-weight: 600;">ONU</th>
                                                <th class="td-class text-indigo-400 text-center uppercase" style="color:#818cf8;background:#141c3d;letter-spacing: .1em;font-weight: 600;">admin</th>
                                                <th class="td-class text-indigo-400 text-center uppercase" style="color:#818cf8;background:#141c3d;letter-spacing: .1em;font-weight: 600;">sfp</th>
                                                <th class="td-class text-indigo-400 text-center uppercase" style="color:#818cf8;background:#141c3d;letter-spacing: .1em;font-weight: 600;">TX</th>
                                                <th class="td-class text-indigo-400 text-center uppercase" style="color:#818cf8;background:#141c3d;letter-spacing: .1em;font-weight: 600;">TEMPERATURE</th>
                                                <th class="td-class text-indigo-400 text-center uppercase" style="color:#818cf8;background:#141c3d;letter-spacing: .1em;font-weight: 600;">VOLTAGE</th>
                                                <th class="td-class text-indigo-400 text-center uppercase" style="color:#818cf8;background:#141c3d;letter-spacing: .1em;font-weight: 600;">CURRENT</th>
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
                    error: function (xhr, status, error) 
                    {
                        showNotification("Error", error ,'warning',5);
                    }
                });
    }

</script>

</x-app-layout>