<?php
// Conexão com o banco de dados
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "banco_exart";

$conn = new mysqli($servername, $username, $password, $dbname);

// Verifica a conexão
if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

// Atualizar status do conteúdo (poema ou arte)
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id = $_POST['id'];
    $status = $_POST['status'];
    $tipo = $_POST['tipo']; // 'postagem' ou 'arte'

    // Determina a tabela correta com base no tipo
    $tabela = ($tipo === 'postagem') ? 'postagens' : 'artes';

    $sql = "UPDATE $tabela SET status = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("si", $status, $id);
        $stmt->execute();
        $stmt->close();
    } else {
        die("Erro ao preparar a consulta: " . $conn->error);
    }
}

// Recuperar poemas e artes pendentes
$sql = "
SELECT 
    'postagem' AS tipo,
    id, 
    titulo, 
    poema AS conteudo, 
    imagem, 
    status 
FROM 
    postagens 
WHERE 
    status = 'pendente'

UNION ALL

SELECT 
    'arte' AS tipo,
    id, 
    titulo, 
    conteudo, 
    imagem, 
    status 
FROM 
    artes 
WHERE 
    status = 'pendente'
ORDER BY 
    id DESC";

$result = $conn->query($sql);

if (!$result) {
    die("Erro ao executar a consulta: " . $conn->error);
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Gerenciar Conteúdo</title>
    <link rel="stylesheet" href="poema.css"> <!-- Link para o arquivo CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <style>
        /* Estilos da tabela de aprovação */
        body {
            background-color: #f4f4f4;
            font-family: 'Arial', sans-serif;
            color: #333;
            margin: 0;
            padding: 0;
        }

        h1 {
            text-align: center;
            margin-top: 20px;
            color: #333;
            font-size: 2em;
        }

        table {
            width: 80%;
            margin: 20px auto;
            border-collapse: collapse;
            background-color: #fff;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        th {
            background-color: #d3b19a;
            color: white;
            padding: 12px 15px;
            text-align: left;
            font-size: 1.1em;
        }

        td {
            padding: 12px 15px;
            text-align: left;
            vertical-align: top;
            border-bottom: 1px solid #ddd;
        }

        tr:hover {
            background-color: #f1f1f1;
        }

        img {
            max-width: 100px;
            border-radius: 5px;
        }

        form {
            display: flex;
            align-items: center;
        }

        select {
            padding: 8px;
            margin-right: 10px;
            font-size: 1em;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        button {
            padding: 8px 12px;
            background-color: #70b3b1;
            color: white;
            font-size: 1em;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover {
            background-color: #5e9a92;
        }

        td[colspan="5"] {
            text-align: center;
            font-size: 1.1em;
            color: #888;
        }
    </style>
</head>
<body>

<!-- Cabeçalho -->
<h1>Conteúdos Pendentes</h1>
<i class="fa-solid fa-arrow-left" onclick="history.back()"></i>


<!-- Tabela de conteúdos pendentes -->
<table>
    <thead>
        <tr>
            <th>Tipo</th>
            <th>Título</th>
            <th>Conteúdo</th>
            <th>Imagem</th>
            <th>Ação</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo ($row['tipo'] === 'postagem') ? 'Poema' : 'Arte'; ?></td>
                    <td><?php echo htmlspecialchars($row['titulo']); ?></td>
                    <td><?php echo nl2br(htmlspecialchars($row['conteudo'])); ?></td>
                    <td>
                        <?php if (!empty($row['imagem'])): ?>
                            <img src="<?php echo htmlspecialchars($row['imagem']); ?>" alt="Imagem do conteúdo">
                        <?php else: ?>
                            Sem imagem
                        <?php endif; ?>
                    </td>
                    <td>
                        <form method="POST">
                            <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                            <input type="hidden" name="tipo" value="<?php echo $row['tipo']; ?>">
                            <select name="status">
                                <option value="aprovado">Aprovar</option>
                                <option value="rejeitado">Rejeitar</option>
                            </select>
                            <button type="submit">Atualizar</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="5">Nenhum conteúdo pendente.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

</body>
</html>