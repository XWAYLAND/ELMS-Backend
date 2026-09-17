// public/js/toast-store.js
// Alpine global toast store for notification system

document.addEventListener('alpine:init', () => {
  Alpine.store('toast', {
    visible: false,
    message: '',
    type: 'success',
    timer: null,

    show(message, type = 'success', duration = 3000) {
      if (this.timer) clearTimeout(this.timer);
      
      this.message = message;
      this.type = type;
      this.visible = true;

      this.timer = setTimeout(() => {
        this.visible = false;
      }, duration);
    },

    hide() {
      this.visible = false;
      if (this.timer) clearTimeout(this.timer);
    }
  });
});
