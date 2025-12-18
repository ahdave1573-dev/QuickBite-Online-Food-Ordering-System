<?php
require_once 'auth_check.php';

$conn = new mysqli("localhost", "root", "", "food");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $price = $_POST['price'];
    $image = $_POST['image'];

    if (strpos($image, 'admin/uploads/') === false) {
         $image = 'admin/uploads/' . basename($image);
    }

    $offer_name = $_POST['offer_name'] ?? '';
    $offer_percentage = $_POST['offer_percentage'] ?? '';

    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    if (isset($_SESSION['cart'][$id])) {
        $_SESSION['cart'][$id]['quantity']++;
    } else {
        $_SESSION['cart'][$id] = [
            'id' => $id,
            'name' => $name,
            'price' => $price,
            'image' => $image,
            'quantity' => 1,
            'offer_name' => $offer_name,
            'offer_percentage' => $offer_percentage
        ];
    }

    $params = $_GET;
    $params['added'] = $name;
    header("Location: offers.php?" . http_build_query($params));
    exit;
}

$message = '';
if (isset($_GET['added'])) {
    $message = htmlspecialchars($_GET['added']) . " has been added to your cart!";
}

$categories = [];
$category_result = $conn->query("SELECT DISTINCT category FROM menu_items WHERE category IS NOT NULL AND category != '' ORDER BY category ASC");
while ($cat_row = $category_result->fetch_assoc()) {
    $categories[] = $cat_row['category'];
}

$selected_category = $_GET['category'] ?? '';

$offers = [];
$today = date("Y-m-d");

$sql = "SELECT 
            o.offer_name as offer_title, o.discount_percent, o.end_date, 
            m.id as item_id, m.name as item_name, m.price as original_price, 
            m.image, m.category, m.is_available 
        FROM offers o 
        JOIN menu_items m ON o.item_id = m.id 
        WHERE o.start_date <= ? AND o.end_date >= ?";

$types = "ss";
$params = [$today, $today];

if (!empty($selected_category)) {
    $sql .= " AND m.category = ?";
    $types .= "s";
    $params[] = $selected_category;
}

$sql .= " ORDER BY o.discount_percent DESC";

$stmt = $conn->prepare($sql);
if ($stmt === false) { die("SQL Error: " . $conn->error); }

$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $row['image_path'] = !empty($row['image']) ? 'admin/uploads/' . basename($row['image']) : 'images/placeholder.png';
    $offers[] = $row;
}
$stmt->close();
$conn->close();

$page_title = "Offers - QuickBite";
require_once 'header.php';
?>

<style>
main { padding: 40px 20px; text-align: center; }
main h1 { font-size: 2.5em; margin-bottom: 20px; color: var(--primary-dark); }
.message { text-align: center; padding: 15px; background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; border-radius: 8px; margin: 0 auto 30px auto; max-width: 600px; }

.controls-wrapper { max-width: 1200px; margin: 0 auto 20px auto; }
.filter-container { display: flex; justify-content: center; flex-wrap: wrap; gap: 10px; margin-bottom: 30px; }
.filter-btn { text-decoration: none; color: var(--primary-dark); background-color: var(--card-bg); padding: 8px 18px; border-radius: 20px; font-weight: 500; border: 1px solid var(--primary-color); transition: all 0.3s ease; }
.filter-btn:hover, .filter-btn.active { background-color: var(--primary-color); color: var(--text-light); box-shadow: 0 2px 8px rgba(0,0,0,0.1); }

.offers-container { display: flex; flex-wrap: wrap; justify-content: center; gap: 25px; max-width: 1200px; margin: 0 auto; }
.offer-card { flex: 1 1 280px; max-width: 280px; background: var(--card-bg); border-radius: 12px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.08); text-align: left; position: relative; display: flex; flex-direction: column; transition: all 0.3s ease; border: 1px solid #f0f0f0; }
.offer-card:hover { transform: translateY(-8px); box-shadow: 0 8px 25px rgba(0,0,0,0.12); }

.offer-card.out-of-stock { filter: grayscale(80%); opacity: 0.7; }

