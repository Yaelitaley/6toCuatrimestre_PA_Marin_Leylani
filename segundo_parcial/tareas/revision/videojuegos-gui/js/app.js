
// - estado -
const state = {
  videojuegos: [],
  generos: [],
  plataformas: [],
  filtros: { texto: "", genero: "", plataforma: "" },
  editandoId: null,      // id del videojuego en edición completa (null = creando)
  quickEditId: null,     // id del videojuego en edición rápida
  deleteId: null,        // id del videojuego a eliminar
};


// - navegacion entre vistas -
document.querySelectorAll(".tab-btn").forEach((btn) => {
  btn.addEventListener("click", () => cambiarVista(btn.dataset.view));
});

function cambiarVista(nombre) {
  document.querySelectorAll(".tab-btn").forEach((b) => b.classList.toggle("active", b.dataset.view === nombre));
  document.querySelectorAll(".view").forEach((v) => v.classList.toggle("active", v.id === `view-${nombre}`));
}


// retroalimentación visual
function toast(mensaje, tipo = "success", duracionMs = 3200) {
  const stack = document.getElementById("toast-stack");
  const el = document.createElement("div");
  el.className = `toast ${tipo}`;
  el.innerHTML = `<span class="toast-dot"></span><span>${mensaje}</span>`;
  stack.appendChild(el);
  if (tipo !== "loading") {
    setTimeout(() => el.remove(), duracionMs);
  }
  return el;
}


// carga inicial de los datos (videojuegos, generos, plataformas)

async function cargarTodo() {
  await Promise.all([cargarVideojuegos(), cargarGeneros(), cargarPlataformas()]);
}

async function cargarVideojuegos() {
  const t = toast("Cargando videojuegos…", "loading");
  try {
    // Los filtros (título, género, plataforma) se mandan al backend
    state.videojuegos = await Api.listarVideojuegos(state.filtros);
    renderVideojuegos();
  } catch (err) {
    state.videojuegos = [];
    renderVideojuegos(); // deja la tabla vacía en lugar de romperse
    toast(`No se pudieron cargar los videojuegos: ${err.message}`, "error", 5000);
  } finally {
    t.remove();
  }
}

async function cargarGeneros() {
  try {
    state.generos = await Api.listarGeneros();
    renderGeneros();
    llenarSelect("select-genero-filtro", state.generos, "Todos los géneros");
    llenarSelect("select[name=id_genero]", state.generos, "Selecciona…", true);
  } catch (err) {
    toast(`No se pudieron cargar los géneros: ${err.message}`, "error", 5000);
  }
}

async function cargarPlataformas() {
  try {
    state.plataformas = await Api.listarPlataformas();
    renderPlataformas();
    llenarSelect("select-plataforma-filtro", state.plataformas, "Todas las plataformas");
    llenarSelect("select[name=id_plataforma]", state.plataformas, "Selecciona…", true);
  } catch (err) {
    toast(`No se pudieron cargar las plataformas: ${err.message}`, "error", 5000);
  }
}

function llenarSelect(selectorOrId, items, placeholder, isQuerySelector = false) {
  const select = isQuerySelector ? document.querySelector(selectorOrId) : document.getElementById(selectorOrId);
  if (!select) return;
  select.innerHTML = `<option value="">${placeholder}</option>`;
  items.forEach((item) => {
    const opt = document.createElement("option");
    opt.value = item.id;
    opt.textContent = item.nombre;
    select.appendChild(opt);
  });
}

// render videojuegos
function nombrePorId(lista, id) {
  const found = lista.find((x) => String(x.id) === String(id));
  return found ? found.nombre : "—";
}

function formatearFecha(iso) {
  if (!iso) return "—";
  const d = new Date(iso);
  if (isNaN(d)) return iso;
  return d.toLocaleDateString("es-MX", { year: "numeric", month: "short", day: "2-digit" });
}

function formatearPrecio(valor) {
  const n = Number(valor);
  return isNaN(n) ? "—" : `$${n.toFixed(2)}`;
}

