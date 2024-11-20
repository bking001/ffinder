<x-app-layout>

    <div class="py-12 w-11/12 mx-auto"> 
    
        <x-bladewind.notification position="top right" />
        <x-bladewind.notification />

        <div class=" mx-auto" style="max-height: 800px; overflow-y: auto;display:block;"> 
  
            <div class="flex items-center justify-center" id="cpuCont"></div>
 


            <input id="cpuCores"         value="{{ $Serverstats['cpuCores'] }}" hidden />
            <input id="cpuModel"         value="{{ $Serverstats['cpuModel'] }}" hidden />
            <input id="cpuUsage"         value="{{ $Serverstats['cpuUsage'] }}" hidden />
            <input id="FixedmemoryUsage" value="{{ $Serverstats['FixedmemoryUsage'] }}" hidden />
            <input id="FixedmemoryTotal" value="{{ $Serverstats['FixedmemoryTotal'] }}" hidden />
            <input id="diskUsed"         value="{{ $Serverstats['diskUsed'] }}" hidden />
            <input id="FixeddiskTotal"   value="{{ $Serverstats['FixeddiskTotal'] }}" hidden />

        </div>  
        <br>

        <div class="flex justify-between mx-auto w-full">
            <div class="w-1/2 mx-auto"> 
                <fieldset class="border border-gray-800 p-3 rounded">
                    <legend class="text-sm text-gray-600 font-semibold uppercase tracking-widest">SERVER</legend>

                        <p><span class="text-indigo-400 text-center" style="font-size: 0.80rem;">🌐 Hostname:</span> <span class="text-gray-400 text-center text:xs" style="font-size: 0.80rem;">{{ $Serverstats['hostname'] }} </span></p>
                        <p><span class="text-indigo-400 text-center" style="font-size: 0.80rem;">🌐 Address:</span> <span class="text-gray-400 text-center text:xs" style="font-size: 0.80rem;">{{ $Serverstats['serverip'] }} </span></p>
                        <p><span class="text-indigo-400 text-center" style="font-size: 0.80rem;">🌐 Uptime:</span> <span class="text-gray-400 text-center text:xs" style="font-size: 0.80rem;">{{ $Serverstats['uptime'] }} </span></p>
                        <hr>
                        <p><span class="text-indigo-400 text-center" style="font-size: 0.80rem;">🌡️ RAM Usage:</span> <span class="text-gray-400 text-center text:xs" style="font-size: 0.80rem;">{{ $Serverstats['FixedmemoryUsage'] }} MB</span></p>
                        <p><span class="text-indigo-400 text-center" style="font-size: 0.80rem;">🖥️ CPU Usage: </span> <span class="text-gray-400 text-center text:xs" style="font-size: 0.80rem;">{{ $Serverstats['cpuUsage'] }}%</span></p>
                        <p><span class="text-indigo-400 text-center" style="font-size: 0.80rem;">💽 Hard Disk Usage: </span> <span class="text-gray-400 text-center text:xs" style="font-size: 0.80rem;">{{ $Serverstats['diskUsed'] }}GB</span></p>
                        <p><span class="text-indigo-400 text-center" style="font-size: 0.80rem;">🔗 Established Connections: </span> <span class="text-gray-400 text-center text:xs" style="font-size: 0.80rem;">{{ $Serverstats['Established'] }}</span></p>
                        <p><span class="text-indigo-400 text-center" style="font-size: 0.80rem;">⛓️ Total Connections: </span> <span class="text-gray-400 text-center text:xs" style="font-size: 0.80rem;">{{ $Serverstats['totalconnections'] }}</span></p>
                        <hr>
                        <p><span class="text-indigo-400 text-center" style="font-size: 0.80rem;">💻 CPU :</span> <span class="text-gray-400 text-center" style="font-size: 0.80rem;">{{ $Serverstats['cpuModel'] }}</span></p>
                        <p><span class="text-indigo-400 text-center" style="font-size: 0.80rem;">🖥️ CPU Cores:</span> <span class="text-gray-400 text-center" style="font-size: 0.80rem;">{{ $Serverstats['cpuCores'] }}</span></p>
                        <hr>
                        <p><span class="text-indigo-400 text-center" style="font-size: 0.80rem;">🌡️ RAM Total:</span> <span class="text-gray-400 text-center" style="font-size: 0.80rem;">{{ $Serverstats['FixedmemoryTotal'] }} GB</span></p>
                        <p><span class="text-indigo-400 text-center" style="font-size: 0.80rem;">🌡️ RAM Used:</span> <span class="text-gray-400 text-center" style="font-size: 0.80rem;">{{ $Serverstats['FixedmemoryUsage'] }} GB</span></p>
                        <p><span class="text-indigo-400 text-center" style="font-size: 0.80rem;">🌡️ RAM Available:</span> <span class="text-gray-400 text-center" style="font-size: 0.80rem;">{{ $Serverstats['FixedmemoryAvailable'] }} GB</span></p>
                        <hr>
                        <p><span class="text-indigo-400 text-center" style="font-size: 0.80rem;">💽 Hard Disk Free:</span> <span class="text-gray-400 text-center" style="font-size: 0.80rem;">{{ $Serverstats['diskFree'] }} GB</span></p>
                        <p><span class="text-indigo-400 text-center" style="font-size: 0.80rem;">💽 Hard Disk Used:</span> <span class="text-gray-400 text-center" style="font-size: 0.80rem;">{{ $Serverstats['diskUsed'] }} GB</span></p>
                        <p><span class="text-indigo-400 text-center" style="font-size: 0.80rem;">💽 Hard Disk Total:</span> <span class="text-gray-400 text-center" style="font-size: 0.80rem;">{{ $Serverstats['FixeddiskTotal'] }} GB</span></p>
            
                        <hr>
                        <p><span class="text-indigo-400 text-center" style="font-size: 0.80rem;">ℹ️ PHP Verison:</span> <span class="text-gray-400 text-center" style="font-size: 0.80rem;">{{ $Serverstats['phpverison'] }} </span></p>
                        <p><span class="text-indigo-400 text-center" style="font-size: 0.80rem;">ℹ️ Node Verison:</span> <span class="text-gray-400 text-center" style="font-size: 0.80rem;">{{ $Serverstats['node'] }} </span></p>
                        <p><span class="text-indigo-400 text-center" style="font-size: 0.80rem;">ℹ️ SQL Verison:</span> <span class="text-gray-400 text-center" style="font-size: 0.80rem;">{{ $Serverstats['sql'] }} </span></p>
                        
                        <hr>
                        <p><span class="text-indigo-400 text-center" style="font-size: 0.80rem;">☑️ Memory Limit:</span> <span class="text-gray-400 text-center" style="font-size: 0.80rem;">{{ $Serverstats['memory_limit'] }} </span></p>
                        <p><span class="text-indigo-400 text-center" style="font-size: 0.80rem;">☑️ Max Execution Time:</span> <span class="text-gray-400 text-center" style="font-size: 0.80rem;">{{ $Serverstats['max_execution_time'] }} </span></p>
                        <p><span class="text-indigo-400 text-center" style="font-size: 0.80rem;">☑️ Post Max Size:</span> <span class="text-gray-400 text-center" style="font-size: 0.80rem;">{{ $Serverstats['post_max_size'] }} </span></p>
                        <p><span class="text-indigo-400 text-center" style="font-size: 0.80rem;">☑️ Max File Uploads:</span> <span class="text-gray-400 text-center" style="font-size: 0.80rem;">{{ $Serverstats['max_file_uploads'] }} </span></p>
                        <p><span class="text-indigo-400 text-center" style="font-size: 0.80rem;">☑️ Upload Max Filesize:</span> <span class="text-gray-400 text-center" style="font-size: 0.80rem;">{{ $Serverstats['upload_max_filesize'] }} </span></p>
    
                </fieldset>
            </div>
            
            &nbsp;&nbsp;&nbsp;

            <div class="w-1/2 mx-auto mr-22"> 
                <fieldset class="border border-gray-800 p-3 rounded">
                    <legend class="text-sm text-gray-600 font-semibold uppercase tracking-widest">PING CHECK</legend>
                        

                    <div class="flex flex-wrap w-full mb-5 justify-center">

                        <div class="relative">
                            <input class="bw-input dark:bg-gray-800 dark:text-gray-200 text-center text-xs overflow-hidden shadow-lg sm:rounded-lg  h-8  border  dark:border-slate-700 " style="font-weight: 600;background:rgb(19 23 29);" type="text" id="pingidinput" name="searchByInt" value="" autocomplete="off" placeholder="ip address" pattern="\d*" inputmode="numeric" required>     
                        </div>
                        
                        <button onclick="PingCheckStart()" id="pingButton" type="submit" class="inline-flex items-center px-4 py-2 h-8 ml-2 disabled:opacity-50 disabled:pointer-events-none  bg-gray-900 dark:bg-gray-900 border  border-indigo-400 rounded-md font-semibold text-xs text-white dark:text-indigo-400 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-indigo-400 dark:hover:text-gray-900  dark:active:text-indigo-400">
                            EXECUTE
                        </button>

                    </div>
                      <div id="loding_screen"><br><br></div> 

                    <textarea id="pingTextArea" rows="23"  readonly class="block w-full text-sm text-gray-900 text-left bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-800 dark:border-gray-600 dark:placeholder-gray-400 dark:text-gray-200 dark:focus:ring-blue-500 dark:focus:border-blue-500" style="white-space: pre-line;background:rgb(19 23 29);">
                         
                    </textarea>


                </fieldset>
            </div>


        </div>

    </div>
 

    <script>
            function PingCheckStart()
            {
                document.getElementById("pingTextArea").innerHTML = '';
                document.getElementById("loding_screen").innerHTML = `
                    <center>
                        <svg  width="30" height="30" fill="#5c6bc0" class="mr-2 animate-spin" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg">
                            <path d="M526 1394q0 53-37.5 90.5t-90.5 37.5q-52 0-90-38t-38-90q0-53 37.5-90.5t90.5-37.5 90.5 37.5 37.5 90.5zm498 206q0 53-37.5 90.5t-90.5 37.5-90.5-37.5-37.5-90.5 37.5-90.5 90.5-37.5 90.5 37.5 37.5 90.5zm-704-704q0 53-37.5 90.5t-90.5 37.5-90.5-37.5-37.5-90.5 37.5-90.5 90.5-37.5 90.5 37.5 37.5 90.5zm1202 498q0 52-38 90t-90 38q-53 0-90.5-37.5t-37.5-90.5 37.5-90.5 90.5-37.5 90.5 37.5 37.5 90.5zm-964-996q0 66-47 113t-113 47-113-47-47-113 47-113 113-47 113 47 47 113zm1170 498q0 53-37.5 90.5t-90.5 37.5-90.5-37.5-37.5-90.5 37.5-90.5 90.5-37.5 90.5 37.5 37.5 90.5zm-640-704q0 80-56 136t-136 56-136-56-56-136 56-136 136-56 136 56 56 136zm530 206q0 93-66 158.5t-158 65.5q-93 0-158.5-65.5t-65.5-158.5q0-92 65.5-158t158.5-66q92 0 158 66t66 158z">
                            </path>
                        </svg>
                    </center><br>`;

                
                $("#pon_parameters_refresh_button").prop("disabled", true);
                $("#pingButton").addClass("opacity-50 pointer-events-none"); 
                     
                
                let ip   = document.getElementById("pingidinput").value;

                $.ajax({
                    url: "/Ping",
                    type: "GET",
                    data:{'ip':ip},
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    dataType: "json",
                    success: function(result)
                    {  
                        if(result.error)
                        {
                            document.getElementById("loding_screen").innerHTML = '<center><br><p class="text-red-400">'+ result.error +'</p></center>';
                            $("#pingButton").removeAttr("disabled");
                            $("#pingButton").removeClass("opacity-50 pointer-events-none");
                            document.getElementById("pingTextArea").innerHTML = '';
                        }
                        else
                        {
                            $("#pingButton").removeAttr("disabled");
                            $("#pingButton").removeClass("opacity-50 pointer-events-none");
                            document.getElementById("pingTextArea").innerHTML = result.ping_output;
                            document.getElementById("loding_screen").innerHTML = `<br><br>`;
                        }
                        
                    },
                    error: function (xhr, status, error) 
                    {
                        $("#pingButton").removeAttr("disabled");
                        $("#pingButton").removeClass("opacity-50 pointer-events-none");
                        document.getElementById("pingTextArea").innerHTML = '';
                        if (xhr.status === 401) 
                        {
                            showNotification('Ping Error', 'You dont have permissions to perform this action','warning',3);
                        }
                        else
                        {
                            showNotification('Ping Error',error,'warning',3);
                        }      
                    }
                });
            }


            let cpuType  = document.getElementById("cpuModel").value;
            let cpuCores = document.getElementById("cpuCores").value;
            let cpuUsed  = document.getElementById("cpuUsage").value;

            let FixedmemoryUsage  = document.getElementById("FixedmemoryUsage").value;
            let FixedmemoryTotal  = document.getElementById("FixedmemoryTotal").value;     

            let diskUsed        = document.getElementById("diskUsed").value;
            let FixeddiskTotal  = document.getElementById("FixeddiskTotal").value;   
      
            Highcharts.chart('cpuCont', {
            chart: {
                type: 'gauge',
                plotBorderWidth: 0,
                plotBackgroundColor: {
                    linearGradient: { x1: 0, y1: 0, x2: 0, y2: 1 },
                    stops: [
                        [0, '#FFF4C6'],
                        [0.3, '#FFFFFF'],
                        [1, '#FFF4C6']
                    ]
                },
                plotBackgroundImage: null,
                height: 200
            },

            title: {
                text: 'S Y S T E M',
                style: {color: '#818cf8' }
            },

            pane: [{
                startAngle: -55,
                endAngle: 55,
                background: null,
                center: ['20%', '145%'],
                size: 300
            }, {
                startAngle: -55,
                endAngle: 55,
                background: null,
                center: ['50%', '145%'],
                size: 300
            },
            {
                startAngle: -55,
                endAngle: 55,
                background: null,
                center: ['80%', '145%'],
                size: 300
            }
            ],

            exporting: {
                enabled: false
            },

            tooltip: {
                enabled: false
            },

            yAxis: [{
                min: 0,
                max: 100,
                minorTickPosition: 'outside',
                tickPosition: 'outside',
                labels: {
                    rotation: 'auto',
                    distance: 20
                },
                plotBands: [{
                    from: 75,
                    to: 100,
                    color: '#C02316',
                    innerRadius: '100%',
                    outerRadius: '105%'
                }],
                pane: 0,
                title: {
                    text: 'CPU',
                    y: -40
                }
            }, {
                min: 0,
                max: parseFloat(FixedmemoryTotal),
                minorTickPosition: 'outside',
                tickPosition: 'outside',
                labels: {
                    rotation: 'auto',
                    distance: 20
                },
                plotBands: [{
                    from: 8,
                    to: parseFloat(FixedmemoryTotal),
                    color: '#C02316',
                    innerRadius: '100%',
                    outerRadius: '105%'
                }],
                pane: 1,
                title: {
                    text: 'MEMORY',
                    y: -40
                }
            }, {
                min: 0,
                max: parseFloat(FixeddiskTotal),
                minorTickPosition: 'outside',
                tickPosition: 'outside',
                labels: {
                    rotation: 'auto',
                    distance: 20
                },
                plotBands: [{
                    from: 30,
                    to: parseFloat(FixeddiskTotal),
                    color: '#C02316',
                    innerRadius: '100%',
                    outerRadius: '105%'
                }],
                pane: 2,
                title: {
                    text: 'DISK',
                    y: -40
                }
            }],

            plotOptions: {
                gauge: {
                    dataLabels: {
                        enabled: true,
                    },
                    dial: {
                        radius: '100%'
                    }
                }
            },

            series: [{
                name: 'Channel A',
                data: [parseFloat(cpuUsed)],
                yAxis: 0,
                dataLabels: {format: '{y}%'}
            }, {
                name: 'Channel B',
                data: [ parseFloat(FixedmemoryUsage)],
                yAxis: 1,
                dataLabels: {format: '{y}GB'}
            },
            {
                name: 'Channel C',
                data: [parseFloat(diskUsed)],
                yAxis: 2,
                dataLabels: {format: '{y}GB'}
            }]

        },
          
            function (chart) {
                setInterval(function () 
                {
                    $.ajax({
                    url: "/UpdateHealth",
                    type: "GET",
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    dataType: "json",
                    success: function(result) 
                    { 
                        // Assuming result contains the updated values
                        const cpu = result.cpuUsage;
                        const ram = result.memoryUsage;
                        const disk = result.diskUsage;

                        // Update chart series with new data
                        chart.series[0].points[0].update(cpu);
                        chart.series[1].points[0].update(ram);
                        chart.series[2].points[0].update(disk);
                    },
                    error: function(xhr, status, error) {
                        console.error("Error fetching data:", error);
                    }
                });
                }, 1000);

            });


 
        

    </script>

</x-app-layout>