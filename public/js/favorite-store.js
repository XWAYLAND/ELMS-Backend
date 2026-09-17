// public/js/favorite-store.js
// Alpine global store for persistent favorite books (DB-backed per user)

document.addEventListener('alpine:init', () => {
  Alpine.store('favorites', {
    items: [],
    _loaded: false,

    async load() {
      if (this._loaded) return;
      if (!window.__isLoggedIn) { this._loaded = true; return; }
      try {
        const res = await fetch('/favorites/api', {
          headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        });
        if (res.ok) {
          this.items = await res.json();
        }
      } catch (_) { /* offline */ }
      this._loaded = true;
    },

    async toggle(isbn) {
      const token = document.querySelector('meta[name="csrf-token"]')?.content;
      if (!token) return; // not logged in

      try {
        const res = await fetch('/favorites/toggle', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': token,
            'X-Requested-With': 'XMLHttpRequest',
          },
          body: JSON.stringify({ isbn }),
        });

        if (!res.ok) return;

        const data = await res.json();
        if (data.action === 'added') {
          this.items.push(isbn);
          Alpine.store('toast').show('Added to Favorite', 'success');
        } else {
          this.items = this.items.filter(i => i !== isbn);
          Alpine.store('toast').show('Removed from Favorite', 'info');
        }
      } catch (_) { /* network error */ }
    },

    isFavorited(isbn) {
      return this.items.includes(isbn);
    }
  });

  // Auto-load favorites on init (noop for guest — server returns 302 redirect)
  Alpine.store('favorites').load();
});
