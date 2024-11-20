<x-app-layout>
 
 
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        
                <div class="max-w-xl mx-auto">
                    <div id="errorAlert" class="px-4 py-3 text-center rounded relative items-center" hidden role="alert" style="color: #084298;background: #cfe2ff;border-color: #b6d4fe;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="currentColor" class="bi bi-exclamation-triangle-fill inline-block align-middle me-2" viewBox="0 0 16 16" role="img" aria-label="Warning:">
                            <path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"></path>
                        </svg>
                        <strong class="font-bold text-sm inline-block align-middle">მოხდა შეცდომა!</strong>
                        <span class="block sm:inline text-sm">ჩაწერე აბონენტის ნომერი</span>
                    </div> <br>
                </div>

                <div class="flex h-10" style="justify-content: center;">

                    <div class="relative ml-2 sm:w-1/3 w-full">
                        <input class="bw-input peer searchByinstall dark:bg-gray-800 dark:text-gray-400 text-center text-xs overflow-hidden shadow-sm sm:rounded-lg  h-8 border border-slate-700 pl-8" style="font-weight: 600;" type="text" id="searchByinstall" name="searchByinstall"   autocomplete="off" placeholder="ჩაწერე აბონენტის ნომერი"   required>
                        <svg class="absolute left-2 top-2 w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"></path>
                        </svg>
                    </div>

                    <button onclick="abnom_search_crm()" id="gsearchbutton" type="submit" class="inline-flex items-center px-4 py-2 h-8 ml-2 disabled:opacity-50 disabled:pointer-events-none  bg-gray-900 dark:bg-gray-900 border  border-indigo-400 rounded-md font-semibold text-xs text-white dark:text-indigo-400 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-indigo-400 dark:hover:text-gray-900  dark:active:text-indigo-400">
                        SEARCH
                    </button>

                    <button  onclick="startFilter()" type="submit" class="inline-flex items-center px-4 py-2 h-8 ml-2  bg-gray-700 dark:bg-gray-900 border border-slate-300 shadow-lg dark:border-indigo-400 rounded-md font-semibold text-xs text-gray-200 dark:text-indigo-400 uppercase tracking-widest hover:bg-slate-400 dark:hover:bg-indigo-400 dark:hover:text-gray-900  dark:active:text-indigo-400">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6h9.75M10.5 6a1.5 1.5 0 1 1-3 0m3 0a1.5 1.5 0 1 0-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-9.75 0h9.75" />
                        </svg>
                    </button>
                </div>
        </div>

                        
                <center><br>
                            <div class="mx-auto sm:px-6 lg:px-8 overflow-x-hidden bg-gray-300/50 dark:bg-gray-800/50 rounded-lg shadow-lg" id="data_div_1" style="height: 55rem;max-width: 100rem;">
                                <br><div id="user_info"         style="display: flex; justify-content: center; align-items: center;"></div>
                                <br><div id="select_crm"        style="display: flex; justify-content: center; align-items: center;"></div>
                                <br><div id="refresh"           style="display: flex; justify-content: center; align-items: center;"></div>
                                <br><div id="data_from_olt"     class="justify-center items-center"  style="justify-content: center; align-items: center;"></div>  
                            </div>
                </center>

       
    </div>

    <x-bladewind::modal
        type="warning"
        title="Uninstall"
        name="uninstall-modal-installside"
        show_close_icon="true"
        size="xl"
        cancel_button_label=""
        ok_button_label=""
        backdrop_can_close="true"
        blur_backdrop="false"> 

        <div style="max-height: 400px; overflow-y: auto;" id="ontListUninstallBody"></div>
        <br>
        <div id="resultBodyLast"></div>

    </x-bladewind::modal>


    <x-bladewind::modal
        type="info"
        title="Filter Search"
        name="Filter-Modal"
        show_close_icon="true"
        size="omg"
        cancel_button_label=""
        ok_button_label=""
        backdrop_can_close="true"
        blur_backdrop="false">
        
        
        <div style="max-height: 800px; overflow-y: auto;">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-6">
                <x-bladewind::input name="user_id" placeholder="აბონ. ნომერი" class="dark:bg-gray-900  text-xs overflow-hidden shadow-sm sm:rounded-lg w-full h-10  border border-slate-700"  style="color:#fde68a;" />
                <x-bladewind::input name="old_contract_num" placeholder="ხელშ. ნომერი" class="dark:bg-gray-900 dark:text-gray-400 text-xs overflow-hidden shadow-sm sm:rounded-lg w-full h-10  border border-slate-700"   style="color:#fde68a;" />
            </div>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mt-6">
                <select name="type" id="disabled" onchange="ChangeCollor('disabled')"  class="dark:bg-gray-900 dark:text-gray-400 text-xs overflow-hidden shadow-sm sm:rounded-lg w-full h-10  border border-slate-700" required>
                    <option selected value="0">გაუქმებული</option>
                    <option value="1">ყველა გაუქმებული</option>
                    <option value="2">გარდა გაუქმებულის</option>
                </select>

                <select name="type" id="iuridiuli" onchange="ChangeCollor('iuridiuli')"  class="dark:bg-gray-900 dark:text-gray-400 text-xs overflow-hidden shadow-sm sm:rounded-lg w-full h-10  border border-slate-700" required>
                    <option selected value="0">იურ. პირები</option>
                    <option value="1">ყველა იურ. პირები</option>
                    <option value="2">გარდა იურ. პირების</option>
                </select>

                <select name="type" id="optika" onchange="ChangeCollor('optika')"  class="dark:bg-gray-900 dark:text-gray-400 text-xs overflow-hidden shadow-sm sm:rounded-lg w-full h-10  border border-slate-700" required>
                    <option selected value="0">ოპტიკა</option>
                    <option value="1">ყველა ოპტიკა</option>
                    <option value="2">გარდა ოპტიკის</option>
                </select>

                <select name="type" id="TV" onchange="ChangeCollor('TV')"  class="dark:bg-gray-900 dark:text-gray-400 text-xs overflow-hidden shadow-sm sm:rounded-lg w-full h-10  border border-slate-700" required>
                    <option selected value="0">TV</option>
                    <option value="1">ყველა TV</option>
                    <option value="2">გარდა TV -ს</option>
                </select>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mt-6">
                <x-bladewind::input name="user_name" placeholder="სახელი" class="dark:bg-gray-900 dark:text-gray-400 text-xs overflow-hidden shadow-sm sm:rounded-lg w-full h-10  border border-slate-700"   style="color:#fde68a;" />
                <x-bladewind::input name="user_lastname" placeholder="გვარი" class="dark:bg-gray-900 dark:text-gray-400 text-xs overflow-hidden shadow-sm sm:rounded-lg w-full h-10  border border-slate-700"   style="color:#fde68a;" />
                <x-bladewind::input name="phone" placeholder="ტელეფონი" class="dark:bg-gray-900 dark:text-gray-400 text-xs overflow-hidden shadow-sm sm:rounded-lg w-full h-10  border border-slate-700"   style="color:#fde68a;" />
                <x-bladewind::input name="misamarti" placeholder="მისამართი" class="dark:bg-gray-900 dark:text-gray-400 text-xs overflow-hidden shadow-sm sm:rounded-lg w-full h-10  border border-slate-700"    style="color:#fde68a;"/>
            </div>



            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mt-6">
                <x-bladewind::input name="personal_id" placeholder="პირადი ნომერი ან ს/კ" class="dark:bg-gray-900 dark:text-gray-400 text-xs overflow-hidden shadow-sm sm:rounded-lg w-full h-10  border border-slate-700"    style="color:#fde68a;"/>
                <x-bladewind::input name="filter_company_name" placeholder="კომპანია" class="dark:bg-gray-900 dark:text-gray-400 text-xs overflow-hidden shadow-sm sm:rounded-lg w-full h-10  border border-slate-700"   style="color:#fde68a;" />
                <x-bladewind::input name="user_ip" placeholder="აბონენტის IP" class="dark:bg-gray-900 dark:text-gray-400 text-xs overflow-hidden shadow-sm sm:rounded-lg w-full h-10  border border-slate-700"    style="color:#fde68a;"/>
                <x-bladewind::input name="antenna_ip" placeholder="Router IP" class="dark:bg-gray-900 dark:text-gray-400 text-xs overflow-hidden shadow-sm sm:rounded-lg w-full h-10  border border-slate-700"   style="color:#fde68a;" />
            </div>


            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mt-6">
                <x-bladewind::input name="sector_ip" placeholder="Sector IP" class="dark:bg-gray-900 dark:text-gray-400 text-xs overflow-hidden shadow-sm sm:rounded-lg w-full h-10  border border-slate-700"   style="color:#fde68a;" />
    

                <x-bladewind::datepicker
                    name="date_picker"
                    default_date=""
                    type="range"
                    format="yyyy-mm-dd"
                    label="Date"
                    placeholder="Select a date"
                    required="false"
                    with_time="false"
                    hours_as="12"
                    time_format="hh:mm"
                    show_seconds="false" 
                />
                <script>
            
                    const divElements = document.querySelectorAll('.bg-white.dark\\:bg-dark-600.mt-12.p-4.absolute.top-0.left-0.z-50.shadow-md.rounded-lg');
                    divElements.forEach(divElement => { 
                        divElement.style.backgroundColor = '#020617';
                    });
                
                    const divElements2 = document.querySelectorAll('.text-lg.text-white\\/90.dark\\:text-gray-400.cursor-default');
                    divElements2.forEach(divElement2 => { 
                        divElement2.style.color = '#e5e7eb';
                    });
                
    


                    let startDateElement = document.getElementsByName("start_date")[0];
                    startDateElement.style.fontSize = '0.75rem';
                    startDateElement.style.color='#9ca3af';
                    startDateElement.style.borderRadius = "0.5rem";
                    startDateElement.style.borderColor = "#334155";
                    startDateElement.style.backgroundColor = "#111827";

                    let endDateElement = document.getElementsByName("end_date")[0];
                    endDateElement.style.fontSize = '0.75rem';
                    endDateElement.style.color='#9ca3af';
                    endDateElement.style.borderRadius = "0.5rem";
                    endDateElement.style.borderColor = "#334155";
                    endDateElement.style.backgroundColor = "#111827";

                </script>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mt-6">
                <select name="town" id="town"   onchange="region_change('town')"  class="dark:bg-gray-900 dark:text-gray-400 text-xs overflow-hidden shadow-sm sm:rounded-lg w-full h-10  border border-slate-700" required>
                    <option selected value="0">რეგიონი</option>
                </select>

                <select name="subregion" id="subregion"    onchange="ChangeCollor('subregion')"  class="dark:bg-gray-900 dark:text-gray-400 text-xs overflow-hidden shadow-sm sm:rounded-lg w-full h-10  border border-slate-700" required>
                    <option selected value="0">რაიონი/სოფელი</option>
                </select>

                <select name="filter_tariff" id="filter_tariff"   onchange="ChangeCollor('filter_tariff')"  class="dark:bg-gray-900 dark:text-gray-400 text-xs overflow-hidden shadow-sm sm:rounded-lg w-full h-10  border border-slate-700" required>
                    <option selected value="0">ტარიფი</option>
                </select> 

                <select name="filter_status" id="filter_status" onchange="ChangeCollor('filter_status')"  class="dark:bg-gray-900 dark:text-gray-400 text-xs overflow-hidden shadow-sm sm:rounded-lg w-full h-10  border border-slate-700" required>
                    <option selected value="0">მომხ. სტატუსი</option>
                    <option  value="9">არ არის აქტივირებული</option>
                    <option  value="1">აქტიური</option>
                    <option  value="2">დროებით ჩართული</option>
                    <option  value="3">უფასო შეჩერება</option>
                    <option  value="4">ფასიანი შეჩერება</option>
                    <option  value="5">კრედიტი</option>
                    <option  value="6">გამორთულია დავალიანების გამო</option>
                    <option  value="8">გამორთული</option>
                    <option  value="-1">გაუქმებული</option>
                </select>

            </div>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mt-6">
                <select name="provider" id="provider" onchange="ChangeCollor('provider')"  class="dark:bg-gray-900 dark:text-gray-400 text-xs overflow-hidden shadow-sm sm:rounded-lg w-full h-10  border border-slate-700" required>
                    <option selected value="0">პროვაიდერი</option>
                    <option  value="111">Airlink</option>
                    <option  value="112">CityNet</option>
                    <option  value="133">Netcom</option>
                    <option  value="153">Netcom Plus</option>
                    <option  value="154">შპს მარია</option>
                    <option  value="155">წალკა</option>
                    <option  value="156">სკოლები</option>
                </select>
                <select name="legal_status" id="legal_status" onchange="ChangeCollor('legal_status')" onchange class="dark:bg-gray-900 dark:text-gray-400 text-xs overflow-hidden shadow-sm sm:rounded-lg w-full h-10  border border-slate-700" required>
                    <option selected value="0">იურ. სტატუსი</option>
                    <option  value="28">კერძო პირი</option>
                    <option  value="29">შ.პ.ს</option>
                    <option  value="30">ინდ. მეწარმე</option>
                    <option  value="31">არასამთავრობო</option>
                    <option  value="32">საბიუჯეტო</option>
                </select>
                <x-bladewind::input name="Filtermac" placeholder="MAC" class="dark:bg-gray-900 dark:text-gray-400 text-xs overflow-hidden shadow-sm sm:rounded-lg w-full h-10  border border-slate-700"    style="color:#fde68a;"/>
                <x-bladewind::input name="tvmac" placeholder="TV MAC" class="dark:bg-gray-900 dark:text-gray-400 text-xs overflow-hidden shadow-sm sm:rounded-lg w-full h-10  border border-slate-700"    style="color:#fde68a;"/>
            </div>


            <button onclick="FilterReset()" type="submit" class="inline-flex items-center px-4 py-2 h-8 ml-2  bg-gray-900  dark:bg-gray-900 border  border-red-400 rounded-md font-semibold text-xs text-white dark:text-red-400 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-red-400 dark:hover:text-gray-900  dark:active:text-red-400">
                    RESET
            </button>
            <center><br>
                <button onclick="Installsidefilter()" type="submit" class="inline-flex items-center px-4 py-2 h-8 ml-2  bg-gray-700 dark:bg-gray-900 border border-slate-300 shadow-lg dark:border-indigo-400 rounded-md font-semibold text-xs text-gray-200 dark:text-indigo-400 uppercase tracking-widest hover:bg-slate-400 dark:hover:bg-indigo-400 dark:hover:text-gray-900  dark:active:text-indigo-400">
                    SEARCH
                </button>
            </center>


            <div id="filterResultTable"></div>

        </div>
        
    </x-bladewind::modal>
     
    <input id="Input_user" value="" hidden />
    <input id="Input_ifIndex" value="" hidden />
    <input id="Input_Ip" value="" hidden />
    <input id="Input_write" value="" hidden />
    <input id="Input_read" value="" hidden />
    <input id="Input_port" value="" hidden />
    <input id="Input_PortCount" value="" hidden />
    <input id="Input_ontMac" value="" hidden />
    <input id="Input_ontDbm" value="" hidden />
    <input id="Input_portFullName" value="" hidden />
    <input id="Input_tv_tarrif" value="" hidden />
    <input id="Input_port_forward_user" value="" hidden />
    <input id="Input_add_tarrif" value="" hidden />
    <input id="Input_tv_account" value="" hidden />
    <input id="Input_tv_device" value="" hidden />
    <input id="Input_tv_mac" value="" hidden />
    <input id="Input_tv_adress" value="" hidden />
    <input id="Input_tv_unique" value="" hidden />
    <input id="Input_tv_buttonID" value="" hidden />

    <input id="Input_uninstall_ab_nom" value="" hidden />
    <input id="Input_uninstall_olt_ip" value="" hidden />
    <input id="Input_uninstall_olt_ifindex" value="" hidden />

    

    

    <x-bladewind::notification position="top right" />
    <x-bladewind::notification />
    
    <x-bladewind.modal
        size="medium"
        center_action_buttons="true"
        type="warning"
        title="Confirm ONT Uninstall"
        :ok_button_action="'ONT_UNINSTALL_FIN()'"
        close_after_action="true"
        name="ONT_UNINSTALL_CONFIRM"
        show_close_icon="true"
        blur_backdrop="false"
        ok_button_label="Yes, Uninstall"
        cancel_button_label="Don't Uninstall">
        Are you sure you want to uninstall onu ? This action cannot be undone
    </x-bladewind.modal>

    <x-bladewind.modal
        size="medium"
        center_action_buttons="true"
        type="warning"
        title="Confirm Zyxel Install"
        :ok_button_action="'Zyxel_install()'"
        close_after_action="true"
        name="zyxel_install"
        show_close_icon="true"
        blur_backdrop="false"
        ok_button_label="Yes, Install"
        cancel_button_label="Don't Install">
        Are you sure you want to install zyxel ? This action cannot be undone
    </x-bladewind.modal>


    <x-bladewind.modal
        size="medium"
        center_action_buttons="true"
        type="warning"
        title="Confirm Bdcom Install"
        :ok_button_action="'Bdcom_install()'"
        close_after_action="true"
        name="bdcom_install"
        show_close_icon="true"
        blur_backdrop="false"
        ok_button_label="Yes, Install"
        cancel_button_label="Don't Install">
        Are you sure you want to install ont on Bdcom ? This action cannot be undone
    </x-bladewind.modal>

    <x-bladewind.modal
        size="medium"
        center_action_buttons="true"
        type="warning"
        title="Confirm Vsolution Install"
        :ok_button_action="'Vsol_install()'"
        close_after_action="true"
        name="vsol_install"
        show_close_icon="true"
        blur_backdrop="false"
        ok_button_label="Yes, Install"
        cancel_button_label="Don't Install">
        Are you sure you want to install ont on Vsolution ? This action cannot be undone
    </x-bladewind.modal>

    <x-bladewind.modal
        size="medium"
        center_action_buttons="true"
        type="warning"
        title="Confirm Hsgq Install"
        :ok_button_action="'Hsgq_install()'"
        close_after_action="true"
        name="hsgq_install"
        show_close_icon="true"
        blur_backdrop="false"
        ok_button_label="Yes, Install"
        cancel_button_label="Don't Install">
        Are you sure you want to install ont on HSGQ ? This action cannot be undone
    </x-bladewind.modal>
     

    <x-bladewind.modal
        size="medium"
        center_action_buttons="true"
        type="warning"
        title="Confirm Cisco Install"
        :ok_button_action="'Cisco_install()'"
        close_after_action="true"
        name="cisco_install"
        show_close_icon="true"
        blur_backdrop="false"
        ok_button_label="Yes, Install"
        cancel_button_label="Don't Install">
        Are you sure you want to install Cisco ? This action cannot be undone
    </x-bladewind.modal>


    <x-bladewind.modal
        size="medium"
        center_action_buttons="true"
        type="warning"
        title="Confirm Airsoft Install"
        :ok_button_action="'crm_install()'"
        close_after_action="true"
        name="crm-install-confirm"
        show_close_icon="true"
        blur_backdrop="false"
        ok_button_label="Yes, Restart"
        cancel_button_label="Don't Restart">
        Are you sure you want to install client in Airsoft ? This action cannot be undone
    </x-bladewind.modal>

    <x-bladewind.modal
        size="medium"
        center_action_buttons="true"
        type="warning"
        title="TMSCreate Account"
        :ok_button_action="'TMS_CREATE_ACCOUNT()'"
        close_after_action="true"
        name="account_add_action_modal"
        show_close_icon="true"
        blur_backdrop="false"
        ok_button_label="Yes, Create"
        cancel_button_label="Don't Create">
      

        <div class="flex items-center justify-center" id="account_create_select"></div> 
    </x-bladewind.modal>

    <x-bladewind.modal
        size="medium"
        center_action_buttons="true"
        type="warning"
        title="TMS Device Bind"
        :ok_button_action="'tvbox_install()'"
        close_after_action="true"
        name="device-bind-confirm"
        show_close_icon="true"
        blur_backdrop="false"
        ok_button_label="Yes, Bind"
        cancel_button_label="Don't Bind">
        Are you sure you want to bind device ? This action cannot be undone
    </x-bladewind.modal>
     

    <x-bladewind.modal
        size="medium"
        center_action_buttons="true"
        type="warning"
        title="Confirm Tarrif Delete"
        :ok_button_action="'Delete_Tarrif_From_Account()'"
        close_after_action="true"
        name="Tarrif-Delete"
        show_close_icon="true"
        blur_backdrop="false"
        ok_button_label="Yes, Delete"
        cancel_button_label="Don't Delete">
        Are you sure you want to delete tarrif ? This action cannot be undone
    </x-bladewind.modal>

    <x-bladewind.modal
        size="medium"
        center_action_buttons="true"
        type="warning"
        title="Confirm Device Delete"
        :ok_button_action="'delete_device_install()'"
        close_after_action="true"
        name="delete-binded-device"
        show_close_icon="true"
        blur_backdrop="false"
        ok_button_label="Yes, Delete"
        cancel_button_label="Don't Delete">
        Are you sure you want to delete tvbox ? This action cannot be undone
    </x-bladewind.modal>

    <x-bladewind.modal
        size="xl"
        show_action_buttons="false"
        type="warning"
        title="TMS Bind Device"
        :ok_button_action="'TMS_ADD_DEVICE()'"
        close_after_action="true"
        show_close_icon="true"
        name="bind-device"
        blur_backdrop="false">
      
        <div class="flex items-center justify-center" id="bind_tms_device_refresh_body"></div><br>

        <div class="flex w-full sm:w-1/3">
            <div class="relative flex-1">
                <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                    <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"/>
                    </svg>
                </div>
                <input type="search" id="TMS_SEARCH_DEVICE_BY_MAC_INPUT" class="block w-full p-2 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-gray-800 dark:bg-gray-900  dark:border-gray-600  dark:placeholder-gray-600 dark:text-gray-400  dark:focus:ring-gray-900 dark:focus:border-gray-600" placeholder="search" required />
            </div>  
            <button type="submit" onclick="TMS_SEARCH_DEVICE_BY_MAC()" class="inline-flex items-center px-4 py-2 ml-2 bg-gray-900 dark:bg-gray-900 border  border-indigo-400 rounded-md font-semibold text-xs text-white dark:text-indigo-400 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-indigo-400  dark:hover:text-gray-900  dark:active:text-indigo-400 ">
            SEARCH BY MAC
            </button>           
        </div><br>

        <div  style="max-height: 500px; overflow-y: scroll;">
            <div class="flex items-center justify-center" id="bind_tms_mac_device_body"></div><br>
            <div class="flex items-center justify-center" id="bind_tms_device_body"></div>
        </div> 
    </x-bladewind.modal>

     
    <x-bladewind.modal
        size="medium"
        center_action_buttons="true"
        type="warning"
        title="Confirm Account Delete"
        :ok_button_action="'TMS_ACCOUNT_DELETE()'"
        close_after_action="true"
        name="Account-Delete"
        show_close_icon="true"
        blur_backdrop="false"
        ok_button_label="Yes, Delete"
        cancel_button_label="Don't Delete">
        Are you sure you want to delete account ? This action cannot be undone
    </x-bladewind.modal>
     

    <x-bladewind.modal
        size="medium"
        center_action_buttons="true"
        type="warning"
        title="TMS Add Tarrif"
        :ok_button_action="'TMS_CREATE_TARRIF()'"
        close_after_action="true"
        name="tarrif_add_action_modal"
        show_close_icon="true"
        blur_backdrop="false"
        ok_button_label="Yes, Add"
        cancel_button_label="Don't Add">
      

        <div class="flex items-center justify-center" id="tarrif_create_select"></div> 
    </x-bladewind.modal>

    <x-bladewind.modal
        size="medium"
        center_action_buttons="true"
        type="warning"
        title="Confirm Onu Restart"
        :ok_button_action="'restartOnu()'"
        close_after_action="true"
        name="custom-actions"
        show_close_icon="true"
        blur_backdrop="false"
        ok_button_label="Yes, Restart"
        cancel_button_label="Don't Restart">
        Are you sure you want to restart onu ? This action cannot be undone

        <br><br>
        <div class="flex flex-col items-center justify-center">

            <textarea id="additional_info_zte_onu_restart" placeholder="ჩაწერე კომენტარი" class="dark:bg-gray-900 dark:text-gray-400 text-xs overflow-hidden shadow-sm sm:rounded-lg w-full h-24 border border-slate-700 p-2" rows="4"></textarea>
            <br>
            <center><p id="zte_onu_restart_comment_empty_" class="text-red-400 text-sm"></p></center>
        </div>
    </x-bladewind.modal>

    <x-bladewind.modal
        size="medium"
        center_action_buttons="true"
        type="warning"
        title="Confirm Onu Restart"
        :ok_button_action="'restartOnu_Huawei()'"
        close_after_action="true"
        name="huawei-onu-restart"
        show_close_icon="true"
        blur_backdrop="false"
        ok_button_label="Yes, Restart"
        cancel_button_label="Don't Restart">
        Are you sure you want to restart onu ? This action cannot be undone

        <br><br>
        <div class="flex flex-col items-center justify-center">

            <textarea id="additional_info_huawei_onu_restart" placeholder="ჩაწერე კომენტარი" class="dark:bg-gray-900 dark:text-gray-400 text-xs overflow-hidden shadow-sm sm:rounded-lg w-full h-24 border border-slate-700 p-2" rows="4"></textarea>
            <br>
            <center><p id="huawei_onu_restart_comment_empty_" class="text-red-400 text-sm"></p></center>
        </div>

    </x-bladewind.modal>

    <x-bladewind.modal
        size="medium"
        center_action_buttons="true"
        type="warning"
        title="Confirm Onu Restart"
        :ok_button_action="'restartOnu_install_hsgq()'"
        close_after_action="true"
        name="hsgq-onu-restart"
        show_close_icon="true"
        blur_backdrop="false"
        ok_button_label="Yes, Restart"
        cancel_button_label="Don't Restart">
        Are you sure you want to restart onu ? This action cannot be undone

        <br><br>
        <div class="flex flex-col items-center justify-center">

            <textarea id="additional_info_hsgq_onu_restart" placeholder="ჩაწერე კომენტარი" class="dark:bg-gray-900 dark:text-gray-400 text-xs overflow-hidden shadow-sm sm:rounded-lg w-full h-24 border border-slate-700 p-2" rows="4"></textarea>
            <br>
            <center><p id="hsgq_onu_restart_comment_empty_" class="text-red-400 text-sm"></p></center>
        </div>
    </x-bladewind.modal>

    <x-bladewind.modal
        size="medium"
        center_action_buttons="true"
        type="warning"
        title="Confirm Onu Restart"
        :ok_button_action="'restartOnu_install_bdcom()'"
        close_after_action="true"
        name="bdcom-onu-restart"
        show_close_icon="true"
        blur_backdrop="false"
        ok_button_label="Yes, Restart"
        cancel_button_label="Don't Restart">
        Are you sure you want to restart onu ? This action cannot be undone

        
        <br><br>
        <div class="flex flex-col items-center justify-center">

            <textarea id="additional_info_bdcom_onu_restart" placeholder="ჩაწერე კომენტარი" class="dark:bg-gray-900 dark:text-gray-400 text-xs overflow-hidden shadow-sm sm:rounded-lg w-full h-24 border border-slate-700 p-2" rows="4"></textarea>
            <br>
            <center><p id="bdcom_onu_restart_comment_empty_" class="text-red-400 text-sm"></p></center>
        </div>

    </x-bladewind.modal>

    <x-bladewind.modal
        size="medium"
        center_action_buttons="true"
        type="warning"
        title="Confirm Onu Restart"
        :ok_button_action="'restartOnu_install_vsolution()'"
        close_after_action="true"
        name="vsolution-onu-restart"
        show_close_icon="true"
        blur_backdrop="false"
        ok_button_label="Yes, Restart"
        cancel_button_label="Don't Restart">
        Are you sure you want to restart onu ? This action cannot be undone

               
        <br><br>
        <div class="flex flex-col items-center justify-center">

            <textarea id="additional_info_vsol_onu_restart" placeholder="ჩაწერე კომენტარი" class="dark:bg-gray-900 dark:text-gray-400 text-xs overflow-hidden shadow-sm sm:rounded-lg w-full h-24 border border-slate-700 p-2" rows="4"></textarea>
            <br>
            <center><p id="vsol_onu_restart_comment_empty_" class="text-red-400 text-sm"></p></center>
        </div>
    </x-bladewind.modal>

    <x-bladewind::modal
        type="info"
        title="ONT DETAILS"
        name="Ont-Details"
        show_close_icon="true"
        size="omg"
        backdrop_can_close="true"
        show_action_buttons="true"
        cancel_button_label=""
        ok_button_label=""
        blur_backdrop="false">

        <div class="flex items-center justify-center" style="max-height: 500px; overflow-y: auto;" id="ont-details-refresh"></div><br>
        <div class="flex items-center justify-center" style="max-height: 500px; overflow-y: auto;" id="ont-details-body"></div>

    </x-bladewind::modal>

    <x-bladewind::modal
        type="info"
        title="Pon Status"
        name="Pon-Status"
        show_close_icon="true"
        size="omg"
        backdrop_can_close="true"
        show_action_buttons="true"
        cancel_button_label=""
        ok_button_label=""
        blur_backdrop="false">

        <div class="flex items-center justify-center" style="max-height: 500px; overflow-y: auto;" id="Pon_Modal_Select"></div> <br>
        <div class="flex items-center justify-center" style="max-height: 700px; overflow-y: auto;display:block;" id="Pon_Modal_Body"></div>
 
    </x-bladewind::modal>


    <input id="Install_input_1" value="" hidden />
    <input id="Install_input_2" value="" hidden />
    <input id="Install_input_3" value="" hidden />
    <input id="Install_input_4" value="" hidden />
    <input id="Install_input_5" value="" hidden />
    <input id="Install_input_6" value="" hidden />
    <input id="Install_input_7" value="" hidden />
    <input id="Install_input_8" value="" hidden />
    <input id="Install_input_9" value="" hidden />
    <input id="Install_input_10" value="" hidden />
    <input id="Install_input_11" value="" hidden />
    <input id="Install_input_12" value="" hidden />
    <input id="Install_input_13" value="" hidden />
    <input id="Install_input_14" value="" hidden />
    <input id="Install_input_15" value="" hidden />


    <x-bladewind::modal
    type="info"
    title="Switch Status"
    name="Switch-Status"
    show_close_icon="true"
    size="omg"
    backdrop_can_close="true"
    show_action_buttons="true"
    cancel_button_label=""
    ok_button_label=""
    blur_backdrop="false">

    <center>
        <div class="flex items-center justify-center" style="max-height: 500px; overflow-y: auto;" id="Switch_Modal_Select"></div> <br>
        <div class="flex items-center justify-center" style="max-height: 700px; overflow-y: auto;display:block;" id="Switch_Modal_Body"></div>
    </center>

    </x-bladewind::modal>
    
  
    <x-bladewind.modal
        size="medium"
        center_action_buttons="true"
        type="warning"
        title="Confirm Airsoft Install"
        :ok_button_action="'FinishInstall()'"
        close_after_action="true"
        name="airsoft-confirm"
        show_close_icon="true"
        blur_backdrop="false"
        ok_button_label="Yes, Install"
        cancel_button_label="Don't Install">
        Are you sure you want to install client in airsoft ? This action cannot be undone
    </x-bladewind.modal>
     
      
    <x-bladewind.modal
        size="medium"
        center_action_buttons="true"
        type="warning"
        title="Confirm ZTE Install"
        :ok_button_action="'FinishZTEInstall()'"
        close_after_action="true"
        name="zte_install"
        show_close_icon="true"
        blur_backdrop="false"
        ok_button_label="Yes, Install"
        cancel_button_label="Don't Install">
        Are you sure you want to install ont in ZTE ? This action cannot be undone
    </x-bladewind.modal>
     

    <x-bladewind.modal
        size="medium"
        center_action_buttons="true"
        type="warning"
        title="Confirm HUAWEI Install"
        :ok_button_action="'FinishHUAWEIInstall()'"
        close_after_action="true"
        name="huawei_install"
        show_close_icon="true"
        blur_backdrop="false"
        ok_button_label="Yes, Install"
        cancel_button_label="Don't Install">
        Are you sure you want to install ont in HUAWEI ? This action cannot be undone
    </x-bladewind.modal>
    
    <x-bladewind::modal
        type="info"
        title="INSTALL"
        name="crm-install-modal"
        show_close_icon="true"
        size="xl"
        backdrop_can_close="false"
        show_action_buttons="true"
        cancel_button_label=""
        ok_button_label=""
        blur_backdrop="false">

        <div   id="forScrollDiv" class="overflow-y-scroll" style="max-height: 45rem;">
            <div class="flex items-center justify-center" style="display:block;" id="Airsoft_Install_Modal_Ethernet_Body"></div><br>
            <div class="flex items-center justify-center" style="display:block;" id="Airsoft_Install_Modal_Ethernet_Body_2"></div> 
            <div class="flex items-center justify-center" style="display:block;" id="Airsoft_Install_Modal_Ethernet_Body_3"></div> 
            <div class="flex items-center justify-center" style="display:block;" id="Airsoft_Install_Modal_Ethernet_Body_4"></div> 
            <div class="flex items-center justify-center" style="display:block;" id="Airsoft_Install_Modal_Ethernet_Body_5"></div> 
            <div class="flex items-center justify-center" style="display:block;" id="Airsoft_Install_Modal_Body"></div>
        </div>
    </x-bladewind::modal>

    <script>

        function abnom_search_crm()
        {
            var inputValue = document.getElementById('searchByinstall').value;    
            if (inputValue === '' || inputValue.length < 4)
            {
                var alertDiv = document.getElementById('errorAlert');
                alertDiv.hidden = false;
                setTimeout(function() {
                    alertDiv.hidden = true;
                }, 2000);
            }
            else
            {
                localStorage.setItem('start_install_search_by_abnom', inputValue);
                location.reload();
            }
        }


        

        window.onload = function()
        {
            let abnom = localStorage.getItem('start_install_search_by_abnom');
            if (abnom)
            {
                document.getElementById('searchByinstall').value = abnom.trim();
                localStorage.removeItem('start_install_search_by_abnom');
                
                startInstallByAbnom(abnom); 
            }
            else
            {
                const urlParams = new URLSearchParams(window.location.search);
                if (urlParams.has('id'))
                {
                    let ab_nom_from_url = urlParams.get('id'); 
                    if (ab_nom_from_url)
                    {               
                        document.getElementById('searchByinstall').value = ab_nom_from_url.trim();
                        startInstallByAbnom(ab_nom_from_url); 
                    }
                }
            }
        };

        function startInstallByAbnom(abnom)
        {

            $.ajax({
                url: "/airsoft-search",
                data:{'ab_nom':abnom},
                type: "GET",
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                dataType: "json",
                success: function(data)
                {   
                    let result = JSON.parse(data);   console.log(result);

                    if(result.error)showNotification('Search Error', result.error,'warning',30);

                    if(result.status == 1)
                    {
                        
                        localStorage.setItem('_abnom_for_install_', abnom);

                        let legal_status = 'Unknow';
                        if (result.legal_status === '28')
                        {
                            legal_status = `<input  value="კერძო პირი"  disabled   class="dark:bg-gray-900 text-center dark:text-gray-400 text-xs overflow-hidden shadow-sm sm:rounded-lg w-full h-8  border border-slate-700" />`;
                        }
                        else if (result.legal_status === '29')
                        {
                            legal_status = `<input  value="შ.პ.ს"  disabled  style="color:#efc776" class="dark:bg-gray-900 text-center dark:text-gray-400 text-xs overflow-hidden shadow-sm sm:rounded-lg w-full h-8  border border-slate-700" />`;
                        }
                        else if (result.legal_status === '30')
                        {
                            legal_status = `<input  value="ინდ. მეწარმე"  disabled   style="color:#efc776;"  class="dark:bg-gray-900 text-center dark:text-gray-400 text-xs overflow-hidden shadow-sm sm:rounded-lg w-full h-8  border border-slate-700" />`;
                        }
                        else if (result.legal_status === '31')
                        {
                            legal_status = `<input  value="არასამთავრობო"  disabled style="color:#efc776;"  class="dark:bg-gray-900 text-center dark:text-gray-400 text-xs overflow-hidden shadow-sm sm:rounded-lg w-full h-8  border border-slate-700" />`;
                        }
                        else if (result.legal_status === '32')
                        {
                            legal_status = `<input  value="საბიუჯეტო"  disabled  style="color:#efc776;" class="dark:bg-gray-900 text-center dark:text-gray-400 text-xs overflow-hidden shadow-sm sm:rounded-lg w-full h-8  border border-slate-700" />`;
                        }

                        document.getElementById("user_info").innerHTML = `
                            <fieldset class="border border-gray-800 p-3 rounded" style="width: 100%;">
                            <legend class="text-sm text-gray-600 font-semibold uppercase tracking-widest">CLIENT</legend>

                                    <center>
                                        <div class="grid grid-cols-2 gap-4 mt-6">
                                            <span class="block text-sm font-medium text-indigo-400">აბონენტი</span>                   
                                            <span class="block text-sm font-medium text-indigo-400">სახ/გვარი</span>
                                        </div>

                                        <div class="grid grid-cols-2 gap-4 mt-1">
                                            <input  value="${result.user_id}"   disabled   class="dark:bg-gray-900 text-center dark:text-gray-400 text-xs overflow-hidden shadow-sm sm:rounded-lg w-full h-8  border border-slate-700" />
                                            <input  value="${result.user_name}  ${result.user_lastname}"  disabled   class="dark:bg-gray-900 text-center dark:text-gray-400 text-xs overflow-hidden shadow-sm sm:rounded-lg w-full h-8  border border-slate-700" /> 
                                        </div>

                                        <div class="grid grid-cols-2 gap-4 mt-6">
                                            <span class="block text-sm font-medium text-indigo-400">იურ. სტატუსი</span>
                                            <span class="block text-sm font-medium text-indigo-400">კომპანია</span>
                                        </div>

                                        <div class="grid grid-cols-2 gap-4 mt-1">
                                            ${legal_status}
                                            <input  value="${result.company_name}"  disabled   class="dark:bg-gray-900 text-center dark:text-gray-400 text-xs overflow-hidden shadow-sm sm:rounded-lg w-full h-8  border border-slate-700" />
                                        </div>

                                        <div class="grid grid-cols-2 gap-4 mt-6">
                                            <span class="block text-sm font-medium text-indigo-400">მისამართი</span>                   
                                            <span class="block text-sm font-medium text-indigo-400">სექტორის IP</span>
                                        </div>

                                        
                                        <div class="grid grid-cols-2 gap-4 mt-1">
                                            <input  value="${result.address}"   disabled   class="dark:bg-gray-900 text-center dark:text-gray-400 text-xs overflow-hidden shadow-sm sm:rounded-lg w-full h-8  border border-slate-700" />
                                            <input  value="${result.sector_ip}"  disabled   class="dark:bg-gray-900 text-center dark:text-gray-400 text-xs overflow-hidden shadow-sm sm:rounded-lg w-full h-8  border border-slate-700" /> 
                                        </div>

                                    </center>

                            </fieldset>
                        `;
                      
    
                        document.getElementById("select_crm").innerHTML = `
                        <br>
                        <select name="type" id="install_type_select" onchange="installChoose('${abnom}','${result.sector_ip}','${result.mac}','${result.sector_ip}')" class="w-full sm:w-1/5 dark:bg-gray-800 text-center dark:text-gray-400 text-xs overflow-hidden  sm:rounded-lg   h-10  border border-slate-700 uppercase tracking-widest" required>
                            <option  value="0"  disabled selected class="dark:bg-gray-900 text-center dark:text-gray-400 text-xs overflow-hidden shadow-sm sm:rounded-lg w-full h-10   border border-slate-700 uppercase tracking-widest">ინსტალაციის ტიპი</option>
                            <option  value="1"  class="dark:bg-gray-900 text-center dark:text-gray-400 text-xs overflow-hidden shadow-sm sm:rounded-lg w-full h-10   border border-slate-700 uppercase tracking-widest">ოპტიკა</option>
                            <option  value="2"  class="dark:bg-gray-900 text-center dark:text-gray-400 text-xs overflow-hidden shadow-sm sm:rounded-lg w-full h-10   border border-slate-700 uppercase tracking-widest">ანტენა</option>
                            <option  value="3"  class="dark:bg-gray-900 text-center dark:text-gray-400 text-xs overflow-hidden shadow-sm sm:rounded-lg w-full h-10   border border-slate-700 uppercase tracking-widest">ეზერნეტი</option>     
                            <option  value="4"  class="dark:bg-gray-900 text-center dark:text-gray-400 text-xs overflow-hidden shadow-sm sm:rounded-lg w-full h-10   border border-slate-700 uppercase tracking-widest">ტელევიზია</option>     
                            <option  value="5"  class="dark:bg-gray-900 text-center dark:text-gray-400 text-xs overflow-hidden shadow-sm sm:rounded-lg w-full h-10   border border-slate-700 uppercase tracking-widest">AIRSOFT</option>  
                            <option  value="6"  class="dark:bg-gray-900 text-center dark:text-red-400  text-xs overflow-hidden shadow-sm sm:rounded-lg w-full h-10   border border-slate-700 uppercase tracking-widest">UNINSTALL</option>                                        
                        </select>
                        `;
                        $(document).ready(function() 
                        {
                            $('#install_type_select').select2();      
                        });


                    }
                    else
                    {
                        document.getElementById("data_div_1").innerHTML = `<br>
                            <div class="sm:w-1/3 w-full" style="background-color: rgb(133 77 14 / 0.1) !important; border: 0.5px solid  rgb(113 63 18 /1) !important; color:rgb(234 179 8 /1) !important; border-radius: 0.375rem !important; padding: 1rem !important; font-size: 0.875rem !important;" role="alert">
                                    <div style="display: flex !important;">
                                        <div style="flex-shrink: 0 !important;">
                                            <svg style="flex-shrink: 0 !important; width: 1.5rem !important; height: 1.5rem !important; margin-top: 0.125rem !important;" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/>
                                                <path d="M12 9v4"/>
                                                <path d="M12 17h.01"/>
                                            </svg>
                                        </div>
                                        <div style="margin-left: 1rem !important;">
                                            <h3 style="font-size: 14px !important; text-align:left;font-weight: 600 !important;">Warning...</h3>
                                            <div style="margin-top: 0.25rem !important; font-size: 12px !important; color: rgb(161 98 7 / 1) !important;">აბონენტი ვერ მოიძებნა Airsoft - ში</div>
                                        </div>
                                    </div>
                                </div>
                            `;
                    }       
                },
                error: function (xhr, status, error) 
                { 
                    showNotification('Search Error ' + xhr.status, xhr.responseJSON.message,'error',30);
                }
            });

        }

        function InstallsideFilterStart(savedData)
        {
            //const currentUrl = window.location.href;
            const currentUrl = window.location.origin + window.location.pathname;
            window.open(`${currentUrl}?id=${savedData}`, '_blank');
        }


    </script>

 
</x-app-layout>
