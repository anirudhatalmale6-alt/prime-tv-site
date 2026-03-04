<?php
session_start();
require_once __DIR__ . '/../includes/functions.php';

$config = loadConfig();
$error = '';
$success = '';

// Login
if (!isset($_SESSION['admin_logged_in'])) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
        $user = $_POST['username'] ?? '';
        $pass = $_POST['password'] ?? '';
        if ($user === 'admin' && $pass === 'prime2026') {
            $_SESSION['admin_logged_in'] = true;
        } else {
            $error = 'Usuário ou senha incorretos.';
        }
    }

    if (!isset($_SESSION['admin_logged_in'])) {
        showLogin($error);
        exit;
    }
}

// Logout
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: index.php');
    exit;
}

// Save changes
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save'])) {
    $section = $_POST['section'] ?? '';

    switch ($section) {
        case 'site':
            $config['site']['whatsapp'] = preg_replace('/[^0-9]/', '', $_POST['whatsapp'] ?? '');
            $config['site']['whatsapp_message'] = $_POST['whatsapp_message'] ?? '';
            $config['site']['hero_headline'] = $_POST['hero_headline'] ?? '';
            $config['site']['hero_highlight'] = $_POST['hero_highlight'] ?? '';
            $config['site']['hero_subtitle'] = $_POST['hero_subtitle'] ?? '';
            $config['site']['hero_badge'] = $_POST['hero_badge'] ?? '';
            break;

        case 'stats':
            $config['stats']['channels'] = $_POST['channels'] ?? '';
            $config['stats']['movies'] = $_POST['movies'] ?? '';
            $config['stats']['series'] = $_POST['series'] ?? '';
            break;

        case 'plans':
            foreach ($config['plans'] as $i => &$plan) {
                if (isset($_POST['plan_name'][$i])) {
                    $plan['name'] = $_POST['plan_name'][$i];
                    $plan['price'] = $_POST['plan_price'][$i];
                    $parts = explode(',', $plan['price']);
                    $plan['price_int'] = $parts[0];
                    $plan['price_dec'] = isset($parts[1]) ? ',' . $parts[1] : '';
                    $plan['period'] = $_POST['plan_period'][$i];
                    $plan['economy'] = $_POST['plan_economy'][$i];
                    $plan['featured'] = isset($_POST['plan_featured']) && in_array($i, $_POST['plan_featured']);
                    $plan['whatsapp_text'] = "Olá! Quero o plano {$plan['name']} PRIME TV - R\${$plan['price']}";
                }
            }
            unset($plan);
            if (isset($_POST['plan_feature'])) {
                $config['plan_features'] = array_values(array_filter($_POST['plan_feature'], fn($f) => trim($f) !== ''));
            }
            break;

        case 'faq':
            $faqs = [];
            if (isset($_POST['faq_question'])) {
                foreach ($_POST['faq_question'] as $i => $q) {
                    if (trim($q) !== '' && trim($_POST['faq_answer'][$i] ?? '') !== '') {
                        $faqs[] = [
                            'question' => $q,
                            'answer' => $_POST['faq_answer'][$i]
                        ];
                    }
                }
            }
            $config['faq'] = $faqs;
            break;

        case 'comparison':
            if (isset($_POST['comp_others'])) {
                $config['comparison']['others'] = array_values(array_filter($_POST['comp_others'], fn($v) => trim($v) !== ''));
            }
            if (isset($_POST['comp_us'])) {
                $config['comparison']['us'] = array_values(array_filter($_POST['comp_us'], fn($v) => trim($v) !== ''));
            }
            break;

        case 'password':
            $current = $_POST['current_password'] ?? '';
            $new = $_POST['new_password'] ?? '';
            $confirm = $_POST['confirm_password'] ?? '';
            if ($current !== 'prime2026' && (!isset($_SESSION['custom_pass']) || $current !== $_SESSION['custom_pass'])) {
                $error = 'Senha atual incorreta.';
            } elseif (strlen($new) < 6) {
                $error = 'Nova senha deve ter pelo menos 6 caracteres.';
            } elseif ($new !== $confirm) {
                $error = 'As senhas não coincidem.';
            } else {
                $_SESSION['custom_pass'] = $new;
                $success = 'Senha atualizada com sucesso!';
            }
            break;
    }

    if (!$error && $section !== 'password') {
        saveConfig($config);
        $success = 'Alterações salvas com sucesso!';
        $config = loadConfig();
    }
}

