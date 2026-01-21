(function(){
    if (typeof document === 'undefined') return;
    document.addEventListener('DOMContentLoaded', function(){
        // Image preview
        (function(){
            var imgInput = document.getElementById('recipe-image');
            var imgPreview = document.getElementById('recipe-image-preview');
            if (!imgInput || !imgPreview) return;
            function updatePreview() {
                var url = imgInput.value && imgInput.value.trim();
                if (!url) { imgPreview.style.display = 'none'; imgPreview.removeAttribute('src'); return; }
                imgPreview.style.display = 'block';
                imgPreview.src = url;
                imgPreview.onerror = function () { imgPreview.style.display = 'none'; };
            }
            imgInput.addEventListener('input', updatePreview);
            updatePreview();
        })();

        // Delete buttons and row selection
        (function(){
            var deleteUrl = window.AdminConfig && window.AdminConfig.deleteUrl;

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
                        if (tr.classList.contains('table-active')) {
                            tr.classList.remove('table-active');
                            if (tr.closest('#recipes')) {
                                let rt = document.getElementById('recipe-title'); if (rt) rt.value = '';
                                let rc = document.getElementById('recipe-category'); if (rc) rc.value = '';
                                let ri = document.getElementById('recipe-instructions'); if (ri) ri.value = '';
                                let rp = document.getElementById('recipe-preptime'); if (rp) rp.value = '';
                                let rs = document.getElementById('recipe-serving-size'); if (rs) rs.value = '';
                                let rim = document.getElementById('recipe-image'); if (rim) rim.value = '';
                                let rimPrev = document.getElementById('recipe-image-preview'); if (rimPrev) { rimPrev.style.display = 'none'; rimPrev.removeAttribute('src'); }
                                return;
                            }
                            if (tr.closest('#ingredients')) {
                                let iname = document.getElementById('ingredient-name'); if (iname) iname.value = '';
                                let icat = document.getElementById('ingredient-category'); if (icat) icat.selectedIndex = 0;
                                return;
                            }
                            if (tr.closest('#users')) {
                                let un = document.getElementById('user-username'); if (un) un.value = '';
                                let ue = document.getElementById('user-email'); if (ue) ue.value = '';
                                let ur = document.getElementById('user-role'); if (ur) ur.value = 'USER';
                                let upw = document.getElementById('user-password'); if (upw) upw.value = '';
                                return;
                            }
                        }

                        if (tr.closest('#recipes')) {
                            let rid = tr.getAttribute('data-id'); if (!rid) return;
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

                        if (tr.closest('#ingredients')) {
                            let iid = tr.getAttribute('data-id'); if (!iid) return;
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

                        if (tr.closest('#users')) {
                            let uid = tr.getAttribute('data-user-id'); if (!uid) return;
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

            wireDeleteButtons(document);
            wireRowSelection(document);
            window._adminWire = { wireDeleteButtons: wireDeleteButtons, wireRowSelection: wireRowSelection };
        })();

        // Save handlers and insertion helpers
        (function(){
            var saveUrl = window.AdminConfig && window.AdminConfig.saveUrl;

            function insertRecipeRow(model) {
                var tbody = document.querySelector('#recipes tbody'); if (!tbody) return;
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
                window._adminWire.wireDeleteButtons(tr);
                window._adminWire.wireRowSelection(tr);
            }

            function insertIngredientRow(model) {
                var tbody = document.querySelector('#ingredients tbody'); if (!tbody) return;
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
                var tbody = document.querySelector('#users tbody'); if (!tbody) return;
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

            var recipeSaveBtn = document.querySelector('#adminRecipeForm button.btn-primary');
            if (recipeSaveBtn) recipeSaveBtn.addEventListener('click', function () {
                var titleEl = document.getElementById('recipe-title');
                var catEl = document.getElementById('recipe-category');
                var instrEl = document.getElementById('recipe-instructions');
                var timeEl = document.getElementById('recipe-preptime');
                var servingEl = document.getElementById('recipe-serving-size');
                var imgEl = document.getElementById('recipe-image');
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
                                sel.setAttribute('data-title', titleEl.value);
                                sel.setAttribute('data-category', catEl.value);
                                sel.setAttribute('data-time', timeEl.value);
                                sel.setAttribute('data-serving', servingEl.value);
                                sel.setAttribute('data-image', imgEl.value);
                                sel.setAttribute('data-instructions', instrEl.value);
                                sel.querySelector('td:nth-child(2)').textContent = titleEl.value;
                                sel.querySelector('td:nth-child(3)').textContent = catEl.value;
                                sel.querySelector('td:nth-child(4)').textContent = (timeEl.value ? (timeEl.value + ' min') : '');
                            } else { if (j.model) insertRecipeRow(j.model); }
                        } else { alert(j?.error || 'Failed to save'); }
                    }).catch(function () { alert('Network error'); });
            });

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
                            } else { if (j.model) insertIngredientRow(j.model); }
                        } else { alert(j?.error || 'Failed to save'); }
                    }).catch(function () { alert('Network error'); });
            });

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
                            } else { if (j.model) insertUserRow(j.model); }
                        } else { alert(j?.error || 'Failed to save'); }
                    }).catch(function () { alert('Network error'); });
            });
        })();
    });
})();
