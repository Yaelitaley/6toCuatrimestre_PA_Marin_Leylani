const API = 'https://fakestoreapi.com'; 
/* guarda la URL de la api , para no repetirla en cada peticion */
let allProducts = []; /* guarda todos los productos*/
let cartCount   = 0; /* lleva la cuenta del carrito */
let currentCat  = 'all'; /* recuerda que categoria esta seleccionada */

const grid         = document.getElementById('products-grid');
const catBox       = document.getElementById('categories');
const searchBar    = document.getElementById('search-bar');
const countLabel   = document.getElementById('count-label');
const sectionTitle = document.getElementById('section-title');
const cartBadge    = document.getElementById('cart-count');
const toast        = document.getElementById('toast');
const toastName    = document.getElementById('toast-name');


/*Limpia el grid y crea 8 cajitas grises animadas 
mientras carga la API. n = 8 es el valor por defecto 
si no mando ningún número.*/
function showSkeletons(n = 8) {
  grid.innerHTML = '';
  for (let i = 0; i < n; i++) {
    const sk = document.createElement('div');
    sk.className = 'skeleton';
    grid.appendChild(sk);
  }
}

function buildStars(rate) {
  const full = Math.round(rate);
  let s = '';
  for (let i = 1; i <= 5; i++) {
    s += i <= full ? '★' : '☆';
  }
  return s;
}

/* limpia el grid antes de meter tarjetas nuevas , para no duplicarlas*/
function renderProducts(products) {
  grid.innerHTML = '';
  countLabel.textContent = `${products.length} producto${products.length !== 1 ? 's' : ''}`;
/* si no hay productos que mostrar que mostrar envia un mensaje de return */
  if (products.length === 0) {
    grid.innerHTML = '<div class="error-msg"><strong>🔍</strong>Sin resultados para tu búsqueda.</div>';
    return;
  }

  /* por cada producto crea un div ,con la informaciony los datos y lo agrega al grid */
  products.forEach(p => {
    const wrap = document.createElement('div');
    wrap.className = 'card-wrap';

    wrap.innerHTML = `
      <div class="card-inner">
        <div class="card-front">
          <div class="card-img-box">
            <img src="${p.image}" alt="${p.title}" loading="lazy" />
          </div>
          <div class="card-category">${p.category}</div>
          <div class="card-title">${p.title}</div>
          <div class="card-stars">${buildStars(p.rating.rate)} <small style="display:inline;color:
          #999;font-size:.7rem">(${p.rating.count})</small></div>
        </div>
        <div class="card-back">
          <div class="back-price">$${p.price.toFixed(2)}<small>USD</small></div>
          <div class="back-desc">${p.description}</div>
          <button class="btn-add" data-id="${p.id}" data-name="${p.title}">+ Agregar al carrito</button>
        </div>
      </div>
    `;
    grid.appendChild(wrap);
  });

  // Listener de carrito
  grid.querySelectorAll('.btn-add').forEach(btn => {
    btn.addEventListener('click', (e) => {
      e.stopPropagation();
      cartCount++;
      cartBadge.textContent = cartCount;
       /* al hacer click en "agregar" el ".stopPropagation" evita que se modifique 
      los demas elementos y aumenta el numero del carrito */
      const name = btn.dataset.name;
      /* si el nombre es muy largo , lo corta a 30 caracteres , 
      muestra el aviso agregando a la clase "show" 
      y lo oculta en 2.5 segundos */
      toastName.textContent = name.length > 30 ? name.slice(0, 30) + '…' : name;
      toast.classList.add('show');
      setTimeout(() => toast.classList.remove('show'), 2500);
    });
  });
}

/* lee el texto del buscador en minisculas y sin espacios */
function applyFilters() {
  const q = searchBar.value.toLowerCase().trim();
  let filtered = allProducts;

  /* si hay una categoria solo deja los prodcutos de esa categoria */
  if (currentCat !== 'all') {
    filtered = filtered.filter(p => p.category === currentCat);
  }
  if (q) {
    filtered = filtered.filter(p => p.title.toLowerCase().includes(q));
  }

  sectionTitle.textContent = currentCat === 'all' ? 'Todos los productos' : capitalize(currentCat);
  renderProducts(filtered);
}

function capitalize(s) {
  return s.charAt(0).toUpperCase() + s.slice(1);
}

//  Cargar categorías 
async function loadCategories() { /* indica que la funcion hace cosas que toman tiempo */
  const res  = await fetch(`${API}/products/categories`); /* hace la peticion y espera la respuesta */
  const cats = await res.json(); /* convierte la respuesta en un array js */

  const allBtn = document.createElement('button');
  allBtn.className = 'cat-btn active';
  allBtn.textContent = 'Todos';
  allBtn.dataset.cat = 'all';
  catBox.appendChild(allBtn);

  cats.forEach(cat => {
    const btn = document.createElement('button');
    btn.className = 'cat-btn';
    btn.textContent = cat;
    btn.dataset.cat = cat;
    catBox.appendChild(btn);
  });

  catBox.addEventListener('click', e => {
    const btn = e.target.closest('.cat-btn');
    if (!btn) return;
    catBox.querySelectorAll('.cat-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    currentCat = btn.dataset.cat;
    applyFilters();
  });
}

// Cargar productos 
async function loadProducts() {
  showSkeletons(8);
  const res   = await fetch(`${API}/products`);
  allProducts = await res.json();
  applyFilters();
}


let debounceTimer;
searchBar.addEventListener('input', () => {
  clearTimeout(debounceTimer);
  debounceTimer = setTimeout(applyFilters, 300);
});

/*try/catch atrapa cualquier error de red y muestra 
 un mensaje en vez de romper la página.*/
(async () => {
  try {
    await loadCategories();
    await loadProducts();
  } catch (err) {
    grid.innerHTML = '<div class="error-msg"><strong>⚠️</strong>Error al cargar los productos. Verifica tu conexión.</div>';
  }
})();
