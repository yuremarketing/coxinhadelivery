<?php

// Namespace padrão do Laravel para controllers
// Todos controllers ficam nesta pasta
namespace App\Http\Controllers\API;

// Importa a classe base Controller do Laravel
// Fornece métodos helpers como response(), validate(), etc.
use App\Http\Controllers\Controller;

// Importa o Model Produto que criamos
// Permite interagir com a tabela 'produtos' do banco
use App\Models\Produto;

// Importa a classe Request do Laravel
// Captura dados das requisições HTTP (GET, POST, etc.)
use Illuminate\Http\Request;

// Importa JsonResponse para retornar respostas JSON padronizadas
// Extende a Response normal mas já formata como JSON
use Illuminate\Http\JsonResponse;

// Importa Validator para validar dados de entrada
// Cria regras de validação e mensagens de erro
use Illuminate\Support\Facades\Validator;

/**
 * ProdutoController
 * Controlador para gerenciar operações relacionadas a produtos
 * Segue padrão RESTful API
 */
class ProdutoController extends Controller
{
    /**
     * Lista todos os produtos disponíveis
     * Método: GET
     * Endpoint: /api/produtos
     * 
     * @param Request $request Objeto da requisição (contém query params)
     * @return JsonResponse Resposta JSON com lista de produtos
     */
    public function index(Request $request): JsonResponse
    {
        try {
            // Inicia query com scope 'disponiveis' (definido no Model)
            // Filtra apenas produtos com estoque > 0 e disponivel = true
            $query = Produto::disponiveis();

            // Filtro opcional: categoria
            // Ex: /api/produtos?categoria=coxinhas
            if ($request->has('categoria')) {
                $query->where('categoria', $request->categoria);
            }

            // Filtro opcional: busca no nome
            // Ex: /api/produtos?busca=frango
            if ($request->has('busca')) {
                $query->where('nome', 'like', '%' . $request->busca . '%');
            }

            // Ordenação dos resultados
            // Parâmetros: ordenar (campo) e direcao (asc/desc)
            // Ex: /api/produtos?ordenar=preco&direcao=desc
            $ordenacao = $request->get('ordenar', 'nome'); // Padrão: ordenar por nome
            $direcao = $request->get('direcao', 'asc');    // Padrão: ascendente
            $query->orderBy($ordenacao, $direcao);

            // Paginação para não sobrecarregar com muitos dados
            // Parâmetro: per_page (itens por página)
            // Ex: /api/produtos?per_page=10
            $perPage = $request->get('per_page', 20); // Padrão: 20 itens por página
            $produtos = $query->paginate($perPage);

            // Retorna resposta de sucesso com dados paginados
            return response()->json([
                'success' => true,
                'message' => 'Produtos listados com sucesso',
                'data' => $produtos
            ]);

        } catch (\Exception $e) {
            // Captura qualquer exceção não tratada
            // Em produção, não expõe detalhes do erro (segurança)
            return response()->json([
                'success' => false,
                'message' => 'Erro ao listar produtos',
                'error' => env('APP_DEBUG') ? $e->getMessage() : null
            ], 500); // HTTP 500: Internal Server Error
        }
    }

