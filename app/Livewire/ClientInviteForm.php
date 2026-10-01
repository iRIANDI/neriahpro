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
    public $country_code = '+62';
    public $phone_digits = '';
    public $service_options = [];

    public function mount($slug)
    {
        $this->blueprint = VisionBlueprint::where('slug', $slug)->firstOrFail();
        $this->client_name = $this->blueprint->client_name;
        $this->email = $this->blueprint->email;
        $this->phone = $this->blueprint->phone;
        
        if ($this->phone) {
            $digits = preg_replace('/\D/', '', $this->phone);
            if (str_starts_with($digits, '62')) {
                $this->country_code = '+62';
                $this->phone_digits = ltrim(substr($digits, 2), '0');
            } else {
                $this->phone_digits = ltrim($digits, '0');
            }
        }

        // Handle array casting correctly
        $this->service_options = is_array($this->blueprint->service_options) ? $this->blueprint->service_options : [];
    }

    public function submit()
    {
        $cleanDigits = ltrim(preg_replace('/\D/', '', $this->phone_digits), '0');
        $code = str_starts_with($this->country_code, '+') ? $this->country_code : ('+' . $this->country_code);
        $finalPhone = $cleanDigits ? ($code . $cleanDigits) : $this->phone;

        $this->blueprint->update([
            'phone' => $finalPhone,
            'service_options' => $this->service_options,
            'project_status' => 'In Progress', // Update status automatically
        ]);

        session()->flash('message', 'Terima kasih! Vision Blueprint Anda telah berhasil disimpan.');
    }

    #[Layout('components.layouts.app')]
    public function render()
    {
        return view('livewire.client-invite-form', [
            'countries' => config('countries', []),
        ]);
    }
}
