<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB; 
use App\Models\Chat;

 

class chatController extends Controller
{
    static public function ChatData(REQUEST $request)
    {
        $data = DB::table('chat')->get();
 
        $username = $request->user()->name;
        
        $Count = DB::table('chat')->count();

        $userExists = DB::table('UsersChatCount')
        ->where('user', $username)
        ->exists();
 
        if ($userExists) 
        {   
            DB::table('UsersChatCount')
                ->where('user', $username)
                ->update(['ChatCount' => $Count]);
        } 
        else 
        {        
            DB::table('UsersChatCount')->insert([
                'user' => $username,
                'ChatCount' => $Count,
            ]);
        }
    
        return response()->json(['table' => $data, 'autor' => $username,'count' => $Count]);
    }

    static public function ChatCountData($user)
    {
        $data      = DB::table('UsersChatCount')->where('user', $user)->get();  
        if ($data->isNotEmpty())
        {
            $chatCount = $data->first()->ChatCount;
 
            $Count = DB::table('chat')->count();
            if((int)$chatCount !== (int)$Count)return 1;
            else return 0;
        }
        else
        {
            return 0;
        }
    }


    static public function ChatWriteMessage(REQUEST $request)
    { 
        $validator = validator()->make($request->only('ChatMessageInput'), [
            'ChatMessageInput' => 'required|string',
        ]);

        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }
     
        $username = $request->user()->name;
 

        DB::table('chat')->insert([
            'user'          => $username,
            'data_text'     => $request->input('ChatMessageInput'),
            'messageTime'   => DB::raw('now()'),
            'imgUrl'        => '',
        ]);


        $data   = DB::table('chat')->get();


        $Count      = DB::table('chat')->count();
        $userExists = DB::table('UsersChatCount')
        ->where('user', $username)
        ->exists();
 
        if ($userExists) 
        {   
            DB::table('UsersChatCount')
                ->where('user', $username)
                ->update(['ChatCount' => $Count]);
        } 
        else 
        {        
            DB::table('UsersChatCount')->insert([
                'user' => $username,
                'ChatCount' => $Count,
            ]);
        }
    
     
        return response()->json(['table' => $data, 'autor' => $username]);
    }

    public function ChatWriteImage(Request $request)
    { 
        $validator = validator()->make($request->all(), [
            'image' => 'required|image',
        ]);

        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

        $username = $request->user()->name;
        $image    = $request->file('image');

        $imageName = uniqid().'.'.$image->getClientOriginalExtension();

        $path = $image->storeAs('public/chat', $imageName);  

        if (!$path) 
        {
            return response()->json(['error' => 'File upload error'], 500);
        }


        $url = asset("storage/chat/{$imageName}");
         
        DB::table('chat')->insert([
            'user'          => $username,
            'messageTime'   => DB::raw('now()'),
            'imgUrl'        => $url,
        ]);

   
        $Count      = DB::table('chat')->count();
        $userExists = DB::table('UsersChatCount')
        ->where('user', $username)
        ->exists();
 
        if ($userExists) 
        {   
            DB::table('UsersChatCount')
                ->where('user', $username)
                ->update(['ChatCount' => $Count]);
        } 
        else 
        {        
            DB::table('UsersChatCount')->insert([
                'user' => $username,
                'ChatCount' => $Count,
            ]);
        }

        return response()->json(['success' => 'Image uploaded successfully']);
    }
        
}
