<x-app-layout>


    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">  
                <div class="max-w-4xl mx-auto">

                                        
                        <div id="forAutoScroll" class="mx-auto sm:px-6 lg:px-8 overflow-x-hidden bg-gray-300/50 dark:bg-gray-800/50 rounded-lg shadow-lg" style="height: 45rem;max-width: 100rem;">                                                    
                            <div class="w-full mt-10" id="chatMainBody">
               
                            </div>
                        </div>
                        <br>


                        <div class="w-full mb-4 border border-gray-200 rounded-lg bg-gray-50 dark:bg-gray-700 dark:border-gray-800">
                            <div class="px-4 py-2 bg-white rounded-t-lg dark:bg-gray-800">
                                <label for="comment" class="sr-only">Your comment</label>
                                <textarea id="ChatMessageInput" rows="3" class="w-full px-0 text-sm text-gray-900 bg-white border-0 dark:bg-gray-800 focus:ring-0 dark:text-white dark:placeholder-gray-400" placeholder="Write a comment..." required ></textarea>
                            </div>
                            <div class="flex items-center justify-end px-3 py-2 border-t dark:border-gray-600">
                                <div class="flex ps-0 space-x-1 sm:ps-2">    
                                    <label for="fileInput">
                                        <svg  class="cursor-pointer w-6 h-6 mt-1" xmlns="http://www.w3.org/2000/svg"   viewBox="0 0 22 22" fill="none">
                                            <g id="Attach 01">
                                                <g id="Vector">
                                                    <path d="M14.9332 7.79175L8.77551 14.323C8.23854 14.8925 7.36794 14.8926 6.83097 14.323C6.294 13.7535 6.294 12.83 6.83097 12.2605L12.9887 5.72925M12.3423 6.41676L13.6387 5.04176C14.7126 3.90267 16.4538 3.90267 17.5277 5.04176C18.6017 6.18085 18.6017 8.02767 17.5277 9.16676L16.2314 10.5418M16.8778 9.85425L10.72 16.3855C9.10912 18.0941 6.49732 18.0941 4.88641 16.3855C3.27549 14.6769 3.27549 11.9066 4.88641 10.198L11.0441 3.66675" stroke="#9CA3AF" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" />
                                                    <path d="M14.9332 7.79175L8.77551 14.323C8.23854 14.8925 7.36794 14.8926 6.83097 14.323C6.294 13.7535 6.294 12.83 6.83097 12.2605L12.9887 5.72925M12.3423 6.41676L13.6387 5.04176C14.7126 3.90267 16.4538 3.90267 17.5277 5.04176C18.6017 6.18085 18.6017 8.02767 17.5277 9.16676L16.2314 10.5418M16.8778 9.85425L10.72 16.3855C9.10912 18.0941 6.49732 18.0941 4.88641 16.3855C3.27549 14.6769 3.27549 11.9066 4.88641 10.198L11.0441 3.66675" stroke="black" stroke-opacity="0.2" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" />
                                                    <path d="M14.9332 7.79175L8.77551 14.323C8.23854 14.8925 7.36794 14.8926 6.83097 14.323C6.294 13.7535 6.294 12.83 6.83097 12.2605L12.9887 5.72925M12.3423 6.41676L13.6387 5.04176C14.7126 3.90267 16.4538 3.90267 17.5277 5.04176C18.6017 6.18085 18.6017 8.02767 17.5277 9.16676L16.2314 10.5418M16.8778 9.85425L10.72 16.3855C9.10912 18.0941 6.49732 18.0941 4.88641 16.3855C3.27549 14.6769 3.27549 11.9066 4.88641 10.198L11.0441 3.66675" stroke="black" stroke-opacity="0.2" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" />
                                                </g>
                                            </g>
                                        </svg>
                                    </label>
                                    <input id="fileInput" type="file"  accept="image/*" hidden />
                                    <button onclick="sendChatMessage()" type="submit" class="inline-flex items-center px-4 py-2 h-8 ml-2  bg-gray-700 dark:bg-indigo-400 border border-slate-300 shadow-lg dark:border-indigo-400 rounded-full font-semibold text-xs text-gray-200 dark:text-gray-900 uppercase tracking-widest hover:bg-slate-400 dark:hover:bg-indigo-400 dark:hover:text-gray-900  dark:active:text-indigo-400">
                                        SEND
                                    </button>
                                </div>
                            </div>
                        </div>
                </div>
        </div>
    </div>

    <input id="Counter" value="0" hidden />

    <x-bladewind.notification position="top right" />
    <x-bladewind.notification />
    
    <script>

        document.getElementById("chatMainBody").innerHTML = `
            <br>
            <center>
            <svg  width="30" height="30" fill="#5c6bc0" class="mr-2 animate-spin" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg">
                <path d="M526 1394q0 53-37.5 90.5t-90.5 37.5q-52 0-90-38t-38-90q0-53 37.5-90.5t90.5-37.5 90.5 37.5 37.5 90.5zm498 206q0 53-37.5 90.5t-90.5 37.5-90.5-37.5-37.5-90.5 37.5-90.5 90.5-37.5 90.5 37.5 37.5 90.5zm-704-704q0 53-37.5 90.5t-90.5 37.5-90.5-37.5-37.5-90.5 37.5-90.5 90.5-37.5 90.5 37.5 37.5 90.5zm1202 498q0 52-38 90t-90 38q-53 0-90.5-37.5t-37.5-90.5 37.5-90.5 90.5-37.5 90.5 37.5 37.5 90.5zm-964-996q0 66-47 113t-113 47-113-47-47-113 47-113 113-47 113 47 47 113zm1170 498q0 53-37.5 90.5t-90.5 37.5-90.5-37.5-37.5-90.5 37.5-90.5 90.5-37.5 90.5 37.5 37.5 90.5zm-640-704q0 80-56 136t-136 56-136-56-56-136 56-136 136-56 136 56 56 136zm530 206q0 93-66 158.5t-158 65.5q-93 0-158.5-65.5t-65.5-158.5q0-92 65.5-158t158.5-66q92 0 158 66t66 158z">
                </path>
            </svg>
            </center>
        `;

        function ChatRefresh() 
        {
            $.ajax({
                url: "/Chat-Data",
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
                        document.getElementById("chatMainBody").innerHTML = `
                                <div class="w-1/2" style="background-color: rgb(133 77 14 / 0.1) !important; border: 0.5px solid  rgb(113 63 18 /1) !important; color:rgb(234 179 8 /1) !important; border-radius: 0.375rem !important; padding: 1rem !important; font-size: 0.875rem !important;" role="alert">
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
                                            <div style="margin-top: 0.25rem !important; font-size: 12px !important; color: rgb(161 98 7 / 1) !important;">${result.error }</div>
                                        </div>
                                    </div>
                                </div>
                            `;
                    }
                    else
                    {           

                        let body = '';
                        let autor = result.autor; 

                        for (var key in result.table) 
                        {
                            if(result.table[key].user == autor)
                            {
                                if(result.table[key].imgUrl)
                                {
                             
                                    body += `
                                            <div class="flex gap-2.5 justify-end">
                                                    <div class="">
                                                        <div class="grid mb-2">
                                                                    <h5 class="text-right text-gray-500 text-sm font-semibold leading-snug pb-1">You</h5>
                                                                <div class="px-3 py-2  rounded">
                                                                    <h2 class="text-gray-800 text-sm font-normal leading-snug">
                                                                        <img src="${result.table[key].imgUrl}" alt="Image" loading="lazy" class="w-full h-96 border none rounded" style="border-color:#111827 !important;">
                                                                    </h2>
                                                                </div>
                                                                <div class="justify-end items-center inline-flex">
                                                                    <h3 class="text-gray-500 text-xs font-normal leading-4 py-1">${result.table[key].messageTime}</h3>
                                                                </div>
                                                        </div>
                                                        
                                                        
                                                    </div>
                                                <img src="user-avatar.png" alt="Admin" loading="lazy" class="rounded-full w-10 h-11 object-cover dark:br-gray-100">
                                            </div>                               
                                    `;
                                }
                                else
                                {
                                    body += `
                                            <div class="flex gap-2.5 justify-end">
                                                        <div class="">
                                                            <div class="grid mb-2">
                                                                <h5 class="text-right text-gray-500 text-sm font-semibold leading-snug pb-1">You</h5>
                                                            <div class="px-3 py-2 bg-indigo-400 rounded">
                                                                <h2 class="whitespace-break-spaces text-gray-800 text-sm text-slate-900 font-normal leading-snug">${result.table[key].data_text}</h2>
                                                            </div>
                                                            <div class="justify-end items-center inline-flex">
                                                                <h3 class="text-gray-500 text-xs font-normal leading-4 py-1">${result.table[key].messageTime}</h3>
                                                            </div>
                                                        </div>
                                                        
                                                        
                                                    </div>
                                                    <img src="user-avatar.png" alt="Admin" loading="lazy" class="rounded-full w-10 h-11 object-cover dark:br-gray-100">
                                                </div>                               
                                    `;
                                }
 
                            }
                            else
                            {
                                if(result.table[key].imgUrl)
                                {
                                    
                                  
                                    body += `
                                            <div class="flex gap-2.5 mb-4">
                                                <img src="user-avatar.png" alt="Admin" loading="lazy" class="rounded-full w-10 h-11 object-cover dark:br-gray-100">
                                                <div class="grid">
                                                <h5 class="text-gray-400 text-sm font-semibold leading-snug pb-1">${result.table[key].user}</h5>
                                                <div class="w-full grid">
                                                    <div class="px-3.5 py-2 rounded justify-start  items-center gap-3 inline-flex">
                                                    <h5 class="text-gray-900 text-sm font-normal leading-snug">
                                                        <img src="${result.table[key].imgUrl}" alt="Image" loading="lazy" class="w-full h-96 border none rounded" style="border-color:#111827 !important;">
                                                    </h5>
                                                    </div>
                                                    <div class="justify-start items-center inline-flex mb-2.5">
                                                    <h6 class="text-gray-500 text-xs font-normal leading-4 py-1">${result.table[key].messageTime}</h6>
                                                    </div>
                                                </div>
                                                
                                                </div>
                                            </div>
                                        `;  
                                }
                                else
                                {
                                    body += `
                                        <div class="flex gap-2.5 mb-4">
                                            <img src="user-avatar.png" alt="Admin" loading="lazy" class="rounded-full w-10 h-11 object-cover dark:br-gray-100">
                                            <div class="grid">
                                            <h5 class="text-gray-400 text-sm font-semibold leading-snug pb-1">${result.table[key].user}</h5>
                                            <div class="w-full grid">
                                                <div class="px-3.5 py-2 bg-gray-300 rounded justify-start  items-center gap-3 inline-flex">
                                                <h5 class="whitespace-break-spaces text-gray-900 text-sm font-normal leading-snug">${result.table[key].data_text}</h5>
                                                </div>
                                                <div class="justify-start items-center inline-flex mb-2.5">
                                                <h6 class="text-gray-500 text-xs font-normal leading-4 py-1">${result.table[key].messageTime}</h6>
                                                </div>
                                            </div>
                                            
                                            </div>
                                        </div>
                                    `;  
                                }
                         

                            }
                        }
                
                        let Counter = document.getElementById("Counter").value;
                        if(result.count > Counter)
                        {
                            document.getElementById("chatMainBody").innerHTML = body;  
                            var chatMainBody = document.getElementById("forAutoScroll");
                            chatMainBody.lastElementChild.scrollIntoView({ behavior: 'smooth', block: 'end' });

                            document.getElementById("Counter").value =  result.count;
                        }
  
                    }
                },
                error: function (xhr, status, error) 
                {
                    document.getElementById("chatMainBody").innerHTML = `<center>
                                <div class="w-full sm:w-1/2  justify-center items-center" style="background-color: rgb(133 77 14 / 0.1) !important; border: 0.5px solid  rgb(113 63 18 /1) !important; color:rgb(234 179 8 /1) !important; border-radius: 0.375rem !important; padding: 1rem !important; font-size: 0.875rem !important;" role="alert">
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
                                            <div style="margin-top: 0.25rem !important; font-size: 12px !important; color: rgb(161 98 7 / 1) !important;">${error }</div>
                                        </div>
                                    </div>
                                </div></center>
                            `;
                }
            });
        }
        ChatRefresh();
        setInterval(ChatRefresh, 3000);

        

        function sendChatMessage()
        {
            ChatMessageInput = document.getElementById("ChatMessageInput").value;  
            document.getElementById("ChatMessageInput").value = ''; 


            if (ChatMessageInput.trim() !== '')
            {
                $.ajax({
                    url: "/Chat-Add-Message",
                    type: "GET",
                    data:{'ChatMessageInput':ChatMessageInput},
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    dataType: "json",
                    success: function(result)
                    {  
                        if(result.error)showNotification('Search Error', result.error,'warning',10);
                        else
                        {
                            let body = '';
                            let autor = result.autor; 

                            for (var key in result.table) 
                            {
                                if(result.table[key].user == autor)
                                {
                                    if(result.table[key].imgUrl)
                                    {
         
                                        body += `
                                                <div class="flex gap-2.5 justify-end">
                                                        <div class="">
                                                            <div class="grid mb-2">
                                                                        <h5 class="text-right text-gray-500 text-sm font-semibold leading-snug pb-1">You</h5>
                                                                    <div class="px-3 py-2  rounded">
                                                                        <h2 class="text-gray-800 text-sm font-normal leading-snug">
                                                                            <img src="${result.table[key].imgUrl}" alt="Image" loading="lazy" class="w-full h-96 rounded" style="border-color:#111827 !important;">
                                                                        </h2>
                                                                    </div>
                                                                    <div class="justify-end items-center inline-flex">
                                                                        <h3 class="text-gray-500 text-xs font-normal leading-4 py-1">${result.table[key].messageTime}</h3>
                                                                    </div>
                                                            </div>
                                                            
                                                            
                                                        </div>
                                                    <img src="user-avatar.png" alt="Admin" loading="lazy" class="rounded-full w-10 h-11 object-cover dark:br-gray-100">
                                                </div>                               
                                        `;
                                    }
                                    else
                                    {
                                        body += `
                                                <div class="flex gap-2.5 justify-end">
                                                            <div class="">
                                                                <div class="grid mb-2">
                                                                    <h5 class="text-right text-gray-500 text-sm font-semibold leading-snug pb-1">You</h5>
                                                                <div class="px-3 py-2 bg-indigo-400 rounded">
                                                                    <h2 class="whitespace-break-spaces text-gray-800 text-sm text-slate-900 font-normal leading-snug">${result.table[key].data_text}</h2>
                                                                </div>
                                                                <div class="justify-end items-center inline-flex">
                                                                    <h3 class="text-gray-500 text-xs font-normal leading-4 py-1">${result.table[key].messageTime}</h3>
                                                                </div>
                                                            </div>
                                                            
                                                            
                                                        </div>
                                                        <img src="user-avatar.png" alt="Admin" loading="lazy" class="rounded-full w-10 h-11 object-cover dark:br-gray-100">
                                                    </div>                               
                                        `;
                                    }
    
                                }
                                else
                                {
                                    if(result.table[key].imgUrl)
                                    {
           
                                        body += `
                                                <div class="flex gap-2.5 mb-4">
                                                    <img src="user-avatar.png" alt="Admin" loading="lazy" class="rounded-full w-10 h-11 object-cover dark:br-gray-100">
                                                    <div class="grid">
                                                    <h5 class="text-gray-400 text-sm font-semibold leading-snug pb-1">${result.table[key].user}</h5>
                                                    <div class="w-full grid">
                                                        <div class="px-3.5 py-2 rounded justify-start  items-center gap-3 inline-flex">
                                                        <h5 class="text-gray-900 text-sm font-normal leading-snug">
                                                            <img src="${result.table[key].imgUrl}" alt="Image" loading="lazy" class="w-full h-96 border none rounded" style="border-color:#111827 !important;">
                                                        </h5>
                                                        </div>
                                                        <div class="justify-start items-center inline-flex mb-2.5">
                                                        <h6 class="text-gray-500 text-xs font-normal leading-4 py-1">${result.table[key].messageTime}</h6>
                                                        </div>
                                                    </div>
                                                    
                                                    </div>
                                                </div>
                                            `;  
                                    }
                                    else
                                    {
                                        body += `
                                            <div class="flex gap-2.5 mb-4">
                                                <img src="user-avatar.png" alt="Admin" loading="lazy" class="rounded-full w-10 h-11 object-cover dark:br-gray-100">
                                                <div class="grid">
                                                <h5 class="text-gray-400 text-sm font-semibold leading-snug pb-1">${result.table[key].user}</h5>
                                                <div class="w-full grid">
                                                    <div class="px-3.5 py-2 bg-gray-300 rounded justify-start  items-center gap-3 inline-flex">
                                                    <h5 class="whitespace-break-spaces text-gray-900 text-sm font-normal leading-snug">${result.table[key].data_text}</h5>
                                                    </div>
                                                    <div class="justify-start items-center inline-flex mb-2.5">
                                                    <h6 class="text-gray-500 text-xs font-normal leading-4 py-1">${result.table[key].messageTime}</h6>
                                                    </div>
                                                </div>
                                                
                                                </div>
                                            </div>
                                        `;  
                                    }
                            

                                }
                            }
                            document.getElementById("chatMainBody").innerHTML = body;  
            
                            var chatMainBody = document.getElementById("forAutoScroll");
                            chatMainBody.lastElementChild.scrollIntoView({ behavior: 'smooth', block: 'end' });
                        }
                         
                    },
                    error: function (xhr, status, error) 
                    {
                        showNotification('Search Error ' + xhr.status, xhr.responseJSON.message,'error',10);
                    }
                });
            }
  
            var fileInput = document.getElementById('fileInput');
            if (fileInput && fileInput.files && fileInput.files.length > 0) 
            {
                var formData = new FormData();
                formData.append('image', fileInput.files[0]);
                document.getElementById('fileInput').value = '';
                fileInput = '';
                $.ajax({
                    url: "/Chat-Add-Image",
                    type: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    success: function(result)
                    {  
                        if(result.error)
                        {
                            showNotification('Image Upload Error ',result.error,'error',10);
                        }  
                    },
                    error: function (xhr, status, error) 
                    {
                        showNotification('Image Upload Error ' + xhr.status, xhr.responseJSON.message,'error',10);
                    }
                });

            }
  
        }
        
    </script> 

</x-app-layout>