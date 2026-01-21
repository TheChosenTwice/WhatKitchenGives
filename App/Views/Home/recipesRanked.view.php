<?php
/** @var \Framework\Support\LinkGenerator $link */
/** @var array<int, array<string, mixed>> $recipes */
/** @var int[] $ingredientIds */
?>

<div class="container-fluid">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h1 class="h4 m-0">Recipes</h1>
        <a class="btn btn-sm btn-outline-secondary" href="<?= $link->url('home.homePage') ?>">Back</a>
    </div>

    <?php if (empty($ingredientIds)) { ?>
        <div class="alert alert-warning">
            No ingredients selected. Go back and select at least one ingredient.
        </div>
    <?php } ?>

    <?php if (!empty($ingredientIds)) { ?>
        <div class="text-muted mb-3">
            Showing recipes ranked by number of matched ingredients.
        </div>
    <?php } ?>

    <?php if (empty($recipes) && !empty($ingredientIds)) { ?>
        <div class="alert alert-info">No recipes found for selected ingredients.</div>
    <?php } ?>

    <div class="row g-3">
        <?php foreach ($recipes as $r) { ?>
            <div class="col-12 col-md-6 col-lg-4">
                <div class="card h-100">
                    <button type="button" class="favourite-btn" data-recipe-id="<?= htmlspecialchars((int)$r['id']) ?>" title="Toggle favourite" aria-pressed="false">♡</button>
                    <?php if (!empty($r['image'])) { ?>
                        <img src="<?= htmlspecialchars($r['image']) ?>" class="card-img-top" alt="<?= htmlspecialchars($r['title']) ?>">
                    <?php } ?>
                    <div class="card-body">
                        <h2 class="h6 card-title mb-2"><?= htmlspecialchars($r['title']) ?></h2>

                        <div class="small text-muted mb-2">
                            Match: <b><?= (int)$r['match_count'] ?></b>/<?= (int)$r['total_ingredients'] ?>
                            <?php if (isset($r['missing_count'])) { ?>
                                &nbsp;• Missing: <?= (int)$r['missing_count'] ?>
                            <?php } ?>
                        </div>

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
                            <p class="card-text small mb-0">
                                <?= htmlspecialchars(mb_strimwidth((string)$r['instructions'], 0, 160, '…')) ?>
                            </p>
                        <?php } ?>
                    </div>
                </div>
            </div>
        <?php } ?>
    </div>
</div>

<style>
/* simple heart button positioning */
.card{ position:relative; }
.favourite-btn{
    position:absolute; right:12px; top:12px; z-index:10; border:0; background:transparent; font-size:20px; line-height:1; cursor:pointer;
}
.favourite-btn.added{ color:#e0245e; }
</style>

<script>
    (function(){
        if (typeof document === 'undefined') return;
        document.addEventListener('DOMContentLoaded', function(){
            const idsUrl = '<?= $link->url("favourite.ids") ?>';
            const toggleUrl = '<?= $link->url("favourite.toggle") ?>';

            // Map of favourited recipe ids
            let favSet = new Set();

            function initButtons(){
                document.querySelectorAll('.favourite-btn').forEach(btn => {
                    const id = parseInt(btn.getAttribute('data-recipe-id'),10);
                    if (favSet.has(id)) btn.classList.add('added');
                    btn.addEventListener('click', function(e){
                        e.stopPropagation();
                        // POST to toggle
                        fetch(toggleUrl, { method: 'POST', headers: { 'Content-Type':'application/x-www-form-urlencoded' }, body: new URLSearchParams({ recipe_id: id }) })
                            .then(r => {
                                // If we get redirected (login) the server will return 301; handle by checking r.redirected
                                if (r.redirected) { window.location = r.url; return; }
                                return r.json().catch(()=>null);
                            })
                            .then(j => {
                                if (!j) return;
                                if (j.success) {
                                    if (j.added) { btn.classList.add('added'); favSet.add(id); }
                                    else { btn.classList.remove('added'); favSet.delete(id); }
                                } else if (j.error) {
                                    alert(j.error);
                                }
                            }).catch(()=>{ alert('Network error'); });
                    });
                });
            }

            // Load ids only if the endpoint exists
            fetch(idsUrl, { method: 'GET', headers: { 'Accept': 'application/json' } })
                .then(r => r.json().catch(()=>null))
                .then(j => { if (j && Array.isArray(j.ids)) { j.ids.forEach(i=>favSet.add(parseInt(i,10))); } })
                .finally(()=>initButtons());
        });
    })();
</script>
