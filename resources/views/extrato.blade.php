<!DOCTYPE html>
<html>

<head>
    <title>Meu Banco</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
    <link rel="stylesheet" href="{{ asset('css/indexUser.css') }}">
    <link rel="stylesheet" href="{{ asset('css/extrato.css') }}">
</head>

<style>
    .logout-button {
        background-color: #f44336;
        color: #ffffff;
        border: none;
        padding: 10px 20px;
        text-align: center;
        text-decoration: none;
        display: inline-block;
        font-size: 16px;
        cursor: pointer;
        border-radius: 4px;
    }

    .logout-button:hover {
        background-color: #d32f2f;
    }

    .larger-td {
        width: 200px;
        /* Defina o tamanho desejado */
    }
</style>

<body>
    <div class="header">
        <div id="welcomeUser">
            <p style="font-family: 'Times New Roman', Times, serif; font-size: larger">
                @php
                date_default_timezone_set('America/Sao_Paulo'); // Defina o fuso horário desejado


                $hour = date('H');
                if ($hour >= 6 && $hour < 12) { echo 'Bom dia,' ; } elseif ($hour>= 12 && $hour < 19) { echo 'Boa tarde,' ; } else { echo 'Boa noite,' ; } @endphp <span id="spanName">{{$user->nome}}!</span>
            </p>
        </div>
        <div id="logo-jpw">
            <img src="/img/jpw.png" alt="" id="logo">
        </div>
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="logout-button">Sair</button>
        </form>
    </div>
    <div id="banco">
        <div id="sidebar">
            <div id="buttons">
                <a href="{{ route('home') }}" class="btn btn-outline-success" id="extratoButton">Home</a>
                <br>
                <a href="{{ route('extrato') }}" class="btn btn-outline-success" id="extratoButton">Extrato</a>
                <br>
                <a href="{{ route('transferencia') }}" class="btn btn-outline-success" id="transferenciaButton">Transferências</a>
                <br>
                <a href="{{ route('pagamentos') }}" class="btn btn-outline-success" id="pagamentoButton">Pagamentos</a>
                <br>
            </div>
        </div>


        <div id="extratoBancario">
            &nbsp;&nbsp; <h1 id="h1Extrato">Extrato Bancário</h1>
            <div id="painelExtrato">
                <table class="table table-bordered" id="painelExtrato">
                    <thead>
                        <tr>
                            <th scope="col">Data</th>
                            <th scope="col">Valor</th>
                            <th scope="col">Transação</th>
                            <th scope="col">Método</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($transacoes as $transacao)

                        <tr>
                            <td style="white-space: nowrap;">
                                @php
                                $dateTime = explode(' ', $transacao['Data']);
                                $data = $dateTime[0];
                                $horas = $dateTime[1];
                                @endphp

                                Data: {{ $data }}<br>
                                Horas: {{ $horas }}
                            </td>
                            <td id="valorExtrato">R$ {{ $transacao->Valor }}</td>
                            <td>{{ $transacao->Descricao }}</td>
                            <td>{{ $transacao->Tipo }}</td>
                            <td></td>

                        </tr>
                        @endforeach
                    </tbody>
                </table>

                <!--Puxar da Tabela HISTORICO, pegar o TR e jogar no php para inserir conforme for utilizado.-->
            </div>
        </div>
    </div>
    </div>
    <div>
    </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" crossorigin="anonymous"></script>
    <script src="https://kit.fontawesome.com/39394938ab.js" crossorigin="anonymous"></script>
    <script src="https://use.fontawesome.com/34480fc0c2.js"></script>
    <script>
    </script>
</body>

</html>