function actualizarContador() {
    const pendientes = document.querySelectorAll('.tarea-item:not(.completada)').length;
    document.getElementById('contador').innerText = pendientes + " tareas pendientes";
}

function agregar() {
    const texto = document.getElementById('nuevaTarea').value;
    if (!texto) return;

    const lista = document.getElementById('lista');
    const li = document.createElement('li');
    li.className = 'tarea-item';
    li.innerHTML = `${texto} <button onclick="this.parentElement.remove(); actualizarContador();">Eliminar</button>`;
    
    li.onclick = function() {
        this.classList.toggle('completada');
        actualizarContador();
    };

    lista.appendChild(li);
    document.getElementById('nuevaTarea').value = "";
    actualizarContador();
}

function filtrar(tipo) {
    const tareas = document.querySelectorAll('.tarea-item');
    tareas.forEach(t => {
        if (tipo === 'todas') t.style.display = 'block';
        else if (tipo === 'pendientes') t.style.display = t.classList.contains('completada') ? 'none' : 'block';
        else if (tipo === 'completadas') t.style.display = t.classList.contains('completada') ? 'block' : 'none';
    });
}

actualizarContador();