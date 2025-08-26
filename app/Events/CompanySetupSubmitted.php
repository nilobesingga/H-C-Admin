<?php

namespace App\Events;

use App\Models\CompanySetup;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CompanySetupSubmitted implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public CompanySetup $companySetup;

    /**
     * Create a new event instance.
     */
    public function __construct(CompanySetup $companySetup)
    {
        $this->companySetup = $companySetup;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('company-setup'),
        ];
    }

    /**
     * Get the data to broadcast.
     *
     * @return array
     */
    public function broadcastWith(): array
    {
        return [
            'id' => $this->companySetup->id,
            'description' => $this->companySetup->description,
            'timeframe' => $this->companySetup->timeframe,
            'language' => $this->companySetup->language,
            'contact_method' => $this->companySetup->contact_method,
            'contact_id' => $this->companySetup->contact_id,
            'created_at' => $this->companySetup->created_at,
        ];
    }

     public function broadcastAs(): string
    {
        return 'company-setup.created';
    }
}
