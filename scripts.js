document.addEventListener("DOMContentLoaded", () => {
  // Configurar validaciones en tiempo real
  configurarValidaciones()

  // Configurar búsqueda en tiempo real
  configurarBusqueda()

  // Mostrar notificaciones si hay mensajes en la URL
  mostrarMensajesURL()
})

// Configurar validaciones en tiempo real
function configurarValidaciones() {
  // Validación para formulario de registro de usuario
  const formRegistro = document.getElementById("formRegistro")
  if (formRegistro) {
    const usuarioInput = document.getElementById("usuarioInput")
    const passwordInput = document.getElementById("passwordInput")
    const confirmarPasswordInput = document.getElementById("confirmarPasswordInput")

    if (usuarioInput) {
      usuarioInput.addEventListener("input", function () {
        validarUsuario(this.value)
      })
    }

    if (passwordInput) {
      passwordInput.addEventListener("input", function () {
        validarPassword(this.value)
      })
    }

    if (confirmarPasswordInput) {
      confirmarPasswordInput.addEventListener("input", function () {
        validarConfirmacionPassword(passwordInput.value, this.value)
      })
    }
  }

  // Validación para formulario de mascota
  const formMascota = document.getElementById("formMascota")
  if (formMascota) {
    const nombreInput = document.getElementById("nombre")
    if (nombreInput) {
      nombreInput.addEventListener("input", function () {
        validarNombreMascota(this.value)
      })
    }
  }

  // Validación para formulario de dueño
  const formDueno = document.getElementById("formDueno")
  if (formDueno) {
    const nombreInput = document.getElementById("nombre")
    const telefonoInput = document.getElementById("telefono")

    if (nombreInput) {
      nombreInput.addEventListener("input", function () {
        validarNombrePersona(this.value)
      })
    }

    if (telefonoInput) {
      telefonoInput.addEventListener("input", function () {
        validarTelefono(this.value)
      })
    }
  }
}

// Funciones de validación
function validarUsuario(usuario) {
  const errorDiv = document.getElementById("errorUsuario")
  const patron = /^[a-zA-Z0-9_]+$/

  if (usuario.length < 3) {
    mostrarError(errorDiv, "Debe tener al menos 3 caracteres")
    return false
  } else if (usuario.length > 20) {
    mostrarError(errorDiv, "No puede tener más de 20 caracteres")
    return false
  } else if (!patron.test(usuario)) {
    mostrarError(errorDiv, "Solo puede contener letras, números y guiones bajos")
    return false
  } else {
    limpiarError(errorDiv)
    return true
  }
}

function validarPassword(password) {
  const errorDiv = document.getElementById("errorPassword")

  if (password.length < 8) {
    mostrarError(errorDiv, "Debe tener al menos 8 caracteres")
    return false
  } else {
    limpiarError(errorDiv)
    return true
  }
}

function validarConfirmacionPassword(password, confirmacion) {
  const errorDiv = document.getElementById("errorConfirmar")

  if (password !== confirmacion) {
    mostrarError(errorDiv, "Las contraseñas no coinciden")
    return false
  } else {
    limpiarError(errorDiv)
    return true
  }
}

function validarNombreMascota(nombre) {
  const errorDiv = document.getElementById("errorNombre")
  const patron = /^[a-zA-ZáéíóúÁÉÍÓÚñÑ ]+$/

  if (nombre.length === 0) {
    limpiarError(errorDiv)
    return true
  } else if (!patron.test(nombre)) {
    mostrarError(errorDiv, "Solo puede contener letras y espacios")
    return false
  } else {
    limpiarError(errorDiv)
    return true
  }
}

function validarNombrePersona(nombre) {
  const errorDiv = document.getElementById("errorNombre")
  const patron = /^[a-zA-ZáéíóúÁÉÍÓÚñÑ ]+$/

  if (nombre.length === 0) {
    limpiarError(errorDiv)
    return true
  } else if (!patron.test(nombre)) {
    mostrarError(errorDiv, "Solo puede contener letras y espacios")
    return false
  } else {
    limpiarError(errorDiv)
    return true
  }
}

function validarTelefono(telefono) {
  const errorDiv = document.getElementById("errorTelefono")
  const patron = /^[0-9\-+$$$$\s]+$/

  if (telefono.length === 0) {
    limpiarError(errorDiv)
    return true
  } else if (!patron.test(telefono)) {
    mostrarError(errorDiv, "Formato de teléfono inválido")
    return false
  } else {
    limpiarError(errorDiv)
    return true
  }
}

