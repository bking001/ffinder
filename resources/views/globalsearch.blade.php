<x-app-layout>
 

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
 
                    
                <div class="max-w-xl mx-auto">
                    <div id="errorAlert" class="px-4 py-3 text-center rounded relative items-center" hidden role="alert" style="color: #084298;background: #cfe2ff;border-color: #b6d4fe;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="currentColor" class="bi bi-exclamation-triangle-fill inline-block align-middle me-2" viewBox="0 0 16 16" role="img" aria-label="Warning:">
                            <path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"></path>
                        </svg>
                        <strong class="font-bold text-sm inline-block align-middle">მოხდა შეცდომა!</strong>
                        <span class="block sm:inline text-sm">ჩაწერე ონუს სერიული ან მაკ მისამართი</span>
                    </div> <br>

                    <div id="errorDescr" class="px-4 py-3 text-center rounded relative items-center" hidden role="alert" style="color: #084298;background: #cfe2ff;border-color: #b6d4fe;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="currentColor" class="bi bi-exclamation-triangle-fill inline-block align-middle me-2" viewBox="0 0 16 16" role="img" aria-label="Warning:">
                            <path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"></path>
                        </svg>
                        <strong class="font-bold text-sm inline-block align-middle">მოხდა შეცდომა!</strong>
                        <span class="block sm:inline text-sm">ჩაწერე ონუს დესქრიფშენი</span>
                    </div> <br>
                </div>


                    <div class="flex h-10" style="justify-content: center;">

                        <div class="relative ml-2 w-full">
                            <input class="bw-input peer searchByGlobal dark:bg-gray-800 dark:text-gray-400 text-center text-xs overflow-hidden shadow-sm sm:rounded-lg  h-8 border border-slate-700 pl-8" style="font-weight: 600;" type="text" id="searchByGlobal" name="searchByGlobal"   autocomplete="off" placeholder="ონუს მაკი ან სერიული"   required>
                            <svg class="absolute left-2 top-2 w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"></path>
                            </svg>
                        </div>

                        <button onclick="globalsearch()" id="gsearchbutton" type="submit" class="inline-flex items-center px-4 py-2 h-8 ml-2 disabled:opacity-50 disabled:pointer-events-none  bg-gray-900 dark:bg-gray-900 border  border-indigo-400 rounded-md font-semibold text-xs text-white dark:text-indigo-400 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-indigo-400 dark:hover:text-gray-900  dark:active:text-indigo-400">
                            SEARCH
                        </button>

                        <div class="relative ml-2 w-1/2">
                            <input class="bw-input peer searchByGlobal dark:bg-gray-800 dark:text-gray-400 text-center text-xs overflow-hidden shadow-sm sm:rounded-lg  h-8 border border-slate-700 pl-8" style="font-weight: 600;" type="text" id="searchByDescr" name="searchByDescr"   autocomplete="off" placeholder="ონუს დესქრიფშენი"   required>
                            <svg class="absolute left-2 top-2 w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"></path>
                            </svg>
                        </div>

                        <button onclick="globalDescr()" id="gDescrsearchbutton" type="submit" class="inline-flex items-center px-4 py-2 h-8 ml-2 disabled:opacity-50 disabled:pointer-events-none  bg-gray-900 dark:bg-gray-900 border  border-indigo-400 rounded-md font-semibold text-xs text-white dark:text-indigo-400 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-indigo-400 dark:hover:text-gray-900  dark:active:text-indigo-400">
                            DESCRIPTION
                        </button>
                    </div>
        </div>

        <center><br>
                    <div class="mx-auto sm:px-6 lg:px-8 overflow-x-hidden bg-gray-300/50 dark:bg-gray-800/50 rounded-lg shadow-lg" id="data_div" style="height: 50rem;max-width: 80%;">
                        <br><div id="refresh"  style="display: flex; justify-content: center; align-items: center;"></div>
                    </div>
        </center>

        <x-bladewind.notification position="top right" />
        <x-bladewind.notification />

    </div>
    
    
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
     

    <script>

        function globalDescr()
        {
            var inputValue = document.getElementById('searchByDescr').value;    console.log(inputValue.length);
            if (inputValue === '' || inputValue.length < 4)
            {
                var alertDiv = document.getElementById('errorDescr');
                alertDiv.hidden = false;
                setTimeout(function() {
                    alertDiv.hidden = true;
                }, 2000);
            }
            else
            { 
                
                localStorage.setItem('global_descr_search_data', inputValue);
                location.reload();
            }
        }

        function globalsearch()
        {
            var inputValue = document.getElementById('searchByGlobal').value;    console.log(inputValue.length);
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
                localStorage.setItem('global_search_data', inputValue);
                location.reload();
            }

        }

        window.onload = function()
        {
            let descrData = localStorage.getItem('global_descr_search_data');
            let savedData = localStorage.getItem('global_search_data');
            if (savedData)
            {
                document.getElementById('searchByGlobal').value = savedData.trim();
                localStorage.removeItem('global_search_data');
                
                startGlobalSearch(savedData); 
            }
            else if (descrData)
            {
                document.getElementById('searchByDescr').value = descrData.trim();
                localStorage.removeItem('global_descr_search_data');
                
                startDescriptionSearch(descrData); 
            }
            else 
            {   
                const urlParams = new URLSearchParams(window.location.search);
                if (urlParams.has('id'))
                {
                    let MacSn = urlParams.get('id'); 
                    if (MacSn)
                    {               
                        document.getElementById('searchByGlobal').value = MacSn;
                        startGlobalSearch(MacSn);
                    }
                }
            }
        };

        function startDescriptionSearch(descrData)
        {
                        $.ajax({
                                    url: "/FindDescription",
                                    data:{'Descr':descrData},
                                    type: "GET",
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                    },
                                    dataType: "json",
                                    success: function(data)
                                    {                         
                                        if(data.error)showNotification('Search Error', data.error,'warning',60);

                                        if (Array.isArray(data) && data.length === 0)
                                        {
                                            document.getElementById("data_div").innerHTML = '<br><center><p class="text-red-400">Not Found</p></center>'
                                        }
                                        else
                                        {
                                            for (var key in data) 
                                            {
                                                document.getElementById("data_div").innerHTML += `<br>
                                                                    <fieldset class="border border-gray-800 p-3 rounded" style="width: 100%;">
                                                                    <legend class="text-sm text-gray-600 font-semibold uppercase tracking-widest">${data[key].oltType}</legend>
                                                                        <div class="flex items-center sm:justify-center ml-4 sm:ml-0" style="width: 100%;">
                                                                            <table class="text-sm border-separate border-spacing-y-2" style="width: 100%;">
                                                                                <thead>
                                                                                <tr  class="tr-class">
                                                                                    <th class="dark:text-indigo-400"style="text-align:center;letter-spacing: .1em;text-transform: uppercase;font-size: 11px;">Server IP</th>
                                                                                    <th class="dark:text-indigo-400"style="text-align:center;letter-spacing: .1em;text-transform: uppercase;font-size: 11px;">Type</th> 
                                                                                    <th class="dark:text-indigo-400"style="text-align:center;letter-spacing: .1em;text-transform: uppercase;font-size: 11px;">Name</th>                                                                                                                                                             
                                                                                    <th class="dark:text-indigo-400"style="text-align:center;letter-spacing: .1em;text-transform: uppercase;font-size: 11px;">PonPort</th>
                                                                                    <th class="dark:text-indigo-400"style="text-align:center;letter-spacing: .1em;text-transform: uppercase;font-size: 11px;">Client</th>
                                                                                    <th class="dark:text-indigo-400"style="text-align:center;letter-spacing: .1em;text-transform: uppercase;font-size: 11px;">Mac address</th>
                                                                                    <th class="dark:text-indigo-400"style="text-align:center;letter-spacing: .1em;text-transform: uppercase;font-size: 11px;">UPDATED</th>  
                                                                                </tr>
                                                                                </thead>
                                                                                <tbody>             
                                                                                    <tr class="tr-class" style="text-align:center;">                                                                                                                                                                            
                                                                                        <td class="td-class"style="text-align:center;letter-spacing: .1em;text-transform: uppercase;font-size: 11px;">${data[key].oltAddress}</td>
                                                                                        <td class="td-class"style="text-align:center;letter-spacing: .1em;text-transform: uppercase;font-size: 11px;">${data[key].oltType}</td>
                                                                                        <td class="td-class"style="text-align:center;letter-spacing: .1em;text-transform: uppercase;font-size: 11px;">${data[key].oltName}</td>                                  
                                                                                        <td class="td-class"style="text-align:center;letter-spacing: .1em;text-transform: uppercase;font-size: 11px;">${data[key].ponPort}</td>
                                                                                        <td class="td-class"style="text-align:center;letter-spacing: .1em;text-transform: uppercase;font-size: 11px;">${data[key].onuDescr}</td>
                                                                                        <td class="td-class"style="text-align:center;letter-spacing: .1em;text-transform: uppercase;font-size: 11px;">${data[key].onuMac}</td>
                                                                                        <td class="td-class"style="text-align:center;letter-spacing: .1em;text-transform: uppercase;font-size: 11px;">${data[key].last_update}</td>                                                
                                                                                    </tr>
                                                                                </tbody>
                                                                            </table>
                                                                        </div>
                                                                    </fieldset>
                                                                    `;
                                            }
                                        }
   
                                    },
                                    error: function (xhr, status, error) 
                                    { 
                                        showNotification('Search Error', xhr.status +'  '+ error ,'error',60);
                                    }
                                });
        }

        function startGlobalSearch(macSn)
        {   
            let LoaderHide = false;
            document.getElementById("refresh").innerHTML = `
            <svg  width="30" height="30" fill="#5c6bc0" class="mr-2 animate-spin" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg">
                <path d="M526 1394q0 53-37.5 90.5t-90.5 37.5q-52 0-90-38t-38-90q0-53 37.5-90.5t90.5-37.5 90.5 37.5 37.5 90.5zm498 206q0 53-37.5 90.5t-90.5 37.5-90.5-37.5-37.5-90.5 37.5-90.5 90.5-37.5 90.5 37.5 37.5 90.5zm-704-704q0 53-37.5 90.5t-90.5 37.5-90.5-37.5-37.5-90.5 37.5-90.5 90.5-37.5 90.5 37.5 37.5 90.5zm1202 498q0 52-38 90t-90 38q-53 0-90.5-37.5t-37.5-90.5 37.5-90.5 90.5-37.5 90.5 37.5 37.5 90.5zm-964-996q0 66-47 113t-113 47-113-47-47-113 47-113 113-47 113 47 47 113zm1170 498q0 53-37.5 90.5t-90.5 37.5-90.5-37.5-37.5-90.5 37.5-90.5 90.5-37.5 90.5 37.5 37.5 90.5zm-640-704q0 80-56 136t-136 56-136-56-56-136 56-136 136-56 136 56 56 136zm530 206q0 93-66 158.5t-158 65.5q-93 0-158.5-65.5t-65.5-158.5q0-92 65.5-158t158.5-66q92 0 158 66t66 158z">
                </path>
            </svg>
            `;
            $("#gsearchbutton").prop("disabled", true);
            $.ajax({
            url: "/OLT-LIST",
            data:{'macSn':macSn},
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
                    $("#gsearchbutton").removeAttr("disabled");
                    showNotification('Search Error', result.error,'warning',3);
                    if(!LoaderHide)document.getElementById("refresh").innerHTML = '';
                }
                else
                {
                    var completedRequests = 0; 
                    if(Array.isArray(result)) 
                    {                                
                        $.each(result, function (index, item) 
                        {          
                            $.ajax({
                                    url: "/FindMacSN",
                                    data:{'ip':item,'macSN':macSn},
                                    type: "GET",
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                    },
                                    dataType: "json",
                                    success: function(data)
                                    {                         
                                        if(data.error)showNotification('Search Error', data.error,'warning',60);
                                    
                                        if (data.address && data.address.length > 5) 
                                        {                                    
                                                $("#gsearchbutton").removeAttr("disabled");    
                                                
                                                if(data.type == 'BDCOM')
                                                {

                                                    var tableRow = '';
                                                    for (var key in data) 
                                                    {
                                                        if (key.startsWith('OnuList_')) 
                                                        {
                                                            let OnuOperateStatusStyle = '';
                                                            if(data[key].Onu_Status.includes('authenticated'))
                                                            {
                                                                OnuOperateStatusStyle = '<td class="td-class" style="text-align:center;color: #efc776;letter-spacing: .1em;text-transform: uppercase;font-size: 11px;">'+data[key].Onu_Status+'</td>';
                                                            }
                                                            else
                                                            {
                                                                OnuOperateStatusStyle = '<td class="td-class" style="text-align:center;letter-spacing: .1em;text-transform: uppercase;font-size: 11px;">'+data[key].Onu_Status+'</td>';
                                                            }           

                                                            let  Onu_RXStyle = '';
                                                            if(data[key].Dbm < -27)
                                                            {
                                                                Onu_RXStyle = '<td class="td-class" style="text-align:center;color: #efc776;letter-spacing: .1em;text-transform: uppercase;font-size: 11px;"><span class="inline-flex items-center rounded-md bg-amber-400 dark:bg-amber-400/20 px-2 py-1 text-xs font-medium text-amber-400 ring-1 ring-inset ring-amber-600/20"  style="background:rgba(251, 191, 36, .1);">'+data[key].Dbm+'</span></td>';
                                                            }
                                                            else
                                                            {
                                                                Onu_RXStyle = '<td class="td-class" style="text-align:center;letter-spacing: .1em;text-transform: uppercase;font-size: 11px;">'+data[key].Dbm+'</td>';
                                                            }


                                                            let opStatus = ''; let Uptime = '';let LanMac = '';
                                                            if(data[key].OperStatus.includes('up'))
                                                            {
                                                                opStatus = '<td class="td-class"style="text-align:center;color:rgb(114, 200, 148);letter-spacing: .1em;text-transform: uppercase;font-size: 11px;"><span  class="inline-flex items-center rounded-md bg-green-400 dark:bg-green-400/20 px-2 py-1 text-xs font-medium text-green-400 ring-1 ring-inset ring-green-600/20"  style="background:rgba(74, 222, 128, .1);">Connected</span></td>';
                                                                Uptime = `<td class="td-class"style="text-align:center;letter-spacing: .1em;text-transform: uppercase;font-size: 11px;">${data[key].Uptime}</td>`;

                                                                LanMac = `
                                                                        <td class="td-class"style="text-align:center;letter-spacing: .1em;text-transform: uppercase;font-size: 11px;">
                                                                            <button onclick="BDCOM_LAN_MACS('${item}','${data[key].Ifindex}')" class="inline-flex items-center px-4 py-2 h-8 ml-2  bg-gray-900 dark:bg-gray-900 border  border-indigo-400 rounded-md font-semibold text-xs text-white dark:text-indigo-400 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-indigo-400 dark:hover:text-gray-900  dark:active:text-indigo-400">
                                                                                LAN MACS
                                                                            </button>  
                                                                        </td>
                                                                    `;
                                                            }
                                                            else
                                                            {
                                                                opStatus    = '<td class="td-class"style="text-align:center;color: #ef7676;letter-spacing: .1em;text-transform: uppercase;font-size: 11px;"><span class="inline-flex items-center rounded-md bg-red-400 dark:bg-red-400/20 px-2 py-1 text-xs font-medium text-red-400 ring-1 ring-inset ring-red-600/20" style="background:rgba(248, 113, 113, .1);">Down</span></td>';
                                                                Onu_RXStyle = '<td class="td-class"style="text-align:center;color: #ef7676;letter-spacing: .1em;text-transform: uppercase;font-size: 11px;">-</td>';
                                                                Uptime = `<td class="td-class"style="text-align:center;color: #ef7676;letter-spacing: .1em;text-transform: uppercase;font-size: 11px;">${data[key].Downtime}</td>`;

                                                                LanMac = `<td class="td-class"style="text-align:center;color: #ef7676;letter-spacing: .1em;text-transform: uppercase;font-size: 11px;">-</td>`;
                                                            }
                                                        
                                                            

                                                            let terminal = `
                                                                <td class="td-class"style="text-align:center;letter-spacing: .1em;text-transform: uppercase;font-size: 11px;">
                                                                    <button onclick="openPopup('https://finder.airlink.ge:2222/ssh/host/${data.address}', '${data.Worker}', '${data.userIp}','${data.sshUser}', '${data.sshPass}','BDCOM'); return false;"  class="inline-flex items-center px-4 py-2 h-8 ml-2  bg-gray-900 dark:bg-gray-900 border  border-indigo-400 rounded-md font-semibold text-xs text-white dark:text-indigo-400 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-indigo-400 dark:hover:text-gray-900  dark:active:text-indigo-400">
                                                                        TERMINAL
                                                                    </button>  
                                                                </td>
                                                            `;

                                                            document.getElementById("data_div").innerHTML += `<br>
                                                                <fieldset class="border border-gray-800 p-3 rounded" style="width: 100%;">
                                                                <legend class="text-sm text-gray-600 font-semibold uppercase tracking-widest">${data.type}</legend>
                                                                    <div class="flex items-center sm:justify-center ml-4 sm:ml-0" style="width: 100%;">
                                                                        <table class="text-sm border-separate border-spacing-y-2" style="width: 100%;">
                                                                            <thead>
                                                                            <tr  class="tr-class">
                                                                                <th class="dark:text-indigo-400"style="text-align:center;letter-spacing: .1em;text-transform: uppercase;font-size: 11px;">Server IP</th>
                                                                                <th class="dark:text-indigo-400"style="text-align:center;letter-spacing: .1em;text-transform: uppercase;font-size: 11px;">Name</th>
                                                                                <th class="dark:text-indigo-400"style="text-align:center;letter-spacing: .1em;text-transform: uppercase;font-size: 11px;">PonPort</th>
                                                                                <th class="dark:text-indigo-400"style="text-align:center;letter-spacing: .1em;text-transform: uppercase;font-size: 11px;">Client</th>
                                                                                <th class="dark:text-indigo-400"style="text-align:center;letter-spacing: .1em;text-transform: uppercase;font-size: 11px;">Type</th>
                                                                                <th class="dark:text-indigo-400"style="text-align:center;letter-spacing: .1em;text-transform: uppercase;font-size: 11px;">Mac address</th>
                                                                                <th class="dark:text-indigo-400"style="text-align:center;letter-spacing: .1em;text-transform: uppercase;font-size: 11px;">Onu Status</th>
                                                                                <th class="dark:text-indigo-400"style="text-align:center;letter-spacing: .1em;text-transform: uppercase;font-size: 11px;">Configure</th>
                                                                                <th class="dark:text-indigo-400"style="text-align:center;letter-spacing: .1em;text-transform: uppercase;font-size: 11px;">Reason</th>
                                                                                <th class="dark:text-indigo-400"style="text-align:center;letter-spacing: .1em;text-transform: uppercase;font-size: 11px;">Dbm</th>
                                                                                <th class="dark:text-indigo-400"style="text-align:center;letter-spacing: .1em;text-transform: uppercase;font-size: 11px;">UPTIME / DOWNTIME</th>  
                                                                                <th class="dark:text-indigo-400"style="text-align:center;letter-spacing: .1em;text-transform: uppercase;font-size: 11px;">LAN MACS</th> 
                                                                                <th class="dark:text-indigo-400"style="text-align:center;letter-spacing: .1em;text-transform: uppercase;font-size: 11px;">TERMINAL</th>
                                                                            </tr>
                                                                            </thead>
                                                                            <tbody>             
                                                                                <tr class="tr-class" style="text-align:center;">                                                                      
                                                                                <td class="td-class"style="text-align:center;letter-spacing: .1em;text-transform: uppercase;font-size: 11px;">${data.address}</td>
                                                                                <td class="td-class"style="text-align:center;letter-spacing: .1em;text-transform: uppercase;font-size: 11px;">${data.ServerName}</td>
                                                                                <td class="td-class"style="text-align:center;letter-spacing: .1em;text-transform: uppercase;font-size: 11px;">${data[key].PonPort}</td>
                                                                                <td class="td-class"style="text-align:center;letter-spacing: .1em;text-transform: uppercase;font-size: 11px;">${data[key].Descr}</td>
                                                                                <td class="td-class"style="text-align:center;letter-spacing: .1em;text-transform: uppercase;font-size: 11px;">${data[key].OnyType}</td>
                                                                                <td class="td-class"style="text-align:center;letter-spacing: .1em;text-transform: uppercase;font-size: 11px;">${data[key].Mac}</td>
                                                                                ${opStatus}
                                                                                ${OnuOperateStatusStyle}
                                                                                <td class="td-class"style="text-align:center;letter-spacing: .1em;text-transform: uppercase;font-size: 11px;">${data[key].reason}</td>
                                                                                ${Onu_RXStyle}
                                                                                ${Uptime} 
                                                                                ${LanMac}
                                                                                ${terminal}
                                                                                </tr>
                                                                            </tbody>
                                                                        </table>
                                                                    </div>
                                                                </fieldset>
                                                                `;
                                                    
                                                        }
                                                    }
                                                    LoaderHide = true;   
                                                }
                                                if(data.type == 'ZTE')
                                                {
                                                    var tableRow = '';
                                                    for (var key in data) 
                                                    {
                                                        if (key.startsWith('OnuList_')) 
                                                        {

                                                            let  Onu_RXStyle = ''; 
                                                            if(data[key].Dbm < -27)
                                                            {
                                                                Onu_RXStyle = '<td class="td-class" style="text-align:center;color: #efc776;letter-spacing: .1em;text-transform: uppercase;font-size: 11px;"><span class="inline-flex items-center rounded-md bg-amber-400 dark:bg-amber-400/20 px-2 py-1 text-xs font-medium text-amber-400 ring-1 ring-inset ring-amber-600/20"  style="background:rgba(251, 191, 36, .1);">'+data[key].Dbm+'</span></td>';
                                                            }
                                                            else
                                                            {
                                                                Onu_RXStyle = '<td class="td-class" style="text-align:center;letter-spacing: .1em;text-transform: uppercase;font-size: 11px;">'+data[key].Dbm+'</td>';
                                                            }

                                                            let Uptime = '';
                                                            let opStatus = '';
                                                            let LanMac = '';
                                                            if(data[key].OperStatus.includes('Working'))
                                                            {
                                                                LanMac = `
                                                                        <td class="td-class"style="text-align:center;letter-spacing: .1em;text-transform: uppercase;font-size: 11px;">
                                                                            <button onclick="ZTE_LAN_MACS('${item}','${data[key].Ifindex}')" class="inline-flex items-center px-4 py-2 h-8 ml-2  bg-gray-900 dark:bg-gray-900 border  border-indigo-400 rounded-md font-semibold text-xs text-white dark:text-indigo-400 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-indigo-400 dark:hover:text-gray-900  dark:active:text-indigo-400">
                                                                                LAN MACS
                                                                            </button>  
                                                                        </td>
                                                                    `;
                                                                opStatus = `<td class="td-class"style="text-align:center;color:rgb(114, 200, 148);letter-spacing: .1em;text-transform: uppercase;font-size: 11px;"><span  class="inline-flex items-center rounded-md bg-green-400 dark:bg-green-400/20 px-2 py-1 text-xs font-medium text-green-400 ring-1 ring-inset ring-green-600/20"  style="background:rgba(74, 222, 128, .1);">Connected</span></td>`;
                                                                Uptime = `<td class="td-class"style="text-align:center;letter-spacing: .1em;text-transform: uppercase;font-size: 11px;">${data[key].Uptime}</td>`;
                                                            }
                                                            else
                                                            {
                                                                LanMac = `<td class="td-class"style="text-align:center;color: #ef7676;letter-spacing: .1em;text-transform: uppercase;font-size: 11px;">-</td>`;
                                                                opStatus    = `<td class="td-class"style="text-align:center;color: #ef7676;letter-spacing: .1em;text-transform: uppercase;font-size: 11px;"><span class="inline-flex items-center rounded-md bg-red-400 dark:bg-red-400/20 px-2 py-1 text-xs font-medium text-red-400 ring-1 ring-inset ring-red-600/20" style="background:rgba(248, 113, 113, .1);">Down</span></td>`;
                                                                Onu_RXStyle = '<td class="td-class"style="text-align:center;color: #ef7676;letter-spacing: .1em;text-transform: uppercase;font-size: 11px;">-</td>';
                                                                Uptime = `<td class="td-class"style="text-align:center;color:#ef7676;letter-spacing: .1em;text-transform: uppercase;font-size: 11px;">${data[key].Downtime}</td>`;
                                                            }

                                                                                                                    
                                                            let terminal = `
                                                                <td class="td-class"style="text-align:center;letter-spacing: .1em;text-transform: uppercase;font-size: 11px;">
                                                                    <button onclick="openPopup('https://finder.airlink.ge:2222/ssh/host/${data.address}', '${data.Worker}', '${data.userIp}','${data.sshUser}', '${data.sshPass}','ZTE'); return false;"  class="inline-flex items-center px-4 py-2 h-8 ml-2  bg-gray-900 dark:bg-gray-900 border  border-indigo-400 rounded-md font-semibold text-xs text-white dark:text-indigo-400 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-indigo-400 dark:hover:text-gray-900  dark:active:text-indigo-400">
                                                                        TERMINAL
                                                                    </button>  
                                                                </td>
                                                            `;
                                                             
                                                            tableRow = `
                                                                    <tr class="tr-class" style="text-align:center;">                                                                      
                                                                        <td class="td-class"style="text-align:center;letter-spacing: .1em;text-transform: uppercase;font-size: 11px;">${data.address}</td>
                                                                        <td class="td-class"style="text-align:center;letter-spacing: .1em;text-transform: uppercase;font-size: 11px;">${data.ServerName}</td>
                                                                        <td class="td-class"style="text-align:center;letter-spacing: .1em;text-transform: uppercase;font-size: 11px;">${data[key].PonPort}</td>
                                                                        <td class="td-class"style="text-align:center;letter-spacing: .1em;text-transform: uppercase;font-size: 11px;">${data[key].Descr}</td>
                                                                        <td class="td-class"style="text-align:center;letter-spacing: .1em;text-transform: uppercase;font-size: 11px;">${data[key].OnyType}</td>
                                                                        <td class="td-class"style="text-align:center;letter-spacing: .1em;text-transform: uppercase;font-size: 11px;">${data[key].Fullsn}</td>
                                                                        <td class="td-class"style="text-align:center;letter-spacing: .1em;text-transform: uppercase;font-size: 11px;">${data[key].Mac}</td>
                                                                        ${opStatus}
                                                                        <td class="td-class"style="text-align:center;letter-spacing: .1em;text-transform: uppercase;font-size: 11px;">${data[key].reason}</td>
                                                                        ${Onu_RXStyle}
                                                                        ${Uptime}
                                                                        ${LanMac}
                                                                        ${terminal}
                                                                    </tr>
                                                            `;
                                                            document.getElementById("data_div").innerHTML += `<br>
                                                                <fieldset class="border border-gray-800 p-3 rounded" style="width: 100%;">
                                                                <legend class="text-sm text-gray-600 font-semibold uppercase tracking-widest">${data.type}</legend>
                                                                    <div class="flex items-center sm:justify-center ml-4 sm:ml-0" style="width: 100%;">
                                                                        <table class="text-sm border-separate border-spacing-y-2" style="width: 100%;">
                                                                            <thead> 
                                                                            <tr  class="tr-class">
                                                                                <th class="dark:text-indigo-400"style="text-align:center;letter-spacing: .1em;text-transform: uppercase;font-size: 11px;">Server IP</th>
                                                                                <th class="dark:text-indigo-400"style="text-align:center;letter-spacing: .1em;text-transform: uppercase;font-size: 11px;">Name</th>
                                                                                <th class="dark:text-indigo-400"style="text-align:center;letter-spacing: .1em;text-transform: uppercase;font-size: 11px;">PonPort</th>
                                                                                <th class="dark:text-indigo-400"style="text-align:center;letter-spacing: .1em;text-transform: uppercase;font-size: 11px;">Client</th>
                                                                                <th class="dark:text-indigo-400"style="text-align:center;letter-spacing: .1em;text-transform: uppercase;font-size: 11px;">Type</th>
                                                                                <th class="dark:text-indigo-400"style="text-align:center;letter-spacing: .1em;text-transform: uppercase;font-size: 11px;">SN</th>
                                                                                <th class="dark:text-indigo-400"style="text-align:center;letter-spacing: .1em;text-transform: uppercase;font-size: 11px;">FULL SN</th>
                                                                                <th class="dark:text-indigo-400"style="text-align:center;letter-spacing: .1em;text-transform: uppercase;font-size: 11px;">Onu Status</th>
                                                                                <th class="dark:text-indigo-400"style="text-align:center;letter-spacing: .1em;text-transform: uppercase;font-size: 11px;">Reason</th>
                                                                                <th class="dark:text-indigo-400"style="text-align:center;letter-spacing: .1em;text-transform: uppercase;font-size: 11px;">Dbm</th>
                                                                                <th class="dark:text-indigo-400"style="text-align:center;letter-spacing: .1em;text-transform: uppercase;font-size: 11px;">UPTIME / DOWNTIME</th> 
                                                                                <th class="dark:text-indigo-400"style="text-align:center;letter-spacing: .1em;text-transform: uppercase;font-size: 11px;">LAN MACS</th>                                                                                 
                                                                                <th class="dark:text-indigo-400"style="text-align:center;letter-spacing: .1em;text-transform: uppercase;font-size: 11px;">TERMINAL</th>
                                                                            </tr>
                                                                            </thead>
                                                                            <tbody>             
                                                                                ${tableRow}
                                                                            </tbody>
                                                                        </table>
                                                                    </div>
                                                                </fieldset>
                                                                `;
                                                        }
                                                    }

                                                    LoaderHide = true;   
                                                }
                                                if(data.type == 'HUAWEI')
                                                {
                                                   
                                                    var tableRow = '';
                                                    for (var key in data) 
                                                    {
                                                        if (key.startsWith('OnuList_')) 
                                                        {
                                                            let  Onu_RXStyle = '';
                                                            if(data[key].Dbm < -27)
                                                            {
                                                                Onu_RXStyle = '<td class="td-class" style="text-align:center;color: #efc776;letter-spacing: .1em;text-transform: uppercase;font-size: 11px;"><span class="inline-flex items-center rounded-md bg-amber-400 dark:bg-amber-400/20 px-2 py-1 text-xs font-medium text-amber-400 ring-1 ring-inset ring-amber-600/20"  style="background:rgba(251, 191, 36, .1);">'+data[key].Dbm+'</span></td>';
                                                            }
                                                            else
                                                            {
                                                                Onu_RXStyle = '<td class="td-class" style="text-align:center;letter-spacing: .1em;text-transform: uppercase;font-size: 11px;">'+data[key].Dbm+'</td>';
                                                            }

                                         
                                                            let opStatus = '';let Uptime = '';
                                                            if(data[key].OperStatus.includes('Online'))
                                                            {
                                                                LanMac = `
                                                                        <td class="td-class"style="text-align:center;letter-spacing: .1em;text-transform: uppercase;font-size: 11px;">
                                                                            <button onclick="HUAWEI_LAN_MACS('${item}','${data[key].Ifindex}')" class="inline-flex items-center px-4 py-2 h-8 ml-2  bg-gray-900 dark:bg-gray-900 border  border-indigo-400 rounded-md font-semibold text-xs text-white dark:text-indigo-400 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-indigo-400 dark:hover:text-gray-900  dark:active:text-indigo-400">
                                                                                LAN MACS
                                                                            </button>  
                                                                        </td>
                                                                    `;
                                                                opStatus = `<td class="td-class"style="text-align:center;color:rgb(114, 200, 148);letter-spacing: .1em;text-transform: uppercase;font-size: 11px;"><span  class="inline-flex items-center rounded-md bg-green-400 dark:bg-green-400/20 px-2 py-1 text-xs font-medium text-green-400 ring-1 ring-inset ring-green-600/20"  style="background:rgba(74, 222, 128, .1);">Connected</span></td>`;
                                                                Uptime = `<td class="td-class"style="text-align:center;letter-spacing: .1em;text-transform: uppercase;font-size: 11px;">${data[key].Uptime}</td>`;
                                                            }
                                                            else
                                                            {
                                                                LanMac = `<td class="td-class"style="text-align:center;color: #ef7676;letter-spacing: .1em;text-transform: uppercase;font-size: 11px;">-</td>`;
                                                                opStatus    = `<td class="td-class"style="text-align:center;color: #ef7676;letter-spacing: .1em;text-transform: uppercase;font-size: 11px;"><span class="inline-flex items-center rounded-md bg-red-400 dark:bg-red-400/20 px-2 py-1 text-xs font-medium text-red-400 ring-1 ring-inset ring-red-600/20" style="background:rgba(248, 113, 113, .1);">Down</span></td>`;
                                                                Onu_RXStyle = '<td class="td-class"style="text-align:center;color: #ef7676;letter-spacing: .1em;text-transform: uppercase;font-size: 11px;">-</td>';
                                                                Uptime      = `<td class="td-class"style="text-align:center;color: #ef7676;letter-spacing: .1em;text-transform: uppercase;font-size: 11px;">${data[key].Downtime}</td>`;
                                                            }
                                                        
                                                            let terminal = `
                                                                <td class="td-class"style="text-align:center;letter-spacing: .1em;text-transform: uppercase;font-size: 11px;">
                                                                    <button onclick="openPopup('https://finder.airlink.ge:2222/ssh/host/${data.address}', '${data.Worker}', '${data.userIp}','${data.sshUser}', '${data.sshPass}','HUAWEI'); return false;"  class="inline-flex items-center px-4 py-2 h-8 ml-2  bg-gray-900 dark:bg-gray-900 border  border-indigo-400 rounded-md font-semibold text-xs text-white dark:text-indigo-400 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-indigo-400 dark:hover:text-gray-900  dark:active:text-indigo-400">
                                                                        TERMINAL
                                                                    </button>  
                                                                </td>
                                                            `;

                                                            document.getElementById("data_div").innerHTML += `<br>
                                                                <fieldset class="border border-gray-800 p-3 rounded" style="width: 100%;">
                                                                <legend class="text-sm text-gray-600 font-semibold uppercase tracking-widest">${data.type}</legend>
                                                                    <div class="flex items-center sm:justify-center ml-4 sm:ml-0" style="width: 100%;">
                                                                        <table class="text-sm border-separate border-spacing-y-2" style="width: 100%;">
                                                                            <thead>
                                                                            <tr  class="tr-class">
                                                                                <th class="dark:text-indigo-400"style="text-align:center;letter-spacing: .1em;text-transform: uppercase;font-size: 11px;">Server IP</th>
                                                                                <th class="dark:text-indigo-400"style="text-align:center;letter-spacing: .1em;text-transform: uppercase;font-size: 11px;">Name</th>
                                                                                <th class="dark:text-indigo-400"style="text-align:center;letter-spacing: .1em;text-transform: uppercase;font-size: 11px;">PonPort</th>
                                                                                <th class="dark:text-indigo-400"style="text-align:center;letter-spacing: .1em;text-transform: uppercase;font-size: 11px;">Client</th>
                                                                                <th class="dark:text-indigo-400"style="text-align:center;letter-spacing: .1em;text-transform: uppercase;font-size: 11px;">Type</th>
                                                                                <th class="dark:text-indigo-400"style="text-align:center;letter-spacing: .1em;text-transform: uppercase;font-size: 11px;">Mac address</th>
                                                                                <th class="dark:text-indigo-400"style="text-align:center;letter-spacing: .1em;text-transform: uppercase;font-size: 11px;">Onu Status</th>
                                                                                <th class="dark:text-indigo-400"style="text-align:center;letter-spacing: .1em;text-transform: uppercase;font-size: 11px;">Reason</th>
                                                                                <th class="dark:text-indigo-400"style="text-align:center;letter-spacing: .1em;text-transform: uppercase;font-size: 11px;">Dbm</th>
                                                                                <th class="dark:text-indigo-400"style="text-align:center;letter-spacing: .1em;text-transform: uppercase;font-size: 11px;">UPTIME / DOWNTIME</th>
                                                                                <th class="dark:text-indigo-400"style="text-align:center;letter-spacing: .1em;text-transform: uppercase;font-size: 11px;">LAN MACS</th> 
                                                                                <th class="dark:text-indigo-400"style="text-align:center;letter-spacing: .1em;text-transform: uppercase;font-size: 11px;">TERMINAL</th>
                                                                            </tr>
                                                                            </thead>
                                                                            <tbody>             
                                                                                <tr class="tr-class" style="text-align:center;">                                                                      
                                                                                <td class="td-class"style="text-align:center;letter-spacing: .1em;text-transform: uppercase;font-size: 11px;">${data.address}</td>
                                                                                <td class="td-class"style="text-align:center;letter-spacing: .1em;text-transform: uppercase;font-size: 11px;">${data.ServerName}</td>
                                                                                <td class="td-class"style="text-align:center;letter-spacing: .1em;text-transform: uppercase;font-size: 11px;">${data[key].PonPort}</td>
                                                                                <td class="td-class"style="text-align:center;letter-spacing: .1em;text-transform: uppercase;font-size: 11px;">${data[key].Descr}</td>
                                                                                <td class="td-class"style="text-align:center;letter-spacing: .1em;text-transform: uppercase;font-size: 11px;">${data[key].OnyType}</td>
                                                                                <td class="td-class"style="text-align:center;letter-spacing: .1em;text-transform: uppercase;font-size: 11px;">${data[key].Mac}</td>
                                                                                ${opStatus}
                                                                                <td class="td-class"style="text-align:center;letter-spacing: .1em;text-transform: uppercase;font-size: 11px;">${data[key].reason}</td>
                                                                                ${Onu_RXStyle}
                                                                                ${Uptime}
                                                                                ${LanMac}
                                                                                ${terminal}
                                                                                </tr>
                                                                            </tbody>
                                                                        </table>
                                                                    </div>
                                                                </fieldset>
                                                                `;
                                                        }
                                                    }
                                                     LoaderHide = true;   
                                                }
                                                if(data.type == 'VSOLUTION')
                                                {

                                                    var tableRow = '';
                                                    for (var key in data) 
                                                    {
                                                        if (key.startsWith('OnuList_')) 
                                                        {
                                                            let  Onu_RXStyle = '';
                                                            if(data[key].Dbm < -27)
                                                            {
                                                                Onu_RXStyle = '<td class="td-class" style="text-align:center;color: #efc776;letter-spacing: .1em;text-transform: uppercase;font-size: 11px;"><span class="inline-flex items-center rounded-md bg-amber-400 dark:bg-amber-400/20 px-2 py-1 text-xs font-medium text-amber-400 ring-1 ring-inset ring-amber-600/20"  style="background:rgba(251, 191, 36, .1);">'+data[key].Dbm+'</span></td>';
                                                            }
                                                            else
                                                            {
                                                                Onu_RXStyle = '<td class="td-class" style="text-align:center;letter-spacing: .1em;text-transform: uppercase;font-size: 11px;">'+data[key].Dbm+'</td>';
                                                            }


                                                            let opStatus = '';let Uptime = '';
                                                            if(data[key].OperStatus == 1)
                                                            {
                                                                LanMac = `
                                                                        <td class="td-class"style="text-align:center;letter-spacing: .1em;text-transform: uppercase;font-size: 11px;">
                                                                            <button onclick="VSOLUTION_LAN_MACS('${item}','${data[key].Ifindex}')" class="inline-flex items-center px-4 py-2 h-8 ml-2  bg-gray-900 dark:bg-gray-900 border  border-indigo-400 rounded-md font-semibold text-xs text-white dark:text-indigo-400 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-indigo-400 dark:hover:text-gray-900  dark:active:text-indigo-400">
                                                                                LAN MACS
                                                                            </button>  
                                                                        </td>
                                                                    `;
                                                                opStatus = `<td class="td-class"style="text-align:center;color:rgb(114, 200, 148);letter-spacing: .1em;text-transform: uppercase;font-size: 11px;"><span  class="inline-flex items-center rounded-md bg-green-400 dark:bg-green-400/20 px-2 py-1 text-xs font-medium text-green-400 ring-1 ring-inset ring-green-600/20"  style="background:rgba(74, 222, 128, .1);">Connected</span></td>`;
                                                                Uptime = `<td class="td-class"style="text-align:center;letter-spacing: .1em;text-transform: uppercase;font-size: 11px;">${data[key].Uptime}</td>`;
                                                            }
                                                            else
                                                            {
                                                                LanMac = `<td class="td-class"style="text-align:center;color: #ef7676;letter-spacing: .1em;text-transform: uppercase;font-size: 11px;">-</td>`;
                                                                opStatus    = `<td class="td-class"style="text-align:center;color: #ef7676;letter-spacing: .1em;text-transform: uppercase;font-size: 11px;"><span class="inline-flex items-center rounded-md bg-red-400 dark:bg-red-400/20 px-2 py-1 text-xs font-medium text-red-400 ring-1 ring-inset ring-red-600/20" style="background:rgba(248, 113, 113, .1);">Down</span></td>`;
                                                                Onu_RXStyle = '<td class="td-class"style="text-align:center;color: #ef7676;letter-spacing: .1em;text-transform: uppercase;font-size: 11px;">-</td>';
                                                                Uptime = `<td class="td-class"style="text-align:center;color: #ef7676;letter-spacing: .1em;text-transform: uppercase;font-size: 11px;">${data[key].Downtime}</td>`;
                                                            }

                                                            
                                                        
                                                            let terminal = `
                                                                <td class="td-class"style="text-align:center;letter-spacing: .1em;text-transform: uppercase;font-size: 11px;">
                                                                    <button onclick="openPopup('https://finder.airlink.ge:2222/ssh/host/${data.address}', '${data.Worker}', '${data.userIp}','${data.sshUser}', '${data.sshPass}','VSOLUTION'); return false;"  class="inline-flex items-center px-4 py-2 h-8 ml-2  bg-gray-900 dark:bg-gray-900 border  border-indigo-400 rounded-md font-semibold text-xs text-white dark:text-indigo-400 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-indigo-400 dark:hover:text-gray-900  dark:active:text-indigo-400">
                                                                        TERMINAL
                                                                    </button>  
                                                                </td>
                                                            `;

                                                            document.getElementById("data_div").innerHTML += `<br>
                                                                <fieldset class="border border-gray-800 p-3 rounded" style="width: 100%;">
                                                                <legend class="text-sm text-gray-600 font-semibold uppercase tracking-widest">${data.type}</legend>
                                                                    <div class="flex items-center sm:justify-center ml-4 sm:ml-0" style="width: 100%;">
                                                                        <table class="text-sm border-separate border-spacing-y-2" style="width: 100%;">
                                                                            <thead>
                                                                            <tr  class="tr-class">
                                                                                <th class="dark:text-indigo-400"style="text-align:center;letter-spacing: .1em;text-transform: uppercase;font-size: 11px;">Server IP</th>
                                                                                <th class="dark:text-indigo-400"style="text-align:center;letter-spacing: .1em;text-transform: uppercase;font-size: 11px;">Name</th>
                                                                                <th class="dark:text-indigo-400"style="text-align:center;letter-spacing: .1em;text-transform: uppercase;font-size: 11px;">PonPort</th>
                                                                                <th class="dark:text-indigo-400"style="text-align:center;letter-spacing: .1em;text-transform: uppercase;font-size: 11px;">Client</th>
                                                                                <th class="dark:text-indigo-400"style="text-align:center;letter-spacing: .1em;text-transform: uppercase;font-size: 11px;">Type</th>
                                                                                <th class="dark:text-indigo-400"style="text-align:center;letter-spacing: .1em;text-transform: uppercase;font-size: 11px;">Mac address</th>
                                                                                <th class="dark:text-indigo-400"style="text-align:center;letter-spacing: .1em;text-transform: uppercase;font-size: 11px;">Onu Status</th>
                                                                                <th class="dark:text-indigo-400"style="text-align:center;letter-spacing: .1em;text-transform: uppercase;font-size: 11px;">Reason</th>
                                                                                <th class="dark:text-indigo-400"style="text-align:center;letter-spacing: .1em;text-transform: uppercase;font-size: 11px;">Dbm</th>
                                                                                <th class="dark:text-indigo-400"style="text-align:center;letter-spacing: .1em;text-transform: uppercase;font-size: 11px;">UPTIME / DONWTIME</th>
                                                                                <th class="dark:text-indigo-400"style="text-align:center;letter-spacing: .1em;text-transform: uppercase;font-size: 11px;">LAN MACS</th>
                                                                                <th class="dark:text-indigo-400"style="text-align:center;letter-spacing: .1em;text-transform: uppercase;font-size: 11px;">TERMINAL</th>
                                                                            </tr>
                                                                            </thead>
                                                                            <tbody>             
                                                                                <tr class="tr-class" style="text-align:center;">                                                                      
                                                                                <td class="td-class"style="text-align:center;letter-spacing: .1em;text-transform: uppercase;font-size: 11px;">${data.address}</td>
                                                                                <td class="td-class"style="text-align:center;letter-spacing: .1em;text-transform: uppercase;font-size: 11px;">${data.ServerName}</td>
                                                                                <td class="td-class"style="text-align:center;letter-spacing: .1em;text-transform: uppercase;font-size: 11px;">${data[key].PonPort}</td>
                                                                                <td class="td-class"style="text-align:center;letter-spacing: .1em;text-transform: uppercase;font-size: 11px;">${data[key].Descr}</td>
                                                                                <td class="td-class"style="text-align:center;letter-spacing: .1em;text-transform: uppercase;font-size: 11px;">${data[key].OnyType}</td>
                                                                                <td class="td-class"style="text-align:center;letter-spacing: .1em;text-transform: uppercase;font-size: 11px;">${data[key].Mac}</td>
                                                                                ${opStatus}
                                                                                <td class="td-class"style="text-align:center;letter-spacing: .1em;text-transform: uppercase;font-size: 11px;">${data[key].reason}</td>
                                                                                ${Onu_RXStyle}
                                                                                ${Uptime}
                                                                                ${LanMac}
                                                                                ${terminal}
                                                                                </tr>
                                                                            </tbody>
                                                                        </table>
                                                                    </div>
                                                                </fieldset>
                                                                `;
                                                   
                                                        }
                                                    }
                  
                                                    LoaderHide = true;   
                                                }
                                                if(data.type == 'HSGQ')
                                                { 

                                                    var tableRow = '';
                                                    for (var key in data) 
                                                    {
                                                        if (key.startsWith('OnuList_')) 
                                                        {
                                                            let  Onu_RXStyle = '';
                                                            if(data[key].Dbm < -27)
                                                            {
                                                                Onu_RXStyle = '<td class="td-class" style="text-align:center;color: #efc776;letter-spacing: .1em;text-transform: uppercase;font-size: 11px;"><span class="inline-flex items-center rounded-md bg-amber-400 dark:bg-amber-400/20 px-2 py-1 text-xs font-medium text-amber-400 ring-1 ring-inset ring-amber-600/20"  style="background:rgba(251, 191, 36, .1);">'+data[key].Dbm+'</span></td>';
                                                            }
                                                            else
                                                            {
                                                                Onu_RXStyle = '<td class="td-class" style="text-align:center;letter-spacing: .1em;text-transform: uppercase;font-size: 11px;">'+data[key].Dbm+'</td>';
                                                            }


                                                            let opStatus = '';let Uptime = '';
                                                            if(data[key].OperStatus.includes('Online'))
                                                            {
                                                                LanMac = `
                                                                        <td class="td-class"style="text-align:center;letter-spacing: .1em;text-transform: uppercase;font-size: 11px;">
                                                                            <button onclick="HSGQ_LAN_MACS('${item}','${data[key].Ifindex}')" class="inline-flex items-center px-4 py-2 h-8 ml-2  bg-gray-900 dark:bg-gray-900 border  border-indigo-400 rounded-md font-semibold text-xs text-white dark:text-indigo-400 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-indigo-400 dark:hover:text-gray-900  dark:active:text-indigo-400">
                                                                                LAN MACS
                                                                            </button>  
                                                                        </td>
                                                                    `;

                                                                opStatus = `<td class="td-class"style="text-align:center;color:rgb(114, 200, 148);letter-spacing: .1em;text-transform: uppercase;font-size: 11px;"><span  class="inline-flex items-center rounded-md bg-green-400 dark:bg-green-400/20 px-2 py-1 text-xs font-medium text-green-400 ring-1 ring-inset ring-green-600/20"  style="background:rgba(74, 222, 128, .1);">Connected</span></td>`;
                                                                Uptime = `<td class="td-class"style="text-align:center;letter-spacing: .1em;text-transform: uppercase;font-size: 11px;">${data[key].Uptime}</td>`;
                                                            }
                                                            else
                                                            {
                                                                LanMac = `<td class="td-class"style="text-align:center;color: #ef7676;letter-spacing: .1em;text-transform: uppercase;font-size: 11px;">-</td>`;
                                                                opStatus    = `<td class="td-class"style="text-align:center;color: #ef7676;letter-spacing: .1em;text-transform: uppercase;font-size: 11px;"><span class="inline-flex items-center rounded-md bg-red-400 dark:bg-red-400/20 px-2 py-1 text-xs font-medium text-red-400 ring-1 ring-inset ring-red-600/20" style="background:rgba(248, 113, 113, .1);">Down</span></td>`;
                                                                Onu_RXStyle = '<td class="td-class"style="text-align:center;color: #ef7676;letter-spacing: .1em;text-transform: uppercase;font-size: 11px;">-</td>';
                                                                Uptime = `<td class="td-class"style="text-align:center;color: #ef7676;letter-spacing: .1em;text-transform: uppercase;font-size: 11px;">${data[key].Downtime}</td>`;
                                                            }
                                                        
                                                            let terminal = `
                                                                <td class="td-class"style="text-align:center;letter-spacing: .1em;text-transform: uppercase;font-size: 11px;">
                                                                    <button onclick="openPopup('https://finder.airlink.ge:2222/ssh/host/${data.address}', '${data.Worker}', '${data.userIp}','${data.sshUser}', '${data.sshPass}','HSGQ'); return false;"  class="inline-flex items-center px-4 py-2 h-8 ml-2  bg-gray-900 dark:bg-gray-900 border  border-indigo-400 rounded-md font-semibold text-xs text-white dark:text-indigo-400 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-indigo-400 dark:hover:text-gray-900  dark:active:text-indigo-400">
                                                                        TERMINAL
                                                                    </button>  
                                                                </td>
                                                            `;

                                                            document.getElementById("data_div").innerHTML += `<br>
                                                                <fieldset class="border border-gray-800 p-3 rounded" style="width: 100%;">
                                                                <legend class="text-sm text-gray-600 font-semibold uppercase tracking-widest">${data.type}</legend>
                                                                    <div class="flex items-center sm:justify-center ml-4 sm:ml-0" style="width: 100%;">
                                                                        <table class="text-sm border-separate border-spacing-y-2" style="width: 100%;">
                                                                            <thead>
                                                                            <tr  class="tr-class">
                                                                                <th class="dark:text-indigo-400"style="text-align:center;letter-spacing: .1em;text-transform: uppercase;font-size: 11px;">Server IP</th>
                                                                                <th class="dark:text-indigo-400"style="text-align:center;letter-spacing: .1em;text-transform: uppercase;font-size: 11px;">Name</th>
                                                                                <th class="dark:text-indigo-400"style="text-align:center;letter-spacing: .1em;text-transform: uppercase;font-size: 11px;">PonPort</th>
                                                                                <th class="dark:text-indigo-400"style="text-align:center;letter-spacing: .1em;text-transform: uppercase;font-size: 11px;">Client</th>
                                                                                <th class="dark:text-indigo-400"style="text-align:center;letter-spacing: .1em;text-transform: uppercase;font-size: 11px;">Type</th>
                                                                                <th class="dark:text-indigo-400"style="text-align:center;letter-spacing: .1em;text-transform: uppercase;font-size: 11px;">Mac address</th>
                                                                                <th class="dark:text-indigo-400"style="text-align:center;letter-spacing: .1em;text-transform: uppercase;font-size: 11px;">Onu Status</th>
                                                                                <th class="dark:text-indigo-400"style="text-align:center;letter-spacing: .1em;text-transform: uppercase;font-size: 11px;">Reason</th>
                                                                                <th class="dark:text-indigo-400"style="text-align:center;letter-spacing: .1em;text-transform: uppercase;font-size: 11px;">Dbm</th>
                                                                                <th class="dark:text-indigo-400"style="text-align:center;letter-spacing: .1em;text-transform: uppercase;font-size: 11px;">UPTIME / DOWNTIME</th>
                                                                                <th class="dark:text-indigo-400"style="text-align:center;letter-spacing: .1em;text-transform: uppercase;font-size: 11px;">LAN MACS</th>
                                                                                <th class="dark:text-indigo-400"style="text-align:center;letter-spacing: .1em;text-transform: uppercase;font-size: 11px;">TERMINAL</th>
                                                                            </tr>
                                                                            </thead>
                                                                            <tbody>             
                                                                                <tr class="tr-class" style="text-align:center;">                                                                      
                                                                                <td class="td-class"style="text-align:center;letter-spacing: .1em;text-transform: uppercase;font-size: 11px;">${data.address}</td>
                                                                                <td class="td-class"style="text-align:center;letter-spacing: .1em;text-transform: uppercase;font-size: 11px;">${data.ServerName}</td>
                                                                                <td class="td-class"style="text-align:center;letter-spacing: .1em;text-transform: uppercase;font-size: 11px;">${data[key].PonPort}</td>
                                                                                <td class="td-class"style="text-align:center;letter-spacing: .1em;text-transform: uppercase;font-size: 11px;">${data[key].Descr}</td>
                                                                                <td class="td-class"style="text-align:center;letter-spacing: .1em;text-transform: uppercase;font-size: 11px;">${data[key].OnyType}</td>
                                                                                <td class="td-class"style="text-align:center;letter-spacing: .1em;text-transform: uppercase;font-size: 11px;">${data[key].Mac}</td>
                                                                                ${opStatus}
                                                                                <td class="td-class"style="text-align:center;letter-spacing: .1em;text-transform: uppercase;font-size: 11px;">${data[key].reason}</td>
                                                                                ${Onu_RXStyle}
                                                                                ${Uptime}
                                                                                ${LanMac}
                                                                                ${terminal}
                                                                                </tr>
                                                                            </tbody>
                                                                        </table>
                                                                    </div>
                                                                </fieldset>
                                                                `;
                                                            
                                                        }
                                                    }

                                                   LoaderHide = true;   
                                                }
                                        }  

                                        completedRequests++;
                                        if (completedRequests === result.length) 
                                        {
                                            $("#gsearchbutton").removeAttr("disabled");
                                            document.getElementById("refresh").innerHTML = '';
                                            if(!LoaderHide)document.getElementById("data_div").innerHTML = `<br><center><p class="text-red-400">Not Found</p></center>`;   
                                        }  
                                    },
                                    error: function (xhr, status, error) 
                                    { 
                                        $("#gsearchbutton").removeAttr("disabled");
                                        showNotification('Search Error', xhr.status +'  '+ error ,'error',60);
                                    }
                                });
                               
                        });
                    } 
                    
                }
            },
            error: function (xhr, status, error) 
            {
                $("#gsearchbutton").removeAttr("disabled");
                if(!LoaderHide)document.getElementById("refresh").innerHTML = '';
                showNotification('Search Error', xhr.status+'  '+error ,'error',3);
            }
             });
        }

    function HSGQ_LAN_MACS(ip,port,refresh = false)
    {
        if(!refresh)
        showModal('lan-macs-modal');

        document.getElementById("LanMacsModalDiv").innerHTML = `
            <svg  width="30" height="30" fill="#5c6bc0" class="mr-2 animate-spin" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg">
                <path d="M526 1394q0 53-37.5 90.5t-90.5 37.5q-52 0-90-38t-38-90q0-53 37.5-90.5t90.5-37.5 90.5 37.5 37.5 90.5zm498 206q0 53-37.5 90.5t-90.5 37.5-90.5-37.5-37.5-90.5 37.5-90.5 90.5-37.5 90.5 37.5 37.5 90.5zm-704-704q0 53-37.5 90.5t-90.5 37.5-90.5-37.5-37.5-90.5 37.5-90.5 90.5-37.5 90.5 37.5 37.5 90.5zm1202 498q0 52-38 90t-90 38q-53 0-90.5-37.5t-37.5-90.5 37.5-90.5 90.5-37.5 90.5 37.5 37.5 90.5zm-964-996q0 66-47 113t-113 47-113-47-47-113 47-113 113-47 113 47 47 113zm1170 498q0 53-37.5 90.5t-90.5 37.5-90.5-37.5-37.5-90.5 37.5-90.5 90.5-37.5 90.5 37.5 37.5 90.5zm-640-704q0 80-56 136t-136 56-136-56-56-136 56-136 136-56 136 56 56 136zm530 206q0 93-66 158.5t-158 65.5q-93 0-158.5-65.5t-65.5-158.5q0-92 65.5-158t158.5-66q92 0 158 66t66 158z">
                </path>
            </svg>
        `;

        $.ajax({
        url: "/HSGQ-AFTERINSTALL-MAC",
        type: "GET",
        data:{'ip':ip,'ifIndex':port},
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        dataType: "text",
        success: function(result)
        {
            let jsonData = JSON.parse(result);
            if(jsonData.error)
            {
               document.getElementById("LanMacsModalDiv").innerHTML = `
               <div style="background-color: rgb(133 77 14 / 0.1) !important; border: 0.5px solid  rgb(113 63 18 /1) !important; color:rgb(234 179 8 /1) !important; border-radius: 0.375rem !important; padding: 1rem !important; font-size: 0.875rem !important;" role="alert">
                    <div style="display: flex !important;">
                        <div style="flex-shrink: 0 !important;">
                            <svg style="flex-shrink: 0 !important; width: 1.5rem !important; height: 1.5rem !important; margin-top: 0.125rem !important;" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/>
                                <path d="M12 9v4"/>
                                <path d="M12 17h.01"/>
                            </svg>
                        </div>
                        <div style="margin-left: 1rem !important;">
                            <h3 style="font-size: 14px !important; font-weight: 600 !important;">Warning...</h3>
                            <div style="margin-top: 0.25rem !important; font-size: 12px !important; color: rgb(161 98 7 / 1) !important;">${jsonData.error }</div>
                        </div>
                    </div>
                </div>
               `;     
            }
            else
            {
                if(jsonData.shutDown == 0)
                {

                    var tableRow = '';
                    for (var key in jsonData) 
                    {
                        if (key.startsWith('macs_num_')) 
                        {
                            if(jsonData[key].mac)
                            {
                                airsoft_mac_check(jsonData[key].mac,`AirSoftFind${key}`,`ClientFind${key}`);
                            }

                            tableRow += `
                                <tr class="tr-class" style="text-align:center;">      
                                    <td class="td-class hover:text-indigo-700" onclick="HSGQ_LAN_MACS('${ip}','${port}','true')"   style="cursor: pointer !important;">
                                        <div class="flex items-center justify-center">    
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" />
                                            </svg>
                                        </div>
                                    </td>
                                    <td class="td-class"style="text-align:center;">${jsonData.PonPort}</td>
                                    <td class="td-class"style="text-align:center;">${jsonData.Description}</td>
                                    <td class="td-class"style="text-align:center;">${jsonData[key].vlan}</td>
                                    <td class="td-class"style="text-align:center;">${jsonData[key].mac}</td>
                                    <td class="td-class"style="text-align:center;">${jsonData[key].vendoor}</td>
                                    <td class="td-class"style="text-align:center;color: #efc776;" id="AirSoftFind${key}"></td>
                                    <td class="td-class"style="text-align:center;" id="ClientFind${key}"></td>  
                                </tr>
                            `;

                        }
                    }

                    document.getElementById("LanMacsModalDiv").innerHTML = `
                    <fieldset class="border border-gray-800 p-3 rounded" style="width: 100%;">
                        <legend class="text-sm text-gray-600 font-semibold uppercase tracking-widest">ONU MACS</legend>
                            <div class="flex items-center sm:justify-center ml-4 sm:ml-0" style="width: 100%;">
                                <table class="text-sm border-separate border-spacing-y-2" style="width: 100%;">
                                    <thead>
                                    <tr  class="tr-class">
                                        <th class="td-class text-indigo-400 text-center uppercase" style="color:#818cf8;background:#141c3d;letter-spacing: .1em;font-weight: 600;">Retry</th>
                                        <th class="td-class text-indigo-400 text-center uppercase" style="color:#818cf8;background:#141c3d;letter-spacing: .1em;font-weight: 600;">Pon Port</th>
                                        <th class="td-class text-indigo-400 text-center uppercase" style="color:#818cf8;background:#141c3d;letter-spacing: .1em;font-weight: 600;">Client</th>
                                        <th class="td-class text-indigo-400 text-center uppercase" style="color:#818cf8;background:#141c3d;letter-spacing: .1em;font-weight: 600;">Vlan</th>
                                        <th class="td-class text-indigo-400 text-center uppercase" style="color:#818cf8;background:#141c3d;letter-spacing: .1em;font-weight: 600;">Mac</th>
                                        <th class="td-class text-indigo-400 text-center uppercase" style="color:#818cf8;background:#141c3d;letter-spacing: .1em;font-weight: 600;">Vendoor</th>
                                        <th class="td-class text-indigo-400 text-center uppercase" style="color:#818cf8;background:#141c3d;letter-spacing: .1em;font-weight: 600;">Airsoft</th>
                                        <th class="td-class text-indigo-400 text-center uppercase" style="color:#818cf8;background:#141c3d;letter-spacing: .1em;font-weight: 600;">CHECK</th>   
                                    </tr>
                                    </thead>
                                    <tbody>             
                                        ${tableRow}
                                    </tbody>
                                </table>
                            </div>
                        </fieldset><br>`;
                }
                else
                {

                    document.getElementById("LanMacsModalDiv").innerHTML = `
                    <fieldset class="border border-gray-800 p-3 rounded" style="width: 100%;">
                        <legend class="text-sm text-gray-600 font-semibold uppercase tracking-widest">ONU STATS</legend>
                            <div class="flex items-center sm:justify-center ml-4 sm:ml-0" style="width: 100%;">
                                <table class="text-sm border-separate border-spacing-y-2" style="width: 100%;">
                                    <thead>
                                    <tr  class="tr-class">
                                        <th class="td-class text-indigo-400 text-center uppercase" style="color:#818cf8;background:#141c3d;letter-spacing: .1em;font-weight: 600;">Retry</th>
                                        <th class="td-class text-indigo-400 text-center uppercase" style="color:#818cf8;background:#141c3d;letter-spacing: .1em;font-weight: 600;">Pon Port</th>
                                        <th class="td-class text-indigo-400 text-center uppercase" style="color:#818cf8;background:#141c3d;letter-spacing: .1em;font-weight: 600;">Client</th>
                                        <th class="td-class text-indigo-400 text-center uppercase" style="color:#818cf8;background:#141c3d;letter-spacing: .1em;font-weight: 600;">Vlan</th>
                                        <th class="td-class text-indigo-400 text-center uppercase" style="color:#818cf8;background:#141c3d;letter-spacing: .1em;font-weight: 600;">Mac</th>
                                        <th class="td-class text-indigo-400 text-center uppercase" style="color:#818cf8;background:#141c3d;letter-spacing: .1em;font-weight: 600;">Vendoor</th>
                                        <th class="td-class text-indigo-400 text-center uppercase" style="color:#818cf8;background:#141c3d;letter-spacing: .1em;font-weight: 600;">Airsoft</th>
                                        <th class="td-class text-indigo-400 text-center uppercase" style="color:#818cf8;background:#141c3d;letter-spacing: .1em;font-weight: 600;">CHECK</th>   
                                    </tr>
                                    </thead>
                                    <tbody>             
                                        <tr class="tr-class" style="text-align:center;">      
                                            <td class="td-class hover:text-indigo-700"  onclick="HSGQ_LAN_MACS('${ip}','${port}','true')"  style="cursor: pointer !important;">
                                                <div class="flex items-center justify-center">    
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" />
                                                    </svg>
                                                </div>
                                            </td>
                                            <td class="td-class"style="text-align:center;">${jsonData.PonPort}</td>
                                            <td class="td-class"style="text-align:center;">${jsonData.Description}</td>
                                            <td class="td-class"style="text-align:center;color:rgb(239, 118, 118) !important;">-</td>
                                            <td class="td-class"style="text-align:center;color:rgb(239, 118, 118) !important;">-</td>
                                            <td class="td-class"style="text-align:center;color:rgb(239, 118, 118) !important;">-</td>
                                            <td class="td-class"style="text-align:center;color:rgb(239, 118, 118) !important;">-</td>
                                            <td class="td-class"style="text-align:center;color:rgb(239, 118, 118) !important;">-</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </fieldset><br>`;
                }
            }
        },
        error: function (xhr, status, error) 
        { 
            showNotification('Hsgq Search', xhr.responseJSON.message,'warning',30);
        }
    });



    }

    function VSOLUTION_LAN_MACS(ip,ifIndex,refresh = false)
    {
        if(!refresh)
        showModal('lan-macs-modal');

        document.getElementById("LanMacsModalDiv").innerHTML = `
            <svg  width="30" height="30" fill="#5c6bc0" class="mr-2 animate-spin" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg">
                <path d="M526 1394q0 53-37.5 90.5t-90.5 37.5q-52 0-90-38t-38-90q0-53 37.5-90.5t90.5-37.5 90.5 37.5 37.5 90.5zm498 206q0 53-37.5 90.5t-90.5 37.5-90.5-37.5-37.5-90.5 37.5-90.5 90.5-37.5 90.5 37.5 37.5 90.5zm-704-704q0 53-37.5 90.5t-90.5 37.5-90.5-37.5-37.5-90.5 37.5-90.5 90.5-37.5 90.5 37.5 37.5 90.5zm1202 498q0 52-38 90t-90 38q-53 0-90.5-37.5t-37.5-90.5 37.5-90.5 90.5-37.5 90.5 37.5 37.5 90.5zm-964-996q0 66-47 113t-113 47-113-47-47-113 47-113 113-47 113 47 47 113zm1170 498q0 53-37.5 90.5t-90.5 37.5-90.5-37.5-37.5-90.5 37.5-90.5 90.5-37.5 90.5 37.5 37.5 90.5zm-640-704q0 80-56 136t-136 56-136-56-56-136 56-136 136-56 136 56 56 136zm530 206q0 93-66 158.5t-158 65.5q-93 0-158.5-65.5t-65.5-158.5q0-92 65.5-158t158.5-66q92 0 158 66t66 158z">
                </path>
            </svg>
        `;

        
        $.ajax({
            url: "/VSOLUTION-AFTERINSTALL-MAC",
            type: "GET",
            data:{'ip':ip,'ifIndex':ifIndex},
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            dataType: "text",
            success: function(result)
            {
                let jsonData = JSON.parse(result);
                if(jsonData.error)
                {
                document.getElementById("LanMacsModalDiv").innerHTML = `
                <div style="background-color: rgb(133 77 14 / 0.1) !important; border: 0.5px solid  rgb(113 63 18 /1) !important; color:rgb(234 179 8 /1) !important; border-radius: 0.375rem !important; padding: 1rem !important; font-size: 0.875rem !important;" role="alert">
                        <div style="display: flex !important;">
                            <div style="flex-shrink: 0 !important;">
                                <svg style="flex-shrink: 0 !important; width: 1.5rem !important; height: 1.5rem !important; margin-top: 0.125rem !important;" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/>
                                    <path d="M12 9v4"/>
                                    <path d="M12 17h.01"/>
                                </svg>
                            </div>
                            <div style="margin-left: 1rem !important;">
                                <h3 style="font-size: 14px !important; font-weight: 600 !important;">Warning...</h3>
                                <div style="margin-top: 0.25rem !important; font-size: 12px !important; color: rgb(161 98 7 / 1) !important;">${jsonData.error }</div>
                            </div>
                        </div>
                    </div>
                `;     
                }
                else
                {
                    if(jsonData.shutdown == 1)
                    {

                        var tableRow = '';
                        for (var key in jsonData) 
                        {
                            if (key.startsWith('macs_num_')) 
                            {
                                if(jsonData[key].mac)
                                {
                                    airsoft_mac_check(jsonData[key].mac,`AirSoftFind${key}`,`ClientFind${key}`);
                                }

                                tableRow += `
                                    <tr class="tr-class" style="text-align:center;">      
                                        <td class="td-class hover:text-indigo-700" onclick="VSOLUTION_LAN_MACS('${ip}','${ifIndex}','true')"  style="cursor: pointer !important;">
                                            <div class="flex items-center justify-center">    
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" />
                                                </svg>
                                            </div>
                                        </td>
                                        <td class="td-class"style="text-align:center;">${jsonData.PonPort}</td>
                                        <td class="td-class"style="text-align:center;">${jsonData.Description}</td>
                                        <td class="td-class"style="text-align:center;">${jsonData[key].vlan}</td>
                                        <td class="td-class"style="text-align:center;">${jsonData[key].mac}</td>
                                        <td class="td-class"style="text-align:center;">${jsonData[key].vendoor}</td>
                                        <td class="td-class"style="text-align:center;color: #efc776;" id="AirSoftFind${key}"></td>
                                        <td class="td-class"style="text-align:center;" id="ClientFind${key}"></td>  
                                    </tr>
                                `;

                            }
                        }

                        document.getElementById("LanMacsModalDiv").innerHTML = `
                        <fieldset class="border border-gray-800 p-3 rounded" style="width: 100%;">
                            <legend class="text-sm text-gray-600 font-semibold uppercase tracking-widest">ONU MACS</legend>
                                <div class="flex items-center sm:justify-center ml-4 sm:ml-0" style="width: 100%;">
                                    <table class="text-sm border-separate border-spacing-y-2" style="width: 100%;">
                                        <thead>
                                        <tr  class="tr-class">
                                            <th class="td-class text-indigo-400 text-center uppercase" style="color:#818cf8;background:#141c3d;letter-spacing: .1em;font-weight: 600;">Retry</th>
                                            <th class="td-class text-indigo-400 text-center uppercase" style="color:#818cf8;background:#141c3d;letter-spacing: .1em;font-weight: 600;">Pon Port</th>
                                            <th class="td-class text-indigo-400 text-center uppercase" style="color:#818cf8;background:#141c3d;letter-spacing: .1em;font-weight: 600;">Client</th>
                                            <th class="td-class text-indigo-400 text-center uppercase" style="color:#818cf8;background:#141c3d;letter-spacing: .1em;font-weight: 600;">Vlan</th>
                                            <th class="td-class text-indigo-400 text-center uppercase" style="color:#818cf8;background:#141c3d;letter-spacing: .1em;font-weight: 600;">Mac</th>
                                            <th class="td-class text-indigo-400 text-center uppercase" style="color:#818cf8;background:#141c3d;letter-spacing: .1em;font-weight: 600;">Vendoor</th>
                                            <th class="td-class text-indigo-400 text-center uppercase" style="color:#818cf8;background:#141c3d;letter-spacing: .1em;font-weight: 600;">Airsoft</th>
                                            <th class="td-class text-indigo-400 text-center uppercase" style="color:#818cf8;background:#141c3d;letter-spacing: .1em;font-weight: 600;">CHECK</th>     
                                        </tr>
                                        </thead>
                                        <tbody>             
                                            ${tableRow}
                                        </tbody>
                                    </table>
                                </div>
                            </fieldset><br>`;
                    }
                    else
                    {

                        document.getElementById("LanMacsModalDiv").innerHTML = `
                        <fieldset class="border border-gray-800 p-3 rounded" style="width: 100%;">
                            <legend class="text-sm text-gray-600 font-semibold uppercase tracking-widest">ONU STATS</legend>
                                <div class="flex items-center sm:justify-center ml-4 sm:ml-0" style="width: 100%;">
                                    <table class="text-sm border-separate border-spacing-y-2" style="width: 100%;">
                                        <thead>
                                        <tr  class="tr-class">
                                            <th class="td-class text-indigo-400 text-center uppercase" style="color:#818cf8;background:#141c3d;letter-spacing: .1em;font-weight: 600;">Retry</th>
                                            <th class="td-class text-indigo-400 text-center uppercase" style="color:#818cf8;background:#141c3d;letter-spacing: .1em;font-weight: 600;">Pon Port</th>
                                            <th class="td-class text-indigo-400 text-center uppercase" style="color:#818cf8;background:#141c3d;letter-spacing: .1em;font-weight: 600;">Client</th>
                                            <th class="td-class text-indigo-400 text-center uppercase" style="color:#818cf8;background:#141c3d;letter-spacing: .1em;font-weight: 600;">Vlan</th>
                                            <th class="td-class text-indigo-400 text-center uppercase" style="color:#818cf8;background:#141c3d;letter-spacing: .1em;font-weight: 600;">Mac</th>
                                            <th class="td-class text-indigo-400 text-center uppercase" style="color:#818cf8;background:#141c3d;letter-spacing: .1em;font-weight: 600;">Vendoor</th>
                                            <th class="td-class text-indigo-400 text-center uppercase" style="color:#818cf8;background:#141c3d;letter-spacing: .1em;font-weight: 600;">Airsoft</th>
                                            <th class="td-class text-indigo-400 text-center uppercase" style="color:#818cf8;background:#141c3d;letter-spacing: .1em;font-weight: 600;">CHECK</th>     
                                        </tr>
                                        </thead>
                                        <tbody>             
                                            <tr class="tr-class" style="text-align:center;">      
                                                <td class="td-class hover:text-indigo-700" onclick="VSOLUTION_LAN_MACS('${ip}','${ifindex}','true')"  style="cursor: pointer !important;">
                                                    <div class="flex items-center justify-center">    
                                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" />
                                                        </svg>
                                                    </div>
                                                </td>
                                                <td class="td-class"style="text-align:center;">${jsonData.PonPort}</td>
                                                <td class="td-class"style="text-align:center;">${jsonData.Description}</td>
                                                <td class="td-class"style="text-align:center;color:rgb(239, 118, 118) !important;">-</td>
                                                <td class="td-class"style="text-align:center;color:rgb(239, 118, 118) !important;">-</td>
                                                <td class="td-class"style="text-align:center;color:rgb(239, 118, 118) !important;">-</td>
                                                <td class="td-class"style="text-align:center;color:rgb(239, 118, 118) !important;">-</td>
                                                <td class="td-class"style="text-align:center;color:rgb(239, 118, 118) !important;">-</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </fieldset><br>`;
                    }
                }
            },
            error: function (xhr, status, error) 
            { 
                showNotification('Vsolution Search', xhr.responseJSON.message,'warning',30);
            }
        });

 
    }

    function HUAWEI_LAN_MACS(ip,ifindex,refresh = false)
    {
        if(!refresh)
        showModal('lan-macs-modal');

        document.getElementById("LanMacsModalDiv").innerHTML = `
            <svg  width="30" height="30" fill="#5c6bc0" class="mr-2 animate-spin" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg">
                <path d="M526 1394q0 53-37.5 90.5t-90.5 37.5q-52 0-90-38t-38-90q0-53 37.5-90.5t90.5-37.5 90.5 37.5 37.5 90.5zm498 206q0 53-37.5 90.5t-90.5 37.5-90.5-37.5-37.5-90.5 37.5-90.5 90.5-37.5 90.5 37.5 37.5 90.5zm-704-704q0 53-37.5 90.5t-90.5 37.5-90.5-37.5-37.5-90.5 37.5-90.5 90.5-37.5 90.5 37.5 37.5 90.5zm1202 498q0 52-38 90t-90 38q-53 0-90.5-37.5t-37.5-90.5 37.5-90.5 90.5-37.5 90.5 37.5 37.5 90.5zm-964-996q0 66-47 113t-113 47-113-47-47-113 47-113 113-47 113 47 47 113zm1170 498q0 53-37.5 90.5t-90.5 37.5-90.5-37.5-37.5-90.5 37.5-90.5 90.5-37.5 90.5 37.5 37.5 90.5zm-640-704q0 80-56 136t-136 56-136-56-56-136 56-136 136-56 136 56 56 136zm530 206q0 93-66 158.5t-158 65.5q-93 0-158.5-65.5t-65.5-158.5q0-92 65.5-158t158.5-66q92 0 158 66t66 158z">
                </path>
            </svg>
        `;
 
            
        $.ajax({
            url: "/HUAWEI-AFTERINSTALL-MAC",
            type: "GET",
            data:{'ip':ip,'ifIndex':ifindex},
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            dataType: "text",
            success: function(result)
            {

                let jsonData = JSON.parse(result);
                if(jsonData.error)
                {
                document.getElementById("LanMacsModalDiv").innerHTML = `
                <div style="background-color: rgb(133 77 14 / 0.1) !important; border: 0.5px solid  rgb(113 63 18 /1) !important; color:rgb(234 179 8 /1) !important; border-radius: 0.375rem !important; padding: 1rem !important; font-size: 0.875rem !important;" role="alert">
                        <div style="display: flex !important;">
                            <div style="flex-shrink: 0 !important;">
                                <svg style="flex-shrink: 0 !important; width: 1.5rem !important; height: 1.5rem !important; margin-top: 0.125rem !important;" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/>
                                    <path d="M12 9v4"/>
                                    <path d="M12 17h.01"/>
                                </svg>
                            </div>
                            <div style="margin-left: 1rem !important;">
                                <h3 style="font-size: 14px !important; font-weight: 600 !important;">Warning...</h3>
                                <div style="margin-top: 0.25rem !important; font-size: 12px !important; color: rgb(161 98 7 / 1) !important;">${jsonData.error }</div>
                            </div>
                        </div>
                    </div>
                `;  
                }
                else
                {
                    
                    if(jsonData.shutdown == 1)
                    { 
                                var tableRow = '';
                                for (var key in jsonData) 
                                {
                                    if (key.startsWith('port_num_')) 
                                    {
                             
                                        if(jsonData[key].mac)
                                        {
                                            airsoft_mac_check(jsonData[key].mac,`AirSoftFind${key}`,`ClientFind${key}`);
                                        }

                                        tableRow += `
                                                    <tr class="tr-class " style="text-align:center;">   
                                                        <td class="td-class hover:text-indigo-700" onclick="HUAWEI_LAN_MACS('${ip}','${ifindex}','true')" style="cursor: pointer !important;">
                                                            <div class="flex items-center justify-center">        
                                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" />
                                                                </svg>
                                                            </div>
                                                        </td>
                                                        <td class="td-class" style="text-align:center;">${jsonData.ponPort}</td>
                                                        <td class="td-class" style="text-align:center;">${jsonData.description}</td>
                                                        <td class="td-class" style="text-align:center;">${jsonData[key].servicePort}</td> 
                                                        <td class="td-class" style="text-align:center;">${jsonData[key].vlan}</td>                                                 
                                                        <td class="td-class" style="text-align:center;">${jsonData[key].mac}</td>
                                                        <td class="td-class" style="text-align:center;">${jsonData[key].vendoor}</td>      
                                                        <td class="td-class"style="text-align:center;color: #efc776;" id="AirSoftFind${key}"></td>
                                                        <td class="td-class"style="text-align:center;" id="ClientFind${key}"></td>     
                                                    </tr>
                                                `;
                                        
                                    }
                                }

                                document.getElementById("LanMacsModalDiv").innerHTML = `<br>
                                <fieldset class="border border-gray-800 p-3 rounded" style="width: 100%;">
                                    <legend class="text-sm text-gray-600 font-semibold uppercase tracking-widest">ONU MACS</legend>
                                        <div class="flex items-center sm:justify-center ml-4 sm:ml-0" style="width: 100%;">
                                            <table class="text-sm border-separate border-spacing-y-2" style="width: 100%;">
                                                <thead>
                                                <tr  class="tr-class">
                                                    <th class="td-class text-indigo-400 text-center uppercase" style="color:#818cf8;background:#141c3d;letter-spacing: .1em;font-weight: 600;">Retry</th>
                                                    <th class="td-class text-indigo-400 text-center uppercase" style="color:#818cf8;background:#141c3d;letter-spacing: .1em;font-weight: 600;">Pon Port</th>
                                                    <th class="td-class text-indigo-400 text-center uppercase" style="color:#818cf8;background:#141c3d;letter-spacing: .1em;font-weight: 600;">Client</th>  
                                                    <th class="td-class text-indigo-400 text-center uppercase" style="color:#818cf8;background:#141c3d;letter-spacing: .1em;font-weight: 600;">Service Port</th>
                                                    <th class="td-class text-indigo-400 text-center uppercase" style="color:#818cf8;background:#141c3d;letter-spacing: .1em;font-weight: 600;">Vlan</th>
                                                    <th class="td-class text-indigo-400 text-center uppercase" style="color:#818cf8;background:#141c3d;letter-spacing: .1em;font-weight: 600;">Mac</th>
                                                    <th class="td-class text-indigo-400 text-center uppercase" style="color:#818cf8;background:#141c3d;letter-spacing: .1em;font-weight: 600;">Vendoor</th>
                                                    <th class="td-class text-indigo-400 text-center uppercase" style="color:#818cf8;background:#141c3d;letter-spacing: .1em;font-weight: 600;">Airsoft</th>
                                                    <th class="td-class text-indigo-400 text-center uppercase" style="color:#818cf8;background:#141c3d;letter-spacing: .1em;font-weight: 600;">CHECK</th>
                                                </tr>
                                                </thead>
                                                <tbody>             
                                                `+tableRow+`
                                                </tbody>
                                            </table>
                                        </div>
                                    </fieldset>
                                    <br>
                                `;
                    }
                    else
                    {
                        document.getElementById("LanMacsModalDiv").innerHTML = `<br>
                        <fieldset class="border border-gray-800 p-3 rounded" style="width: 100%;">
                        <legend class="text-sm text-gray-600 font-semibold uppercase tracking-widest">ONU MACS</legend>
                            <div class="flex items-center sm:justify-center ml-4 sm:ml-0" style="width: 100%;">
                                <table class="text-sm border-separate border-spacing-y-2" style="width: 100%;">
                                    <thead>
                                    <tr  class="tr-class">
                                        <th class="td-class text-indigo-400 text-center uppercase" style="color:#818cf8;background:#141c3d;letter-spacing: .1em;font-weight: 600;">Retry</th>
                                        <th class="td-class text-indigo-400 text-center uppercase" style="color:#818cf8;background:#141c3d;letter-spacing: .1em;font-weight: 600;">Pon Port</th>
                                        <th class="td-class text-indigo-400 text-center uppercase" style="color:#818cf8;background:#141c3d;letter-spacing: .1em;font-weight: 600;">Client</th>  
                                        <th class="td-class text-indigo-400 text-center uppercase" style="color:#818cf8;background:#141c3d;letter-spacing: .1em;font-weight: 600;">Service Port</th>
                                        <th class="td-class text-indigo-400 text-center uppercase" style="color:#818cf8;background:#141c3d;letter-spacing: .1em;font-weight: 600;">Vlan</th>
                                        <th class="td-class text-indigo-400 text-center uppercase" style="color:#818cf8;background:#141c3d;letter-spacing: .1em;font-weight: 600;">Mac</th>
                                        <th class="td-class text-indigo-400 text-center uppercase" style="color:#818cf8;background:#141c3d;letter-spacing: .1em;font-weight: 600;">Vendoor</th>
                                        <th class="td-class text-indigo-400 text-center uppercase" style="color:#818cf8;background:#141c3d;letter-spacing: .1em;font-weight: 600;">Airsoft</th>
                                        <th class="td-class text-indigo-400 text-center uppercase" style="color:#818cf8;background:#141c3d;letter-spacing: .1em;font-weight: 600;">CHECK</th>                            
                                    </tr>
                                    </thead>
                                    <tbody>             
                                        <tr class="tr-class" style="text-align:center;">                                                        
                                            <td class="td-class hover:text-indigo-700" onclick="HUAWEI_LAN_MACS('${ip}','${ifindex}','true')"    style="cursor: pointer !important;">
                                                <div class="flex items-center justify-center">        
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" />
                                                    </svg>
                                                </div>
                                            </td>
                                            <td class="td-class"style="text-align:center;color:rgb(239, 118, 118) !important;">`+ jsonData.ponPort +`</td>
                                            <td class="td-class"style="text-align:center;color:rgb(239, 118, 118) !important;">`+ jsonData.description +`</td>
                                            <td class="td-class"style="text-align:center;color:rgb(239, 118, 118) !important;">-</td>
                                            <td class="td-class"style="text-align:center;color:rgb(239, 118, 118) !important;">-</td>
                                            <td class="td-class"style="text-align:center;color:rgb(239, 118, 118) !important;">-</td>
                                            <td class="td-class"style="text-align:center;color:rgb(239, 118, 118) !important;">-</td>
                                            <td class="td-class"style="text-align:center;color:rgb(239, 118, 118) !important;">-</td>
                                            <td class="td-class"style="text-align:center;color:rgb(239, 118, 118) !important;">-</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </fieldset>
                        <br>
                        `;
                    }
                
                }
            }
        });
    }

    function ZTE_LAN_MACS(ip,ifindex,refresh = false)
    {
        if(!refresh)
        showModal('lan-macs-modal');

        document.getElementById("LanMacsModalDiv").innerHTML = `
            <svg  width="30" height="30" fill="#5c6bc0" class="mr-2 animate-spin" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg">
                <path d="M526 1394q0 53-37.5 90.5t-90.5 37.5q-52 0-90-38t-38-90q0-53 37.5-90.5t90.5-37.5 90.5 37.5 37.5 90.5zm498 206q0 53-37.5 90.5t-90.5 37.5-90.5-37.5-37.5-90.5 37.5-90.5 90.5-37.5 90.5 37.5 37.5 90.5zm-704-704q0 53-37.5 90.5t-90.5 37.5-90.5-37.5-37.5-90.5 37.5-90.5 90.5-37.5 90.5 37.5 37.5 90.5zm1202 498q0 52-38 90t-90 38q-53 0-90.5-37.5t-37.5-90.5 37.5-90.5 90.5-37.5 90.5 37.5 37.5 90.5zm-964-996q0 66-47 113t-113 47-113-47-47-113 47-113 113-47 113 47 47 113zm1170 498q0 53-37.5 90.5t-90.5 37.5-90.5-37.5-37.5-90.5 37.5-90.5 90.5-37.5 90.5 37.5 37.5 90.5zm-640-704q0 80-56 136t-136 56-136-56-56-136 56-136 136-56 136 56 56 136zm530 206q0 93-66 158.5t-158 65.5q-93 0-158.5-65.5t-65.5-158.5q0-92 65.5-158t158.5-66q92 0 158 66t66 158z">
                </path>
            </svg>
        `;
 

        $.ajax({
                url: "/ZTE-AFTERINSTALL-MAC",
                type: "GET",
                data:{'ip':ip,'ifIndex':ifindex},
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                dataType: "text",
                success: function(result)
                {

                    let jsonData = JSON.parse(result);
                    if(jsonData.error)
                    {
                    document.getElementById("LanMacsModalDiv").innerHTML = `
                    <div style="background-color: rgb(133 77 14 / 0.1) !important; border: 0.5px solid  rgb(113 63 18 /1) !important; color:rgb(234 179 8 /1) !important; border-radius: 0.375rem !important; padding: 1rem !important; font-size: 0.875rem !important;" role="alert">
                            <div style="display: flex !important;">
                                <div style="flex-shrink: 0 !important;">
                                    <svg style="flex-shrink: 0 !important; width: 1.5rem !important; height: 1.5rem !important; margin-top: 0.125rem !important;" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/>
                                        <path d="M12 9v4"/>
                                        <path d="M12 17h.01"/>
                                    </svg>
                                </div>
                                <div style="margin-left: 1rem !important;">
                                    <h3 style="font-size: 14px !important; font-weight: 600 !important;">Warning...</h3>
                                    <div style="margin-top: 0.25rem !important; font-size: 12px !important; color: rgb(161 98 7 / 1) !important;">${jsonData.error }</div>
                                </div>
                            </div>
                        </div>
                    `;  
                    }
                    else
                    {
                        
                        if(jsonData.shutdown == 1)
                        {
                            var tableRow = '';
                            for (var key in jsonData) 
                            {
                                if (key.startsWith('PortList_')) 
                                {
                                    if(jsonData[key].RMac)
                                    {
                                       airsoft_mac_check(jsonData[key].RMac,`AirSoftFind${key}`,`ClientFind${key}`);
                                    }

                                    tableRow += `
                                                <tr class="tr-class " style="text-align:center;">   
                                                    <td class="td-class hover:text-indigo-700" onclick="ZTE_LAN_MACS('${ip}','${ifindex}','true')"  style="cursor: pointer !important;">
                                                        <div class="flex items-center justify-center">        
                                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" />
                                                            </svg>
                                                        </div>
                                                    </td>
                                                    <td class="td-class" style="text-align:center;">${jsonData.OnuPort}</td>
                                                    <td class="td-class" style="text-align:center;">${jsonData.Description}</td>
                                                    <td class="td-class" style="text-align:center;">${jsonData[key].Vlan}</td>                                                 
                                                    <td class="td-class" style="text-align:center;">${jsonData[key].RMac}</td>
                                                    <td class="td-class" style="text-align:center;">${jsonData[key].Vendoor}</td>   
                                                    <td class="td-class"style="text-align:center;color: #efc776;" id="AirSoftFind${key}"></td>
                                                    <td class="td-class"style="text-align:center;" id="ClientFind${key}"></td>                                              
                                                </tr>
                                            `;
                                    
                                }
                            }
                                document.getElementById("LanMacsModalDiv").innerHTML = `
                                    <fieldset class="border border-gray-800 p-3 rounded" style="width: 100%;">
                                        <legend class="text-sm text-gray-600 font-semibold uppercase tracking-widest">ONU MACS</legend>
                                            <div class="flex items-center sm:justify-center ml-4 sm:ml-0" style="width: 100%;">
                                                <table class="text-sm border-separate border-spacing-y-2" style="width: 100%;">
                                                    <thead>
                                                    <tr  class="tr-class">
                                                        <th class="td-class text-indigo-400 text-center uppercase" style="color:#818cf8;background:#141c3d;letter-spacing: .1em;font-weight: 600;">Retry</th>
                                                        <th class="td-class text-indigo-400 text-center uppercase" style="color:#818cf8;background:#141c3d;letter-spacing: .1em;font-weight: 600;">Pon Port</th>
                                                        <th class="td-class text-indigo-400 text-center uppercase" style="color:#818cf8;background:#141c3d;letter-spacing: .1em;font-weight: 600;">Client</th>  
                                                        <th class="td-class text-indigo-400 text-center uppercase" style="color:#818cf8;background:#141c3d;letter-spacing: .1em;font-weight: 600;">Vlan</th>
                                                        <th class="td-class text-indigo-400 text-center uppercase" style="color:#818cf8;background:#141c3d;letter-spacing: .1em;font-weight: 600;">Mac</th>
                                                        <th class="td-class text-indigo-400 text-center uppercase" style="color:#818cf8;background:#141c3d;letter-spacing: .1em;font-weight: 600;">Vendoor</th>
                                                        <th class="td-class text-indigo-400 text-center uppercase" style="color:#818cf8;background:#141c3d;letter-spacing: .1em;font-weight: 600;">Airsoft</th>
                                                        <th class="td-class text-indigo-400 text-center uppercase" style="color:#818cf8;background:#141c3d;letter-spacing: .1em;font-weight: 600;">CHECK</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>             
                                                    `+tableRow+`
                                                    </tbody>
                                                </table>
                                            </div>
                                        </fieldset>
                                        <br>
                                    `;
                        }
                        else
                        {
 
                            document.getElementById("LanMacsModalDiv").innerHTML = `
                                <fieldset class="border border-gray-800 p-3 rounded" style="width: 100%;">
                                    <legend class="text-sm text-gray-600 font-semibold uppercase tracking-widest">ONU MACS</legend>
                                        <div class="flex items-center sm:justify-center ml-4 sm:ml-0" style="width: 100%;">
                                            <table class="text-sm border-separate border-spacing-y-2" style="width: 100%;">
                                                <thead>
                                                <tr  class="tr-class">
                                                    <th class="td-class text-indigo-400 text-center uppercase" style="color:#818cf8;background:#141c3d;letter-spacing: .1em;font-weight: 600;">Retry</th>
                                                    <th class="td-class text-indigo-400 text-center uppercase" style="color:#818cf8;background:#141c3d;letter-spacing: .1em;font-weight: 600;">Pon Port</th>
                                                    <th class="td-class text-indigo-400 text-center uppercase" style="color:#818cf8;background:#141c3d;letter-spacing: .1em;font-weight: 600;">Client</th>  
                                                    <th class="td-class text-indigo-400 text-center uppercase" style="color:#818cf8;background:#141c3d;letter-spacing: .1em;font-weight: 600;">Vlan</th>
                                                    <th class="td-class text-indigo-400 text-center uppercase" style="color:#818cf8;background:#141c3d;letter-spacing: .1em;font-weight: 600;">Mac</th>
                                                    <th class="td-class text-indigo-400 text-center uppercase" style="color:#818cf8;background:#141c3d;letter-spacing: .1em;font-weight: 600;">Vendoor</th>
                                                    <th class="td-class text-indigo-400 text-center uppercase" style="color:#818cf8;background:#141c3d;letter-spacing: .1em;font-weight: 600;">Airsoft</th>
                                                    <th class="td-class text-indigo-400 text-center uppercase" style="color:#818cf8;background:#141c3d;letter-spacing: .1em;font-weight: 600;">CHECK</th>
                                                </tr>
                                                </thead>
                                                <tbody>             
                                                    <tr class="tr-class" style="text-align:center;">                                                        
                                                            <td class="td-class hover:text-indigo-700" onclick="ZTE_LAN_MACS('${ip}','${jsonData.ifIndex}','true')"  style="cursor: pointer !important;">
                                                                <div class="flex items-center justify-center">   
                                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" />
                                                                    </svg>
                                                                </div>
                                                            </td>
                                                        <td class="td-class"style="text-align:center;color:rgb(239, 118, 118) !important;">`+ jsonData.OnuPort +`</td>
                                                        <td class="td-class"style="text-align:center;color:rgb(239, 118, 118) !important;">`+ jsonData.Description +`</td>
                                                        <td class="td-class"style="text-align:center;color:rgb(239, 118, 118) !important;">-</td>
                                                        <td class="td-class"style="text-align:center;color:rgb(239, 118, 118) !important;">-</td>
                                                        <td class="td-class"style="text-align:center;color:rgb(239, 118, 118) !important;">-</td>
                                                        <td class="td-class"style="text-align:center;color:rgb(239, 118, 118) !important;">-</td>
                                                        <td class="td-class"style="text-align:center;color:rgb(239, 118, 118) !important;">-</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </fieldset>
                                    <br>
                                `;
                        }
                    }
                }
            });
    }

    function BDCOM_LAN_MACS(ip,ifIndex,refresh = false)
    {
        if(!refresh)
        showModal('lan-macs-modal');

        document.getElementById("LanMacsModalDiv").innerHTML = `
            <svg  width="30" height="30" fill="#5c6bc0" class="mr-2 animate-spin" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg">
                <path d="M526 1394q0 53-37.5 90.5t-90.5 37.5q-52 0-90-38t-38-90q0-53 37.5-90.5t90.5-37.5 90.5 37.5 37.5 90.5zm498 206q0 53-37.5 90.5t-90.5 37.5-90.5-37.5-37.5-90.5 37.5-90.5 90.5-37.5 90.5 37.5 37.5 90.5zm-704-704q0 53-37.5 90.5t-90.5 37.5-90.5-37.5-37.5-90.5 37.5-90.5 90.5-37.5 90.5 37.5 37.5 90.5zm1202 498q0 52-38 90t-90 38q-53 0-90.5-37.5t-37.5-90.5 37.5-90.5 90.5-37.5 90.5 37.5 37.5 90.5zm-964-996q0 66-47 113t-113 47-113-47-47-113 47-113 113-47 113 47 47 113zm1170 498q0 53-37.5 90.5t-90.5 37.5-90.5-37.5-37.5-90.5 37.5-90.5 90.5-37.5 90.5 37.5 37.5 90.5zm-640-704q0 80-56 136t-136 56-136-56-56-136 56-136 136-56 136 56 56 136zm530 206q0 93-66 158.5t-158 65.5q-93 0-158.5-65.5t-65.5-158.5q0-92 65.5-158t158.5-66q92 0 158 66t66 158z">
                </path>
            </svg>
        `;
 
        $.ajax({
                url: "/BDCOM-AFTERINSTALL-MAC",
                type: "GET",
                data:{'ip':ip,'ifIndex':ifIndex},
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                dataType: "text",
                success: function(result)
                {
                    let jsonData = JSON.parse(result);
                    if(jsonData.error)
                    {
                        document.getElementById("LanMacsModalDiv").innerHTML = `
                        <div style="background-color: rgb(133 77 14 / 0.1) !important; border: 0.5px solid  rgb(113 63 18 /1) !important; color:rgb(234 179 8 /1) !important; border-radius: 0.375rem !important; padding: 1rem !important; font-size: 0.875rem !important;" role="alert">
                                <div style="display: flex !important;">
                                    <div style="flex-shrink: 0 !important;">
                                        <svg style="flex-shrink: 0 !important; width: 1.5rem !important; height: 1.5rem !important; margin-top: 0.125rem !important;" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/>
                                            <path d="M12 9v4"/>
                                            <path d="M12 17h.01"/>
                                        </svg>
                                    </div>
                                    <div style="margin-left: 1rem !important;">
                                        <h3 style="font-size: 14px !important; font-weight: 600 !important;">Warning...</h3>
                                        <div style="margin-top: 0.25rem !important; font-size: 12px !important; color: rgb(161 98 7 / 1) !important;">${jsonData.error }</div>
                                    </div>
                                </div>
                            </div>
                        `;     
                    }
                    else
                    {
                        if(jsonData.shutdown == 0)
                        {

                            var tableRow = '';
                            for (var key in jsonData) 
                            {
                                if (key.startsWith('MacList_')) 
                                {

                                    if(jsonData[key].mac)
                                    {
                                       airsoft_mac_check(jsonData[key].mac,`AirSoftFind${key}`,`ClientFind${key}`);
                                    }

                                    tableRow += `
                                        <tr class="tr-class" style="text-align:center;">      
                                            <td class="td-class hover:text-indigo-700" onclick="BDCOM_LAN_MACS('${ip}','${jsonData.ifIndex}','true')"  style="cursor: pointer !important;">
                                                <div class="flex items-center justify-center">    
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" />
                                                    </svg>
                                                </div>
                                            </td>
                                            <td class="td-class"style="text-align:center;">${jsonData.PonPort}</td>
                                            <td class="td-class"style="text-align:center;">${jsonData.Description}</td>
                                            <td class="td-class"style="text-align:center;">${jsonData[key].vlan}</td>
                                            <td class="td-class"style="text-align:center;">${jsonData[key].mac}</td>                                            
                                            <td class="td-class"style="text-align:center;">${jsonData[key].vendoor}</td>
                                            <td class="td-class"style="text-align:center;color: #efc776;" id="AirSoftFind${key}"></td>
                                            <td class="td-class"style="text-align:center;color: #efc776;" id="ClientFind${key}"></td>
                                        </tr>
                                    `;

                                }
                            }

                            document.getElementById("LanMacsModalDiv").innerHTML = `
                            <fieldset class="border border-gray-800 p-3 rounded" style="width: 100%;">
                                <legend class="text-sm text-gray-600 font-semibold uppercase tracking-widest">ONU MACS</legend>
                                    <div class="flex items-center sm:justify-center ml-4 sm:ml-0" style="width: 100%;">
                                        <table class="text-sm border-separate border-spacing-y-2" style="width: 100%;">
                                            <thead>
                                            <tr  class="tr-class">
                                                <th class="td-class text-indigo-400 text-center uppercase" style="color:#818cf8;background:#141c3d;letter-spacing: .1em;font-weight: 600;">Retry</th>
                                                <th class="td-class text-indigo-400 text-center uppercase" style="color:#818cf8;background:#141c3d;letter-spacing: .1em;font-weight: 600;">Pon Port</th>
                                                <th class="td-class text-indigo-400 text-center uppercase" style="color:#818cf8;background:#141c3d;letter-spacing: .1em;font-weight: 600;">Client</th>
                                                <th class="td-class text-indigo-400 text-center uppercase" style="color:#818cf8;background:#141c3d;letter-spacing: .1em;font-weight: 600;">Vlan</th>
                                                <th class="td-class text-indigo-400 text-center uppercase" style="color:#818cf8;background:#141c3d;letter-spacing: .1em;font-weight: 600;">Mac</th>                                               
                                                <th class="td-class text-indigo-400 text-center uppercase" style="color:#818cf8;background:#141c3d;letter-spacing: .1em;font-weight: 600;">Vendoor</th>
                                                <th class="td-class text-indigo-400 text-center uppercase" style="color:#818cf8;background:#141c3d;letter-spacing: .1em;font-weight: 600;">Airsoft</th>
                                                <th class="td-class text-indigo-400 text-center uppercase" style="color:#818cf8;background:#141c3d;letter-spacing: .1em;font-weight: 600;">CHECK</th>
                                            </tr>
                                            </thead>
                                            <tbody>             
                                                ${tableRow}
                                            </tbody>
                                        </table>
                                    </div>
                                </fieldset><br>`;
                        }
                        else
                        {

                            document.getElementById("LanMacsModalDiv").innerHTML = `
                            <fieldset class="border border-gray-800 p-3 rounded" style="width: 100%;">
                                <legend class="text-sm text-gray-600 font-semibold uppercase tracking-widest">ONU STATS</legend>
                                    <div class="flex items-center sm:justify-center ml-4 sm:ml-0" style="width: 100%;">
                                        <table class="text-sm border-separate border-spacing-y-2" style="width: 100%;">
                                            <thead>
                                            <tr  class="tr-class">
                                                <th class="td-class text-indigo-400 text-center uppercase" style="color:#818cf8;background:#141c3d;letter-spacing: .1em;font-weight: 600;">Retry</th>
                                                <th class="td-class text-indigo-400 text-center uppercase" style="color:#818cf8;background:#141c3d;letter-spacing: .1em;font-weight: 600;">Pon Port</th>
                                                <th class="td-class text-indigo-400 text-center uppercase" style="color:#818cf8;background:#141c3d;letter-spacing: .1em;font-weight: 600;">Client</th>
                                                <th class="td-class text-indigo-400 text-center uppercase" style="color:#818cf8;background:#141c3d;letter-spacing: .1em;font-weight: 600;">Vlan</th>
                                                <th class="td-class text-indigo-400 text-center uppercase" style="color:#818cf8;background:#141c3d;letter-spacing: .1em;font-weight: 600;">Mac</th>
                                                <th class="td-class text-indigo-400 text-center uppercase" style="color:#818cf8;background:#141c3d;letter-spacing: .1em;font-weight: 600;">Vendoor</th>
                                                <th class="td-class text-indigo-400 text-center uppercase" style="color:#818cf8;background:#141c3d;letter-spacing: .1em;font-weight: 600;">Airsoft</th>
                                                <th class="td-class text-indigo-400 text-center uppercase" style="color:#818cf8;background:#141c3d;letter-spacing: .1em;font-weight: 600;">CHECK</th>
                                            </tr>
                                            </thead>
                                            <tbody>             
                                                <tr class="tr-class" style="text-align:center;">      
                                                    <td class="td-class hover:text-indigo-700" onclick="BDCOM_LAN_MACS('${ip}','${jsonData.ifIndex}')"  style="cursor: pointer !important;">
                                                        <div class="flex items-center justify-center">    
                                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" />
                                                            </svg>
                                                        </div>
                                                    </td>
                                                    <td class="td-class"style="text-align:center;">${jsonData.PonPort}</td>
                                                    <td class="td-class"style="text-align:center;">${jsonData.Description}</td>
                                                    <td class="td-class"style="text-align:center;color:rgb(239, 118, 118) !important;">-</td>
                                                    <td class="td-class"style="text-align:center;color:rgb(239, 118, 118) !important;">-</td>
                                                    <td class="td-class"style="text-align:center;color:rgb(239, 118, 118) !important;">-</td>
                                                    <td class="td-class"style="text-align:center;color:rgb(239, 118, 118) !important;">-</td>
                                                    <td class="td-class"style="text-align:center;color:rgb(239, 118, 118) !important;">-</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </fieldset><br>`;
                        }
                    }
                },
                error: function (xhr, status, error) 
                { 
                    showNotification('Bdcom Search', xhr.responseJSON.message,'warning',30);
                }
            });
    }
    
    function airsoft_mac_check(mac,keyID,goID)
    {
        $.ajax({
                url: "/filterSearch",
                type: "GET",
                data:{'activate_date':null,'activate_date_end':null,'is_fiber':null,'provider':null,'user_id':null,'old_contract_num':null,'user_name':null,'user_lastname':null,'phone':null,'address':null,'personal_id':null,'company_name':null,'user_ip':null
                ,'antenna_ip':null,'sector_ip':null,'mac':mac,'tvmac':null,'disabled':null,'legal':null,'optika':null,'tv':null,'town':null,'subregion':null,'tariff':null,'status':null,'legal_status':null},
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                dataType: "json",
                success: function(result)
                { 
                    if(result.error)
                    {
                        showNotification('Filter Search Result Error', result.error,'warning',3);
                    }
                    else
                    {       
                        for (var key in result.status) 
                        {
                            if(result.status[key].user_id.length > 4)
                            {
                                document.getElementById(keyID).innerText = result.status[key].user_id;

                                document.getElementById(goID).innerHTML = `
                                    <button onclick="FilterStart('${result.status[key].user_id}');" type="submit" class="inline-flex items-center px-4 py-2 h-8 ml-2  bg-gray-700 dark:bg-gray-900 border border-slate-300 shadow-lg dark:border-indigo-400 rounded-md font-semibold text-xs text-gray-200 dark:text-indigo-400 uppercase tracking-widest hover:bg-slate-400 dark:hover:bg-indigo-400 dark:hover:text-gray-900  dark:active:text-indigo-400">
                                        CHECK
                                    </button>
                                `;
                            }
                        }
                    }
                },
                error: function (xhr, status, error) 
                {
                    showNotification('Filter Search Result Error',error,'warning',3);
                }
        });
    }

    </script>

</x-app-layout>