function renderVideojuegos() {
  const tbody = document.getElementById("tbody-videojuegos");
  const empty = document.getElementById("empty-videojuegos");
  const lista = state.videojuegos;

  tbody.innerHTML = "";

  if (lista.length === 0) {
    empty.style.display = "block";
    return;
  }
  empty.style.display = "none";

  lista.forEach((v) => {
    const tr = document.createElement("tr");
    const calificacion = Number(v.calificacion) || 0;
    tr.innerHTML = `
      <td data-label="Imagen"><img class="thumb" src="${resolveImagenUrl(v.imagen)}" alt="${escapeHtml(v.titulo)}" onerror="this.src='assets/placeholder.svg'" /></td>
      <td data-label="Título" class="cell-title">${escapeHtml(v.titulo)}</td>
      <td data-label="Descripción" class="cell-desc" title="${escapeHtml(v.descripcion || "")}">${escapeHtml(v.descripcion || "")}</td>
      <td data-label="Precio" class="cell-mono">${formatearPrecio(v.precio)}</td>
      <td data-label="Lanzamiento" class="cell-mono">${formatearFecha(v.lanzamiento)}</td>
      <td data-label="Calificación">
        <span class="rating">${calificacion.toFixed(1)}<span class="rating-bar"><span style="width:${Math.min(100, (calificacion / 9.9) * 100)}%"></span></span></span>
      </td>
      <td data-label="Género"><span class="chip">${escapeHtml(nombrePorId(state.generos, v.id_genero))}</span></td>
      <td data-label="Plataforma"><span class="chip alt">${escapeHtml(nombrePorId(state.plataformas, v.id_plataforma))}</span></td>
      <td data-label="Acciones">
        <div class="actions-cell">
          <button class="btn-icon" title="Editar" data-action="editar" data-id="${v.id}">✎</button>
          <button class="btn-icon" title="Edición rápida" data-action="quick" data-id="${v.id}">⚡</button>
          <button class="btn-icon danger" title="Eliminar" data-action="eliminar" data-id="${v.id}">🗑</button>
        </div>
      </td>
    `;
    tbody.appendChild(tr);
  });
}

function escapeHtml(str) {
  const div = document.createElement("div");
  div.textContent = str ?? "";
  return div.innerHTML;
}

// Delegación de eventos para los botones de acción de cada fila
document.getElementById("tbody-videojuegos").addEventListener("click", (e) => {
  const btn = e.target.closest("button[data-action]");
  if (!btn) return;
  const id = btn.dataset.id;
  const accion = btn.dataset.action;
  if (accion === "editar") abrirModalEdicionCompleta(id);
  if (accion === "quick") abrirModalEdicionRapida(id);
  if (accion === "eliminar") abrirModalEliminar(id);
});

// render de generos y plataformas, sin edicion y sin modificacion 
function renderGeneros() {
  const tbody = document.getElementById("tbody-generos");
  const empty = document.getElementById("empty-generos");
  tbody.innerHTML = "";
  if (state.generos.length === 0) { empty.style.display = "block"; return; }
  empty.style.display = "none";
  state.generos.forEach((g) => {
    tbody.innerHTML += `<tr><td data-label="ID">${g.id}</td><td data-label="Nombre">${escapeHtml(g.nombre)}</td></tr>`;
  });
}

function renderPlataformas() {
  const tbody = document.getElementById("tbody-plataformas");
  const empty = document.getElementById("empty-plataformas");
  tbody.innerHTML = "";
  if (state.plataformas.length === 0) { empty.style.display = "block"; return; }
  empty.style.display = "none";
  state.plataformas.forEach((p) => {
    tbody.innerHTML += `<tr><td data-label="ID">${p.id}</td><td data-label="Nombre">${escapeHtml(p.nombre)}</td></tr>`;
  });
}

// BÚSQUEDA Y FILTROS
let debounceBusqueda;
document.getElementById("input-buscar").addEventListener("input", (e) => {
  state.filtros.texto = e.target.value.trim();
  clearTimeout(debounceBusqueda);
  debounceBusqueda = setTimeout(() => cargarVideojuegos(), 350);
});
// filtros de genero y plataforma 
document.getElementById("select-genero-filtro").addEventListener("change", (e) => {
  state.filtros.genero = e.target.value;
  cargarVideojuegos();
});
document.getElementById("select-plataforma-filtro").addEventListener("change", (e) => {
  state.filtros.plataforma = e.target.value;
  cargarVideojuegos();
});

// MODALES: abrir / cerrar 
function abrirModal(id) { document.getElementById(id).classList.add("active"); }
function cerrarModal(id) { document.getElementById(id).classList.remove("active"); }

