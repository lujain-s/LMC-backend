<?php

namespace App\Http\Controllers;

use App\Services\LanguageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class LanguageController extends Controller
{
    protected $languageService;
    
    public function __construct(LanguageService $languageService){
            $this->languageService = $languageService;
    }
    
    public function addLanguage(Request $request){
            $language = $this->languageService->addLanguage($request->all());
    
            return response()->json([
                'message' => 'Language added successfully',
                'data' => $language,
            ], 201);
    }
    
    public function updateLanguage(Request $request, $id)
    {
     $response = $this->languageService->updateLanguage($id, $request->all());

     if (!$response['status']) {
        return response()->json([
            'message' => $response['message'],
            'errors' => $response['errors'] ?? null,
        ], 400); // Bad Request
     }

     return response()->json([
        'message' => $response['message'],
        'data' => $response['data'],
     ]);
    }

    public function deleteLanguage($id)
    {
     $user = Auth::user();

     if (!$user) {
        return response()->json([
            'status' => 'error',
            'message' => 'Unauthenticated'
        ], 401);
     }

     $response = $this->languageService->deleteLanguage($id, $user->id);

     return response()->json($response, $response['status_code']);
    }

    public function showLanguage($id)
    {
     $response = $this->languageService->getLanguageDetails($id);

     return response()->json($response, $response['status_code']);
    }

    public function showAllLanguage()
    {
     $response = $this->languageService->getAllLanguage();

     return response()->json($response, $response['status_code']);
    }
    
}
