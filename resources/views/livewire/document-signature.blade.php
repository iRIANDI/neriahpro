<div class="max-w-3xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
    <div class="bg-white shadow sm:rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900">
                Persetujuan Dokumen: {{ $document->title }}
            </h3>
            <div class="mt-2 max-w-xl text-sm text-gray-500">
                <p>Tipe: {{ str($document->document_type)->headline() }}</p>
                <p>Status: <span class="font-bold {{ $isSigned ? 'text-green-600' : 'text-yellow-600' }}">{{ str($document->status)->headline() }}</span></p>
            </div>
            
            @if($isSigned)
                <div class="mt-5 rounded-md bg-green-50 p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <!-- Heroicon name: solid/check-circle -->
                            <svg class="h-5 w-5 text-green-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-green-800">
                                Dokumen ini telah ditandatangani
                            </h3>
                            <div class="mt-2 text-sm text-green-700">
                                <p>Ditandatangani pada: {{ $document->signed_at->format('d M Y H:i:s') }} (Zona Waktu: {{ config('app.timezone') }})</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                @if($document->digital_signature_image)
                    <div class="mt-6 border border-gray-200 rounded-lg p-4 bg-gray-50">
                        <h4 class="text-sm font-medium text-gray-700 mb-2">Tanda Tangan yang Tercatat:</h4>
                        <img src="{{ $document->digital_signature_image }}" alt="Tanda Tangan" class="max-w-full h-auto max-h-48" />
                    </div>
                @endif
            @else
                <form wire:submit="submitSignature" class="mt-8 space-y-6">
                    
                    {{ $this->form }}

                    <div class="pt-5">
                        <div class="flex justify-end">
                            <button type="submit" class="ml-3 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Kirim Tanda Tangan
                            </button>
                        </div>
                    </div>
                </form>
            @endif
        </div>
    </div>
</div>
