<?php
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use Illuminate\Support\Facades\Route;


use App\Http\Controllers\OLTSIDEController;


Route::middleware('auth')->group(function ()
{
    Route::get('/OLT-NAME-SEARCH',                              [OLTSIDEController::class,'NameSearch'])->name('nameSearch');


    Route::get('/BDCOM-Olt-side-SystemInfo',                    [OLTSIDEController::class,'bdcom_SystemInfo'])->name('bdcom.SystemInfo');
    Route::get('/BDCOM-Olt-side-PonCharts',                     [OLTSIDEController::class,'bdcom_PonCharts'])->name('bdcom.PonCharts');
    Route::get('/BDCOM-Olt-side-SwitchPorts',                   [OLTSIDEController::class,'bdcom_SwitchPorts'])->name('bdcom.SwitchPorts');
    Route::get('/BDCOM-Olt-side-OnuDescriptionEdit',            [OLTSIDEController::class,'bdcom_OnuDescription'])->name('bdcom.OnuDescription');
    Route::get('/BDCOM-Olt-side-OnuUninstall',                  [OLTSIDEController::class,'bdcom_OnuUninstall'])->name('bdcom.OnuUninstall');
    Route::get('/BDCOM-Olt-side-PonParameters',                 [OLTSIDEController::class,'bdcom_PonParameters'])->name('bdcom.PonParameters');
    Route::get('/BDCOM-Olt-side-PonTurnOn',                     [OLTSIDEController::class,'bdcom_PonTurnOn'])->name('bdcom.PonTurnOn');
    Route::get('/BDCOM-Olt-side-ShutDown',                      [OLTSIDEController::class,'bdcom_ShutDown'])->name('bdcom.ShutDown');
    Route::get('/BDCOM-Olt-side-PonDescriptionEdit',            [OLTSIDEController::class,'bdcom_PonDescriptionEdit'])->name('bdcom.PonDescriptionEdit');
    Route::get('/BDCOM-Olt-side-Uplinks',                       [OLTSIDEController::class,'bdcom_Uplinks'])->name('bdcom.Uplinks');
    Route::get('/BDCOM-Olt-side-UplinksTurnOn',                 [OLTSIDEController::class,'bdcom_UplinksTurnOn'])->name('bdcom.UplinksTurnOn');
    Route::get('/BDCOM-Olt-side-UplinksShutDown',               [OLTSIDEController::class,'bdcom_UplinksShutDown'])->name('bdcom.UplinksShutDown');
    Route::get('/BDCOM-Olt-side-UplinkDescriptionEdit',         [OLTSIDEController::class,'bdcom_UplinksDescriptionEdit'])->name('bdcom.UplinksDescriptionEdit');
    Route::get('/BDCOM-Olt-side-Details',                       [OLTSIDEController::class,'bdcom_Details'])->name('bdcom.Details');
     
    
    Route::get('/HUAWEI-Olt-side-SystemInfo',                   [OLTSIDEController::class,'huawei_SystemInfo'])->name('huawei.SystemInfo');
    Route::get('/HUAWEI-Olt-side-PonCharts',                    [OLTSIDEController::class,'huawei_PonCharts'])->name('huawei.PonCharts');
    Route::get('/HUAWEI-Olt-side-SwitchPorts',                  [OLTSIDEController::class,'huawei_SwitchPorts'])->name('huawei.SwitchPorts');
    Route::get('/HUAWEI-Olt-side-OnuDescriptionEdit',           [OLTSIDEController::class,'huawei_OnuDescription'])->name('huawei.OnuDescription');
    Route::get('/HUAWEI-Olt-side-OnuUninstall',                 [OLTSIDEController::class,'huawei_OnuUninstall'])->name('huawei.OnuUninstall');
    Route::get('/HUAWEI-Olt-side-PonParameters',                [OLTSIDEController::class,'huawei_PonParameters'])->name('huawei.PonParameters');
    Route::get('/HUAWEI-Olt-side-PonTurnOn',                    [OLTSIDEController::class,'huawei_PonTurnOn'])->name('huawei.PonTurnOn');
    Route::get('/HUAWEI-Olt-side-ShutDown',                     [OLTSIDEController::class,'huawei_ShutDown'])->name('huawei.ShutDown');
    Route::get('/HUAWEI-Olt-side-PonDescriptionEdit',           [OLTSIDEController::class,'huawei_PonDescriptionEdit'])->name('huawei.PonDescriptionEdit');
    Route::get('/HUAWEI-Olt-side-Uplinks',                      [OLTSIDEController::class,'huawei_Uplinks'])->name('huawei.Uplinks');
    Route::get('/HUAWEI-Olt-side-UplinksTurnOn',                [OLTSIDEController::class,'huawei_UplinksTurnOn'])->name('huawei.UplinksTurnOn');
    Route::get('/HUAWEI-Olt-side-UplinksShutDown',              [OLTSIDEController::class,'huawei_UplinksShutDown'])->name('huawei.UplinksShutDown');
    Route::get('/HUAWEI-Olt-side-UplinkDescriptionEdit',        [OLTSIDEController::class,'huawei_UplinksDescriptionEdit'])->name('huawei.UplinksDescriptionEdit');
    Route::get('/HUAWEI-Olt-side-Details',                      [OLTSIDEController::class,'huawei_Details'])->name('huawei.Details');
    Route::get('/HUAWEI-Olt-side-OnuControlOff',                [OLTSIDEController::class,'huawei_OnuControlOff'])->name('huawei.OnuControlOff');
    Route::get('/HUAWEI-Olt-side-OnuControlOn',                 [OLTSIDEController::class,'huawei_OnuControlOn'])->name('huawei.OnuControlOn');


    Route::get('/VSOLUTION-Olt-side-SystemInfo',                [OLTSIDEController::class,'vsolution_SystemInfo'])->name('vsolution.SystemInfo');
    Route::get('/VSOLUTION-Olt-side-PonCharts',                 [OLTSIDEController::class,'vsolution_PonCharts'])->name('vsolution.PonCharts');
    Route::get('/VSOLUTION-Olt-side-SwitchPorts',               [OLTSIDEController::class,'vsolution_SwitchPorts'])->name('vsolution.SwitchPorts');
    Route::get('/VSOLUTION-Olt-side-OnuDescriptionEdit',        [OLTSIDEController::class,'vsolution_OnuDescription'])->name('vsolution.OnuDescription');
    Route::get('/VSOLUTION-Olt-side-OnuUninstall',              [OLTSIDEController::class,'vsolution_OnuUninstall'])->name('vsolution.OnuUninstall');
    Route::get('/VSOLUTION-Olt-side-PonParameters',             [OLTSIDEController::class,'vsolution_PonParameters'])->name('vsolution.PonParameters');
    Route::get('/VSOLUTION-Olt-side-PonTurnOn',                 [OLTSIDEController::class,'vsolution_PonTurnOn'])->name('vsolution.PonTurnOn');
    Route::get('/VSOLUTION-Olt-side-ShutDown',                  [OLTSIDEController::class,'vsolution_ShutDown'])->name('vsolution.ShutDown');
    Route::get('/VSOLUTION-Olt-side-PonDescriptionEdit',        [OLTSIDEController::class,'vsolution_PonDescriptionEdit'])->name('vsolution.PonDescriptionEdit');
    Route::get('/VSOLUTION-Olt-side-Uplinks',                   [OLTSIDEController::class,'vsolution_Uplinks'])->name('vsolution.Uplinks');
    Route::get('/VSOLUTION-Olt-side-UplinksTurnOn',             [OLTSIDEController::class,'vsolution_UplinksTurnOn'])->name('vsolution.UplinksTurnOn');
    Route::get('/VSOLUTION-Olt-side-UplinksShutDown',           [OLTSIDEController::class,'vsolution_UplinksShutDown'])->name('vsolution.UplinksShutDown');
    Route::get('/VSOLUTION-Olt-side-UplinkDescriptionEdit',     [OLTSIDEController::class,'vsolution_UplinksDescriptionEdit'])->name('vsolution.UplinksDescriptionEdit');
    Route::get('/VSOLUTION-Olt-side-Details',                   [OLTSIDEController::class,'vsolution_Details'])->name('vsolution.Details');


    Route::get('/ZTE-Olt-side-SystemInfo',                      [OLTSIDEController::class,'zte_SystemInfo'])->name('zte.SystemInfo');
    Route::get('/ZTE-Olt-side-PonCharts',                       [OLTSIDEController::class,'zte_PonCharts'])->name('zte.PonCharts');
    Route::get('/ZTE-Olt-side-SwitchPorts',                     [OLTSIDEController::class,'zte_SwitchPorts'])->name('zte.SwitchPorts');
    Route::get('/ZTE-Olt-side-OnuDescriptionEdit',              [OLTSIDEController::class,'zte_OnuDescription'])->name('zte.OnuDescription');
    Route::get('/ZTE-Olt-side-OnuUninstall',                    [OLTSIDEController::class,'zte_OnuUninstall'])->name('zte.OnuUninstall');
    Route::get('/ZTE-Olt-side-PonParameters',                   [OLTSIDEController::class,'zte_PonParameters'])->name('zte.PonParameters');
    Route::get('/ZTE-Olt-side-PonTurnOn',                       [OLTSIDEController::class,'zte_PonTurnOn'])->name('zte.PonTurnOn');
    Route::get('/ZTE-Olt-side-ShutDown',                        [OLTSIDEController::class,'zte_ShutDown'])->name('zte.ShutDown');
    Route::get('/ZTE-Olt-side-PonDescriptionEdit',              [OLTSIDEController::class,'zte_PonDescriptionEdit'])->name('zte.PonDescriptionEdit');
    Route::get('/ZTE-Olt-side-Uplinks',                         [OLTSIDEController::class,'zte_Uplinks'])->name('zte.Uplinks');
    Route::get('/ZTE-Olt-side-UplinksTurnOn',                   [OLTSIDEController::class,'zte_UplinksTurnOn'])->name('zte.UplinksTurnOn');
    Route::get('/ZTE-Olt-side-UplinksShutDown',                 [OLTSIDEController::class,'zte_UplinksShutDown'])->name('zte.UplinksShutDown');
    Route::get('/ZTE-Olt-side-UplinkDescriptionEdit',           [OLTSIDEController::class,'zte_UplinksDescriptionEdit'])->name('zte.UplinksDescriptionEdit');
    Route::get('/ZTE-Olt-side-Details',                         [OLTSIDEController::class,'zte_Details'])->name('zte.Details');
    Route::get('/ZTE-Olt-side-OnuControlOff',                   [OLTSIDEController::class,'zte_OnuControlOff'])->name('zte.OnuControlOff');
    Route::get('/ZTE-Olt-side-OnuControlOn',                    [OLTSIDEController::class,'zte_OnuControlOn'])->name('zte.OnuControlOn');



    Route::get('/HSGQ-Olt-side-SystemInfo',                     [OLTSIDEController::class,'hsgq_SystemInfo'])->name('hsgq.SystemInfo');
    Route::get('/HSGQ-Olt-side-PonCharts',                      [OLTSIDEController::class,'hsgq_PonCharts'])->name('hsgq.PonCharts');
    Route::get('/HSGQ-Olt-side-SwitchPorts',                    [OLTSIDEController::class,'hsgq_SwitchPorts'])->name('hsgq.SwitchPorts');
    Route::get('/HSGQ-Olt-side-OnuDescriptionEdit',             [OLTSIDEController::class,'hsgq_OnuDescription'])->name('hsgq.OnuDescription');
    Route::get('/HSGQ-Olt-side-OnuUninstall',                   [OLTSIDEController::class,'hsgq_OnuUninstall'])->name('hsgq.OnuUninstall');
    Route::get('/HSGQ-Olt-side-PonParameters',                  [OLTSIDEController::class,'hsgq_PonParameters'])->name('hsgq.PonParameters');
    Route::get('/HSGQ-Olt-side-PonTurnOn',                      [OLTSIDEController::class,'hsgq_PonTurnOn'])->name('hsgq.PonTurnOn');
    Route::get('/HSGQ-Olt-side-ShutDown',                       [OLTSIDEController::class,'hsgq_ShutDown'])->name('hsgq.ShutDown');
    Route::get('/HSGQ-Olt-side-PonDescriptionEdit',             [OLTSIDEController::class,'hsgq_PonDescriptionEdit'])->name('hsgq.PonDescriptionEdit');
    Route::get('/HSGQ-Olt-side-Uplinks',                        [OLTSIDEController::class,'hsgq_Uplinks'])->name('hsgq.Uplinks');
    Route::get('/HSGQ-Olt-side-UplinksTurnOn',                  [OLTSIDEController::class,'hsgq_UplinksTurnOn'])->name('hsgq.UplinksTurnOn');
    Route::get('/HSGQ-Olt-side-UplinksShutDown',                [OLTSIDEController::class,'hsgq_UplinksShutDown'])->name('hsgq.UplinksShutDown');
    Route::get('/HSGQ-Olt-side-UplinkDescriptionEdit',          [OLTSIDEController::class,'hsgq_UplinksDescriptionEdit'])->name('hsgq.UplinksDescriptionEdit');
    Route::get('/HSGQ-Olt-side-Details',                        [OLTSIDEController::class,'hsgq_Details'])->name('hsgq.Details');

    Route::get('/ZYXEL-Olt-side-SystemInfo',                    [OLTSIDEController::class,'zyxel_SystemInfo'])->name('zyxel.SystemInfo');
    Route::get('/ZYXEL-Olt-side-OnuDescriptionEdit',            [OLTSIDEController::class,'zyxel_OnuDescriptionEdit'])->name('zyxel.OnuDescriptionEdit');
     
    Route::get('/CISCO-Olt-side-SystemInfo',                    [OLTSIDEController::class,'cisco_SystemInfo'])->name('cisco.SystemInfo');
    Route::get('/CISCO-Olt-side-OnuDescriptionEdit',            [OLTSIDEController::class,'cisco_OnuDescriptionEdit'])->name('cisco.OnuDescriptionEdit');
});


 