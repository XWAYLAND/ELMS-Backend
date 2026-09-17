{{-- resources/views/auth/login.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login</title>
  <link rel="icon" type="image/svg+xml" href="{{ asset('assets/logo.svg') }}">

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">

  <link rel="stylesheet" href="{{ asset('css/app.css') }}">
  <link rel="stylesheet" href="{{ asset('css/pages/login.css') }}">
</head>
<body class="login-page">

  <div class="login-card">

    <div class="login-left">
      <img src="{{ asset('assets/logo.svg') }}" alt="e-LiBraRy" class="login-logo">
      <p class="login-subtitle">Login for experience</p>

      <form method="POST" action="{{ route('login') }}" class="login-form" id="login-form">
        @csrf

        <div class="form-group">

          <div class="role-toggle" role="radiogroup" aria-label="Pilih peran login">
            <input type="hidden" name="role" value="siswa" id="role-input">

            <button type="button"
                    class="role-toggle__btn role-toggle__btn--siswa active"
                    data-role="siswa"
                    role="radio"
                    aria-checked="true"
                    tabindex="0">
              Student
            </button>

            <button type="button"
                    class="role-toggle__btn role-toggle__btn--pegawai"
                  data-role="petugas"
                    role="radio"
                    aria-checked="false"
                    tabindex="0">
              Staff
            </button>
          </div>
        </div>

        <div class="form-group" id="identifier-group">
          <label for="identifier" id="identifier-label">NIS</label>
          <input id="identifier"
                 type="text"
                 name="identifier"
                 value="{{ old('identifier') }}"
                 placeholder="Insert NIS"
                 autocomplete="username"
                 autofocus>
          @error('identifier')<p class="form-error">{{ $message }}</p>@enderror
          @error('login')<p class="form-error login-error">{{ $message }}</p>@enderror
        </div>


        <div class="form-group">
          <label for="password">Password</label>
          <input id="password"
                 type="password"
                 name="password"
                 placeholder="Insert Password"
                 autocomplete="current-password">
          @error('password')<p class="form-error">{{ $message }}</p>@enderror
        </div>

        <button type="submit" class="btn-masuk" id="login-btn">Login</button>
        <div class="form-group">
          <a href="{{ route('home') }}" class="btn-back-home">
            ← Back To Home
          </a>
        </div>
      </form>
    </div>

    <div class="login-right">
      <img src="{{ asset('assets/Hi.svg') }}" alt="" class="login-hi">
    </div>

  </div>

  <script>
    document.addEventListener('DOMContentLoaded', () => {
      const toggle = document.querySelector('.role-toggle');
      const roleInput = document.getElementById('role-input');
      const label = document.getElementById('identifier-label');
      const input = document.getElementById('identifier');
      const form = document.getElementById('login-form');
      const loginBtn = document.getElementById('login-btn');
      const btnText = loginBtn.textContent;
      let isSubmitting = false;

      if (!toggle) return;

      toggle.querySelectorAll('.role-toggle__btn').forEach(btn => {
        btn.addEventListener('click', () => {
          // Update active state
          toggle.querySelectorAll('.role-toggle__btn').forEach(b => {
            b.classList.remove('active');
            b.setAttribute('aria-checked', 'false');
          });
          btn.classList.add('active');
          btn.setAttribute('aria-checked', 'true');

          // Update hidden input
          roleInput.value = btn.dataset.role;

          // Update label & placeholder
          if (btn.dataset.role === 'petugas') {
            label.textContent = 'ID';
            input.placeholder = 'Insert ID';
          } else {
            label.textContent = 'NIS';
            input.placeholder = 'Insert NIS';
          }
        });

        // Keyboard support
        btn.addEventListener('keydown', (e) => {
          if (e.key === 'Enter' || e.key === ' ') {
            e.preventDefault();
            btn.click();
          }
        });
      });

      // Phase 1.1 & 1.2: Loading state and prevent double submit
      if (form && loginBtn) {
        form.addEventListener('submit', async (e) => {
          if (isSubmitting) {
            e.preventDefault();
            return;
          }

          isSubmitting = true;
          console.log('[Login] Form submitted', {
            identifier: document.getElementById('identifier').value,
            role: document.getElementById('role-input').value,
            timestamp: new Date().toISOString()
          });

          // Phase 1.1: Add loading state
          loginBtn.disabled = true;
          loginBtn.classList.add('loading');
          loginBtn.textContent = '';

          // Phase 1.3: Debug logging
          console.log('[Login] Form submitting...', {
            identifier: document.getElementById('identifier').value,
            role: document.getElementById('role-input').value,
            passwordLength: document.getElementById('password').value.length
          });

          // Don't prevent default - let form submit normally
          // The loading state will persist until page redirects
        });

        // Re-enable button if form validation fails (page reloads with errors)
        // Check if there are server-side validation errors on page load
        @if ($errors->any())
          console.log('[Login] Page loaded with validation errors');
          loginBtn.disabled = false;
          loginBtn.classList.remove('loading');
          loginBtn.textContent = '{{ $btnText ?? "Masuk" }}';

          // Add has-error classes to form groups with errors
          @if ($errors->has('identifier') || $errors->has('login'))
            document.getElementById('identifier-group').classList.add('has-error');
          @endif
          @if ($errors->has('password'))
            document.querySelector('#password').closest('.form-group').classList.add('has-error');
          @endif
        @endif
      }
    });
  </script>

</body>
</html>
