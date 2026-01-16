@extends('layouts.admin')

@section('conteudo')
    {{-- O React vai renderizar o título e a tabela dentro desta div --}}
    <div id="relatorio-vendas-app" data-vendas="{{ json_encode($vendas) }}">
        <div class="p-12 text-center">
            <div class="inline-block animate-spin rounded-full h-8 w-8 border-t-2 border-b-2 border-orange-600 mb-4"></div>
            <p class="text-slate-400 font-bold uppercase text-[10px] tracking-widest">
                Carregando Relatório Profissional...
            </p>
        </div>
    </div>
@endsection
