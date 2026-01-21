<?php
/** @var \Framework\Support\LinkGenerator $link */
/** @var array<int, array<string, mixed>> $recipes */
?>

<div class="container-fluid">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h1 class="h4 m-0">Your Favourites</h1>
        <a class="btn btn-sm btn-outline-secondary" href="<?= $link->url('home.homePage') ?>">Back</a>
    </div>

    <?php if (empty($recipes)) { ?>
        <div class="alert alert-info">You have no favourite recipes yet.</div>
    <?php } ?>

    <?php if (!empty($recipes)) { ?>
        <div class="text-muted mb-3">Showing recipes you've marked as favourites.</div>
    <?php } ?>

    <div class="row g-3">
        <?php foreach ($recipes as $r) { ?>
            <div class="col-12 col-md-6 col-lg-4">
                <div class="card h-100">
                    <button type="button" class="favourite-btn added" data-recipe-id="<?= htmlspecialchars((int)$r['id']) ?>" title="Toggle favourite" aria-pressed="true">♡</button>
                    <?php if (!empty($r['image'])) { ?>
                        <img src="<?= htmlspecialchars($r['image']) ?>" class="card-img-top" alt="<?= htmlspecialchars($r['title']) ?>">
                    <?php } ?>
                    <div class="card-body">
                        <h2 class="h6 card-title mb-2"><?= htmlspecialchars($r['title']) ?></h2>

                        <?php if (!empty($r['category'])) { ?>
                            <div class="badge text-bg-light border mb-2"><?= htmlspecialchars($r['category']) ?></div>
                        <?php } ?>

                        <?php if (!empty($r['cooking_time'])) { ?>
                            <div class="small mb-2">Cooking time: <?= (int)$r['cooking_time'] ?> min</div>
                        <?php } ?>

                        <?php if (!empty($r['serving_size'])) { ?>
                            <div class="small mb-2">Servings: <?= (int)$r['serving_size'] ?></div>
                        <?php } ?>

                        <?php if (!empty($r['instructions'])) { ?>
                            <p class="card-text small mb-0"><?= htmlspecialchars(mb_strimwidth((string)$r['instructions'], 0, 160, '…')) ?></p>
                        <?php } ?>
                    </div>
                </div>
            </div>
        <?php } ?>
    </div>
</div>

<style>
.card{ position:relative; }
.favourite-btn{ position:absolute; right:12px; top:12px; z-index:10; border:0; background:transparent; font-size:20px; line-height:1; cursor:pointer; }
.favourite-btn.added{ color:#e0245e; }
</style>

<script>
    (function(){
        if (typeof document === 'undefined') return;
        document.addEventListener('DOMContentLoaded', function(){
            const toggleUrl = '<?= $link->url("favourite.toggle") ?>';
            document.querySelectorAll('.favourite-btn').forEach(btn => {
                const id = parseInt(btn.getAttribute('data-recipe-id'),10);
                btn.addEventListener('click', function(e){
                    e.stopPropagation();
                    fetch(toggleUrl, { method: 'POST', headers: { 'Content-Type':'application/x-www-form-urlencoded' }, body: new URLSearchParams({ recipe_id: id }) })
                        .then(r => { if (r.redirected) { window.location = r.url; return; } return r.json().catch(()=>null); })
                        .then(j => { if (!j) return; if (j.success) { if (j.added) { btn.classList.add('added'); } else { btn.classList.remove('added'); btn.closest('.col-12')?.remove(); } } else if (j.error) { alert(j.error); } })
                        .catch(()=>{ alert('Network error'); });
                });
            });
        });
    })();
</script>
