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


<!-- extracted CSS moved to public/css/views-inline.css -->
<link rel="stylesheet" href="<?= $link->asset('css/views-inline.css') ?>">

<script>
    // Bootstrap config for external favourite script
    window.FavouriteConfig = {
        idsUrl: '<?= $link->url("favourite.ids") ?>',
        toggleUrl: '<?= $link->url("favourite.toggle") ?>'
    };
</script>

<script src="<?= $link->asset('js/favourite.js') ?>" defer></script>
