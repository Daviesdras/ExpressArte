<?php
session_start();
require_once 'conexao.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['id_usuario'])) {
    $postagem_id = $_POST['postagem_id'];
    $usuario_id = $_SESSION['id_usuario'];

    // Verifica se o usuário já curtiu
    $sql_verificar = "SELECT * FROM curtidas WHERE postagem_id = ? AND usuario_id = ?";
    $stmt_verificar = $conn->prepare($sql_verificar);
    $stmt_verificar->bind_param("ii", $postagem_id, $usuario_id);
    $stmt_verificar->execute();
    $result_verificar = $stmt_verificar->get_result();

    if ($result_verificar->num_rows > 0) {
        // Remove curtida
        $sql_remover = "DELETE FROM curtidas WHERE postagem_id = ? AND usuario_id = ?";
        $stmt_remover = $conn->prepare($sql_remover);
        $stmt_remover->bind_param("ii", $postagem_id, $usuario_id);
        $stmt_remover->execute();
        echo json_encode(['action' => 'removed']);
    } else {
        // Adiciona curtida
        $sql_inserir = "INSERT INTO curtidas (postagem_id, usuario_id) VALUES (?, ?)";
        $stmt_inserir = $conn->prepare($sql_inserir);
        $stmt_inserir->bind_param("ii", $postagem_id, $usuario_id);
        $stmt_inserir->execute();
        echo json_encode(['action' => 'added']);
    }
} else {
    echo json_encode(['error' => 'Ação não permitida.']);
}
$conn->close();
?>
