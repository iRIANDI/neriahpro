<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\VisionBlueprint;
use Livewire\Attributes\Layout;

class ClientInviteForm extends Component
{
    public VisionBlueprint $blueprint;

    public $client_name;
    public $email;
    public $phone;
    public $service_options = [];

    public function mount($slug)
    {
        $this->blueprint = VisionBlueprint::where('slug', $slug)->firstOrFail();
        $this->client_name = $this->blueprint->client_name;
        $this->email = $this->blueprint->email;
        $this->phone = $this->blueprint->phone;
        // Handle array casting correctly
        $this->service_options = is_array($this->blueprint->service_options) ? $this->blueprint->service_options : [];
    }

    public function submit()
    {
        $this->blueprint->update([
            'phone' => $this->phone,
            'service_options' => $this->service_options,
            'project_status' => 'In Progress', // Update status automatically
        ]);

        session()->flash('message', 'Terima kasih! Vision Blueprint Anda telah berhasil disimpan.');
    }

    #[Layout('components.layouts.app')]
    public function render()
    {
        return view('livewire.client-invite-form');
    }
}
