<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});


//This is all the function from MC
Route::post('newDevice', 'DeviceController@newDevice');
Route::post('offlineDevice', 'DeviceController@offlineDevice');
Route::post('newData', 'ProcessedDataController@newProcessedData');
Route::post('delete/Device/MC', 'DeviceController@deleteDeviceFromMC');

Route::post('onlineGateway', 'ProcessedDataController@otherMCFunctions');
Route::post('offlineGateway', 'ProcessedDataController@otherMCFunctions');
Route::post('zigbeeSetup', 'ProcessedDataController@otherMCFunctions');
Route::post('setupDevice', 'ProcessedDataController@otherMCFunctions');
Route::post('alarmDevice', 'ProcessedDataController@otherMCFunctions');
Route::post('logMessage', 'ProcessedDataController@otherMCFunctions');

//This is all the function from OPS to MC
Route::post('/sendInstruction', 'InstructionController@sendInstruction');
Route::post('/enableJoin', 'GatewayController@enableJoin');


//User for adding Floor
Route::post('imageUpload', 'ImageUploadFloorController@imageUpload');
Route::post('addNewRoom', 'ImageUploadFloorController@addRoom');



Route::group(['prefix' => 'device'], function() {
    Route::get('', 'DeviceController@getDeviceAll');
    Route::get('scan', 'DeviceController@scanDeviceAll');
    Route::get('unregistered', 'DeviceController@getUnregisteredDevices');
    Route::get('registered', 'DeviceController@getRegisteredDevices');
    Route::get('blocked', 'DeviceController@getBlockedDevices');
    Route::get('deleted', 'DeviceController@getDeletedDevices');
    Route::get('{id}', 'DeviceController@getDevice');
    Route::get('{id}/floor', 'DeviceController@getDeviceFloor');
    Route::get('{id}/room', 'DeviceController@getDeviceRoom');
    Route::get('{id}/gateway', 'DeviceController@getDeviceGateway');
    Route::get('{id}/processed-data', 'DeviceController@getDeviceProcessedData');
    Route::get('{id}/instructions', 'DeviceController@getDeviceInstructions');
    Route::get('{id}/bindings', 'DeviceController@getDeviceBindings');
    Route::post('{id}/scan', 'DeviceController@scanDevice');
    Route::post('register', 'DeviceController@registerDevice');
    Route::post('update', 'DeviceController@updateDevice');
    Route::post('delete', 'DeviceController@deleteDevice');
    Route::post('block', 'DeviceController@blockDevice');
});


Route::post('floors/create', 'FloorController@newFloor');

// Notification Data
Route::group(['prefix' => 'notification-data'], function() {
    Route::post('/insertNotif', 'NotificationController@insertNotification');
    Route::get('{id}/getNotif', 'NotificationController@getNotification');
    Route::post('/updateNotif', 'NotificationController@updateNotification');
    Route::get('/notificationSound', 'NotificationController@notificationSound');
});

Route::post('testFunction', 'ProcessedDataController@testFunction');

//Delete this on future only for testing
Route::post('gateways/reset/force', 'GatewayController@resetGatewayForce');
Route::post('deleteGateway', 'GatewayController@deleteGateway');

Route::post('gateways/delete/manual', 'GatewayController@deleteGatewayManual');
Route::post('modbuscheck', 'ModBusController@checkModBusData');
Route::post('modbusSave', 'ModBusController@saveModBusData');
Route::post('getProcessedData', 'DashboardController@getProcessedData');
Route::post('getUniqueDevices', 'DashboardController@getUniqueDevices');


// Route::post('testData', 'IrLearningController@deleteIrRecord');

    // Device
    Route::group(['prefix' => 'devices'], function() {
        Route::get('', 'DeviceController@getDeviceAll');
        Route::get('search', 'DeviceController@searchDevice');
        Route::get('scan', 'DeviceController@scanDeviceAll');
        Route::get('tests', 'BindingController@tests');
        // Route::get('{id}/scan', 'DeviceController@scanDevice');
        Route::get('unregistered', 'DeviceController@getUnregisteredDevices');
        Route::get('registered', 'DeviceController@getRegisteredDevices');
        Route::get('blocked', 'DeviceController@getBlockedDevices');
        Route::get('deleted', 'DeviceController@getDeletedDevices');
        Route::get('with-bindings', 'DeviceController@getDevicesWithBindings');
        Route::get('{id}', 'DeviceController@getDevice');
        Route::get('{id}/floor', 'DeviceController@getDeviceFloor');
        Route::get('{id}/room', 'DeviceController@getDeviceRoom');
        Route::get('{id}/gateway', 'DeviceController@getDeviceGateway');
        Route::get('{id}/users', 'DeviceController@getDeviceUsers');
        Route::get('{id}/processed-data', 'DeviceController@getDeviceProcessedData');
        Route::get('{id}/instructions', 'DeviceController@getDeviceInstructions');
        Route::get('{id}/notifications', 'DeviceController@getDeviceNotifications');
        Route::get('{id}/bindings', 'DeviceController@getDeviceBindings');
        Route::get('{id}/binding-list', 'DeviceController@getDeviceBindingList');
        Route::get('{id}/binding-list-condition', 'DeviceController@getDeviceBindingListCondition');
        // Route::get('{id}/binding-list-devices/{condition}', 'DeviceController@getDeviceBindingListDevices');
        Route::get('{id}/binding-list-devices/{condition}/{devicetypeid}', 'DeviceController@getDeviceBindingListDevices');
        // Route::get('{id}/binding-list-devices/{devicetype}/{condition}', 'DeviceController@getMultiDeviceBindingListDevices');
        Route::get('{id}/binding-list-devices/{devicetype}/{condition}/{devicetypeid}', 'DeviceController@getMultiDeviceBindingListDevices');
        Route::post('register', 'DeviceController@registerDevice');
        Route::post('update', 'DeviceController@updateDevice');
        Route::post('delete', 'DeviceController@deleteDevice');
        Route::post('undelete', 'DeviceController@undeleteDevice');
        Route::post('block', 'DeviceController@blockDevice');
        Route::post('unblock', 'DeviceController@unblockDevice');
        Route::post('updateDeviceMapName', 'DeviceController@updateDeviceMapName');
        Route::post('{id}', 'DeviceController@getDevice');
    });

Route::get('nextBinding', 'BindingController@checkNextActivity');
Route::get('{id}/check-sess', 'SessionController@checkSession');
