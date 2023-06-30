<!DOCTYPE html>
<html>

<head>
    <title>Meu Banco</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
    <link rel="stylesheet" href="{{ asset('css/indexUser.css') }}">
    <link rel="stylesheet" href="{{ asset('css/transferencias.css') }}">
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

        <div id="transferencias">
            <h1 id="h1Transferencia">Transferencia</h1>
            <div id="painelTransferencia">
                <p>Seu saldo é: R$ @php echo $user['saldo']@endphp</p>
                <form method="post" action="{{ route('transferir') }}">
                    @csrf
                    <!--Mudar para session-->

                    <p>Chave:</p><input type="text" name="numeroConta" placeholder="Insira a chave da conta">
                    <p>Valor:</p><input type="text" name="valor" placeholder="Valor da Transferência">
                    <p></p>
            </div>
            <button type="submit" class="btn btn-success" id="payButton">Transferir</button>
            </form>
        </div>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" crossorigin="anonymous"></script>
    <script src="https://kit.fontawesome.com/39394938ab.js" crossorigin="anonymous"></script>
    <script src="https://use.fontawesome.com/34480fc0c2.js"></script>
    <script>
        function toggleSaldoVisibility() {
            var saldoInput = document.getElementById("saldoInput");
            var toggleButton = document.getElementById("toggleButton");

            if (saldoInput.textContent === "R$????") {
                saldoInput.textContent = "R$50,00";
                //Patrick no lugar no 50,00 tu insere a variavel que irá receber o valor do saldo
                toggleButton.classList.remove("hide-saldo");
                toggleButton.classList.add("show-saldo");
            } else {
                saldoInput.textContent = "R$????";
                toggleButton.classList.remove("show-saldo");
                toggleButton.classList.add("hide-saldo");
            }
        }
    </script>
</body>

</html>