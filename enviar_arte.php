<?php
session_start(); // Inicia a sessão

// Verifica se o usuário está logado
if (!isset($_SESSION['id_usuario'])) {
    $mensagem = "Você precisa estar logado para enviar uma arte.";
    $tipo = "erro";
} else {
    // Configuração do banco de dados
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "banco_exart";

    // Criando a conexão
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Verifica a conexão
    if ($conn->connect_error) {
        $mensagem = "Erro ao conectar ao banco de dados.";
        $tipo = "erro";
    } else {
        // Recebe os dados do formulário
        $titulo = $_POST['titulo'] ?? '';
        $conteudo = $_POST['conteudo'] ?? '';
        $categoria_id = $_POST['categoria'] ?? 0;
        $usuario_id = $_SESSION['id_usuario'];

        // Processa o upload da imagem
        if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] === UPLOAD_ERR_OK) {
            $imagem_nome = $_FILES['imagem']['name'];
            $imagem_tmp = $_FILES['imagem']['tmp_name'];
            $imagem_destino = "uploads/" . basename($imagem_nome);

            // Move a imagem para o diretório de uploads
            if (move_uploaded_file($imagem_tmp, $imagem_destino)) {
                // Insere a arte no banco de dados
                $sql = "INSERT INTO artes (usuario_id, titulo, conteudo, imagem, categoria_id, status) 
                        VALUES (?, ?, ?, ?, ?, 'pendente')";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("isssi", $usuario_id, $titulo, $conteudo, $imagem_destino, $categoria_id);

                if ($stmt->execute()) {
                    $mensagem = "Arte enviada com sucesso! Aguarde aprovação.";
                    $tipo = "sucesso";
                } else {
                    $mensagem = "Erro ao enviar a arte.";
                    $tipo = "erro";
                }

                $stmt->close();
            } else {
                $mensagem = "Erro ao fazer upload da imagem.";
                $tipo = "erro";
            }
        } else {
            $mensagem = "Erro no envio da imagem.";
            $tipo = "erro";
        }

        // Fecha a conexão
        $conn->close();
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resultado do Envio</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f4f4f4;
        }

        .mensagem-container {
            background-color: <?php echo $tipo === "sucesso" ? "#d4edda" : "#f8d7da"; ?>;
            color: <?php echo $tipo === "sucesso" ? "#155724" : "#721c24"; ?>;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2);
            text-align: center;
            max-width: 400px;
            width: 90%;
        }

        .mensagem-container h2 {
            margin-bottom: 10px;
        }

        .btn {
            display: inline-block;
            margin-top: 15px;
            padding: 10px 20px;
            background-color: <?php echo $tipo === "sucesso" ? "#28a745" : "#dc3545"; ?>;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            transition: 0.3s;
        }

        .btn:hover {
            background-color: <?php echo $tipo === "sucesso" ? "#218838" : "#c82333"; ?>;
        }
    </style>
</head>
<body>

<div class="mensagem-container">
    <h2><?php echo $mensagem; ?></h2>
    <a href="princip_pagin.php" class="btn">Voltar para o início</a>
</div>

</body>
</html>
