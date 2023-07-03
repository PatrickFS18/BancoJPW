
<!DOCTYPE html>
<html>

<head>
    <title>Meu Banco</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
    <link rel="stylesheet" href="{{ asset('css/indexUser.css') }}">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">

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

    .hide {
        display: none;
    }

    .logout-button:hover {
        background-color: #d32f2f;
    }
</style>

<body>
    <div class="header">
        <div id="welcomeUser">
            <p style="font-family: 'Times New Roman', Times, serif; font-size: larger">
            
                @php
                date_default_timezone_set('America/Sao_Paulo'); // Defina o fuso horário desejado
            $usernome=json_decode($user, true);    
                $hour = date('H');
                if ($hour >= 6 && $hour < 12) { echo 'Bom dia,' ; } elseif ($hour>= 12 && $hour < 19) { echo 'Boa tarde,' ; } else { echo 'Boa noite,' ; } @endphp <span id="spanName">{{$usernome['nome']}}!</span>
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
    <!-- Exibição de mensagens de erro -->
    @if (session('errors'))
    <div class="alert alert-danger">

        <li>{{ $errors }}</li>

    </div>
    @endif
    <!-- Exibição de mensagem de sucesso -->
    @if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
    @endif
    @if(session('warning'))
    <div class="alert alert-danger" style="background-color:aqua;">
        {{ session('warning') }}
    </div>
    @endif

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




        <!-- Formulário para confirmação do pagamento -->



        <!-- Exibir um botão para confirmar o pagamento -->


        <form method="POST" action="{{ route('pagamento-pix') }}">
            @csrf
                        <input type="hidden" name="userId" value="{{ $usernome['id'] }}">

            <input type="hidden" name="metodo" value="{{ $metodoPagamento }}">
            <input type="hidden" name="chavePix" value="{{ $chavePix }}">
            <input type="hidden" name="valor" value="{{ $valorDoPagamento }}">
            <div class="card" style="margin-left: 25em; margin-top: 10em; width: 39rem; height: 23.4rem; background-color: rgba(255,201,192,0.1)">
                <div class="card-body">
                    <h2 class="card-title" style="text-align: center; margin-top: 1em">Confirmação</h2>
                    <h4 class="card-subtitle mb-2" style="text-align: center; margin-top: 3em">Destinatário: {{ $clienteDestino['nome'] }}</h4>
                    <h4 class="card-text" style="text-align: center;">Valor do pagamento: R$ {{ $valorDoPagamento }}</h4>
                    <div style="text-align: center; margin-top: 2em">
                        <button type="submit" class="btn btn-success">Confirmar pagamento</button>
                        <a href="{{ route('pagamentos') }}" class="btn btn-danger">Cancelar pagamento</a>
                    </div>
                </div>
            </div>
        </form>


</body>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" crossorigin="anonymous"></script>
<script src="https://kit.fontawesome.com/39394938ab.js" crossorigin="anonymous"></script>
<script src="https://use.fontawesome.com/34480fc0c2.js"></script>

</html>