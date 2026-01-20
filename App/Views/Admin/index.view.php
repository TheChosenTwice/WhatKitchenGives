<?php

/** @var \Framework\Support\LinkGenerator $link */
/** @var \Framework\Auth\AppUser $user */
/** @var \App\Models\Recipe[] $recipes */
/** @var \App\Models\Ingredient[] $ingredients */
/** @var \App\Models\User[] $users */
/** @var \App\Models\Category[] $categories */

?>

<!-- Hide the application's root navbar for this admin view via JS (no inline CSS remains) -->
<script>
    (function () {
        if (typeof document !== 'undefined') {
            document.addEventListener('DOMContentLoaded', function () {
                var nav = document.querySelector('nav.navbar');
                if (nav) { nav.style.display = 'none'; }
            });
        }
    })();
</script>

<!-- Admin Panel Visual (no backend logic) -->
<div class="admin-panel container-fluid">
    <!-- Header -->
    <div class="row mb-3 align-items-center admin-header">
        <div class="col-md-3 d-flex align-items-center">
            <a href="<?= $link->url('home.index') ?>">
                <img src="<?= $link->asset('images/vaiicko_logo.png') ?>" alt="Logo" style="height:48px;"/>
            </a>
            <h4 class="ms-2 mb-0">Admin Panel</h4>
        </div>
        <div class="col-md-6">
            <!-- Top header menu intentionally removed for admin panel (sidebar + tabs are used instead) -->
            <div class="text-center">
                <!-- space reserved for header center area -->
            </div>
        </div>
        <div class="col-md-3 text-end">
            <span class="me-3">Signed in as <strong><?= $user->getName() ?></strong></span>
            <a class="btn btn-outline-secondary btn-sm" href="<?= $link->url('auth.logout') ?>">Logout</a>
        </div>
    </div>

    <div class="row">
        <!-- Main Content (sidebar removed; main expanded) -->
        <main class="col-md-12">
            <!-- Dashboard cards -->
            <div class="row mb-3">
                <div class="col-sm-4">
                    <div class="card text-white bg-primary mb-3">
                        <div class="card-body">
                            <h5 class="card-title">Recipes</h5>
                            <p class="card-text display-6"><?= isset($recipesCount) ? (int)$recipesCount : 0 ?></p>
                        </div>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="card text-white bg-success mb-3">
                        <div class="card-body">
                            <h5 class="card-title">Ingredients</h5>
                            <p class="card-text display-6"><?= isset($ingredientsCount) ? (int)$ingredientsCount : 0 ?></p>
                        </div>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="card text-white bg-secondary mb-3">
                        <div class="card-body">
                            <h5 class="card-title">Users</h5>
                            <p class="card-text display-6"><?= isset($usersCount) ? (int)$usersCount : 0 ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabs for Recipes / Ingredients -->
            <ul class="nav nav-tabs" id="adminTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="recipes-tab" data-bs-toggle="tab" data-bs-target="#recipes" type="button" role="tab">Recipes</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="ingredients-tab" data-bs-toggle="tab" data-bs-target="#ingredients" type="button" role="tab">Ingredients</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="users-tab" data-bs-toggle="tab" data-bs-target="#users" type="button" role="tab">Users</button>
                </li>
            </ul>
            <div class="tab-content mt-3">
                <!-- Recipes Tab -->
                <div class="tab-pane fade show active" id="recipes" role="tabpanel" aria-labelledby="recipes-tab">
                    <div class="row">
                        <div class="col-lg-8 mb-3">
                            <div id="all-recipes" class="card">
                                <div class="card-header d-flex align-items-center justify-content-between">
                                    <div>All Recipes</div>
                                    <div style="width:260px;">
                                        <label class="visually-hidden" for="recipes-search">Search recipes</label>
                                        <input id="recipes-search" type="search" class="form-control form-control-sm" placeholder="Search recipes...">
                                    </div>
                                </div>
                                 <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-striped mb-0">
                                            <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Title</th>
                                                <th>Category</th>
                                                <th>Time</th>
                                                <th>Actions</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <?php if (!empty($recipes)) { ?>
                                                <?php foreach ($recipes as $idx => $r) {
                                                    // $r is an instance of App\Models\Recipe
                                                    $id = method_exists($r, 'getId') ? $r->getId() : null;
                                                    $title = method_exists($r, 'getTitle') ? $r->getTitle() : '';
                                                    $category = method_exists($r, 'getCategory') ? $r->getCategory() : '';
                                                    $time = method_exists($r, 'getCookingTime') ? $r->getCookingTime() : null;
                                                ?>
                                                    <tr data-id="<?= (int)$id ?>" data-title="<?= htmlspecialchars((string)$title, ENT_QUOTES) ?>" data-category="<?= htmlspecialchars((string)$category, ENT_QUOTES) ?>" data-time="<?= $time !== null ? (int)$time : '' ?>" data-serving="<?= method_exists($r,'getServingSize') && $r->getServingSize() !== null ? (int)$r->getServingSize() : '' ?>" data-image="<?= htmlspecialchars((string)(method_exists($r,'getImage') ? $r->getImage() : ''), ENT_QUOTES) ?>" data-instructions="<?= htmlspecialchars((string)(method_exists($r,'getInstructions') ? $r->getInstructions() : ''), ENT_QUOTES) ?>">
                                                        <td><?= htmlspecialchars((int)$id) ?></td>
                                                        <td><?= htmlspecialchars((string)$title) ?></td>
                                                        <td><?= htmlspecialchars((string)$category) ?></td>
                                                        <td><?= $time !== null ? htmlspecialchars((string)($time . ' min')) : '' ?></td>
                                                        <td>
                                                            <button type="button" class="btn btn-sm btn-outline-danger admin-delete" data-type="recipe" data-id="<?= (int)$id ?>">Delete</button>
                                                        </td>
                                                    </tr>
                                                <?php } ?>
                                            <?php } else { ?>
                                                <tr>
                                                    <td colspan="5" class="text-center text-muted">No recipes found.</td>
                                                </tr>
                                            <?php } ?>
                                         </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-4 mb-3">
                            <div id="add-recipe" class="card">
                                <div class="card-header">Add / Edit Recipe</div>
                                <div class="card-body">
                                    <form id="adminRecipeForm" enctype="multipart/form-data">
                                        <div class="mb-2">
                                            <label class="form-label" for="recipe-title">Title</label>
                                            <input id="recipe-title" class="form-control" type="text" placeholder="Recipe title">
                                        </div>

                                        <div class="mb-2">
                                            <label class="form-label" for="recipe-category">Category</label>
                                            <input id="recipe-category" class="form-control" type="text" placeholder="Category">
                                        </div>

                                        <div class="mb-2">
                                            <label class="form-label" for="recipe-instructions">Instructions</label>
                                            <textarea id="recipe-instructions" class="form-control" rows="6" placeholder="Step-by-step instructions (visual only)"></textarea>
                                        </div>

                                        <div class="row">
                                            <div class="col-sm-6 mb-2">
                                                <label class="form-label" for="recipe-preptime">Cooking time (minutes)</label>
                                                <input id="recipe-preptime" class="form-control" type="number" min="0" placeholder="e.g. 30">
                                            </div>
                                            <div class="col-sm-6 mb-2">
                                                <label class="form-label" for="recipe-serving-size">Serving size</label>
                                                <input id="recipe-serving-size" class="form-control" type="number" min="1" placeholder="e.g. 4">
                                            </div>
                                        </div>

                                        <div class="mb-2">
                                            <label class="form-label" for="recipe-image">Image URL</label>
                                            <input id="recipe-image" class="form-control" type="text" placeholder="Image URL or path (visual only)">
                                            <small class="form-text text-muted">Paste an image URL to preview below (visual only).</small>
                                            <div class="mt-2 text-center">
                                                <img id="recipe-image-preview" src="<?= $link->asset('images/vaiicko_logo.png') ?>" alt="Preview" style="max-width:100%; max-height:160px; object-fit:contain; display:none;">
                                            </div>
                                        </div>

                                        <div class="mb-2">
                                            <label class="form-label" for="recipe-ingredients">Ingredients</label>
                                            <textarea id="recipe-ingredients" class="form-control" rows="3" placeholder="List ingredient IDs or names (visual only)"></textarea>
                                        </div>

                                        <div class="d-grid">
                                            <button type="button" class="btn btn-primary">Save</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Ingredients Tab -->
                <div class="tab-pane fade" id="ingredients" role="tabpanel" aria-labelledby="ingredients-tab">
                    <div class="row">
                        <div class="col-lg-8 mb-3">
                            <div id="all-ingredients" class="card">
                                <div class="card-header d-flex align-items-center justify-content-between">
                                    <div>All Ingredients</div>
                                    <div style="width:260px;">
                                        <label class="visually-hidden" for="ingredients-search">Search ingredients</label>
                                        <input id="ingredients-search" type="search" class="form-control form-control-sm" placeholder="Search ingredients...">
                                    </div>
                                </div>
                                 <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-striped mb-0">
                                            <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Name</th>
                                                <th>Category</th>
                                                <th>Actions</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <?php if (!empty($ingredients)) { ?>
                                                <?php foreach ($ingredients as $ing) {
                                                    $iid = method_exists($ing, 'getId') ? $ing->getId() : null;
                                                    $iname = method_exists($ing, 'getName') ? $ing->getName() : '';
                                                    $icategory = method_exists($ing, 'getCategory') ? $ing->getCategory() : '';
                                                ?>
                                                    <tr data-id="<?= (int)$iid ?>" data-name="<?= htmlspecialchars((string)$iname, ENT_QUOTES) ?>" data-category="<?= htmlspecialchars((string)$icategory, ENT_QUOTES) ?>">
                                                        <td><?= htmlspecialchars((int)$iid) ?></td>
                                                        <td><?= htmlspecialchars((string)$iname) ?></td>
                                                        <td><?= htmlspecialchars((string)$icategory) ?></td>
                                                        <td>
                                                            <button type="button" class="btn btn-sm btn-outline-danger admin-delete" data-type="ingredient" data-id="<?= (int)$iid ?>">Delete</button>
                                                        </td>
                                                    </tr>
                                                <?php } ?>
                                            <?php } else { ?>
                                                <tr>
                                                    <td colspan="4" class="text-center text-muted">No ingredients found.</td>
                                                </tr>
                                            <?php } ?>
                                         </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-4 mb-3">
                            <div id="add-ingredient" class="card">
                                <div class="card-header">Add / Edit Ingredient</div>
                                <div class="card-body">
                                    <form>
                                        <div class="mb-2">
                                            <label class="form-label" for="ingredient-name">Name</label>
                                            <input id="ingredient-name" class="form-control" type="text" placeholder="Ingredient name">
                                        </div>

                                        <div class="mb-2">
                                            <label class="form-label" for="ingredient-category">Category</label>
                                            <select id="ingredient-category" class="form-select">
                                                <option value="">Select category</option>
                                                <?php if (!empty($categories)) { ?>
                                                    <?php foreach ($categories as $category) {
                                                        $cid = method_exists($category, 'getId') ? $category->getId() : null;
                                                        $cname = method_exists($category, 'getName') ? $category->getName() : '';
                                                    ?>
                                                        <option value="<?= htmlspecialchars((int)$cid) ?>"><?= htmlspecialchars((string)$cname) ?></option>
                                                    <?php } ?>
                                                <?php } ?>
                                            </select>
                                        </div>

                                        <div class="d-grid">
                                            <button type="button" class="btn btn-success">Save</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Users Tab (two-column: list + Add/Edit panel) -->
                <div class="tab-pane fade" id="users" role="tabpanel" aria-labelledby="users-tab">
                    <div class="row">
                        <div class="col-lg-8 mb-3">
                            <div id="all-users" class="card">
                                <div class="card-header d-flex align-items-center justify-content-between">
                                    <div>All Users</div>
                                    <div style="width:260px;">
                                        <label class="visually-hidden" for="users-search">Search users</label>
                                        <input id="users-search" type="search" class="form-control form-control-sm" placeholder="Search users...">
                                    </div>
                                </div>
                                 <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-striped mb-0">
                                            <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Username</th>
                                                <th>Email Address</th>
                                                <th>Member Since</th>
                                                <th>Role</th>
                                                <th>Actions</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <?php if (!empty($users)) { ?>
                                                <?php foreach ($users as $u) {
                                                    $uid = method_exists($u, 'getId') ? $u->getId() : null;
                                                    $uname = method_exists($u, 'getUsername') ? $u->getUsername() : (method_exists($u,'getName') ? $u->getName() : '');
                                                    $uemail = method_exists($u, 'getEmail') ? $u->getEmail() : '';
                                                    $ureg = method_exists($u, 'getCreatedAt') ? $u->getCreatedAt() : '';
                                                    $urole = method_exists($u, 'getRole') ? $u->getRole() : 'USER';
                                                ?>
                                                    <tr
                                                        data-user-id="<?= (int)$uid ?>"
                                                        data-username="<?= htmlspecialchars((string)$uname, ENT_QUOTES) ?>"
                                                        data-email="<?= htmlspecialchars((string)$uemail, ENT_QUOTES) ?>"
                                                        data-role="<?= htmlspecialchars((string)$urole, ENT_QUOTES) ?>"
                                                    >
                                                        <td><?= htmlspecialchars((int)$uid) ?></td>
                                                        <td><?= htmlspecialchars((string)$uname) ?></td>
                                                        <td><?= htmlspecialchars((string)$uemail) ?></td>
                                                        <td><?= $ureg ? htmlspecialchars((string)$ureg) : '' ?></td>
                                                        <td><?= htmlspecialchars((string)$urole) ?></td>
                                                        <td>
                                                            <button type="button" class="btn btn-sm btn-outline-danger admin-delete" data-type="user" data-id="<?= (int)$uid ?>">Delete</button>
                                                        </td>
                                                    </tr>
                                                <?php } ?>
                                            <?php } else { ?>
                                                <tr>
                                                    <td colspan="6" class="text-center text-muted">No users found.</td>
                                                </tr>
                                            <?php } ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-4 mb-3">
                            <div id="add-user" class="card">
                                <div class="card-header">Add / Edit User</div>
                                <div class="card-body">
                                    <form id="adminUserForm">
                                        <div class="mb-2">
                                            <label class="form-label" for="user-username">Username</label>
                                            <input id="user-username" class="form-control" type="text" placeholder="Username">
                                        </div>
                                        <div class="mb-2">
                                            <label class="form-label" for="user-email">Email</label>
                                            <input id="user-email" class="form-control" type="email" placeholder="Email address">
                                        </div>
                                        <div class="mb-2">
                                            <label class="form-label" for="user-role">Role</label>
                                            <select id="user-role" class="form-select">
                                                <option value="USER">User</option>
                                                <option value="ADMIN">Admin</option>
                                            </select>
                                        </div>
                                        <div class="mb-2">
                                            <label class="form-label" for="user-password">Password</label>
                                            <input id="user-password" class="form-control" type="password" placeholder="Password (leave empty to keep)">
                                        </div>
                                        <div class="d-grid">
                                            <button type="button" class="btn btn-primary">Save</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </main>
    </div>
