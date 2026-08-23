<div class="w-full max-w-2xl bg-white/70 backdrop-blur-lg rounded-2xl shadow-xl overflow-hidden border border-white/20">
    <div class="px-8 py-10">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Vision Blueprint</h1>
            <p class="text-gray-500 mt-2 text-sm">Let&apos;s build something amazing together, <span class="font-semibold text-gray-700">{{ $client_name }}</span>!</p>
        </div>

        @if (session()->has('message'))
            <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-4 rounded-lg flex items-center shadow-sm mb-6 animate-pulse">
                <svg class="w-6 h-6 mr-3 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                <span class="font-medium">{{ session('message') }}</span>
            </div>
        @else
            <form wire:submit="submit" class="space-y-6">
                <!-- Client Details (Readonly) -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                        <input type="text" value="{{ $client_name }}" disabled class="w-full bg-gray-100 border-transparent rounded-lg text-gray-500 py-3 px-4 shadow-sm cursor-not-allowed">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <input type="email" value="{{ $email }}" disabled class="w-full bg-gray-100 border-transparent rounded-lg text-gray-500 py-3 px-4 shadow-sm cursor-not-allowed">
                    </div>
                </div>

                <!-- Phone Number (Editable) -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Phone / WhatsApp <span class="text-red-500">*</span></label>
                    <input type="tel" wire:model="phone" required class="w-full bg-white border border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 rounded-lg text-gray-900 py-3 px-4 shadow-sm transition duration-200" placeholder="+62 812-3456-7890">
                </div>

                <!-- Service Options (Cards) -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-3">Which services are you interested in?</label>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @php
                            $services = [
                                'Web Development' => 'heroicon-o-computer-desktop',
                                'SEO' => 'heroicon-o-magnifying-glass',
                                'Social Media Management' => 'heroicon-o-share',
                                'App Development' => 'heroicon-o-device-phone-mobile',
                            ];
                        @endphp

                        @foreach($services as $service => $icon)
                            <label class="relative flex items-center p-4 border rounded-xl cursor-pointer hover:bg-blue-50 transition duration-200 shadow-sm
                                @if(in_array($service, $service_options)) border-blue-500 bg-blue-50/50 ring-1 ring-blue-500 @else border-gray-200 bg-white @endif">
                                <input type="checkbox" wire:model.live="service_options" value="{{ $service }}" class="w-5 h-5 text-blue-600 border-gray-300 rounded focus:ring-blue-500 hidden">
                                <div class="flex items-center space-x-3 w-full">
                                    <div class="flex-shrink-0 w-10 h-10 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center">
                                        <!-- Fallback simple icon if heroicons not available on public view -->
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                                    </div>
                                    <span class="font-medium text-gray-900">{{ $service }}</span>
                                </div>
                                @if(in_array($service, $service_options))
                                    <svg class="absolute top-4 right-4 w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                                @endif
                            </label>
                        @endforeach
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="pt-4">
                    <button type="submit" class="w-full flex justify-center items-center py-4 px-4 border border-transparent rounded-xl shadow-md text-sm font-bold text-white bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transform transition hover:-translate-y-0.5">
                        Submit Vision Blueprint
                        <svg class="ml-2 -mr-1 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                    </button>
                </div>
            </form>
        @endif
    </div>
    <!-- Decorative bottom border -->
    <div class="h-2 w-full bg-gradient-to-r from-blue-400 via-indigo-500 to-purple-600"></div>
</div>
