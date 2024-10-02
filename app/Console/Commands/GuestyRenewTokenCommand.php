<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Option;
use App\Services\GuestyService;

class GuestyRenewTokenCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'guesty:token:renew';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Renew guesty token';

    private $guestyService;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(GuestyService $guestyService)
    {
        parent::__construct();
        $this->guestyService = $guestyService;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->setToken();
    }

    /**
     * Permet de set un nouveau token
     */
    private function setToken()
    {
        $res = $this->guestyService->renewToken();
        if (isset($res['access_token'])) {
            Option::where('name', 'guesty_open_api_token_expires_in')->delete();
            Option::where('name', 'guesty_open_api_token_created_at')->delete();
            Option::where('name', 'guesty_open_api_token')->delete();
            Option::create([
                'name' => 'guesty_open_api_token',
                'value' => $res['access_token'],
            ]);
            Option::create([
                'name' => 'guesty_open_api_token_expires_in',
                'value' => $res['expires_in'],
            ]);
            Option::create([
                'name' => 'guesty_open_api_token_created_at',
                'value' => date('Y-m-d H:i:s'),
            ]);
        }
    }
}
