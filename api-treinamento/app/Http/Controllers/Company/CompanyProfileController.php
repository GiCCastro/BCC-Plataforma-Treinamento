<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


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
        try {
            $company = auth('company')->user();

            if (!$company) {
                return response()->json(['message' => 'Empresa não autenticada'], 401);
            }

            $validator = \Validator::make($request->all(), [
                'logo' => 'nullable',
                'banner' => 'nullable',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Dados inválidos',
                    'errors' => $validator->errors()
                ], 422);
            }

            DB::beginTransaction();

            // Atualiza o logo, se enviado
            if ($request->hasFile('logo')) {
                $file = $request->file('logo');
                $mime = $file->getClientMimeType();
                $base64Logo = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($file->getRealPath()));
                $company->logo = $base64Logo;
            } elseif ($request->filled('logo')) {
                // Caso já venha em Base64 no corpo
                $company->logo = $request->logo;
            }

            // Atualiza o banner, se enviado
            if ($request->hasFile('banner')) {
                $file = $request->file('banner');
                $mime = $file->getClientMimeType();
                $base64Banner = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($file->getRealPath()));
                $company->banner = $base64Banner;
            } elseif ($request->filled('banner')) {
                // Caso já venha em Base64 no corpo
                $company->banner = $request->banner;
            }

            $company->save();

            DB::commit();

            return response()->json([
                'message' => 'Logo e/ou banner atualizados com sucesso',
                'data' => [
                    'logo' => $company->logo,
                    'banner' => $company->banner,
                ]
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Erro ao atualizar logo/banner',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();

        try{
            $company = auth('company')->user();
        
            if(!$company){
                return response()->json(['message' => 'Empresa não autenticada'], 401);
            }

            $company->delete();

            DB::commit();

            return response()->json([
                'message' => 'Empresa deletada com sucesso!'
            ], 200);

        } catch(\Exception $e){
            DB::rollBack();

            return response()->json([
                'message' => 'Erro ao deletar empresa',
                'error' => $e->getMessage(),
            ], 500);

        }
    }
}

