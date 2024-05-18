<?php
session_start();

// Inicializar as cadeiras e clientes (somente na primeira execução)
if (!isset($_SESSION['seats'])) {
    $_SESSION['seats'] = [
        'A1' => ['reserved' => false, 'name' => ''],
        'A2' => ['reserved' => false, 'name' => ''],
        'A3' => ['reserved' => false, 'name' => ''],
        'A4' => ['reserved' => false, 'name' => ''],
        'A5' => ['reserved' => false, 'name' => ''],
        'B1' => ['reserved' => false, 'name' => ''],
        'B2' => ['reserved' => false, 'name' => ''],
        'B3' => ['reserved' => false, 'name' => ''],
        'B4' => ['reserved' => false, 'name' => ''],
        'B5' => ['reserved' => false, 'name' => ''],
    ];
    $_SESSION['clients'] = [];
}

// Verificar se há uma solicitação de reserva
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['seat_number']) && isset($_POST['customer_name'])) {
    $seat_number = $_POST['seat_number'];
    $customer_name = trim($_POST['customer_name']);
    
    if (isset($_SESSION['seats'][$seat_number]) && !$_SESSION['seats'][$seat_number]['reserved'] && !empty($customer_name)) {
        $_SESSION['seats'][$seat_number] = ['reserved' => true, 'name' => $customer_name];
        $_SESSION['clients'][] = $customer_name;
        $message = "Reserva confirmada para a cadeira $seat_number pelo cliente $customer_name!";
    } else {
        $message = "Erro: a cadeira $seat_number já está reservada, não existe, ou o nome do cliente está vazio.";
    }
}

// Verificar se há uma solicitação de sorteio
if (isset($_POST['draw_winner'])) {
    $occupied_seats = array_filter($_SESSION['seats'], function($seat_info) {
        return $seat_info['reserved'];
    });

    if (!empty($occupied_seats)) {
        $random_seat = array_rand($occupied_seats);
        $winner = $occupied_seats[$random_seat]['name'];
        $message = "O cliente sorteado para ganhar o brinde é: $winner!";
    } else {
        $message = "Erro: não há cadeiras ocupadas para o sorteio.";
    }
}

// Verificar se há uma solicitação de liberar todas as posições
if (isset($_POST['release_all'])) {
    foreach ($_SESSION['seats'] as $seat_number => $seat_info) {
        $_SESSION['seats'][$seat_number] = ['reserved' => false, 'name' => ''];
    }
    $_SESSION['clients'] = [];
    $message = "Todas as cadeiras foram liberadas.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style2.css">
    <title>Escolher lugar</title>
</head>
<body>
    <div class="movie-container">
        <label>Escolha um filme:</label>
        <select id="movie">
            <option value="12">Todo poderoso: O filme. ($12)</option>
            <option value="10">A historia de um sonho.($10)</option>
            <option value="15">1976 O ano da invasão corinthiana. ($15)</option>
        </select>
    </div>

    <ul class="showcase">
        <li>
            <div class="seat"></div>
            <small>Livre</small>
        </li>

        <li>
            <div class="seat selected"></div>
            <small>Selecionado</small>
        </li>

        <li>
            <div class="seat occupied"></div>
            <small>Ocupado</small>
        </li>
    </ul>

    <div class="container">
        <div class="screen"></div>

        <?php
        // Criação das filas de cadeiras
        $rows = [
            ['A1', 'A2', 'A3', 'A4', 'A5', 'A6', 'A7', 'A8', 'A9', 'A10'],
            ['B1', 'B2', 'B3', 'B4', 'B5', 'B6', 'B7', 'B8', 'B9', 'B10'],
            ['C1', 'C2', 'C3', 'C4', 'C5','C6', 'C7', 'C8', 'C9', 'C10' ],
            ['D1', 'D2', 'D3', 'D4', 'D5', 'D6', 'D7', 'D8', 'D9', 'D10'],
            ['E1', 'E2', 'E3', 'E4', 'E5', 'E6', 'E7', 'E8', 'E9', 'E10'],
        ];

        foreach ($rows as $row) {
            echo '<div class="row">';
            foreach ($row as $seat_number) {
                if ($_SESSION['seats'][$seat_number]['reserved']) {
                    echo "<div class='seat occupied'></div>";
                } else {
                    echo "<div class='seat' onclick='reservarCadeira(this)'>$seat_number</div>";
                }
            }
            echo '</div>';
        }
        ?>

    </div>

    <p class="text">
        Você selecionou <span id="count">0</span> cadeira(s) pelo preço de $<span id="total">0</span>
    </p>

    <div class="container">
        <div class="screen"></div>
    </div>
    
    <p class="text">
        Cadeira reservada por: <span id="nomeReserva">Nenhum</span>
    </p>
    
    <button onclick="sortearGanhador()">Sortear Ganhador</button>
    
    <script>
    function reservarCadeira(seat) {
        var nome = prompt("Por favor, insira o seu nome:");
        if (nome.trim() === "" || nome === null) {
            alert("Por favor, insira o nome do cliente.");
            return;
        }
    
        var output = document.getElementById("nomeReserva");
        output.textContent = "Cadeira reservada por: " + nome + " (Cadeira " + seat.textContent + ")";
    
        // Atualiza visualmente o estado da cadeira
        seat.classList.add("occupied");
    }
   
    function sortearGanhador() {
        var occupiedSeats = document.querySelectorAll('.seat.occupied');
        
        if (occupiedSeats.length > 0) {
            var randomIndex = Math.floor(Math.random() * occupiedSeats.length);
            var ganhador = occupiedSeats[randomIndex].textContent.trim();
    
            var premio = ["pipoca", "refrigerante", "poster autografado"];
            var sorteio = premio[Math.floor(Math.random() * premio.length)];
    
            alert("O ganhador do prêmio " + sorteio + " é: " + ganhador);
        } else {
            alert("Erro: Não há cadeiras ocupadas para o sorteio.");
        }
    }
    </script>

    <script src="script.js"></script>
</body>
</html>
