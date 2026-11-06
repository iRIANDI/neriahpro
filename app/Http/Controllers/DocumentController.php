<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Document;
use Barryvdh\DomPDF\Facade\Pdf;

class DocumentController extends Controller
{
    public function preview(Request $request, Document $document)
    {
        $locale = $request->query('lang', $request->query('locale', 'id'));
        $pdf = Pdf::loadView('pdf.document', compact('document', 'locale'));
        return $pdf->stream('document-' . $document->id . '.pdf');
    }
}
