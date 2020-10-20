<?php namespace App\Jobs;

use App\Models\Webhook;
use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Osiset\ShopifyApp\Contracts\Objects\Values\ShopDomain;
use stdClass;

class ShopUpdateJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Shop's myshopify domain
     *
     * @var ShopDomain
     */
    public $shopDomain;

    /**
     * The webhook data
     *
     * @var object
     */
    public $data;

    /**
     * Create a new job instance.
     *
     * @param ShopDomain $shopDomain The shop's myshopify domain
     * @param stdClass   $data       The webhook data (JSON decoded)
     *
     * @return void
     */
    public function __construct($shopDomain, $data)
    {
        $this->shopDomain = $shopDomain;
        $this->data = $data;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // Do what you wish with the data
        // Access domain name as $this->shopDomain->toNative()
        \Log::info('============ Shop Update Webhook executed==================');
        $shop = User::where('name',$this->shopDomain->toNative())->first();

        $shop_data = json_encode($this->data);
        $shopify_id = json_decode($shop_data)->id;

        $topic = "shop/update";
        $entity = Webhook::updateOrCreate(
                ['shopify_id' => $shopify_id, 'topic' => $topic, 'user_id' => $shop->id],
                ['shopify_id' => $shopify_id, 'topic' => $topic, 'user_id' => $shop->id, 'data' => $shop_data, 'is_executed' => 0]
        );

        dispatch(new SyncLatestShopData($entity->id));
    }
}
