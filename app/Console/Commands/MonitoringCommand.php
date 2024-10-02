<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Notifications\MonitoringNotification;
use App\Services\AdminService;

class MonitoringCommand extends Command
{
    private $adminService;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notify:monitoring';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Notify guests paid reservation by sms and email';


    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(AdminService $adminService)
    {
        parent::__construct();
        $this->adminService = $adminService;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $admins = $this->adminService->getAll();
        foreach ($admins as $admin) {
            $admin->notify(new MonitoringNotification());
        }
    }
}
