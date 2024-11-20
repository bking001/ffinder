<?php

use App\Http\Controllers\PRTGcontoller;
use App\Http\Controllers\OptionPageController;
use App\Http\Controllers\DevicesPageController;
use App\Http\Controllers\LogsController;
use App\Http\Controllers\MikrotikController;
use App\Http\Controllers\Privillages;
use App\Http\Controllers\OLT_SECTOR;
use App\Http\Controllers\airsoft;
use App\Http\Controllers\TMScontroller;
use App\Http\Controllers\PortForwardController;
use App\Http\Controllers\chatController;
use App\Http\Controllers\sshController;
use App\Http\Controllers\PonsPageController;
use App\Http\Controllers\CRON;

use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Route;
use Illuminate\View\View;

use App\Helpers\MikrotikFunctions;
use App\Models\PrivilegesModel;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

use Illuminate\Support\Facades\Http;

Route::get('/', function ():View {
    if (Auth::check())
    {
        return view('dashboard');
    }
    else
    {
        return view('auth.login');
    }
});



Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/OLT', function () {
    return view('olt');
})->middleware(['auth', 'verified'])->name('OLT');

Route::get('/Options',function (){
    PrivilegesModel::PrivCheck('Priv_Install');
    return view('options');
})->middleware(['auth','verified'])->name('Options');

Route::get('/GlobalSearch', function () {
    return view('globalsearch');
})->middleware(['auth', 'verified'])->name('GlobalSearch');

 

Route::get('/bug-report', function () {
    return view('bugg');
})->middleware(['auth', 'verified'])->name('bug-data');

