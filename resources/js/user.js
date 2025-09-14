//Logica de usuario (registro e inicio de sesion)
document.addEventListener("DOMContentLoaded", () => {
    // Registro
    const btnRegistrar = document.getElementById("btnRegistrar");
    if (btnRegistrar) btnRegistrar.addEventListener("click", registrarUsuario);

    // Login
    const btnLogin = document.getElementById("btnLogin");
    if (btnLogin) btnLogin.addEventListener("click", iniciarSesion);
});

function iniciarSesion() {
    const correo = document.getElementById("loginEmail").value.trim();
    const password = document.getElementById("loginPassword").value;

    const formData = new FormData();
    formData.append("Accion", "Login");
    formData.append("correo", correo);
    formData.append("password", password);

    fetch("../func/procesarRegistro.php", {
            method: "POST",
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.exito) {
                Swal.fire("Éxito", "Bienvenido " + data.Correo, "success")
                    .then(() => {
                        if (data.redirect) window.location.href = data.redirect;
                    });
            } else {
                Swal.fire("Error", data.error || "Usuario o contraseña incorrectos.", "error");
            }
        })
        .catch(err => {
            console.error("Error:", err);
            Swal.fire("Error", "No se pudo conectar con el servidor.", "error");
        });
}

function registrarUsuario() {
    const correo = document.getElementById("correo").value.trim();

    const checkForm = new FormData();
    checkForm.append("Accion", "VerificarDuplicado");
    checkForm.append("Correo", correo);

    fetch("../func/procesarRegistro.php", {
            method: "POST",
            body: checkForm
        })
        .then(async res => {
            const raw = await res.text();
            console.log("Respuesta cruda:", raw);
            try {
                return JSON.parse(raw);
            } catch {
                throw new Error("Respuesta no es JSON: " + raw);
            }
        })
        .then(data => {
            if (data.existe) {
                Swal.fire("Error", "Ya existe un usuario con este correo.", "warning");
            } else {
                enviarFormularioRegistro();
            }
        })
        .catch(err => {
            console.error("Error:", err);
            Swal.fire("Error", err.message, "error");
        });

}

function enviarFormularioRegistro() {
    const form = document.getElementById("registroForm");
    const formData = new FormData(form);

    // Validar contraseñas
    const password = formData.get("password");
    const confirmPassword = formData.get("confirmPassword");

    if (!validarPasswordSegura(password)) {
        Swal.fire("Error", "La contraseña debe tener al menos 8 caracteres, incluir letras, números y un símbolo especial.", "warning");
        return;
    }
    if (password !== confirmPassword) {
        Swal.fire("Error", "Las contraseñas no coinciden.", "error");
        return;
    }

    formData.append("Accion", "Guardar");

    fetch("../func/procesarRegistro.php", {
            method: "POST",
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.exito) {
                Swal.fire("Éxito", "Usuario registrado correctamente.", "success")
                    .then(() => {
                        if (data.redirect) {
                            window.location.href = data.redirect;
                        }
                    });
                form.reset();
            } else {
                Swal.fire("Error", data.error || "Ocurrió un error al registrar.", "error");
            }
        })
        .catch(err => {
            console.error("Error:", err);
            Swal.fire("Error", "No se pudo conectar con el servidor.", "error");
        });
}

function validarPasswordSegura(password) {
    const regex = /^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*#?&\-_])[A-Za-z\d@$!%*#?&\-_]{8,}$/;
    return regex.test(password);
}