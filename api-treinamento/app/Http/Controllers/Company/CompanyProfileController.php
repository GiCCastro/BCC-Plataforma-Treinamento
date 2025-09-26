<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CompanyProfileController extends Controller
{
    public function updateProfile(Request $request)
    {
        $company = $request->user();

        $request->validate([
            'primary_color' => 'nullable|string|max:20',
            'secondary_color' => 'nullable|string|max:20',
            'text_color' => 'nullable|string|max:20',
            'button_color' => 'nullable|string|max:20',
            'font' => 'nullable|string|max:100',
        ]);

        $company->update($request->only([
            'primary_color',
            'secondary_color',
            'text_color',
            'button_color',
            'font',
        ]));

        return response()->json([
            'message' => 'Perfil atualizado com sucesso',
            'company' => $company
        ], 200);
    }

    
    public function uploadAssets(Request $request)
    {
        $company = $request->user();

        $request->validate([
            'logo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'banner' => 'nullable|image|mimes:jpg,jpeg,png|max:4096',
        ]);

        $data = [];

        if ($request->hasFile('logo')) {
            $logoMime = $request->file('logo')->getMimeType();
            if (!in_array($logoMime, ['image/jpeg', 'image/png'])) {
                return response()->json(['error' => 'Apenas imagens JPG ou PNG são permitidas para logo'], 422);
            }
            $data['logo'] = $request->file('logo')->store('logos', 'public');
        }

        if ($request->hasFile('banner')) {
            $bannerMime = $request->file('banner')->getMimeType();
            if (!in_array($bannerMime, ['image/jpeg', 'image/png'])) {
                return response()->json(['error' => 'Apenas imagens JPG ou PNG são permitidas para banner'], 422);
            }
            $data['banner'] = $request->file('banner')->store('banners', 'public');
        }

        $company->update($data);

        return response()->json([
            'message' => 'Imagens atualizadas com sucesso',
            'company' => $company
        ], 200);
    }


}

