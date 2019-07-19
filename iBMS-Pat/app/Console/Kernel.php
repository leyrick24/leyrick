<?php
/*
* <System Name> iBMS
* <Program Name> Kernel.php
*
* <Create> 2018.XX.XX TP Yani
*
*/

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Auth;

/**
 * <Class Name> Kernel
 * <Overview> Scheduler
 */
class Kernel extends ConsoleKernel
{
    /*******************************************************************/
    /* Processing Hierarchy                                            */
    /*******************************************************************/
    // schedule                         (1.0) Create a new command instance
    // commands                         (2.0) Execute the console command

    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
        'App\Console\Commands\SaveElectricMeter',
        'App\Console\Commands\BindingCheckNextActivity',
        // 'App\Console\Commands\DeviceDataPull'
    ];

    /**
     * <Layer number> (1.0) Define the application's command schedule.
     * <Processing name> schedule
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('save:electricMeter')->hourly();

        //  $schedule->command('save:electricMeter')->everyMinute();
        // $schedule->command('save:electricMeter')->twiceDaily(11, 23);
        //$schedule->command('save:electricMeter')->dailyAt('23:59');
        //$schedule->command('save:electricMeter')->dailyAt('3:59');
        //$schedule->command('save:electricMeter')->dailyAt('7:59');
        //$schedule->command('save:electricMeter')->dailyAt('11:59');
        //$schedule->command('save:electricMeter')->dailyAt('15:59');
        //$schedule->command('save:electricMeter')->dailyAt('19:59');
        $schedule->command('save:resetEMeterData')->monthlyOn(1, '00:00');
        // $schedule->command('check:deviceData')->everyMinute();
        // $schedule->command('check:nextActivity')->everyMinute();
        // $schedule->command('check:nextActivity')->everyFiveMinutes();
        // $schedule->command('gateway:Status')->everyMinute();
        //$schedule->command('check:nextActivity')->cron('0-58/2 * * * *');                //Check Binding Next Activity Trigger
        $schedule->command('check:nextActivity')->everyMinute();                //Check Binding Next Activity Trigger
        $schedule->command('gateway:Status')->cron('0-58/2 * * * *');           //Change the Online Status of Gateway
         // $schedule->command('device:Status')->cron('0-58/2 * * * *');            //Change the Online Status of Devices based on Status of Gateway
         // $schedule->command('save:electricMeter')->cron('1-59/2 * * * *')->withoutOverlapping();       //Save ElectriC Meter Data
        if (Auth::check()) {
            $schedule->command('check:Timeout')->everyMinute();
        }
    }

    /**
     * <Layer number> (2.0) Register the commands for the application.
     * <Processing name> commands
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
