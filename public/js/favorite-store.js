// public/js/favorite-store.js
// Alpine global store for persistent favorite books (LocalStorage)

document.addEventListener('alpine:init', () => {
  Alpine.store('favorites', {
    items: JSON.parse(localStorage.getItem('favorites') || '[]'),

    toggle(isbn) {
      const index = this.items.indexOf(isbn);
      
      if (index === -1) {
        this.items.push(isbn);
        localStorage.setItem('favorites', JSON.stringify(this.items));
        Alpine.store('toast').show('Added to Favorite', 'success');
      } else {
        this.items.splice(index, 1);
        localStorage.setItem('favorites', JSON.stringify(this.items));
        Alpine.store('toast').show('Removed from Favorite', 'info');
      }
    },

    isFavorited(isbn) {
      return this.items.includes(isbn);
    }
  });
});
