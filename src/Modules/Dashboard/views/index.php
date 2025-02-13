<?php

// Definições de cores e fontes (valores padrão)
$bg_color = '#f8f9fa';
$font_color = '#212529';
$font_family = 'Arial, sans-serif';

$modulos = [
    ['nome' => 'Dashboard', 'link' => '/dashboard'],
    ['nome' => 'Vendas', 'link' => '/vendas'],
    ['nome' => 'Produtos', 'link' => '/produtos'],
    ['nome' => 'Clientes', 'link' => '/clientes'],
    ['nome' => 'Relatórios', 'link' => '/relatorios'],
];
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: <?php echo $bg_color; ?>;
            color: <?php echo $font_color; ?>;
            font-family: <?php echo $font_family; ?>;
        }
        .sidebar {
            width: 250px;
            background-color: #343a40;
            color: white;
            height: 100vh;
            position: fixed;
            padding-top: 20px;
        }
        .sidebar a {
            color: white;
            text-decoration: none;
            display: block;
            padding: 10px 20px;
        }
        .sidebar a:hover {
            background-color: #495057;
        }
        .content {
            margin-left: 260px;
            padding: 20px;
        }
    </style>
</head>
<body>
    <div class="d-flex">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="text-center mb-4">
                <img src="/public/assets/images/150.png" alt="Logo" class="img-fluid" style="max-width: 150px;">
            </div>
            <?php foreach ($modulos as $modulo): ?>
                <a href="<?php echo $modulo['link']; ?>"><?php echo $modulo['nome']; ?></a>
            <?php endforeach; ?>
            <hr>
            <a href="#" data-bs-toggle="modal" data-bs-target="#configModal">Configurações</a>
        </div>
        
        <!-- Conteúdo principal -->
        <div class="content">
            <h2>Dashboard - Empresa Nome</h2>
            <div class="row">
                <!-- Espaço para os cards de BI -->
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Projeção 1</h5>
                            <p class="card-text">Dados e gráficos aqui...</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Projeção 2</h5>
                            <p class="card-text">Dados e gráficos aqui...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Configuração -->
    <div class="modal fade" id="configModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Configurações</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form action="salvar_configuracoes.php" method="post">
                        <label for="bg_color">Cor de fundo:</label>
                        <input type="color" id="bg_color" name="bg_color" value="<?php echo $bg_color; ?>" class="form-control">
                        
                        <label for="font_color">Cor da fonte:</label>
                        <input type="color" id="font_color" name="font_color" value="<?php echo $font_color; ?>" class="form-control">
                        
                        <label for="font_family">Fonte:</label>
                        <select id="font_family" name="font_family" class="form-control">
                            <option value="Arial, sans-serif" <?php echo ($font_family == 'Arial, sans-serif') ? 'selected' : ''; ?>>Arial</option>
                            <option value="Verdana, sans-serif" <?php echo ($font_family == 'Verdana, sans-serif') ? 'selected' : ''; ?>>Verdana</option>
                            <option value="Tahoma, sans-serif" <?php echo ($font_family == 'Tahoma, sans-serif') ? 'selected' : ''; ?>>Tahoma</option>
                        </select>
                        
                        <button type="submit" class="btn btn-primary mt-3">Salvar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
