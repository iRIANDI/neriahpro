<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\ClientOnboarding;
use Illuminate\Support\Facades\Validator;

class OnboardingController extends Controller
{
    public function store(Request $request)
    {
        if ($request->filled('email')) {
            $request->merge([
                'email' => \App\Support\InputHygieneStandard::sanitizeStrictEmail((string) $request->input('email')),
            ]);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', 'max:255', 'regex:/^[a-zA-Z0-9._%+\-]+@[a-zA-Z0-9.\-]+\.[a-zA-Z]{2,}$/'],
            'phone' => 'nullable|string|max:50',
            'country_code' => 'nullable|string|max:10',
            'company_name' => 'nullable|string|max:255',
            'project_needs' => 'required|array',
            'budget_range' => 'required|string',
            'privacy_consent_agreed' => 'required|boolean|accepted',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $payload = $validator->validated();
        unset($payload['phone'], $payload['country_code']);

        $onboarding = ClientOnboarding::create($payload);

        return response()->json([
            'message' => 'Onboarding data secured successfully.',
            'id' => $onboarding->id
        ], 201);
    }
}
