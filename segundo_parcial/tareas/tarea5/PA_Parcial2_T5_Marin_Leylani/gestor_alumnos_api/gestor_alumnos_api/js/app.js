const API_BASE = "api";


let carreras = [];
let cuatrimestres = [];

async function apiFetch(url, options = {}) {
    try {
        const respuesta = await fetch(url, {
            headers: { "Content-Type": "application/json" },
            ...options
        });
        const json = await respuesta.json();
        if (!respuesta.ok || json.success === false) {
            throw new Error(json.message || "Error en la petición.");
        }
        return json;
    } catch (error) {
        mostrarToast(error.message || "Error de red.", "error");
        throw error;
    }
}

function mostrarToast(mensaje, tipo = "exito") {
    const toast = document.getElementById("toast");
    toast.textContent = mensaje;
    toast.className = "toast mostrar " + tipo;
    setTimeout(() => {
        toast.classList.remove("mostrar");
    }, 3000);
}

function abrirModal(id) {
    document.getElementById(id).classList.add("abierto");
}

function cerrarModal(id) {
    document.getElementById(id).classList.remove("abierto");
}

document.querySelectorAll("[data-close]").forEach(el => {
    el.addEventListener("click", () => cerrarModal(el.dataset.close));
});

document.querySelectorAll(".modal").forEach(modal => {
    modal.addEventListener("click", (e) => {
        if (e.target === modal) cerrarModal(modal.id);
    });
});

function badgeEstatus(estatus) {
    const clases = { "Activo": "badge-activo", "Baja": "badge-baja", "Egresado": "badge-egresado" };
    return `<span class="badge ${clases[estatus] || ''}">${estatus}</span>`;
}


document.querySelectorAll(".tab-btn").forEach(btn => {
    btn.addEventListener("click", () => {
        document.querySelectorAll(".tab-btn").forEach(b => b.classList.remove("active"));
        document.querySelectorAll(".tab-content").forEach(c => c.classList.remove("active"));
        btn.classList.add("active");
        document.getElementById(btn.dataset.tab).classList.add("active");
    });
});


async function cargarCatalogos() {
    const [resCarreras, resCuatrimestres] = await Promise.all([
        apiFetch(`${API_BASE}/carreras.php`),
        apiFetch(`${API_BASE}/cuatrimestres.php`)
    ]);

    carreras = resCarreras.data;
    cuatrimestres = resCuatrimestres.data;

    llenarSelect("filtroCarrera", carreras, "Todas las carreras");
    llenarSelect("filtroMateriaCarrera", carreras, "Todas las carreras");
    llenarSelect("filtroMateriaCuatrimestre", cuatrimestres, "Todos los cuatrimestres");
    llenarSelect("alumnoCarrera", carreras, "Selecciona...");
    llenarSelect("alumnoCuatrimestre", cuatrimestres, "Selecciona...");
    llenarSelect("cambioCuatrimestre", cuatrimestres, "Selecciona...");
}

function llenarSelect(idSelect, items, textoPorDefecto) {
    const select = document.getElementById(idSelect);
    const valorActual = select.value;
    select.innerHTML = `<option value="">${textoPorDefecto}</option>`;
    items.forEach(item => {
        const option = document.createElement("option");
        option.value = item.id;
        option.textContent = item.nombre;
        select.appendChild(option);
    });
    select.value = valorActual;
}