</div>

<!-- Admin styles have been migrated to public/css/styl.css -->

<script>
    // Small UX: show preview when an image URL is pasted/typed
    (function () {
        if (typeof document === 'undefined') return;
        document.addEventListener('DOMContentLoaded', function () {
            var imgInput = document.getElementById('recipe-image');
            var imgPreview = document.getElementById('recipe-image-preview');
            if (!imgInput || !imgPreview) return;
            function updatePreview() {
                var url = imgInput.value && imgInput.value.trim();
                if (!url) {
                    imgPreview.style.display = 'none';
                    imgPreview.removeAttribute('src');
                    return;
                }
                // optimistic set - if image fails to load we hide it
                imgPreview.style.display = 'block';
                imgPreview.src = url;
                imgPreview.onerror = function () { imgPreview.style.display = 'none'; };
            }
            imgInput.addEventListener('input', updatePreview);
            // set initial state if form prefilled
            updatePreview();
        });
    })();
</script>

<script>
    // Attach delete handlers for recipes, ingredients and users
    (function () {
        if (typeof document === 'undefined') return;
        document.addEventListener('DOMContentLoaded', function () {
            var deleteUrl = '<?= $link->url("admin.delete") ?>';
            function wireDeleteButtons(root) {
                root.querySelectorAll('button.admin-delete').forEach(function (btn) {
                    if (btn._wired) return; btn._wired = true;
                    btn.addEventListener('click', function (e) {
                        e.stopPropagation();
                        var type = btn.getAttribute('data-type');
                        var id = parseInt(btn.getAttribute('data-id'), 10);
                        if (!type || !isFinite(id) || id <= 0) return alert('Invalid item');
                        if (!confirm('Delete ' + type + ' #' + id + '?')) return;
                        btn.disabled = true;
                        fetch(deleteUrl, { method: 'POST', headers: { 'Content-Type': 'application/x-www-form-urlencoded' }, body: new URLSearchParams({ type: type, id: id }) })
                            .then(r => r.json())
                            .then(j => {
                                if (j && j.success) { btn.closest('tr')?.remove(); } else { alert(j?.error || 'Failed to delete'); btn.disabled = false; }
                            }).catch(function () { alert('Network error'); btn.disabled = false; });
                    });
                });
            }

            function wireRowSelection(root) {
                root.querySelectorAll('table tbody tr').forEach(function (tr) {
                    if (tr._wired) return; tr._wired = true;
                    tr.addEventListener('click', function () {
                        // If this row is already active -> deselect and clear panel
                        if (tr.classList.contains('table-active')) {
                            tr.classList.remove('table-active');

                            // Clear recipes panel
                            if (tr.closest('#recipes')) {
                                let rt = document.getElementById('recipe-title');
                                if (rt) rt.value = '';
                                let rc = document.getElementById('recipe-category'); if (rc) rc.value = '';
                                let ri = document.getElementById('recipe-instructions'); if (ri) ri.value = '';
                                let rp = document.getElementById('recipe-preptime'); if (rp) rp.value = '';
                                let rs = document.getElementById('recipe-serving-size'); if (rs) rs.value = '';
                                let rim = document.getElementById('recipe-image'); if (rim) rim.value = '';
                                let rimPrev = document.getElementById('recipe-image-preview'); if (rimPrev) { rimPrev.style.display = 'none'; rimPrev.removeAttribute('src'); }
                                return;
                            }

                            // Clear ingredients panel
                            if (tr.closest('#ingredients')) {
                                let iname = document.getElementById('ingredient-name'); if (iname) iname.value = '';
                                let icat = document.getElementById('ingredient-category'); if (icat) icat.selectedIndex = 0;
                                return;
                            }

                            // Clear users panel
                            if (tr.closest('#users')) {
                                let un = document.getElementById('user-username'); if (un) un.value = '';
                                let ue = document.getElementById('user-email'); if (ue) ue.value = '';
                                let ur = document.getElementById('user-role'); if (ur) ur.value = 'USER';
                                let upw = document.getElementById('user-password'); if (upw) upw.value = '';
                                return;
                            }
                        }

                        // Recipes selection
                        if (tr.closest('#recipes')) {
                            let rid = tr.getAttribute('data-id');
                            if (!rid) return;
                            let title = tr.getAttribute('data-title') || '';
                            let category = tr.getAttribute('data-category') || '';
                            let time = tr.getAttribute('data-time') || '';
                            let serving = tr.getAttribute('data-serving') || '';
                            let image = tr.getAttribute('data-image') || '';
                            let instructions = tr.getAttribute('data-instructions') || '';

                            let rt = document.getElementById('recipe-title'); if (rt) rt.value = title;
                            let rc = document.getElementById('recipe-category'); if (rc) rc.value = category;
                            let ri = document.getElementById('recipe-instructions'); if (ri) ri.value = instructions;
                            let rp = document.getElementById('recipe-preptime'); if (rp) rp.value = time;
                            let rs = document.getElementById('recipe-serving-size'); if (rs) rs.value = serving;
                            let rim = document.getElementById('recipe-image'); if (rim) rim.value = image;

                            let recipesTab = document.getElementById('recipes-tab');
                            if (recipesTab && recipesTab.classList.contains('active') === false) recipesTab.click();
                            document.querySelectorAll('#recipes tbody tr').forEach(r => r.classList.remove('table-active'));
                            tr.classList.add('table-active');
                            return;
                        }

                        // Ingredients
                        if (tr.closest('#ingredients')) {
                            let iid = tr.getAttribute('data-id');
                            if (!iid) return;
                            let iname = tr.getAttribute('data-name') || '';
                            let icat = tr.getAttribute('data-category') || '';

                            let inameEl = document.getElementById('ingredient-name'); if (inameEl) inameEl.value = iname;
                            let select = document.getElementById('ingredient-category');
                            if (select) {
                                for (let i = 0; i < select.options.length; i++) {
                                    if (select.options[i].text === icat) { select.selectedIndex = i; break; }
                                }
                            }
                            let ingTab = document.getElementById('ingredients-tab');
                            if (ingTab && ingTab.classList.contains('active') === false) ingTab.click();
                            document.querySelectorAll('#ingredients tbody tr').forEach(r => r.classList.remove('table-active'));
                            tr.classList.add('table-active');
                            return;
                        }

                        // Users
                        if (tr.closest('#users')) {
                            let uid = tr.getAttribute('data-user-id');
                            if (!uid) return;
                            let uname = tr.getAttribute('data-username') || '';
                            let uemail = tr.getAttribute('data-email') || '';
                            let urole = tr.getAttribute('data-role') || 'USER';

                            let un = document.getElementById('user-username'); if (un) un.value = uname;
                            let ue = document.getElementById('user-email'); if (ue) ue.value = uemail;
                            let roleSelect = document.getElementById('user-role'); if (roleSelect) roleSelect.value = urole;

                            let usersTab = document.getElementById('users-tab');
                            if (usersTab && usersTab.classList.contains('active') === false) usersTab.click();
                            document.querySelectorAll('#users tbody tr').forEach(r => r.classList.remove('table-active'));
                            tr.classList.add('table-active');
                        }
                    });
                });
            }

            // Initial wiring
            wireDeleteButtons(document);
            wireRowSelection(document);

            // Expose helpers for later when adding rows dynamically
            window._adminWire = { wireDeleteButtons: wireDeleteButtons, wireRowSelection: wireRowSelection };
        });
    })();
