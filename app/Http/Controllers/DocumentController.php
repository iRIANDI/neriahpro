<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Document;
use Barryvdh\DomPDF\Facade\Pdf;

class DocumentController extends Controller
{
    public function preview(Document $document)
    {
        $pdf = Pdf::loadView('pdf.document', compact('document'));
        return $pdf->stream('document-' . $document->id . '.pdf');
    }
}
