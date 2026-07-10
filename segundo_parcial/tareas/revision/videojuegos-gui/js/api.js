async function handleResponse(res) {
  if (!res.ok) {
    let mensaje = `Error ${res.status}`;
    try {
      const data = await res.json();
      mensaje = data.message || data.error || mensaje;
    } catch (_) {
      /* el cuerpo no era JSON, se usa el mensaje por defecto */
    }
    throw new Error(mensaje);
  }
  if (res.status === 204) return null;
  return res.json();
}

// Construye un FormData a partir de un objeto plano. Los valores
// numéricos se agregan como strings (el backend los convierte).
function objetoAFormData(datos, imgFile) {
  const fd = new FormData();
  Object.entries(datos).forEach(([key, val]) => {
    if (val === undefined || val === null) return;
    fd.append(key, String(val));
  });
  if (imgFile) fd.append("imagen", imgFile);
  return fd;
}

const Api = {
  // - Videojuegos -

  // filtros = (texto, genero, plataforma )
  async listarVideojuegos(filtros = {}) {
    const params = new URLSearchParams();
    if (filtros.texto) params.append("titulo", filtros.texto);
    if (filtros.genero) params.append("genero", filtros.genero);
    if (filtros.plataforma) params.append("plataforma", filtros.plataforma);
    const qs = params.toString();
    const url = qs ? `${ENDPOINTS.videojuegos}?${qs}` : ENDPOINTS.videojuegos;
    const res = await fetch(url);
    return handleResponse(res);
  },

  async obtenerVideojuego(id) {
    const res = await fetch(ENDPOINTS.videojuego(id));
    return handleResponse(res);
  },

  // Crea un videojuego. Si imgFile viene definido, se envía como json 
  async crearVideojuego(datos, imgFile) {
    let opciones;
    if (imgFile) {
      opciones = { method: "POST", body: objetoAFormData(datos, imgFile) };
    } else {
      opciones = {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(datos),
      };
    }
    const res = await fetch(ENDPOINTS.videojuegos, opciones);
    return handleResponse(res);
  },

  // Actualización completa (put)
  async actualizarVideojuego(id, datos, imgFile) {
    let opciones;
    if (imgFile) {
      opciones = { method: "PUT", body: objetoAFormData(datos, imgFile) };
    } else {
      opciones = {
        method: "PUT",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(datos),
      };
    }
    const res = await fetch(ENDPOINTS.videojuego(id), opciones);
    return handleResponse(res);
  },

  // Edición rápida (patch)
  async edicionRapida(id, { precio, calificacion }) {
    const res = await fetch(ENDPOINTS.videojuego(id), {
      method: "PATCH",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ precio, calificacion }),
    });
    return handleResponse(res);
  },

  async eliminarVideojuego(id) {
    const res = await fetch(ENDPOINTS.videojuego(id), { method: "DELETE" });
    return handleResponse(res);
  },

  // -- imagen -
  // Sube una imagen por separado
  async subirImagen(file) {
    const fd = new FormData();
    fd.append("imagen", file);
    const res = await fetch(ENDPOINTS.imagenes, { method: "POST", body: fd });
    const data = await handleResponse(res);
    return data.nombre;
  },

  // - generos - 
  async listarGeneros() {
    const res = await fetch(ENDPOINTS.generos);
    return handleResponse(res);
  },

  // - plataformas - 
  async listarPlataformas() {
    const res = await fetch(ENDPOINTS.plataformas);
    return handleResponse(res);
  },
};
