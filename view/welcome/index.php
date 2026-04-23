<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenido | Code TFG</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Icons -->
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <!-- Styles -->
    <link rel="stylesheet" href="<?= BASE_URL ?>css/welcome.css">
</head>
<body>
    <!-- Background Canvas/Elements -->
    <div class="glow-bg glow-1"></div>
    <div class="glow-bg glow-2"></div>

    <nav class="navbar">
        <div class="nav-container">
            <div class="brand">
                <i class="ph-fill ph-buildings"></i>
                <span>Hello Neighbor</span>
            </div>
            <div class="nav-actions">
                <a href="<?= BASE_URL ?>?controller=Auth&action=login" class="btn btn-outline">Iniciar sesión</a>
            </div>
        </div>
    </nav>

    <main>
        <!-- Hero Section -->
        <section class="hero reveal">
            <div class="hero-content">
                <span class="badge">La nueva forma de conectar</span>
                <h1 class="hero-title">Tu comunidad de vecinos,<br><span class="text-gradient">más unida que nunca.</span></h1>
                <p class="hero-subtitle">
                    Olvídate de los grupos de WhatsApp caóticos y las notas en el ascensor. 
                    Una plataforma profesional diseñada para gestionar, comunicar y conectar con tus vecinos de forma inteligente.
                </p>
                <div class="hero-actions">
                    <a href="<?= BASE_URL ?>?controller=Auth&action=register" class="btn btn-primary btn-lg">
                        Empieza ahora
                        <i class="ph-bold ph-arrow-right"></i>
                    </a>
                    <a href="#features" class="btn btn-secondary btn-lg">
                        Explorar funciones
                    </a>
                </div>
            </div>
            
            <!-- Floating Elements / Abstract Graphic for Hero -->
            <div class="hero-graphic">
                <div class="glass-card card-1 floating">
                    <div class="card-header">
                        <div class="avatar bg-blue"><i class="ph-bold ph-chat-text"></i></div>
                        <div>
                            <strong>Reunión Anual</strong>
                            <span>Hace 5 min</span>
                        </div>
                    </div>
                    <div class="card-body">
                        ¿Alguien puede confirmar la hora de la reunión de propietarios?
                    </div>
                </div>
                <div class="glass-card card-2 floating-delayed">
                    <div class="card-header">
                        <div class="avatar bg-green"><i class="ph-bold ph-megaphone"></i></div>
                        <div>
                            <strong>Aviso Administración</strong>
                            <span>Ayer</span>
                        </div>
                    </div>
                    <div class="card-body">
                        Corte de agua programado para el jueves de 10:00 a 12:00.
                    </div>
                </div>
            </div>
        </section>

        <!-- Features Section -->
        <section id="features" class="features reveal">
            <div class="section-header">
                <h2>Todo lo que tu edificio necesita</h2>
                <p>Herramientas pensadas para facilitar la convivencia y la organización.</p>
            </div>
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon"><i class="ph-duotone ph-chats-circle"></i></div>
                    <h3>Canales Temáticos</h3>
                    <p>Organiza las conversaciones por temas. Desde "Mantenimiento" hasta "Eventos del barrio", mantén la comunicación limpia y estructurada.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon"><i class="ph-duotone ph-paper-plane-tilt"></i></div>
                    <h3>Mensajes Directos</h3>
                    <p>Comunícate en privado con cualquier vecino sin necesidad de compartir tu número de teléfono personal.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon"><i class="ph-duotone ph-push-pin"></i></div>
                    <h3>Tablón de Avisos</h3>
                    <p>Publicaciones oficiales del administrador o presidente. Accede a documentos, normativas y comunicados importantes al instante.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon"><i class="ph-duotone ph-shield-check"></i></div>
                    <h3>Privacidad Total</h3>
                    <p>Acceso seguro mediante códigos de invitación. Solo los residentes reales pueden unirse a la comunidad de tu edificio.</p>
                </div>
            </div>
        </section>

        <!-- Onboarding / How to start -->
        <section class="onboarding reveal">
            <div class="section-header">
                <h2>Comenzar es muy sencillo</h2>
                <p>En menos de 3 minutos estarás conectado con tu edificio.</p>
            </div>
            <div class="steps-container">
                <div class="step">
                    <div class="step-number">1</div>
                    <h3>Crea tu cuenta</h3>
                    <p>Regístrate de forma rápida y segura en nuestra plataforma.</p>
                </div>
                <div class="step-connector"></div>
                <div class="step">
                    <div class="step-number">2</div>
                    <h3>Únete a tu comunidad</h3>
                    <p>Introduce el código de acceso proporcionado por tu administrador o presidente.</p>
                </div>
                <div class="step-connector"></div>
                <div class="step">
                    <div class="step-number">3</div>
                    <h3>Empieza a interactuar</h3>
                    <p>Participa en los canales, lee los avisos o envía mensajes a tus vecinos.</p>
                </div>
            </div>
        </section>

        <!-- Final CTA -->
        <section class="cta-bottom reveal">
            <div class="cta-card">
                <h2>¿Listo para mejorar la convivencia?</h2>
                <p>Únete a miles de vecinos que ya disfrutan de una comunicación más clara, rápida y respetuosa.</p>
                <div class="cta-actions">
                    <a href="<?= BASE_URL ?>?controller=Auth&action=register" class="btn btn-primary btn-lg">Crear mi cuenta gratuita</a>
                </div>
            </div>
        </section>
    </main>

    <footer class="footer">
        <div class="footer-content">
            <div class="brand">
                <i class="ph-fill ph-buildings"></i>
                <span>Hello Neighbor</span>
            </div>
            <p>&copy; 2026 Hello Neighbor. Todos los derechos reservados.</p>
        </div>
    </footer>

    <script>
        // Smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                document.querySelector(this.getAttribute('href')).scrollIntoView({
                    behavior: 'smooth'
                });
            });
        });

        // Reveal animation on scroll
        const revealElements = document.querySelectorAll('.reveal');
        
        const revealObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('active');
                }
            });
        }, { threshold: 0.1, rootMargin: "0px 0px -50px 0px" });

        revealElements.forEach(el => revealObserver.observe(el));
        
        // Trigger initial reveal for hero
        setTimeout(() => {
            document.querySelector('.hero').classList.add('active');
        }, 100);
    </script>
</body>
</html>
