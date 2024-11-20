<x-app-layout>
 

<div class="py-5">
        <div class="max-w-xl mx-auto">

                    <div id="errorAlert" class="px-4 py-3 text-center rounded relative items-center" hidden role="alert" style="color: #084298;background: #cfe2ff;border-color: #b6d4fe;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="currentColor" class="bi bi-exclamation-triangle-fill inline-block align-middle me-2" viewBox="0 0 16 16" role="img" aria-label="Warning:">
                            <path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"></path>
                        </svg>
                        <strong class="font-bold text-sm inline-block align-middle">მოხდა შეცდომა!</strong>
                        <span class="block sm:inline text-sm">ჩაწერე ოელტეს აიპი ან სახელი</span>
                    </div> <br>


                    <div class="flex h-10" style="justify-content: center;">

                        <div class="relative ml-2 w-1/2">
                            <input class="bw-input peer searchByOlt dark:bg-gray-800 dark:text-gray-400 text-center text-xs overflow-hidden shadow-sm sm:rounded-lg  h-8 border border-slate-700 pl-8" style="font-weight: 600;" type="text" id="searchByOlt" name="searchByOlt"   autocomplete="off" placeholder="ოელტეს ძებნა"   required>
                            <svg class="absolute left-2 top-2 w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"></path>
                            </svg>
                        </div>

                        <button onclick="olt_search()" type="submit" class="inline-flex items-center px-4 py-2 h-8 ml-2  bg-gray-700 dark:bg-gray-900 border border-slate-300 shadow-lg dark:border-indigo-400 rounded-md font-semibold text-xs text-gray-200 dark:text-indigo-400 uppercase tracking-widest hover:bg-slate-400 dark:hover:bg-indigo-400 dark:hover:text-gray-900  dark:active:text-indigo-400">
                            SEARCH
                        </button>
                    </div>
        </div>
        <br>
        
        <div class="flex  flex-grid  justify-center sm:space-x-4" id="SearchByName"></div> <br>
        <div class="flex  flex-grid  justify-center sm:space-x-4" id="buttonsDiv"></div> <br>
              
        

    
        <div class="flex flex-wrap justify-center  sm:space-x-4">

                    <div class="w-full sm:w-1/2 md:w-1/3 lg:w-1/4 xl:w-1/4 mb-4  overflow-y-scroll bg-gray-300/50 dark:bg-gray-800/50 dark:border border-gray-800 rounded-lg shadow-lg" style="max-height: 35rem;" id="dinfo">
                        <div class="max-w-full rounded overflow-hidden">
                            <div class="px-6 py-4">
                                <div class="flex overflow-hidden gap-2 items-start" id="header_dinfo">
                                    <div class="flex-shrink-0 w-6 h-6" style="color:#5c6bc0 !important;">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-activity" viewBox="0 0 16 16">
                                            <path fill-rule="evenodd" d="M6 2a.5.5 0 0 1 .47.33L10 12.036l1.53-4.208A.5.5 0 0 1 12 7.5h3.5a.5.5 0 0 1 0 1h-3.15l-1.88 5.17a.5.5 0 0 1-.94 0L6 3.964 4.47 8.171A.5.5 0 0 1 4 8.5H.5a.5.5 0 0 1 0-1h3.15l1.88-5.17A.5.5 0 0 1 6 2"/>
                                        </svg>
                                    </div>

                                    <hgroup class="flex flex-wrap items-baseline gap-x-2 overflow-hidden">
                                        <h2 class="text-sm font-sans font-bold text-gray-600 dark:truncate text-sm text-gray-600 font-semibold uppercase tracking-widest">Device Information</h2>
                                    </hgroup>
                                </div><br>

                                <div class="flex w-full flex-col items-center justify-center p-2" id="hsgq_ont_div"></div>
                                <div class="flex w-full flex-col items-center justify-center p-2" id="Device_Information_Div"></div>

                            </div>
                        </div>
                    </div>

            

                    <div class="w-full sm:w-1/2 md:w-1/3 lg:w-1/4 xl:w-47 mb-4  overflow-y-scroll bg-gray-300/50 dark:bg-gray-800/50 dark:border border-gray-800 rounded-lg shadow-lg" style="max-height: 35rem;" id="poninfo">
                        <div class="max-w-full rounded overflow-hidden">
                            <div class="px-6 py-4">
                                <div class="flex overflow-hidden gap-2 items-start" id="header_poninfo">
                                    <div class="flex-shrink-0 w-6 h-6" style="color:#5c6bc0 !important;">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-body-text" viewBox="0 0 16 16">
                                        <path fill-rule="evenodd" d="M0 .5A.5.5 0 0 1 .5 0h4a.5.5 0 0 1 0 1h-4A.5.5 0 0 1 0 .5m0 2A.5.5 0 0 1 .5 2h7a.5.5 0 0 1 0 1h-7a.5.5 0 0 1-.5-.5m9 0a.5.5 0 0 1 .5-.5h5a.5.5 0 0 1 0 1h-5a.5.5 0 0 1-.5-.5m-9 2A.5.5 0 0 1 .5 4h3a.5.5 0 0 1 0 1h-3a.5.5 0 0 1-.5-.5m5 0a.5.5 0 0 1 .5-.5h5a.5.5 0 0 1 0 1h-5a.5.5 0 0 1-.5-.5m7 0a.5.5 0 0 1 .5-.5h3a.5.5 0 0 1 0 1h-3a.5.5 0 0 1-.5-.5m-12 2A.5.5 0 0 1 .5 6h6a.5.5 0 0 1 0 1h-6a.5.5 0 0 1-.5-.5m8 0a.5.5 0 0 1 .5-.5h5a.5.5 0 0 1 0 1h-5a.5.5 0 0 1-.5-.5m-8 2A.5.5 0 0 1 .5 8h5a.5.5 0 0 1 0 1h-5a.5.5 0 0 1-.5-.5m7 0a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7a.5.5 0 0 1-.5-.5m-7 2a.5.5 0 0 1 .5-.5h8a.5.5 0 0 1 0 1h-8a.5.5 0 0 1-.5-.5m0 2a.5.5 0 0 1 .5-.5h4a.5.5 0 0 1 0 1h-4a.5.5 0 0 1-.5-.5m0 2a.5.5 0 0 1 .5-.5h2a.5.5 0 0 1 0 1h-2a.5.5 0 0 1-.5-.5"/>
                                    </svg>
                                    </div>

                                    <hgroup class="flex flex-wrap items-baseline gap-x-2 overflow-hidden">
                                        <h2 class="text-sm font-sans font-bold text-gray-600 dark:truncate text-sm text-gray-600 font-semibold uppercase tracking-widest">Onu Pon Statistic</h2>
                                    </hgroup>
                                </div><br>

                                <div class="flex flex-col items-center justify-center p-2" id="switchPorts"></div>
                                <div class="flex flex-col items-center justify-center p-2" id="Onu_Pon_Statistic_Div"></div>

                            </div>
                        </div>
                    </div>


                    <div class="w-full sm:w-1/2 md:w-1/3 lg:w-1/4 xl:w-1/4 mb-4  overflow-y-scroll bg-gray-300/50 dark:bg-gray-800/50 dark:border border-gray-800 rounded-lg shadow-lg" style="max-height: 35rem;" id="prtgOltside">
                        <div class="max-w-full rounded overflow-hidden">
                            <div class="px-6 py-4">
                                <div class="flex overflow-hidden gap-2 items-start" id="header_prtg">
                                    <div class="flex-shrink-0 w-6 h-6" style="color:#5c6bc0 !important;">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-boxes" viewBox="0 0 16 16">
                                            <path d="M7.752.066a.5.5 0 0 1 .496 0l3.75 2.143a.5.5 0 0 1 .252.434v3.995l3.498 2A.5.5 0 0 1 16 9.07v4.286a.5.5 0 0 1-.252.434l-3.75 2.143a.5.5 0 0 1-.496 0l-3.502-2-3.502 2.001a.5.5 0 0 1-.496 0l-3.75-2.143A.5.5 0 0 1 0 13.357V9.071a.5.5 0 0 1 .252-.434L3.75 6.638V2.643a.5.5 0 0 1 .252-.434zM4.25 7.504 1.508 9.071l2.742 1.567 2.742-1.567zM7.5 9.933l-2.75 1.571v3.134l2.75-1.571zm1 3.134 2.75 1.571v-3.134L8.5 9.933zm.508-3.996 2.742 1.567 2.742-1.567-2.742-1.567zm2.242-2.433V3.504L8.5 5.076V8.21zM7.5 8.21V5.076L4.75 3.504v3.134zM5.258 2.643 8 4.21l2.742-1.567L8 1.076zM15 9.933l-2.75 1.571v3.134L15 13.067zM3.75 14.638v-3.134L1 9.933v3.134z"/>
                                        </svg>
                                    </div>

                                    <hgroup class="flex flex-wrap items-baseline gap-x-2 overflow-hidden">
                                        <h2 class="text-sm font-sans font-bold text-gray-600 dark:truncate text-sm text-gray-600 font-semibold uppercase tracking-widest">PRTG</h2>
                                    </hgroup>
                                </div><br>

                                <div class="flex flex-col items-center justify-center p-2" id="Prtg_Div"></div>
                                <iframe id="resultOLTFrame" name="resultOLTFrame" frameborder="0" width="100%" height="420"></iframe>

                            </div>
                        </div>
                    </div>
 
        </div>           


        <div class="flex flex-wrap justify-center  sm:space-x-4">
            <div class="w-full sm:w-1/2 md:w-full lg:w-full xl:w-2/3 mb-4  overflow-y-scroll bg-gray-300/50 dark:bg-gray-800/50 dark:border border-gray-800 rounded-lg  shadow-lg" style="max-height: 35rem;" id="hystoryGraph">
                <div class="max-w-full rounded overflow-hidden">
                    <div class="px-6 py-4">
                        <div class="flex overflow-hidden gap-2 items-start" id="header_history">
                            <div class="flex-shrink-0 w-6 h-6" style="color:#5c6bc0 !important;">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6a7.5 7.5 0 1 0 7.5 7.5h-7.5V6Z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 10.5H21A7.5 7.5 0 0 0 13.5 3v7.5Z" />
                                </svg>

                            </div>

                            <hgroup class="flex flex-wrap items-baseline gap-x-2 overflow-hidden">
                                <h2 class="text-sm font-sans font-bold text-gray-600 dark:truncate text-sm text-gray-600 font-semibold uppercase tracking-widest">History Graph</h2>
                            </hgroup>
                        </div>

                        <div class="flex flex-col items-center justify-center p-2" id="HistoryGraph"></div>
                        <iframe id="HistoryFrame" name="HistoryFrame" frameborder="0" width="100%"  ></iframe>

                    </div>
                </div>
            </div>
        </div>