Route::middleware('auth')->group(function ()
{

    Route::get('/Health',                                       [Controller::class,'MONITOR'])->name('server.monitor');
    Route::get('/UpdateHealth',                                 [Controller::class,'UPDATE_MONITOR'])->name('server.update');
    Route::get('/Ping',                                         [Controller::class,'pingAddress'])->name('server.pingAddress');
     
    
    Route::get('/schedule',                                     [CRON::class, 'CronTable'])->name('schedule');
    Route::get('/stopScheduler',                                [CRON::class, 'stop'])->name('startschedule');
    Route::get('/startScheduler',                               [CRON::class, 'start'])->name('stopschedule');


    Route::get('/Chat-Data',                                    [chatController::class, 'ChatData'])->name('Chat.ChatData');
    Route::get('/Chat-Add-Message',                             [chatController::class, 'ChatWriteMessage'])->name('Chat.ChatWriteMessage');
    Route::post('/Chat-Add-Image',                              [chatController::class, 'ChatWriteImage'])->name('Chat.ChatWriteImage');
    
    Route::get('/filterSearch',                                 [OLT_SECTOR::class, 'filterSearch'])->name('filterSearch');
    Route::get('/filterGetTarrifs',                             [OLT_SECTOR::class, 'GetTarriff'])->name('GetTarriff');
    Route::get('/filterGetRegions',                             [OLT_SECTOR::class, 'GetRegions'])->name('GetRegions');
    Route::get('/filterGetSUBRegions',                          [OLT_SECTOR::class, 'SUBRegions'])->name('SUBRegions');
     
    Route::get('/Parameters',                                   [OptionPageController::class, 'getDataAndView'])->name('parameters');
    Route::post('/Parameters',                                  [OptionPageController::class, 'parametersUpdate'])->name('Parameters.update');

    Route::get('/Devices',                                      [DevicesPageController::class, 'getDataAndView'])->name('Devices');
    Route::patch('/Devices',                                    [DevicesPageController::class, 'parametersUpdate'])->name('Device.update');
    Route::patch('/Devices-Delete',                             [DevicesPageController::class, 'Delete_Device'])->name('Device.delete');
    Route::post('/Devices-Create',                              [DevicesPageController::class, 'Create_Device'])->name('Device.create');
    Route::post('/Devices-GlobalEdit',                          [DevicesPageController::class, 'GlobalEdit'])->name('Device.GlobalEdit');
    Route::get('/Devices-search',                               [DevicesPageController::class, 'search'])->name('DeviceSearch');
    Route::get('/Devices-DefaultCreds',                         [DevicesPageController::class, 'DefaultCreds'])->name('DeviceDefaultCreds');
     
    Route::get('/na-onts-get',                                  [PonsPageController::class, 'naOntGet'])->name('naOntGet');
    Route::get('/NASearch',                                     [PonsPageController::class, 'NA_Search'])->name('NASearch');
    Route::get('/NA-OnuDescriptionEdit',                        [PonsPageController::class, 'NA_Description_Edit'])->name('NADescription');
    Route::get('/Delete-NA-Onu',                                [PonsPageController::class, 'NA_Delete'])->name('NADelete');
     
    Route::get('/FindDescription',                              [PonsPageController::class,'GlobalDescriptionSearch'])->name('Global.GlobalDescriptionSearch');

    Route::get('/duplicated-get',                               [PonsPageController::class, 'duplicatedGet'])->name('duplicatedGet');
    Route::get('/duplicated-count',                             [PonsPageController::class, 'duplicatedCount'])->name('duplicatedCount');
    Route::get('/duplicated-onts',                              [PonsPageController::class, 'duplicatedOnts'])->name('duplicatedOnts');
    Route::get('/ClonesSearch',                                 [PonsPageController::class, 'Clones_Search'])->name('ClonesSearch');
    Route::get('/Delete-Clone-Onu',                             [PonsPageController::class, 'Clones_Delete'])->name('ClonesDelete');
    Route::get('/CLONE-OnuDescriptionEdit',                     [PonsPageController::class, 'Clones_Description_Edit'])->name('ClonesDescription');
     
    
    Route::get('/PonStats',                                     [PonsPageController::class, 'getPonStats'])->name('PonStats');
    Route::get('/getPonsSelect',                                [PonsPageController::class, 'getPonsSelect'])->name('PonsSelect');
    Route::get('/MastOrder',                                    [PonsPageController::class, 'getMastOrder'])->name('MastOrder');
    Route::get('/PonArray',                                     [PonsPageController::class, 'getPonArray'])->name('getPonArray');
    Route::get('/PonArray-search',                              [PonsPageController::class, 'search'])->name('PonArraySearch');

    Route::get('/OnuStats',                                     [PonsPageController::class, 'getOnuStats'])->name('OnuStats');
    Route::get('/OnuStatsAllOnline',                            [PonsPageController::class, 'getOnuStatsllOnline'])->name('getOnuStatsllOnline');
    Route::get('/OnuStatsAllOffline',                           [PonsPageController::class, 'getOnuStatAllOffline'])->name('getOnuStatAllOffline');
    Route::get('/OnuStatsAllHighDbm',                           [PonsPageController::class, 'getOnuStatsAllHighDbm'])->name('getOnuStatsAllHighDbm');
    Route::get('/OnuStatsAllLos',                               [PonsPageController::class, 'getOnuStatsAllLos'])->name('getOnuStatsAllLos');
    Route::get('/OnuStatsSearch',                               [PonsPageController::class, 'OnuStat_Search'])->name('OnuStatsSearch');
    Route::get('/OnuStatsAdvancedSearch',                       [PonsPageController::class, 'OnuStat_AdvancedSearch'])->name('OnuStatsAdvancedSearch');
    Route::get('/Export/Exel/OnuStats',                         [PonsPageController::class, 'OnuStat_Export_Exel'])->name('OnuStatsExel');
    Route::get('/Export/Csv/OnuStats',                          [PonsPageController::class, 'OnuStat_Export_Csv'])->name('OnuStatsCsv');
     
     
    Route::get('/Masts-search',                                 [DevicesPageController::class, 'mast_search'])->name('MastsSearch');
    Route::get('/Masts-Create',                                 [DevicesPageController::class, 'mast_add'])->name('mast.add');
    Route::post('/Masts-Delete',                                [DevicesPageController::class, 'mast_delete'])->name('mast.delete');
    Route::post('/Masts-Update',                                [DevicesPageController::class, 'mast_update'])->name('mast.update');
    Route::get('/Masts',                                        [DevicesPageController::class, 'masts'])->name('mast_table');

    Route::get('/privilege',                                    [Privillages::class,'ViewTable'])->name('Privilege.logs');
    Route::post('/privilege',                                   [Privillages::class,'PrivSearch'])->name('Privilege.search');
    Route::post('/privilegeOfforon',                            [Privillages::class,'Disable_Enabled_Priv'])->name('Privilege.Switcher');


    Route::get('/airsoft-search',                               [airsoft::class,'search'])->name('airsoft.search');
    Route::get('/airsoft-macvendoor',                           [airsoft::class,'macvendoor'])->name('airsoft.macvendoor');
    Route::get('/airsoft-comments',                             [airsoft::class,'comments'])->name('airsoft.comments');
    Route::get('/airsoft-tasks',                                [airsoft::class,'tasks'])->name('airsoft.tasks');
    Route::get('/airsoft-coordinates',                          [airsoft::class,'coordinates'])->name('airsoft.coordinates');
    Route::get('/pon-coordinates',                              [airsoft::class,'PONcoordinates'])->name('airsoft.fullPONcoordinates');
    Route::get('/swich-coordinates',                            [airsoft::class,'SWICHcoordinates'])->name('airsoft.fullSWICHcoordinates');
    Route::get('/airsoft-send-comment',                         [airsoft::class,'SendComment'])->name('airsoft.cooSendCommentrdinates');

     
    Route::get('/Tasks-Monitoring',                             [airsoft::class,'Monitoring_View'])->name('airsoft.Monitoring_View');
    Route::get('/Task-History',                                 [airsoft::class,'Task_History'])->name('airsoft.Task_History');
    Route::get('/Task-Stop',                                    [airsoft::class,'Task_Stop'])->name('airsoft.Task_Stop');
    Route::get('/Task-Restore',                                 [airsoft::class,'Task_Restore'])->name('airsoft.Task_Restore');
    Route::get('/TaskSearch',                                   [airsoft::class,'Task_Search'])->name('TaskSearch');
    Route::get('/ArchiveTaskSearch',                            [airsoft::class,'ArchiveTask_Search'])->name('ArchiveTaskSearch');
    Route::get('/archivedTasks',                                [airsoft::class,'archived_Tasks'])->name('archivedTasks');
    Route::get('/Same',                                         [airsoft::class,'SameTasks'])->name('SameTasks');
 
     
    Route::get('/Task-TEST',                                    [airsoft::class,'Monitoring_TEST'])->name('airsoft.Monitoring_TEST');

    Route::get('/prtg-search',                                  [PRTGcontoller::class,'search'])->name('prtg.search');
    Route::get('/prtg-graph',                                   [PRTGcontoller::class,'graph'])->name('prtg.graph');
    

    Route::get('/mikrotik-search',                              [MikrotikController::class,'search'])->name('mikrotik.search');
    
    Route::get('/port-forward-search',                          [PortForwardController::class,'search'])->name('forward.search');
    Route::get('/port-forward-custom-port-search',              [PortForwardController::class,'custom_port_search'])->name('forward.custom_port_search');
    Route::get('/port-forward-add',                             [PortForwardController::class,'port_add'])->name('forward.PortAdd');
    Route::get('/port-forward-delete',                          [PortForwardController::class,'port_delete'])->name('forward.Portdelete');
    Route::get('/port-forward-change',                          [PortForwardController::class,'privat_address_change'])->name('forward.privat_address_change');
    Route::get('/port-forward-edit',                            [PortForwardController::class,'port_forward_edit'])->name('forward.port_forward_edit');
 

    Route::get('/tvip-check',                                   [TMScontroller::class,'tvipcheck'])->name('tvip.check');
    Route::get('/tvip-tarriffromaccount',                       [TMScontroller::class,'tarriffromaccount'])->name('tvip.tarriffromaccount');
    Route::get('/tvip-tarrifchange',                            [TMScontroller::class,'tviptarrifchange'])->name('tvip.tarrifchange');
    Route::get('/tvip-tarrif-delete',                           [TMScontroller::class,'tarrifdelete'])->name('tvip.tarrifdelete');
    Route::get('/tvip-tarrif-create',                           [TMScontroller::class,'tarrifcreate'])->name('tvip.tarrifcreate');
    Route::get('/tvip-restart',                                 [TMScontroller::class,'tviprestart'])->name('tvip.restart');
    Route::get('/tvip-update',                                  [TMScontroller::class,'tvipupdate'])->name('tvip.update');
    Route::get('/tvip-channel-list',                            [TMScontroller::class,'channelList'])->name('tvip.channelList');
    Route::get('/tvip-channel-change',                          [TMScontroller::class,'channelChange'])->name('tvip.channelChange');
    

    Route::get('/Type',                                         [OLT_SECTOR::class,'type'])->name('type.search');
    Route::get('/sshQuery',                                     [sshController::class,'CommandQuery'])->name('ssh.CommandQuery');

    Route::get('/BDCOM-ClientSide-Onuinfo',                     [OLT_SECTOR::class,'bdcom_client'])->name('bdcom.client');
    Route::get('/BDCOM-ClientSide-OnuPorts',                    [OLT_SECTOR::class,'bdcom_onuPorts'])->name('bdcom.onuPorts');
    Route::get('/BDCOM-ClientSide-OnuMacs',                     [OLT_SECTOR::class,'bdcom_onuMacs'])->name('bdcom.onuMacs');
    Route::get('/BDCOM-ClientSide-OnuRestart',                  [OLT_SECTOR::class,'bdcom_Onu_Restart'])->name('bdcom.onuRestart');
    Route::get('/BDCOM-ClientSide-OnuPortAdminStatusOFF',       [OLT_SECTOR::class,'bdcom_Onu_PortAdminStatusOFF'])->name('bdcom.PortAdminStatusOFF');
    Route::get('/BDCOM-ClientSide-OnuPortAdminStatusON',        [OLT_SECTOR::class,'bdcom_Onu_PortAdminStatusON'])->name('bdcom.PortAdminStatusON');
    Route::get('/BDCOM-ClientSide-PortVlanChange',              [OLT_SECTOR::class,'bdcom_Onu_PortVlanChange'])->name('bdcom.PortVlanChange');
    Route::get('/BDCOM-ClientSide-PonSelect',                   [OLT_SECTOR::class,'bdcom_clientside_pon_select'])->name('bdcom.PonSelect');
    Route::get('/BDCOM-ClientSide-PonData',                     [OLT_SECTOR::class,'bdcom_clientside_pon_data'])->name('bdcom.PonData');
    Route::get('/BDCOM-ClientSide-PonAllOnline',                [OLT_SECTOR::class,'bdcom_clientside_pon_PonAllOnline'])->name('bdcom.PonAllOnline');
    Route::get('/BDCOM-ClientSide-PonAllOffline',               [OLT_SECTOR::class,'bdcom_clientside_pon_PonAllOffline'])->name('bdcom.PonAllOffline');
    Route::get('/BDCOM-ClientSide-PonAllWireDown',              [OLT_SECTOR::class,'bdcom_clientside_pon_PonAllWireDown'])->name('bdcom.PonAllWireDown');
    Route::get('/BDCOM-ClientSide-PonAllPowerOff',              [OLT_SECTOR::class,'bdcom_clientside_pon_PonAllPowerOff'])->name('bdcom.PonAllPowerOff');
 
 
    Route::get('/HUAWEI-ClientSide-Onuinfo',                    [OLT_SECTOR::class,'huawei_client_Onuinfo'])->name('huawei.client');
    Route::get('/HUAWEI-ClientSide-OnuPorts',                   [OLT_SECTOR::class,'huawei_client_OnuPorts'])->name('huawei.onuPorts');
    Route::get('/HUAWEI-ClientSide-OnuMacs',                    [OLT_SECTOR::class,'huawei_client_OnuMacs'])->name('huawei.onuMacs');
    Route::get('/HUAWEI-ClientSide-OnuRestart',                 [OLT_SECTOR::class,'huawei_Onu_Restart'])->name('huawei.onuRestart');
    Route::get('/HUAWEI-ClientSide-OnuPortAdminStatusOFF',      [OLT_SECTOR::class,'huawei_Onu_PortAdminStatusOFF'])->name('huawei.PortAdminStatusOFF');
    Route::get('/HUAWEI-ClientSide-OnuPortAdminStatusON',       [OLT_SECTOR::class,'huawei_Onu_PortAdminStatusON'])->name('huawei.PortAdminStatusON');
    Route::get('/HUAWEI-ClientSide-PonSelect',                  [OLT_SECTOR::class,'huawei_clientside_pon_select'])->name('huawei.PonSelect');
    Route::get('/HUAWEI-ClientSide-PonData',                    [OLT_SECTOR::class,'huawei_clientside_pon_data'])->name('huawei.PonData');
    Route::get('/HUAWEI-ClientSide-PonAllOnline',               [OLT_SECTOR::class,'huawei_clientside_pon_PonAllOnline'])->name('huawei.PonAllOnline');
    Route::get('/HUAWEI-ClientSide-PonAllOffline',              [OLT_SECTOR::class,'huawei_clientside_pon_PonAllOffline'])->name('huawei.PonAllOffline');
    Route::get('/HUAWEI-ClientSide-PonAllWireDown',             [OLT_SECTOR::class,'huawei_clientside_pon_PonAllWireDown'])->name('huawei.PonAllWireDown');
    Route::get('/HUAWEI-ClientSide-PonAllPowerOff',             [OLT_SECTOR::class,'huawei_clientside_pon_PonAllPowerOff'])->name('huawei.PonAllPowerOff');
    Route::get('/HUAWEI-EPON-RECONFIG',                         [OLT_SECTOR::class,'huawei_epon_reconfigure'])->name('huawei.huawei_epon_reconfigure');
    Route::get('/HUAWEI-EPON-RECONFIG-FINISH',                  [OLT_SECTOR::class,'huawei_epon_reconfigure_finish'])->name('huawei.huawei_epon_reconfigure_finish');

    Route::get('/HUAWEI-TEST',             [OLT_SECTOR::class,'huawei_epon_test'])->name('huawei.huawei_epon_test');
     

    Route::get('/ZTE-ClientSide-Onuinfo',                       [OLT_SECTOR::class,'zte_client_Onuinfo'])->name('zte.client');
    Route::get('/ZTE-ClientSide-OnuPorts',                      [OLT_SECTOR::class,'zte_client_onuPorts'])->name('zte.onuPorts');
    Route::get('/ZTE-ClientSide-OnuMacs',                       [OLT_SECTOR::class,'zte_client_OnuMacs'])->name('zte.onuMacs');
    Route::get('/ZTE-ClientSide-OnuRestart',                    [OLT_SECTOR::class,'zte_client_OnuRestart'])->name('zte.OnuRestart');
    Route::get('/ZTE-ClientSide-OnuPortAdminStatusOFF',         [OLT_SECTOR::class,'zte_Onu_PortAdminStatusOFF'])->name('zte.PortAdminStatusOFF');
    Route::get('/ZTE-ClientSide-OnuPortAdminStatusON',          [OLT_SECTOR::class,'zte_Onu_PortAdminStatusON'])->name('zte.PortAdminStatusON');
    Route::get('/ZTE-ClientSide-PonSelect',                     [OLT_SECTOR::class,'zte_clientside_pon_select'])->name('zte.PonSelect');
    Route::get('/ZTE-ClientSide-PonData',                       [OLT_SECTOR::class,'zte_clientside_pon_data'])->name('zte.PonData');
    Route::get('/ZTE-ClientSide-PonAllOnline',                  [OLT_SECTOR::class,'zte_clientside_pon_PonAllOnline'])->name('zte.PonAllOnline');
    Route::get('/ZTE-ClientSide-PonAllOffline',                 [OLT_SECTOR::class,'zte_clientside_pon_PonAllOffline'])->name('zte.PonAllOffline');
    Route::get('/ZTE-ClientSide-PonAllWireDown',                [OLT_SECTOR::class,'zte_clientside_pon_PonAllWireDown'])->name('zte.PonAllWireDown');
    Route::get('/ZTE-ClientSide-PonAllPowerOff',                [OLT_SECTOR::class,'zte_clientside_pon_PonAllPowerOff'])->name('zte.PonAllPowerOff');


    Route::get('/VSOLUTION-ClientSide-Onuinfo',                 [OLT_SECTOR::class,'vsolution_client_Onuinfo'])->name('vsolution.client');
    Route::get('/VSOLUTION-ClientSide-OnuPorts',                [OLT_SECTOR::class,'vsolution_client_onuPorts'])->name('vsolution.onuPorts');
    Route::get('/VSOLUTION-ClientSide-OnuMacs',                 [OLT_SECTOR::class,'vsolution_client_OnuMacs'])->name('vsolution.onuMacs');
    Route::get('/VSOLUTION-ClientSide-OnuRestart',              [OLT_SECTOR::class,'vsolution_client_OnuRestart'])->name('vsolution.OnuRestart');
    Route::get('/VSOLUTION-ClientSide-OnuPortAdminStatusOFF',   [OLT_SECTOR::class,'vsolution_Onu_PortAdminStatusOFF'])->name('vsolution.PortAdminStatusOFF');
    Route::get('/VSOLUTION-ClientSide-OnuPortAdminStatusON',    [OLT_SECTOR::class,'vsolution_Onu_PortAdminStatusON'])->name('vsolution.PortAdminStatusON');
    Route::get('/VSOLUTION-ClientSide-PortVlanChange',          [OLT_SECTOR::class,'vsolution_Onu_PortVlanChange'])->name('vsolution.PortVlanChange');
    Route::get('/VSOLUTION-ClientSide-PonSelect',               [OLT_SECTOR::class,'vsolution_clientside_pon_select'])->name('vsolution.PonSelect');
    Route::get('/VSOLUTION-ClientSide-PonData',                 [OLT_SECTOR::class,'vsolution_clientside_pon_data'])->name('vsolution.PonData');
    Route::get('/VSOLUTION-ClientSide-PonAllOnline',            [OLT_SECTOR::class,'vsolution_clientside_pon_PonAllOnline'])->name('vsolution.PonAllOnline');
    Route::get('/VSOLUTION-ClientSide-PonAllOffline',           [OLT_SECTOR::class,'vsolution_clientside_pon_PonAllOffline'])->name('vsolution.PonAllOffline');
    Route::get('/VSOLUTION-ClientSide-PonAllWireDown',          [OLT_SECTOR::class,'vsolution_clientside_pon_PonAllWireDown'])->name('vsolution.PonAllWireDown');
    Route::get('/VSOLUTION-ClientSide-PonAllPowerOff',          [OLT_SECTOR::class,'vsolution_clientside_pon_PonAllPowerOff'])->name('vsolution.PonAllPowerOff');


    Route::get('/HSGQ-ClientSide-Onuinfo',                      [OLT_SECTOR::class,'hsgq_client_Onuinfo'])->name('hsgq.client');
    Route::get('/HSGQ-ClientSide-OnuPorts',                     [OLT_SECTOR::class,'hsgq_client_OnuPorts'])->name('hsgq.onuPorts');
    Route::get('/HSGQ-ClientSide-OnuMacs',                      [OLT_SECTOR::class,'hsgq_client_OnuMacs'])->name('hsgq.onuMacs');
    Route::get('/HSGQ-ClientSide-OnuRestart',                   [OLT_SECTOR::class,'hsgq_client_OnuRestart'])->name('hsgq.OnuRestart');
    Route::get('/HSGQ-ClientSide-OnuPortAdminStatusOFF',        [OLT_SECTOR::class,'hsgq_Onu_PortAdminStatusOFF'])->name('hsgq.PortAdminStatusOFF');
    Route::get('/HSGQ-ClientSide-OnuPortAdminStatusON',         [OLT_SECTOR::class,'hsgq_Onu_PortAdminStatusON'])->name('hsgq.PortAdminStatusON');
    Route::get('/HSGQ-ClientSide-PortVlanChange',               [OLT_SECTOR::class,'hsgq_Onu_PortVlanChange'])->name('hsgq.PortVlanChange');
    Route::get('/HSGQ-ClientSide-PonSelect',                    [OLT_SECTOR::class,'hsgq_clientside_pon_select'])->name('hsgq.PonSelect');
    Route::get('/HSGQ-ClientSide-PonData',                      [OLT_SECTOR::class,'hsgq_clientside_pon_data'])->name('hsgq.PonData');
    Route::get('/HSGQ-ClientSide-PonAllOnline',                 [OLT_SECTOR::class,'hsgq_clientside_pon_PonAllOnline'])->name('hsgq.PonAllOnline');
    Route::get('/HSGQ-ClientSide-PonAllOffline',                [OLT_SECTOR::class,'hsgq_clientside_pon_PonAllOffline'])->name('hsgq.PonAllOffline');
    Route::get('/HSGQ-ClientSide-PonAllWireDown',               [OLT_SECTOR::class,'hsgq_clientside_pon_PonAllWireDown'])->name('hsgq.PonAllWireDown');
    Route::get('/HSGQ-ClientSide-PonAllPowerOff',               [OLT_SECTOR::class,'hsgq_clientside_pon_PonAllPowerOff'])->name('hsgq.PonAllPowerOff');


    Route::get('/Sector',                                       [OLT_SECTOR::class,'sectorSearch'])->name('sector.search');
    Route::get('/Antenna-Kick',                                 [OLT_SECTOR::class,'AntennaKick'])->name('sector.AntennaKick');
    Route::get('/Antenna-Reboot',                               [OLT_SECTOR::class,'AntennaReboot'])->name('sector.AntennaReboot');
    Route::get('/UISP',                                         [OLT_SECTOR::class,'UISPSearch'])->name('UISP.search');



    Route::get('/ZYXEL-ClientSide-info',                        [OLT_SECTOR::class,'ZyxelSearch'])->name('Zyxel.client');
    Route::get('/ZYXEL-ClientSide-OnuPortAdminStatusOFF',       [OLT_SECTOR::class,'Zyxel_Onu_PortAdminStatusOFF'])->name('Zyxel.PortAdminStatusOFF');
    Route::get('/ZYXEL-ClientSide-OnuPortAdminStatusON',        [OLT_SECTOR::class,'Zyxel_Onu_PortAdminStatusON'])->name('Zyxel.PortAdminStatusON');
    Route::get('/ZYXEL-ClientSide-SwitchData',                  [OLT_SECTOR::class,'Zyxel_clientside_SwitchData'])->name('Zyxel.SwitchData');


    Route::get('/CISCO-ClientSide-info',                        [OLT_SECTOR::class,'ciscoSearch'])->name('cisco.client');
    Route::get('/CISCO-ClientSide-OnuPortAdminStatusOFF',       [OLT_SECTOR::class,'cisco_Onu_PortAdminStatusOFF'])->name('cisco.PortAdminStatusOFF');
    Route::get('/CISCO-ClientSide-OnuPortAdminStatusON',        [OLT_SECTOR::class,'cisco_Onu_PortAdminStatusON'])->name('cisco.PortAdminStatusON');
    Route::get('/CISCO-ClientSide-SwitchData',                  [OLT_SECTOR::class,'cisco_clientside_SwitchData'])->name('cisco.SwitchData');

    Route::get('/MIKROTIK-ROUTER-ClientSide-info',              [OLT_SECTOR::class,'MikrotikRouter'])->name('MikrotikRouter.client');
    Route::get('/MIKROTIK-ROUTER-ClientSide-PortOff',           [OLT_SECTOR::class,'MikrotikRouterPortOff'])->name('MikrotikRouterPortOff.client');
    Route::get('/MIKROTIK-ROUTER-ClientSide-PortOn',            [OLT_SECTOR::class,'MikrotikRouterPortOn'])->name('MikrotikRouterPortOn.client');

    Route::get('/all-ont-History',                              [airsoft::class,'AllOntHistory'])->name('search.AllOntHistory');
    Route::get('/all-antenna-History',                          [airsoft::class,'AllAntennaHistory'])->name('search.AllAntennaHistory');
    Route::get('/pon-ont-History',                              [airsoft::class,'PonOntHistory'])->name('search.PonOntHistory');


    Route::get('/History-January',                              [airsoft::class,'January'])->name('search.January');
    Route::get('/History-February',                             [airsoft::class,'February'])->name('search.February');
    Route::get('/History-March',                                [airsoft::class,'March'])->name('search.March');
    Route::get('/History-April',                                [airsoft::class,'April'])->name('search.April');
    Route::get('/History-May',                                  [airsoft::class,'May'])->name('search.May');
    Route::get('/History-June',                                 [airsoft::class,'June'])->name('search.June');
    Route::get('/History-July',                                 [airsoft::class,'July'])->name('search.July');
    Route::get('/History-August',                               [airsoft::class,'August'])->name('search.August');
    Route::get('/History-September',                            [airsoft::class,'September'])->name('search.September');
    Route::get('/History-October',                              [airsoft::class,'October'])->name('search.October');
    Route::get('/History-November',                             [airsoft::class,'November'])->name('search.November');
    Route::get('/History-December',                             [airsoft::class,'December'])->name('search.December');

    
    Route::get('/History-Pon-January',                          [airsoft::class,'Pon_January'])->name('search.PonJanuary');
    Route::get('/History-Pon-February',                         [airsoft::class,'Pon_February'])->name('search.PonFebruary');
    Route::get('/History-Pon-March',                            [airsoft::class,'Pon_March'])->name('search.PonMarch');
    Route::get('/History-Pon-April',                            [airsoft::class,'Pon_April'])->name('search.PonApril');
    Route::get('/History-Pon-May',                              [airsoft::class,'Pon_May'])->name('search.PonMay');
    Route::get('/History-Pon-June',                             [airsoft::class,'Pon_June'])->name('search.PonJune');
    Route::get('/History-Pon-July',                             [airsoft::class,'Pon_July'])->name('search.PonJuly');
    Route::get('/History-Pon-August',                           [airsoft::class,'Pon_August'])->name('search.PonAugust');
    Route::get('/History-Pon-September',                        [airsoft::class,'Pon_September'])->name('search.PonSeptember');
    Route::get('/History-Pon-October',                          [airsoft::class,'Pon_October'])->name('search.PonOctober');
    Route::get('/History-Pon-November',                         [airsoft::class,'Pon_November'])->name('search.PonNovember');
    Route::get('/History-Pon-December',                         [airsoft::class,'Pon_December'])->name('search.PonDecember');
    

});

require __DIR__.'/auth.php';
