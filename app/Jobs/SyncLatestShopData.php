<?php

namespace App\Jobs;

use App\Models\Webhook;
use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SyncLatestShopData implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $webhook_id;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($webhook_id)
    {
        $this->webhook_id = $webhook_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $webhook = Webhook::where('id', $this->webhook_id)->where('is_executed', 0)->first();
        if($webhook) {
            $json_data = json_decode($webhook->data,1);
            $shop = User::find($webhook->user_id);
            $shop->store_email_address = $json_data['email'];
            $shop->save();
            $webhook->is_executed = 1;
            $webhook->save();
        }
    }
}
