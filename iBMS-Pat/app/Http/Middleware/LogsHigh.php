<?php

namespace App\Http\Middleware;

use Closure;
use App\Traits\CommonFunctions;
use App\Device;
class logsHigh
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $device = Device::where('DEVICE_ID', $request->DEVICE_ID)
                        ->select('DEVICE_TYPE')
                        ->firstOrFail();
        if($request->TYPE == "Manual"){
            switch ($device->DEVICE_TYPE) {
                case 'temp_light':
                    if ($request->COMMAND == 'status') {
                        if ($request->VALUE == 1) {
                            $request->event = 'Has been turned ON';
                        }else{
                            $request->event = 'Has been turned OFF';
                        }
                    }else{
                        $request->event = $request->COMMAND.': '.$request->VALUE;
                    }
                    break;
                case 'water_valve':
                    if ($request->VALUE == 0) {
                        $request->event = 'Valve has been CLOSED';
                    }else{
                        $request->event = 'Valve has been OPENED';
                    }
                    break;
                case 'gas_valve':
                    if ($request->VALUE == 0) {
                        $request->event = 'Valve has been CLOSED';
                    }else{
                        $request->event = 'Valve has been OPENED';
                    }
                    break;
                case 'ir_remote':
                    $request->event = $request->COMMAND.': '.$request->VALUE;
                    break;
                case 'door_lock':
                    if($request->VALUE == 0){
                        $request->event = "Door has been LOCKED";
                    }else{
                        $request->event = "Door has been UNLOCKED";
                    }
                    break;
                case 'curtain_1':
                    if ($request->VALUE == 0) {
                        $request->event = "Curtain has been Closed";
                    }else{
                        $request->event = "Curtain has been Opened: ".$request->VALUE;
                    }
                case 'wall_switch_1':
                    if ($request->VALUE == 1) {
                        $request->event = 'Switch has been turned ON';
                    }else{
                        $request->event = 'Switch has been turned OFF';
                    }
                    break;
                case 'wall_switch_2':
                    if ($request->COMMAND == "status_1") {
                        if ($request->VALUE == 1) {
                            $request->event = 'Switch 1 has been turned ON';
                        }else{
                            $request->event = 'Switch 1 has been turned OFF';
                        }
                    }else if($request->COMMAND == "status_2"){
                        if ($request->VALUE == 1) {
                            $request->event = 'Switch 2 has been turned ON';
                        }else{
                            $request->event = 'Switch 2 has been turned OFF';
                        }
                    }else{
                        if ($request->VALUE == 1) {
                            $request->event = 'All switches have been turned ON';
                        }else{
                            $request->event = 'All switches have been turned OFF';
                        }
                    }
                    break;
                case 'wall_switch_3':
                    if ($request->COMMAND == "status_1") {
                        if ($request->VALUE == 1) {
                            $request->event = 'Switch 1 has been turned ON';
                        }else{
                            $request->event = 'Switch 1 has been turned OFF';
                        }
                    }else if($request->COMMAND == "status_2") {
                        if ($request->VALUE == 1) {
                            $request->event = 'Switch 2 has been turned ON';
                        }else{
                            $request->event = 'Switch 2 has been turned OFF';
                        }
                    }else if($request->COMMAND == "status_3") {
                        if ($request->VALUE == 1) {
                            $request->event = 'Switch 3 has been turned ON';
                        }else{
                            $request->event = 'Switch 3 has been turned OFF';
                        }
                    }else{
                        if ($request->VALUE == 1) {
                            $request->event = 'All switches have been turned ON';
                        }else{
                            $request->event = 'All switches have been turned OFF';
                        }
                    }
                    break;
                case 'embedded_switch_1':
                    if ($request->VALUE == 1) {
                        $request->event = 'Switch has been turned ON';
                    }else{
                        $request->event = 'Switch has been turned OFF';
                    }
                    break;
                case 'embedded_switch_2':
                    if ($request->COMMAND == "status_1") {
                        if ($request->VALUE == 1) {
                            $request->event = 'Switch 1 has been turned ON';
                        }else{
                            $request->event = 'Switch 1 has been turned OFF';
                        }
                    }else if($request->COMMAND == "status_2"){
                        if ($request->VALUE == 1) {
                            $request->event = 'Switch 2 has been turned ON';
                        }else{
                            $request->event = 'Switch 2 has been turned OFF';
                        }
                    }else{
                        if ($request->VALUE == 1) {
                            $request->event = 'All switches have been turned ON';
                        }else{
                            $request->event = 'All switches have been turned OFF';
                        }
                    }
                    break;
                case 'embedded_switch_3':
                    if ($request->COMMAND == "status_1") {
                        if ($request->VALUE == 1) {
                            $request->event = 'Switch 1 has been turned ON';
                        }else{
                            $request->event = 'Switch 1 has been turned OFF';
                        }
                    }else if($request->COMMAND == "status_2") {
                        if ($request->VALUE == 1) {
                            $request->event = 'Switch 2 has been turned ON';
                        }else{
                            $request->event = 'Switch 2 has been turned OFF';
                        }
                    }else if($request->COMMAND == "status_3") {
                        if ($request->VALUE == 1) {
                            $request->event = 'Switch 3 has been turned ON';
                        }else{
                            $request->event = 'Switch 3 has been turned OFF';
                        }
                    }else{
                        if ($request->VALUE == 1) {
                            $request->event = 'All switches have been turned ON';
                        }else{
                            $request->event = 'All switches have been turned OFF';
                        }
                    }
                    break;
                default:
                    $request->event = $request->COMMAND.': '.$request->VALUE;
            }
        }else{
            
        }
        return $next($request);
    }
}
