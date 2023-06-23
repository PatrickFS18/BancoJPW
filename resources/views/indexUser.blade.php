<!DOCTYPE html>
<html>

<head>
    <title>Meu Banco</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
    <link rel="stylesheet" href="{{ asset('css/indexUser.css') }}">
</head>

<body>
    <div class="header">
        <div id="welcomeUser">
            <p style="font-family: 'Times New Roman', Times, serif; font-size: larger">
                @php
                date_default_timezone_set('America/Sao_Paulo'); // Defina o fuso horário desejado


                $hour = date('H');
                if ($hour >= 6 && $hour < 12) { echo 'Bom dia,' ; } elseif ($hour>= 12 && $hour < 19) { echo 'Boa tarde,' ; } else { echo 'Boa noite,' ; } @endphp <span id="spanName">user!</span>
            </p>
        </div>
        <div id="logo-jpw">
            <img src="/img/jpw.png" alt="" id="logo">
        </div>
    </div>
    <div id="banco">
        <div id="sidebar">
            <div id="buttons">
                <button type="button" class="btn btn-outline-success" id="extratoButton">Extrato</button>
                <br>
                <button type="button" class="btn btn-outline-success" id="transferenciaButton">Transferências</button>
                <br>
                <button type="button" class="btn btn-outline-success" id="pagamentoButton">Pagamentos</button>
                <br>
            </div>
        </div>
        <div id="conteudoBanco">
            <div id="conteudoBancoBackground">
                <h1 id="titleSaldo">Saldo Disponivel:</h1>
                
                @php
                $saldo="500,00";
                $saldoInput = "R$****"; // Defina o valor inicial do saldo
                $toggleButtonClass = "hide-saldo"; // Defina a classe inicial do botão

                if ($saldoInput === "R$****") {
                $saldoInput = '{{$saldo}}'; // Substitua pelo valor da variável que receberá o valor do saldo
                $toggleButtonClass = "show-saldo";
                }
                @endphp

                <h3 id="saldoInput">R$****</h3>
                <button type="button" class="btn btn-link {{ $toggleButtonClass }}" id="toggleButton" onclick="toggleSaldoVisibility()"><i class="fa fa-eye" aria-hidden="true"></i></button>
                <h5 id="limite">Seu limite é: <span id='valorLimite'> R$1000,00</span></h5>
            </div>

            <div id="divTransacao">
                <button type="button" class="btn btn-light"><img src="/img/pix.png" id='pixIcon' alt="">pix</button>
                <button type="button" class="btn btn-light"><img src="/img/pagar.png" id='payIcon' alt="">Pagar</button>
                <button type="button" class="btn btn-light" id="transferButton"><img src="/img/transferir.png" id='transferIcon' alt="">
                    <p id="transferButtonP">Transferir</p>
                </button>
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
            saldoInput.textContent = "R$50,00";
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