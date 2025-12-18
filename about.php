<?php
require_once 'auth_check.php';
$page_title = "About Us - QuickBite";
require_once 'header.php';
?>

<style>
:root{
    --font-heading: "Poppins", sans-serif;
    --primary-color: #ff5722;
    --surface-color: #fff7f2;
    --surface-light: #fff3ec;
    --border-color: #e7e1dd;
    --text-dark: #2e2e2e;
}

body {
    background: #faf4ef;
    margin: 0;
    font-family: system-ui, -apple-system, "Segoe UI", Roboto, Arial;
    color: #333;
}

.about-container { overflow-x: hidden; }

.about-hero {
    background-image:
      linear-gradient(rgba(0,0,0,0.45), rgba(0,0,0,0.45)),
      url('https://images.unsplash.com/photo-1555396273-3GH7ea4eb4db5?auto=format&fit=crop&w=1200&q=80');
    background-size: cover;
    background-position: center;
    color: white;
    text-align: center;
    padding: 70px 20px;
}
.about-hero h1 {
    font-size: 2.4rem;
    font-weight: 700;
    margin: 0 0 10px;
}
.about-hero p { max-width: 800px; margin: 0 auto; font-size: 1rem; line-height: 1.5; }

.about-content { max-width: 1100px; margin: 50px auto; padding: 0 20px; }
.section-title { font-size: 1.9rem; font-weight: 700; margin-bottom: 10px; text-align: center; color: var(--text-dark); }
.about-content hr { border: none; border-top: 1px solid #d4c8bf; margin: 30px 0; }

.story-section p { text-align: center; max-width: 900px; margin: auto; font-size: 1.05rem; line-height: 1.8; }

.values-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 25px; }
.value-card {
    background: var(--surface-color);
    padding: 22px;
    border: 1px solid var(--border-color);
    border-radius: 12px;
    text-align: center;
    min-height: 230px;
    box-sizing: border-box;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: flex-start;
}
.value-card .icon {
    width: 72px;
    height: 72px;
    background: #fff;
    border-radius: 50%;
    border: 2px solid rgba(0,0,0,0.05);
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 12px;
}
.value-card .icon i { color: var(--primary-color); font-size: 26px; }
.value-card .icon img { width: 72px; height: 72px; border-radius: 50%; object-fit: cover; display: block; }
.icon-fallback {
    width: 72px; height: 72px; border-radius: 50%; border: 2px solid rgba(0,0,0,0.05);
    background: #fff; display:flex; align-items:center; justify-content:center;
}
.icon-fallback i { font-size: 24px; color: var(--primary-color); }

.value-card h3 { font-size: 1.2rem; font-weight: bold; margin: 6px 0 8px; }
.value-card p { font-size: 0.95rem; color: #444; line-height: 1.6; margin: 0; }

.team-grid { display:flex; justify-content:center; margin-top:20px; }
.team-member { text-align:center; }
.team-member img { width:150px; height:150px; border-radius:50%; object-fit:cover; border:4px solid var(--primary-color); }
.team-member h3 { margin:10px 0 5px; font-size:1.2rem; }
.team-member .title { font-size:0.95rem; font-weight:700; color:var(--primary-color); }

.cta-section { padding:40px; background:var(--surface-light); text-align:center; border-radius:10px; }
.cta-section h2 { font-size:1.6rem; margin-bottom:14px; }
.cta-button { background-color:var(--primary-color); color:#fff; padding:12px 28px; border-radius:30px; text-decoration:none; display:inline-block; }

@media (max-width:700px){
    .about-hero { padding:50px 20px; }
    .about-hero h1 { font-size:1.9rem; }
    .value-card { min-height:auto; padding:18px; }
    .value-card .icon, .value-card .icon img, .icon-fallback { width:56px; height:56px; }
    .value-card .icon i, .icon-fallback i { font-size:22px; }
}
</style>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

<div class="about-container">

    <section class="about-hero">
        <h1>Our Journey of Taste</h1>
        <p>Bringing authentic Indian flavors to the heart of Rajkot, one delicious meal at a time.</p>
    </section>

    <div class="about-content">

        <section class="story-section">
            <h2 class="section-title">Our Story</h2>
            <p><strong>QuickBite</strong> was born from a passion for authentic Indian cuisine. Our goal is to bring fresh, delicious food to your doorstep with traditional taste and modern convenience.</p>
        </section>

        <hr>

        <section class="values-section">
            <h2 class="section-title">Our Promise to You</h2>
            <div class="values-grid">

                <div class="value-card">
                    <div class="icon"><i class="fas fa-leaf" aria-hidden="true"></i></div>
                    <h3>Quality Ingredients</h3>
                    <p>We source the freshest vegetables and spices from trusted suppliers to ensure every dish tastes amazing.</p>
                </div>

                <div class="value-card">
                    <div class="icon">

                        <img src="Images/anshul-dave-profile.jpg" alt="Chef">
                    </div>
                    <h3>Expert Chefs</h3>
                    <p>Our chefs prepare each meal with care and expertise, ensuring an unforgettable dining experience.</p>
                </div>

                <div class="value-card">
                    <div class="icon"><i class="fas fa-rocket" aria-hidden="true"></i></div>
                    <h3>Speedy Delivery</h3>
                    <p>Your order is delivered hot and fresh, right on time — every time.</p>
                </div>

            </div>
        </section>

        <hr>

        <section class="team-section">
            <h2 class="section-title">Our Speciality</h2>
            <div class="team-grid">
                <div class="team-member">
                    <img src="Images/veg_biryani.jpg" alt="Veg Biryani">
                    <h3>Veg Biryani</h3>
                    <p class="title">Our Signature Dish</p>
                </div>
            </div>
        </section>

        <hr>

        <section class="cta-section">
            <h2>Ready for a Taste Adventure?</h2>
            <a href="menu.php" class="cta-button">Explore Our Menu</a>
        </section>

    </div>
</div>

<script>

document.addEventListener('DOMContentLoaded', function () {
    const imgs = document.querySelectorAll('.value-card img, .team-member img');

    function replaceWithIcon(imgEl, iconClass = 'fas fa-utensils') {
        const wrapper = document.createElement('div');
        wrapper.className = 'icon-fallback';
        wrapper.innerHTML = '<i class="' + iconClass + '"></i>';
        imgEl.replaceWith(wrapper);
    }

    function tryCandidates(imgEl, candidates, idx = 0) {
        if (!candidates[idx]) {
            replaceWithIcon(imgEl);
            return;
        }
        const test = new Image();
        test.onload = function () {
            imgEl.src = candidates[idx];
            imgEl.style.display = 'block';
        };
        test.onerror = function () {
            tryCandidates(imgEl, candidates, idx + 1);
        };
        test.src = candidates[idx];
    }

    imgs.forEach(img => {
        const orig = img.getAttribute('src') || '';
        
        const candidates = [orig];
        const swappedLower = orig.replace(/^Images\//, 'images/');
        const swappedUpper = orig.replace(/^images\//, 'Images/');
        if (swappedLower !== orig) candidates.push(swappedLower);
        if (swappedUpper !== orig && swappedUpper !== swappedLower) candidates.push(swappedUpper);

        tryCandidates(img, candidates);


        img.addEventListener('error', function () { replaceWithIcon(img); });
    });
});
</script>

<?php
require_once 'footer.php';
?>
