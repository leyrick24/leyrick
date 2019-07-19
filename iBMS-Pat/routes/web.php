<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Auth::routes();

    Route::get('', 'PagesController@dashboard');
    Route::get('device-monitoring', 'PagesController@monitoring');
    Route::get('floor-view', 'PagesController@displayview');
    Route::get('user-management', 'PagesController@users')->middleware('module');
    Route::get('gateway-management', 'PagesController@gateway')->middleware('module');
    Route::get('device-management', 'PagesController@device')->middleware('module');
    Route::get('binding-management', 'PagesController@binding')->middleware('module');
    Route::get('ir-management', 'PagesController@infrared')->middleware('module');
    Route::get('logs', 'PagesController@logs')->middleware('module');
    Route::get('notifications', 'PagesController@notifications');
    Route::get('floor-management', 'PagesController@floor')->middleware('module');
    Route::get('about', 'PagesController@about');
    Route::get('help', 'PagesController@help');

    //Dashboard
    Route::post('gateway', 'DashboardController@getGateway');
    Route::post('device', 'DashboardController@getDevice');
    Route::post('status', 'DashboardController@getStatus');
    Route::post('gatewayStatus', 'DashboardController@gatewayStatus');
    Route::post('deviceStatus', 'DashboardController@deviceStatus');
    Route::get('getUniqueDevices', 'DashboardController@getUniqueDevices');
    Route::post('getProcessedData', 'DashboardController@getProcessedData');

    //Graphs
    Route::get('dataByMonth/{id}', 'DeviceGraphController@dataByMonth');

    // User
    Route::group(['prefix' => 'users'], function() {
        Route::post('users-data', 'UserController@getUsers');
        Route::post('user-data', 'UserController@getUser');
        Route::post('user-inact','UserController@getInactiveUsers');
        Route::post('user-act','UserController@getActiveUsers');
        Route::post('new-user', 'UserController@newUser');
        Route::post('disable-user', 'UserController@disableUser');
        Route::post('enable-user', 'UserController@enableUser');
        Route::post('change-password-user', 'UserController@changePasswordUser');
        Route::post('update-user-profile', 'UserController@updateUserProfile');
        Route::post('update-user-designation', 'UserController@updateUserDesignation');
    });

    // Manufacturer
    Route::group(['prefix' => 'manufacturer'], function() {
        Route::get('', 'ManufacturerController@getManufacturerAll');
    });

    // Floor
    Route::group(['prefix' => 'floors'], function() {
        Route::get('', 'FloorController@getFloorAll');
        Route::post('', 'FloorController@getFloorAll')->middleware('auth.admin');                // Add "include" parameter in you want added stuff (like room objects themselves instead of just ids)
        Route::get('search', 'FloorController@searchFloors');
        Route::get('{id}', 'FloorController@getFloor');
        Route::get('{id}/rooms', 'FloorController@getFloorRooms');
        Route::post('{id}/rooms', 'FloorController@getFloorRooms');
        Route::get('{id}/gateways', 'FloorController@getFloorGateways');
        Route::get('{id}/devices', 'FloorController@getFloorDevices');
        Route::get('{id}/users', 'FloorController@getFloorUsers');
        Route::post('create', 'FloorController@newFloor');
        Route::post('update', 'FloorController@updateFloor');
        Route::post('delete', 'FloorController@deleteFloor');
        Route::post('getAuthFloor', 'FloorController@getAuthFloor');
        Route::post('{id}', 'FloorController@getFloor');
    });

    // Floor
    Route::group(['prefix' => 'modules'], function() {
        Route::post('', 'ModuleController@getModuleAll');
        Route::get('', 'ModuleController@getModuleAll');
    });

    // Room
    Route::group(['prefix' => 'rooms'], function() {
        Route::get('', 'RoomController@getRoomAll');
        Route::post('', 'RoomController@getRoomAll');
        Route::get('search', 'RoomController@searchRoom');
        Route::get('{id}', 'RoomController@getRoom');
        Route::get('{id}/floor', 'RoomController@getRoomFloor');
        Route::get('{id}/gateways', 'RoomController@getRoomGateways');
        Route::get('{id}/devices', 'RoomController@getRoomDevices');
        Route::get('{id}/users', 'RoomController@getRoomUsers');
        Route::get('{id}/meters', 'RoomController@getRoomMeter');
        Route::post('create', 'RoomController@newRoom');
        Route::post('update', 'RoomController@updateRoom');
        Route::post('delete', 'RoomController@deleteRoom');
    });

    // Gateway
    Route::group(['prefix' => 'gateways'], function() {
        Route::get('', 'GatewayController@getGatewayAll');
        Route::get('search', 'GatewayController@searchGateway');
        Route::get('scan', 'GatewayController@scanGatewayAll');
        Route::get('unregistered', 'GatewayController@getUnregisteredGateways');
        Route::get('registered', 'GatewayController@getRegisteredGateways');
        Route::get('blocked', 'GatewayController@getBlockedGateways');
        Route::get('deleted', 'GatewayController@getDeletedGateways');
        Route::get('{id}', 'GatewayController@getGateway');
        Route::get('{id}/devices', 'GatewayController@getGatewayDevices');
        Route::get('{id}/floor', 'GatewayController@getGatewayFloor');
        Route::get('{id}/room', 'GatewayController@getGatewayRoom');
        Route::get('{id}/users', 'GatewayController@getGatewayUsers');
        Route::get('{id}/notifications', 'GatewayController@getGatewayNotifications');
        Route::post('register', 'GatewayController@registerGateway');
        Route::post('registerModBus', 'GatewayController@registerGatewayModbus');
        Route::post('update', 'GatewayController@updateGateway');
        Route::post('delete', 'GatewayController@deleteGateway');
        Route::post('undelete', 'GatewayController@undeleteGateway');
        Route::post('block', 'GatewayController@blockGateway');
        Route::post('unblock', 'GatewayController@unblockGateway');
        Route::get('alert/{AlertType}','sweetalertController@alert')->name('alert');
    });

    // Device
    Route::group(['prefix' => 'devices'], function() {
        Route::get('', 'DeviceController@getDeviceAll');
        Route::get('search', 'DeviceController@searchDevice');
        Route::get('scan', 'DeviceController@scanDeviceAll');
        Route::get('unregistered', 'DeviceController@getUnregisteredDevices');
        Route::get('registered', 'DeviceController@getRegisteredDevices');
        Route::get('newdev', 'DeviceController@getNewDev');
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
        Route::get('{id}/binding-list-devices/{condition}/{devicetypeid}', 'DeviceController@getDeviceBindingListDevices');
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
    // Meter
    Route::group(['prefix' => 'meters'], function() {
        Route::get('registered', 'ElectricMeterController@getRegisteredMeter');
        Route::get('deleted', 'ElectricMeterController@getDeletedMeter');
        Route::post('register', 'ElectricMeterController@registerMeter');
        Route::post('update', 'ElectricMeterController@updateMeter');
        Route::post('delete', 'ElectricMeterController@deleteMeter');
        Route::post('unDelete', 'ElectricMeterController@unDeleteMeter');
    });

    // Binding
    Route::group(['prefix' => 'bindings'], function() {
        Route::get('', 'BindingController@getBindingAll');
        Route::get('{id}', 'BindingController@getBinding');
        Route::post('new', 'BindingController@newBinding');
        Route::post('delete', 'BindingController@deleteBinding');
        Route::post('delete-binding', 'BindingController@deleteSensorBinding');
        Route::post('enable', 'BindingController@enableBinding');
        Route::post('disable', 'BindingController@disableBinding');
        Route::post('enableAll', 'BindingController@enableAllBinding');
        Route::post('disableAll', 'BindingController@disableAllBinding');
    });

    // Binding List
    Route::group(['prefix' => 'binding-list'], function() {
        Route::get('', 'BindingListController@getBindingListAll');
        Route::get('{id}', 'BindingListController@getBindingList');
        Route::get('{id}/bindings', 'BindingListController@getBindingListBindings');
        Route::get('{id}/source-devices', 'BindingListController@getBindingListSourceDevices');
        Route::get('{id}/target-devices', 'BindingListController@getBindingListTargetDevices');
    });

    // Instruction
    Route::group(['prefix' => 'instructions'], function() {
        Route::get('', 'InstructionController@getInstructionAll');
        Route::get('{id}', 'InstructionController@getInstruction');
        Route::get('{id}/device', 'InstructionController@getInstructionDevice');
        Route::get('{id}/user', 'InstructionController@getInstructionUser');
        Route::post('send', 'InstructionController@sendInstruction')->middleware('high');
    });

    // Processed Data
    Route::group(['prefix' => 'processed-data'], function() {
        Route::get('', 'ProcessedDataController@getProcessedDataAll');
        Route::get('{id}', 'ProcessedDataController@getProcessedData');
        Route::post('{id}/device', 'DeviceController@getProcessedDataDevice');
    });

    // Notification Data
    Route::group(['prefix' => 'notification-data'], function() {
        Route::post('/insertNotif', 'NotificationController@insertNotification');
        Route::get('{id}/getNotif', 'NotificationController@getNotification');
        Route::post('/updateNotif', 'NotificationController@updateNotification');
        Route::post('/logs', 'NotificationController@logsNotification');
    });

    // ModBus Data
    Route::group(['prefix' => 'modbus-data'], function() {
        Route::post('/getModBusData', 'ModBusController@getModBusData');
        Route::post('/display', 'ModBusController@displayMeterData');
        Route::post('/monthlyConsumption', 'ModBusController@monthlyConsumption');
        Route::post('/check', 'BindingController@checkNextActivity');
        Route::post('/meterdata', 'ModBusController@indivMeterData');
        Route::post('/indv-month', 'ModBusController@indivMonthly');
    });

    // IR Learning List
    Route::group(['prefix' => 'learning-list'], function() {
        Route::get('', 'IrLearningController@getAllIrLearningList');
        Route::get('{id}', 'IrLearningController@getIrLearning');
        Route::post('new', 'IrLearningController@newIrLearning');
        Route::post('update', 'IrLearningController@updateIrLearning');
        Route::get('targetList', 'IrLearningController@getIrTargetList');
        Route::post('delete', 'IrLearningController@deleteIrRecord');
    });

    Route::group(['prefix' => 'logs'], function() {
        Route::get('systemLogs', 'LogController@getSystemLogs');
        Route::get('auditTrailLogs', 'LogController@getAuditTrailLogs');
        Route::post('addSystemLogs', 'LogController@addSystemLogs');
    });
Route::get('sess-user', 'SessionController@getId');
Route::get('locale/{locale}', 'SessionController@changeLocale');
Route::get('session', 'SessionController@getSession');