</div>

<x-bladewind.notification position="top right" />
<x-bladewind.notification />
 
<input id="Input_Olt_User"    value="" hidden />
<input id="Input_Olt_Ip"      value="" hidden />
<input id="Input_Olt_Write"   value="" hidden />
<input id="Input_Olt_Read"    value="" hidden />
<input id="Input_Olt_iFindex" value="" hidden />

<input id="uplinkAdminButton" value="" hidden />
<input id="ponAdminButton" value="" hidden />

<input id="Input_user" value="" hidden />

<input id="Input_Olt_admin" value="" hidden />
<input id="Input_Olt_neg" value="" hidden />
<input id="Input_Olt_deuplex" value="" hidden />
<input id="Input_Olt_erate" value="" hidden />
<input id="Input_Olt_flag" value="" hidden />
<input id="Input_Olt_control" value="" hidden />
<input id="Input_Olt_irate" value="" hidden />
<input id="Input_Olt_mtu" value="" hidden />
<input id="Input_Olt_pvid" value="" hidden />
<input id="Input_Olt_speed" value="" hidden />
<input id="Input_Olt_Admin_ButtonID" value="" hidden />


    <input id="Input_Epon_Onu_Reconfigure_ip" value="" hidden />
    <input id="Input_Epon_Onu_Reconfigure_read" value="" hidden />
    <input id="Input_Epon_Onu_Reconfigure_write" value="" hidden />
    <input id="Input_Epon_Onu_Reconfigure_user" value="" hidden />
    <input id="Input_Epon_Onu_Reconfigure_ponPort" value="" hidden />
    <input id="Input_Epon_Onu_Reconfigure_ifindex" value="" hidden />

 
    <x-bladewind.modal
        size="small"
        center_action_buttons="true"
        type="warning"
        title="Confirm Huawei Epon Onu Reconfigure"
        :ok_button_action="'CONFIG_FILE_FINISH()'"
        close_after_action="true"
        name="huawei-onu-reconfigure-first"
        show_close_icon="false"
        blur_backdrop="false"
        ok_button_label="Yes, Reconfig"
        cancel_button_label="Don't Reconfig">

        Are you sure you want to reconfig onu ? This action cannot be undone
    </x-bladewind.modal>

    <x-bladewind.modal
        size="big"
        center_action_buttons="true"
        type="warning"
        title="Huawei Epon Onu Reconfigure"
        :ok_button_action="'CONFIG_FILE_FINISH_FIRST()'"
        close_after_action="false"
        name="huawei-onu-reconfigure"
        show_close_icon="false"
        blur_backdrop="false"
        show_action_buttons="false">

        <br><br>
        <div class="flex flex-col items-center justify-center">
            <center>
                <p class="text-sm">მხოლოდ ROUTER მოუდზე მუშაობს</p>
                <br>
                <div id="HuaweiReconfigOnuDiv"></div>
                <br>
                <div id="HuaweiReconfigOnuDiv_result"></div>
            </center>
        </div>

    </x-bladewind.modal>

    <x-bladewind::modal
        type="info"
        title="Lan Macs"
        name="lan-macs-modal"
        show_close_icon="true"
        size="xl"
        backdrop_can_close="true"
        show_action_buttons="true"
        cancel_button_label=""
        ok_button_label=""
        blur_backdrop="false">

        <div style="max-height: 700px; overflow-y: auto;">
            <div id="LanMacsModalDiv" class="flex items-center sm:justify-center ml-4 sm:ml-0" style="width: 100%;">
               
            </div>
        </div>
 
    </x-bladewind::modal>

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
        title="Confirm Onu Restart"
        :ok_button_action="'restartBdcomOltOnu()'"
        close_after_action="false"
        name="custom-actions"
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
        :ok_button_action="'restartHuaweiOltOnu()'"
        close_after_action="false"
        name="huawei-ont-restart"
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
        type="error"
        title="Confirm Onu Uninstall"
        :ok_button_action="'OnuUninstall()'"
        close_after_action="true"
        name="onu-uninstall"
        show_close_icon="true"
        blur_backdrop="false"
        ok_button_label="Yes, Uninstall"
        cancel_button_label="Don't Uninstall">
        Are you sure you want to uninstall onu ? This action cannot be undone
    </x-bladewind.modal>

    <x-bladewind.modal
        size="medium"
        center_action_buttons="true"
        type="error"
        title="Confirm Onu Uninstall"
        :ok_button_action="'OnuHuaweiUninstall()'"
        close_after_action="true"
        name="onu-huawei-uninstall"
        show_close_icon="true"
        blur_backdrop="false"
        ok_button_label="Yes, Uninstall"
        cancel_button_label="Don't Uninstall">
        Are you sure you want to uninstall onu ? This action cannot be undone
    </x-bladewind.modal>
     
    <x-bladewind.modal
        size="medium"
        center_action_buttons="true"
        type="error"
        title="Confirm Onu Control Off"
        :ok_button_action="'HuaweiOnuControlDisable()'"
        close_after_action="true"
        name="onu-huawei-control-off"
        show_close_icon="true"
        blur_backdrop="false"
        ok_button_label="Yes, Disable"
        cancel_button_label="Don't Disable">
        Are you sure you want to disable control onu ? This action cannot be undone
    </x-bladewind.modal>

    <x-bladewind.modal
        size="medium"
        center_action_buttons="true"
        type="error"
        title="Confirm Onu Control On"
        :ok_button_action="'HuaweiOnuControlEnable()'"
        close_after_action="true"
        name="onu-huawei-control-on"
        show_close_icon="true"
        blur_backdrop="false"
        ok_button_label="Yes, Enable"
        cancel_button_label="Don't Enable">
        Are you sure you want to enable control onu ? This action cannot be undone
    </x-bladewind.modal>

    <x-bladewind.modal
        size="medium"
        center_action_buttons="true"
        type="error"
        title="Confirm Onu Control Off"
        :ok_button_action="'ZteOnuControlDisable()'"
        close_after_action="true"
        name="onu-zte-control-off"
        show_close_icon="true"
        blur_backdrop="false"
        ok_button_label="Yes, Disable"
        cancel_button_label="Don't Disable">
        Are you sure you want to disable control onu ? This action cannot be undone
    </x-bladewind.modal>

    <x-bladewind.modal
        size="medium"
        center_action_buttons="true"
        type="error"
        title="Confirm Onu Control On"
        :ok_button_action="'ZteOnuControlEnable()'"
        close_after_action="true"
        name="onu-zte-control-on"
        show_close_icon="true"
        blur_backdrop="false"
        ok_button_label="Yes, Enable"
        cancel_button_label="Don't Enable">
        Are you sure you want to enable control onu ? This action cannot be undone
    </x-bladewind.modal>

    <x-bladewind.modal
        size="medium"
        center_action_buttons="true"
        type="error"
        title="Confirm Onu Uninstall"
        :ok_button_action="'HSGQ_OnuUninstall()'"
        close_after_action="true"
        name="hsgq-onu-uninstall"
        show_close_icon="true"
        blur_backdrop="false"
        ok_button_label="Yes, Uninstall"
        cancel_button_label="Don't Uninstall">
        Are you sure you want to uninstall onu ? This action cannot be undone
    </x-bladewind.modal>

    <x-bladewind.modal
        size="medium"
        center_action_buttons="true"
        type="error"
        title="Confirm Onu Uninstall"
        :ok_button_action="'VsolOnuUninstall()'"
        close_after_action="true"
        name="vsol-onu-uninstall"
        show_close_icon="true"
        blur_backdrop="false"
        ok_button_label="Yes, Uninstall"
        cancel_button_label="Don't Uninstall">
        Are you sure you want to uninstall onu ? This action cannot be undone
    </x-bladewind.modal>


    <x-bladewind.modal
        size="medium"
        center_action_buttons="true"
        type="error"
        title="Confirm Onu Uninstall"
        :ok_button_action="'ZteOnuUninstall()'"
        close_after_action="true"
        name="zte-onu-uninstall"
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
        title="Confirm Uplink Shutdown"
        :ok_button_action="'UPLINK_ADMIN_OFF()'"
        close_after_action="true"
        name="uplink-admin-status-off"
        show_close_icon="true"
        blur_backdrop="false"
        ok_button_label="Yes, Shutdown"
        cancel_button_label="Don't Shutdown">
        Are you sure you want to shutdown uplink ? This action cannot be undone
    </x-bladewind.modal>

    <x-bladewind.modal
        size="medium"
        center_action_buttons="true"
        type="warning"
        title="Confirm Uplink Turn On"
        :ok_button_action="'UPLINK_ADMIN_ON()'"
        close_after_action="true"
        name="uplink-admin-status-on"
        show_close_icon="true"
        blur_backdrop="false"
        ok_button_label="Yes, Turn On"
        cancel_button_label="Don't Turn On">
        Are you sure you want to turn on uplink ? This action cannot be undone
    </x-bladewind.modal>

    <x-bladewind.modal
        size="medium"
        center_action_buttons="true"
        type="warning"
        title="Confirm Uplink Shutdown"
        :ok_button_action="'VSOL_UPLINK_ADMIN_OFF()'"
        close_after_action="true"
        name="vsol-uplink-admin-status-off"
        show_close_icon="true"
        blur_backdrop="false"
        ok_button_label="Yes, Shutdown"
        cancel_button_label="Don't Shutdown">
        Are you sure you want to shutdown uplink ? This action cannot be undone
    </x-bladewind.modal>

    <x-bladewind.modal
        size="medium"
        center_action_buttons="true"
        type="warning"
        title="Confirm Uplink Turn On"
        :ok_button_action="'VSOL_UPLINK_ADMIN_ON()'"
        close_after_action="true"
        name="vsol-uplink-admin-status-on"
        show_close_icon="true"
        blur_backdrop="false"
        ok_button_label="Yes, Turn On"
        cancel_button_label="Don't Turn On">
        Are you sure you want to turn on uplink ? This action cannot be undone
    </x-bladewind.modal>


    <x-bladewind.modal
        size="medium"
        center_action_buttons="true"
        type="warning"
        title="Confirm Uplink Shutdown"
        :ok_button_action="'HUAWEI_UPLINK_ADMIN_OFF()'"
        close_after_action="true"
        name="huawei-uplink-admin-status-off"
        show_close_icon="true"
        blur_backdrop="false"
        ok_button_label="Yes, Shutdown"
        cancel_button_label="Don't Shutdown">
        Are you sure you want to shutdown uplink ? This action cannot be undone
    </x-bladewind.modal>

    <x-bladewind.modal
        size="medium"
        center_action_buttons="true"
        type="warning"
        title="Confirm Uplink Turn On"
        :ok_button_action="'HUAWEI_UPLINK_ADMIN_ON()'"
        close_after_action="true"
        name="huawei-uplink-admin-status-on"
        show_close_icon="true"
        blur_backdrop="false"
        ok_button_label="Yes, Turn On"
        cancel_button_label="Don't Turn On">
        Are you sure you want to turn on uplink ? This action cannot be undone
    </x-bladewind.modal>

    <x-bladewind.modal
        size="medium"
        center_action_buttons="true"
        type="warning"
        title="Confirm Uplink Shutdown"
        :ok_button_action="'HSGQ_UPLINK_ADMIN_OFF()'"
        close_after_action="true"
        name="hsgq-uplink-admin-status-off"
        show_close_icon="true"
        blur_backdrop="false"
        ok_button_label="Yes, Shutdown"
        cancel_button_label="Don't Shutdown">
        Are you sure you want to shutdown uplink ? This action cannot be undone
    </x-bladewind.modal>

    <x-bladewind.modal
        size="medium"
        center_action_buttons="true"
        type="warning"
        title="Confirm Uplink Turn On"
        :ok_button_action="'HSGQ_UPLINK_ADMIN_ON()'"
        close_after_action="true"
        name="hsgq-uplink-admin-status-on"
        show_close_icon="true"
        blur_backdrop="false"
        ok_button_label="Yes, Turn On"
        cancel_button_label="Don't Turn On">
        Are you sure you want to turn on uplink ? This action cannot be undone
    </x-bladewind.modal>

    <x-bladewind.modal
        size="medium"
        center_action_buttons="true"
        type="warning"
        title="Confirm Pon Shutdown"
        :ok_button_action="'HSGQ_PON_ADMIN_OFF()'"
        close_after_action="true"
        name="hsgq-pon-admin-status-off"
        show_close_icon="true"
        blur_backdrop="false"
        ok_button_label="Yes, Shutdown"
        cancel_button_label="Don't Shutdown">
        Are you sure you want to shutdown pon ? This action cannot be undone
    </x-bladewind.modal>

    <x-bladewind.modal
        size="medium"
        center_action_buttons="true"
        type="warning"
        title="Confirm Pon Turn On"
        :ok_button_action="'HSGQ_PON_ADMIN_ON()'"
        close_after_action="true"
        name="hsgq-pon-admin-status-on"
        show_close_icon="true"
        blur_backdrop="false"
        ok_button_label="Yes, Turn On"
        cancel_button_label="Don't Turn On">
        Are you sure you want to turn on pon ? This action cannot be undone
    </x-bladewind.modal>


    <x-bladewind.modal
        size="medium"
        center_action_buttons="true"
        type="warning"
        title="Confirm Pon Shutdown"
        :ok_button_action="'HUAWEI_PON_ADMIN_OFF()'"
        close_after_action="true"
        name="huawei-pon-admin-status-off"
        show_close_icon="true"
        blur_backdrop="false"
        ok_button_label="Yes, Shutdown"
        cancel_button_label="Don't Shutdown">
        Are you sure you want to shutdown pon ? This action cannot be undone
    </x-bladewind.modal>

    <x-bladewind.modal
        size="medium"
        center_action_buttons="true"
        type="warning"
        title="Confirm Pon Turn On"
        :ok_button_action="'HUAWEI_PON_ADMIN_ON()'"
        close_after_action="true"
        name="huawei-pon-admin-status-on"
        show_close_icon="true"
        blur_backdrop="false"
        ok_button_label="Yes, Turn On"
        cancel_button_label="Don't Turn On">
        Are you sure you want to turn on pon ? This action cannot be undone
    </x-bladewind.modal>

    <x-bladewind.modal
        size="medium"
        center_action_buttons="true"
        type="warning"
        title="Confirm Uplink Shutdown"
        :ok_button_action="'ZTE_UPLINK_ADMIN_OFF()'"
        close_after_action="true"
        name="zte-uplink-admin-status-off"
        show_close_icon="true"
        blur_backdrop="false"
        ok_button_label="Yes, Shutdown"
        cancel_button_label="Don't Shutdown">
        Are you sure you want to shutdown uplink ? This action cannot be undone
    </x-bladewind.modal>

    <x-bladewind.modal
        size="medium"
        center_action_buttons="true"
        type="warning"
        title="Confirm Uplink Turn On"
        :ok_button_action="'ZTE_UPLINK_ADMIN_ON()'"
        close_after_action="true"
        name="zte-uplink-admin-status-on"
        show_close_icon="true"
        blur_backdrop="false"
        ok_button_label="Yes, Turn On"
        cancel_button_label="Don't Turn On">
        Are you sure you want to turn on uplink ? This action cannot be undone
    </x-bladewind.modal>

    <x-bladewind.modal
        size="medium"
        center_action_buttons="true"
        type="warning"
        title="Confirm Pon Shutdown"
        :ok_button_action="'PON_ADMIN_OFF()'"
        close_after_action="true"
        name="pon-admin-status-off"
        show_close_icon="true"
        blur_backdrop="false"
        ok_button_label="Yes, Shutdown"
        cancel_button_label="Don't Shutdown">
        Are you sure you want to shutdown pon ? This action cannot be undone
    </x-bladewind.modal>

    <x-bladewind.modal
        size="medium"
        center_action_buttons="true"
        type="warning"
        title="Confirm Pon Turn On"
        :ok_button_action="'PON_ADMIN_ON()'"
        close_after_action="true"
        name="pon-admin-status-on"
        show_close_icon="true"
        blur_backdrop="false"
        ok_button_label="Yes, Turn On"
        cancel_button_label="Don't Turn On">
        Are you sure you want to turn on pon ? This action cannot be undone
    </x-bladewind.modal>


    <x-bladewind.modal
        size="medium"
        center_action_buttons="true"
        type="warning"
        title="Confirm Pon Shutdown"
        :ok_button_action="'VSOL_PON_ADMIN_OFF()'"
        close_after_action="true"
        name="vsol-pon-admin-status-off"
        show_close_icon="true"
        blur_backdrop="false"
        ok_button_label="Yes, Shutdown"
        cancel_button_label="Don't Shutdown">
        Are you sure you want to shutdown pon ? This action cannot be undone
    </x-bladewind.modal>

    <x-bladewind.modal
        size="medium"
        center_action_buttons="true"
        type="warning"
        title="Confirm Pon Shutdown"
        :ok_button_action="'ZTE_PON_ADMIN_OFF()'"
        close_after_action="true"
        name="zte-pon-admin-status-off"
        show_close_icon="true"
        blur_backdrop="false"
        ok_button_label="Yes, Shutdown"
        cancel_button_label="Don't Shutdown">
        Are you sure you want to shutdown pon ? This action cannot be undone
    </x-bladewind.modal>

    <x-bladewind.modal
        size="medium"
        center_action_buttons="true"
        type="warning"
        title="Confirm Pon Shutdown"
        :ok_button_action="'ZTE_PON_ADMIN_ON()'"
        close_after_action="true"
        name="zte-pon-admin-status-on"
        show_close_icon="true"
        blur_backdrop="false"
        ok_button_label="Yes, Turn On"
        cancel_button_label="Don't Turn On">
        Are you sure you want to turn on pon ? This action cannot be undone
    </x-bladewind.modal>

    <x-bladewind.modal
        size="medium"
        center_action_buttons="true"
        type="warning"
        title="Confirm Pon Turn On"
        :ok_button_action="'VSOL_PON_ADMIN_ON()'"
        close_after_action="true"
        name="vsol-pon-admin-status-on"
        show_close_icon="true"
        blur_backdrop="false"
        ok_button_label="Yes, Turn On"
        cancel_button_label="Don't Turn On">
        Are you sure you want to turn on pon ? This action cannot be undone
    </x-bladewind.modal>

    
    <x-bladewind::modal
        size="medium"
        center_action_buttons="true"
        type="warning"
        title="PORT DESCRIPTION EDIT"
        :ok_button_action="'Onu_Zyxel_Description()'"
        close_after_action="true"
        name="Ont-Zyxel-Description-Edit"
        show_close_icon="true"
        blur_backdrop="false"
        ok_button_label="Yes, Save"
        cancel_button_label="Don't Save">
        Are you sure you want to edit port description ? This action cannot be undone
        <div class="flex sm:w-2/2 w-full items-center justify-center">
            <br>
            <div class="relative w-full dv-ZYXEL_DESCR_NEW_ONT  mb-3 ">
                <input class="bw-input peer  ZYXEL_DESCR_NEW_ONT  dark:bg-gray-900 mt-10 dark:text-gray-400 text-xs overflow-hidden shadow-sm sm:rounded-lg w-full h-8  border border-slate-700" id="ZYXEL_DESCR_NEW_ONT_ID" prefix-class="text-red-500" type="text" name="ZYXEL_DESCR_NEW_ONT" value="" autocomplete="off" placeholder="ONT DESCRIPTION" style="padding-left: 68px;">
                <div id="room" class="ZYXEL_DESCR_NEW_ONT-prefix mt-10 prefix text-sm select-none pl-3.5 pr-2 z-20  text-indigo-400 dark:text-indigo-400 absolute left-0 inset-y-0 inline-flex items-center " data-transparency="1">
                     
                </div>
                <script>positionPrefix('ZYXEL_DESCR_NEW_ONT', 'blur');</script>
            </div>
            <input type="hidden" class="bw-raw-select">
 
        </div> 
    </x-bladewind::modal>

    <x-bladewind::modal
        size="medium"
        center_action_buttons="true"
        type="warning"
        title="PORT DESCRIPTION EDIT"
        :ok_button_action="'Onu_Cisco_Description()'"
        close_after_action="true"
        name="Ont-Cisco-Description-Edit"
        show_close_icon="true"
        blur_backdrop="false"
        ok_button_label="Yes, Save"
        cancel_button_label="Don't Save">
        Are you sure you want to edit port description ? This action cannot be undone
        <div class="flex sm:w-2/2 w-full items-center justify-center">
            <br>
   

            <div class="relative w-full dv-site2  mb-3 ">
            <input class="bw-input peer  site2  dark:bg-gray-900 mt-10 dark:text-gray-400 text-xs overflow-hidden shadow-sm sm:rounded-lg w-full h-8  border border-slate-700" id="CISCO_DESCR_NEW_ONT_ID" type="text" name="site2" value="" autocomplete="off" placeholder="PORT DESCRIPTION">
                <div id="cisco_room"  class="site2-suffix dark:bg-gray-800 suffix mt-10 text-sm select-none pl-3.5 !pr-3 rtl:!right-[unset] rtl:!left-0 z-20 text-indigo-400  sm:rounded-r-lg dark:text-indigo-400 absolute right-0 inset-y-0 inline-flex items-center " data-transparency="1">
                            
                </div>
                <script>positionSuffix('site2');</script>
            </div>

        </div> 
    </x-bladewind::modal>

    <x-bladewind.modal
        size="xl"
        center_action_buttons="true"
        type="warning"
        title="Pon Coordinates"
        close_after_action="false"
        name="pon-coordinate-modal"
        show_close_icon="true"
        blur_backdrop="false"
        backdrop_can_close="false"
        ok_button_label=""
        cancel_button_label="">
  
        <div class="flex items-center justify-center" style="max-height: 500px;  overflow-y: auto;display:block;" id="Pon_Full_Coordinates_Body"></div>

    </x-bladewind.modal>

    <x-bladewind::modal
        size="medium"
        center_action_buttons="true"
        type="warning"
        title="ONT DESCRIPTION EDIT"
        :ok_button_action="'OnuDescription()'"
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

    <x-bladewind::modal
        size="medium"
        center_action_buttons="true"
        type="warning"
        title="ONT DESCRIPTION EDIT"
        :ok_button_action="'OnuHuaweiDescription()'"
        close_after_action="true"
        name="Ont-Huawei-Description-Edit"
        show_close_icon="true"
        blur_backdrop="false"
        ok_button_label="Yes, Save"
        cancel_button_label="Don't Save">
        Are you sure you want to edit onu description ? This action cannot be undone
        <div class="flex sm:w-2/2 w-full items-center justify-center">
                <x-bladewind::input required="false" name="HUAWEI_DESCR_NEW_ONT" id="HUAWEI_DESCR_NEW_ONT_ID" placeholder="ONT DESCRIPTION"
                        class="dark:bg-gray-900 mt-10 dark:text-gray-400 text-xs overflow-hidden shadow-sm sm:rounded-lg w-full h-8  border border-slate-700" />
        </div> 
    </x-bladewind::modal>

    <x-bladewind::modal
        size="medium"
        center_action_buttons="true"
        type="warning"
        title="ONT DESCRIPTION EDIT"
        :ok_button_action="'HSGQ_OnuDescription()'"
        close_after_action="true"
        name="HSGQ_Ont-Description-Edit"
        show_close_icon="true"
        blur_backdrop="false"
        ok_button_label="Yes, Save"
        cancel_button_label="Don't Save">
        Are you sure you want to edit onu description ? This action cannot be undone
        <div class="flex sm:w-2/2 w-full items-center justify-center">
                <x-bladewind::input required="false" name="HSGQ_DESCR_NEW_ONT" id="HSGQ_DESCR_NEW_ONT_ID" placeholder="ONT DESCRIPTION"
                        class="dark:bg-gray-900 mt-10 dark:text-gray-400 text-xs overflow-hidden shadow-sm sm:rounded-lg w-full h-8  border border-slate-700" />
        </div> 
    </x-bladewind::modal>

    <x-bladewind::modal
        size="medium"
        center_action_buttons="true"
        type="warning"
        title="ONT DESCRIPTION EDIT"
        :ok_button_action="'Onu_Vsolution_Description()'"
        close_after_action="true"
        name="Ont-Vsol-Description-Edit"
        show_close_icon="true"
        blur_backdrop="false"
        ok_button_label="Yes, Save"
        cancel_button_label="Don't Save">
        Are you sure you want to edit onu description ? This action cannot be undone
        <div class="flex sm:w-2/2 w-full items-center justify-center">
                <x-bladewind::input required="false" name="VSOL_DESCR_NEW_ONT" id="VSOL_DESCR_NEW_ONT_ID" placeholder="ONT DESCRIPTION"
                        class="dark:bg-gray-900 mt-10 dark:text-gray-400 text-xs overflow-hidden shadow-sm sm:rounded-lg w-full h-8  border border-slate-700" />
        </div> 
    </x-bladewind::modal>

    <x-bladewind::modal
        size="medium"
        center_action_buttons="true"
        type="warning"
        title="ONT DESCRIPTION EDIT"
        :ok_button_action="'Onu_Zte_Description()'"
        close_after_action="true"
        name="Ont-Zte-Description-Edit"
        show_close_icon="true"
        blur_backdrop="false"
        ok_button_label="Yes, Save"
        cancel_button_label="Don't Save">
        Are you sure you want to edit onu description ? This action cannot be undone
        <div class="flex sm:w-2/2 w-full items-center justify-center">
                <x-bladewind::input required="false" name="ZTE_DESCR_NEW_ONT" id="ZTE_DESCR_NEW_ONT_ID" placeholder="ONT DESCRIPTION"
                        class="dark:bg-gray-900 mt-10 dark:text-gray-400 text-xs overflow-hidden shadow-sm sm:rounded-lg w-full h-8  border border-slate-700" />
        </div> 
    </x-bladewind::modal>

    <x-bladewind.modal
        size="medium"
        center_action_buttons="true"
        type="warning"
        title="Confirm Onu Restart"
        :ok_button_action="'restart_OLTSIDE_Onu_zte()'"
        close_after_action="false"
        name="zte-onu-restart"
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
        :ok_button_action="'restart_OLT_Onu_hsgq()'"
        close_after_action="false"
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
        :ok_button_action="'restart_OLTSIDE_Onu_vsolution()'"
        close_after_action="false"
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
        size="medium"
        center_action_buttons="true"
        type="warning"
        title="PON DESCRIPTION EDIT"
        :ok_button_action="'PonDescription()'"
        close_after_action="true"
        name="Pon-Description-Edit"
        show_close_icon="true"
        blur_backdrop="false"
        ok_button_label="Yes, Save"
        cancel_button_label="Don't Save">
        Are you sure you want to edit pon description ? This action cannot be undone

        <div class="flex sm:w-2/2 w-full items-center justify-center">
                <x-bladewind::input required="false" name="DESCR_NEW_PON" id="DESCR_NEW_PON_ID" placeholder="PON DESCRIPTION"
                        class="dark:bg-gray-900 mt-10 dark:text-gray-400 text-xs overflow-hidden shadow-sm sm:rounded-lg w-full h-8  border border-slate-700" />
        </div> 
    </x-bladewind::modal>

    <x-bladewind::modal
        size="medium"
        center_action_buttons="true"
        type="warning"
        title="PON DESCRIPTION EDIT"
        :ok_button_action="'HuaweiPonDescription()'"
        close_after_action="true"
        name="Huawei-Pon-Description-Edit"
        show_close_icon="true"
        blur_backdrop="false"
        ok_button_label="Yes, Save"
        cancel_button_label="Don't Save">
        Are you sure you want to edit pon description ? This action cannot be undone

        <div class="flex sm:w-2/2 w-full items-center justify-center">
                <x-bladewind::input required="false" name="HUAWEI_DESCR_NEW_PON" id="HUAWEI_DESCR_NEW_PON_ID" placeholder="PON DESCRIPTION"
                        class="dark:bg-gray-900 mt-10 dark:text-gray-400 text-xs overflow-hidden shadow-sm sm:rounded-lg w-full h-8  border border-slate-700" />
        </div> 
    </x-bladewind::modal>

    <x-bladewind::modal
        size="medium"
        center_action_buttons="true"
        type="warning"
        title="PON DESCRIPTION EDIT"
        :ok_button_action="'ZtePonDescription()'"
        close_after_action="true"
        name="Zte-Pon-Description-Edit"
        show_close_icon="true"
        blur_backdrop="false"
        ok_button_label="Yes, Save"
        cancel_button_label="Don't Save">
        Are you sure you want to edit pon description ? This action cannot be undone

        <div class="flex sm:w-2/2 w-full items-center justify-center">
                <x-bladewind::input required="false" name="ZTE_DESCR_NEW_PON" id="ZTE_DESCR_NEW_PON_ID" placeholder="PON DESCRIPTION"
                        class="dark:bg-gray-900 mt-10 dark:text-gray-400 text-xs overflow-hidden shadow-sm sm:rounded-lg w-full h-8  border border-slate-700" />
        </div> 
    </x-bladewind::modal>

    <x-bladewind::modal
        size="medium"
        center_action_buttons="true"
        type="warning"
        title="PON DESCRIPTION EDIT"
        :ok_button_action="'HSGQ_PonDescription()'"
        close_after_action="true"
        name="Hsgq-Pon-Description-Edit"
        show_close_icon="true"
        blur_backdrop="false"
        ok_button_label="Yes, Save"
        cancel_button_label="Don't Save">
        Are you sure you want to edit pon description ? This action cannot be undone

        <div class="flex sm:w-2/2 w-full items-center justify-center">
                <x-bladewind::input required="false" name="HSGQ_DESCR_NEW_PON" id="HSGQ_DESCR_NEW_PON_ID" placeholder="PON DESCRIPTION"
                        class="dark:bg-gray-900 mt-10 dark:text-gray-400 text-xs overflow-hidden shadow-sm sm:rounded-lg w-full h-8  border border-slate-700" />
        </div> 
    </x-bladewind::modal>

    <x-bladewind::modal
        size="medium"
        center_action_buttons="true"
        type="warning"
        title="PON DESCRIPTION EDIT"
        :ok_button_action="'VsolPonDescription()'"
        close_after_action="true"
        name="Vsol-Pon-Description-Edit"
        show_close_icon="true"
        blur_backdrop="false"
        ok_button_label="Yes, Save"
        cancel_button_label="Don't Save">
        Are you sure you want to edit pon description ? This action cannot be undone

        <div class="flex sm:w-2/2 w-full items-center justify-center">
                <x-bladewind::input required="false" name="VSOL_DESCR_NEW_PON" id="VSOL_DESCR_NEW_PON_ID" placeholder="PON DESCRIPTION"
                        class="dark:bg-gray-900 mt-10 dark:text-gray-400 text-xs overflow-hidden shadow-sm sm:rounded-lg w-full h-8  border border-slate-700" />
        </div> 
    </x-bladewind::modal>

    <x-bladewind::modal
        size="medium"
        center_action_buttons="true"
        type="warning"
        title="UPLINK DESCRIPTION EDIT"
        :ok_button_action="'UplinkDescription()'"
        close_after_action="true"
        name="Uplink-Description-Edit"
        show_close_icon="true"
        blur_backdrop="false"
        ok_button_label="Yes, Save"
        cancel_button_label="Don't Save">
        Are you sure you want to edit uplink description ? This action cannot be undone

        <div class="flex sm:w-2/2 w-full items-center justify-center">
                <x-bladewind::input required="false" name="DESCR_NEW_UPLINK" id="DESCR_NEW_UPLINK_ID" placeholder="UPLINK DESCRIPTION"
                        class="dark:bg-gray-900 mt-10 dark:text-gray-400 text-xs overflow-hidden shadow-sm sm:rounded-lg w-full h-8  border border-slate-700" />
        </div> 
    </x-bladewind::modal>

    
    <x-bladewind::modal
        size="medium"
        center_action_buttons="true"
        type="warning"
        title="UPLINK DESCRIPTION EDIT"
        :ok_button_action="'Huawei_UplinkDescription()'"
        close_after_action="true"
        name="Huawei-Uplink-Description-Edit"
        show_close_icon="true"
        blur_backdrop="false"
        ok_button_label="Yes, Save"
        cancel_button_label="Don't Save">
        Are you sure you want to edit uplink description ? This action cannot be undone

        <div class="flex sm:w-2/2 w-full items-center justify-center">
                <x-bladewind::input required="false" name="HUAWEI_DESCR_NEW_UPLINK" id="HUAWEI_DESCR_NEW_UPLINK_ID" placeholder="UPLINK DESCRIPTION"
                        class="dark:bg-gray-900 mt-10 dark:text-gray-400 text-xs overflow-hidden shadow-sm sm:rounded-lg w-full h-8  border border-slate-700" />
        </div> 
    </x-bladewind::modal>

    <x-bladewind::modal
        size="medium"
        center_action_buttons="true"
        type="warning"
        title="UPLINK DESCRIPTION EDIT"
        :ok_button_action="'Vsol_UplinkDescription()'"
        close_after_action="true"
        name="Vsol-Uplink-Description-Edit"
        show_close_icon="true"
        blur_backdrop="false"
        ok_button_label="Yes, Save"
        cancel_button_label="Don't Save">
        Are you sure you want to edit uplink description ? This action cannot be undone

        <div class="flex sm:w-2/2 w-full items-center justify-center">
                <x-bladewind::input required="false" name="VSOL_DESCR_NEW_UPLINK" id="VSOL_DESCR_NEW_UPLINK_ID" placeholder="UPLINK DESCRIPTION"
                        class="dark:bg-gray-900 mt-10 dark:text-gray-400 text-xs overflow-hidden shadow-sm sm:rounded-lg w-full h-8  border border-slate-700" />
        </div> 
    </x-bladewind::modal>

    <x-bladewind::modal
        size="medium"
        center_action_buttons="true"
        type="warning"
        title="UPLINK DESCRIPTION EDIT"
        :ok_button_action="'Hsgq_UplinkDescription()'"
        close_after_action="true"
        name="Hsgq-Uplink-Description-Edit"
        show_close_icon="true"
        blur_backdrop="false"
        ok_button_label="Yes, Save"
        cancel_button_label="Don't Save">
        Are you sure you want to edit uplink description ? This action cannot be undone

        <div class="flex sm:w-2/2 w-full items-center justify-center">
                <x-bladewind::input required="false" name="HSGQ_DESCR_NEW_UPLINK" id="HSGQ_DESCR_NEW_UPLINK_ID" placeholder="UPLINK DESCRIPTION"
                        class="dark:bg-gray-900 mt-10 dark:text-gray-400 text-xs overflow-hidden shadow-sm sm:rounded-lg w-full h-8  border border-slate-700" />
        </div> 
    </x-bladewind::modal>
 

    <x-bladewind::modal
        size="medium"
        center_action_buttons="true"
        type="warning"
        title="UPLINK DESCRIPTION EDIT"
        :ok_button_action="'Zte_UplinkDescription()'"
        close_after_action="true"
        name="Zte-Uplink-Description-Edit"
        show_close_icon="true"
        blur_backdrop="false"
        ok_button_label="Yes, Save"
        cancel_button_label="Don't Save">
        Are you sure you want to edit uplink description ? This action cannot be undone

        <div class="flex sm:w-2/2 w-full items-center justify-center">
                <x-bladewind::input required="false" name="ZTE_DESCR_NEW_UPLINK" id="ZTE_DESCR_NEW_UPLINK_ID" placeholder="UPLINK DESCRIPTION"
                        class="dark:bg-gray-900 mt-10 dark:text-gray-400 text-xs overflow-hidden shadow-sm sm:rounded-lg w-full h-8  border border-slate-700" />
        </div> 
    </x-bladewind::modal>



    <x-bladewind::modal
        type="info"
        title="Pon Parameters"
        name="Pon-Parameters"
        show_close_icon="true"
        size="omg"
        backdrop_can_close="true"
        show_action_buttons="true"
        cancel_button_label=""
        ok_button_label=""
        blur_backdrop="false">

        <div class="flex items-center justify-center" style="max-height: 500px; overflow-y: auto;" id="Pon_Parameters_Refresh"></div> <br>
        <div class="flex items-center justify-center" style="max-height: 700px; overflow-y: auto;display:block;" id="Pon_Parameters_Modal_Body"></div>
 
    </x-bladewind::modal>

    <x-bladewind::modal
        type="info"
        title="Uplink Parameters"
        name="Uplink-Parameters"
        show_close_icon="true"
        size="omg"
        backdrop_can_close="true"
        show_action_buttons="true"
        cancel_button_label=""
        ok_button_label=""
        blur_backdrop="false">

        <div class="flex items-center justify-center" style="max-height: 500px; overflow-y: auto;" id="Uplink_Parameters_Refresh"></div> <br>
        <div class="flex items-center justify-center" style="max-height: 700px; overflow-y: auto;display:block;" id="Uplink_Parameters_Modal_Body"></div>
 
    </x-bladewind::modal>


    <x-bladewind.modal
        size="omg"                
        name="pon-ont-History"
        show_close_icon="true"
        blur_backdrop="false"
         cancel_button_label=""
        ok_button_label=""
        show_action_buttons="true">

        <div style="max-height: 700px; overflow-y: auto;display:block;">
            <div id="pon-ont-graph" class="flex items-center justify-center"></div><br>
            <div id="pon-ont-data" class="flex items-center justify-center"></div>
        
        </div>

    </x-bladewind.modal>

    <x-bladewind.modal
        size="xl"                
        name="all-ont-History"
        show_close_icon="true"
        blur_backdrop="false"
        show_action_buttons="false">
       
  
            <x-bladewind.datepicker
                name="history_date_picker"
                default_date=""
                type="range"
                format="yyyy-mm-dd"
                label="Time"
                placeholder="Select a date"
                required="false"
                with_time="false"
                hours_as="12"
                time_format="hh:mm"
                show_seconds="false"
                />

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
            <div id="all-ont-data" class="flex items-center justify-center"  ></div>

       </div>
 
 
    </x-bladewind.modal>

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
        <div class="items-center justify-center"      style="max-height: 700px; overflow-y: auto;display:block;" id="Pon_Modal_Body"></div>
  
    </x-bladewind::modal>
 
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
        title="SSH CONNECTION..."
        name="SSG-CONNECT"
        show_close_icon="true"
        size="omg"
        backdrop_can_close="true"
        show_action_buttons="true"
        cancel_button_label=""
        ok_button_label=""
        blur_backdrop="false">

        <div class="flex items-center justify-center" style="max-height: 500px; overflow-y: auto;" id="ssh_button_id"></div> <br>
        <div class="flex items-center justify-center" style="max-height: 700px; overflow-y: auto;display:block;" id="ssh_modal_body"></div>
 
    </x-bladewind::modal>
 
