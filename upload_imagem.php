<?php
session_start(); // Inicia a sessão

// Verifica se o usuário está logado
if (!isset($_SESSION['id_usuario'])) {
    // Se o usuário não estiver logado, redireciona para a página de login
    header("Location: login.php");
    exit();
}

// Configurações de conexão com o banco de dados
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "banco_exart";

// Criando a conexão
$conn = new mysqli($servername, $username, $password, $dbname);

// Verifica a conexão
if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

// Verifica se o formulário foi enviado e se o arquivo foi enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['imagem_perfil'])) {
    // Diretório para salvar as imagens
    $diretorio = 'uploads/';
    
    // Verifica se o diretório existe, caso contrário, cria-o
    if (!is_dir($diretorio)) {
        mkdir($diretorio, 0755, true);
    }
    
    // Definindo o nome do arquivo
    $nome_arquivo = $diretorio . basename($_FILES['imagem_perfil']['name']);
    
    // Verifica se o arquivo é uma imagem
    $tipo_arquivo = strtolower(pathinfo($nome_arquivo, PATHINFO_EXTENSION));
    $verifica = getimagesize($_FILES['imagem_perfil']['tmp_name']);
    if ($verifica !== false) {
        // Verifica se o upload foi bem-sucedido
        if (move_uploaded_file($_FILES['imagem_perfil']['tmp_name'], $nome_arquivo)) {
            // Atualiza o caminho da imagem na tabela de usuários
            $usuario_id = $_SESSION['id_usuario'];
            $sql = "UPDATE usuarios SET imagem_perfil = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("si", $nome_arquivo, $usuario_id);
            if ($stmt->execute()) {
                echo "Imagem de perfil atualizada com sucesso!";
            } else {
                echo "Erro ao atualizar a imagem: " . $conn->error;
            }
        } else {
            echo "Erro ao fazer upload da imagem.";
        }
    } else {
        echo "Arquivo enviado não é uma imagem válida.";
    }

    // Após salvar a imagem, redirecionar para a página princip_poema
    header("Location: princip_pagin.php");
    exit();
}

// Fechar a conexão
$conn->close();
?>
