<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Incident\ClientIncidentReportService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ClientIncidentController extends Controller
{
    /**
     * Submit client error incident report and instantly receive World Class SLA Compensation.
     */
    public function report(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'error_message' => ['required', 'string', 'max:5000'],
            'client_email' => ['required', 'email', 'max:190'],
            'client_name' => ['nullable', 'string', 'max:160'],
            'client_phone' => ['nullable', 'string', 'max:32'],
            'error_type' => ['nullable', 'string', 'max:64'],
            'vision_blueprint_id' => ['nullable', 'string', 'max:36'],
            'client_notes' => ['nullable', 'string', 'max:2000'],
            'technical_diagnostics' => ['nullable', 'array'],
            '_website' => ['nullable', 'string'],
            '_hp_check' => ['nullable', 'string'],
        ]);

        try {
            $incident = ClientIncidentReportService::recordIncidentAndCompensate($validated, $request);

            return response()->json([
                'success' => true,
                'message' => 'Laporan insiden berhasil dicatat oleh AI Engineering Squad. Kompensasi garansi SLA & voucher telah diterbitkan otomatis!',
                'data' => [
                    'incident_code' => $incident->incident_code,
                    'voucher_code' => $incident->compensation_voucher_code,
                    'compensation_amount_idr' => $incident->compensation_amount_idr,
                    'extra_sla_warranty_days' => $incident->extra_sla_warranty_days,
                    'vip_fast_track' => true,
                    'status' => $incident->status,
                ],
            ], 200);
        } catch (ValidationException $valEx) {
            return response()->json([
                'success' => false,
                'message' => $valEx->getMessage(),
                'errors' => $valEx->errors(),
            ], 422);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Gagal mencatat insiden klien: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kendala saat menyimpan laporan. Tim engineering telah menerima log sistem Anda.',
            ], 500);
        }
    }
}

