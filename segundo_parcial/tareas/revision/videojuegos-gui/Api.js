async function handleResponse(res) {
  if (!res.ok) {
    let mensaje = `Error ${res.status}`;
    try {
      const data = await res.json();
      mensaje = data.message || data.error || mensaje;
    } catch (_) {
    }
    throw new Error(mensaje);
  }
  if (res.status === 204) return null;
  return res.json();
}

const Api = {
  //  Videojuegos 
  async listarVideojuegos() {
    const res = await fetch(ENDPOINTS.videojuegos);
    return handleResponse(res);
  },

  async crearVideojuego(formData) {
    const res = await fetch(ENDPOINTS.videojuegos, {
      method: "POST",
      body: formData, 
    });
    return handleResponse(res);
  },

  async actualizarVideojuego(id, formData) {
    const res = await fetch(ENDPOINTS.videojuego(id), {
      method: "PUT",
      body: formData,
    });
    return handleResponse(res);
  },

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

  //  Generos
  async listarGeneros() {
    const res = await fetch(ENDPOINTS.generos);
    return handleResponse(res);
  },

  //  Plataformas
  async listarPlataformas() {
    const res = await fetch(ENDPOINTS.plataformas);
    return handleResponse(res);
  },
};