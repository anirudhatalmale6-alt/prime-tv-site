<?php
require_once __DIR__ . '/includes/functions.php';
$config = loadConfig();
$site = $config['site'];
$stats = $config['stats'];
$plans = $config['plans'];
$faq = $config['faq'];
$comp = $config['comparison'];
$planFeatures = $config['plan_features'];
$waUrl = waLink($site['whatsapp'], $site['whatsapp_message']);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($site['name']) ?> — <?= e($site['tagline']) ?></title>
    <meta name="description" content="<?= e($site['name']) ?>: +<?= e($stats['channels']) ?> canais ao vivo, +<?= e($stats['movies']) ?> filmes e +<?= e($stats['series']) ?> séries em HD e 4K. Sem travamentos. Teste grátis agora!">
    <meta property="og:title" content="<?= e($site['name']) ?> — <?= e($site['tagline']) ?>">
    <meta property="og:description" content="+<?= e($stats['channels']) ?> canais, +<?= e($stats['movies']) ?> filmes, +<?= e($stats['series']) ?> séries. HD e 4K sem travar.">
    <meta property="og:type" content="website">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }
        :root {
            --bg-deep: #06091a; --bg-main: #0a0e27; --bg-card: #101538; --bg-card-hover: #151b45;
            --pink: #e91e8c; --pink-glow: #ff2da5; --orange: #ff6b35; --gold: #ffd700; --gold-dim: #f5c518;
            --text: #e8e8f0; --text-dim: #8888aa; --text-bright: #ffffff;
            --gradient-main: linear-gradient(135deg, var(--pink), var(--orange));
            --gradient-card: linear-gradient(160deg, rgba(233,30,140,0.08), rgba(255,107,53,0.05));
            --font-display: 'Bebas Neue', sans-serif; --font-body: 'Plus Jakarta Sans', sans-serif;
            --radius: 16px; --radius-sm: 10px;
        }
        html { scroll-behavior: smooth; }
        body { font-family: var(--font-body); background: var(--bg-deep); color: var(--text); line-height: 1.6; overflow-x: hidden; -webkit-font-smoothing: antialiased; }
        body::before { content: ''; position: fixed; inset: 0; background: repeating-linear-gradient(0deg, transparent, transparent 2px, rgba(233,30,140,0.015) 2px, rgba(233,30,140,0.015) 4px); pointer-events: none; z-index: 9999; }

        .nav { position: fixed; top: 0; left: 0; right: 0; z-index: 1000; padding: 1rem 2rem; display: flex; align-items: center; justify-content: space-between; background: rgba(6,9,26,0.85); backdrop-filter: blur(20px); border-bottom: 1px solid rgba(233,30,140,0.1); transition: all 0.3s ease; }
        .nav.scrolled { padding: 0.6rem 2rem; background: rgba(6,9,26,0.95); }
        .nav-logo { height: 50px; transition: height 0.3s ease; }
        .nav.scrolled .nav-logo { height: 40px; }
        .nav-links { display: flex; gap: 2rem; list-style: none; }
        .nav-links a { color: var(--text-dim); text-decoration: none; font-size: 0.85rem; font-weight: 600; text-transform: uppercase; letter-spacing: 1.5px; transition: color 0.3s ease; position: relative; }
        .nav-links a::after { content: ''; position: absolute; bottom: -4px; left: 0; width: 0; height: 2px; background: var(--gradient-main); transition: width 0.3s ease; }
        .nav-links a:hover { color: var(--text-bright); }
        .nav-links a:hover::after { width: 100%; }
        .nav-cta { background: var(--gradient-main); color: white !important; padding: 0.6rem 1.5rem; border-radius: 50px; font-weight: 700 !important; letter-spacing: 1px !important; transition: transform 0.3s ease, box-shadow 0.3s ease !important; }
        .nav-cta:hover { transform: scale(1.05) !important; box-shadow: 0 0 25px rgba(233,30,140,0.4) !important; }
        .nav-cta::after { display: none !important; }
        .hamburger { display: none; flex-direction: column; gap: 5px; cursor: pointer; padding: 5px; }
        .hamburger span { width: 28px; height: 2.5px; background: var(--text); border-radius: 2px; transition: all 0.3s ease; }
        .hamburger.active span:nth-child(1) { transform: rotate(45deg) translate(5px, 5px); }
        .hamburger.active span:nth-child(2) { opacity: 0; }
        .hamburger.active span:nth-child(3) { transform: rotate(-45deg) translate(5px, -5px); }

        .hero { min-height: 100vh; display: flex; align-items: center; justify-content: center; text-align: center; padding: 8rem 2rem 4rem; position: relative; overflow: hidden; }
        .hero-bg { position: absolute; inset: 0; background: radial-gradient(ellipse 80% 60% at 50% 0%, rgba(233,30,140,0.12) 0%, transparent 60%), radial-gradient(ellipse 60% 40% at 20% 80%, rgba(255,107,53,0.08) 0%, transparent 50%), radial-gradient(ellipse 50% 50% at 80% 60%, rgba(233,30,140,0.06) 0%, transparent 50%); z-index: 0; }
        .hero-bg::before { content: ''; position: absolute; width: 400px; height: 400px; border-radius: 50%; background: radial-gradient(circle, rgba(233,30,140,0.15), transparent 70%); top: 10%; right: -5%; animation: float-orb 8s ease-in-out infinite; }
        .hero-bg::after { content: ''; position: absolute; width: 300px; height: 300px; border-radius: 50%; background: radial-gradient(circle, rgba(255,107,53,0.12), transparent 70%); bottom: 10%; left: -5%; animation: float-orb 10s ease-in-out infinite reverse; }
        @keyframes float-orb { 0%, 100% { transform: translate(0, 0) scale(1); } 33% { transform: translate(30px, -20px) scale(1.05); } 66% { transform: translate(-20px, 15px) scale(0.95); } }
        .hero-content { position: relative; z-index: 1; max-width: 800px; }
        .hero-badge { display: inline-flex; align-items: center; gap: 0.5rem; background: rgba(233,30,140,0.1); border: 1px solid rgba(233,30,140,0.25); padding: 0.5rem 1.2rem; border-radius: 50px; font-size: 0.8rem; font-weight: 600; color: var(--pink-glow); text-transform: uppercase; letter-spacing: 2px; margin-bottom: 2rem; animation: fadeInDown 0.8s ease; }
        .hero-badge .pulse { width: 8px; height: 8px; background: var(--pink); border-radius: 50%; animation: pulse 2s ease-in-out infinite; }
        @keyframes pulse { 0%, 100% { box-shadow: 0 0 0 0 rgba(233,30,140,0.4); } 50% { box-shadow: 0 0 0 8px rgba(233,30,140,0); } }
        .hero h1 { font-family: var(--font-display); font-size: clamp(3rem, 8vw, 5.5rem); line-height: 0.95; color: var(--text-bright); margin-bottom: 0.5rem; animation: fadeInUp 0.8s ease 0.2s both; }
        .hero h1 .gradient-text { background: var(--gradient-main); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; }
        .hero-subtitle { font-family: var(--font-display); font-size: clamp(1.4rem, 3vw, 2rem); color: var(--gold); letter-spacing: 4px; margin-bottom: 2rem; animation: fadeInUp 0.8s ease 0.35s both; }
        .hero-features { display: flex; flex-wrap: wrap; justify-content: center; gap: 1rem; margin-bottom: 2.5rem; animation: fadeInUp 0.8s ease 0.5s both; }
        .hero-feature { display: flex; align-items: center; gap: 0.5rem; background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.06); padding: 0.6rem 1.2rem; border-radius: 50px; font-size: 0.88rem; font-weight: 500; }
        .hero-feature .icon { font-size: 1.1rem; }
        .hero-stats { display: flex; justify-content: center; gap: 3rem; margin-bottom: 2.5rem; animation: fadeInUp 0.8s ease 0.65s both; }
        .hero-stat { text-align: center; }
        .hero-stat-number { font-family: var(--font-display); font-size: clamp(2rem, 4vw, 3rem); background: var(--gradient-main); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; line-height: 1; }
        .hero-stat-label { font-size: 0.75rem; color: var(--text-dim); text-transform: uppercase; letter-spacing: 1.5px; font-weight: 600; }
        .hero-cta { display: inline-flex; align-items: center; gap: 0.75rem; background: var(--gradient-main); color: white; padding: 1rem 2.5rem; border-radius: 50px; font-size: 1.1rem; font-weight: 700; text-decoration: none; text-transform: uppercase; letter-spacing: 1.5px; transition: all 0.3s ease; box-shadow: 0 4px 25px rgba(233,30,140,0.3); animation: fadeInUp 0.8s ease 0.8s both; }
        .hero-cta:hover { transform: translateY(-3px) scale(1.03); box-shadow: 0 8px 40px rgba(233,30,140,0.45); }
        .hero-cta svg { width: 22px; height: 22px; }

        section { padding: 5rem 2rem; position: relative; }
        .section-container { max-width: 1100px; margin: 0 auto; }
        .section-header { text-align: center; margin-bottom: 3.5rem; }
        .section-tag { display: inline-block; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 3px; color: var(--pink); margin-bottom: 0.75rem; }
        .section-title { font-family: var(--font-display); font-size: clamp(2rem, 5vw, 3.2rem); color: var(--text-bright); line-height: 1; }

        .compare { background: var(--bg-main); }
        .compare-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; max-width: 900px; margin: 0 auto; }
        .compare-col { padding: 2.5rem; border-radius: var(--radius); position: relative; }
        .compare-col.others { background: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.05); }
        .compare-col.us { background: var(--gradient-card); border: 1px solid rgba(233,30,140,0.2); box-shadow: 0 0 60px rgba(233,30,140,0.06); }
        .compare-col.us::before { content: ''; position: absolute; inset: -1px; border-radius: var(--radius); background: var(--gradient-main); opacity: 0.1; z-index: 0; }
        .compare-col-title { font-family: var(--font-display); font-size: 1.6rem; margin-bottom: 1.5rem; letter-spacing: 2px; position: relative; z-index: 1; }
        .compare-col.others .compare-col-title { color: var(--text-dim); }
        .compare-col.us .compare-col-title { background: var(--gradient-main); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; }
        .compare-item { display: flex; align-items: flex-start; gap: 0.75rem; padding: 0.75rem 0; border-bottom: 1px solid rgba(255,255,255,0.04); position: relative; z-index: 1; }
        .compare-item:last-child { border-bottom: none; }
        .compare-icon { flex-shrink: 0; width: 24px; height: 24px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 0.75rem; font-weight: 700; margin-top: 2px; }
        .compare-col.others .compare-icon { background: rgba(255,60,60,0.15); color: #ff4444; }
        .compare-col.us .compare-icon { background: rgba(0,220,130,0.15); color: #00dc82; }
        .compare-item-text { font-size: 0.92rem; line-height: 1.5; }
        .compare-col.others .compare-item-text { color: var(--text-dim); }

        .pricing { background: var(--bg-deep); }
        .pricing-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 1.25rem; }
        .price-card { background: var(--bg-card); border: 1px solid rgba(255,255,255,0.05); border-radius: var(--radius); padding: 2rem 1.5rem; text-align: center; position: relative; transition: all 0.4s cubic-bezier(0.22, 1, 0.36, 1); overflow: hidden; }
        .price-card::before { content: ''; position: absolute; top: 0; left: 0; right: 0; height: 3px; background: var(--gradient-main); opacity: 0; transition: opacity 0.3s ease; }
        .price-card:hover { transform: translateY(-8px); border-color: rgba(233,30,140,0.2); box-shadow: 0 20px 60px rgba(233,30,140,0.1); }
        .price-card:hover::before { opacity: 1; }
        .price-card.featured { border-color: rgba(233,30,140,0.3); background: linear-gradient(160deg, rgba(233,30,140,0.08) 0%, var(--bg-card) 40%); }
        .price-card.featured::before { opacity: 1; }
        .price-badge { position: absolute; top: 12px; right: -28px; background: var(--gradient-main); color: white; font-size: 0.65rem; font-weight: 700; padding: 0.3rem 2rem; text-transform: uppercase; letter-spacing: 1px; transform: rotate(45deg); }
        .price-duration { font-family: var(--font-display); font-size: 1.3rem; letter-spacing: 2px; color: var(--text-dim); margin-bottom: 1.5rem; }
        .price-card.featured .price-duration { color: var(--pink-glow); }
        .price-amount { margin-bottom: 0.25rem; }
        .price-currency { font-size: 1rem; color: var(--text-dim); vertical-align: super; }
        .price-value { font-family: var(--font-display); font-size: 3.2rem; color: var(--text-bright); line-height: 1; }
        .price-card.featured .price-value { background: var(--gradient-main); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; }
        .price-cents { font-size: 1.2rem; color: var(--text-dim); }
        .price-period { font-size: 0.8rem; color: var(--text-dim); margin-bottom: 1.5rem; }
        .price-features { list-style: none; text-align: left; margin-bottom: 2rem; }
        .price-features li { display: flex; align-items: center; gap: 0.6rem; padding: 0.5rem 0; font-size: 0.85rem; color: var(--text); border-bottom: 1px solid rgba(255,255,255,0.03); }
        .price-features li:last-child { border-bottom: none; }
        .price-features .check { color: #00dc82; font-weight: 700; flex-shrink: 0; }
        .price-btn { display: block; width: 100%; padding: 0.9rem; border: 2px solid rgba(233,30,140,0.3); background: transparent; color: var(--pink-glow); border-radius: 50px; font-family: var(--font-body); font-size: 0.9rem; font-weight: 700; text-transform: uppercase; letter-spacing: 1.5px; cursor: pointer; transition: all 0.3s ease; text-decoration: none; text-align: center; }
        .price-btn:hover { background: var(--gradient-main); border-color: transparent; color: white; box-shadow: 0 4px 20px rgba(233,30,140,0.3); }
        .price-card.featured .price-btn { background: var(--gradient-main); border-color: transparent; color: white; }
        .price-card.featured .price-btn:hover { box-shadow: 0 6px 30px rgba(233,30,140,0.4); transform: scale(1.03); }
        .price-economy { display: inline-block; margin-top: 0.75rem; background: rgba(0,220,130,0.1); color: #00dc82; font-size: 0.72rem; font-weight: 700; padding: 0.3rem 0.8rem; border-radius: 20px; letter-spacing: 0.5px; }

        .faq { background: var(--bg-main); }
        .faq-list { max-width: 750px; margin: 0 auto; display: flex; flex-direction: column; gap: 0.75rem; }
        .faq-item { background: var(--bg-card); border: 1px solid rgba(255,255,255,0.04); border-radius: var(--radius-sm); overflow: hidden; transition: border-color 0.3s ease; }
        .faq-item.active { border-color: rgba(233,30,140,0.15); }
        .faq-question { display: flex; align-items: center; justify-content: space-between; padding: 1.25rem 1.5rem; cursor: pointer; font-weight: 600; font-size: 0.95rem; color: var(--text); transition: color 0.3s ease; user-select: none; }
        .faq-item.active .faq-question { color: var(--pink-glow); }
        .faq-question:hover { color: var(--text-bright); }
        .faq-toggle { width: 28px; height: 28px; border-radius: 50%; background: rgba(233,30,140,0.1); display: flex; align-items: center; justify-content: center; flex-shrink: 0; transition: all 0.3s ease; }
        .faq-toggle svg { width: 14px; height: 14px; stroke: var(--pink); transition: transform 0.3s ease; }
        .faq-item.active .faq-toggle { background: var(--gradient-main); }
        .faq-item.active .faq-toggle svg { transform: rotate(45deg); stroke: white; }
        .faq-answer { max-height: 0; overflow: hidden; transition: max-height 0.4s cubic-bezier(0.22, 1, 0.36, 1); }
        .faq-answer-inner { padding: 0 1.5rem 1.25rem; font-size: 0.9rem; color: var(--text-dim); line-height: 1.7; }

        .footer { background: var(--bg-deep); border-top: 1px solid rgba(255,255,255,0.04); padding: 3rem 2rem 2rem; text-align: center; }
        .footer-logo { height: 60px; margin-bottom: 1rem; }
        .footer-text { color: var(--text-dim); font-size: 0.8rem; margin-bottom: 0.5rem; }
        .footer-links { display: flex; justify-content: center; gap: 2rem; margin-bottom: 1.5rem; }
        .footer-links a { color: var(--text-dim); text-decoration: none; font-size: 0.8rem; transition: color 0.3s ease; }
        .footer-links a:hover { color: var(--pink); }
        .footer-copy { color: rgba(136,136,170,0.5); font-size: 0.7rem; }

        .whatsapp-float { position: fixed; bottom: 24px; right: 24px; z-index: 9998; width: 60px; height: 60px; border-radius: 50%; background: #25d366; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 20px rgba(37,211,102,0.4); transition: all 0.3s ease; text-decoration: none; animation: whatsapp-enter 0.5s ease 1.5s both; }
        .whatsapp-float:hover { transform: scale(1.1); box-shadow: 0 6px 30px rgba(37,211,102,0.5); }
        .whatsapp-float svg { width: 32px; height: 32px; fill: white; }
        .whatsapp-float .tooltip { position: absolute; right: 70px; background: white; color: #333; padding: 0.6rem 1rem; border-radius: 8px; font-size: 0.8rem; font-weight: 600; white-space: nowrap; box-shadow: 0 4px 15px rgba(0,0,0,0.15); opacity: 0; transform: translateX(10px); transition: all 0.3s ease; pointer-events: none; }
        .whatsapp-float .tooltip::after { content: ''; position: absolute; right: -6px; top: 50%; transform: translateY(-50%); border-left: 6px solid white; border-top: 6px solid transparent; border-bottom: 6px solid transparent; }
        .whatsapp-float:hover .tooltip { opacity: 1; transform: translateX(0); }
        @keyframes whatsapp-enter { from { opacity: 0; transform: scale(0.5) translateY(20px); } to { opacity: 1; transform: scale(1) translateY(0); } }

        @keyframes fadeInUp { from { opacity: 0; transform: translateY(30px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes fadeInDown { from { opacity: 0; transform: translateY(-20px); } to { opacity: 1; transform: translateY(0); } }
        .reveal { opacity: 0; transform: translateY(40px); transition: all 0.8s cubic-bezier(0.22, 1, 0.36, 1); }
        .reveal.visible { opacity: 1; transform: translateY(0); }

        @media (max-width: 1024px) { .pricing-grid { grid-template-columns: repeat(2, 1fr); } }
        @media (max-width: 768px) {
            .nav-links { display: none; }
            .hamburger { display: flex; }
            .nav-links.mobile-open { display: flex; flex-direction: column; position: absolute; top: 100%; left: 0; right: 0; background: rgba(6,9,26,0.98); backdrop-filter: blur(20px); padding: 1.5rem 2rem 2rem; gap: 1rem; border-bottom: 1px solid rgba(233,30,140,0.1); animation: fadeInDown 0.3s ease; }
            .nav-links.mobile-open a { font-size: 1rem; padding: 0.5rem 0; }
            .nav-cta { text-align: center; display: block; }
            .hero { padding: 5.5rem 1.25rem 2rem; min-height: auto; }
            .hero-badge { margin-bottom: 1rem; font-size: 0.7rem; padding: 0.4rem 1rem; }
            .hero h1 { margin-bottom: 0.3rem; }
            .hero-subtitle { margin-bottom: 1rem; }
            .hero-features { gap: 0.35rem; margin-bottom: 1.2rem; }
            .hero-feature { font-size: 0.78rem; padding: 0.35rem 0.7rem; }
            .hero-stats { gap: 1.5rem; margin-bottom: 1.5rem; }
            .hero-cta { padding: 0.85rem 2rem; font-size: 1rem; }
            .compare-grid { grid-template-columns: 1fr; gap: 1.25rem; }
            .compare-col { padding: 1.5rem; }
            .pricing-grid { grid-template-columns: 1fr; max-width: 400px; margin: 0 auto; }
            section { padding: 3.5rem 1.25rem; }
            .whatsapp-float { bottom: 16px; right: 16px; width: 56px; height: 56px; }
        }
        @media (max-width: 480px) {
            .hero { padding: 5rem 1rem 1.5rem; }
            .hero-badge { margin-bottom: 0.75rem; font-size: 0.65rem; }
            .hero h1 { font-size: 2.3rem; margin-bottom: 0.2rem; }
            .hero-subtitle { font-size: 1rem; letter-spacing: 2px; margin-bottom: 0.75rem; }
            .hero-features { gap: 0.3rem; margin-bottom: 1rem; }
            .hero-feature { font-size: 0.72rem; padding: 0.3rem 0.6rem; }
            .hero-feature .icon { font-size: 0.9rem; }
            .hero-stats { flex-direction: row; gap: 1rem; margin-bottom: 1.2rem; }
            .hero-stat-number { font-size: 1.6rem; }
            .hero-stat-label { font-size: 0.6rem; }
            .hero-cta { padding: 0.75rem 1.8rem; font-size: 0.9rem; }
        }
    </style>
</head>
<body>

    <nav class="nav" id="navbar">
        <img src="assets/img/logo.png" alt="<?= e($site['name']) ?>" class="nav-logo">
        <ul class="nav-links" id="navLinks">
            <li><a href="#sobre">Sobre</a></li>
            <li><a href="#planos">Planos</a></li>
            <li><a href="#duvidas">Dúvidas</a></li>
            <li><a href="<?= $waUrl ?>" class="nav-cta" target="_blank">Assinar Agora</a></li>
        </ul>
        <div class="hamburger" id="hamburger" onclick="toggleMenu()">
            <span></span><span></span><span></span>
        </div>
    </nav>

    <section class="hero" id="sobre">
        <div class="hero-bg"></div>
        <div class="hero-content">
            <div class="hero-badge"><span class="pulse"></span> <?= e($site['hero_badge']) ?></div>
            <h1><?= e($site['hero_headline']) ?><br><span class="gradient-text"><?= e($site['hero_highlight']) ?></span></h1>
            <p class="hero-subtitle"><?= e($site['hero_subtitle']) ?></p>

            <div class="hero-features">
                <?php foreach ($config['features'] as $feat): ?>
                <div class="hero-feature"><span class="icon"><?= $feat['icon'] ?></span> <?= e($feat['text']) ?></div>
                <?php endforeach; ?>
            </div>

            <div class="hero-stats">
                <div class="hero-stat">
                    <div class="hero-stat-number"><?= e($stats['channels']) ?></div>
                    <div class="hero-stat-label"><?= e($stats['channels_label']) ?></div>
                </div>
                <div class="hero-stat">
                    <div class="hero-stat-number"><?= e($stats['movies']) ?></div>
                    <div class="hero-stat-label"><?= e($stats['movies_label']) ?></div>
                </div>
                <div class="hero-stat">
                    <div class="hero-stat-number"><?= e($stats['series']) ?></div>
                    <div class="hero-stat-label"><?= e($stats['series_label']) ?></div>
                </div>
            </div>

            <a href="<?= waLink($site['whatsapp'], 'Olá! Quero assinar o plano PRIME TV!') ?>" class="hero-cta" target="_blank">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                Quero Assinar Agora
            </a>
        </div>
    </section>

    <section class="compare" id="comparacao">
        <div class="section-container">
            <div class="section-header reveal">
                <span class="section-tag">Comparativo</span>
                <h2 class="section-title">POR QUE ESCOLHER A <?= e(strtoupper($site['name'])) ?>?</h2>
            </div>
            <div class="compare-grid reveal">
                <div class="compare-col others">
                    <div class="compare-col-title">OUTROS SERVIÇOS</div>
                    <?php foreach ($comp['others'] as $item): ?>
                    <div class="compare-item">
                        <span class="compare-icon">✕</span>
                        <span class="compare-item-text"><?= e($item) ?></span>
                    </div>
                    <?php endforeach; ?>
                </div>
                <div class="compare-col us">
                    <div class="compare-col-title"><?= e(strtoupper($site['name'])) ?></div>
                    <?php foreach ($comp['us'] as $item): ?>
                    <div class="compare-item">
                        <span class="compare-icon">✓</span>
                        <span class="compare-item-text"><?= e($item) ?></span>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </section>

    <section class="pricing" id="planos">
        <div class="section-container">
            <div class="section-header reveal">
                <span class="section-tag">Nossos Planos</span>
                <h2 class="section-title">ESCOLHA O SEU PLANO</h2>
            </div>
            <div class="pricing-grid reveal">
                <?php foreach ($plans as $plan): ?>
                <div class="price-card<?= $plan['featured'] ? ' featured' : '' ?>">
                    <?php if ($plan['featured']): ?><div class="price-badge">Popular</div><?php endif; ?>
                    <div class="price-duration"><?= e($plan['name']) ?></div>
                    <div class="price-amount">
                        <span class="price-currency">R$</span>
                        <span class="price-value"><?= e($plan['price_int']) ?></span>
                        <span class="price-cents"><?= e($plan['price_dec']) ?></span>
                    </div>
                    <div class="price-period"><?= e($plan['period']) ?></div>
                    <ul class="price-features">
                        <?php foreach ($planFeatures as $feat): ?>
                        <li><span class="check">✓</span> <?= e($feat) ?></li>
                        <?php endforeach; ?>
                    </ul>
                    <a href="<?= waLink($site['whatsapp'], $plan['whatsapp_text']) ?>" class="price-btn" target="_blank">Assinar Agora</a>
                    <?php if ($plan['economy']): ?>
                    <div class="price-economy"><?= e($plan['economy']) ?></div>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <section class="faq" id="duvidas">
        <div class="section-container">
            <div class="section-header reveal">
                <span class="section-tag">Perguntas Frequentes</span>
                <h2 class="section-title">FICOU COM ALGUMA DÚVIDA?</h2>
            </div>
            <div class="faq-list reveal">
                <?php foreach ($faq as $item): ?>
                <div class="faq-item">
                    <div class="faq-question" onclick="toggleFaq(this)">
                        <?= e($item['question']) ?>
                        <span class="faq-toggle"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg></span>
                    </div>
                    <div class="faq-answer">
                        <div class="faq-answer-inner"><?= e($item['answer']) ?></div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <footer class="footer">
        <img src="assets/img/logo.png" alt="<?= e($site['name']) ?>" class="footer-logo">
        <div class="footer-links">
            <a href="#sobre">Sobre</a>
            <a href="#planos">Planos</a>
            <a href="#duvidas">Dúvidas</a>
            <a href="<?= waLink($site['whatsapp']) ?>" target="_blank">Contato</a>
        </div>
        <p class="footer-text"><?= e($site['name']) ?> — O melhor entretenimento na palma da sua mão.</p>
        <p class="footer-copy">&copy; <?= date('Y') ?> <?= e($site['name']) ?>. Todos os direitos reservados.</p>
    </footer>

    <a href="<?= $waUrl ?>" class="whatsapp-float" target="_blank" aria-label="WhatsApp">
        <span class="tooltip">Fale conosco!</span>
        <svg viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg"><path d="M16.004 0h-.008C7.174 0 0 7.176 0 16c0 3.5 1.128 6.744 3.046 9.378L1.054 31.29l6.118-1.958A15.914 15.914 0 0 0 16.004 32C24.826 32 32 24.822 32 16S24.826 0 16.004 0zm9.338 22.594c-.392 1.106-1.932 2.024-3.178 2.292-.852.18-1.964.324-5.71-1.228-4.796-1.986-7.882-6.848-8.122-7.166-.23-.318-1.932-2.574-1.932-4.908s1.224-3.482 1.66-3.96c.392-.432 1.034-.648 1.65-.648.198 0 .376.01.536.018.478.02.718.048 1.034.8.392.936 1.35 3.27 1.468 3.51.12.24.24.558.08.876-.148.326-.278.47-.518.748-.24.278-.468.49-.708.79-.22.26-.468.538-.19.996.278.45 1.234 2.036 2.65 3.298 1.82 1.624 3.352 2.126 3.828 2.364.358.178.784.14 1.074-.168.368-.398.822-1.058 1.284-1.708.328-.462.742-.52 1.136-.358.4.154 2.528 1.192 2.962 1.41.434.218.722.326.83.51.106.184.106 1.078-.286 2.184z"/></svg>
    </a>

    <script>
        const navbar = document.getElementById('navbar');
        window.addEventListener('scroll', () => { navbar.classList.toggle('scrolled', window.scrollY > 50); });
        function toggleMenu() { document.getElementById('navLinks').classList.toggle('mobile-open'); document.getElementById('hamburger').classList.toggle('active'); }
        document.querySelectorAll('.nav-links a').forEach(link => { link.addEventListener('click', () => { document.getElementById('navLinks').classList.remove('mobile-open'); document.getElementById('hamburger').classList.remove('active'); }); });
        function toggleFaq(el) { const item = el.parentElement; const answer = item.querySelector('.faq-answer'); const isActive = item.classList.contains('active'); document.querySelectorAll('.faq-item').forEach(i => { i.classList.remove('active'); i.querySelector('.faq-answer').style.maxHeight = '0'; }); if (!isActive) { item.classList.add('active'); answer.style.maxHeight = answer.scrollHeight + 'px'; } }
        const revealEls = document.querySelectorAll('.reveal'); const revealObserver = new IntersectionObserver((entries) => { entries.forEach(entry => { if (entry.isIntersecting) { entry.target.classList.add('visible'); revealObserver.unobserve(entry.target); } }); }, { threshold: 0.1, rootMargin: '0px 0px -40px 0px' }); revealEls.forEach(el => revealObserver.observe(el));
    </script>
</body>
</html>
