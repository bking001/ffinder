<?php
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use Illuminate\Support\Facades\Route;


use App\Http\Controllers\INSTALLController;
use App\Http\Controllers\TMScontroller;

Route::middleware('auth')->group(function ()
{
    Route::get('/FindMacSN',                                [INSTALLController::class,'GlobalSearch'])->name('Global.Search');
    Route::get('/OLT-LIST',                                 [INSTALLController::class,'oltList'])->name('oltList');
    Route::get('/OLT-LIST-AUTOFIND',                        [INSTALLController::class,'oltList_autofind'])->name('oltList_autofind');

    Route::get('/Uninstall-Search-Client',                  [INSTALLController::class,'uninstall_autofind'])->name('uninstall_autofind');
    Route::get('/Finish_Uninstall_Universal',               [INSTALLController::class,'uninstall_finish'])->name('uninstall_finish');
     
     
    Route::get('/InstallFindMacSN',                         [INSTALLController::class,'InstallMACSNSearch'])->name('Install.Search');
    Route::get('/InstallGponAutofind',                      [INSTALLController::class,'InstallGponAutofind'])->name('Install.InstallGponAutofind');
     
    Route::get('/GPS-ADD-CRM',                              [INSTALLController::class,'GpsAddCRM'])->name('Install.GpsAddCRM');

    Route::get('/tvip-account-search',                      [TMScontroller::class,'accountSearch'])->name('tvip.accountSearch');
    Route::get('/tvip-tarrif-search',                       [TMScontroller::class,'tarrifSearch'])->name('tvip.tarrifSearch');
    Route::get('/tvip-device-search',                       [TMScontroller::class,'deviceSearch'])->name('tvip.deviceSearch');
    Route::get('/tvip-account-create',                      [TMScontroller::class,'accountCreate'])->name('tvip.accountCreate');
    Route::get('/tvip-account-delete',                      [TMScontroller::class,'accountDelete'])->name('tvip.accountDelete');
    Route::get('/tvip-1111-devices',                        [TMScontroller::class,'unactivatedDevices'])->name('tvip.unactivatedDevices');
    Route::get('/tvip-device-delete',                       [TMScontroller::class,'devicetDelete'])->name('tvip.devicetDelete');
    Route::get('/tvip-device-bind',                         [TMScontroller::class,'devicetBind'])->name('tvip.devicetBind');
    Route::get('/tvip-search-by-mac',                       [TMScontroller::class,'SearchByMac'])->name('tvip.SearchByMac');


    Route::get('/airsoft-install-first',                    [INSTALLController::class,'AirsoftInstallFirst'])->name('Install.airsoft-first');
    Route::get('/airsoft-install-finish',                   [INSTALLController::class,'AirsoftInstallFinish'])->name('Install.airsoft-finish');
    Route::get('/airsoft-install-ponchange',                [INSTALLController::class,'AirsoftInstallponID'])->name('Install.airsoft-ponID');
    Route::get('/new-gps',                                  [INSTALLController::class,'GPS'])->name('Install.GPS');
    Route::get('/airsoft-antenna-install-first',            [INSTALLController::class,'AirsoftAntennaInstallFirst'])->name('Install.airsoft-antenna-first');
     

    Route::get('/antenna-andzebi',                          [INSTALLController::class,'andzebi'])->name('Install.andzebi');
    Route::get('/antenna-choosenMast',                      [INSTALLController::class,'choosenMast'])->name('Install.choosenMast');
    Route::get('/antenna-search-by-sectorMac',              [INSTALLController::class,'SearchBysectorMac'])->name('Install.SearchBysectorMac');
    Route::get('/antenna-search-by-sectorName',             [INSTALLController::class,'SearchBysectorName'])->name('Install.SearchBysectorName');
    Route::get('/antenna-search-by-sectorIP',               [INSTALLController::class,'SearchBysectorIP'])->name('Install.SearchBysectorIP');
    Route::get('/antenna-search-by-customer',               [INSTALLController::class,'SearchByCustomer'])->name('Install.SearchByCustomer');
    Route::get('/antenna-info-for-install',                 [INSTALLController::class,'SectorInfo'])->name('Install.SectorInfo');


    Route::get('/ethernet-andzebi',                         [INSTALLController::class,'EthernetAndzebi'])->name('Install.EthernetAndzebi');
    Route::get('/ethernet-switches-list',                   [INSTALLController::class,'EthernetSwitches'])->name('Install.EthernetSwitches');
    Route::get('/ethernet-switches-data',                   [INSTALLController::class,'EthernetSwitchData'])->name('Install.EthernetSwitchData');
    Route::get('/ethernet-port-adminOff',                   [INSTALLController::class,'ZyxelEthernetPortAdminOff'])->name('Install.EthernetPortAdminOff');
    Route::get('/ethernet-port-adminON',                    [INSTALLController::class,'ZyxelEthernetPortAdminON'])->name('Install.EthernetPortAdminON');
    Route::get('/ZYXEL-Install',                            [INSTALLController::class,'ZyxelInstall'])->name('Install.ZyxelInstall');
    Route::get('/ETHERNET-LIST',                            [INSTALLController::class,'SwitchesListInstall'])->name('Install.SwitchesListInstall');
    Route::get('/FindRooM',                                 [INSTALLController::class,'FindRooMInstall'])->name('Install.FindRooMInstall');
    Route::get('/ethernet-Cisco-port-adminOff',             [INSTALLController::class,'CiscoEthernetPortAdminOff'])->name('Install.CiscoEthernetPortAdminOff');
    Route::get('/ethernet-Cisco-port-adminON',              [INSTALLController::class,'CiscoEthernetPortAdminON'])->name('Install.CiscoEthernetPortAdminON');
    Route::get('/CISCO-Install',                            [INSTALLController::class,'CiscoInstall'])->name('Install.CiscoInstall');


    Route::get('/VSOLUTION-Install',                        [INSTALLController::class,'VsolutionInstall'])->name('Install.VsolutionInstall');
    Route::get('/VSOLUTION-AFTERINSTALL-INFO',              [INSTALLController::class,'VsolutionInfoByIfindex'])->name('Install.VsolutionInfoByIfindex');
    Route::get('/VSOLUTION-AFTERINSTALL-PORTS',             [INSTALLController::class,'VsolutionPortByIfindex'])->name('Install.VsolutionPortByIfindex');
    Route::get('/VSOLUTION-AFTERINSTALL-MAC',               [INSTALLController::class,'VsolutionMacByIfindex'])->name('Install.VsolutionMacByIfindex');
    Route::get('/VSOLUTION-Install-OnuRestart',             [INSTALLController::class,'VsolutionOnuRestartByIfindex'])->name('Install.VsolutionOnuRestartByIfindex');
    
    Route::get('/VSOLUTION-Install-OnuPortAdminStatusON',   [INSTALLController::class,'VsolutionOnuPortAdminStatusON'])->name('Install.VsolutionOnuPortAdminStatusON');
    Route::get('/VSOLUTION-Install-OnuPortAdminStatusOFF',  [INSTALLController::class,'VsolutionOnuPortAdminStatusOFF'])->name('Install.VsolutionOnuPortAdminStatusOFF');
    Route::get('/VSOLUTION-Install-PortVlanChange',         [INSTALLController::class,'VsolutionPortVlanChange'])->name('Install.VsolutionPortVlanChange');
    Route::get('/VSOLUTION-Install-Details',                [INSTALLController::class,'VsolutionDetails'])->name('Install.VsolutionDetails');


    Route::get('/HSGQ-Install',                             [INSTALLController::class,'HsgqInstall'])->name('Install.HsgqInstall');
    Route::get('/HSGQ-AFTERINSTALL-INFO',                   [INSTALLController::class,'HsgqInfoByIfindex'])->name('Install.HsgqInfoByIfindex');
    Route::get('/HSGQ-AFTERINSTALL-PORTS',                  [INSTALLController::class,'HsgqPortByIfindex'])->name('Install.HsgqPortByIfindex');
    Route::get('/HSGQ-AFTERINSTALL-MAC',                    [INSTALLController::class,'HsgqMacByIfindex'])->name('Install.HsgqMacByIfindex');

    Route::get('/HSGQ-Install-OnuRestart',                  [INSTALLController::class,'HsgqOnuRestartByIfindex'])->name('Install.HsgqOnuRestartByIfindex');
    Route::get('/HSGQ-Install-OnuPortAdminStatusON',        [INSTALLController::class,'HsgqOnuPortAdminStatusON'])->name('Install.HsgqOnuPortAdminStatusON');
    Route::get('/HSGQ-Install-OnuPortAdminStatusOFF',       [INSTALLController::class,'HsgqOnuPortAdminStatusOFF'])->name('Install.HsgqOnuPortAdminStatusOFF');
    Route::get('/HSGQ-Install-PortVlanChange',              [INSTALLController::class,'HsgqPortVlanChange'])->name('Install.HsgqPortVlanChange');
    Route::get('/HSGQ-Install-Details',                     [INSTALLController::class,'HsgqDetails'])->name('Install.HsgqDetails');


    Route::get('/BDCOM-Install',                            [INSTALLController::class,'BdcomInstall'])->name('Install.BdcomInstall');
    Route::get('/BDCOM-AFTERINSTALL-INFO',                  [INSTALLController::class,'BdcomInfoByIfindex'])->name('Install.BdcomInfoByIfindex');
    Route::get('/BDCOM-AFTERINSTALL-PORTS',                 [INSTALLController::class,'BdcomPortByIfindex'])->name('Install.BdcomPortByIfindex');
    Route::get('/BDCOM-AFTERINSTALL-MAC',                   [INSTALLController::class,'BdcomMacByIfindex'])->name('Install.BdcomMacByIfindex');
    Route::get('/BDCOM-Install-OnuRestart',                 [INSTALLController::class,'BdcomOnuRestartByIfindex'])->name('Install.BdcomOnuRestartByIfindex');
    
    Route::get('/BDCOM-Install-OnuPortAdminStatusON',       [INSTALLController::class,'BdcomOnuPortAdminStatusON'])->name('Install.BdcomOnuPortAdminStatusON');
    Route::get('/BDCOM-Install-OnuPortAdminStatusOFF',      [INSTALLController::class,'BdcomOnuPortAdminStatusOFF'])->name('Install.BdcomOnuPortAdminStatusOFF');
    Route::get('/BDCOM-Install-PortVlanChange',             [INSTALLController::class,'BdcomPortVlanChange'])->name('Install.BdcomPortVlanChange');
    Route::get('/BDCOM-Install-Details',                    [INSTALLController::class,'BdcomDetails'])->name('Install.BdcomDetails');


    Route::get('/ZTE-Install',                              [INSTALLController::class,'ZteInstall'])->name('Install.ZtemInstall');
    Route::get('/ZTE-Finish-Install',                       [INSTALLController::class,'ZteFinishInstall'])->name('Install.ZtemInstall');
    Route::get('/ZTE-Service-Profile-4Port-Bridge',         [INSTALLController::class,'ZteBridge4PortModeVlans'])->name('Install.ZteBridge4PortModeVlans'); 
    Route::get('/ZTE-onu-trunkMode-vlanList',               [INSTALLController::class,'ZteVlanList'])->name('Install.ZteVlanList'); 
    Route::get('/ZTE-AFTERINSTALL-INFO',                    [INSTALLController::class,'ZteInfoByIfindex'])->name('Install.ZteInfoByIfindex');
    Route::get('/ZTE-AFTERINSTALL-PORTS',                   [INSTALLController::class,'ZtePortByIfindex'])->name('Install.ZtePortByIfindex');
    Route::get('/ZTE-AFTERINSTALL-MAC',                     [INSTALLController::class,'ZteMacByIfindex'])->name('Install.ZteMacByIfindex');


    Route::get('/HUAWEI-Install',                           [INSTALLController::class,'HuaweiInstall'])->name('Install.HuaweiInstall');
    Route::get('/HUAWEI-Finish-Install',                    [INSTALLController::class,'HuaweiFinishInstall'])->name('Install.HuaweiFinishInstall');
    Route::get('/HUAWEI-AFTERINSTALL-INFO',                 [INSTALLController::class,'HuaweiInfoByIfindex'])->name('Install.HuaweiInfoByIfindex');
    Route::get('/HUAWEI-AFTERINSTALL-PORTS',                [INSTALLController::class,'HuaweiPortByIfindex'])->name('Install.HuaweiPortByIfindex');
    Route::get('/HUAWEI-AFTERINSTALL-MAC',                  [INSTALLController::class,'HuaweiMacByIfindex'])->name('Install.HuaweiMacByIfindex');


    Route::get('/HUAWEI-EPON-FTP',                          [INSTALLController::class,'HuaweiePON_FTP'])->name('Install.HuaweiePON_FTP');
    Route::get('/HUAWEI-EPON-VlansFromServiceProfile',      [INSTALLController::class,'HuaweiePON_VlansFromServiceProfile'])->name('Install.VlansFromServiceProfile');

     

});