// Handle logo upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
    $allowed = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
    if (in_array($_FILES['logo']['type'], $allowed)) {
        $dest = __DIR__ . '/../assets/img/logo.png';
        move_uploaded_file($_FILES['logo']['tmp_name'], $dest);
        $success = 'Logo atualizada com sucesso!';
    } else {
        $error = 'Formato de imagem não suportado. Use JPG, PNG, WebP ou GIF.';
    }
}

$tab = $_GET['tab'] ?? 'site';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin — PRIME TV</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: #0f1225; color: #e8e8f0; min-height: 100vh; }

        .sidebar {
            position: fixed; left: 0; top: 0; bottom: 0; width: 240px;
            background: #0a0d1f; border-right: 1px solid rgba(255,255,255,0.06);
            padding: 1.5rem 0; overflow-y: auto;
        }

        .sidebar-logo { text-align: center; padding: 0 1rem 1.5rem; border-bottom: 1px solid rgba(255,255,255,0.06); margin-bottom: 1rem; }
        .sidebar-logo img { height: 50px; }
        .sidebar-logo span { display: block; font-size: 0.7rem; color: #888; margin-top: 0.5rem; text-transform: uppercase; letter-spacing: 2px; }

        .nav-item {
            display: block; padding: 0.75rem 1.5rem; color: #888; text-decoration: none;
            font-size: 0.88rem; font-weight: 500; transition: all 0.2s; border-left: 3px solid transparent;
        }
        .nav-item:hover { color: #e8e8f0; background: rgba(255,255,255,0.03); }
        .nav-item.active { color: #e91e8c; background: rgba(233,30,140,0.05); border-left-color: #e91e8c; }

        .nav-item-logout { color: #ff4444 !important; margin-top: 1rem; border-top: 1px solid rgba(255,255,255,0.06); padding-top: 1rem; }

        .main { margin-left: 240px; padding: 2rem; }
        .main h1 { font-size: 1.5rem; margin-bottom: 0.5rem; }
        .main .subtitle { color: #888; font-size: 0.85rem; margin-bottom: 2rem; }

        .alert { padding: 0.8rem 1.2rem; border-radius: 8px; margin-bottom: 1.5rem; font-size: 0.88rem; font-weight: 500; }
        .alert-success { background: rgba(0,220,130,0.1); color: #00dc82; border: 1px solid rgba(0,220,130,0.2); }
        .alert-error { background: rgba(255,60,60,0.1); color: #ff4444; border: 1px solid rgba(255,60,60,0.2); }

        .card { background: #151839; border: 1px solid rgba(255,255,255,0.06); border-radius: 12px; padding: 1.5rem; margin-bottom: 1.5rem; }
        .card-title { font-size: 1rem; font-weight: 700; margin-bottom: 1rem; color: #e91e8c; }

        .form-group { margin-bottom: 1rem; }
        .form-group label { display: block; font-size: 0.8rem; font-weight: 600; color: #aaa; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 0.4rem; }
        .form-group input, .form-group textarea, .form-group select {
            width: 100%; padding: 0.7rem 1rem; background: #0d1025; border: 1px solid rgba(255,255,255,0.08);
            border-radius: 8px; color: #e8e8f0; font-family: inherit; font-size: 0.9rem;
            transition: border-color 0.2s;
        }
        .form-group input:focus, .form-group textarea:focus { outline: none; border-color: #e91e8c; }
        .form-group textarea { min-height: 80px; resize: vertical; }

        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
        @media (max-width: 768px) { .form-row { grid-template-columns: 1fr; } }

        .btn {
            display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.7rem 1.5rem;
            border: none; border-radius: 8px; font-family: inherit; font-size: 0.88rem;
            font-weight: 600; cursor: pointer; transition: all 0.2s;
        }
        .btn-primary { background: linear-gradient(135deg, #e91e8c, #ff6b35); color: white; }
        .btn-primary:hover { opacity: 0.9; transform: translateY(-1px); }
        .btn-secondary { background: rgba(255,255,255,0.06); color: #aaa; }
        .btn-secondary:hover { background: rgba(255,255,255,0.1); color: #e8e8f0; }
        .btn-danger { background: rgba(255,60,60,0.15); color: #ff4444; }
        .btn-danger:hover { background: rgba(255,60,60,0.25); }

        .btn-row { display: flex; gap: 0.75rem; margin-top: 1.5rem; flex-wrap: wrap; }

        .item-row { display: flex; gap: 0.75rem; align-items: start; margin-bottom: 0.75rem; }
        .item-row input, .item-row textarea { flex: 1; }
        .item-row .btn-remove { flex-shrink: 0; margin-top: 0.4rem; padding: 0.5rem 0.8rem; font-size: 0.8rem; }

        .checkbox-wrap { display: flex; align-items: center; gap: 0.5rem; cursor: pointer; }
        .checkbox-wrap input[type="checkbox"] { width: 18px; height: 18px; accent-color: #e91e8c; }

        .plan-card { background: #0d1025; border: 1px solid rgba(255,255,255,0.06); border-radius: 10px; padding: 1.25rem; margin-bottom: 1rem; }
        .plan-card-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; }
        .plan-card-num { font-size: 0.75rem; color: #e91e8c; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; }

        @media (max-width: 768px) {
            .sidebar { position: static; width: 100%; }
            .main { margin-left: 0; }
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="sidebar-logo">
            <img src="../assets/img/logo.png" alt="PRIME TV">
            <span>Painel Admin</span>
        </div>
        <a href="?tab=site" class="nav-item <?= $tab === 'site' ? 'active' : '' ?>">🏠 Configurações</a>
        <a href="?tab=plans" class="nav-item <?= $tab === 'plans' ? 'active' : '' ?>">💰 Planos</a>
        <a href="?tab=faq" class="nav-item <?= $tab === 'faq' ? 'active' : '' ?>">❓ Perguntas (FAQ)</a>
        <a href="?tab=compare" class="nav-item <?= $tab === 'compare' ? 'active' : '' ?>">⚡ Comparativo</a>
        <a href="?tab=logo" class="nav-item <?= $tab === 'logo' ? 'active' : '' ?>">🎨 Logo</a>
        <a href="?tab=password" class="nav-item <?= $tab === 'password' ? 'active' : '' ?>">🔒 Senha</a>
        <a href="?logout=1" class="nav-item nav-item-logout">↩ Sair</a>
    </div>

    <div class="main">
        <?php if ($success): ?><div class="alert alert-success"><?= e($success) ?></div><?php endif; ?>
        <?php if ($error): ?><div class="alert alert-error"><?= e($error) ?></div><?php endif; ?>

        <?php if ($tab === 'site'): ?>
            <h1>Configurações do Site</h1>
            <p class="subtitle">Altere os textos e informações do seu site.</p>

            <form method="POST">
                <input type="hidden" name="save" value="1">
                <input type="hidden" name="section" value="site">

                <div class="card">
                    <div class="card-title">WhatsApp</div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Número (somente números)</label>
                            <input type="text" name="whatsapp" value="<?= e($config['site']['whatsapp']) ?>" placeholder="5571920090133">
                        </div>
                        <div class="form-group">
                            <label>Mensagem automática</label>
                            <input type="text" name="whatsapp_message" value="<?= e($config['site']['whatsapp_message']) ?>">
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-title">Hero (Topo do site)</div>
                    <div class="form-group">
                        <label>Badge (texto pequeno)</label>
                        <input type="text" name="hero_badge" value="<?= e($config['site']['hero_badge']) ?>">
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Título principal</label>
                            <input type="text" name="hero_headline" value="<?= e($config['site']['hero_headline']) ?>">
                        </div>
                        <div class="form-group">
                            <label>Destaque (colorido)</label>
                            <input type="text" name="hero_highlight" value="<?= e($config['site']['hero_highlight']) ?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Subtítulo</label>
                        <input type="text" name="hero_subtitle" value="<?= e($config['site']['hero_subtitle']) ?>">
                    </div>
                </div>

                <div class="card">
                    <div class="card-title">Números / Estatísticas</div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Canais</label>
                            <input type="text" name="channels" value="<?= e($config['stats']['channels']) ?>">
                        </div>
                        <div class="form-group">
                            <label>Filmes</label>
                            <input type="text" name="movies" value="<?= e($config['stats']['movies']) ?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Séries</label>
                        <input type="text" name="series" value="<?= e($config['stats']['series']) ?>">
                    </div>
                </div>

                <div class="btn-row">
                    <button type="submit" class="btn btn-primary">Salvar Alterações</button>
                </div>
            </form>

        <?php elseif ($tab === 'plans'): ?>
            <h1>Planos</h1>
            <p class="subtitle">Gerencie os planos e preços do seu serviço.</p>

            <form method="POST">
                <input type="hidden" name="save" value="1">
                <input type="hidden" name="section" value="plans">

                <?php foreach ($config['plans'] as $i => $plan): ?>
                <div class="plan-card">
                    <div class="plan-card-header">
                        <span class="plan-card-num">Plano <?= $i + 1 ?></span>
                        <label class="checkbox-wrap">
                            <input type="checkbox" name="plan_featured[]" value="<?= $i ?>" <?= $plan['featured'] ? 'checked' : '' ?>>
                            Destacar
                        </label>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Nome</label>
                            <input type="text" name="plan_name[<?= $i ?>]" value="<?= e($plan['name']) ?>">
                        </div>
                        <div class="form-group">
                            <label>Preço (ex: 29,90)</label>
                            <input type="text" name="plan_price[<?= $i ?>]" value="<?= e($plan['price']) ?>">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Valor por mês (ex: R$ 28,17/mês)</label>
                            <input type="text" name="plan_period[<?= $i ?>]" value="<?= e($plan['period']) ?>">
                        </div>
                        <div class="form-group">
                            <label>Economia (ex: Economize R$ 5,20)</label>
                            <input type="text" name="plan_economy[<?= $i ?>]" value="<?= e($plan['economy']) ?>">
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>

                <div class="card">
                    <div class="card-title">Itens incluídos em todos os planos</div>
                    <?php foreach ($config['plan_features'] as $j => $feat): ?>
                    <div class="item-row">
                        <input type="text" name="plan_feature[]" value="<?= e($feat) ?>">
                    </div>
                    <?php endforeach; ?>
                    <div class="item-row">
                        <input type="text" name="plan_feature[]" value="" placeholder="Adicionar novo item...">
                    </div>
                </div>

                <div class="btn-row">
                    <button type="submit" class="btn btn-primary">Salvar Planos</button>
                </div>
            </form>

        <?php elseif ($tab === 'faq'): ?>
            <h1>Perguntas Frequentes (FAQ)</h1>
            <p class="subtitle">Gerencie as perguntas e respostas do seu site.</p>

            <form method="POST">
                <input type="hidden" name="save" value="1">
                <input type="hidden" name="section" value="faq">

                <?php foreach ($config['faq'] as $k => $faq): ?>
                <div class="card">
                    <div class="form-group">
                        <label>Pergunta <?= $k + 1 ?></label>
                        <input type="text" name="faq_question[]" value="<?= e($faq['question']) ?>">
                    </div>
                    <div class="form-group">
                        <label>Resposta</label>
                        <textarea name="faq_answer[]"><?= e($faq['answer']) ?></textarea>
                    </div>
                </div>
                <?php endforeach; ?>

                <div class="card">
                    <div class="card-title">Adicionar Nova Pergunta</div>
                    <div class="form-group">
                        <label>Pergunta</label>
                        <input type="text" name="faq_question[]" value="" placeholder="Nova pergunta...">
                    </div>
                    <div class="form-group">
                        <label>Resposta</label>
                        <textarea name="faq_answer[]" placeholder="Resposta..."></textarea>
                    </div>
                </div>

                <div class="btn-row">
                    <button type="submit" class="btn btn-primary">Salvar FAQ</button>
                </div>
            </form>

        <?php elseif ($tab === 'compare'): ?>
            <h1>Comparativo</h1>
            <p class="subtitle">Edite os pontos de comparação entre sua marca e os concorrentes.</p>

            <form method="POST">
                <input type="hidden" name="save" value="1">
                <input type="hidden" name="section" value="comparison">

                <div class="card">
                    <div class="card-title" style="color: #ff4444;">❌ Outros Serviços</div>
                    <?php foreach ($config['comparison']['others'] as $item): ?>
                    <div class="item-row">
                        <input type="text" name="comp_others[]" value="<?= e($item) ?>">
                    </div>
                    <?php endforeach; ?>
                    <div class="item-row">
                        <input type="text" name="comp_others[]" value="" placeholder="Adicionar item...">
                    </div>
                </div>

                <div class="card">
                    <div class="card-title" style="color: #00dc82;">✓ PRIME TV</div>
                    <?php foreach ($config['comparison']['us'] as $item): ?>
                    <div class="item-row">
                        <input type="text" name="comp_us[]" value="<?= e($item) ?>">
                    </div>
                    <?php endforeach; ?>
                    <div class="item-row">
                        <input type="text" name="comp_us[]" value="" placeholder="Adicionar item...">
                    </div>
                </div>

                <div class="btn-row">
                    <button type="submit" class="btn btn-primary">Salvar Comparativo</button>
                </div>
            </form>

        <?php elseif ($tab === 'logo'): ?>
            <h1>Logo</h1>
            <p class="subtitle">Atualize a logo do seu site.</p>

            <div class="card">
                <div class="card-title">Logo Atual</div>
                <img src="../assets/img/logo.png" style="max-height: 80px; margin-bottom: 1rem; display: block;" alt="Logo">

                <form method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label>Enviar nova logo</label>
                        <input type="file" name="logo" accept="image/*" style="padding: 0.5rem;">
                    </div>
                    <button type="submit" class="btn btn-primary">Atualizar Logo</button>
                </form>
            </div>

        <?php elseif ($tab === 'password'): ?>
            <h1>Alterar Senha</h1>
            <p class="subtitle">Altere a senha de acesso ao painel.</p>

            <form method="POST">
                <input type="hidden" name="save" value="1">
                <input type="hidden" name="section" value="password">

                <div class="card">
                    <div class="form-group">
                        <label>Senha Atual</label>
                        <input type="password" name="current_password" required>
                    </div>
                    <div class="form-group">
                        <label>Nova Senha</label>
                        <input type="password" name="new_password" required>
                    </div>
                    <div class="form-group">
                        <label>Confirmar Nova Senha</label>
                        <input type="password" name="confirm_password" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Alterar Senha</button>
                </div>
            </form>

        <?php endif; ?>
    </div>
</body>
</html>

<?php
function showLogin($error) {
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — PRIME TV Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: #06091a; color: #e8e8f0; min-height: 100vh; display: flex; align-items: center; justify-content: center; }
        .login-box { background: #0f1225; border: 1px solid rgba(255,255,255,0.06); border-radius: 16px; padding: 2.5rem; width: 100%; max-width: 380px; text-align: center; }
        .login-box img { height: 60px; margin-bottom: 1.5rem; }
        .login-box h2 { font-size: 1.2rem; margin-bottom: 0.3rem; }
        .login-box p { color: #888; font-size: 0.8rem; margin-bottom: 1.5rem; }
        .login-box input { width: 100%; padding: 0.75rem 1rem; background: #0a0d1f; border: 1px solid rgba(255,255,255,0.08); border-radius: 8px; color: #e8e8f0; font-family: inherit; font-size: 0.9rem; margin-bottom: 0.75rem; }
        .login-box input:focus { outline: none; border-color: #e91e8c; }
        .login-box button { width: 100%; padding: 0.8rem; background: linear-gradient(135deg, #e91e8c, #ff6b35); color: white; border: none; border-radius: 8px; font-family: inherit; font-size: 0.95rem; font-weight: 700; cursor: pointer; transition: opacity 0.2s; }
        .login-box button:hover { opacity: 0.9; }
        .error { background: rgba(255,60,60,0.1); color: #ff4444; padding: 0.6rem; border-radius: 6px; font-size: 0.8rem; margin-bottom: 1rem; }
    </style>
</head>
<body>
    <div class="login-box">
        <img src="../assets/img/logo.png" alt="PRIME TV">
        <h2>Painel Admin</h2>
        <p>Faça login para gerenciar seu site</p>
        <?php if ($error): ?><div class="error"><?= htmlspecialchars($error) ?></div><?php endif; ?>
        <form method="POST">
            <input type="hidden" name="login" value="1">
            <input type="text" name="username" placeholder="Usuário" required autofocus>
            <input type="password" name="password" placeholder="Senha" required>
            <button type="submit">Entrar</button>
        </form>
    </div>
</body>
</html>
<?php } ?>