async function cargarAlumnos(params = {}) {
    const cuerpo = document.getElementById("cuerpoTablaAlumnos");
    cuerpo.innerHTML = `<tr><td colspan="7" class="vacio">Cargando alumnos...</td></tr>`;

    const query = new URLSearchParams(params).toString();
    const resultado = await apiFetch(`${API_BASE}/alumnos.php${query ? "?" + query : ""}`);
    const alumnos = resultado.data;

    if (!alumnos.length) {
        cuerpo.innerHTML = `<tr><td colspan="7" class="vacio">No se encontraron alumnos.</td></tr>`;
        return;
    }

    cuerpo.innerHTML = "";
    alumnos.forEach(a => {
        const nombreCompleto = `${a.nombre} ${a.apellido_paterno} ${a.apellido_materno || ""}`.trim();
        const fila = document.createElement("tr");
        fila.innerHTML = `
            <td>${a.matricula}</td>
            <td>${nombreCompleto}</td>
            <td>${a.correo}</td>
            <td>${a.carrera}</td>
            <td>${a.cuatrimestre}</td>
            <td>${badgeEstatus(a.estatus)}</td>
            <td>
                <button class="btn btn-secondary btn-small" onclick="editarAlumno(${a.id})">Editar</button>
                <button class="btn btn-primary btn-small" onclick="abrirCambioRapido(${a.id}, '${a.estatus}', ${a.cuatrimestre_actual})">Estatus</button>
                <button class="btn btn-danger btn-small" onclick="eliminarAlumno(${a.id})">Eliminar</button>
            </td>
        `;
        cuerpo.appendChild(fila);
    });

    llenarSelectAlumnos(alumnos);
}

function llenarSelectAlumnos(alumnos) {
    const selects = ["selectAlumnoInscripcion", "inscripcionAlumno"];
    selects.forEach(idSelect => {
        const select = document.getElementById(idSelect);
        const valorActual = select.value;
        select.innerHTML = `<option value="">Selecciona un alumno...</option>`;
        alumnos.forEach(a => {
            const option = document.createElement("option");
            option.value = a.id;
            option.textContent = `${a.matricula} - ${a.nombre} ${a.apellido_paterno}`;
            select.appendChild(option);
        });
        select.value = valorActual;
    });
}

document.getElementById("btnBuscarAlumnos").addEventListener("click", () => {
    cargarAlumnos({
        nombre: document.getElementById("filtroNombre").value.trim(),
        matricula: document.getElementById("filtroMatricula").value.trim(),
        id_carrera: document.getElementById("filtroCarrera").value
    });
});

document.getElementById("btnLimpiarFiltros").addEventListener("click", () => {
    document.getElementById("filtroNombre").value = "";
    document.getElementById("filtroMatricula").value = "";
    document.getElementById("filtroCarrera").value = "";
    cargarAlumnos();
});

document.getElementById("btnNuevoAlumno").addEventListener("click", () => {
    document.getElementById("formAlumno").reset();
    document.getElementById("alumnoId").value = "";
    document.getElementById("tituloModalAlumno").textContent = "Nuevo alumno";
    abrirModal("modalAlumno");
});

async function editarAlumno(id) {
    const resultado = await apiFetch(`${API_BASE}/alumnos.php?id=${id}`);
    const a = resultado.data;

    document.getElementById("tituloModalAlumno").textContent = "Editar alumno";
    document.getElementById("alumnoId").value = a.id;
    document.getElementById("alumnoMatricula").value = a.matricula;
    document.getElementById("alumnoNombre").value = a.nombre;
    document.getElementById("alumnoApellidoPaterno").value = a.apellido_paterno;
    document.getElementById("alumnoApellidoMaterno").value = a.apellido_materno || "";
    document.getElementById("alumnoCorreo").value = a.correo;
    document.getElementById("alumnoTelefono").value = a.telefono || "";
    document.getElementById("alumnoFechaNacimiento").value = a.fecha_nacimiento || "";
    document.getElementById("alumnoCarrera").value = a.id_carrera;
    document.getElementById("alumnoCuatrimestre").value = a.cuatrimestre_actual;
    document.getElementById("alumnoEstatus").value = a.estatus;

    abrirModal("modalAlumno");
}

