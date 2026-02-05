const canvas = document.getElementById('auth-canvas');
const ctx = canvas.getContext('2d');

let width, height;

// Configuration
const config = {
    color: '0, 85, 255', // Cobalt Blue RGB
    glowSize: 600,       // Size of the glow
    intensity: 0.15,     // Opacity of the glow
    followSpeed: 0.1     // Factor [0, 1] for movement smoothing (lower is smoother/slower)
};

// State
const target = { x: window.innerWidth / 2, y: window.innerHeight / 2 }; // Target position (mouse)
const current = { x: window.innerWidth / 2, y: window.innerHeight / 2 }; // Current position (interpolated)

// Mouse tracking
window.addEventListener('mousemove', e => {
    target.x = e.x;
    target.y = e.y;
});

// Resize handling
function resize() {
    width = canvas.width = window.innerWidth;
    height = canvas.height = window.innerHeight;
}
window.addEventListener('resize', resize);
resize();

// Linear interpolation utility
function lerp(start, end, factor) {
    return start + (end - start) * factor;
}

function animate() {
    requestAnimationFrame(animate);

    // Clear canvas with pitch black
    ctx.fillStyle = '#000000';
    ctx.fillRect(0, 0, width, height);

    // Smooth movement
    current.x = lerp(current.x, target.x, config.followSpeed);
    current.y = lerp(current.y, target.y, config.followSpeed);

    // Create Gradient
    // We create a radial gradient centered at the current smoothed position
    const gradient = ctx.createRadialGradient(
        current.x, current.y, 0,                // Inner circle (center)
        current.x, current.y, config.glowSize   // Outer circle
    );

    // Inner color: Cobalt Blue with defined intensity
    gradient.addColorStop(0, `rgba(${config.color}, ${config.intensity})`);

    // Middle fade
    gradient.addColorStop(0.5, `rgba(${config.color}, ${config.intensity * 0.4})`);

    // Outer edge: Transparent
    gradient.addColorStop(1, 'rgba(0, 0, 0, 0)');

    // Draw the glow
    ctx.fillStyle = gradient;
    ctx.fillRect(0, 0, width, height);
}

// Start animation
animate();
