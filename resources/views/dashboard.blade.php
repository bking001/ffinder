<x-app-layout>

    <div class="py-5">
        <div class="mx-auto">

                    @if (!empty($UserFixedTaskNotification))
                            <input id="Notification_user_id" value="{{ $UserFixedTaskNotification->user_id }}" hidden />  
                            <input id="Notification_task_id" value="{{ $UserFixedTaskNotification->task_id }}" hidden />  
                    @endif
                    
                    <div id="errorAlert" class="max-w-2xl mx-auto px-4 py-3 text-center rounded relative items-center" hidden role="alert" style="color: #084298;background: #cfe2ff;border-color: #b6d4fe;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="currentColor" class="bi bi-exclamation-triangle-fill inline-block align-middle me-2" viewBox="0 0 16 16" role="img" aria-label="Warning:">
                            <path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"></path>
                        </svg>
                        <strong class="font-bold text-sm inline-block align-middle">მოხდა შეცდომა!</strong>
                        <span class="block sm:inline text-sm">ჩაწერე აბონენტის ნომერი</span>
                    </div> <br>


                    <div class="flex flex-wrap w-full mb-5 justify-center">
              
                        <button onclick="coordinatesModalStart()"  id="crm_coordButton" type="submit"  class="hidden inline-flex items-center px-4 py-2 h-8 ml-2  bg-gray-700 dark:bg-gray-900 border border-slate-300 shadow-lg dark:border-indigo-400 rounded-md font-semibold text-xs text-gray-200 dark:text-indigo-400 uppercase tracking-widest hover:bg-slate-400 dark:hover:bg-indigo-400 dark:hover:text-gray-900  dark:active:text-indigo-400">
                            coordinates
                        </button>
                       
                        <div id="SSHDIV" class="mb-5">
                            <button type="button" onclick="" id="TerminalButton" class="py-3 px-4 inline-flex items-center h-8 ml-2   text-xs font-semibold rounded-lg border border-gray-400 dark:border border-gray-200 text-gray-500 hover:border-blue-600 hover:text-blue-600 disabled:opacity-50 disabled:pointer-events-none dark:border-gray-700 dark:text-gray-400  uppercase tracking-widest dark:hover:text-blue-500 dark:hover:border-blue-600 dark:focus:outline-none dark:focus:ring-1 dark:focus:ring-gray-600" disabled>
                                TERMINAL
                            </button>
                        </div>

                        <button type="button" onclick="showModal('tvip-modal')" id="tvip_check_button" class="py-3 px-4 inline-flex items-center h-8 ml-2   text-xs font-semibold rounded-lg  border border-gray-400 dark:border border-gray-200 text-gray-500 dark:hover:border-blue-600 hover:text-blue-600 disabled:opacity-50 disabled:pointer-events-none dark:border-gray-700 dark:text-gray-400  uppercase tracking-widest  dark:hover:text-blue-500 dark:hover:border-blue-600 dark:focus:outline-none dark:focus:ring-1 dark:focus:ring-gray-600" disabled>
                            TVIP
                        </button>

                        <div id="Forward_div">
                        <button type="button" onclick="showModal('portForward-modal')" id="port_forward_button" class="hidden inline-flex items-center px-4 py-2 h-8 ml-2  bg-gray-700 dark:bg-gray-900 border border-slate-300 shadow-lg dark:border-indigo-400 rounded-md font-semibold text-xs text-gray-200 dark:text-indigo-400 uppercase tracking-widest hover:bg-slate-400 dark:hover:bg-indigo-400 dark:hover:text-gray-900  dark:active:text-indigo-400" disabled>
                          OPEN  PORTS
                        </button>
                        </div>           
                        
                        <div class="relative ml-2">
                            <input class="bw-input peer searchByInt   dark:bg-gray-800 dark:text-gray-400 text-center text-xs overflow-hidden shadow-lg sm:rounded-lg  h-8   border  dark:border-slate-700 pl-8" style="font-weight: 600;" type="text" id="searchByInt" name="searchByInt" value="" autocomplete="off" placeholder="აბონენტის ნომერი" pattern="\d*" inputmode="numeric" required>
                            <svg class="absolute left-2 top-2 w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"></path>
                            </svg>
                        </div>
                   
                        <button onclick="start()" type="submit" class="inline-flex items-center px-4 py-2 h-8 ml-2  bg-gray-700 dark:bg-gray-900 border border-slate-300 shadow-lg dark:border-indigo-400 rounded-md font-semibold text-xs text-gray-200 dark:text-indigo-400 uppercase tracking-widest hover:bg-slate-400 dark:hover:bg-indigo-400 dark:hover:text-gray-900  dark:active:text-indigo-400">
                            SEARCH
                        </button>

                        <button  onclick="startFilter()" type="submit" class="inline-flex items-center px-4 py-2 h-8 ml-2  bg-gray-700 dark:bg-gray-900 border border-slate-300 shadow-lg dark:border-indigo-400 rounded-md font-semibold text-xs text-gray-200 dark:text-indigo-400 uppercase tracking-widest hover:bg-slate-400 dark:hover:bg-indigo-400 dark:hover:text-gray-900  dark:active:text-indigo-400">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6h9.75M10.5 6a1.5 1.5 0 1 1-3 0m3 0a1.5 1.5 0 1 0-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-9.75 0h9.75" />
                            </svg>
                        </button>
                       

                    </div>
        </div>
 


                <div class="flex flex-wrap justify-center  sm:space-x-4">
                    <div class="w-full sm:w-1/2 md:w-1/3 lg:w-1/4 xl:w-1/4 mb-4  overflow-y-scroll bg-gray-300/50 dark:bg-gray-800/50 dark:border border-gray-800 rounded-lg shadow-lg" style="max-height: 35rem;" id="airsoft">
                        <div class="max-w-full rounded overflow-hidden">
                            <div class="px-6 py-4">
                                <div class="flex overflow-hidden gap-2 items-start" id="header_airsoft">
                                    <div class="flex-shrink-0 w-6 h-6" style="color:#5c6bc0 !important;">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.042 21.672L13.684 16.6m0 0l-2.51 2.225.569-9.47 5.227 7.917-3.286-.672zM12 2.25V4.5m5.834.166l-1.591 1.591M20.25 10.5H18M7.757 14.743l-1.59 1.59M6 10.5H3.75m4.007-4.243l-1.59-1.59"></path>
                                            </svg>
                                    </div>

                                    <hgroup class="flex flex-wrap items-baseline gap-x-2 overflow-hidden">
                                        <h2 class="text-sm font-sans font-bold text-gray-600 dark:truncate text-sm text-gray-600 font-semibold uppercase tracking-widest">Airsoft</h2>
                                    </hgroup>
                                </div>

                                            <div id="airsoft_buttons" class="flex flex-nowrap items-center justify-center p-2"></div><br>

                                            <table class="table-xs sm:w-full  text-sm text-left  text-gray-100 dark:text-gray-400">
                                                <thead class="text-xs  text-gray-600 uppercase" style="	line-height: .75rem;">
                                                    <tr>
                                                        <th scope="col" class="px-1 py-1 w-1/3   dark:text-gray-500  border-b border-gray-800">მდგომარეობა</th>
                                                        <td id="status_crm" class="px-1 py-3 w-1/2  font-bold border-b  border-gray-800"></td>
                                                    </tr>
                                                    <tr>
                                                        <th scope="col" class=" py-1  dark:text-gray-500 border-b  border-gray-800">
                                                            აბონენტი
                                                        </th>
                                                        <td id="ab_nom_crm" class="px-1  text-left  font-bold py-4 border-b dark:text-gray-400 border-gray-800"></td>
                                                    </tr>
                                                    <tr>
                                                        <th scope="col" class="px-1 py-1  dark:text-gray-500 border-b  border-gray-800">
                                                            სახ/გვარი
                                                        </th>
                                                        <td id="name_surname_crm" class="px-1 py-4 font-bold dark:text-gray-400 border-b  border-gray-800"></td>
                                                    </tr>
                                                    <tr>
                                                        <th scope="col" class="px-1 py-1  dark:text-gray-500 border-b  border-gray-800">
                                                            მისამართი
                                                        </th>
                                                        <td id="address_left_crm" class="px-1 py-4 font-bold dark:text-gray-400 border-b  border-gray-800"></td>
                                                    </tr>
                                                    <tr>
                                                        <th scope="col" class="px-1 py-1 dark:text-gray-500 border-b  border-gray-800">
                                                            იურ. სტატუსი
                                                        </th>
                                                        <td id="legal_status_crm" class="px-1 py-4 font-bold dark:text-gray-400 border-b  border-gray-800"></td>
                                                    </tr>
                                                    <tr>
                                                        <th id="com_name_th" scope="col" class="px-1 py-1 dark:text-gray-500 border-b  border-gray-800 hidden">
                                                           კომპანია
                                                        </th>
                                                        <td id="company_name" class="px-1 py-4 font-bold dark:text-gray-400 border-b  border-gray-800 hidden"></td>
                                                    </tr>
                                                    <tr>
                                                        <th scope="col" class="px-1 py-1 dark:text-gray-500 border-b  border-gray-800">
                                                            კოორდინატები
                                                        </th>
                                                        <td id="coordinates_crm" class="px-1 py-4 font-bold border-b  dark:text-gray-400  border-gray-800"></td>
                                                    </tr>
                                                    <tr>
                                                        <th scope="col" class="px-1 py-1 dark:text-gray-500 border-b  border-gray-800">
                                                            აბონენტის IP
                                                        </th>
                                                        <td id="onu_crm" class="px-1 py-4 font-bold border-b  dark:text-gray-400  border-gray-800"></td>
                                                    </tr>
                                                    <tr>
                                                        <th scope="col" class="px-1 py-1 dark:text-gray-500 border-b  border-gray-800">
                                                            სექტორი
                                                        </th>
                                                        <td id="olt_crm" class="px-1 py-4 font-bold border-b  dark:text-gray-400  border-gray-800"></td>
                                                    </tr>
                                                    <tr>
                                                        <th scope="col" class="px-1 py-1 font-bold dark:text-gray-500 border-b  border-gray-800">
                                                            როუტერი
                                                        </th>
                                                        <td id="mikrotik_crm" class="px-1 font-bold py-4 border-b  dark:text-gray-400  border-gray-800"></td>
                                                    </tr>
                                                    <tr>
                                                        <th scope="col" class="px-1 py-1 font-bold dark:text-gray-500 border-b  border-gray-800">
                                                            მაკი
                                                        </th>
                                                        <td id="mac_crm" class="px-1 py-4 font-bold border-b  dark:text-gray-400  border-gray-800"></td>
                                                    </tr>
                                                    <tr>
                                                        <th scope="col" class="px-1 py-1  dark:text-gray-500 border-b  border-gray-800">
                                                            Macvendors
                                                        </th>
                                                        <td id="vendoor_crm" class="px-1 py-4 font-bold border-b  dark:text-gray-400  border-gray-800"></td>
                                                    </tr>

                                                    <tr>
                                                        <th id="comment_th" scope="col" class="px-1 py-1 dark:text-gray-500 border-b  border-gray-800 hidden">
                                                            კომენტარი
                                                        </th>
                                                        <td id="comment_td" class="px-1 py-4 border-b font-bold dark:text-gray-400  border-gray-800  hidden">
                                                        </td>
                                                    </tr>
                                                </thead>
                                            </table>

                            </div>
                        </div>
                    </div>


                    <div class="w-full sm:w-1/2 md:w-1/3 lg:w-1/4 xl:w-47  mb-4  overflow-x-hidden bg-gray-300/50 dark:bg-gray-800/50 dark:border border-gray-800 rounded-lg shadow-lg"  style="max-height: 35rem;"id="olt">
                        <div class="max-w-full  rounded overflow-hidden">
                            <div class="px-6 py-4">
                                <div class="flex overflow-hidden gap-2 items-start" id="header_olt">
                                    <div class="flex-shrink-0 w-6 h-6" style="color:#5c6bc0 !important;">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 21L3 16.5m0 0L7.5 12M3 16.5h13.5m0-13.5L21 7.5m0 0L16.5 12M21 7.5H7.5"></path>
                                            </svg>
                                    </div>

                                    <hgroup class="flex flex-wrap items-baseline gap-x-2 overflow-hidden">
                                        <h2 class="text-sm font-bold font-sans text-gray-600  dark:truncate text-sm text-gray-600 font-semibold uppercase tracking-widest" id="Device_Type_Tittle">Olt/Sector</h2>
                                    </hgroup>
                                </div>

                                <div class="flex flex-col items-center justify-center p-2">
                                    <div id="OLT_DATA" style="display: contents;">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32" fill="none" stroke-width="1.5" stroke="currentColor" class="h-8 w-8 stroke-gray-300 stroke-gray-700 dark:stroke-gray-700">
                                            <path d="M2 15.6154H7.5L9.5 12L12 19L14.1094 15.6154H29.75" stroke-linecap="round" stroke-linejoin="round"></path>
                                        </svg>
                                        <p class="mt-2 text-sm text-gray-900 dark:text-gray-600">
                                            No results
                                        </p>
                                    </div><br>

                                    <div id="OLT_LINKS" style="display: contents;"></div><br>
                                    <div id="OLT_MACS" style="display: contents;"></div><br>

                                </div>


                            </div>
                        </div>
                    </div>


                    <div class="w-full sm:w-1/2 md:w-1/3 lg:w-1/4 xl:w-1/4 mb-4   bg-gray-300/50 dark:bg-gray-800/50 dark:border border-gray-800 rounded-lg shadow-lg"  style="max-height: 35rem;" id="PRTG">
                        <div class="w-full rounded overflow-hidden">
                            <div class="px-6 py-4">
                                <div class="flex overflow-hidden gap-2 items-start" id="header_prtg">
                                   <div class="flex-shrink-0 w-6 h-6" style="color:#5c6bc0 !important;">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 6.375c0 2.278-3.694 4.125-8.25 4.125S3.75 8.653 3.75 6.375m16.5 0c0-2.278-3.694-4.125-8.25-4.125S3.75 4.097 3.75 6.375m16.5 0v11.25c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125V6.375m16.5 0v3.75m-16.5-3.75v3.75m16.5 0v3.75C20.25 16.153 16.556 18 12 18s-8.25-1.847-8.25-4.125v-3.75m16.5 0c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125"></path>
                                            </svg>
                                        </div>

                                    <hgroup class="flex flex-wrap items-baseline gap-x-2 overflow-hidden">
                                        <h2 class="text-sm font-bold text-gray-600 dark:truncate text-sm text-gray-600 font-semibold uppercase tracking-widest">PRTG</h2>
                                    </hgroup>
                                </div>
                            </div>

                                <div  class="w-full h-full flex flex-col items-center justify-center p-2">
                                    <div id="Prtg_Data" style="display: contents;">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32" fill="none" stroke-width="1.5" stroke="currentColor" class="h-8 w-8 stroke-gray-700 dark:stroke-gray-700">
                                            <path d="M2 15.6154H7.5L9.5 12L12 19L14.1094 15.6154H29.75" stroke-linecap="round" stroke-linejoin="round"></path>
                                        </svg>
                                        <p  class="mt-2 items-center justify-center text-sm text-gray-900 dark:text-gray-600">
                                            No results
                                        </p>
                                    </div>
                                    <iframe id="resultFrame" name="resultFrame" frameborder="0" width="100%" height="420"></iframe>
                                </div>

                        </div>
                    </div>
                </div>


                    <div class="w-full sm:w-1/2 md:w-4/5 lg:w-4/5 xl:w-4/5 mb-4 mx-auto bg-gray-300/50 dark:bg-gray-800/50 dark:border border-gray-800 rounded-lg shadow-lg" id="mikrotik">

                        <div class="px-6 py-4">
                            <div class="flex overflow-hidden gap-2 items-start" id="header_mikrotik">
                                    <div class="flex-shrink-0 w-6 h-6" style="color:#5c6bc0 !important;">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.59 14.37a6 6 0 01-5.84 7.38v-4.8m5.84-2.58a14.98 14.98 0 006.16-12.12A14.98 14.98 0 009.631 8.41m5.96 5.96a14.926 14.926 0 01-5.841 2.58m-.119-8.54a6 6 0 00-7.381 5.84h4.8m2.581-5.84a14.927 14.927 0 00-2.58 5.84m2.699 2.7c-.103.021-.207.041-.311.06a15.09 15.09 0 01-2.448-2.448 14.9 14.9 0 01.06-.312m-2.24 2.39a4.493 4.493 0 00-1.757 4.306 4.493 4.493 0 004.306-1.758M16.5 9a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z"></path>
                                        </svg>
                                    </div>

                                <hgroup class="flex flex-wrap items-baseline gap-x-2 overflow-hidden">
                                    <h2 class="text-sm font-bold text-gray-600 dark:truncate text-sm text-gray-600 font-semibold uppercase tracking-widest">Mikrotik</h2>
                                </hgroup>
                            </div>
                            <div class="w-full flex flex-col items-center justify-center p-2" id="Mikrotik_Data">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32" fill="none" stroke-width="1.5" stroke="currentColor" class="h-8 w-8 stroke-gray-700 dark:stroke-gray-700">
                                        <path d="M2 15.6154H7.5L9.5 12L12 19L14.1094 15.6154H29.75" stroke-linecap="round" stroke-linejoin="round"></path>
                                    </svg>
                                    <p class="mt-2 text-sm text-gray-900 dark:text-gray-600">
                                        No results
                                    </p>
                            </div>


                            <div class="flex flex-nowrap items-center justify-center p-2">
                                            <div id="Download" class="chart-container"></div>
                                            <div id="Upload" class="chart-container"></div>
                                            <div id="vlan" class="chart-container"></div>
                            </div>


                        </div>
                    </div>
                
    </div>

   
    <x-bladewind.notification position="top right" />
    <x-bladewind.notification />
 
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
        title="SFP DETAILS"
        name="mikrotik-router-details"
        show_close_icon="true"
        size="large"
        backdrop_can_close="true"
        show_action_buttons="true"
        cancel_button_label=""
        ok_button_label=""
        blur_backdrop="false">

        
        <div style="max-height: 700px; overflow-y: auto;">
            <div id="mikrotikroutersfpdetails" class="flex items-center sm:justify-center ml-4 sm:ml-0" style="width: 100%;">
               
            </div>
        </div>
 
    </x-bladewind::modal>
 
    <x-bladewind::modal
        type="info"
        title="კომენტარები"
        name="comment-modal"
        show_close_icon="true"
        size="xl"
        backdrop_can_close="true"
        show_action_buttons="true"
        cancel_button_label=""
        ok_button_label=""
        blur_backdrop="false">

        <div style="max-height: 700px !important; overflow-y: scroll;">
 
                        <div class="mx-auto">
                                <select id="comment_type_select" class="block appearance-none w-full bg-white  border border-gray-300 text-sm text-gray-700 py-2 px-3 pr-8 rounded leading-tight focus:outline-none focus:bg-white focus:border-gray-500">
                                    <option selected value="-1">აირჩიე ტიპი</option>
                                    <option value="59">სიჩქარის პრობლემა</option>
                                    <option value="60">არ მიეწოდება ინტერნეტი</option>
                                    <option value="61">გამორთულია დავალიანების გამო</option>
                                    <option value="62">პარატურის პრობლემა</option>
                                    <option value="63">სხვა</option>
                                    <option value="64">კრედიტის ჩართვა</option>
                                    <option value="65">დროებით შეჩერება</option>
                                    <option value="66">პაკეტის შეცვლა</option>
                                    <option value="67">გაუქმება</option>
                                    <option value="68">სააბონენტოს გადახდა</option>
                                    <option value="69">ინტერნეტის განახლება</option>
                                    <option value="70">პორტის გახსნა</option>
                                    <option value="71">კონსულტაცია</option>
                                    <option value="72">დასარეკი</option>
                                    <option value="73">აპარატურის გადატანა</option>
                                    <option value="74">თანხის ჩამოჭრა</option>
                                    <option value="75">თანხის ანაზღაურება</option>
                                    <option value="76">აუნაზღაურდა</option>
                                    <option value="77">ბუღალტრული</option>
                                    <option value="78">კვების ბლოკია დასაბრუნებელი</option>
                                    <option value="79">ინსტალაციის განაწილება</option>
                                    <option value="81">შეჩერების მოხსნა</option>
                                    <option value="82">ტელევიზია</option>
                                    <option value="136">აქციები</option>
                                </select>
                            </div>
             
                        <br>
                        <div class="w-full mb-4 border border-gray-200 rounded-lg bg-gray-50 dark:bg-gray-700 dark:border-gray-800">
                            <div class="px-4 py-2 bg-white rounded-t-lg dark:bg-gray-200">
                                <textarea id="CommentTextareaMessageInput" rows="5" class="w-full px-0 text-sm text-gray-900 bg-white border-0 dark:bg-gray-200 focus:ring-0 dark:text-gray-900 dark:placeholder-gray-400" placeholder="Write a comment..." required ></textarea>
                            </div>
                            <div class="flex items-center justify-start px-3 py-2 border-t dark:border-gray-600">
                                    <div class="flex ps-0 space-x-1 sm:ps-2">    
                                        <button onclick="add_CRM_Comment()" type="submit" class="inline-flex items-center px-4 py-2 h-8 ml-2  bg-gray-700 dark:bg-indigo-400 border border-slate-300 shadow-lg dark:border-indigo-400 rounded-full font-semibold text-xs text-gray-200 dark:text-gray-900 uppercase tracking-widest hover:bg-slate-400 dark:hover:bg-indigo-400 dark:hover:text-gray-900  dark:active:text-indigo-400">
                                            დაწერე კომენტარი
                                        </button>
                                    </div>
                            </div>
                        </div>

                        <br>
                            <div id="Comment_Result_Status_Body"></div>
                        <br>

                        <div class="flex items-center sm:justify-center ml-4 sm:ml-0" style="width: 100%;">
                            <table class="text-sm border-separate" style="width: 100%;">
                                <thead>
                                <tr  class="tr-class sticky top-0 z-10">
                                    <th class="td-class text-indigo-400 text-center text:xs uppercase tracking-widest" style="color:#818cf8;background:#141c3d;letter-spacing: .1em;font-weight: 600;">თარიღი</th>
                                    <th class="td-class text-indigo-400 text-center text:xs uppercase tracking-widest" style="color:#818cf8;background:#141c3d;letter-spacing: .1em;font-weight: 600;">ტიპი</th>
                                    <th class="td-class text-indigo-400 text-center text:xs uppercase tracking-widest" style="color:#818cf8;background:#141c3d;letter-spacing: .1em;font-weight: 600;">თანამშრომელი</th>
                                    <th class="td-class text-indigo-400 text-center text:xs uppercase tracking-widest" style="color:#818cf8;background:#141c3d;letter-spacing: .1em;font-weight: 600;">კომენტარი</th>
                                </tr>
                                </thead>
                                <tbody  id="table-body">             
 
                                </tbody>
                            </table> 
                        </div>
                         
                  

        </div>

    </x-bladewind::modal>


    <x-bladewind::modal
        type="info"
        title="Tasks"
        name="task-modal"
        show_close_icon="true"
        size="xl"
        backdrop_can_close="true"
        show_action_buttons="true"
        cancel_button_label=""
        ok_button_label=""
        blur_backdrop="false">

        <div style="max-height: 700px; overflow-y: auto;">
 
            <div class="flex items-center sm:justify-center ml-4 sm:ml-0" style="width: 100%;">
                <table class="text-sm border-separate" style="width: 100%;">
                    <thead>
                    <tr  class="tr-class sticky top-0 z-10">
                        <th class="td-class text-indigo-400 text-center text:xs uppercase tracking-widest" style="color:#818cf8;background:#141c3d;letter-spacing: .1em;font-weight: 600;">თარიღი</th>
                        <th class="td-class text-indigo-400 text-center text:xs uppercase tracking-widest" style="color:#818cf8;background:#141c3d;letter-spacing: .1em;font-weight: 600;">ტიპი</th>
                        <th class="td-class text-indigo-400 text-center text:xs uppercase tracking-widest" style="color:#818cf8;background:#141c3d;letter-spacing: .1em;font-weight: 600;">კომენტარი</th>
                    </tr>
                    </thead>
                    <tbody  id="task_table-body">             

                    </tbody>
                </table>
            </div>

        </div>
 
    </x-bladewind::modal>

    <input id="Input_user" value="" hidden />
    <input id="Input_ifIndex" value="" hidden />
    <input id="Input_Ip" value="" hidden />
    <input id="Input_write" value="" hidden />
    <input id="Input_read" value="" hidden />
    <input id="Input_tv_tarrif" value="" hidden />
    <input id="Input_port_forward_user" value="" hidden />
    <input id="Input_port_forward_ip" value="" hidden />
    <input id="Input_add_tarrif" value="" hidden />
    <input id="Input_tv_account" value="" hidden />


    <input id="Input_antenna_ip" value="" hidden />
    <input id="Input_antenna_mac" value="" hidden />
    <input id="Input_antenna_name" value="" hidden />


    <input id="Input_Port_Forward_client" value="" hidden />
    <input id="Input_Port_Forward_pPort" value="" hidden />
    <input id="Input_Port_Forward_rPort" value="" hidden />
    <input id="Input_Port_Forward_pIP" value="" hidden />
    <input id="Input_Port_Forward_rIP" value="" hidden />
    <input id="Input_Port_Forward_protocol" value="" hidden />
    <input id="Input_Port_Forward_id" value="" hidden />


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
    
    <x-bladewind.modal
        size="medium"
        center_action_buttons="true"
        type="warning"
        title="Confirm Onu Restart"
        :ok_button_action="'restartBdcomOnu()'"
        close_after_action="false"
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
        :ok_button_action="'restartOnu()'"
        close_after_action="false"
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
        close_after_action="false"
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
        :ok_button_action="'restartOnu_hsgq()'"
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
        title="Confirm Port Rule Delete"
        :ok_button_action="'Port_Forward_Rule_Delete()'"
        close_after_action="false"
        name="portForward-delete-modal"
        show_close_icon="true"
        blur_backdrop="false"
        ok_button_label="Yes, Delete"
        cancel_button_label="Don't Delete">
        Are you sure you want to delete rule ? This action cannot be undone

        <br><br>
        <div class="flex flex-col items-center justify-center">

            <textarea id="additional_info_port_delete" placeholder="ჩაწერე კომენტარი" class="dark:bg-gray-900 dark:text-gray-400 text-xs overflow-hidden shadow-sm sm:rounded-lg w-full h-24 border border-slate-700 p-2" rows="4"></textarea>
            <br>
            <center><p id="port_delete_comment_empty_" class="text-red-400 text-sm"></p></center>
        </div>

    </x-bladewind.modal>

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
        size="medium"
        center_action_buttons="true"
        type="warning"
        title="Confirm Port Rule Edit"
        :ok_button_action="'Port_Forward_Rule_Edit()'"
        close_after_action="false"
        name="portForward-edit-modal"
        show_close_icon="true"
        blur_backdrop="false"
        ok_button_label="Yes, Edit"
        cancel_button_label="Don't Edit">
        Are you sure you want to edit rule ? This action cannot be undone

        <br><br>
        <div class="flex flex-col items-center justify-center">

            <textarea id="additional_info_port_edit" placeholder="ჩაწერე კომენტარი" class="dark:bg-gray-900 dark:text-gray-400 text-xs overflow-hidden shadow-sm sm:rounded-lg w-full h-24 border border-slate-700 p-2" rows="4"></textarea>
            <br>
            <center><p id="port_edit_comment_empty_" class="text-red-400 text-sm"></p></center>
        </div>

    </x-bladewind.modal>


    <x-bladewind.modal
        size="medium"
        center_action_buttons="true"
        type="warning"
        title="Confirm Antenna Kick"
        :ok_button_action="'AntennaKickFin()'"
        close_after_action="false"
        name="antenna_kick_name"
        show_close_icon="true"
        blur_backdrop="false"
        ok_button_label="Yes, Kick"
        cancel_button_label="Don't Kick">
        Are you sure you want to kick antenna ? This action cannot be undone

        <br><br>
        <div class="flex flex-col items-center justify-center">

            <textarea id="additional_info_antenna_kick" placeholder="ჩაწერე კომენტარი" class="dark:bg-gray-900 dark:text-gray-400 text-xs overflow-hidden shadow-sm sm:rounded-lg w-full h-24 border border-slate-700 p-2" rows="4"></textarea>
            <br>
            <center><p id="antenna_kick_comment_empty_" class="text-red-400 text-sm"></p></center>
        </div>

    </x-bladewind.modal>

    
    <x-bladewind.modal
        size="medium"
        center_action_buttons="true"
        type="warning"
        title="Confirm Antenna Reboot"
        :ok_button_action="'AntennaRebootFin()'"
        close_after_action="false"
        name="antenna_reboot_name"
        show_close_icon="true"
        blur_backdrop="false"
        ok_button_label="Yes, Reboot"
        cancel_button_label="Don't Reboot">
        Are you sure you want to kick antenna ? This action cannot be undone

        <br><br>
        <div class="flex flex-col items-center justify-center">

            <textarea id="additional_info_antenna_restart" placeholder="ჩაწერე კომენტარი" class="dark:bg-gray-900 dark:text-gray-400 text-xs overflow-hidden shadow-sm sm:rounded-lg w-full h-24 border border-slate-700 p-2" rows="4"></textarea>
            <br>
            <center><p id="antenna_restart_comment_empty_" class="text-red-400 text-sm"></p></center>
        </div>

    </x-bladewind.modal>

    <x-bladewind.modal
        size="medium"
        center_action_buttons="true"
        type="warning"
        title="Add Coordinates"
        :ok_button_action="'coordinatesAdd()'"
        close_after_action="false"
        name="coordinatesAdd"
        show_close_icon="true"
        blur_backdrop="false"
        ok_button_label="Yes, Add"
        cancel_button_label="Don't Add">
        
        <br>
        <div class="flex flex-col items-center justify-center">

          
            {{-- <select  id="units_dashboard"   style="width:100% !important;height:100% !important;" class="w-full mx-auto sm:w-1/2 dark:bg-gray-900 text-center dark:text-gray-400 text-xs overflow-hidden  sm:rounded-lg   h-9  border border-slate-700 uppercase tracking-widest" required> 
                <option value="0" class="dark:bg-gray-900 text-center dark:text-gray-400 text-xs overflow-hidden shadow-sm sm:rounded-lg w-full h-8 border border-slate-700 uppercase tracking-widest">GEO GPS</option>
            </select>       
            <br> --}}
            <select  id="new_gps"   style="width:100% !important;height:100% !important;" class="w-full mx-auto sm:w-1/2 dark:bg-gray-900 text-center dark:text-gray-400 text-xs overflow-hidden  sm:rounded-lg   h-9  border border-slate-700 uppercase tracking-widest" required> 
                <option value="0" class="dark:bg-gray-900 text-center dark:text-gray-400 text-xs overflow-hidden shadow-sm sm:rounded-lg w-full h-8 border border-slate-700 uppercase tracking-widest">NEW GPS</option>
            </select>       
            <br>
            <x-bladewind::input required="false" name="crm_coord_input" placeholder="ჩაწერე კოორდინატი" class="dark:bg-gray-900 dark:text-gray-400 text-xs overflow-hidden shadow-sm sm:rounded-lg w-full h-8  border border-slate-700" />
        
            <center><p id="gps_empty_" class="text-red-400 text-sm"></p></center>
            <br>
            <textarea id="additional_info_coordinates" placeholder="ჩაწერე კომენტარი" class="dark:bg-gray-900 dark:text-gray-400 text-xs overflow-hidden shadow-sm sm:rounded-lg w-full h-24 border border-slate-700 p-2" rows="4"></textarea>
            <br>
            <center><p id="gps_comment_empty_" class="text-red-400 text-sm"></p></center>
        </div>

    </x-bladewind.modal>

     
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

    <x-bladewind.modal
        size="medium"
        center_action_buttons="true"
        type="warning"
        title="Confirm Onu Restart"
        :ok_button_action="'restartOnu_vsolution()'"
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

    
    <x-bladewind.modal
        size="medium"
        center_action_buttons="true"
        type="warning"
        title="Confirm Open Port"
        :ok_button_action="'Open_Port_Finish()'"
        close_after_action="false"
        name="open-ports-confirm"
        show_close_icon="true"
        blur_backdrop="false"
        ok_button_label="Yes, Open"
        cancel_button_label="Don't Open">
        Are you sure you want to open port ? This action cannot be undone

        <br>
        <div class="flex flex-col items-center justify-center">
            <br>
            <textarea id="additional_info_port_create" placeholder="ჩაწერე კომენტარი" class="dark:bg-gray-900 dark:text-gray-400 text-xs overflow-hidden shadow-sm sm:rounded-lg w-full h-24 border border-slate-700 p-2" rows="4"></textarea>
            <br>
            <center><p id="port_create_comment_empty_" class="text-red-400 text-sm"></p></center>
        </div>

    </x-bladewind.modal>
     

    <x-bladewind.modal
        size="medium"
        center_action_buttons="true"
        type="warning"
        title="Confirm Tarrif Delete"
        :ok_button_action="'DeleteTarrifFromAccount()'"
        close_after_action="true"
        name="Tarrif-Delete"
        show_close_icon="true"
        blur_backdrop="false"
        ok_button_label="Yes, Delete"
        cancel_button_label="Don't Delete">
        Are you sure you want to delete tarrif ? This action cannot be undone
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
        <div class="flex items-center justify-center" style="max-height: 700px; overflow-y: auto;display:block;" id="Pon_Modal_Body"></div>
 
    </x-bladewind::modal>

    <x-bladewind::modal
        type="info"
        title="Filter Search"
        name="Filter-Modal"
        show_close_icon="false"
        size="omg"
        show_close_icon="false"
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
                <button onclick="filter()" type="submit" class="inline-flex items-center px-4 py-2 h-8 ml-2  bg-gray-700 dark:bg-gray-900 border border-slate-300 shadow-lg dark:border-indigo-400 rounded-md font-semibold text-xs text-gray-200 dark:text-indigo-400 uppercase tracking-widest hover:bg-slate-400 dark:hover:bg-indigo-400 dark:hover:text-gray-900  dark:active:text-indigo-400">
                    SEARCH
                </button>
            </center>


            <div id="filterResultTable"></div>

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
        title="Create Account Tarrif"
        :ok_button_action="'CreateTarrif()'"
        close_after_action="true"
        name="tarrif_add_action_modal"
        show_close_icon="true"
        blur_backdrop="false"
        ok_button_label="Yes, Create"
        cancel_button_label="Don't Create">
      

        <div class="flex items-center justify-center" id="tarrif_create_select"></div> 
    </x-bladewind.modal>
 

    <x-bladewind::modal
        type="info"
        title="TMS"
        name="tvip-modal"
        show_close_icon="true"
        size="xl"
        backdrop_can_close="true"
        show_action_buttons="true"
        cancel_button_label=""
        ok_button_label=""
        blur_backdrop="false">
 
        <div class="flex flex-col md:flex-row lg:md:flex-row items-center justify-center" style="max-height: 500px; overflow-y: auto;">

            <button onclick="tmsRefresh()" id="tms_refresh_button" type="submit" class="inline-flex items-center justify-center px-4 py-2 h-8 ml-2 w-48 bg-gray-900 center disabled:opacity-50 disabled:pointer-events-none dark:bg-gray-900 border  border-indigo-400 rounded-md font-semibold text-xs text-white dark:text-indigo-400 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-indigo-400 dark:hover:text-gray-900  dark:active:text-indigo-400" style="text-align:center;">
                REFRESH
            </button>

            <button onclick="tvboxAddTarrif()" type="submit" class="inline-flex items-center justify-center px-4 py-2 h-8 ml-2 w-48 bg-gray-900   dark:bg-gray-900 border  border-indigo-400 rounded-md font-semibold text-xs text-white dark:text-indigo-400 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-indigo-400 dark:hover:text-gray-900  dark:active:text-indigo-400">
                ADD TARRIF
            </button>

        </div><br>

        <div style="max-height: 700px; overflow-y: auto;">
  
            <div class="flex items-center sm:justify-center ml-4 sm:ml-0" >
                <table class="text-sm border-separate border-spacing-y-2" >
                    <thead>
                    <tr  class="tr-class">
                        <th class="td-class text-indigo-400 text-center text:xs" style="color:#818cf8;background:#141c3d;letter-spacing: .1em;font-weight: 600;text-transform: uppercase;">ID</th>           
                        <th class="td-class text-indigo-400 text-center text:xs" style="color:#818cf8;background:#141c3d;letter-spacing: .1em;font-weight: 600;text-transform: uppercase;">Tarrif</th>
                        <th class="td-class text-indigo-400 text-center text:xs" style="color:#818cf8;background:#141c3d;letter-spacing: .1em;font-weight: 600;text-transform: uppercase;">Delete</th>
                    </tr>
                    </thead>
                    <tbody  id="tvip_tarrif_table_body">             
                        
                    </tbody>
                </table>
            </div>
  

            <div class="flex items-center sm:justify-center ml-4 sm:ml-0" style="width: 100%;">
                <table class="text-sm border-separate border-spacing-y-2" style="width: 100%;">
                    <thead>
                    <tr  class="tr-class">
                        <th class="td-class text-indigo-400 text-center text:xs" style="color:#818cf8;background:#141c3d;letter-spacing: .1em;font-weight: 600;text-transform: uppercase;">Account</th>           
                        <th class="td-class text-indigo-400 text-center text:xs" style="color:#818cf8;background:#141c3d;letter-spacing: .1em;font-weight: 600;text-transform: uppercase;">Type</th>
                        <th class="td-class text-indigo-400 text-center text:xs" style="color:#818cf8;background:#141c3d;letter-spacing: .1em;font-weight: 600;text-transform: uppercase;">Mac Address</th>
                        <th class="td-class text-indigo-400 text-center text:xs" style="color:#818cf8;background:#141c3d;letter-spacing: .1em;font-weight: 600;text-transform: uppercase;">Version</th>   
                        <th class="td-class text-indigo-400 text-center text:xs" style="color:#818cf8;background:#141c3d;letter-spacing: .1em;font-weight: 600;text-transform: uppercase;">Last Online</th>  
                        <th class="td-class text-indigo-400 text-center text:xs" style="color:#818cf8;background:#141c3d;letter-spacing: .1em;font-weight: 600;text-transform: uppercase;">CHANNELS</th> 
                        <th class="td-class text-indigo-400 text-center text:xs" style="color:#818cf8;background:#141c3d;letter-spacing: .1em;font-weight: 600;text-transform: uppercase;">UPDATE</th> 
                        <th class="td-class text-indigo-400 text-center text:xs" style="color:#818cf8;background:#141c3d;letter-spacing: .1em;font-weight: 600;text-transform: uppercase;">RESTART</th>                   
                    </tr>
                    </thead>
                    <tbody  id="tvip_table_body">             

                    </tbody>
                </table>
            </div>
        </div>
    </x-bladewind::modal>


    <x-bladewind::modal
        type="info"
        title="SEARCH OPEN PORTS"
        name="SEARCH_FREE_OPEN_PORTS_MODAL"
        show_close_icon="true"
        size="xl"
        backdrop_can_close="true"
        show_action_buttons="true"
        cancel_button_label=""
        ok_button_label=""
        blur_backdrop="false">

        <div class="flex flex-col w-full md:flex-row lg:md:flex-row items-center justify-center">
   
            <div class="grid grid-cols-2 gap-4 mt-6"> 
                <x-bladewind::input name="PortForSearchInputId" placeholder="Search Port e.g   80,443,8000,..."
                                            class="dark:bg-gray-900 dark:text-gray-400 text-xs overflow-hidden shadow-sm sm:rounded-lg w-full h-8  border border-slate-700"/>
                <button onclick="PortForwardSearchCustomPort()" id="port_refresh_button_id" type="submit" class="inline-flex items-center justify-center px-4 py-2 h-8 ml-2 w-48 bg-gray-900 center disabled:opacity-50 disabled:pointer-events-none dark:bg-gray-900 border  border-indigo-400 rounded-md font-semibold text-xs text-white dark:text-indigo-400 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-indigo-400 dark:hover:text-gray-900  dark:active:text-indigo-400" style="text-align:center;">
                    SEARCH PORT
                </button>
            </div>
        </div>
        
        <br>
     
        <div id="SearchPortResultSaerto"></div> <br>
        <div id="PortSearchResultDiv" style="max-height: 600px; overflow-y: auto;"></div>
         

    </x-bladewind::modal>   
     

    <x-bladewind::modal
        type="info"
        title="PORT FORWARDING"
        name="portForward-modal"
        show_close_icon="true"
        size="xl"
        backdrop_can_close="true"
        show_action_buttons="true"
        cancel_button_label=""
        ok_button_label=""
        blur_backdrop="false">

        <div class="flex flex-col md:flex-row lg:md:flex-row items-center justify-center" style="max-height: 500px; overflow-y: auto;">
            <button onclick="PortForwardRefresh()" id="port_refresh_button_id" type="submit" class="inline-flex items-center justify-center px-4 py-2 h-8 ml-2 w-48 bg-gray-900 center disabled:opacity-50 disabled:pointer-events-none dark:bg-gray-900 border  border-indigo-400 rounded-md font-semibold text-xs text-white dark:text-indigo-400 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-indigo-400 dark:hover:text-gray-900  dark:active:text-indigo-400" style="text-align:center;">
                REFRESH
            </button>
        </div><br>

        <div style="max-height: 700px; overflow-y: auto;">

            <div id="forward_add_div">

                <button onclick="SEARCH_CUSTOM_OPEN_PORTS()"  type="submit" class="inline-flex items-center justify-center px-4 py-2 h-8 ml-2 w-48 bg-gray-900 center disabled:opacity-50 disabled:pointer-events-none dark:bg-gray-900 border  border-indigo-400 rounded-md font-semibold text-xs text-white dark:text-indigo-400 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-indigo-400 dark:hover:text-gray-900  dark:active:text-indigo-400" style="text-align:center;">
                    CREATE OPEN PORTS
                </button>
            </div>
            <br>

            <div id="pforward_div" class="flex items-center sm:justify-center ml-4 sm:ml-0" style="width: 100%;">
                <table class="text-sm border-separate border-spacing-y-2" style="width: 100%;">
                    <thead>
                    <tr  class="tr-class">
                        <th class="td-class text-indigo-400 text-center text:xs" style="color:#818cf8;background:#141c3d;letter-spacing: .1em;font-weight: 600;text-transform: uppercase;">Comment</th>           
                        <th class="td-class text-indigo-400 text-center text:xs" style="color:#818cf8;background:#141c3d;letter-spacing: .1em;font-weight: 600;text-transform: uppercase;">Privat Address</th>
                        <th class="td-class text-indigo-400 text-center text:xs" style="color:#818cf8;background:#141c3d;letter-spacing: .1em;font-weight: 600;text-transform: uppercase;">Privat Port</th>
                        <th class="td-class text-indigo-400 text-center text:xs" style="color:#818cf8;background:#141c3d;letter-spacing: .1em;font-weight: 600;text-transform: uppercase;">Protocol</th>   
                        <th class="td-class text-indigo-400 text-center text:xs" style="color:#818cf8;background:#141c3d;letter-spacing: .1em;font-weight: 600;text-transform: uppercase;">Remote Address</th>  
                        <th class="td-class text-indigo-400 text-center text:xs" style="color:#818cf8;background:#141c3d;letter-spacing: .1em;font-weight: 600;text-transform: uppercase;">Remote Port</th> 
                        <th class="td-class text-indigo-400 text-center text:xs" style="color:#818cf8;background:#141c3d;letter-spacing: .1em;font-weight: 600;text-transform: uppercase;">Scanner</th> 
                        <th class="td-class text-indigo-400 text-center text:xs" style="color:#818cf8;background:#141c3d;letter-spacing: .1em;font-weight: 600;text-transform: uppercase;">Rule</th>   
                        <th class="td-class text-indigo-400 text-center text:xs" style="color:#818cf8;background:#141c3d;letter-spacing: .1em;font-weight: 600;text-transform: uppercase;">Edit</th> 
                        <th class="td-class text-indigo-400 text-center text:xs" style="color:#818cf8;background:#141c3d;letter-spacing: .1em;font-weight: 600;text-transform: uppercase;">Delete</th>                  
                    </tr>
                    </thead>
                    <tbody  id="PortForward_table_body">             

                    </tbody>
                </table>
            </div>
        </div>


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
        title="AIRSOFT INSTALL"
        name="crm-install-modal"
        show_close_icon="true"
        size="large"
        backdrop_can_close="true"
        show_action_buttons="true"
        cancel_button_label=""
        ok_button_label=""
        blur_backdrop="false">

        <div class="flex items-center justify-center" style="max-height: 700px; overflow-y: auto;display:block;" id="Airsoft_Install_Modal_Body"></div>
 
    </x-bladewind::modal>

    <x-bladewind.modal
        size="medium"
        center_action_buttons="true"
        type="warning"
        title="Confirm Edit Vlan"
        :ok_button_action="'Port_vsolution_VlanEdit()'"
        close_after_action="false"
        name="vlanEdit-confirm-modal_vsol"
        show_close_icon="true"
        blur_backdrop="false"
        ok_button_label="Yes, Install"
        cancel_button_label="Don't Install">

        Are you sure you want to edit vlan in onu ? This action cannot be undone

        <br><br>
        <div class="flex flex-col items-center justify-center">

            <textarea id="additional_info_edit_vlans_vsol" placeholder="ჩაწერე კომენტარი" class="dark:bg-gray-900 dark:text-gray-400 text-xs overflow-hidden shadow-sm sm:rounded-lg w-full h-24 border border-slate-700 p-2" rows="4"></textarea>
            <br>
            <center><p id="edit_vlans_vsol_comment_empty_" class="text-red-400 text-sm"></p></center>
        </div>

    </x-bladewind.modal>

    <x-bladewind.modal
        size="medium"
        center_action_buttons="true"
        type="warning"
        title="Confirm Edit Vlan"
        :ok_button_action="'Port_hsgq_VlanEdit()'"
        close_after_action="false"
        name="vlanEdit-confirm-modal_hsgq"
        show_close_icon="true"
        blur_backdrop="false"
        ok_button_label="Yes, Install"
        cancel_button_label="Don't Install">

        Are you sure you want to edit vlan in onu ? This action cannot be undone

        <br><br>
        <div class="flex flex-col items-center justify-center">

            <textarea id="additional_info_edit_vlans_hsgq" placeholder="ჩაწერე კომენტარი" class="dark:bg-gray-900 dark:text-gray-400 text-xs overflow-hidden shadow-sm sm:rounded-lg w-full h-24 border border-slate-700 p-2" rows="4"></textarea>
            <br>
            <center><p id="edit_vlans_hsgq_comment_empty_" class="text-red-400 text-sm"></p></center>
        </div>

    </x-bladewind.modal>

     
     
    <x-bladewind.modal
        size="medium"
        center_action_buttons="true"
        type="warning"
        title="Confirm Edit Vlan"
        :ok_button_action="'PortVlanEdit()'"
        close_after_action="false"
        name="vlanEdit-confirm-modal"
        show_close_icon="true"
        blur_backdrop="false"
        ok_button_label="Yes, Install"
        cancel_button_label="Don't Install">

        Are you sure you want to edit vlan in onu ? This action cannot be undone

        <br><br>
        <div class="flex flex-col items-center justify-center">

            <textarea id="additional_info_edit_vlans" placeholder="ჩაწერე კომენტარი" class="dark:bg-gray-900 dark:text-gray-400 text-xs overflow-hidden shadow-sm sm:rounded-lg w-full h-24 border border-slate-700 p-2" rows="4"></textarea>
            <br>
            <center><p id="edit_vlans_comment_empty_" class="text-red-400 text-sm"></p></center>
        </div>

    </x-bladewind.modal>

    <x-bladewind.modal
        size="omg"                
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

    <x-bladewind.modal
        size="medium"
        center_action_buttons="true"
        type="warning"
        title="Confirm Airsoft Install"
        :ok_button_action="'FinishInstall()'"
        close_after_action="false"
        name="airsoft-confirm"
        show_close_icon="true"
        blur_backdrop="false"
        ok_button_label="Yes, Install"
        cancel_button_label="Don't Install">

        Are you sure you want to install client in airsoft ? This action cannot be undone

        <br> <br>
        <div class="flex flex-col items-center justify-center">

            <textarea id="additional_info_make_static" placeholder="ჩაწერე კომენტარი" class="dark:bg-gray-900 dark:text-gray-400 text-xs overflow-hidden shadow-sm sm:rounded-lg w-full h-24 border border-slate-700 p-2" rows="4"></textarea>
            <br>
            <center><p id="make_static_comment_empty_" class="text-red-400 text-sm"></p></center>
        </div>

    </x-bladewind.modal>
 
 
    <input id="vlanEdit_index" value="" hidden />
    <input id="vlanEdit_portindex" value="" hidden />
    <input id="vlanEdit_user" value="" hidden />
    <input id="vlanEdit_ip" value="" hidden />
    <input id="vlanEdit_read" value="" hidden />
    <input id="vlanEdit_write" value="" hidden />
    <input id="vlanEdit_username" value="" hidden />
    <input id="vlanEdit_password" value="" hidden />



    <input id="crm_user_first_id" value="" hidden />

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
    