</script>

<script>
    // Attach save handlers for the edit panels (supports create when no row selected)
    (function () {
        if (typeof document === 'undefined') return;
        document.addEventListener('DOMContentLoaded', function () {
            var saveUrl = '<?= $link->url("admin.save") ?>';

            function insertRecipeRow(model) {
                // model expected to have id, title, category, cooking_time
                var tbody = document.querySelector('#recipes tbody');
                if (!tbody) return;
                var tr = document.createElement('tr');
                tr.setAttribute('data-id', model.id ?? '');
                tr.setAttribute('data-title', model.title ?? '');
                tr.setAttribute('data-category', model.category ?? '');
                tr.setAttribute('data-time', model.cooking_time ?? '');
                tr.setAttribute('data-serving', model.serving_size ?? '');
                tr.setAttribute('data-image', model.image ?? '');
                tr.setAttribute('data-instructions', model.instructions ?? '');
                tr.innerHTML = '<td>' + (model.id ?? '') + '</td>' +
                    '<td>' + (model.title ?? '') + '</td>' +
                    '<td>' + (model.category ?? '') + '</td>' +
                    '<td>' + ((model.cooking_time ? (model.cooking_time + ' min') : '')) + '</td>' +
                    '<td><button type="button" class="btn btn-sm btn-outline-danger admin-delete" data-type="recipe" data-id="' + (model.id ?? '') + '">Delete</button></td>';
                tbody.insertBefore(tr, tbody.firstChild);
                // wire events
                window._adminWire.wireDeleteButtons(tr);
                window._adminWire.wireRowSelection(tr);
            }

            function insertIngredientRow(model) {
                var tbody = document.querySelector('#ingredients tbody');
                if (!tbody) return;
                var tr = document.createElement('tr');
                tr.setAttribute('data-id', model.id ?? '');
                tr.setAttribute('data-name', model.name ?? '');
                tr.setAttribute('data-category', model.category ?? '');
                tr.innerHTML = '<td>' + (model.id ?? '') + '</td>' +
                    '<td>' + (model.name ?? '') + '</td>' +
                    '<td>' + (model.category ?? '') + '</td>' +
                    '<td><button type="button" class="btn btn-sm btn-outline-danger admin-delete" data-type="ingredient" data-id="' + (model.id ?? '') + '">Delete</button></td>';
                tbody.insertBefore(tr, tbody.firstChild);
                window._adminWire.wireDeleteButtons(tr);
                window._adminWire.wireRowSelection(tr);
            }

            function insertUserRow(model) {
                var tbody = document.querySelector('#users tbody');
                if (!tbody) return;
                var tr = document.createElement('tr');
                tr.setAttribute('data-user-id', model.id ?? '');
                tr.setAttribute('data-username', model.username ?? '');
                tr.setAttribute('data-email', model.email ?? '');
                tr.setAttribute('data-role', model.role ?? 'USER');
                tr.innerHTML = '<td>' + (model.id ?? '') + '</td>' +
                    '<td>' + (model.username ?? '') + '</td>' +
                    '<td>' + (model.email ?? '') + '</td>' +
                    '<td></td>' +
                    '<td>' + (model.role ?? '') + '</td>' +
                    '<td><button type="button" class="btn btn-sm btn-outline-danger admin-delete" data-type="user" data-id="' + (model.id ?? '') + '">Delete</button></td>';
                tbody.insertBefore(tr, tbody.firstChild);
                window._adminWire.wireDeleteButtons(tr);
                window._adminWire.wireRowSelection(tr);
            }

            // Recipes save
            var recipeSaveBtn = document.querySelector('#adminRecipeForm button.btn-primary');
            if (recipeSaveBtn) recipeSaveBtn.addEventListener('click', function () {
                var titleEl = document.getElementById('recipe-title');
                var catEl = document.getElementById('recipe-category');
                var instrEl = document.getElementById('recipe-instructions');
                var timeEl = document.getElementById('recipe-preptime');
                var servingEl = document.getElementById('recipe-serving-size');
                var imgEl = document.getElementById('recipe-image');
                // Find selected row id
                var sel = document.querySelector('#recipes tbody tr.table-active');
                var isUpdate = !!sel;
                var payload = new URLSearchParams({ type: 'recipe' });
                if (isUpdate) payload.set('id', sel.getAttribute('data-id'));
                if (titleEl) payload.set('title', titleEl.value);
                if (instrEl) payload.set('instructions', instrEl.value);
                if (catEl) payload.set('category', catEl.value);
                if (timeEl) payload.set('cooking_time', timeEl.value);
                if (servingEl) payload.set('serving_size', servingEl.value);
                if (imgEl) payload.set('image', imgEl.value);
                fetch(saveUrl, { method: 'POST', headers: { 'Content-Type': 'application/x-www-form-urlencoded' }, body: payload })
                    .then(r => r.json())
                    .then(j => {
                        if (j && j.success) {
                            alert('Recipe saved');
                            if (isUpdate) {
                                // Update row attributes and visible cells
                                sel.setAttribute('data-title', titleEl.value);
                                sel.setAttribute('data-category', catEl.value);
                                sel.setAttribute('data-time', timeEl.value);
                                sel.setAttribute('data-serving', servingEl.value);
                                sel.setAttribute('data-image', imgEl.value);
                                sel.setAttribute('data-instructions', instrEl.value);
                                // update displayed cells: title and category and time
                                sel.querySelector('td:nth-child(2)').textContent = titleEl.value;
                                sel.querySelector('td:nth-child(3)').textContent = catEl.value;
                                sel.querySelector('td:nth-child(4)').textContent = (timeEl.value ? (timeEl.value + ' min') : '');
                            } else {
                                // Insert new row using returned model
                                if (j.model) insertRecipeRow(j.model);
                            }
                        } else {
                            alert(j?.error || 'Failed to save');
                        }
                    }).catch(function () { alert('Network error'); });
            });

            // Ingredients save
            var ingSaveBtn = document.querySelector('#add-ingredient button.btn-success');
            if (ingSaveBtn) ingSaveBtn.addEventListener('click', function () {
                var nameEl = document.getElementById('ingredient-name');
                var catEl = document.getElementById('ingredient-category');
                var sel = document.querySelector('#ingredients tbody tr.table-active');
                var isUpdate = !!sel;
                var payload = new URLSearchParams({ type: 'ingredient' });
                if (isUpdate) payload.set('id', sel.getAttribute('data-id'));
                if (nameEl) payload.set('name', nameEl.value);
                if (catEl) payload.set('category_id', catEl.value);
                fetch(saveUrl, { method: 'POST', headers: { 'Content-Type': 'application/x-www-form-urlencoded' }, body: payload })
                    .then(r => r.json())
                    .then(j => {
                        if (j && j.success) {
                            alert('Ingredient saved');
                            if (isUpdate) {
                                sel.setAttribute('data-name', nameEl.value);
                                sel.setAttribute('data-category', catEl.options[catEl.selectedIndex].text);
                                sel.querySelector('td:nth-child(2)').textContent = nameEl.value;
                                sel.querySelector('td:nth-child(3)').textContent = catEl.options[catEl.selectedIndex].text;
                            } else {
                                if (j.model) insertIngredientRow(j.model);
                            }
                        } else { alert(j?.error || 'Failed to save'); }
                    }).catch(function () { alert('Network error'); });
            });

            // Users save
            var userSaveBtn = document.querySelector('#adminUserForm button.btn-primary');
            if (userSaveBtn) userSaveBtn.addEventListener('click', function () {
                var uName = document.getElementById('user-username');
                var uEmail = document.getElementById('user-email');
                var uRole = document.getElementById('user-role');
                var uPass = document.getElementById('user-password');
                var sel = document.querySelector('#users tbody tr.table-active');
                var isUpdate = !!sel;
                var payload = new URLSearchParams({ type: 'user' });
                if (isUpdate) payload.set('id', sel.getAttribute('data-user-id'));
                if (uName) payload.set('username', uName.value);
                if (uEmail) payload.set('email', uEmail.value);
                if (uRole) payload.set('role', uRole.value);
                if (uPass && uPass.value) payload.set('password', uPass.value);
                fetch(saveUrl, { method: 'POST', headers: { 'Content-Type': 'application/x-www-form-urlencoded' }, body: payload })
                    .then(r => r.json())
                    .then(j => {
                        if (j && j.success) {
                            alert('User saved');
                            if (isUpdate) {
                                sel.setAttribute('data-username', uName.value);
                                sel.setAttribute('data-email', uEmail.value);
                                sel.setAttribute('data-role', uRole.value);
                                sel.querySelector('td:nth-child(2)').textContent = uName.value;
                                sel.querySelector('td:nth-child(3)').textContent = uEmail.value;
                            } else {
                                if (j.model) insertUserRow(j.model);
                            }
                        } else { alert(j?.error || 'Failed to save'); }
                    }).catch(function () { alert('Network error'); });
            });
        });
    })();
</script>

<?php

