// Shared JavaScript for the app.
// Keep page-specific logic behind DOM guards so this file can be included everywhere.

(function () {
  'use strict';

  function initHomePageIngredients() {
    const container = document.querySelector('.home-page__ingredients');
    if (!container) return;

    const chips = Array.from(container.querySelectorAll('[data-ingredient-chip]'));
    const listEl = document.getElementById('selectedIngredientsList');
    const countEl = document.getElementById('selectedIngredientsCount');
    const clearBtn = document.getElementById('selectedIngredientsClear');
    const findRecipesBtn = document.getElementById('findRecipesBtn');

    function isPressed(chip) {
      return chip.getAttribute('aria-pressed') === 'true';
    }

    function setPressed(chip, pressed) {
      chip.setAttribute('aria-pressed', pressed ? 'true' : 'false');
      chip.classList.toggle('is-selected', pressed);
    }

    function getSelectedNames() {
      return chips
        .filter(isPressed)
        .map((chip) => chip.getAttribute('data-ingredient-name') || (chip.textContent || '').trim())
        .filter(Boolean)
        .sort((a, b) => a.localeCompare(b, undefined, { sensitivity: 'base' }));
    }

    function getSelectedIds() {
      return chips
        .filter(isPressed)
        .map((chip) => {
          const v = chip.getAttribute('data-ingredient-id');
          if (!v) return null;
          const n = Number(v);
          return Number.isFinite(n) && n > 0 ? Math.trunc(n) : null;
        })
        .filter((v) => v !== null);
    }

    function renderSelected() {
      if (!listEl || !countEl) return;

      const selected = getSelectedNames();
      countEl.textContent = `${selected.length} selected`;

      if (findRecipesBtn) {
        findRecipesBtn.disabled = selected.length === 0;
      }

      listEl.innerHTML = '';
      for (const name of selected) {
        const li = document.createElement('li');
        li.className = 'selected-ingredients__item';
        li.textContent = name;
        listEl.appendChild(li);
      }
    }

    function toggleChip(chip) {
      setPressed(chip, !isPressed(chip));
      renderSelected();
    }

    chips.forEach((chip) => {
      // Ensure initial style matches aria-pressed
      setPressed(chip, isPressed(chip));

      chip.addEventListener('click', () => toggleChip(chip));
      chip.addEventListener('keydown', (e) => {
        if (e.key === 'Enter' || e.key === ' ') {
          e.preventDefault();
          toggleChip(chip);
        }
      });
    });

    // Pool expand/collapse
    container.querySelectorAll('[data-pool-toggle]').forEach((btn) => {
      btn.addEventListener('click', () => {
        const expanded = btn.getAttribute('aria-expanded') === 'true';
        const pool = btn.closest('[data-ingredient-pool]');
        if (!pool) return;

        pool.querySelectorAll('[data-pool-hidden]').forEach((li) => {
          li.hidden = expanded; // currently expanded -> collapse to hidden
        });

        btn.setAttribute('aria-expanded', expanded ? 'false' : 'true');

        const hiddenCount = pool.querySelectorAll('[data-pool-hidden]').length;
        btn.textContent = expanded ? `+${hiddenCount} more` : 'Show less';
      });
    });

    if (clearBtn) {
      clearBtn.addEventListener('click', () => {
        chips.forEach((chip) => setPressed(chip, false));
        renderSelected();
      });
    }

    if (findRecipesBtn) {
      findRecipesBtn.addEventListener('click', () => {
        const baseUrl = findRecipesBtn.getAttribute('data-find-recipes-url') || '?';

        const ids = getSelectedIds();
        const names = getSelectedNames();

        const url = new URL(baseUrl, window.location.href);

        // Prefer IDs. If IDs are not available (e.g., DB not migrated), fall back to names.
        if (ids.length > 0) {
          ids.forEach((id) => url.searchParams.append('ingredient_ids[]', String(id)));
        } else {
          url.searchParams.set('ingredient_names', names.join(','));
        }

        window.location.assign(url.toString());
      });
    }

    renderSelected();
  }

  // script.js is loaded in <head> in both layouts, so wait for DOM.
  document.addEventListener('DOMContentLoaded', () => {
    initHomePageIngredients();
  });
})();