// cerrar modales con botones de cierre 
document.querySelectorAll("[data-close]").forEach((el) => {
  el.addEventListener("click", () => cerrarModal(el.dataset.close));
});
document.querySelectorAll(".modal-overlay").forEach((overlay) => {
  overlay.addEventListener("click", (e) => {
    if (e.target === overlay) cerrarModal(overlay.id);
  });
});
document.addEventListener("keydown", (e) => {
  if (e.key === "Escape") document.querySelectorAll(".modal-overlay.active").forEach((m) => cerrarModal(m.id));
});

//  formulario para crear y editar un videojuego (edicion completa) , no rspida 
const formVideojuego = document.getElementById("form-videojuego");
const inputImagen = formVideojuego.querySelector("input[name=imagen]");
const previewImagen = document.getElementById("preview-imagen");
const labelImagen = document.getElementById("label-imagen");

inputImagen.addEventListener("change", () => {
  const file = inputImagen.files[0];
  if (!file) return;
  previewImagen.src = URL.createObjectURL(file);
  labelImagen.textContent = file.name;
});

// abrir el modal , pantalla para crear un nuevo videojuego 
document.getElementById("btn-nuevo-videojuego").addEventListener("click", () => {
  state.editandoId = null;
  formVideojuego.reset();
  previewImagen.src = "assets/placeholder.svg";
  labelImagen.textContent = "Haz clic para elegir un archivo…";
  limpiarErrores(formVideojuego);
  document.getElementById("modal-form-title").textContent = "Nuevo videojuego";
  abrirModal("modal-form");
});

// abrir el modal para editar un videojuego 
function abrirModalEdicionCompleta(id) {
  const v = state.videojuegos.find((x) => String(x.id) === String(id));
  if (!v) return;
  state.editandoId = id;
  limpiarErrores(formVideojuego);
  document.getElementById("modal-form-title").textContent = "Editar videojuego";
  formVideojuego.titulo.value = v.titulo || "";
  formVideojuego.descripcion.value = v.descripcion || "";
  formVideojuego.precio.value = v.precio ?? "";
  formVideojuego.lanzamiento.value = (v.lanzamiento || "").slice(0, 10);
  formVideojuego.calificacion.value = v.calificacion ?? "";
  formVideojuego.id_genero.value = v.id_genero ?? "";
  formVideojuego.id_plataforma.value = v.id_plataforma ?? "";
  previewImagen.src = resolveImagenUrl(v.imagen);
  labelImagen.textContent = v.imagen ? "Cambiar imagen (opcional)" : "Haz clic para elegir un archivo…";
  inputImagen.value = "";
  abrirModal("modal-form");
}

function limpiarErrores(form) {
  form.querySelectorAll(".form-field").forEach((f) => {
    f.classList.remove("invalid");
    const err = f.querySelector(".field-error");
    if (err) err.textContent = "";
  });
}
// marca un campo invalido y muestra un mensaje de error 
function marcarError(fieldId, mensaje) {
  const field = document.getElementById(fieldId);
  field.classList.add("invalid");
  field.querySelector(".field-error").textContent = mensaje;
}

// valida los datos del formulario del videojuego , regresa V (true) si es valido 
function validarVideojuego(data) {
  limpiarErrores(formVideojuego);
  let valido = true;
  if (!data.titulo.trim()) { marcarError("field-titulo", "El título es obligatorio."); valido = false; }
  if (!data.descripcion.trim()) { marcarError("field-descripcion", "La descripción es obligatoria."); valido = false; }
  if (data.precio === "" || Number(data.precio) < 0) { marcarError("field-precio", "Indica un precio válido."); valido = false; }
  if (!data.lanzamiento) { marcarError("field-lanzamiento", "La fecha es obligatoria."); valido = false; }
  if (data.calificacion === "" || Number(data.calificacion) < 0 || Number(data.calificacion) > 9.9) {
    marcarError("field-calificacion", "Debe estar entre 0 y 9.9."); valido = false;
  }
  if (!data.id_genero) { marcarError("field-genero", "Selecciona un género."); valido = false; }
  if (!data.id_plataforma) { marcarError("field-plataforma", "Selecciona una plataforma."); valido = false; }
  return valido;
}

