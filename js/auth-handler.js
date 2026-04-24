import { auth, provider, signInWithPopup, onAuthStateChanged, signOut } from './firebase-config.js';

const authSection = document.getElementById('auth-section');
const btnLoginGoogle = document.getElementById('btn-login-google');

// Escuchar cambios en el estado de autenticación
onAuthStateChanged(auth, (user) => {
    if (user) {
        // Usuario está logueado
        updateUI(user);
        // Opcional: Sincronizar con sesión PHP vía fetch
        syncSession(user);
    } else {
        // Usuario no está logueado
        resetUI();
    }
});

// Evento de Login
if(btnLoginGoogle) {
    btnLoginGoogle.onclick = async () => {
        try {
            await signInWithPopup(auth, provider);
        } catch (error) {
            console.error("Error al iniciar sesión:", error);
            alert("No se pudo iniciar sesión con Google.");
        }
    };
}

function updateUI(user) {
    if(!authSection) return;
    authSection.innerHTML = `
        <div class="user-profile">
            <img src="${user.photoURL}" alt="${user.displayName}" class="user-avatar">
            <span>${user.displayName}</span>
            <button id="btn-logout" class="btn-outline-small">Salir</button>
        </div>
    `;

    document.getElementById('btn-logout').onclick = () => {
        signOut(auth);
    };
}

function resetUI() {
    if(!authSection) return;
    authSection.innerHTML = `
        <button id="btn-login-google" class="btn-google">
            <img src="https://www.gstatic.com/firebasejs/ui/2.0.0/images/action/google.svg" alt="Google">
            Entrar
        </button>
    `;
    // Re-vincular evento ya que el innerHTML borró el anterior
    const newBtn = document.getElementById('btn-login-google');
    newBtn.onclick = async () => {
        try {
            await signInWithPopup(auth, provider);
        } catch (error) {
            console.error(error);
        }
    };
}

async function syncSession(user) {
    const idToken = await user.getIdToken();
    // Enviamos el token a PHP para crear una sesión de servidor si es necesario
    // fetch('api/login.php', {
    //     method: 'POST',
    //     body: JSON.stringify({ token: idToken })
    // });
}
