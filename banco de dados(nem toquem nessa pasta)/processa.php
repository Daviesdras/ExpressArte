<?php
// Inclui o arquivo de conexão com o banco de dados
include 'conexao.php';

// Verifica se o formulário foi enviado via método POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Coleta e sanitiza os dados enviados pelo formulário
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    // Criptografa a senha para armazenamento seguro
    $senha = password_hash($_POST['senha'], PASSWORD_BCRYPT);
    
    // Captura a data e hora atual para os campos created e modified
    $created = date('Y-m-d H:i:s');

    try {
        // SQL para inserir os dados na tabela usuarios
        // O comando SQL insere o nome, email, senha, data de criação e modificação
        $sql = "INSERT INTO usuarios (nome, email, senha, created) VALUES (:nome, :email, :senha, :created)";
        
        // Prepara a instrução SQL para execução
        // O método prepare é usado para evitar SQL Injection
        $stmt = $pdo->prepare($sql);

        // Executa a instrução SQL com os dados fornecidos
        $stmt->execute([
            ':nome' => $nome,
            ':email' => $email,
            ':senha' => $senha,
            ':created' => $created,
           
        ]);

        // Mensagem de sucesso após a inserção dos dados
        echo 'Cadastro realizado com sucesso!';
    } catch (PDOException $e) {
        // Captura e exibe qualquer erro ocorrido durante a inserção
        echo 'Erro: ' . $e->getMessage();
    }
}
?>