document.getElementById("formAlumno").addEventListener("submit", async (e) => {
    e.preventDefault();

    const id = document.getElementById("alumnoId").value;
    const datos = {
        matricula: document.getElementById("alumnoMatricula").value.trim(),
        nombre: document.getElementById("alumnoNombre").value.trim(),
        apellido_paterno: document.getElementById("alumnoApellidoPaterno").value.trim(),
        apellido_materno: document.getElementById("alumnoApellidoMaterno").value.trim(),
        correo: document.getElementById("alumnoCorreo").value.trim(),
        telefono: document.getElementById("alumnoTelefono").value.trim(),
        fecha_nacimiento: document.getElementById("alumnoFechaNacimiento").value,
        id_carrera: document.getElementById("alumnoCarrera").value,
        cuatrimestre_actual: document.getElementById("alumnoCuatrimestre").value,
        estatus: document.getElementById("alumnoEstatus").value
    };

    try {
        if (id) {
            await apiFetch(`${API_BASE}/alumnos.php?id=${id}`, {
                method: "PUT",
                body: JSON.stringify(datos)
            });
            mostrarToast("Alumno actualizado correctamente.");
        } else {
            await apiFetch(`${API_BASE}/alumnos.php`, {
                method: "POST",
                body: JSON.stringify(datos)
            });
            mostrarToast("Alumno registrado correctamente.");
        }
        cerrarModal("modalAlumno");
        cargarAlumnos();
    } catch (e) {
     
    }
});


function abrirCambioRapido(id, estatusActual, cuatrimestreActual) {
    document.getElementById("cambioAlumnoId").value = id;
    document.getElementById("cambioEstatus").value = estatusActual;
    document.getElementById("cambioCuatrimestre").value = cuatrimestreActual;
    abrirModal("modalCambioRapido");
}

document.getElementById("formCambioRapido").addEventListener("submit", async (e) => {
    e.preventDefault();
    const id = document.getElementById("cambioAlumnoId").value;

    const datos = {
        estatus: document.getElementById("cambioEstatus").value,
        cuatrimestre_actual: document.getElementById("cambioCuatrimestre").value
    };

    try {
        await apiFetch(`${API_BASE}/alumnos.php?id=${id}`, {
            method: "PATCH",
            body: JSON.stringify(datos)
        });
        mostrarToast("Alumno actualizado correctamente.");
        cerrarModal("modalCambioRapido");
        cargarAlumnos();
    } catch (e) {}
});


async function eliminarAlumno(id) {
    if (!confirm("¿Seguro que deseas eliminar este alumno? Esta acción también eliminará sus inscripciones.")) return;

    try {
        await apiFetch(`${API_BASE}/alumnos.php?id=${id}`, { method: "DELETE" });
        mostrarToast("Alumno eliminado correctamente.");
        cargarAlumnos();
    } catch (e) {}
}


async function cargarMaterias(params = {}) {
    const cuerpo = document.getElementById("cuerpoTablaMaterias");
    cuerpo.innerHTML = `<tr><td colspan="4" class="vacio">Cargando materias...</td></tr>`;

    const query = new URLSearchParams(params).toString();
    const resultado = await apiFetch(`${API_BASE}/materias.php${query ? "?" + query : ""}`);
    const materias = resultado.data;

    if (!materias.length) {
        cuerpo.innerHTML = `<tr><td colspan="4" class="vacio">No se encontraron materias.</td></tr>`;
        return;
    }

    cuerpo.innerHTML = "";
    materias.forEach(m => {
        const fila = document.createElement("tr");
        fila.innerHTML = `
            <td>${m.nombre}</td>
            <td>${m.creditos}</td>
            <td>${m.carrera}</td>
            <td>${m.cuatrimestre}</td>
        `;
        cuerpo.appendChild(fila);
    });

    llenarSelectMaterias(materias);
}

function llenarSelectMaterias(materias) {
    const select = document.getElementById("inscripcionMateria");
    const valorActual = select.value;
    select.innerHTML = `<option value="">Selecciona una materia...</option>`;
    materias.forEach(m => {
        const option = document.createElement("option");
        option.value = m.id;
        option.textContent = `${m.nombre} (${m.carrera} - ${m.cuatrimestre})`;
        select.appendChild(option);
    });
    select.value = valorActual;
}

