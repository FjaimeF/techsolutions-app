<?php
session_start();
require 'config/database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// Actualizamos el rol desde la BD
$stmt = $pdo->prepare("SELECT rol, nombre FROM usuarios WHERE id = :id");
$stmt->execute(['id' => $_SESSION['user_id']]);
$user = $stmt->fetch();

$_SESSION['user_rol'] = $user['rol'];
$_SESSION['user_nombre'] = $user['nombre'];

// Si es admin, lo sacamos de la tienda
if ($_SESSION['user_rol'] == 'admin' || $_SESSION['user_rol'] == 'subadmin' || $_SESSION['user_rol'] == 'empleado') {
    header("Location: dashboard.php");
    exit();
}

// Contar cuántos productos hay en el carrito
$cantidad_carrito = 0;
if (isset($_SESSION['carrito'])) {
    foreach ($_SESSION['carrito'] as $cantidad) {
        $cantidad_carrito += $cantidad;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tienda - TechSolutions</title>
    <link rel="icon" href="data:,"> 
    <link rel="stylesheet" href="assets/css/styles.css">
    <link rel="stylesheet" href="assets/css/catalogo.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

    <header class="topbar" style="background: var(--primary-color); color: white;">
        <div style="display: flex; align-items: center; gap: 10px;">
            <i class="fa-solid fa-microchip" style="font-size: 24px;"></i>
            <h2 style="font-size: 20px;">TechSolutions</h2>
        </div>
        <div style="display: flex; align-items: center; gap: 20px;">
            <span>Hola, <strong><?= htmlspecialchars($_SESSION['user_nombre']) ?></strong></span>
            
            <a href="carrito.php" style="color: white; text-decoration: none; position: relative; font-size: 20px;" id="cart-link">
                <i class="fa-solid fa-cart-shopping"></i>
                <!-- El contador se actualiza vía JS. Le ponemos un ID y lo ocultamos si es 0 -->
                <span id="cart-count" style="position: absolute; top: -8px; right: -10px; background: var(--danger); color: white; font-size: 12px; padding: 2px 6px; border-radius: 50%; font-weight: bold; display: <?= $cantidad_carrito > 0 ? 'block' : 'none' ?>;">
                    <?= $cantidad_carrito ?>
                </span>
            </a>

            <a href="logout.php" style="color: white; text-decoration: none; font-size: 14px; background: rgba(255,255,255,0.2); padding: 8px 15px; border-radius: 5px;">
                <i class="fa-solid fa-arrow-right-from-bracket"></i> Salir
            </a>
        </div>
    </header>

    <div class="content-area">
        <h1 style="margin-bottom: 20px; font-size: 24px;">Catálogo de Productos</h1>

        <div class="catalog-controls">
            <input type="text" id="search-input" placeholder="Buscar producto..." class="search-input">
            <div class="filter-buttons" id="filter-container">
                <button class="active" data-categoria="todas">Todas</button>
                <button data-categoria="Laptops">Laptops</button>
                <button data-categoria="Celulares">Celulares</button>
                <button data-categoria="Audio">Audio</button>
            </div>
        </div>

        <div class="catalog-grid" id="contenedor-productos">
            <p style="text-align:center; grid-column: 1/-1; color: var(--text-muted);">Cargando catálogo...</p>
        </div>
    </div>

    <script>
    let productosGlobales = [];

    // Función para agregar al carrito sin recargar
    function agregarAlCarrito(id, boton) {
        const textoOriginal = boton.innerText;
        boton.innerText = 'Agregando...';
        boton.disabled = true;

        const formData = new FormData();
        formData.append('id', id);

        fetch('agregar_carrito.php', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.status === 'success') {
                // Actualizar el badge del carrito
                const cartCount = document.getElementById('cart-count');
                cartCount.innerText = data.cart_count;
                cartCount.style.display = 'block';

                // Feedback visual en el botón
                boton.innerText = '¡Agregado!';
                boton.style.background = 'var(--success)';
                
                setTimeout(() => {
                    boton.innerText = textoOriginal;
                    boton.style.background = 'var(--primary-color)';
                    boton.disabled = false;
                }, 1500);

                // Forzar actualización de productos para ver stock nuevo
                cargarProductos();
            }
        })
        .catch(err => {
            console.error(err);
            boton.innerText = 'Error';
            boton.disabled = false;
        });
    }

    function cargarProductos() {
        const timestamp = new Date().getTime();
        fetch('api/productos.php?t=' + timestamp)
            .then(res => res.json())
            .then(data => {
                if (Array.isArray(data)) {
                    if (JSON.stringify(data) !== JSON.stringify(productosGlobales)) {
                        productosGlobales = data;
                        renderizarProductos(productosGlobales);
                    }
                }
            })
            .catch(err => console.error("Error cargando productos:", err));
    }

    function renderizarProductos(productos) {
        const contenedor = document.getElementById('contenedor-productos');
        if (!contenedor) return; 

        contenedor.innerHTML = ''; 

        if (productos.length === 0) {
            contenedor.innerHTML = '<p style="text-align:center; grid-column: 1/-1;">No hay productos en el catálogo.</p>';
            return;
        }

        productos.forEach(prod => {
            let badgeClass = 'badge-success';
            let estadoTexto = 'Disponible';
            if (prod.stock == 0) {
                badgeClass = 'badge-danger';
                estadoTexto = 'Agotado';
            } else if (prod.stock <= prod.stock_minimo) {
                badgeClass = 'badge-warning';
                estadoTexto = 'Stock Bajo';
            }

            // Botón con llamada a la función JS
            let botonHTML = '';
            if (prod.stock > 0) {
                botonHTML = `<button onclick="agregarAlCarrito(${prod.id}, this)" class="btn-primary" style="margin-top: 15px; font-size: 14px; padding: 10px; text-align: center; display: block; width: 100%; border: none; cursor: pointer;">Agregar al Carrito</button>`;
            } else {
                botonHTML = `<button class="btn-primary" style="margin-top: 15px; font-size: 14px; padding: 10px; background: #9ca3af; cursor: not-allowed; width: 100%; border: none;" disabled>Sin Stock</button>`;
            }

            const card = document.createElement('div');
            card.className = 'product-card';
            card.setAttribute('data-categoria', prod.categoria);
            card.setAttribute('data-nombre', prod.nombre.toLowerCase());
            card.innerHTML = `
                <div class="product-image">
                    <i class="fa-solid fa-box"></i>
                </div>
                <div class="product-info">
                    <div class="product-header">
                        <h3>${prod.nombre}</h3>
                        <span class="badge ${badgeClass}">${estadoTexto}</span>
                    </div>
                    <p class="product-category">${prod.categoria} - ${prod.codigo}</p>
                    <div class="product-footer">
                        <span class="price">S/ ${parseFloat(prod.precio).toFixed(2)}</span>
                        <span class="stock">Stock: ${prod.stock} und.</span>
                    </div>
                    ${botonHTML}
                </div>
            `;
            contenedor.appendChild(card);
        });
    }

    document.addEventListener("DOMContentLoaded", function() {
        cargarProductos();
        setInterval(cargarProductos, 5000); 
        
        const searchInput = document.getElementById('search-input');
        const filterButtons = document.querySelectorAll('#filter-container button');
        let currentCategory = 'todas';
        let currentSearch = '';

        function aplicarFiltros() {
            const cards = document.querySelectorAll('.product-card');
            cards.forEach(card => {
                const category = card.getAttribute('data-categoria');
                const name = card.getAttribute('data-nombre');
                const matchesCategory = (currentCategory === 'todas' || category === currentCategory);
                const matchesSearch = name.includes(currentSearch);

                if (matchesCategory && matchesSearch) {
                    card.style.display = 'flex';
                } else {
                    card.style.display = 'none';
                }
            });
        }

        if (searchInput) {
            searchInput.addEventListener('input', function(e) {
                currentSearch = e.target.value.toLowerCase();
                aplicarFiltros();
            });
        }

        filterButtons.forEach(button => {
            button.addEventListener('click', function() {
                filterButtons.forEach(btn => btn.classList.remove('active'));
                this.classList.add('active');
                currentCategory = this.getAttribute('data-categoria');
                aplicarFiltros();
            });
        });
    });
    </script>
</body>
</html>