<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller; // Controller base do Laravel
use App\Models\Pedido;               // Model Pedido que criamos
use App\Models\Produto;              // Model Produto que criamos  
use App\Models\PedidoItem;           // Model PedidoItem que criamos
use Illuminate\Http\Request;         // Para receber dados da requisição
use Illuminate\Http\JsonResponse;    // Para retornar respostas JSON
use Illuminate\Support\Facades\Validator; // Para validar dados
use Illuminate\Support\Facades\DB;        // Para transações no banco

class PedidoController extends Controller
{
    /**
     * Cria um novo pedido
     * POST /api/pedidos
     */
    public function store(Request $request): JsonResponse
    {
        // Valida dados básicos do pedido usando regras definidas
        $validator = Validator::make($request->all(), [
            'cliente_nome' => 'required|string|max:100',       // Nome obrigatório, máximo 100 caracteres
            'cliente_telefone' => 'required|string|max:20',    // Telefone obrigatório, máximo 20 chars
            'cliente_email' => 'nullable|email|max:100',       // Email opcional, mas se informado deve ser válido
            'tipo' => 'required|in:retirada,entrega',          // Deve ser um desses dois valores
            'observacoes' => 'nullable|string|max:500',        // Observações opcionais, máximo 500 chars
            'itens' => 'required|array|min:1',                 // Deve ter pelo menos 1 item no array
            'itens.*.produto_id' => 'required|exists:produtos,id', // Cada produto_id deve existir na tabela produtos
            'itens.*.quantidade' => 'required|integer|min:1',  // Quantidade deve ser número inteiro positivo
            'itens.*.observacoes_item' => 'nullable|string|max:200' // Observações específicas do item
        ]);

        // Se validação falhar, retorna erro 422 com detalhes
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Erro de validação',
                'errors' => $validator->errors()
            ], 422);
        }

        // Inicia transação - garante que tudo salva ou nada salva
        DB::beginTransaction();

        try {
            // Pega dados validados (após passar pela validação)
            $dados = $validator->validated();
            $valorTotal = 0;                     // Inicializa total do pedido como zero
            $itensValidados = [];                // Array para guardar itens após validação

            // Valida cada item do pedido individualmente
            foreach ($dados['itens'] as $item) {
                // Busca produto no banco pelo ID
                $produto = Produto::find($item['produto_id']);
                
                // Verifica se produto existe
                if (!$produto) {
                    throw new \Exception("Produto ID {$item['produto_id']} não encontrado");
                }
                
                // Verifica se produto está disponível (estoque > 0 e disponivel = true)
                if (!$produto->estaDisponivel()) {
                    throw new \Exception("Produto {$produto->nome} indisponível");
                }
                
                // Verifica se tem estoque suficiente
                if ($produto->estoque < $item['quantidade']) {
                    throw new \Exception("Estoque insuficiente para {$produto->nome}");
                }
                
                // Calcula subtotal do item: preço × quantidade
                $subtotal = $produto->preco * $item['quantidade'];
                $valorTotal += $subtotal; // Soma ao total geral
                
                // Guarda item validado para criar registro depois
                $itensValidados[] = [
                    'produto' => $produto,                    // Objeto produto completo
                    'dados' => $item,                         // Dados originais do item
                    'preco_unitario' => $produto->preco,      // Preço na hora da compra
                    'subtotal' => $subtotal                   // Total deste item
                ];
            }

            // Cria registro do pedido na tabela 'pedidos'
            $pedido = Pedido::create([
                'cliente_nome' => $dados['cliente_nome'],      // Nome do cliente
                'cliente_telefone' => $dados['cliente_telefone'], // Telefone
                'cliente_email' => $dados['cliente_email'] ?? null, // Email (ou null)
                'tipo' => $dados['tipo'],                      // Retirada ou entrega
                'observacoes' => $dados['observacoes'] ?? null, // Observações gerais
                'valor_total' => $valorTotal,                   // Total calculado
                'status' => 'pendente'                         // Status inicial
            ]);

            // Cria cada item do pedido na tabela 'pedido_itens'
            foreach ($itensValidados as $item) {
                // Cria registro do item
                PedidoItem::create([
                    'pedido_id' => $pedido->id,                // ID do pedido criado
                    'produto_id' => $item['produto']->id,      // ID do produto
                    'quantidade' => $item['dados']['quantidade'], // Quantidade comprada
                    'preco_unitario' => $item['preco_unitario'], // Preço na hora da compra
                    'subtotal' => $item['subtotal'],           // Subtotal calculado
                    'observacoes_item' => $item['dados']['observacoes_item'] ?? null // Obs do item
                ]);
                
                // Atualiza estoque do produto (diminui quantidade vendida)
                $item['produto']->diminuirEstoque($item['dados']['quantidade']);
            }

            // Confirma transação - salva tudo definitivamente no banco
            DB::commit();

            // Retorna resposta de sucesso com dados importantes
            return response()->json([
                'success' => true,
                'message' => 'Pedido criado com sucesso',
                'data' => [
                    'pedido_id' => $pedido->id,                // ID interno do pedido
                    'numero_pedido' => $pedido->numero_pedido, // Número único para cliente
                    'valor_total' => $pedido->valor_total,     // Valor total
                    'status' => $pedido->status                // Status atual
                ]
            ], 201); // HTTP 201: Created

        } catch (\Exception $e) {
            // Reverte todas alterações do banco se houver qualquer erro
            DB::rollBack();
            
            // Retorna erro com mensagem da exceção
            return response()->json([
                'success' => false,
                'message' => 'Erro ao criar pedido: ' . $e->getMessage()
            ], 500); // HTTP 500: Internal Server Error
        }
    }

    /**
     * Mostra detalhes de um pedido pelo código/número
     * GET /api/pedidos/{codigo}
     * Usado pelo cliente para acompanhar pedido
     */
    public function show(string $codigo): JsonResponse
    {
        try {
            // Busca pedido pelo número único (não pelo ID interno)
            $pedido = Pedido::where('numero_pedido', $codigo)->first();

            // Se não encontrar, retorna erro 404
            if (!$pedido) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pedido não encontrado'
                ], 404);
            }

            // Carrega relacionamentos: itens do pedido e produtos desses itens
            $pedido->load(['itens.produto']);

            // Retorna pedido completo com itens
            return response()->json([
                'success' => true,
                'message' => 'Pedido encontrado',
                'data' => $pedido
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao buscar pedido'
            ], 500);
        }
    }

    /**
     * Lista todos pedidos (apenas admin)
     * GET /api/admin/pedidos
     * Precisa de autenticação (middleware auth:sanctum)
     */
    public function index(Request $request): JsonResponse
    {
        try {
            // Cria query base com relacionamentos carregados
            $query = Pedido::with(['itens.produto']);

            // Filtro por status (ex: ?status=pendente)
            if ($request->has('status')) {
                $query->where('status', $request->status);
            }

            // Filtro por data inicial (ex: ?data_inicio=2024-12-01)
            if ($request->has('data_inicio')) {
                $query->whereDate('created_at', '>=', $request->data_inicio);
            }

            // Filtro por data final
            if ($request->has('data_fim')) {
                $query->whereDate('created_at', '<=', $request->data_fim);
            }

            // Ordena por data de criação, mais recentes primeiro
            $query->orderBy('created_at', 'desc');

            // Paginação (padrão: 20 itens por página)
            $pedidos = $query->paginate($request->get('per_page', 20));

            return response()->json([
                'success' => true,
                'message' => 'Pedidos listados com sucesso',
                'data' => $pedidos
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao listar pedidos'
            ], 500);
        }
    }

    /**
     * Atualiza status de um pedido (apenas admin)
     * PUT /api/admin/pedidos/{id}/status
     * Usado para mudar status: pendente → confirmado → em_preparo → etc.
     */
    public function updateStatus(Request $request, int $id): JsonResponse
    {
        try {
            // Busca pedido pelo ID interno
            $pedido = Pedido::find($id);

            if (!$pedido) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pedido não encontrado'
                ], 404);
            }

            // Valida que o status fornecido é válido
            $validator = Validator::make($request->all(), [
                'status' => 'required|in:pendente,confirmado,em_preparo,pronto,entregue,cancelado'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Status inválido',
                    'errors' => $validator->errors()
                ], 422);
            }

            $novoStatus = $request->status;
            
            // Verifica regras de negócio para cancelamento
            if ($novoStatus === 'cancelado' && !$pedido->podeSerCancelado()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pedido não pode ser cancelado neste status'
                ], 400);
            }

            // Atualiza status usando método do Model Pedido
            $pedido->atualizarStatus($novoStatus);

            return response()->json([
                'success' => true,
                'message' => 'Status atualizado com sucesso',
                'data' => $pedido
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao atualizar status'
            ], 500);
        }
    }

    /**
     * Mostra detalhes de um pedido para admin (com mais informações)
     * GET /api/admin/pedidos/{id}
     * Similar ao show(), mas usa ID interno em vez do número do pedido
     */
    public function adminShow(int $id): JsonResponse
    {
        try {
            // Busca por ID interno com relacionamentos
            $pedido = Pedido::with(['itens.produto'])->find($id);

            if (!$pedido) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pedido não encontrado'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Pedido encontrado',
                'data' => $pedido
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao buscar pedido'
            ], 500);
        }
    }
}