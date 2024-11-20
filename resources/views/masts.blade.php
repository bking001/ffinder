<x-app-layout>

    <div class="py-12">


        <x-bladewind.notification position="top right" />
        <x-bladewind.notification />

        <div class="flex justify-center">
            <button onclick="showModal('form-mode');" type="button"
                class="inline-flex items-center px-4 py-2 ml-2  bg-gray-900 dark:bg-gray-900 border  border-indigo-400 rounded-md font-semibold text-xs text-white dark:text-indigo-400 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-indigo-400 dark:hover:text-gray-900  dark:active:text-indigo-400">ADD
                MAST</button>
        </div>
 


        <div class="w-1/2 mx-auto">
            @if (session('status'))
                <div class="flex flex-col justify-center items-center text-sm text-red-600 dark:text-green-400 space-y-1 mt-2 mx-auto"
                    x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)">
                    {{ session('status') }}
                </div>
            @else (session('error')) 
                <div class="flex flex-col justify-center items-center text-sm text-red-600 dark:text-red-400 space-y-1 mt-2 mx-auto">
                    {{ session('error') }}
                </div>
            @endif
            <br>
            <form class="max-w-md mx-left" method="get" action="{{ route('MastsSearch') }}">
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


            <div class="flex items-center  justify-center text:xs sm:justify-center ml-4 sm:ml-4"  >
                <table class="text-sm  w-full border-separate border-spacing-y-2" >                
                    <thead class="sr-only">
                            <tr class="tr-class">
                                <th class="text-indigo-400">PON</th>
                                <th class="text-indigo-400">PON</th>
                                <th class="text-indigo-400">PON</th>
                            </tr>
                     </thead>

                     <tbody  id="pon_statistic_table-body">      
                        <tr  class="tr-class">
                            <td class="td-class text-indigo-400 text-center text:xs" style="color:#818cf8;background:#141c3d;letter-spacing: .1em;font-weight: 600;text-transform: uppercase;">NAME</th>
                            <td class="td-class text-indigo-400 text-center text:xs" style="color:#818cf8;background:#141c3d;letter-spacing: .1em;font-weight: 600;text-transform: uppercase;">EDIT</th>
                            <td class="td-class text-indigo-400 text-center text:xs" style="color:#818cf8;background:#141c3d;letter-spacing: .1em;font-weight: 600;text-transform: uppercase;">DELETE</th>                  
                        </tr>
                        
                        @foreach($data as $item)
                        <tr class="tr-class">                                    
                            <td class="td-class text:xs" style="text-align:center;background:#111b30;letter-spacing: .1em;font-weight: 600;text-transform: uppercase;">
                                <x-bladewind::input id="mastname{{ $item->id }}" class="dark:bg-gray-900 dark:text-gray-400 text-xs overflow-hidden shadow-sm sm:rounded-lg w-full h-8  border border-slate-700" value="{{ $item->saxeli }}"  style="letter-spacing: .1em;font-weight: 600;" />
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



    </div>

    <input id="InputForCaptureModal" value="" hidden />
    <input id="DeleteInput" value="" hidden />

    <x-bladewind::modal size="medium" center_action_buttons="true" type="warning" title="Confirm Edit"
        :ok_button_action="'updateData()'" close_after_action="true" name="custom-actions" show_close_icon="true" blur_backdrop="false"
        ok_button_label="Yes, save" cancel_button_label="don't save">
        Are you sure you want to edit this data? This action cannot be undone.
    </x-bladewind::modal>

    <x-bladewind::modal name="delete-Device" size="medium" type="error" :ok_button_action="'deleteData()'" show_close_icon="true"
        ok_button_label="Delete" blur_backdrop="false" title="Confirm Device Deletion">
        Are you really sure you want to delete <b class="title"></b>?
        This action cannot be reversed.
    </x-bladewind::modal>

    <x-bladewind::modal center_action_buttons="true" type="info" title="Add MAST" :ok_button_action="'saveProfile()'"
        close_after_action="true" name="form-mode" show_close_icon="true" ok_button_label="CREATE"
        cancel_button_label="cancel" backdrop_can_close="true" blur_backdrop="false">

        <form method="get" action="{{ route('mast.add') }}" class="profile-form">
            @csrf

            <div class="grid">
                <x-bladewind::input required="false" name="Mast_Name" placeholder="Name"
                    class="dark:bg-gray-900 dark:text-gray-400 text-xs overflow-hidden shadow-sm sm:rounded-lg w-full h-8  border border-slate-700" />
            </div>

        </form>
    </x-bladewind::modal>


    <script type="text/javascript">
        saveProfile = () => {
            if (validateForm('.profile-form')) {
                domEl('.profile-form').submit();
            } else {
                return false;
            }
        }

        function Update(id) {
            document.getElementById('InputForCaptureModal').value = id;
            showModal('custom-actions');
        }

        function Delete(id) {
            document.getElementById('DeleteInput').value = id;
            showModal('delete-Device');
        }

        function updateData() {
            var id = document.getElementById('InputForCaptureModal').value;
            var data = [];

            var Name = document.getElementById('mastname' + id).value;
            var data = {
                'id': id,
                'saxeli':Name
            };

                        $.ajax({
                                    url: "/Masts-Update",
                                    type: "POST",
                                    data: JSON.stringify(data),
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                    },
                                    dataType: "json",
                                    success: function(result)
                                    {  
                                        showNotification(Name, Name+' updated successfully','success',1);  
                                        setTimeout(function(){ location.reload();}, 1000);                                  
                                    },
                                    error: function (xhr, status, error) 
                                    {
                                        showNotification("Can't Update Data", error ,'warning',5);
                                    }
                                });            

        }

        function deleteData() {
            var id = document.getElementById('DeleteInput').value;
            var data = {
                id: id
            };

                        $.ajax({
                                    url: "/Masts-Delete",
                                    type: "POST",
                                    data: JSON.stringify(data),
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                    },
                                    dataType: "json",
                                    success: function(result)
                                    {  
                                        showNotification('Delete', 'Mast deleted successfully','success',1);  
                                        setTimeout(function(){ location.reload();}, 1000);                                  
                                    },
                                    error: function (xhr, status, error) 
                                    {
                                        showNotification("Can't Update Data", error ,'warning',5);
                                    }
                                });  
        }
    </script>


</x-app-layout>
