<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\LojaConfig;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class LojaConfigController extends Controller
{
    /**
     * O App pergunta: "E aí, tá aberto? Tem live rolando?".
     * Eu respondo com a verdade do momento. Essa rota todo mundo pode ver,
     * porque o cliente precisa saber se a gente tá atendendo antes de pedir.
     */
    public function index(): JsonResponse
    {
        // Pega a primeira (e única) linha de configuração
        $config = LojaConfig::first();
        
        return response()->json([
            'success' => true, 
            'data' => $config
        ]);
    }

    /**
     * Aqui é onde eu assumo o controle. 
     * Fecho a loja se der ruim (acabou o gás), colo o link da live nova 
     * ou aumento o tempo de espera se a cozinha estiver um caos.
     * Só o patrão (eu) mexe aqui.
     */
    public function update(Request $request): JsonResponse
    {
        // Validação básica pra não salvar bobagem
        $validator = Validator::make($request->all(), [
            'link_youtube' => 'nullable|url', // Tem que ser link válido ou nulo
            'loja_aberta' => 'required|boolean',
            'tempo_espera_minutos' => 'required|integer|min:0'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        // Atualiza a configuração existente
        $config = LojaConfig::first();
        $config->update($validator->validated());

        return response()->json([
            'success' => true,
            'message' => 'Configurações da loja atualizadas com sucesso',
            'data' => $config
        ]);
    }
}
