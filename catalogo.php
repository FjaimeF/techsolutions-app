<?php include 'includes/header.php'; ?>
<link rel="stylesheet" href="assets/css/catalogo.css">

<h1 style="margin-bottom: 20px; font-size: 24px;">Catálogo de Productos</h1>

<!-- Controles de Búsqueda y Filtro -->
<div class="catalog-controls">
    <input type="text" id="search-input" placeholder="Buscar producto..." class="search-input">
    <div class="filter-buttons" id="filter-container">
        <button class="active" data-categoria="todas">Todas</button>
        <button data-categoria="Laptops">Laptops</button>
        <button data-categoria="Celulares">Celulares</button>
        <button data-categoria="Audio">Audio</button>
        <button data-categoria="Monitores">Monitores</button>
        <button data-categoria="Accesorios">Accesorios</button>
    </div>
</div>

<!-- Grid de Productos -->
<div class="catalog-grid" id="catalog-grid">
    <?php
    // Traemos los productos desde la base de datos
    $stmt = $pdo->query("SELECT * FROM productos ORDER BY id DESC");
    $productos = $stmt->fetchAll();

    if (count($productos) > 0) {
        foreach ($productos as $prod) {
            // Lógica de colores para el inventario
            $badge_class = 'badge-success';
            $estado_texto = 'Disponible';
            
            if ($prod['stock'] == 0) {
                $badge_class = 'badge-danger';
                $estado_texto = 'Agotado';
            } elseif ($prod['stock'] <= $prod['stock_minimo']) {
                $badge_class = 'badge-warning';
                $estado_texto = 'Stock Bajo';
            }
            ?>
            
            <!-- Tarjeta de Producto -->
            <div class="product-card" data-categoria="<?= htmlspecialchars($prod['categoria']) ?>" data-nombre="<?= htmlspecialchars(strtolower($prod['nombre'])) ?>">
                <div class="product-image">
                    <i class="fa-solid fa-laptop"></i> <!-- Icono genérico, puedes cambiarlo según categoría -->
                </div>
                <div class="product-info">
                    <div class="product-header">
                        <h3><?= htmlspecialchars($prod['nombre']) ?></h3>
                        <span class="badge <?= $badge_class ?>"><?= $estado_texto ?></span>
                    </div>
                    <p class="product-category"><?= htmlspecialchars($prod['categoria']) ?> - <?= htmlspecialchars($prod['codigo']) ?></p>
                    
                    <div class="product-footer">
                        <span class="price">S/ <?= number_format($prod['precio'], 2) ?></span>
                        <span class="stock">Stock: <?= $prod['stock'] ?> und.</span>
                    </div>
                </div>
            </div>

            <?php
        }
    } else {
        echo "<p style='text-align:center; grid-column: 1/-1;'>No hay productos registrados en el catálogo aún.</p>";
    }
    ?>
</div>

<!-- Mensaje si la búsqueda no arroja resultados -->
<div id="no-results" style="display: none; text-align: center; color: var(--text-muted); margin-top: 30px;">
    <h3>No se encontraron productos con esa búsqueda.</h3>
</div>

<!-- Script para el filtrado en vivo -->
<script>
document.addEventListener("DOMContentLoaded", function() {
    const searchInput = document.getElementById('search-input');
    const filterButtons = document.querySelectorAll('#filter-container button');
    const productCards = document.querySelectorAll('.product-card');
    const noResults = document.getElementById('no-results');

    let currentCategory = 'todas';
    let currentSearch = '';

    function filterProducts() {
        let visibleCount = 0;

        productCards.forEach(card => {
            const category = card.getAttribute('data-categoria');
            const name = card.getAttribute('data-nombre');

            const matchesCategory = (currentCategory === 'todas' || category === currentCategory);
            const matchesSearch = name.includes(currentSearch);

            if (matchesCategory && matchesSearch) {
                card.style.display = 'block';
                visibleCount++;
            } else {
                card.style.display = 'none';
            }
        });

        // Mostrar u ocultar mensaje de "No hay resultados"
        if (visibleCount === 0) {
            noResults.style.display = 'block';
        } else {
            noResults.style.display = 'none';
        }
    }

    // Evento de búsqueda por teclado
    searchInput.addEventListener('input', function(e) {
        currentSearch = e.target.value.toLowerCase();
        filterProducts();
    });

    // Evento de clic en los botones de categoría
    filterButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Quitar clase 'active' de todos los botones
            filterButtons.forEach(btn => btn.classList.remove('active'));
            // Poner 'active' al botón clickeado
            this.classList.add('active');
            
            currentCategory = this.getAttribute('data-categoria');
            filterProducts();
        });
    });
});
</script>

<?php include 'includes/footer.php'; ?>