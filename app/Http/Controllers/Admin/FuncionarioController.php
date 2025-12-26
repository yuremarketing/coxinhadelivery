<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Funcionario;
use Illuminate\Http\Request;

class FuncionarioController extends Controller
{
    /**
     * LISTAGEM (A TELA PRINCIPAL)
     * Mostra a lista de todos os meus funcionários.
     */
    public function index()
    {
        // Pego todo mundo do banco
        $funcionarios = Funcionario::all();
        
        // Mando pra tela de listagem (que vamos criar jaja)
        return view('admin.funcionarios.index', compact('funcionarios'));
    }

    /**
     * O FORMULÁRIO DE CADASTRO
     * Abre a tela vazia pra eu digitar os dados de um novo contratado.
     */
    public function create()
    {
        return view('admin.funcionarios.create');
    }

    /**
     * A AÇÃO DE SALVAR
     * Recebe os dados do formulário e grava no banco.
     */
    public function store(Request $request)
    {
        // 1. Validação: Não deixo salvar se faltar o básico
        $dados = $request->validate([
            'nome' => 'required|string|max:255',
            'telefone' => 'nullable|string|max:20',
            'cargo' => 'required|in:COZINHEIRO,ENTREGADOR,FAXINEIRO,BALCONISTA,OUTRO',
            // Placa só importa se for entregador, mas deixo passar string
            'placa_veiculo' => 'nullable|string|max:10',
            'modelo_veiculo' => 'nullable|string|max:50',
        ]);

        // 2. Gravar no Banco
        Funcionario::create($dados);

        // 3. Voltar pra lista avisando que deu certo
        return redirect()->route('admin.funcionarios.index')
                         ->with('sucesso', 'Funcionário contratado com sucesso!');
    }

    /**
     * O FORMULÁRIO DE EDIÇÃO
     * Abre a tela já preenchida pra eu mudar algo (ex: trocou de moto).
     */
    public function edit(Funcionario $funcionario)
    {
        return view('admin.funcionarios.edit', compact('funcionario'));
    }

    /**
     * A AÇÃO DE ATUALIZAR
     * Salva as mudanças que eu fiz.
     */
    public function update(Request $request, Funcionario $funcionario)
    {
        // 1. Valido tudo de novo
        $dados = $request->validate([
            'nome' => 'required|string|max:255',
            'telefone' => 'nullable|string|max:20',
            'cargo' => 'required',
            'placa_veiculo' => 'nullable|string|max:10',
            'modelo_veiculo' => 'nullable|string|max:50',
            'ativo' => 'boolean' // Posso ativar/desativar aqui
        ]);

        // 2. Atualizo o registro
        $funcionario->update($dados);

        // 3. Volto pra lista
        return redirect()->route('admin.funcionarios.index')
                         ->with('sucesso', 'Dados do funcionário atualizados!');
    }

    /**
     * AÇÃO DE EXCLUIR
     * Remove o funcionário do sistema.
     */
    public function destroy(Funcionario $funcionario)
    {
        $funcionario->delete();

        return redirect()->route('admin.funcionarios.index')
                         ->with('sucesso', 'Funcionário removido!');
    }
}
