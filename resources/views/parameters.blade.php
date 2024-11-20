<x-app-layout>
 


    <div class="py-12">
        <div class="w-11/12 mx-auto">
            <x-bladewind.notification position="top right" />
            <x-bladewind.notification />


                        <div class="flex items-center  justify-center text:xs sm:justify-center ml-4 sm:ml-4"  >
                            <table class="text-sm  w-full border-separate border-spacing-y-2" >                
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
                                        <td class="td-class text-indigo-400 text-center text:xs" style="color:#818cf8;background:#141c3d;letter-spacing: .1em;font-weight: 600;text-transform: uppercase;">TARGE</th>
                                        <td class="td-class text-indigo-400 text-center text:xs" style="color:#818cf8;background:#141c3d;letter-spacing: .1em;font-weight: 600;text-transform: uppercase;">URL</th>
                                        <td class="td-class text-indigo-400 text-center text:xs" style="color:#818cf8;background:#141c3d;letter-spacing: .1em;font-weight: 600;text-transform: uppercase;">USERNAME</th>    
                                        <td class="td-class text-indigo-400 text-center text:xs" style="color:#818cf8;background:#141c3d;letter-spacing: .1em;font-weight: 600;text-transform: uppercase;">PASSWORD</th>                  
                                        <td class="td-class text-indigo-400 text-center text:xs" style="color:#818cf8;background:#141c3d;letter-spacing: .1em;font-weight: 600;text-transform: uppercase;">EDIT</th>                                
                                    </tr>
                                    
                                    @foreach($data as $item)
                                    <tr class="tr-class">                                    
                                        <td class="td-class text:xs" style="text-align:center;background:#111b30;letter-spacing: .1em;font-weight: 600;text-transform: uppercase;">
                                            <x-bladewind::input disabled id="Parameters_Type{{$item->id}}" class="uppercase dark:bg-gray-900 text-center dark:text-indigo-400 text-xs overflow-hidden shadow-sm sm:rounded-lg w-full h-8  border border-slate-700" value="{{ $item->type }}"  style="letter-spacing: .1em;font-weight: 600;" />
                                        </td>   

                                        <td class="td-class text:xs" style="text-align:center;background:#111b30;letter-spacing: .1em;font-weight: 600;text-transform: uppercase;">
                                            <x-bladewind::input id="Parameters_Url{{$item->id}}"  class="dark:bg-gray-900 dark:text-gray-400 text-xs overflow-hidden shadow-sm sm:rounded-lg w-full h-8  border border-slate-700" value="{{ $item->url }}"  style="letter-spacing: .1em;font-weight: 600;" />
                                        </td>  

                                        <td class="td-class text:xs" style="text-align:center;background:#111b30;letter-spacing: .1em;font-weight: 600;text-transform: uppercase;">
                                            <x-bladewind::input id="Parameters_Username{{$item->id}}" class="dark:bg-gray-900 dark:text-gray-400 text-xs overflow-hidden shadow-sm sm:rounded-lg w-full h-8  border border-slate-700" value="{{ $item->username }}"  style="letter-spacing: .1em;font-weight: 600;" />
                                        </td>  

                                        <td class="td-class text:xs" style="text-align:center;background:#111b30;letter-spacing: .1em;font-weight: 600;text-transform: uppercase;">
                                            <x-bladewind::input id="Parameters_Password{{$item->id}}" class="dark:bg-gray-900 dark:text-gray-400 text-xs overflow-hidden shadow-sm sm:rounded-lg w-full h-8  border border-slate-700" value="{{ $item->password }}" suffix="eye" viewable="true" type="password" style="letter-spacing: .1em;font-weight: 600;" />
                                        </td>  

                                        <td class="td-class text:xs" style="text-align:center;vertical-align: baseline;background:#111b30;letter-spacing: .1em;font-weight: 600;text-transform: uppercase;">
                                            <button class="inline-flex items-center px-4 py-2 h-8 ml-2  bg-gray-900 dark:bg-gray-900 border  border-indigo-400 rounded-md font-semibold text-xs text-white dark:text-indigo-400 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-indigo-400 dark:hover:text-gray-900  dark:active:text-indigo-400" onclick="Update('{{ $item->id }}')" type="button">
                                                EDIT
                                            </button>
                                        </td>    
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                    <input id="InputForCaptureModal" value="" hidden />
 

                    <x-bladewind::modal
                    size="medium"
                    center_action_buttons="true"
                    type="warning"
                    title="Confirm Edit"
                    :ok_button_action="'updateData()'"
                    close_after_action="true"
                    name="custom-actions"
                    show_close_icon="true"
                    ok_button_label="Yes, save"
                    blur_backdrop="false"
                    cancel_button_label="don't save">
                    Are you sure you want to edit this data? This action cannot be undone.
                    </x-bladewind::modal>
                 
                    <script>
                        
                        function Update(id)
                        {
                            document.getElementById('InputForCaptureModal').value = id;
                            showModal('custom-actions');
                        }

                        function updateData() 
                        {
                            var id = document.getElementById('InputForCaptureModal').value;     
                            var data = [];
                            var type = document.getElementById('Parameters_Type' + id).value;
                            var url = document.getElementById('Parameters_Url' + id).value;
                            var username = document.getElementById('Parameters_Username' + id).value;
                            var password = document.getElementById('Parameters_Password' + id).value;
                            data.push({type, url, username, password}); 
                             
                            
                            $.ajax({
                                    url: "/Parameters",
                                    type: "POST",
                                    data: JSON.stringify(data),
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                    },
                                    dataType: "json",
                                    success: function(result)
                                    {  
                                        showNotification(type, type+' updated successfully','success',3);                              
                                    },
                                    error: function (xhr, status, error) 
                                    {
                                        showNotification("Can't Update Data", error ,'warning',5);
                                    }
                                });
 
                         }
                    </script>
                
             
        </div>
    </div>
    
</x-app-layout>
 