<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Postagens</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            color: #333;
            margin: 0;
            padding: 0;
        }
        h1 {
            text-align: center;
            color: #444;
            margin-top: 20px;
        }
        a {
            text-decoration: none;
            color: #007bff;
            margin-left: 20px;
        }
        a:hover {
            color: #0056b3;
        }
        .container {
            max-width: 900px;
            margin: 0 auto;
            padding: 20px;
        }
        .postagem {
            background-color: #fff;
            border: 1px solid #ddd;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s;
        }
        .postagem:hover {
            transform: translateY(-5px);
        }
        .imagem {
            max-width: 100%;
            height: auto;
            margin-top: 10px;
            border-radius: 5px;
        }
        h2 {
            color: #333;
            font-size: 1.6em;
            margin-bottom: 10px;
        }
        p {
            font-size: 1em;
            line-height: 1.6;
            color: #666;
        }
        button {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
            cursor: pointer;
            margin-left: 20px;
            transition: background-color 0.3s;
        }
        button:hover {
            background-color: #0056b3;
        }
        hr {
            border: 0;
            height: 1px;
            background-color: #ddd;
            margin-top: 30px;
        }
        .nav-links {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="nav-links">
        <a href="cadrasto/sistemas.html">
           <button>Voltar</button> 
        </a>
        <a href="postar.html">
            <button>Enviar Nova Postagem</button>
    </a>
    </div>
    
    <h1>Postagens</h1>
    <hr>

    <?php
    // Configurações de conexão com o banco de dados
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = 'banco_exart';

    // Cria conexão
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Verifica conexão
    if ($conn->connect_error) {
        die("Conexão falhou: " . $conn->connect_error);
    }

    // Consulta para obter as postagens
    $sql = "SELECT titulo, poema, imagem FROM postagens";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Saída dos dados de cada linha
        while($row = $result->fetch_assoc()) {
            echo "<div class='postagem'>";
            echo "<h2>" . htmlspecialchars($row["titulo"]) . "</h2>";
            echo "<p>" . nl2br(htmlspecialchars($row["poema"])) . "</p>";
            if (!empty($row["imagem"])) {
                echo "<img src='" . htmlspecialchars($row["imagem"]) . "' alt='Imagem do Poema' class='imagem'>";
            }
            echo "</div>";
        }
    } else {
        echo "<p>Nenhuma postagem encontrada.</p>";
    }

    // Fecha a conexão
    $conn->close();
    ?>
</div>

</body>
</html>
