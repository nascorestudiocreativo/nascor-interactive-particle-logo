<?php
/**
 * Plugin Name:       Nascor Interactive Particle Logo
 * Plugin URI:        https://nascor.ar
 * Description:       Logo formado por partículas interactivas. Se dispersan con el cursor y regresan a su forma original. Shortcode: [nascor_particle_logo]
 * Version:           2.0.1
 * Author:            Nascor Estudio Creativo
 */

// Evita acceso directo
if (!defined('ABSPATH')) exit;

add_shortcode('nascor_particle_logo', 'nascor_particle_logo_shortcode');

function nascor_particle_logo_shortcode($atts) {
    // Puedes cambiar la URL del logo directamente en el shortcode: 
    // [nascor_particle_logo logo_url="https://tu-sitio.com/logo.png"]
    $a = shortcode_atts([
        'logo_url' => 'https://nascor.ar/wp-content/uploads/2026/04/Nascor-logo-solo.png' 
    ], $atts);

    ob_start();
    ?>
    <div class="nascor-logo-wrapper" style="width: 100%; display: flex; justify-content: center; align-items: center; padding: 20px 0; overflow: visible;">
        
        <canvas id="nascor-logo-canvas" style="display: block; max-width: 100%; cursor: crosshair;"></canvas>

        <script>
            document.addEventListener("DOMContentLoaded", () => {
                const canvas = document.getElementById('nascor-logo-canvas');
                const ctx = canvas.getContext('2d', { willReadFrequently: true });
                
                let particles = [];
                let animationFrame;
                
                // Configuración de interacción
                const mouse = { x: null, y: null, radius: 80 };

                // --- EVENTOS DEL MOUSE Y TÁCTILES (CORREGIDOS) ---
                // Ahora se calcula en tiempo real para evitar el desfase por scroll o escalado CSS
                canvas.addEventListener('mousemove', (e) => {
                    const rect = canvas.getBoundingClientRect();
                    const scaleX = canvas.width / rect.width;
                    const scaleY = canvas.height / rect.height;
                    
                    mouse.x = (e.clientX - rect.left) * scaleX;
                    mouse.y = (e.clientY - rect.top) * scaleY;
                });
                
                canvas.addEventListener('mouseleave', () => {
                    mouse.x = null;
                    mouse.y = null;
                });
                
                canvas.addEventListener('touchmove', (e) => {
                    const rect = canvas.getBoundingClientRect();
                    const scaleX = canvas.width / rect.width;
                    const scaleY = canvas.height / rect.height;
                    
                    mouse.x = (e.touches[0].clientX - rect.left) * scaleX;
                    mouse.y = (e.touches[0].clientY - rect.top) * scaleY;
                }, { passive: true });
                
                canvas.addEventListener('touchend', () => {
                    mouse.x = null;
                    mouse.y = null;
                });

                // --- CLASE PARTÍCULA ---
                class Particle {
                    constructor(x, y, color) {
                        this.x = x + (Math.random() - 0.5) * 20; 
                        this.y = y + (Math.random() - 0.5) * 20;
                        this.baseX = x; 
                        this.baseY = y;
                        this.color = color;
                        this.size = 2; 
                        this.vx = 0;
                        this.vy = 0;
                        this.density = (Math.random() * 30) + 1; 
                    }

                    update() {
                        // 1. Interacción con el cursor (Repulsión)
                        if (mouse.x != null && mouse.y != null) {
                            let dx = mouse.x - this.x;
                            let dy = mouse.y - this.y;
                            let distance = Math.sqrt(dx * dx + dy * dy);
                            
                            if (distance < mouse.radius) {
                                let forceDirectionX = dx / distance;
                                let forceDirectionY = dy / distance;
                                let maxDistance = mouse.radius;
                                let force = (maxDistance - distance) / maxDistance;
                                
                                // Empuje dinámico
                                let directionX = forceDirectionX * force * this.density;
                                let directionY = forceDirectionY * force * this.density;
                                
                                this.vx -= directionX;
                                this.vy -= directionY;
                            }
                        }

                        // 2. Fuerza de retorno (Resorte/Spring hacia la forma original)
                        let returnForceX = (this.baseX - this.x) * 0.05; 
                        let returnForceY = (this.baseY - this.y) * 0.05;
                        
                        this.vx += returnForceX;
                        this.vy += returnForceY;

                        // 3. Fricción para que se detengan suavemente
                        this.vx *= 0.85;
                        this.vy *= 0.85;

                        // 4. Aplicar movimiento
                        this.x += this.vx;
                        this.y += this.vy;
                    }

                    draw() {
                        ctx.fillStyle = this.color;
                        ctx.beginPath();
                        ctx.arc(this.x, this.y, this.size, 0, Math.PI * 2);
                        ctx.fill();
                    }
                }

                // --- CARGA Y ANÁLISIS DE LA IMAGEN ---
                function initLogo() {
                    particles = [];
                    const image = new Image();
                    image.crossOrigin = "Anonymous"; 
                    image.src = "<?php echo esc_url($a['logo_url']); ?>";

                    image.onload = () => {
                        const isMobile = window.innerWidth <= 600;
                        canvas.width = isMobile ? window.innerWidth - 40 : 500;
                        canvas.height = canvas.width; 

                        const scale = Math.min(canvas.width / image.width, canvas.height / image.height) * 0.8; 
                        const drawWidth = image.width * scale;
                        const drawHeight = image.height * scale;
                        const startX = (canvas.width - drawWidth) / 2;
                        const startY = (canvas.height - drawHeight) / 2;

                        ctx.drawImage(image, startX, startY, drawWidth, drawHeight);
                        const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
                        const data = imageData.data;

                        ctx.clearRect(0, 0, canvas.width, canvas.height);

                        const step = isMobile ? 5 : 4; 
                        
                        for (let y = 0; y < canvas.height; y += step) {
                            for (let x = 0; x < canvas.width; x += step) {
                                const index = (y * canvas.width + x) * 4;
                                const alpha = data[index + 3];

                                if (alpha > 128) {
                                    const red = data[index];
                                    const green = data[index + 1];
                                    const blue = data[index + 2];
                                    const color = `rgb(${red},${green},${blue})`;
                                    
                                    particles.push(new Particle(x, y, color));
                                }
                            }
                        }

                        animate();
                    };
                }

                function animate() {
                    ctx.clearRect(0, 0, canvas.width, canvas.height);
                    
                    for (let i = 0; i < particles.length; i++) {
                        particles[i].update();
                        particles[i].draw();
                    }
                    
                    animationFrame = requestAnimationFrame(animate);
                }

                // Reiniciar si se redimensiona fuertemente la pantalla
                window.addEventListener('resize', () => {
                    clearTimeout(window.resizeTimer);
                    window.resizeTimer = setTimeout(() => {
                        cancelAnimationFrame(animationFrame);
                        initLogo();
                    }, 200);
                });

                // Iniciar
                initLogo();
            });
        </script>
    </div>
    <?php
    return ob_get_clean();
}
?>