.offer-card img { width: 100%; height: 180px; object-fit: cover; }
.card-content { padding: 20px; flex-grow: 1; display: flex; flex-direction: column; }
.card-content h4 { font-size: 1.2em; margin: 0 0 8px 0; color: var(--text-dark); min-height: 58px; }
.offer-name { color: #E67E22; margin-bottom: 5px; font-weight: bold; font-size: 0.9em; text-transform: uppercase; }
.price-container { display: flex; align-items: center; gap: 10px; margin-top: 6px; }
.original-price { text-decoration: line-through; color: #999; font-size: 0.9em; }
.discount-price { color: var(--primary-dark); font-weight: 700; font-size: 1.3em; }
.validity { font-size: 0.8em; color: #888; padding-top: 15px; margin-top: auto; }
.discount-percent { background: var(--primary-dark); color: white; font-weight: 600; padding: 4px 8px; border-radius: 6px; position: absolute; top: 12px; right: 12px; font-size: 0.8em; }
.add-to-cart-btn { background: var(--primary-color); color: white; border: none; padding: 12px 15px; border-radius: 8px; cursor: pointer; width: 100%; font-weight: 600; font-size: 1em; transition: all 0.3s ease; margin-top: 10px; display: flex; align-items: center; justify-content: center; gap: 8px; }
.add-to-cart-btn:hover { background-color: var(--primary-dark); transform: scale(1.03); }
.add-to-cart-btn:disabled { background-color: #bdc3c7; cursor: not-allowed; transform: none; }

@media (max-width: 768px) {
    .offer-card { max-width: 100%; flex: 1 1 100%; }
}
</style>

<main>
    <h1>Current Offers</h1>

    <?php if (!empty($message)): ?>
        <p class="message"><?= $message ?></p>
    <?php endif; ?>

    <div class="controls-wrapper">
        <div class="filter-container">
            <input type="hidden" id="categoryInput" name="category" value="<?= htmlspecialchars($selected_category) ?>">
            <a href="javascript:void(0);" onclick="setCategoryAndReload('')" class="filter-btn <?= empty($selected_category) ? 'active' : '' ?>">All</a>
            <?php foreach($categories as $category): ?>
                <a href="javascript:void(0);" onclick="setCategoryAndReload('<?= htmlspecialchars($category, ENT_QUOTES) ?>')" class="filter-btn <?= ($selected_category == $category) ? 'active' : '' ?>">
                    <?= htmlspecialchars($category) ?>
                </a>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="offers-container">
        <?php if (empty($offers)): ?>
            <p>
                No active offers found for this category.
            </p>
        <?php else: ?>
            <?php foreach ($offers as $offer):
                $discount_price = $offer['original_price'] * (1 - $offer['discount_percent']/100);
                $card_class = ($offer['is_available'] == 1) ? '' : 'out-of-stock';
            ?>
                <div class="offer-card <?= $card_class ?>">
                    <img src="<?= htmlspecialchars($offer['image_path']) ?>" alt="<?= htmlspecialchars($offer['item_name']) ?>">
                    <span class="discount-percent"><?= htmlspecialchars($offer['discount_percent']) ?>% OFF</span>
                    <div class="card-content">
                        <div class="offer-name"><?= htmlspecialchars($offer['offer_title']) ?></div>
                        <h4><?= htmlspecialchars($offer['item_name']) ?></h4>

                        <div class="price-container">
                            <span class="discount-price">₹<?= number_format($discount_price, 2) ?></span>
                            <span class="original-price">₹<?= number_format($offer['original_price'], 2) ?></span>
                        </div>

                        <div class="validity">⏳ Valid until: <?= date("j M, Y", strtotime($offer['end_date'])) ?></div>

                        <form method="POST" action="offers.php?<?= http_build_query($_GET) ?>" style="width:100%; margin-top:12px;">
                            <input type="hidden" name="id" value="<?= $offer['item_id'] ?>">
                            <input type="hidden" name="name" value="<?= htmlspecialchars($offer['item_name']) ?>">
                            <input type="hidden" name="price" value="<?= $discount_price ?>">
                            <input type="hidden" name="image" value="<?= htmlspecialchars($offer['image_path']) ?>">
                            <input type="hidden" name="action" value="add">
                            <input type="hidden" name="offer_name" value="<?= htmlspecialchars($offer['offer_title']) ?>">
                            <input type="hidden" name="offer_percentage" value="<?= htmlspecialchars($offer['discount_percent']) ?>">

                            <?php if ($offer['is_available'] == 1): ?>
                                <button type="submit" class="add-to-cart-btn">🛒 Add to Cart</button>
                            <?php else: ?>
                                <button type="button" class="add-to-cart-btn" disabled>Out of Stock</button>
                            <?php endif; ?>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</main>

<script>
function setCategoryAndReload(category) {

    const url = new URL(window.location.href);
    if (category === '') {
        url.searchParams.delete('category');
    } else {
        url.searchParams.set('category', category);
    }

    window.location.href = url.toString();
}
</script>

<?php
require_once 'footer.php';
?>
