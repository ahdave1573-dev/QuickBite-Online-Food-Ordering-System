<?php
$page_title = "Home - QuickBite";

require_once 'header.php';

$host = "localhost";
$username = "root";
$password = "";
$database = "food";

$conn = new mysqli($host, $username, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$categories = [];

$sql_categories = "SELECT id, name, image FROM categories WHERE image IS NOT NULL AND image != '' ORDER BY name ASC LIMIT 6"; 
$result_categories = $conn->query($sql_categories);
if ($result_categories && $result_categories->num_rows > 0) {
    while ($row = $result_categories->fetch_assoc()) {

        $row['image_path'] = !empty($row['image']) ? 'admin/uploads/' . basename($row['image']) : 'images/placeholder.png';
        $categories[] = $row;
    }
}

$topItems = [];
$sql_top_items = "SELECT 
                    m.id, m.name, m.price, m.rating, m.image, m.is_available,
                    o.offer_name, o.discount_percent
                FROM 
                    menu_items m
                LEFT JOIN 
                    offers o ON m.id = o.item_id AND CURDATE() BETWEEN o.start_date AND o.end_date
                WHERE 
                    m.image IS NOT NULL AND m.image != ''
                ORDER BY 
                    m.rating DESC
                LIMIT 8";

$result_top_items = $conn->query($sql_top_items);

if ($result_top_items && $result_top_items->num_rows > 0) {
    while ($row = $result_top_items->fetch_assoc()) {
        $row['img'] = !empty($row['image']) ? 'admin/uploads/' . basename($row['image']) : 'images/placeholder.png';
        $topItems[] = $row;
    }
}

$today = date("Y-m-d");
$sql_offers = "SELECT 
                    o.offer_name, o.discount_percent, 
                    m.id as item_id, m.name as item_name, m.price as original_price, m.image, m.is_available
                FROM offers o
                JOIN menu_items m ON o.item_id = m.id
                WHERE o.start_date <= ? AND o.end_date >= ? AND o.discount_percent > 30
                ORDER BY o.discount_percent DESC";
$offers = [];
if ($stmt = $conn->prepare($sql_offers)) {
    $stmt->bind_param('ss', $today, $today);
    $stmt->execute();
    $result_offers = $stmt->get_result();
    if ($result_offers && $result_offers->num_rows > 0) {
        while ($row = $result_offers->fetch_assoc()) {
            $row['image'] = !empty($row['image']) ? 'admin/uploads/' . basename($row['image']) : 'images/placeholder.png';
            $offers[] = $row;
        }
    }
    $stmt->close();
}

$conn->close();
?>

<style>

    main { padding: 50px 20px; max-width: 1200px; margin: 0 auto; text-align: center; }

    main h2 { font-family: var(--font-heading); font-size: 2.2em; margin-top: 60px; margin-bottom: 30px; position: relative; display: inline-block; }
    main h2::after { content: ''; position: absolute; bottom: -10px; left: 50%; transform: translateX(-50%); width: 60px; height: 4px; background-color: var(--primary-color); border-radius: 2px; }

    .hero {
        background-image: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('https://images.unsplash.com/photo-1504674900247-0877df9cc836?auto=format&fit=crop&w=1200&q=80');
        background-size: cover;
        background-position: center;
        padding: 100px 20px;
        border-radius: 15px;
        margin-bottom: 40px;
    }
    .hero h1 {
        font-family: var(--font-heading);
        font-size: 3.5em;
        color: white;
        margin: 0;
        text-shadow: 2px 2px 8px rgba(0,0,0,0.7);
    }
    .hero p {
        font-size: 1.3em;
        color: white;
        margin: 15px 0 30px 0;
    }
    .hero-btn {
        display: inline-block;
        background-color: var(--primary-color);
        color: white;
        padding: 15px 35px;
        font-size: 1.1em;
        font-weight: bold;
        border-radius: 50px;
        text-decoration: none;
        transition: background-color 0.3s ease, transform 0.3s ease;
    }
    .hero-btn:hover {
        background-color: var(--primary-dark);
        transform: scale(1.05);
    }

    .categories-section {
        margin-bottom: 40px;
    }
    .category-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 25px;
        justify-content: center;
    }
    .category-card {
        background: var(--surface-color);
        border-radius: 15px;
        box-shadow: var(--shadow);
        overflow: hidden;
        text-decoration: none;
        color: var(--text-dark);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        border: 1px solid var(--border-color);
    }
    .category-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 12px 25px rgba(0, 0, 0, 0.1);
    }
    .category-card img {
        width: 100%;
        height: 120px;
        object-fit: cover;
    }
    .category-card h3 {
        font-size: 1.1em;
        font-weight: 600;
        margin: 15px;
        text-align: center;
    }

    .carousel { position: relative; }
    .carousel-container { overflow: hidden; padding: 0 10px; }
    .carousel-track { display: flex; transition: transform 0.5s ease-in-out; }
    .carousel-btn { position: absolute; top: 50%; transform: translateY(-50%); background-color: rgba(255, 255, 255, 0.8); border: 1px solid var(--border-color); border-radius: 50%; width: 45px; height: 45px; cursor: pointer; z-index: 10; font-size: 24px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); display: flex; align-items: center; justify-content: center; }
    .carousel-btn:hover { background-color: white; }
    .carousel-btn.prev { left: -20px; }
    .carousel-btn.next { right: -20px; }
    .carousel-btn:disabled { cursor: not-allowed; opacity: 0.4; }

    .offers-container { display: flex; flex-wrap: wrap; justify-content: center; gap: 30px; }
    .item-card, .offer-card { background: var(--surface-color); border-radius: 15px; box-shadow: var(--shadow); overflow: hidden; transition: transform 0.3s ease, box-shadow 0.3s ease; text-align: left; border: 1px solid var(--border-color); }
    .item-card:hover, .offer-card:hover { transform: translateY(-8px); box-shadow: 0 12px 25px rgba(0, 0, 0, 0.1); }
    .item-card { flex: 0 0 260px; margin: 15px; display: flex; flex-direction: column; position: relative; }
    .item-card img { width: 100%; height: 170px; object-fit: cover; }
    .item-card .info { padding: 20px; flex-grow: 1; display: flex; flex-direction: column; }
    .item-card .info form { margin-top: auto; }
    .item-card h3 { margin: 0 0 8px 0; font-size: 1.2em; font-weight: 600; }
    .item-card p { margin: 4px 0; color: #666; }
    .rating { color: var(--primary-color); font-weight: 600; font-size: 1.1em; }
    .offer-card { width: 250px; text-align: center; position: relative; padding-bottom: 20px; display: flex; flex-direction: column; }
    .offer-card img { width: 100%; height: 160px; object-fit: cover; }
    .discount-percent { background: var(--primary-dark); color: white; font-weight: 600; padding: 5px 10px; border-radius: 0 0 0 15px; position: absolute; top: 0; right: 0; font-size: 0.9em; }
    .item-card .discount-percent { border-radius: 0 15px 0 15px; }
    .offer-card h4 { margin: 15px 10px 5px 10px; font-size: 1.2em; font-weight: 600; }
    .original-price { text-decoration: line-through; color: #999; }
    .discount-price { color: var(--primary-dark); font-weight: 700; font-size: 1.3em; margin-left: 8px; }
    .offer-card form { margin-top: auto; }
    .add-to-cart-btn { display: inline-block; margin-top: 15px; padding: 12px 25px; background-color: var(--primary-color); color: white; text-decoration: none; border-radius: 8px; font-size: 1em; font-weight: 600; border: none; cursor: pointer; transition: background-color 0.2s, transform 0.2s; }
    .add-to-cart-btn:hover { background-color: var(--primary-dark); transform: scale(1.05); }

    .add-to-cart-btn:disabled {
        background-color: #bdc3c7;
        cursor: not-allowed;
        transform: none;
    }
    .add-to-cart-btn:disabled:hover {
        background-color: #bdc3c7;
    }
</style>

<section class="hero">
    <h1>Delicious Food, Delivered Fast.</h1>
    <p>Your favorite meals from local restaurants, right to your door.</p>
    <a href="menu.php" class="hero-btn">Explore Full Menu</a>
</section>

<?php if (!empty($categories)): ?>
<section class="categories-section">
    <h2>Browse by Category</h2>
    <div class="category-grid">
        <?php foreach ($categories as $category): ?>
            <a href="menu.php?category_id=<?= $category['id'] ?>" class="category-card">
                <img src="<?= htmlspecialchars($category['image_path']) ?>" alt="<?= htmlspecialchars($category['name']) ?>">
                <h3><?= htmlspecialchars($category['name']) ?></h3>
            </a>
        <?php endforeach; ?>
    </div>
</section>
<?php endif; ?>


<?php if (!empty($topItems)): ?>
<section class="top-items">
    <h2>⭐ Top Rated Dishes</h2>
    <div class="carousel">
        <div class="carousel-container">
            <div class="carousel-track" id="carousel-track">
                <?php foreach ($topItems as $item): ?>
                    <?php
                        $is_on_offer = !empty($item['discount_percent']);
                        $display_price = $item['price'];
                        if ($is_on_offer) {
                            $discounted_price = $item['price'] * (1 - $item['discount_percent'] / 100);
                            $display_price = $discounted_price;
                        }
                    ?>
                    <div class="item-card">
                        <img src="<?= htmlspecialchars($item['img']) ?>" alt="<?= htmlspecialchars($item['name']) ?>">
                        <?php if ($is_on_offer): ?>
                            <span class="discount-percent"><?= $item['discount_percent'] ?>% OFF</span>
                        <?php endif; ?>
                        <div class="info">
                            <h3><?= htmlspecialchars($item['name']) ?></h3>
                            <?php if ($is_on_offer): ?>
                                <p><span class="original-price">₹<?= number_format($item['price'], 2) ?></span> <span class="discount-price">₹<?= number_format($display_price, 2) ?></span></p>
                            <?php else: ?>
                                <p>Price: ₹<?= number_format($display_price, 2) ?></p>
                            <?php endif; ?>
                            <p class="rating">⭐ <?= number_format($item['rating'], 1) ?></p>
                            <form method="POST" action="cart.php" style="margin-top: 10px;">
                                <input type="hidden" name="id" value="<?= htmlspecialchars($item['id']) ?>">
                                <input type="hidden" name="name" value="<?= htmlspecialchars($item['name']) ?>">
                                <input type="hidden" name="price" value="<?= htmlspecialchars($display_price) ?>">
                                <input type="hidden" name="image" value="<?= htmlspecialchars($item['img']) ?>">
                                <input type="hidden" name="action" value="add">
                                <input type="hidden" name="offer_name" value="<?= htmlspecialchars($item['offer_name'] ?? '') ?>">
                                <input type="hidden" name="offer_percentage" value="<?= htmlspecialchars($item['discount_percent'] ?? 0) ?>">
                                <?php if ($item['is_available'] == 1): ?>
                                    <button type="submit" class="add-to-cart-btn">Add to Cart</button>
                                <?php else: ?>
                                    <button type="button" class="add-to-cart-btn" disabled>Out of Stock</button>
                                <?php endif; ?>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <button class="carousel-btn prev" id="prevBtn">❮</button>
        <button class="carousel-btn next" id="nextBtn">❯</button>
    </div>
</section>
<?php endif; ?>

<section class="offers-section">
    <h2>🎉 Exclusive Discounts</h2>
    <div class="offers-container">
        <?php foreach ($offers as $offer):
            $discount_price = $offer['original_price'] * (1 - $offer['discount_percent'] / 100);
        ?>
        <div class="offer-card">
            <img src="<?= htmlspecialchars($offer['image']) ?>" alt="<?= htmlspecialchars($offer['item_name']) ?>">
            <span class="discount-percent"><?= htmlspecialchars($offer['discount_percent']) ?>% OFF</span>
            <h4><?= htmlspecialchars($offer['item_name']) ?></h4>
            <p><span class="original-price">₹<?= number_format($offer['original_price'], 2) ?></span> <span class="discount-price">₹<?= number_format($discount_price, 2) ?></span></p>
            <form method="POST" action="cart.php">
                <input type="hidden" name="id" value="<?= htmlspecialchars($offer['item_id']) ?>">
                <input type="hidden" name="name" value="<?= htmlspecialchars($offer['item_name']) ?>">
                <input type="hidden" name="price" value="<?= htmlspecialchars($discount_price) ?>">
                <input type="hidden" name="image" value="<?= htmlspecialchars($offer['image']) ?>">
                <input type="hidden" name="action" value="add">
                <input type="hidden" name="offer_name" value="<?= htmlspecialchars($offer['offer_name'] ?? '') ?>">
                <input type="hidden" name="offer_percentage" value="<?= htmlspecialchars($offer['discount_percent'] ?? 0) ?>">
                <?php if ($offer['is_available'] == 1): ?>
                    <button type="submit" class="add-to-cart-btn">Add to Cart</button>
                <?php else: ?>
                    <button type="button" class="add-to-cart-btn" disabled>Out of Stock</button>
                <?php endif; ?>
            </form>
        </div>
        <?php endforeach; ?>
    </div>
</section>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const track = document.getElementById('carousel-track');
        if (track && track.children.length > 0) {
            const prevBtn = document.getElementById('prevBtn');
            const nextBtn = document.getElementById('nextBtn');
            const items = Array.from(track.children);
            
            const getVisibleItems = () => {
                if (window.innerWidth >= 1200) return 4;
                if (window.innerWidth >= 992) return 3;
                if (window.innerWidth >= 768) return 2;
                return 1;
            };

            let visibleItems = getVisibleItems();
            let cardWidth = items[0].offsetWidth + 30;
            let currentIndex = 0;

            function updateCarousel() {
                visibleItems = getVisibleItems();
                cardWidth = items[0].offsetWidth + 30;
                track.style.transform = `translateX(-${currentIndex * cardWidth}px)`;
                updateButtons();
            }

            function updateButtons() {
                prevBtn.disabled = currentIndex === 0;
                nextBtn.disabled = currentIndex >= items.length - visibleItems;
            }

            function slideToPosition(index) {
                if (index < 0) {
                    index = 0;
                } else if (index > items.length - visibleItems) {
                    index = items.length - visibleItems;
                }
                track.style.transform = `translateX(-${index * cardWidth}px)`;
                currentIndex = index;
                updateButtons();
            }

            nextBtn.addEventListener('click', () => {
                slideToPosition(currentIndex + 1);
            });

            prevBtn.addEventListener('click', () => {
                slideToPosition(currentIndex - 1);
            });
            
            window.addEventListener('resize', updateCarousel);
            updateCarousel();
        }
    });
</script>
<?php require_once 'footer.php'; ?>