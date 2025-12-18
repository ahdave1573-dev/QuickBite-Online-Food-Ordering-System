<?php
require_once 'auth_check.php';

$conn = new mysqli("localhost", "root", "", "food");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = '';
if (isset($_GET['added'])) {
    $message = htmlspecialchars($_GET['added']) . " has been added to your cart!";
}

$categories = [];
$category_result = $conn->query("SELECT DISTINCT category FROM menu_items WHERE category IS NOT NULL AND category != '' ORDER BY category");
while ($cat_row = $category_result->fetch_assoc()) {
    $categories[] = $cat_row['category'];
}

$menu_items_grouped = [];
$selected_category = $_GET['category'] ?? '';

$sql = "SELECT 
            m.id, m.name, m.category, m.price, m.rating, m.image, m.is_available,
            o.discount_percent 
        FROM 
            menu_items m
        LEFT JOIN 
            offers o ON m.id = o.item_id AND CURDATE() BETWEEN o.start_date AND o.end_date";

$where_clauses = [];
$params = [];
$types = "";

if (!empty($selected_category)) {
    $where_clauses[] = "m.category = ?";
    $params[] = $selected_category;
    $types .= "s";
}

if (!empty($where_clauses)) {
    $sql .= " WHERE " . implode(" AND ", $where_clauses);
}

$sql .= " ORDER BY m.category, m.name";

$stmt = $conn->prepare($sql);
if ($stmt === false) {
    die("Prepare failed: " . htmlspecialchars($conn->error));
}

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $row['image'] = !empty($row['image']) ? 'admin/uploads/' . basename($row['image']) : 'images/placeholder.png';
        $menu_items_grouped[$row['category']][] = $row;
    }
}
$stmt->close();
$conn->close();

$page_title = "Menu - QuickBite";
require_once 'header.php';
?>

<style>
    main { padding: 40px 20px; max-width: 1200px; margin: auto; }
    main h2 { text-align: center; font-size: 2.5em; margin-bottom: 20px; color: var(--primary-dark); }
    .message { text-align: center; padding: 15px; background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; border-radius: 8px; margin: 0 auto 30px auto; max-width: 600px; }
    
    .controls-container {
        display: flex;
        flex-wrap: wrap;
        justify-content: center; 
        align-items: center;
        gap: 20px;
        margin-bottom: 30px;
    }

    .filter-container {
        display: flex;
        justify-content: center;
        flex-wrap: wrap;
        gap: 10px;
    }
    .filter-btn { text-decoration: none; color: var(--primary-dark); background-color: var(--card-bg); padding: 8px 18px; border-radius: 20px; font-weight: 500; border: 1px solid var(--primary-color); transition: all 0.15s ease; }
    .filter-btn:hover, .filter-btn.active { background-color: var(--primary-color); color: var(--text-light); }

    .category-section h3 { font-size: 1.8em; color: var(--primary-dark); padding-bottom: 10px; margin-top: 40px; margin-bottom: 20px; border-bottom: 2px solid var(--background-color); text-align: left; }
    .category-items { display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 25px; }
    .item-card { background: var(--card-bg); border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.08); text-align: left; position: relative; display: flex; flex-direction: column; transition: all 0.15s ease; }
    .item-card:hover { transform: translateY(-6px); box-shadow: 0 8px 25px rgba(0,0,0,0.12); }
    .item-card.out-of-stock { filter: grayscale(80%); opacity: 0.7; }
    .item-card img { width: 100%; height: 200px; object-fit: cover; border-top-left-radius: 12px; border-top-right-radius: 12px; }
    .card-content { padding: 20px; flex-grow: 1; display: flex; flex-direction: column; }
    .card-content h4 { font-size: 1.3em; margin: 0 0 10px 0; min-height: 40px; }
    .price-rating-row { display: flex; justify-content: space-between; align-items: center; }

    .price { font-weight: 700; font-size: 1.4em; color: var(--primary-dark); }
    .original-price { font-size: 1.1em; color: #888; text-decoration: line-through; margin-right: 8px; }
    .discount-badge { position: absolute; top: 10px; right: 10px; background-color: var(--primary-dark); color: white; padding: 5px 10px; border-radius: 8px; font-size: 0.9em; font-weight: bold; }

    .rating { color: #f39c12; font-weight: bold; }
    .item-card form { margin-top: auto; padding-top: 15px; }
    .add-to-cart-btn { background: var(--primary-color); color: white; border: none; padding: 12px; border-radius: 8px; cursor: pointer; width: 100%; font-weight: 600; font-size: 1em; transition: background-color 0.15s ease; }
    .add-to-cart-btn:hover { background-color: var(--primary-dark); }
    .add-to-cart-btn:disabled { background-color: #bdc3c7; cursor: not-allowed; }
</style>

<main>
    <h2>Our Menu</h2>

    <?php if (!empty($message)): ?>
        <p class="message"><?= $message ?></p>
    <?php endif; ?>

    <div class="controls-container">
        <div class="filter-container" role="navigation" aria-label="Category filters">
            <a href="menu.php" class="filter-btn <?= empty($selected_category) ? 'active' : '' ?>">All Categories</a>
            <?php foreach ($categories as $cat): ?>
                <a href="menu.php?category=<?= urlencode($cat) ?>" class="filter-btn <?= ($selected_category == $cat) ? 'active' : '' ?>">
                    <?= htmlspecialchars($cat) ?>
                </a>
            <?php endforeach; ?>
        </div>
    </div>

    <?php if (empty($menu_items_grouped)): ?>
        <p style="text-align:center; font-size: 1.2em;">
            No menu items found for this category.
        </p>
    <?php else: ?>
        <?php foreach ($menu_items_grouped as $category => $items): ?>
            <div class="category-section">
                <h3><?= htmlspecialchars($category) ?></h3>
                <div class="category-items">
                    <?php foreach ($items as $item): ?>
                        <?php
                            $is_on_offer = !empty($item['discount_percent']);
                            $display_price = $item['price'];
                            if ($is_on_offer) {
                                $display_price = $item['price'] * (1 - $item['discount_percent'] / 100);
                            }
                            $card_class = ($item['is_available'] == 1) ? '' : 'out-of-stock';
                        ?>

                        <div class="item-card <?= $card_class ?>">
                            <img src="<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['name']) ?>">
                            
                            <?php if ($is_on_offer): ?>
                                <span class="discount-badge"><?= htmlspecialchars($item['discount_percent']) ?>% OFF</span>
                            <?php endif; ?>

                            <div class="card-content">
                                <h4><?= htmlspecialchars($item['name']) ?></h4>
                                <div class="price-rating-row">
                                    <div class="price">
                                        <?php if ($is_on_offer): ?>
                                            <span class="original-price">₹<?= number_format($item['price'], 2) ?></span>
                                        <?php endif; ?>
                                        ₹<?= number_format($display_price, 2) ?>
                                    </div>
                                    <div class="rating"><?= htmlspecialchars($item['rating']) ?> ★</div>
                                </div>

                                <form method="POST" action="cart.php">
                                    <input type="hidden" name="id" value="<?= $item['id'] ?>">
                                    <input type="hidden" name="name" value="<?= htmlspecialchars($item['name']) ?>">
                                    <input type="hidden" name="price" value="<?= $display_price ?>">
                                    <input type="hidden" name="image" value="<?= htmlspecialchars($item['image']) ?>">
                                    <input type="hidden" name="action" value="add">
                                    <input type="hidden" name="return_url" value="menu.php?<?= http_build_query($_GET) ?>">

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
        <?php endforeach; ?>
    <?php endif; ?>
</main>

<?php
require_once 'footer.php';
?>
