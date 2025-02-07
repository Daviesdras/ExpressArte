<?php
session_start(); // Inicia a sessão

// Configuração do banco de dados
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "banco_exart";

// Conexão com o banco de dados
$conn = new mysqli($servername, $username, $password, $dbname);

// Verifica a conexão
if ($conn->connect_error) {
    die("Erro na conexão: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['senha'];

    // Validação básica
    if (empty($email) || empty($password)) {
        echo "Preencha todos os campos.";
        exit();
    }

    // Escapando caracteres perigosos para prevenir SQL Injection
    $email = $conn->real_escape_string($email);


    
    $sql = "SELECT id, nome, senha, tipo_usuario FROM usuarios WHERE email = '$email'";
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
    
        // Verifica se a senha está correta
        if (password_verify($password, $row['senha'])) {
            // Salva as informações do usuário na sessão
            $_SESSION['id_usuario'] = $row['id'];
            $_SESSION['nome_usuario'] = $row['nome'];
            $_SESSION['admin'] = ($row['tipo_usuario'] === 'admin') ? 1 : 0; // Verifica se é admin ou usuário comum
    
            // Redireciona para a página inicial ou dashboard
            header("Location: princip_pagin.php");
            exit();
        } else {
            echo "Senha incorreta.";
        }
    } else {
        echo "Email não encontrado.";
    }
    
}

$conn->close();
?>