    /**
     * Exibe um produto específico
     * Método: GET
     * Endpoint: /api/produtos/{id}
     * 
     * @param int $id ID do produto
     * @return JsonResponse Resposta JSON com detalhes do produto
     */
    public function show(int $id): JsonResponse
    {
        try {
            // Busca produto disponível pelo ID
            // Usa scope 'disponiveis' para não retornar produtos indisponíveis
            $produto = Produto::disponiveis()->find($id);

            // Verifica se produto foi encontrado
            if (!$produto) {
                return response()->json([
                    'success' => false,
                    'message' => 'Produto não encontrado ou indisponível'
                ], 404); // HTTP 404: Not Found
            }

            // Retorna produto encontrado
            return response()->json([
                'success' => true,
                'message' => 'Produto encontrado',
                'data' => $produto
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao buscar produto',
                'error' => env('APP_DEBUG') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Cria um novo produto (Apenas admin)
     * Método: POST
     * Endpoint: /api/produtos
     * 
     * @param Request $request Dados do novo produto
     * @return JsonResponse Resposta JSON com produto criado
     */
    public function store(Request $request): JsonResponse
    {
        try {
            // Define regras de validação para os dados de entrada
            $regras = [
                'nome' => 'required|string|max:100',
                'descricao' => 'nullable|string|max:500',
                'preco' => 'required|numeric|min:0|max:999999.99',
                'categoria' => 'required|string|max:50',
                'estoque' => 'required|integer|min:0',
                'disponivel' => 'boolean'
            ];

            // Cria validador com regras e mensagens personalizadas
            $validador = Validator::make($request->all(), $regras, [
                'nome.required' => 'O nome do produto é obrigatório',
                'preco.min' => 'O preço não pode ser negativo',
                'estoque.min' => 'O estoque não pode ser negativo'
            ]);

            // Verifica se a validação falhou
            if ($validador->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erro de validação',
                    'errors' => $validador->errors()
                ], 422); // HTTP 422: Unprocessable Entity
            }

            // Cria produto com dados validados
            $produto = Produto::create($validador->validated());

            // Retorna resposta de sucesso com produto criado
            return response()->json([
                'success' => true,
                'message' => 'Produto criado com sucesso',
                'data' => $produto
            ], 201); // HTTP 201: Created

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao criar produto',
                'error' => env('APP_DEBUG') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Atualiza um produto existente (Apenas admin)
     * Método: PUT/PATCH
     * Endpoint: /api/produtos/{id}
     * 
     * @param Request $request Dados para atualização
     * @param int $id ID do produto a ser atualizado
     * @return JsonResponse Resposta JSON com produto atualizado
     */
    public function update(Request $request, int $id): JsonResponse
    {
        try {
            // Busca produto pelo ID (mesmo se não estiver disponível)
            $produto = Produto::find($id);

            // Verifica se produto existe
            if (!$produto) {
                return response()->json([
                    'success' => false,
                    'message' => 'Produto não encontrado'
                ], 404);
            }

            // Define regras de validação (menos restritivas que store)
            // 'sometimes' = valida apenas se o campo estiver presente
            $regras = [
                'nome' => 'sometimes|string|max:100',
                'descricao' => 'nullable|string|max:500',
                'preco' => 'sometimes|numeric|min:0|max:999999.99',
                'categoria' => 'sometimes|string|max:50',
                'estoque' => 'sometimes|integer|min:0',
                'disponivel' => 'sometimes|boolean'
            ];

            // Valida dados
            $validador = Validator::make($request->all(), $regras);

            if ($validador->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erro de validação',
                    'errors' => $validador->errors()
                ], 422);
            }

            // Atualiza produto apenas com campos fornecidos
            $produto->update($validador->validated());

            return response()->json([
                'success' => true,
                'message' => 'Produto atualizado com sucesso',
                'data' => $produto
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao atualizar produto',
                'error' => env('APP_DEBUG') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Remove (ou desativa) um produto (Apenas admin)
     * Método: DELETE
     * Endpoint: /api/produtos/{id}
     * 
     * @param int $id ID do produto a ser removido
     * @return JsonResponse Resposta JSON de confirmação
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $produto = Produto::find($id);

            if (!$produto) {
                return response()->json([
                    'success' => false,
                    'message' => 'Produto não encontrado'
                ], 404);
            }

            // Soft delete: marca como indisponível em vez de apagar do banco
            // Preserva histórico e referências em pedidos antigos
            $produto->update(['disponivel' => false, 'estoque' => 0]);

            return response()->json([
                'success' => true,
                'message' => 'Produto removido com sucesso'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao remover produto',
                'error' => env('APP_DEBUG') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Lista categorias disponíveis
     * Método: GET
     * Endpoint: /api/produtos/categorias
     * 
     * @return JsonResponse Resposta JSON com lista de categorias
     */
    public function categorias(): JsonResponse
{
    try {
        // Método mais seguro e compatível
        $categorias = Produto::disponiveis()
            ->select('categoria')
            ->distinct()
            ->orderBy('categoria')
            ->get()
            ->pluck('categoria');

        return response()->json([
            'success' => true,
            'message' => 'Categorias listadas com sucesso',
            'data' => $categorias
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Erro ao listar categorias',
            'error' => env('APP_DEBUG') ? $e->getMessage() : null
        ], 500);
    }
}
}