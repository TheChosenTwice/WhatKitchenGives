// AI-generated: This file contains code generated with AI assistance.
(function(){
    if (typeof document === 'undefined') return;
    document.addEventListener('DOMContentLoaded', function(){
        const cfg = window.FavouriteConfig || {};
        const idsUrl = cfg.idsUrl;
        const toggleUrl = cfg.toggleUrl;

        let favSet = new Set();

        function initButtons(){
            document.querySelectorAll('.favourite-btn').forEach(btn => {
                const id = parseInt(btn.getAttribute('data-recipe-id'),10);
                if (!isFinite(id)) return;
                if (favSet.has(id)) btn.classList.add('added');
                const initiallyAdded = btn.classList.contains('added');
                btn.addEventListener('click', function(e){
                    e.stopPropagation();
                    fetch(toggleUrl, { method: 'POST', headers: { 'Content-Type':'application/x-www-form-urlencoded' }, body: new URLSearchParams({ recipe_id: id }) })
                        .then(r => {
                            if (r.redirected) { window.location = r.url; return; }
                            return r.json().catch(()=>null);
                        })
                        .then(j => {
                            if (!j) return;
                            if (j.success) {
                                if (j.added) { btn.classList.add('added'); favSet.add(id); }
                                else { btn.classList.remove('added'); favSet.delete(id); if (initiallyAdded) { btn.closest('.col-12')?.remove(); } }
                            } else if (j.error) {
                                alert(j.error);
                            }
                        }).catch(()=>{ alert('Network error'); });
                });
            });
        }

        if (idsUrl) {
            fetch(idsUrl, { method: 'GET', headers: { 'Accept': 'application/json' } })
                .then(r => r.json().catch(()=>null))
                .then(j => { if (j && Array.isArray(j.ids)) { j.ids.forEach(i=>{ const n = parseInt(i,10); if (isFinite(n)) favSet.add(n); }); } })
                .finally(()=>initButtons());
        } else {
            // Seed from existing buttons that already carry .added
            document.querySelectorAll('.favourite-btn.added').forEach(b=>{ const n = parseInt(b.getAttribute('data-recipe-id'),10); if (isFinite(n)) favSet.add(n); });
            initButtons();
        }
    });
})();
