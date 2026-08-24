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
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'company_name' => 'nullable|string|max:255',
            'project_needs' => 'required|array',
            'budget_range' => 'required|string',
            'privacy_consent_agreed' => 'required|boolean|accepted',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $onboarding = ClientOnboarding::create($validator->validated());

        return response()->json([
            'message' => 'Onboarding data secured successfully.',
            'id' => $onboarding->id
        ], 201);
    }
}
