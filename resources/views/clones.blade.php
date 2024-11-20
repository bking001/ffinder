<x-app-layout>

    <div class="py-12">
        <x-bladewind.notification position="top right" />
        <x-bladewind.notification />

        <div class="w-11/12 mx-auto" style="max-height: 1200px; overflow-y: auto;display:block;"> 
        
                       
            <form class="max-w-md mx-left"   method="get" action="{{ route('ClonesSearch') }}" >
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
                            <td class="td-class text-indigo-400 text-center text:xs" style="color:#818cf8;background:#141c3d;letter-spacing: .1em;font-weight: 600;text-transform: uppercase;">DESCRIPTION</th>
                            <td class="td-class text-indigo-400 text-center text:xs" style="color:#818cf8;background:#141c3d;letter-spacing: .1em;font-weight: 600;text-transform: uppercase;">PON PORT</th>
                            <td class="td-class text-indigo-400 text-center text:xs" style="color:#818cf8;background:#141c3d;letter-spacing: .1em;font-weight: 600;text-transform: uppercase;">MAC SN</th>
                            <td class="td-class text-indigo-400 text-center text:xs" style="color:#818cf8;background:#141c3d;letter-spacing: .1em;font-weight: 600;text-transform: uppercase;">UPDATED</th>
                            <td class="td-class text-indigo-400 text-center text:xs" style="color:#818cf8;background:#141c3d;letter-spacing: .1em;font-weight: 600;text-transform: uppercase;">CHECK</th>
                            <td class="td-class text-indigo-400 text-center text:xs" style="color:#818cf8;background:#141c3d;letter-spacing: .1em;font-weight: 600;text-transform: uppercase;">MAC</th>
                            <td class="td-class text-indigo-400 text-center text:xs" style="color:#818cf8;background:#141c3d;letter-spacing: .1em;font-weight: 600;text-transform: uppercase;">EDIT</th>
                            <td class="td-class text-indigo-400 text-center text:xs" style="color:#818cf8;background:#141c3d;letter-spacing: .1em;font-weight: 600;text-transform: uppercase;">DELETE</th>                
                        </tr>
                        
                        @foreach($data as $key => $item)
 
                            @if ($item->ByType == 'MAC')
                                <tr class="tr-class">                                  
                                    <td class="td-class text:xs" style="text-align:center;background:#111b30;letter-spacing: .1em;font-weight: 600;text-transform: uppercase;">{{ $item->oltType }}</td>   
                                    <td class="td-class text:xs" style="text-align:center;background:#111b30;letter-spacing: .1em;font-weight: 600;text-transform: uppercase;">{{ $item->oltAddress }}</td>   
                                    <td class="td-class text:xs" style="text-align:center;background:#111b30;letter-spacing: .1em;font-weight: 600;text-transform: uppercase;">{{ $item->oltName }}</td>   
                                    <td class="td-class text:xs" style="text-align:center;background:#111b30;letter-spacing: .1em;font-weight: 600;text-transform: uppercase;">{{ $item->onuDescr }}</td>   
                                    <td class="td-class text:xs" style="text-align:center;background:#111b30;letter-spacing: .1em;font-weight: 600;text-transform: uppercase;">{{ $item->ponPort}}</td>  
                                    <td class="td-class text:xs" style="color: #efc776;text-align:center;background:#111b30;letter-spacing: .1em;font-weight: 600;text-transform: uppercase;"><span class="inline-flex items-center rounded-md bg-amber-400 dark:bg-amber-400/20 px-2 py-1 text-xs font-medium text-amber-400 ring-1 ring-inset ring-amber-600/20"  style="background:rgba(251, 191, 36, .1);">{{ $item->onuMac}}</span></td> 
                                    <td class="td-class text:xs" style="text-align:center;background:#111b30;letter-spacing: .1em;font-weight: 600;text-transform: uppercase;">{{ $item->last_update}}</td>            
                                    <td class="td-class text:xs" style="text-align:center;background:#111b30;letter-spacing: .1em;font-weight: 600;text-transform: uppercase;">
                                        <button onclick="checkClonePon('{{$item->oltAddress}}')" class="inline-flex items-center px-4 py-2 h-8 ml-2  bg-gray-900 dark:bg-gray-900 border  border-indigo-400 rounded-md font-semibold text-xs text-white dark:text-indigo-400 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-indigo-400 dark:hover:text-gray-900  dark:active:text-indigo-400"   type="button">
                                            CHECK
                                        </button>
                                        
                                    </td> 
                                    <td class="td-class text:xs" style="text-align:center;background:#111b30;letter-spacing: .1em;font-weight: 600;text-transform: uppercase;">
                                        <!-- <button onclick="macGSearch('{{$item->onuMac}}')" class="inline-flex items-center px-4 py-2 h-8 ml-2  bg-gray-900 dark:bg-gray-900 border  border-indigo-400 rounded-md font-semibold text-xs text-white dark:text-indigo-400 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-indigo-400 dark:hover:text-gray-900  dark:active:text-indigo-400"   type="button">
                                            MAC
                                        </button> -->
                                        <a href="{{ url('/GlobalSearch?id=' . $item->onuMac) }}" target="_blank" class="inline-flex items-center px-4 py-2 h-8 ml-2  bg-gray-900 dark:bg-gray-900 border  border-indigo-400 rounded-md font-semibold text-xs text-white dark:text-indigo-400 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-indigo-400 dark:hover:text-gray-900  dark:active:text-indigo-400">
                                            MAC
                                        </a>
                                    </td> 
                                    <td class="td-class text:xs" id="DescriptionButton{{$key}}"  style="text-align:center;background:#111b30;letter-spacing: .1em;font-weight: 600;text-transform: uppercase;">
                                        <button   onclick="Edit_Clone_Description('{{$item->oltType}}','{{$item->oltAddress}}','{{$item->ifindex}}','{{$item->onuDescr}}','{{$item->onuMac}}','{{$key}}')" class="inline-flex items-center px-4 py-2 h-8 ml-2  bg-gray-900 dark:bg-gray-900 border  border-indigo-400 rounded-md font-semibold text-xs text-white dark:text-indigo-400 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-indigo-400 dark:hover:text-gray-900  dark:active:text-indigo-400"   type="button">
                                            DESCRIPTION 
                                        </button>
                                    </td> 
                                    <td class="td-class text:xs" id="DeleteButton{{$key}}" style="text-align:center;background:#111b30;letter-spacing: .1em;font-weight: 600;text-transform: uppercase;">
                                        <button    onclick="CloneDelete('{{$item->oltType}}','{{$item->oltAddress}}','{{$item->ifindex}}','{{$item->onuDescr}}','{{$item->onuMac}}','{{$key}}')"  class="inline-flex items-center px-4 py-2 h-8 ml-2  bg-gray-900  dark:bg-gray-900 border  border-red-400 rounded-md font-semibold text-xs text-white dark:text-red-400 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-red-400 dark:hover:text-gray-900  dark:active:text-red-400"  type="button">
                                            DELETE
                                        </button>
                                    </td>   
                                </tr>
                            @elseif ($item->ByType == 'DESCRIPTION')
                                <tr class="tr-class">                                  
                                    <td class="td-class text:xs" style="text-align:center;background:#111b30;letter-spacing: .1em;font-weight: 600;text-transform: uppercase;">{{ $item->oltType }}</td>   
                                    <td class="td-class text:xs" style="text-align:center;background:#111b30;letter-spacing: .1em;font-weight: 600;text-transform: uppercase;">{{ $item->oltAddress }}</td>   
                                    <td class="td-class text:xs" style="text-align:center;background:#111b30;letter-spacing: .1em;font-weight: 600;text-transform: uppercase;">{{ $item->oltName }}</td>   
                                    <td class="td-class text:xs" style="color: #efc776;text-align:center;background:#111b30;letter-spacing: .1em;font-weight: 600;text-transform: uppercase;"><span class="inline-flex items-center rounded-md bg-amber-400 dark:bg-amber-400/20 px-2 py-1 text-xs font-medium text-amber-400 ring-1 ring-inset ring-amber-600/20"  style="background:rgba(251, 191, 36, .1);">{{ $item->onuDescr}}</span></td>   
                                    <td class="td-class text:xs" style="text-align:center;background:#111b30;letter-spacing: .1em;font-weight: 600;text-transform: uppercase;">{{ $item->ponPort }}</td>  
                                    <td class="td-class text:xs" style="text-align:center;background:#111b30;letter-spacing: .1em;font-weight: 600;text-transform: uppercase;">{{ $item->onuMac }}</td> 
                                    <td class="td-class text:xs" style="text-align:center;background:#111b30;letter-spacing: .1em;font-weight: 600;text-transform: uppercase;">{{ $item->last_update}}</td> 
                                    <td class="td-class text:xs" style="text-align:center;background:#111b30;letter-spacing: .1em;font-weight: 600;text-transform: uppercase;">
                                        <button onclick="checkClonePon('{{$item->oltAddress}}')" class="inline-flex items-center px-4 py-2 h-8 ml-2  bg-gray-900 dark:bg-gray-900 border  border-indigo-400 rounded-md font-semibold text-xs text-white dark:text-indigo-400 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-indigo-400 dark:hover:text-gray-900  dark:active:text-indigo-400"   type="button">
                                            CHECK
                                        </button>
                                    </td> 
                                    <td class="td-class text:xs" style="text-align:center;background:#111b30;letter-spacing: .1em;font-weight: 600;text-transform: uppercase;">
                                        <!-- <button onclick="macGSearch('{{$item->onuMac}}')" class="inline-flex items-center px-4 py-2 h-8 ml-2  bg-gray-900 dark:bg-gray-900 border  border-indigo-400 rounded-md font-semibold text-xs text-white dark:text-indigo-400 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-indigo-400 dark:hover:text-gray-900  dark:active:text-indigo-400"   type="button">
                                            MAC
                                        </button> -->
 
                                        <a href="{{ url('/GlobalSearch?id=' . $item->onuMac) }}" target="_blank" class="inline-flex items-center px-4 py-2 h-8 ml-2 bg-gray-900 dark:bg-gray-900 border border-indigo-400 rounded-md font-semibold text-xs text-white dark:text-indigo-400 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-indigo-400 dark:hover:text-gray-900 dark:active:text-indigo-400">
                                            MAC
                                        </a>
                                    </td> 
                                    <td class="td-class text:xs" id="DescriptionButton{{$key}}" style="text-align:center;background:#111b30;letter-spacing: .1em;font-weight: 600;text-transform: uppercase;">
                                        <button   onclick="Edit_Clone_Description('{{$item->oltType}}','{{$item->oltAddress}}','{{$item->ifindex}}','{{$item->onuDescr}}','{{$item->onuMac}}','{{$key}}')" class="inline-flex items-center px-4 py-2 h-8 ml-2  bg-gray-900 dark:bg-gray-900 border  border-indigo-400 rounded-md font-semibold text-xs text-white dark:text-indigo-400 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-indigo-400 dark:hover:text-gray-900  dark:active:text-indigo-400"   type="button">
                                            DESCRIPTION
                                        </button>
                                    </td> 
                                    <td class="td-class text:xs" id="DeleteButton{{$key}}" style="text-align:center;background:#111b30;letter-spacing: .1em;font-weight: 600;text-transform: uppercase;">
                                        <button   onclick="CloneDelete('{{$item->oltType}}','{{$item->oltAddress}}','{{$item->ifindex}}','{{$item->onuDescr}}','{{$item->onuMac}}','{{$key}}')"  class="inline-flex items-center px-4 py-2 h-8 ml-2  bg-gray-900  dark:bg-gray-900 border  border-red-400 rounded-md font-semibold text-xs text-white dark:text-red-400 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-red-400 dark:hover:text-gray-900  dark:active:text-red-400"  type="button">
                                            DELETE
                                        </button>
                                    </td>   
                                </tr>
                            @endif

                        @endforeach  
                    </tbody>
                </table>
            </div>
            {{$data->appends(request()->query())->links()}}
        
        </div>


        <x-bladewind.modal
            size="medium"
            center_action_buttons="true"
            type="warning"
            title="Confirm Onu Delete"
            :ok_button_action="'Delete_ONU()'"
            close_after_action="true"
            name="ont-delete"
            show_close_icon="true"
            blur_backdrop="false"
            ok_button_label="Yes, Delete"
            cancel_button_label="Don't Delete">
            Are you sure you want to delete onu ? This action cannot be undone
        </x-bladewind.modal>

        <x-bladewind::modal
            size="medium"
            center_action_buttons="true"
            type="warning"
            title="ONT DESCRIPTION EDIT"
            :ok_button_action="'EditCloneFinish()'"
            close_after_action="true"
            name="Ont-Description-Edit"
            show_close_icon="true"
            blur_backdrop="false"
            ok_button_label="Yes, Save"
            cancel_button_label="Don't Save">
            Are you sure you want to edit onu description ? This action cannot be undone
            <div class="flex sm:w-2/2 w-full items-center justify-center">
                    <x-bladewind::input required="false" name="DESCR_NEW_ONT" id="DESCR_NEW_ONT_ID" placeholder="ONT DESCRIPTION"
                            class="dark:bg-gray-900 mt-10 dark:text-gray-400 text-xs overflow-hidden shadow-sm sm:rounded-lg w-full h-8  border border-slate-700" />
            </div> 
        </x-bladewind::modal>

        <input id="Input_oltType" value="" hidden />
        <input id="Input_oltAddress" value="" hidden />
        <input id="Input_ifindex" value="" hidden />
        <input id="Input_onuDescr" value="" hidden />
        <input id="Input_onuMac" value="" hidden />
        <input id="Input_onuNEWDescr" value="" hidden />
        <input id="Input_DescrRefresh" value="" hidden />
        <input id="Input_DeleteRefresh" value="" hidden />

    </div>

    <script type="text/javascript">

        window.onload = function()
        {
            let result = localStorage.getItem('successClonedMessage');
            if (result)
            {
                showNotification('Done', 'Onu operation done successfully','success',3);
                localStorage.removeItem('successClonedMessage');
            }
        };

        function CloneDelete(Type,ip,ifindex,descr,onuMac,key)
        {
            document.getElementById("Input_oltType").value = Type;
            document.getElementById("Input_oltAddress").value = ip;
            document.getElementById("Input_ifindex").value = ifindex;
            document.getElementById("Input_onuDescr").value = descr;
            document.getElementById("Input_onuMac").value = onuMac;
            document.getElementById("Input_DeleteRefresh").value = key;
            showModal('ont-delete');
        }

        function Delete_ONU()
        {
            let Type    = document.getElementById("Input_oltType").value; 
            let ip      = document.getElementById("Input_oltAddress").value;
            let ifindex = document.getElementById("Input_ifindex").value;
            let descr   = document.getElementById("Input_onuDescr").value; 
            let onuMac  = document.getElementById("Input_onuMac").value; 
            let key  = document.getElementById("Input_DeleteRefresh").value; 

            document.getElementById(`DeleteButton${key}`).innerHTML = `
                <center>
                    <svg  width="30" height="30" fill="#5c6bc0" class="mr-2 animate-spin" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg">
                        <path d="M526 1394q0 53-37.5 90.5t-90.5 37.5q-52 0-90-38t-38-90q0-53 37.5-90.5t90.5-37.5 90.5 37.5 37.5 90.5zm498 206q0 53-37.5 90.5t-90.5 37.5-90.5-37.5-37.5-90.5 37.5-90.5 90.5-37.5 90.5 37.5 37.5 90.5zm-704-704q0 53-37.5 90.5t-90.5 37.5-90.5-37.5-37.5-90.5 37.5-90.5 90.5-37.5 90.5 37.5 37.5 90.5zm1202 498q0 52-38 90t-90 38q-53 0-90.5-37.5t-37.5-90.5 37.5-90.5 90.5-37.5 90.5 37.5 37.5 90.5zm-964-996q0 66-47 113t-113 47-113-47-47-113 47-113 113-47 113 47 47 113zm1170 498q0 53-37.5 90.5t-90.5 37.5-90.5-37.5-37.5-90.5 37.5-90.5 90.5-37.5 90.5 37.5 37.5 90.5zm-640-704q0 80-56 136t-136 56-136-56-56-136 56-136 136-56 136 56 56 136zm530 206q0 93-66 158.5t-158 65.5q-93 0-158.5-65.5t-65.5-158.5q0-92 65.5-158t158.5-66q92 0 158 66t66 158z">
                        </path>
                    </svg>
                </center>
            `;
    
            $.ajax({
                    url: "/Delete-Clone-Onu",
                    type: "GET",
                    data:{'Type':Type,'ip':ip,'ifindex':ifindex,'descr':descr,'onuMac':onuMac},
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    dataType: "json",
                    success: function(result)
                    { 
                        if(result.error)
                        {
                            showNotification('Warning...', result.error ,'warning',10);
                        }
                        else
                        {        
                            localStorage.setItem('successClonedMessage', true);
                            location.reload();
                        }
                    },
                    error: function (xhr, status, error) 
                    {
                        showNotification('Warning...', error ,'warning',10);
                    }

            });  
            
        }

        function checkClonePon(ip)
        {
            const currentUrl = window.location.origin + '/OLT';
            window.open(`${currentUrl}?id=${ip}`, '_blank');
        }

        function macGSearch(mac)
        {
            const currentUrl = window.location.origin + '/GlobalSearch';
            window.open(`${currentUrl}?id=${mac}`, '_blank');
        }
         
        function Edit_Clone_Description(Type,ip,ifindex,descr,onuMac,key)
        {
            document.getElementById("Input_oltType").value      = Type;
            document.getElementById("Input_oltAddress").value   = ip;
            document.getElementById("Input_ifindex").value      = ifindex;
            document.getElementById("Input_onuDescr").value     = descr;
            document.getElementById("Input_onuMac").value       = onuMac;
            document.getElementById("Input_DescrRefresh").value = key;
            showModal('Ont-Description-Edit');
        }

        function EditCloneFinish()
        {
            let Type      = document.getElementById("Input_oltType").value; 
            let ip        = document.getElementById("Input_oltAddress").value;
            let ifindex   = document.getElementById("Input_ifindex").value;
            let oldDescr  = document.getElementById("Input_onuDescr").value; 
            let onuMac    = document.getElementById("Input_onuMac").value; 
            let newDescr  = document.getElementById("DESCR_NEW_ONT_ID").value; 
            let key       = document.getElementById("Input_DescrRefresh").value; 

            
            document.getElementById(`DescriptionButton${key}`).innerHTML = `
                <center>
                    <svg  width="30" height="30" fill="#5c6bc0" class="mr-2 animate-spin" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg">
                        <path d="M526 1394q0 53-37.5 90.5t-90.5 37.5q-52 0-90-38t-38-90q0-53 37.5-90.5t90.5-37.5 90.5 37.5 37.5 90.5zm498 206q0 53-37.5 90.5t-90.5 37.5-90.5-37.5-37.5-90.5 37.5-90.5 90.5-37.5 90.5 37.5 37.5 90.5zm-704-704q0 53-37.5 90.5t-90.5 37.5-90.5-37.5-37.5-90.5 37.5-90.5 90.5-37.5 90.5 37.5 37.5 90.5zm1202 498q0 52-38 90t-90 38q-53 0-90.5-37.5t-37.5-90.5 37.5-90.5 90.5-37.5 90.5 37.5 37.5 90.5zm-964-996q0 66-47 113t-113 47-113-47-47-113 47-113 113-47 113 47 47 113zm1170 498q0 53-37.5 90.5t-90.5 37.5-90.5-37.5-37.5-90.5 37.5-90.5 90.5-37.5 90.5 37.5 37.5 90.5zm-640-704q0 80-56 136t-136 56-136-56-56-136 56-136 136-56 136 56 56 136zm530 206q0 93-66 158.5t-158 65.5q-93 0-158.5-65.5t-65.5-158.5q0-92 65.5-158t158.5-66q92 0 158 66t66 158z">
                        </path>
                    </svg>
                </center>
            `;

          
            if(!newDescr || newDescr.trim() === '')newDescr = 'N/A';
  
            $.ajax({
                    url: "/CLONE-OnuDescriptionEdit",
                    type: "GET",
                    data:{'Type':Type,'ip':ip,'ifindex':ifindex,'OLDdescr':oldDescr,'onuMac':onuMac,'NEWdescr':newDescr},
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    dataType: "json",
                    success: function(result)
                    {      
                        if(result.error)
                        {
                            showNotification('Onu Description Edit', result.error,'warning',3);
                        }
                        else
                        {
                            localStorage.setItem('successClonedMessage', true);
                            location.reload();
                        }
                    },
                    error: function (xhr, status, error) 
                    {
                        if (xhr.status === 401) 
                        {
                            showNotification('Onu Description Edit', 'You dont have permissions to perform this action','warning',3);
                        }
                        else   showNotification('Onu Description Edit', xhr.status+'  '+error ,'error',3);
                    }
            });
        }

    </script>

</x-app-layout>