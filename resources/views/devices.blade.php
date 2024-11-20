<x-app-layout>

<div class="py-12">

        <x-bladewind.notification position="top right" />
        <x-bladewind.notification />

        <div class="flex flex-wrap  mb-5 justify-center">
            <div class="flex justify-center">
                <button onclick="showModal('form-mode');" type="button" class="inline-flex items-center px-4 py-2 ml-2  bg-gray-900 dark:bg-gray-900 border  border-indigo-400 rounded-md font-semibold text-xs text-white dark:text-indigo-400 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-indigo-400 dark:hover:text-gray-900  dark:active:text-indigo-400">ADD DEVICE</button>
            </div>

            <div class="flex justify-center">
                <button onclick="showModal('global-form-mode');" type="button" class="inline-flex items-center px-4 py-2 ml-2  bg-gray-900 dark:bg-gray-900 border  border-indigo-400 rounded-md font-semibold text-xs text-white dark:text-indigo-400 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-indigo-400 dark:hover:text-gray-900  dark:active:text-indigo-400">GLOBAL EDIT</button>
            </div>
        </div>

        <center>
            @if (session('status'))
                <div class="text-green-400">
                    {{ session('status') }}
                </div>
            @endif
            
            @if (session('error'))
                <div class="text-red-400">
                    {{ session('error') }}
                </div>
            @endif
        </center>

        <div class="w-11/12 mx-auto">
            <br>
            <form class="max-w-md mx-left"   method="get" action="{{ route('DeviceSearch') }}" >
            @csrf
            <div class="flex">
                <div class="relative flex-1">
                        <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                            <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"/>
                            </svg>
                        </div>
                        <input type="search" name="default_search" class="block w-full p-2 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-gray-800 dark:bg-gray-900  dark:border-gray-600  dark:placeholder-gray-400 dark:text-white  dark:focus:ring-gray-900 dark:focus:border-gray-600" placeholder="Search..." required />
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
                                        <td class="td-class text-indigo-400 text-center text:xs" style="color:#818cf8;background:#141c3d;letter-spacing: .1em;font-weight: 600;text-transform: uppercase;">COORDINATES</th>
                                        <td class="td-class text-indigo-400 text-center text:xs" style="color:#818cf8;background:#141c3d;letter-spacing: .1em;font-weight: 600;text-transform: uppercase;">USERNAME</th>
                                        <td class="td-class text-indigo-400 text-center text:xs" style="color:#818cf8;background:#141c3d;letter-spacing: .1em;font-weight: 600;text-transform: uppercase;">PASSWORD</th>
                                        <td class="td-class text-indigo-400 text-center text:xs" style="color:#818cf8;background:#141c3d;letter-spacing: .1em;font-weight: 600;text-transform: uppercase;">READ COMUNITY</th>
                                        <td class="td-class text-indigo-400 text-center text:xs" style="color:#818cf8;background:#141c3d;letter-spacing: .1em;font-weight: 600;text-transform: uppercase;">WRITE COMUNITY</th>
                                        <td class="td-class text-indigo-400 text-center text:xs" style="color:#818cf8;background:#141c3d;letter-spacing: .1em;font-weight: 600;text-transform: uppercase;">EDIT</th>
                                        <td class="td-class text-indigo-400 text-center text:xs" style="color:#818cf8;background:#141c3d;letter-spacing: .1em;font-weight: 600;text-transform: uppercase;">DELETE</th>                   
                                    </tr>
                                    
                                    @foreach($data as $item)
                                    <tr class="tr-class">                                  
                                        <td class="td-class text:xs" style="text-align:center;background:#111b30;letter-spacing: .1em;font-weight: 600;text-transform: uppercase;">
                                            <x-bladewind::input id="Parameters_Type{{$item->id}}" class="dark:bg-gray-900 dark:text-indigo-400 text-xs overflow-hidden shadow-sm sm:rounded-lg w-full h-8 text-center border border-slate-700" value="{{ $item->Type }}"  style="letter-spacing: .1em;font-weight: 600;"/>
                                        </td>   
                                        <td class="td-class text:xs" style="text-align:center;background:#111b30;letter-spacing: .1em;font-weight: 600;text-transform: uppercase;">
                                            <x-bladewind::input id="Parameters_Address{{$item->id}}" class="dark:bg-gray-900 dark:text-gray-400 text-xs overflow-hidden shadow-sm sm:rounded-lg w-full h-8  border border-slate-700"  value="{{ $item->Address }}"  style="letter-spacing: .1em;font-weight: 600;" />
                                        </td>   
                                        <td class="td-class text:xs" style="text-align:center;background:#111b30;letter-spacing: .1em;font-weight: 600;text-transform: uppercase;">
                                            <x-bladewind::input id="Parameters_Name{{$item->id}}" class="dark:bg-gray-900 dark:text-gray-400 text-xs overflow-hidden shadow-sm sm:rounded-lg w-full h-8  border border-slate-700"  value="{{ $item->device_name }}"  style="letter-spacing: .1em;font-weight: 600;" />
                                        </td>   
                                        <td class="td-class text:xs" style="text-align:center;background:#111b30;letter-spacing: .1em;font-weight: 600;text-transform: uppercase;">
                                            <x-bladewind::input id="Parameters_Point{{$item->id}}" class="dark:bg-gray-900 dark:text-gray-400 text-xs  overflow-hidden shadow-sm sm:rounded-lg w-full h-8  border border-slate-700"  value="{{ $item->mast }}"  style="letter-spacing: .1em;font-weight: 600;" />
                                        </td>   
                                        <td class="td-class text:xs" style="text-align:center;background:#111b30;letter-spacing: .1em;font-weight: 600;text-transform: uppercase;">
                                            <x-bladewind::input id="Parameters_Coords{{$item->id}}" class="dark:bg-gray-900 dark:text-gray-400 text-xs overflow-hidden shadow-sm sm:rounded-lg w-full h-8  border border-slate-700"  value="{{ $item->coordinates }}"  style="letter-spacing: .1em;font-weight: 600;" />
                                        </td>   
                                        <td class="td-class text:xs" style="text-align:center;background:#111b30;letter-spacing: .1em;font-weight: 600;text-transform: uppercase;">
                                            <x-bladewind::input id="Parameters_Username{{$item->id}}" class="dark:bg-gray-900 dark:text-gray-400 text-xs overflow-hidden shadow-sm sm:rounded-lg w-full h-8  border border-slate-700" value="{{ $item->Username }}"  style="letter-spacing: .1em;font-weight: 600;" />
                                        </td>   
                                        <td class="td-class text:xs" style="text-align:center;background:#111b30;letter-spacing: .1em;font-weight: 600;text-transform: uppercase;">
                                            <x-bladewind::input id="Parameters_Password{{$item->id}}" suffix="eye" viewable="true" type="password" class="dark:bg-gray-900    text-xs overflow-hidden dark:text-gray-400 shadow-sm sm:rounded-lg w-full h-8  border border-slate-700" value="{{ $item->Pass }}"   style="letter-spacing: .1em;font-weight: 600;" />
                                        </td>                                                 
                                        <td class="td-class text:xs" style="text-align:center;background:#111b30;letter-spacing: .1em;font-weight: 600;text-transform: uppercase;">
                                        <x-bladewind::input id="Parameters_Read{{$item->id}}" class="dark:bg-gray-900 dark:text-gray-400 text-xs overflow-hidden shadow-sm sm:rounded-lg w-full h-8  border border-slate-700" value="{{ $item->snmpRcomunity }}"  style="letter-spacing: .1em;font-weight: 600;" />
                                        </td>   
                                        <td class="td-class text:xs" style="text-align:center;background:#111b30;letter-spacing: .1em;font-weight: 600;text-transform: uppercase;">
                                            <x-bladewind::input id="Parameters_write{{$item->id}}" suffix="eye" viewable="true" type="password"  class="dark:bg-gray-900 dark:text-gray-400 text-xs overflow-hidden shadow-sm sm:rounded-lg w-full h-8  border border-slate-700" value="{{ $item->snmpWcomunity }}"  style="letter-spacing: .1em;font-weight: 600;" />
                                        </td>   
                                        <td class="td-class text:xs" style="text-align:center;vertical-align: baseline;background:#111b30;letter-spacing: .1em;font-weight: 600;text-transform: uppercase;">
                                            <button class="inline-flex items-center px-4 py-2 h-8 ml-2  bg-gray-900 dark:bg-gray-900 border  border-indigo-400 rounded-md font-semibold text-xs text-white dark:text-indigo-400 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-indigo-400 dark:hover:text-gray-900  dark:active:text-indigo-400" onclick="Update('{{ $item->id }}')" type="button">
                                                EDIT
                                            </button>
                                        </td>   
                                        <td class="td-class text:xs" style="text-align:center;vertical-align: baseline;background:#111b30;letter-spacing: .1em;font-weight: 600;text-transform: uppercase;">
                                            <button class="inline-flex items-center px-4 py-2 h-8 ml-2  bg-gray-900  dark:bg-gray-900 border  border-red-400 rounded-md font-semibold text-xs text-white dark:text-red-400 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-red-400 dark:hover:text-gray-900  dark:active:text-red-400" onclick="Delete('{{ $item->id }}')" type="button">
                                              DELETE
                                            </button>
                                        </td>   
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        {{ $data->appends(request()->query())->links() }}
 
                    <br>
                </div>

                    <input id="InputForCaptureModal" value="" hidden />
                    <input id="DeleteInput" value="" hidden />



                    <x-bladewind.modal
                        name="delete-Device"
                        size="medium"
                        type="error"
                        :ok_button_action="'deleteData()'"
                        show_close_icon="true"
                        ok_button_label="Delete"
                        blur_backdrop="false"
                        title="Confirm Device Deletion">
                            Are you really sure you want to delete <b class="title"></b>?
                            This action cannot be reversed.
                    </x-bladewind.modal>

                    <x-bladewind.modal
                        size="medium"
                        center_action_buttons="true"
                        type="warning"
                        title="Confirm Edit"
                        :ok_button_action="'updateData()'"
                        close_after_action="true"
                        name="custom-actions"
                        show_close_icon="true"
                        blur_backdrop="false"
                        ok_button_label="Yes, save"
                        cancel_button_label="don't save">
                        Are you sure you want to edit this data? This action cannot be undone.
                    </x-bladewind.modal>


                    <x-bladewind.modal
                        center_action_buttons="true"
                        type="info"
                        title="Add Device"
                        :ok_button_action="'saveProfile()'"
                        close_after_action="true"
                        name="form-mode"
                        show_close_icon="true"
                        ok_button_label="CREATE"
                        cancel_button_label="cancel"
                        backdrop_can_close="true"
                        blur_backdrop="false">

                        <form method="post" action="{{ route('Device.create') }}" class="profile-form">
                            @csrf
                            <div class="grid grid-cols-2 gap-4 mt-6">

                                <select id="DeviceTypeSelect" name="type"  onchange="DefaultCredsInsert()" class="dark:bg-gray-900 dark:text-gray-400 text-xs overflow-hidden shadow-sm sm:rounded-lg w-full h-8  border border-slate-700" required>
                                    <option selected disabled>Choose Device Type</option>
                                    <option value="BDCOM">BDCOM</option>
                                    <option value="HUAWEI">HUAWEI</option>
                                    <option value="ZTE">ZTE</option>
                                    <option value="HSGQ">HSGQ</option>
                                    <option value="VSOLUTION">VSOLUTION</option>
                                    <option value="ZYXEL">ZYXEL</option>
                                    <option value="CISCO_CATALYST">CISCO CATALYST 3750</option>
                                    <option value="MIKROTIK_ROUTER">MIKROTIK ROUTER</option>
                                </select>


                                <x-bladewind::input name="IP" placeholder="IP address"
                                    class="dark:bg-gray-900 dark:text-gray-400 text-xs overflow-hidden shadow-sm sm:rounded-lg w-full h-8  border border-slate-700"  />
                            </div>

                            <div class="grid grid-cols-2 gap-4 mt-6">
                                <x-bladewind::input required="false" name="Device_Name" placeholder="Name"
                                    class="dark:bg-gray-900 dark:text-gray-400 text-xs overflow-hidden shadow-sm sm:rounded-lg w-full h-8  border border-slate-700" />

                                <x-bladewind::input required="false" name="Device_Point" placeholder="Point"
                                    class="dark:bg-gray-900 dark:text-gray-400 text-xs overflow-hidden shadow-sm sm:rounded-lg w-full h-8  border border-slate-700"/>
                            </div>

                            <div class="grid grid-cols-2 gap-4 mt-6">
                                <x-bladewind::input required="false" name="Read_Comunity" id="ReadID" placeholder="Read Comunity"
                                    class="dark:bg-gray-900 dark:text-gray-400 text-xs overflow-hidden shadow-sm sm:rounded-lg w-full h-8  border border-slate-700" />

                                <x-bladewind::input required="false" name="Write_Comunity" id="WriteID" placeholder="Write Comunity"
                                    class="dark:bg-gray-900 dark:text-gray-400 text-xs overflow-hidden shadow-sm sm:rounded-lg w-full h-8  border border-slate-700"/>
                            </div>
                            <div class="grid grid-cols-2 gap-4 mt-6">
                                <x-bladewind::input required="false" name="Username" id="UsernameID"  placeholder="Username"
                                    class="dark:bg-gray-900 dark:text-gray-400 text-xs overflow-hidden shadow-sm sm:rounded-lg w-full h-8  border border-slate-700"  />

                                <x-bladewind::input numeric="false" name="Password" id="PasswordID"  type="password" placeholder="Password"
                                    class="dark:bg-gray-900 dark:text-gray-400 text-xs overflow-hidden shadow-sm sm:rounded-lg w-full h-8  border border-slate-700"  />
                            </div>

                            <div class="grid grid-cols-2 gap-4 mt-6">
                                <x-bladewind::input required="false" name="Device_Coordinates" placeholder="Coordinates"
                                    class="dark:bg-gray-900 dark:text-gray-400 text-xs overflow-hidden shadow-sm sm:rounded-lg w-full h-8  border border-slate-700" />
                            </div>

                        </form>
                    </x-bladewind.modal>


                    <x-bladewind.modal
                        center_action_buttons="true"
                        type="info"
                        title="Global Edit"
                        :ok_button_action="'saveEdit()'"
                        close_after_action="true"
                        name="global-form-mode"
                        size="xl"
                        show_close_icon="true"
                        ok_button_label="EDIT"
                        cancel_button_label="cancel"
                        backdrop_can_close="true"
                        blur_backdrop="false">

                        <form method="post" action="{{ route('Device.GlobalEdit') }}" class="edit-form">
                            @csrf
                            
                            <center>
                                <div class="grid grid-cols-7 gap-4 mt-6">
                                    <x-bladewind::checkbox
                                        color="indigo"
                                        checked="true"
                                        value=1
                                        name="BDCOM_CHECKBOX"
                                        label="BDCOM" />

                                    <x-bladewind::checkbox
                                        color="indigo"
                                        checked="true"
                                        name="HUAWEI_CHECKBOX"
                                        value=1
                                        label="HUAWEI" />    

                                    <x-bladewind::checkbox
                                        color="indigo"
                                        checked="true"
                                        value=1
                                        name="ZTE_CHECKBOX"
                                        label="ZTE" />   
                                        
                                    <x-bladewind::checkbox
                                        color="indigo"
                                        checked="true"
                                        value=1
                                        name="HSGQ_CHECKBOX"
                                        label="HSGQ" />   
                                        
                                    <x-bladewind::checkbox
                                        color="indigo"
                                        checked="true"
                                        value=1
                                        name="VSOLUTION_CHECKBOX"
                                        label="VSOLUTION" />  

                                    <x-bladewind::checkbox
                                        color="indigo"
                                        checked="true"
                                        value=1
                                        name="CISCO_CHECKBOX"
                                        label="CISCO" />  

                                    <x-bladewind::checkbox
                                        color="indigo"
                                        checked="true"
                                        value=1
                                        name="ZYXEL_CHECKBOX"
                                        label="ZYXEL" />      
                                </div>
                            </center>
                            <div class="grid grid-cols-2 gap-4 mt-6">
                                <x-bladewind::input required="false" name="Read_Comunity" placeholder="Read Comunity"
                                    class="dark:bg-gray-900 dark:text-gray-400 text-xs overflow-hidden shadow-sm sm:rounded-lg w-full h-8  border border-slate-700" />

                                <x-bladewind::input required="false" name="Write_Comunity" placeholder="Write Comunity"
                                    class="dark:bg-gray-900 dark:text-gray-400 text-xs overflow-hidden shadow-sm sm:rounded-lg w-full h-8  border border-slate-700"/>
                            </div>
                            <div class="grid grid-cols-2 gap-4 mt-6">
                                <x-bladewind::input required="false" name="Username"   placeholder="Username"
                                    class="dark:bg-gray-900 dark:text-gray-400 text-xs overflow-hidden shadow-sm sm:rounded-lg w-full h-8  border border-slate-700"  />

                                <x-bladewind::input numeric="false" name="Password" type="password" placeholder="Password"
                                    class="dark:bg-gray-900 dark:text-gray-400 text-xs overflow-hidden shadow-sm sm:rounded-lg w-full h-8  border border-slate-700"  />
                            </div>

                        </form>
                    </x-bladewind.modal>



                    <script type="text/javascript">

                        saveEdit = () => {
                            if(validateForm('.edit-form')){
                                domEl('.edit-form').submit();
                            } else {
                                return false;
                            }
                        }

                        saveProfile = () => {
                            if(validateForm('.profile-form')){
                                domEl('.profile-form').submit();
                            } else {
                                return false;
                            }
                        }

                        function Update(id)
                        {
                            document.getElementById('InputForCaptureModal').value = id;
                            showModal('custom-actions');
                        }
                        function Delete(id)
                        {
                            document.getElementById('DeleteInput').value = id;
                            showModal('delete-Device');
                        }

                        function updateData()
                        {
                            var id = document.getElementById('InputForCaptureModal').value;
                            var data = [];

                            var type      = document.getElementById('Parameters_Type' + id).value;
                            var Address   = document.getElementById('Parameters_Address' + id).value;
                            var name      = document.getElementById('Parameters_Name' + id).value;
                            var point     = document.getElementById('Parameters_Point' + id).value;
                            var coord     = document.getElementById('Parameters_Coords' + id).value;
                            var username  = document.getElementById('Parameters_Username' + id).value;
                            var password  = document.getElementById('Parameters_Password' + id).value;
                            var Read      = document.getElementById('Parameters_Read' + id).value;
                            var write     = document.getElementById('Parameters_write' + id).value;


                            data.push({id,type, Address, username, password , Read , write , name , point , coord });

                            $.ajax({
                                    url: "/Devices",
                                    type: "PATCH",
                                    data: JSON.stringify(data),
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                    },
                                    dataType: "json",
                                    success: function(result)
                                    {  
                                        showNotification(type, type+' updated successfully','success',1); 
                                        setTimeout(function(){ location.reload();}, 1000);                                
                                    },
                                    error: function (xhr, status, error) 
                                    {
                                        showNotification("Can't Update Data", error ,'warning',5);
                                    }
                                });

                        }

                        function deleteData()
                        {
                            var id = document.getElementById('DeleteInput').value;
                            var data = [];
                            data.push({id});

                            
                            $.ajax({
                                    url: "/Devices-Delete",
                                    type: "PATCH",
                                    data: JSON.stringify(data),
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                    },
                                    dataType: "json",
                                    success: function(result)
                                    {  
                                        showNotification('Delete Action', ' Device deleted successfully','success',1);      
                                        setTimeout(function(){ location.reload();}, 1000);                        
                                    },
                                    error: function (xhr, status, error) 
                                    {
                                        showNotification("Can't Update Data", error ,'warning',5);
                                    }
                                });
                        }

                        function DefaultCredsInsert()
                        {
                            var selectElement         = document.getElementById('DeviceTypeSelect');
                            var selectedOption        = selectElement.options[selectElement.selectedIndex];              
                            var SelectedDeviceType    = selectedOption.value;

                           $.ajax({
                                    url: "/Devices-DefaultCreds",
                                    type: "GET",
                                    data: {'SelectedDeviceType':SelectedDeviceType},
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                    },
                                    dataType: "json",
                                    success: function(result)
                                    {  
                                        if(result.error)
                                        {
                                            showNotification("Can't Get Data", result.error ,'warning',5);
                                        }
                                        else
                                        {
                                            document.getElementById("UsernameID").value = result.username;
                                            document.getElementById("PasswordID").value = result.Pass;
                                            document.getElementById("ReadID").value = result.snmpRcomunity;
                                            document.getElementById("WriteID").value = result.snmpWcomunity;
                                        } 
                                    },
                                    error: function (xhr, status, error) 
                                    {
                                        showNotification("Can't Get Data", error ,'warning',5);
                                    }
                                });

                        }

                    </script>


        </div>
</div>

</x-app-layout>