document.getElementById("btnBuscarMaterias").addEventListener("click", () => {
    cargarMaterias({
        nombre: document.getElementById("filtroMateriaNombre").value.trim(),
        id_carrera: document.getElementById("filtroMateriaCarrera").value,
        id_cuatrimestre: document.getElementById("filtroMateriaCuatrimestre").value
    });
});

document.getElementById("btnLimpiarFiltrosMaterias").addEventListener("click", () => {
    document.getElementById("filtroMateriaNombre").value = "";
    document.getElementById("filtroMateriaCarrera").value = "";
    document.getElementById("filtroMateriaCuatrimestre").value = "";
    cargarMaterias();
});


async function cargarInscripcionesAlumno(idAlumno) {
    const cuerpo = document.getElementById("cuerpoTablaInscripciones");
    cuerpo.innerHTML = `<tr><td colspan="6" class="vacio">Cargando inscripciones...</td></tr>`;

    const resultado = await apiFetch(`${API_BASE}/inscripciones.php?id_alumno=${idAlumno}`);
    const inscripciones = resultado.data;

    if (!inscripciones.length) {
        cuerpo.innerHTML = `<tr><td colspan="6" class="vacio">Este alumno no tiene materias inscritas.</td></tr>`;
        return;
    }

    cuerpo.innerHTML = "";
    inscripciones.forEach(i => {
        const fila = document.createElement("tr");
        fila.innerHTML = `
            <td>${i.materia}</td>
            <td>${i.creditos}</td>
            <td>${i.periodo || "-"}</td>
            <td>${i.fecha_inscripcion}</td>
            <td>${i.calificacion !== null ? i.calificacion : "-"}</td>
            <td><button class="btn btn-danger btn-small" onclick="eliminarInscripcion(${i.id}, ${idAlumno})">Eliminar</button></td>
        `;
        cuerpo.appendChild(fila);
    });
}

document.getElementById("btnVerInscripciones").addEventListener("click", () => {
    const idAlumno = document.getElementById("selectAlumnoInscripcion").value;
    if (!idAlumno) {
        mostrarToast("Selecciona un alumno primero.", "error");
        return;
    }
    cargarInscripcionesAlumno(idAlumno);
});

document.getElementById("btnNuevaInscripcion").addEventListener("click", () => {
    document.getElementById("formInscripcion").reset();
    const idAlumnoSeleccionado = document.getElementById("selectAlumnoInscripcion").value;
    if (idAlumnoSeleccionado) {
        document.getElementById("inscripcionAlumno").value = idAlumnoSeleccionado;
    }
    abrirModal("modalInscripcion");
});

document.getElementById("formInscripcion").addEventListener("submit", async (e) => {
    e.preventDefault();

    const idAlumno = document.getElementById("inscripcionAlumno").value;
    const datos = {
        id_alumno: idAlumno,
        id_materia: document.getElementById("inscripcionMateria").value,
        fecha_inscripcion: document.getElementById("inscripcionFecha").value,
        periodo: document.getElementById("inscripcionPeriodo").value.trim(),
        calificacion: document.getElementById("inscripcionCalificacion").value || null
    };

    try {
        await apiFetch(`${API_BASE}/inscripciones.php`, {
            method: "POST",
            body: JSON.stringify(datos)
        });
        mostrarToast("Inscripción registrada correctamente.");
        cerrarModal("modalInscripcion");

        document.getElementById("selectAlumnoInscripcion").value = idAlumno;
        cargarInscripcionesAlumno(idAlumno);
    } catch (e) {}
});

async function eliminarInscripcion(id, idAlumno) {
    if (!confirm("¿Seguro que deseas eliminar esta inscripción?")) return;

    try {
        await apiFetch(`${API_BASE}/inscripciones.php?id=${id}`, { method: "DELETE" });
        mostrarToast("Inscripción eliminada correctamente.");
        cargarInscripcionesAlumno(idAlumno);
    } catch (e) {}
}


async function iniciar() {
    await cargarCatalogos();
    await cargarAlumnos();
    await cargarMaterias();
}

iniciar();