<script>     
 
    $(function() {
        $("#hystoryGraph").draggable({handle: "#header_history"}).resizable({handles: 'e, w'});
        $("#prtgOltside").draggable({handle: "#header_prtg"}).resizable({handles: 'e, w'});
        $("#dinfo").draggable({handle: "#header_dinfo"}).resizable({handles: 'e, w'});
        $("#poninfo").draggable({handle: "#header_poninfo"}).resizable({handles: 'e, w'});
    });

    function olt_search()
    {
        var inputValue = document.getElementById('searchByOlt').value;
        if (inputValue === '')
        {
            var alertDiv = document.getElementById('errorAlert');
            alertDiv.hidden = false;
            setTimeout(function() {
                alertDiv.hidden = true;
            }, 2000);
        }
        else
        {
            localStorage.setItem('olt_search_data', inputValue);
            location.reload();
        }

    }
 
    
    window.onload = function()
    {
        let savedData = localStorage.getItem('olt_search_data');
        if (savedData)
        {
            document.getElementById('searchByOlt').value = savedData.trim();
            localStorage.removeItem('olt_search_data');
   

            if(checkType(savedData.trim()) == 'ipv4')
            {
                olt_get_all_stat(savedData);
            }
            else if(checkType(savedData.trim()) == 'string')
            {
                document.getElementById("SearchByName").innerHTML = `
                    <svg  width="30" height="30" fill="#5c6bc0" class="mr-2 animate-spin" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg">
                        <path d="M526 1394q0 53-37.5 90.5t-90.5 37.5q-52 0-90-38t-38-90q0-53 37.5-90.5t90.5-37.5 90.5 37.5 37.5 90.5zm498 206q0 53-37.5 90.5t-90.5 37.5-90.5-37.5-37.5-90.5 37.5-90.5 90.5-37.5 90.5 37.5 37.5 90.5zm-704-704q0 53-37.5 90.5t-90.5 37.5-90.5-37.5-37.5-90.5 37.5-90.5 90.5-37.5 90.5 37.5 37.5 90.5zm1202 498q0 52-38 90t-90 38q-53 0-90.5-37.5t-37.5-90.5 37.5-90.5 90.5-37.5 90.5 37.5 37.5 90.5zm-964-996q0 66-47 113t-113 47-113-47-47-113 47-113 113-47 113 47 47 113zm1170 498q0 53-37.5 90.5t-90.5 37.5-90.5-37.5-37.5-90.5 37.5-90.5 90.5-37.5 90.5 37.5 37.5 90.5zm-640-704q0 80-56 136t-136 56-136-56-56-136 56-136 136-56 136 56 56 136zm530 206q0 93-66 158.5t-158 65.5q-93 0-158.5-65.5t-65.5-158.5q0-92 65.5-158t158.5-66q92 0 158 66t66 158z">
                        </path>
                    </svg>
                `;
            

                $.ajax({
                            url: "/OLT-NAME-SEARCH",
                            type: "GET",
                            data:{'name':savedData},
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            dataType: "json",
                            success: function(result)
                            {
                                if(result.error)
                                {
                                    document.getElementById("SearchByName").innerHTML = ' <p class="text-red-400">'+result.error+'</p>';
                                }
                                else
                                {
                                    let SelectArray;
                                    for (const key in result) 
                                    {
                                        SelectArray += `<option value="${result[key].ip}" class="dark:bg-gray-900 text-center dark:text-gray-400 text-xs overflow-hidden rounded-lg shadow-sm sm:rounded-lg w-full h-15  border border-slate-700 uppercase tracking-widest">${result[key].name}</option>`;
                                    }
 
                                    document.getElementById("SearchByName").innerHTML = `
                                    <select name="type" id="SearchByNameOlT" onchange="SwitcherFromNameToIp()" class="w-full sm:w-1/5 dark:bg-gray-800 text-center dark:text-gray-400 text-xs overflow-hidden  sm:rounded-lg   h-8  border border-slate-700 uppercase tracking-widest" required>
                                        <option selected value="" disabled class="dark:bg-gray-900 text-center dark:text-gray-400 text-xs overflow-hidden shadow-sm sm:rounded-lg w-full h-8  border border-slate-700 uppercase tracking-widest">Choose Device</option>
                                        ${SelectArray}
                                    </select>
                                    
                                    `;
                                }
                            },
                            error: function (xhr, status, error) 
                            {
                                document.getElementById("SearchByName").innerHTML = ' <p class="text-red-400">'+error+'</p>';
                            }
                    });
            }
        }
        else 
        {   
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.has('id'))
            {
                let ipFromUrl = urlParams.get('id'); 
                if (ipFromUrl)
                {               
                    document.getElementById('searchByOlt').value = ipFromUrl;
                    olt_search(ipFromUrl);
                }
            }
        }
    };


    function SwitcherFromNameToIp()
    {
        var selectElement   = document.getElementById('SearchByNameOlT');
        var selectedOption  = selectElement.options[selectElement.selectedIndex];              
        var dataValue       = selectedOption.value;

        if(dataValue)olt_get_all_stat(dataValue);
    }

    function olt_get_all_stat(inputValue)
    {

        document.getElementById("Device_Information_Div").innerHTML = `
            <svg  width="30" height="30" fill="#5c6bc0" class="mr-2 animate-spin" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg">
                <path d="M526 1394q0 53-37.5 90.5t-90.5 37.5q-52 0-90-38t-38-90q0-53 37.5-90.5t90.5-37.5 90.5 37.5 37.5 90.5zm498 206q0 53-37.5 90.5t-90.5 37.5-90.5-37.5-37.5-90.5 37.5-90.5 90.5-37.5 90.5 37.5 37.5 90.5zm-704-704q0 53-37.5 90.5t-90.5 37.5-90.5-37.5-37.5-90.5 37.5-90.5 90.5-37.5 90.5 37.5 37.5 90.5zm1202 498q0 52-38 90t-90 38q-53 0-90.5-37.5t-37.5-90.5 37.5-90.5 90.5-37.5 90.5 37.5 37.5 90.5zm-964-996q0 66-47 113t-113 47-113-47-47-113 47-113 113-47 113 47 47 113zm1170 498q0 53-37.5 90.5t-90.5 37.5-90.5-37.5-37.5-90.5 37.5-90.5 90.5-37.5 90.5 37.5 37.5 90.5zm-640-704q0 80-56 136t-136 56-136-56-56-136 56-136 136-56 136 56 56 136zm530 206q0 93-66 158.5t-158 65.5q-93 0-158.5-65.5t-65.5-158.5q0-92 65.5-158t158.5-66q92 0 158 66t66 158z">
                </path>
            </svg>
            `;
        document.getElementById("Onu_Pon_Statistic_Div").innerHTML = `
            <svg  width="30" height="30" fill="#5c6bc0" class="mr-2 animate-spin" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg">
                <path d="M526 1394q0 53-37.5 90.5t-90.5 37.5q-52 0-90-38t-38-90q0-53 37.5-90.5t90.5-37.5 90.5 37.5 37.5 90.5zm498 206q0 53-37.5 90.5t-90.5 37.5-90.5-37.5-37.5-90.5 37.5-90.5 90.5-37.5 90.5 37.5 37.5 90.5zm-704-704q0 53-37.5 90.5t-90.5 37.5-90.5-37.5-37.5-90.5 37.5-90.5 90.5-37.5 90.5 37.5 37.5 90.5zm1202 498q0 52-38 90t-90 38q-53 0-90.5-37.5t-37.5-90.5 37.5-90.5 90.5-37.5 90.5 37.5 37.5 90.5zm-964-996q0 66-47 113t-113 47-113-47-47-113 47-113 113-47 113 47 47 113zm1170 498q0 53-37.5 90.5t-90.5 37.5-90.5-37.5-37.5-90.5 37.5-90.5 90.5-37.5 90.5 37.5 37.5 90.5zm-640-704q0 80-56 136t-136 56-136-56-56-136 56-136 136-56 136 56 56 136zm530 206q0 93-66 158.5t-158 65.5q-93 0-158.5-65.5t-65.5-158.5q0-92 65.5-158t158.5-66q92 0 158 66t66 158z">
                </path>
            </svg>
            `;
        document.getElementById("Prtg_Div").innerHTML = `
            <svg  width="30" height="30" fill="#5c6bc0" class="mr-2 animate-spin" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg">
                <path d="M526 1394q0 53-37.5 90.5t-90.5 37.5q-52 0-90-38t-38-90q0-53 37.5-90.5t90.5-37.5 90.5 37.5 37.5 90.5zm498 206q0 53-37.5 90.5t-90.5 37.5-90.5-37.5-37.5-90.5 37.5-90.5 90.5-37.5 90.5 37.5 37.5 90.5zm-704-704q0 53-37.5 90.5t-90.5 37.5-90.5-37.5-37.5-90.5 37.5-90.5 90.5-37.5 90.5 37.5 37.5 90.5zm1202 498q0 52-38 90t-90 38q-53 0-90.5-37.5t-37.5-90.5 37.5-90.5 90.5-37.5 90.5 37.5 37.5 90.5zm-964-996q0 66-47 113t-113 47-113-47-47-113 47-113 113-47 113 47 47 113zm1170 498q0 53-37.5 90.5t-90.5 37.5-90.5-37.5-37.5-90.5 37.5-90.5 90.5-37.5 90.5 37.5 37.5 90.5zm-640-704q0 80-56 136t-136 56-136-56-56-136 56-136 136-56 136 56 56 136zm530 206q0 93-66 158.5t-158 65.5q-93 0-158.5-65.5t-65.5-158.5q0-92 65.5-158t158.5-66q92 0 158 66t66 158z">
                </path>
            </svg>
            `;  
        document.getElementById("switchPorts").innerHTML = `
            <svg  width="30" height="30" fill="#5c6bc0" class="mr-2 animate-spin" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg">
                <path d="M526 1394q0 53-37.5 90.5t-90.5 37.5q-52 0-90-38t-38-90q0-53 37.5-90.5t90.5-37.5 90.5 37.5 37.5 90.5zm498 206q0 53-37.5 90.5t-90.5 37.5-90.5-37.5-37.5-90.5 37.5-90.5 90.5-37.5 90.5 37.5 37.5 90.5zm-704-704q0 53-37.5 90.5t-90.5 37.5-90.5-37.5-37.5-90.5 37.5-90.5 90.5-37.5 90.5 37.5 37.5 90.5zm1202 498q0 52-38 90t-90 38q-53 0-90.5-37.5t-37.5-90.5 37.5-90.5 90.5-37.5 90.5 37.5 37.5 90.5zm-964-996q0 66-47 113t-113 47-113-47-47-113 47-113 113-47 113 47 47 113zm1170 498q0 53-37.5 90.5t-90.5 37.5-90.5-37.5-37.5-90.5 37.5-90.5 90.5-37.5 90.5 37.5 37.5 90.5zm-640-704q0 80-56 136t-136 56-136-56-56-136 56-136 136-56 136 56 56 136zm530 206q0 93-66 158.5t-158 65.5q-93 0-158.5-65.5t-65.5-158.5q0-92 65.5-158t158.5-66q92 0 158 66t66 158z">
                </path>
            </svg>
            `;             
     
        OLT(inputValue);
    }

    function checkType(str) 
    {
        const ipv4Regex = /^(\d{1,3}\.){3}\d{1,3}$/;
    
        if (ipv4Regex.test(str)) 
        {
            const octets = str.split('.');
            if (octets.every(octet => parseInt(octet, 10) >= 0 && parseInt(octet, 10) <= 255)) {
                return 'ipv4';
            }
        }
        
        return 'string';
    }

</script>

</x-app-layout>
