<?php
session_start(); // Inicia a sessão

// Configurações de conexão com o banco de dados
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "banco_exart";

// Cria a conexão
$conn = new mysqli($servername, $username, $password, $dbname);

// Verifica a conexão
if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

// Verifica se o formulário foi enviado via método POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Coleta e sanitiza os dados enviados pelo formulário
    $nome = trim($_POST['nome']);
    $email = trim($_POST['email']);
    $senha = password_hash($_POST['senha'], PASSWORD_BCRYPT); // Hash da senha
    $created = date('Y-m-d H:i:s');
    
    // Obtém a imagem selecionada
    $imagem_perfil = trim($_POST['imagem']);

    // Verifica se o email já está cadastrado
    $sql_check_email = "SELECT id FROM usuarios WHERE email = ?";
    $stmt_check_email = $conn->prepare($sql_check_email);
    $stmt_check_email->bind_param("s", $email);
    $stmt_check_email->execute();
    $stmt_check_email->store_result();

    if ($stmt_check_email->num_rows > 0) {
        echo "Este email já está cadastrado.";
        $stmt_check_email->close();
        $conn->close();
        exit();
    }
    $stmt_check_email->close();

    // SQL para inserir os dados na tabela usuarios
    $sql_insert = "INSERT INTO usuarios (nome, email, senha, created, imagem_perfil) VALUES (?, ?, ?, ?, ?)";
    $stmt_insert = $conn->prepare($sql_insert);
    $stmt_insert->bind_param("sssss", $nome, $email, $senha, $created, $imagem_perfil);

    // Executa a instrução SQL
    if ($stmt_insert->execute()) {
        // Recupera o ID do usuário cadastrado
        $last_id = $conn->insert_id;

        // Armazena os dados do usuário na sessão para login automático
        $_SESSION['id_usuario'] = $last_id;
        $_SESSION['user_nome'] = $nome;
        $_SESSION['user_email'] = $email;
        $_SESSION['user_imagem'] = $imagem_perfil;

        // Redireciona para a página de perfil
        header("Location: perfil.php");
        exit();
    } else {
        echo "Erro ao cadastrar: " . $stmt_insert->error;
    }

    $stmt_insert->close();
    $conn->close();
}
?>
