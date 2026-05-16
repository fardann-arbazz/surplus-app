<?php

namespace App\Events;

use App\Models\SurplusProduct;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SurplusStatusUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public SurplusProduct $surplus) {}

    public function broadcastOn(): Channel
    {
        // Channel per store agar tidak bocor ke seller lain
        $storeId = $this->surplus->product->store_id;

        return new Channel("store.{$storeId}.surplus");
    }

    public function broadcastAs(): string
    {
        return 'status.updated';
    }

    public function broadcastWith(): array
    {
        return [
            'id'     => $this->surplus->id,
            'status' => $this->surplus->status,
        ];
    }
}