// Funciones auxiliares para errores
function mostrarError(elemento, mensaje) {
  if (elemento) {
    elemento.textContent = mensaje
    elemento.style.display = "block"
  }
}

function limpiarError(elemento) {
  if (elemento) {
    elemento.textContent = ""
    elemento.style.display = "none"
  }
}

// Configurar búsqueda en tiempo real
function configurarBusqueda() {
  const inputBusqueda = document.getElementById("buscarMascota")
  if (inputBusqueda) {
    inputBusqueda.addEventListener("input", function () {
      filtrarTabla(this.value)
    })
  }
}

// Función para filtrar tablas
function filtrarTabla(termino) {
  const tabla = document.querySelector(".tabla-datos tbody")
  if (!tabla) return

  const filas = tabla.getElementsByTagName("tr")

  for (let i = 0; i < filas.length; i++) {
    const fila = filas[i]
    const celdas = fila.getElementsByTagName("td")
    let mostrar = false

    // Buscar en todas las celdas excepto la de acciones
    for (let j = 0; j < celdas.length - 1; j++) {
      if (celdas[j]) {
        const texto = celdas[j].textContent.toLowerCase()
        if (texto.includes(termino.toLowerCase())) {
          mostrar = true
          break
        }
      }
    }

    fila.style.display = mostrar ? "" : "none"
  }
}

// Mostrar confirmación antes de eliminar
function confirmarEliminacion(tipo, nombre) {
  return confirm(`¿Estás seguro de que quieres eliminar ${tipo} "${nombre}"? Esta acción no se puede deshacer.`)
}

// Mostrar mensajes de la URL
function mostrarMensajesURL() {
  const urlParams = new URLSearchParams(window.location.search)
  const exito = urlParams.get('exito')
  const error = urlParams.get('error')

  if (exito) {
    mostrarNotificacion(exito, 'exito')
    // Limpiar URL sin recargar
    window.history.replaceState({}, document.title, window.location.pathname)
  }

  if (error) {
    mostrarNotificacion(error, 'error')
    // Limpiar URL sin recargar
    window.history.replaceState({}, document.title, window.location.pathname)
  }
}

// Mostrar notificaciones
function mostrarNotificacion(mensaje, tipo = "info") {
  const notificacion = document.createElement("div")
  notificacion.className = `notificacion notificacion-${tipo}`
  notificacion.textContent = mensaje

  // Estilos para la notificación
  notificacion.style.cssText = `
    position: fixed;
    top: 20px;
    right: 20px;
    padding: 1rem 1.5rem;
    border-radius: 0.5rem;
    color: white;
    font-weight: 600;
    z-index: 1000;
    transform: translateX(100%);
    transition: transform 0.3s ease;
    max-width: 300px;
  `

  // Colores según el tipo
  if (tipo === 'exito') {
    notificacion.style.backgroundColor = 'var(--color-primario)'
  } else if (tipo === 'error') {
    notificacion.style.backgroundColor = '#dc2626'
  } else {
    notificacion.style.backgroundColor = 'var(--color-hover)'
  }

  document.body.appendChild(notificacion)

  // Mostrar la notificación
  setTimeout(() => {
    notificacion.style.transform = 'translateX(0)'
  }, 100)

  // Ocultar después de 4 segundos
  setTimeout(() => {
    notificacion.style.transform = 'translateX(100%)'
    setTimeout(() => {
      if (document.body.contains(notificacion)) {
        document.body.removeChild(notificacion)
      }
    }, 300)
  }, 4000)
}

// Función para alternar visibilidad de contraseña
function alternarVisibilidadPassword(inputId) {
  const input = document.getElementById(inputId)
  const tipo = input.type === 'password' ? 'text' : 'password'
  input.type = tipo
}

// Auto-envío de formularios de búsqueda
document.addEventListener('DOMContentLoaded', function() {
  const formulariosBusqueda = document.querySelectorAll('form input[name="buscar"]')
  
  formulariosBusqueda.forEach(input => {
    let tiempoEspera
    input.addEventListener('input', function() {
      clearTimeout(tiempoEspera)
      tiempoEspera = setTimeout(() => {
        this.form.submit()
      }, 500) // Esperar 500ms después de que el usuario deje de escribir
    })
  })
})

// Función para previsualizar imagen
function previsualizarImagen(input, contenedorId) {
  const contenedor = document.getElementById(contenedorId)
  const archivo = input.files[0]

  if (archivo) {
    const lector = new FileReader()
    lector.onload = function(e) {
      contenedor.innerHTML = `<img src="${e.target.result}" style="max-width: 200px; max-height: 200px; border-radius: 0.5rem;">`
    }
    lector.readAsDataURL(archivo)
  }
}