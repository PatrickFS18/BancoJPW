<?php

session_start();
?>




<!DOCTYPE html>
<html>

<head>
    <title>Meu Banco</title>
    <meta charset="UTF-8">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
    <link rel="stylesheet" href="{{ asset('css/indexUser.css') }}">
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
    <div id="banco">
        <div id="sidebar">
            <div id="buttons">
                <a href="{{ route('extrato') }}" class="btn btn-outline-success" id="extratoButton">Extrato</a>
                <br>
                <a href="{{ route('transferencia') }}" class="btn btn-outline-success" id="transferenciaButton">Transferências</a>
                <br>
                <a href="{{ route('pagamentos') }}" class="btn btn-outline-success" id="pagamentoButton">Pagamentos</a>
                <br>
            </div>
        </div>
        <div id="conteudoBanco">
            <div id="conteudoBancoBackground">
                <h1 id="titleSaldo">Saldo Disponivel:</h1>

                @php
                $limite=$user['limite'];
                $saldo=$user->saldo;
                $saldoInput = "R$****"; // Defina o valor inicial do saldo
                $toggleButtonClass = "hide-saldo"; // Defina a classe inicial do botão

                if ($saldoInput === "R$****") {
                $saldoInput = '{{$saldo}}'; // Substitua pelo valor da variável que receberá o valor do saldo
                $toggleButtonClass = "show-saldo";
                }
                @endphp
                <button type="button" class="btn btn-link {{ $toggleButtonClass }}" id="toggleButton" onclick="toggleSaldoVisibility()"><i class="fa fa-eye" aria-hidden="true"></i></button>
                <h3 id="saldoInput">R$****</h3>

                <h5 id="limite">Seu limite é: <span id='valorLimite'>R$ @php echo($limite); @endphp</span></h5>
            </div>

            <div id="divTransacao">
                <a class="btn btn-light" href="{{ route('pagamentos') }}"  style="margin-left: 4em;"><img src="/img/pix.png" id='pixIcon' alt="">pix</a>
                <a class="btn btn-light" href="{{ route('transferencia') }}" id="transferButton" style="margin-left: 4em;"><img src="/img/transferir.png" id='transferIcon' alt="">
                    <p id="transferButtonP">Transferir</p>
                </a>

            </div>
        </div>
        <div>
        </div>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" crossorigin="anonymous"></script>
    <script src="https://kit.fontawesome.com/39394938ab.js" crossorigin="anonymous"></script>
    <script src="https://use.fontawesome.com/34480fc0c2.js"></script>
    <script>
        function toggleSaldoVisibility() {
            var saldoInput = document.getElementById("saldoInput");
            var toggleButton = document.getElementById("toggleButton");

            if (saldoInput.textContent === "R$****") {
                saldoInput.textContent = 'R${{$saldo}}';
                toggleButton.classList.remove("btn btn-link hide-saldo");
                toggleButton.classList.add("btn btn-link show-saldo");
            } else {
                saldoInput.textContent = "R$****";
                toggleButton.classList.remove("btn btn-link show-saldo");
                toggleButton.classList.add("btn btn-link hide-saldo");
            }
        }
    </script>
</body>

</html>