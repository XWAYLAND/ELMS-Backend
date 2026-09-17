{{-- resources/views/components/loader.blade.php --}}
<div class="page-loader" id="pageLoader">
  <div class="loader-books">
    <div class="loader-book"></div>
    <div class="loader-book"></div>
    <div class="loader-book"></div>
    <div class="loader-book"></div>
    <div class="loader-book"></div>
  </div>
  <img src="{{ asset('assets/logo.svg') }}" width="120" alt="e-LiBraRy">
  <span class="loader-text">Loading page…</span>
</div>
<script>
window.addEventListener('load', function() {
  var loader = document.getElementById('pageLoader');
  loader.classList.add('is-hidden');
  setTimeout(function() { loader.style.display = 'none'; }, 300);
});
</script>
