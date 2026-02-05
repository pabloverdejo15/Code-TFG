
<!-- Global Loader Overlay -->
<div id="global-loader" style="display: none;">
    <div class="loader-content">
        <div class="loader">
            <span class="loader-text">loading</span>
            <span class="load"></span>
        </div>
        <p id="loader-message" style="margin-top: 1rem; color: var(--text-muted); font-size: 0.9rem; letter-spacing: 0.5px;">Procesando...</p>
    </div>
</div>

<style>
    #global-loader {
        position: fixed;
        top: 0; left: 0; width: 100%; height: 100%;
        background: rgba(0, 0, 0, 0.85);
        backdrop-filter: blur(10px);
        z-index: 99999; /* Topmost */
        display: flex;
        justify-content: center;
        align-items: center;
        opacity: 0;
        animation: fadeInLoader 0.3s ease forwards;
    }

    .loader-content {
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .loader {
        width: 80px;
        height: 50px;
        position: relative;
    }

    .loader-text {
        position: absolute;
        top: 0;
        padding: 0;
        margin: 0;
        color: #a5b4fc;
        animation: text_713 3.5s ease both infinite;
        font-size: .8rem;
        letter-spacing: 1px;
        text-transform: uppercase;
        font-weight: 600;
    }

    .load {
        background-color: #0055FF;
        border-radius: 50px;
        display: block;
        height: 16px;
        width: 16px;
        bottom: 0;
        position: absolute;
        transform: translateX(64px);
        animation: loading_713 3.5s ease both infinite;
    }

    .load::before {
        position: absolute;
        content: "";
        width: 100%;
        height: 100%;
        background-color: #60a5fa;
        border-radius: inherit;
        animation: loading2_713 3.5s ease both infinite;
    }

    @keyframes fadeInLoader { to { opacity: 1; } }

    @keyframes text_713 {
        0% { letter-spacing: 1px; transform: translateX(0px); }
        40% { letter-spacing: 2px; transform: translateX(26px); }
        80% { letter-spacing: 1px; transform: translateX(32px); }
        90% { letter-spacing: 2px; transform: translateX(0px); }
        100% { letter-spacing: 1px; transform: translateX(0px); }
    }

    @keyframes loading_713 {
        0% { width: 16px; transform: translateX(0px); }
        40% { width: 100%; transform: translateX(0px); }
        80% { width: 16px; transform: translateX(64px); }
        90% { width: 100%; transform: translateX(0px); }
        100% { width: 16px; transform: translateX(0px); }
    }

    @keyframes loading2_713 {
        0% { transform: translateX(0px); width: 16px; }
        40% { transform: translateX(0%); width: 80%; }
        80% { width: 100%; transform: translateX(0px); }
        90% { width: 80%; transform: translateX(15px); }
        100% { transform: translateX(0px); width: 16px; }
    }
</style>

<script>
    function showGlobalLoader(message = 'Procesando...') {
        const loader = document.getElementById('global-loader');
        const msgEl = document.getElementById('loader-message');
        if (loader) {
            msgEl.textContent = message;
            loader.style.display = 'flex';
        }
    }

    // Auto-attach to forms
    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('form').forEach(form => {
            form.addEventListener('submit', () => {
                showGlobalLoader();
            });
        });
    });
</script>