formVideojuego.addEventListener("submit", async (e) => {
  e.preventDefault();

  // Valores tal cual están en el form , usados para validar.
  const crudos = {
    titulo: formVideojuego.titulo.value,
    descripcion: formVideojuego.descripcion.value,
    precio: formVideojuego.precio.value,
    lanzamiento: formVideojuego.lanzamiento.value,
    calificacion: formVideojuego.calificacion.value,
    id_genero: formVideojuego.id_genero.value,
    id_plataforma: formVideojuego.id_plataforma.value,
  };
  if (!validarVideojuego(crudos)) return;

  // Valores ya convertidos a número, listos para enviar al backend.
  const datos = {
    ...crudos,
    precio: Number(crudos.precio),
    calificacion: Number(crudos.calificacion),
    id_genero: Number(crudos.id_genero),
    id_plataforma: Number(crudos.id_plataforma),
  };
  const esCreacion = state.editandoId === null;
  const imgFile = inputImagen.files[0] || null; // null es si no se selecciono una imagen nueva 

  const btn = document.getElementById("btn-guardar-videojuego");
  btn.disabled = true;
  const t = toast(esCreacion ? "Guardando videojuego…" : "Guardando cambios…", "loading");

  try {
    if (esCreacion) {
      // si no, se manda como JSON (ver api.js -> crearVideojuego).
      await Api.crearVideojuego(datos, imgFile);
      toast("Videojuego creado con éxito.", "success");
    } else {
      await Api.actualizarVideojuego(state.editandoId, datos, imgFile);
      toast("Videojuego actualizado con éxito.", "success");
    }
    cerrarModal("modal-form");
    await cargarVideojuegos();
  } catch (err) {
    toast(`No se pudo guardar: ${err.message}`, "error", 5000);
  } finally {
    btn.disabled = false;
    t.remove();
  }
});


// EDICIÓN RÁPIDA (precio y calificación)
const formQuick = document.getElementById("form-quick-edit");

function abrirModalEdicionRapida(id) {
  const v = state.videojuegos.find((x) => String(x.id) === String(id));
  if (!v) return;
  state.quickEditId = id;
  limpiarErrores(formQuick);
  document.getElementById("quick-edit-target").textContent = v.titulo;
  formQuick.precio.value = v.precio ?? "";
  formQuick.calificacion.value = v.calificacion ?? "";
  abrirModal("modal-quick");
}

formQuick.addEventListener("submit", async (e) => {
  e.preventDefault();
  limpiarErrores(formQuick);
  const precio = formQuick.precio.value;
  const calificacion = formQuick.calificacion.value;
  let valido = true;
  if (precio === "" || Number(precio) < 0) { marcarError("field-quick-precio", "Indica un precio válido."); valido = false; }
  if (calificacion === "" || Number(calificacion) < 0 || Number(calificacion) > 9.9) {
    marcarError("field-quick-calificacion", "Debe estar entre 0 y 9.9."); valido = false;
  }
  if (!valido) return;

  const t = toast("Guardando cambios…", "loading");
  try {
    await Api.edicionRapida(state.quickEditId, { precio: Number(precio), calificacion: Number(calificacion) });
    toast("Cambios guardados.", "success");
    cerrarModal("modal-quick");
    await cargarVideojuegos();
  } catch (err) {
    toast(`No se pudo actualizar: ${err.message}`, "error", 5000);
  } finally {
    t.remove();
  }
});

// eliminar un videojuego , abrir la pantallita de confirmacion 
function abrirModalEliminar(id) {
  const v = state.videojuegos.find((x) => String(x.id) === String(id));
  if (!v) return;
  state.deleteId = id;
  document.getElementById("delete-target-title").textContent = v.titulo;
  abrirModal("modal-delete");
}

document.getElementById("btn-confirmar-eliminar").addEventListener("click", async () => {
  const btn = document.getElementById("btn-confirmar-eliminar");
  btn.disabled = true;
  const t = toast("Eliminando…", "loading");
  try {
    await Api.eliminarVideojuego(state.deleteId);
    toast("Videojuego eliminado.", "success");
    cerrarModal("modal-delete");
    await cargarVideojuegos();
  } catch (err) {
    toast(`No se pudo eliminar: ${err.message}`, "error", 5000);
  } finally {
    btn.disabled = false;
    t.remove();
  }
});

cargarTodo();