</x-app-layout>


 


<script>     
     

    $(function() {
        $("#PRTG").draggable({handle: "#header_prtg"}).resizable({handles: 'e, w'});
        $("#airsoft").draggable({handle: "#header_airsoft"}).resizable({handles: 'e, w'});
        $("#olt").draggable({handle: "#header_olt"}).resizable({handles: 'e, w'});
        $("#mikrotik").draggable({handle: "#header_mikrotik"}).resizable({handles: 'e, w'});
    });


    function start()
    {

        var Notification_user_id = document.getElementById('Notification_user_id');
        var Notification_task_id = document.getElementById('Notification_task_id');
        let NotTaskId = '', NotClientId = '';
        if (Notification_task_id && Notification_user_id) 
        {
            NotClientId = Notification_user_id.value;
            NotTaskId   = Notification_task_id.value;
        }


        var inputValue = document.getElementById('searchByInt').value;
        if (inputValue === '' || isNaN(inputValue))
        {
            var alertDiv = document.getElementById('errorAlert');
            alertDiv.hidden = false;
            setTimeout(function() {
                alertDiv.hidden = true;
            }, 2000);
        }
        else
        {
            localStorage.setItem('Notification_user_id', NotClientId);
            localStorage.setItem('Notification_task_id', NotTaskId);
            localStorage.setItem('data', inputValue);
            location.reload();
        }

    }

    window.onload = function()
    {
        let savedData = localStorage.getItem('data');
        if (savedData)
        {
            let _user_id = localStorage.getItem('Notification_user_id');
            let _task_id = localStorage.getItem('Notification_task_id');
           
            if (_user_id && _task_id)
            {
                let currentUrl =  window.location.protocol + '//' + window.location.host+ '/dashboard?id=' + _user_id; 
                showNotification('Task Monitor', `შენს მიერ აბონენტზე  <span style="color:#818cf8;"><a target="_blank" href="${currentUrl}" style="text-decoration: underline;text-underline-offset: 4px;">${_user_id}</a></span>  გაფორმებულ  დავალებაში თასქის ნომერი  <span style="color:#f87171;">${_task_id}</span>  ცვლილებებია შეამოწმე და თუ დასახურია დახურე `,'warning', 60);
            }
           
            document.getElementById('searchByInt').value = savedData;
            localStorage.removeItem('data');
            client_get_all_stat(savedData);
        }
        else 
        {   
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.has('id'))
            {
                let ab_nom_from_url = urlParams.get('id'); 
                if (ab_nom_from_url)
                { 
                    let _user_id = localStorage.getItem('Notification_user_id');
                    let _task_id = localStorage.getItem('Notification_task_id');
                
                    if (_user_id && _task_id)
                    {
                        let currentUrl =  window.location.protocol + '//' + window.location.host+ '/dashboard?id=' + _user_id; 
                        showNotification('Task Monitor', `შენს მიერ აბონენტზე  <span style="color:#818cf8;"><a target="_blank" href="${currentUrl}" style="text-decoration: underline;text-underline-offset: 4px;">${_user_id}</a></span>  გაფორმებულ  დავალებაში თასქის ნომერი  <span style="color:#f87171;">${_task_id}</span>  ცვლილებებია შეამოწმე და თუ დასახურია დახურე `,'warning', 60);
                    }
                    

                    document.getElementById('searchByInt').value = ab_nom_from_url;
                    client_get_all_stat(ab_nom_from_url);
                }
            }
        }  
    };

    function coordinatesModalStart()
    {
        showModal('coordinatesAdd');

        // $(document).ready(function () 
        // {    
        //     wialon.core.Session.getInstance().initSession("https://local.geogps.ge");  
        //     wialon.core.Session.getInstance().loginToken("32899d2d2d756771c2a0166c1493ab2c7B00BC0AD8F0005E07E792AF67F1C09B8707A399", "",  
        //     function (code) 
        //     {   
        //         if (code){ alert("GEO GPS ERROR "+wialon.core.Errors.getErrorText(code)); return; }
        //         init('#units_dashboard');
        //         $(document).ready(function() 
        //         {
        //             $('#units_dashboard').select2({placeholder: "მოზებნე მანქანა"});      
        //         });
        //     });
        // });        

        $.ajax({
            url: "/new-gps",
            type: "GET",
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            dataType: "json",
            success: function(result)
            {  
                if(result.error)
                {
                    showNotification('Coordinates Error', result.error, 'warning',3);
                }
                else
                {
                    console.log(result);

                    var new_gps_options = '';
                    for (const key in result) 
                    {
                        if (key.startsWith('item_'))                     
                        {
                            new_gps_options += `<option class="font-semibold text-md" value="${result[key].coordinates}">${result[key].name}</option>`;
                        }
                    }

                    document.getElementById("new_gps").innerHTML = `
                        <select  id="new_gps"   style="width:100% !important;height:100% !important;" class="w-full mx-auto sm:w-1/2 dark:bg-gray-900 text-center dark:text-gray-400 text-xs overflow-hidden  sm:rounded-lg   h-9  border border-slate-700 uppercase tracking-widest" required> 
                            <option value="0" class="dark:bg-gray-900 text-center dark:text-gray-400 text-xs overflow-hidden shadow-sm sm:rounded-lg w-full h-8 border border-slate-700 uppercase tracking-widest">NEW GPS</option>
                            ${new_gps_options}    
                        </select>   
                    `;

                    $(document).ready(function() 
                    {
                        $('#new_gps').select2({placeholder: "მოზებნე მანქანა"});      
                    });
 
                }
            },
            error: function (xhr, status, error) 
            {
                if (xhr.status === 401) 
                {
                    showNotification('Coordinates Error', 'You dont have permissions to perform this action','warning',3);
                }
                else
                {
                    showNotification('Coordinates Error',error,'warning',3);
                }      
            }
        });

    }

    function coordinatesAdd()
    {   
        let coord   = document.getElementById("crm_coord_input").value;
        let client  = document.getElementById('searchByInt').value;
        let comment = document.getElementById('additional_info_coordinates').value;
         
        // if($('#units_dashboard').val() !== '0')
        // {
        //     coord = $('#units_dashboard').val();
        // }
        if($('#new_gps').val() !== '0')
        {
            coord = $('#new_gps').val();
        }

        if (coord.length === 0) 
        {
            document.getElementById("gps_empty_").innerText = 'აირჩიე ან ჩაწერე კოორდინატები';
        }
        else
        {
            document.getElementById("gps_empty_").innerText = '';
        }


        if (comment.length === 0) 
        {
            document.getElementById("gps_comment_empty_").innerText = 'ჩაწერე კომენტარი'; 
        }
        else
        {
            document.getElementById("gps_comment_empty_").innerText = '';
        }
         
        $.ajax({
            url: "/GPS-ADD-CRM",
            type: "GET",
            data:{'client':client,'coordinates':coord,'comment':comment},
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            dataType: "json",
            success: function(result)
            {  
                if (result == true)
                {
                    showNotification('Coordinates', 'კოორდინატები შენახულია ','info',3);
                    hideModal('coordinatesAdd');
                }
                else
                {
                    showNotification('Coordinates Error', result.error ,'warning',3);
                }
            },
            error: function (xhr, status, error) 
            {
                if (xhr.status === 401) 
                {
                    showNotification('Coordinates Error', 'You dont have permissions to perform this action','warning',3);
                }
                else
                {
                    showNotification('Coordinates Error',error,'warning',3);
                }      
            }
        });
   
    }

    function FilterStart(savedData)
    {
        //const currentUrl = window.location.href;
        const currentUrl = window.location.origin + window.location.pathname;
        window.open(`${currentUrl}?id=${savedData}`, '_blank');
    }

    function client_get_all_stat(inputValue)
    {

        document.getElementById("Mikrotik_Data").innerHTML = `
            <svg  width="30" height="30" fill="#5c6bc0" class="mr-2 animate-spin" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg">
                <path d="M526 1394q0 53-37.5 90.5t-90.5 37.5q-52 0-90-38t-38-90q0-53 37.5-90.5t90.5-37.5 90.5 37.5 37.5 90.5zm498 206q0 53-37.5 90.5t-90.5 37.5-90.5-37.5-37.5-90.5 37.5-90.5 90.5-37.5 90.5 37.5 37.5 90.5zm-704-704q0 53-37.5 90.5t-90.5 37.5-90.5-37.5-37.5-90.5 37.5-90.5 90.5-37.5 90.5 37.5 37.5 90.5zm1202 498q0 52-38 90t-90 38q-53 0-90.5-37.5t-37.5-90.5 37.5-90.5 90.5-37.5 90.5 37.5 37.5 90.5zm-964-996q0 66-47 113t-113 47-113-47-47-113 47-113 113-47 113 47 47 113zm1170 498q0 53-37.5 90.5t-90.5 37.5-90.5-37.5-37.5-90.5 37.5-90.5 90.5-37.5 90.5 37.5 37.5 90.5zm-640-704q0 80-56 136t-136 56-136-56-56-136 56-136 136-56 136 56 56 136zm530 206q0 93-66 158.5t-158 65.5q-93 0-158.5-65.5t-65.5-158.5q0-92 65.5-158t158.5-66q92 0 158 66t66 158z">
                </path>
            </svg>
            `;

        document.getElementById("OLT_DATA").innerHTML = `
            <svg  width="30" height="30" fill="#5c6bc0" class="mr-2 animate-spin" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg">
                <path d="M526 1394q0 53-37.5 90.5t-90.5 37.5q-52 0-90-38t-38-90q0-53 37.5-90.5t90.5-37.5 90.5 37.5 37.5 90.5zm498 206q0 53-37.5 90.5t-90.5 37.5-90.5-37.5-37.5-90.5 37.5-90.5 90.5-37.5 90.5 37.5 37.5 90.5zm-704-704q0 53-37.5 90.5t-90.5 37.5-90.5-37.5-37.5-90.5 37.5-90.5 90.5-37.5 90.5 37.5 37.5 90.5zm1202 498q0 52-38 90t-90 38q-53 0-90.5-37.5t-37.5-90.5 37.5-90.5 90.5-37.5 90.5 37.5 37.5 90.5zm-964-996q0 66-47 113t-113 47-113-47-47-113 47-113 113-47 113 47 47 113zm1170 498q0 53-37.5 90.5t-90.5 37.5-90.5-37.5-37.5-90.5 37.5-90.5 90.5-37.5 90.5 37.5 37.5 90.5zm-640-704q0 80-56 136t-136 56-136-56-56-136 56-136 136-56 136 56 56 136zm530 206q0 93-66 158.5t-158 65.5q-93 0-158.5-65.5t-65.5-158.5q0-92 65.5-158t158.5-66q92 0 158 66t66 158z">
                </path>
            </svg>
            `;
        document.getElementById("OLT_LINKS").innerHTML = `
            <svg  width="30" height="30" fill="#5c6bc0" class="mr-2 animate-spin" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg">
                <path d="M526 1394q0 53-37.5 90.5t-90.5 37.5q-52 0-90-38t-38-90q0-53 37.5-90.5t90.5-37.5 90.5 37.5 37.5 90.5zm498 206q0 53-37.5 90.5t-90.5 37.5-90.5-37.5-37.5-90.5 37.5-90.5 90.5-37.5 90.5 37.5 37.5 90.5zm-704-704q0 53-37.5 90.5t-90.5 37.5-90.5-37.5-37.5-90.5 37.5-90.5 90.5-37.5 90.5 37.5 37.5 90.5zm1202 498q0 52-38 90t-90 38q-53 0-90.5-37.5t-37.5-90.5 37.5-90.5 90.5-37.5 90.5 37.5 37.5 90.5zm-964-996q0 66-47 113t-113 47-113-47-47-113 47-113 113-47 113 47 47 113zm1170 498q0 53-37.5 90.5t-90.5 37.5-90.5-37.5-37.5-90.5 37.5-90.5 90.5-37.5 90.5 37.5 37.5 90.5zm-640-704q0 80-56 136t-136 56-136-56-56-136 56-136 136-56 136 56 56 136zm530 206q0 93-66 158.5t-158 65.5q-93 0-158.5-65.5t-65.5-158.5q0-92 65.5-158t158.5-66q92 0 158 66t66 158z">
                </path>
            </svg>
            `;
        document.getElementById("OLT_MACS").innerHTML = `
            <svg  width="30" height="30" fill="#5c6bc0" class="mr-2 animate-spin" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg">
                <path d="M526 1394q0 53-37.5 90.5t-90.5 37.5q-52 0-90-38t-38-90q0-53 37.5-90.5t90.5-37.5 90.5 37.5 37.5 90.5zm498 206q0 53-37.5 90.5t-90.5 37.5-90.5-37.5-37.5-90.5 37.5-90.5 90.5-37.5 90.5 37.5 37.5 90.5zm-704-704q0 53-37.5 90.5t-90.5 37.5-90.5-37.5-37.5-90.5 37.5-90.5 90.5-37.5 90.5 37.5 37.5 90.5zm1202 498q0 52-38 90t-90 38q-53 0-90.5-37.5t-37.5-90.5 37.5-90.5 90.5-37.5 90.5 37.5 37.5 90.5zm-964-996q0 66-47 113t-113 47-113-47-47-113 47-113 113-47 113 47 47 113zm1170 498q0 53-37.5 90.5t-90.5 37.5-90.5-37.5-37.5-90.5 37.5-90.5 90.5-37.5 90.5 37.5 37.5 90.5zm-640-704q0 80-56 136t-136 56-136-56-56-136 56-136 136-56 136 56 56 136zm530 206q0 93-66 158.5t-158 65.5q-93 0-158.5-65.5t-65.5-158.5q0-92 65.5-158t158.5-66q92 0 158 66t66 158z">
                </path>
            </svg>
            `;        
        airsoft(inputValue);
    }

 
    // create the chart
    var Download = Highcharts.chart('Download', {
        chart: {
            type: 'bar',
            height: 120,
        },
        title: {
            text: 'Download',
            align: 'center',
            margin: 0,
            style: {color: '#8b92a9' , fontSize: '12px'}
        },
        exporting: {
            enabled: false
        },
        subtitle: {
            text: '',
            verticalAlign: "bottom",
        },
        credits: false,
        legend: false,
        tooltip: false,
        plotOptions: {
            bar: {
            borderWidth: 0,
            animation: true,
            borderRadius: 3
            }
        },
        xAxis: {
            visible: false
        },
        yAxis: {
            visible: true,
            min: 0,
            max: 100,
            title: {
            text: null
            },
            gridLineWidth: 0,
            labels: {
            y: -2
            }
        },
        series:
        [{
            name: "Fill",
            data: [100],
            color: "gray",
            opacity:"0.2",
            grouping: false
            },
        {
        name: "Percentage",
        data: [0],
        color: "#5a00ff",
        dataLabels:
        {
            enabled: true,
            inside: true,
            align: 'center',
            format: '{point.y} Mbps',
            style:
            {
            color: 'white',
            textOutline: false,
            }
        }
        }]
    });


    // create the chart
    var Upload = Highcharts.chart('Upload', {
    chart: {
        type: 'bar',
        height: 120
    },
    title: {
        text: 'Upload',
        align: 'center',
        margin: 0,
        style: {color: '#8b92a9' , fontSize: '12px'}
    },
    exporting: {
        enabled: false
    },
    subtitle: {
        text: '',
        verticalAlign: "bottom",
    },
    credits: false,
    legend: false,
    tooltip: false,
    plotOptions: {
        bar: {
        borderWidth: 0,
        //animation: false,
        //colorByPoint: true // Enable color by point
        borderRadius: 3
        }
    },
    xAxis: {
        visible: false
    },
    yAxis: {
        visible: true,
        min: 0,
        max: 100,
        title: {
        text: null
        },
        gridLineWidth: 0,
        labels: {
        y: -2
        }
    },
    series:
                [{
                name: "Fill",
                data: [100],
                color: "gray",
                opacity:"0.2",
                grouping: false
                },
                {
                name: "Percentage",
                data: [0],
                color: "#5a00ff",
                dataLabels: {
                    enabled: true,
                    inside: true,
                    align: 'center',
                    format: '{point.y} Mbps',
                    style: {
                    color: 'white',
                    textOutline: false,
                    }
                }
                }]
    });



    var vlan = Highcharts.chart('vlan', {
                    chart: {
                      type: 'bar',
                      height: 120
                    },
                    title: {
                      text: 'Vlan ',
                      align: 'center',
                      margin: 0,
                      style: {color: '#8b92a9' , fontSize: '12px' }
                    },
                    exporting: {
                      enabled: false
                    },
                    subtitle: {
                      text: '',
                      verticalAlign: "bottom",
                    },
                    credits: false,
                    legend: false,
                    tooltip: false,
                    plotOptions: {
                      bar: {
                        borderWidth: 0,
                        animation: true,
                        borderRadius: 3
                      }
                    },
                    xAxis: {
                      visible: false
                    },
                    yAxis: {
                      visible: true,
                      min: 0,
                      max: 1000,
                      title: {
                        text: null
                      },
                      gridLineWidth: 0,
                      labels: {
                        y: -2
                      }
                    },
                    series:
                    [{
	                    name: "Fill",
	                    data: [1000],
	                    color: "gray",
	                    opacity:"0.2",
	                    grouping: false
                  	},

                  {
                    name: "Percentage",
                    data: [0],
                    color: "#5a00ff",
                    dataLabels:
                    {
                      enabled: true,
                      inside: true,
                      align: 'center',
                      format: '{point.y} Mbps',
                      style:
                      {
                        color: 'white',
                        textOutline: false,
                      }
                    }
                  }]
                  });

</script>
