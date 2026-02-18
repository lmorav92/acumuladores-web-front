// assets/js/auth-check.js
const sessionKey = "session_mi_turno";
const INACTIVITY_LIMIT = 10 * 60 * 1000; // 10 minutos

function checkAuth() {
    const sessionStr = localStorage.getItem(sessionKey);
    if (!sessionStr) {
        window.location.href = "index.html";
        return;
    }

    const session = JSON.parse(sessionStr);
    const now = new Date().getTime();

    if (now - session.lastActivity > INACTIVITY_LIMIT) {
        localStorage.removeItem(sessionKey);
        window.location.href = "index.html";
        return;
    }

    session.lastActivity = now;
    localStorage.setItem(sessionKey, JSON.stringify(session));
}

// Función global para cerrar sesión
window.logout = function() {
    Swal.fire({
        title: "¿Deseas cerrar sesión?",
        icon: 'question',
        showDenyButton: true,
        confirmButtonText: "Salir",
        denyButtonText: `Cancelar`,
    }).then((result) => {
        if (result.isConfirmed) {
            localStorage.removeItem(sessionKey);
            window.location.href = "index.html";
        }
    });
};

// Listeners para actividad
document.onmousemove = checkAuth;
document.onkeypress = checkAuth;

// Ejecución inicial
checkAuth();