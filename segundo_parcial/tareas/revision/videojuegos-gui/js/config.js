const API_URL = "http://192.168.1.140/videojuegos_app/";
// esta es la url que debo cmabiar , si cambia la ip del profe , que pasa cada que se desconecta del internet 

const ENDPOINTS = {
  videojuegos: `${API_URL}api-videojuego.php`,
  videojuego: (id) => `${API_URL}api-videojuego.php?id=${encodeURIComponent(id)}`,
  imagenes: `${API_URL}api-imagen.php`,
  generos: `${API_URL}api-genero.php`,
  plataformas: `${API_URL}api-plataforma.php`,
};

// Construye la URL completa de una imagen a partir del nombre que
function resolveImagenUrl(nombreArchivo) {
  if (!nombreArchivo) return "assets/placeholder.svg";
  if (/^https?:\/\//i.test(nombreArchivo)) return nombreArchivo;
  return `${ENDPOINTS.imagenes}?nombre=${encodeURIComponent(nombreArchivo)}`;
}
