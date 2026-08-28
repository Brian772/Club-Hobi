@if (session('success'))
  <div id="alert"
    class="fixed flex top-18 lg:top-4 right-4 w-max p-2 lg:pl-4 lg:pr-12 lg:py-4 bg-white border border-teal-300 rounded-md z-100 overflow-hidden transition-opacity duration-500"
    role="alert">
    <div class="flex flex-row justify-center items-center gap-2">
      <i class="fa-solid fa-circle-check text-teal-600"></i>
      <span class="block sm:inline text-caption lg:text-body-mid text-teal-600">{{ session('success') }}</span>
    </div>
    <div id="alertProgressBar" class="absolute rounded-full bottom-0 left-0 h-1 bg-teal-600"></div>
  </div>
@endif

@if (session('error'))
  <div id="alert"
    class="fixed flex top-18 lg:top-4 right-4 w-max p-2 lg:pl-4 lg:pr-12 lg:py-4 bg-white border border-red-300 rounded-md z-100 overflow-hidden transition-opacity duration-500"
    role="alert">
    <div class="flex flex-row justify-center items-center gap-2">
      <i class="fa-solid fa-circle-exclamation text-red-600"></i>
      <span class="block sm:inline text-caption lg:text-body-mid text-red-600">{{ session('error') }}</span>
    </div>
    <div id="alertProgressBar" class="absolute rounded-full bottom-0 left-0 h-1 bg-red-600"></div>
  </div>
@endif

@if (session('warning'))
  <div id="alert"
    class="fixed flex top-18 lg:top-4 right-4 w-max p-2 lg:pl-4 lg:pr-12 lg:py-4 bg-white border border-yellow-300 rounded-md z-100 overflow-hidden transition-opacity duration-500"
    role="alert">
    <div class="flex flex-row justify-center items-center gap-2">
      <i class="fa-solid fa-triangle-exclamation text-yellow-600"></i>
      <span class="block sm:inline text-caption lg:text-body-mid text-yellow-600">{{ session('warning') }}</span>
    </div>
    <div id="alertProgressBar" class="absolute rounded-full bottom-0 left-0 h-1 bg-yellow-600"></div>
  </div>
@endif

@if (session('info'))
  <div id="alert"
    class="fixed flex top-18 lg:top-4 right-4 w-max p-2 lg:pl-4 lg:pr-12 lg:py-4 bg-white border border-blue-300 rounded-md z-100 overflow-hidden transition-opacity duration-500"
    role="alert">
    <div class="flex flex-row justify-center items-center gap-2">
      <i class="fa-solid fa-circle-info text-blue-600"></i>
      <span class="block sm:inline text-caption lg:text-body-mid text-blue-600">{{ session('info') }}</span>
    </div>
    <div id="alertProgressBar" class="absolute rounded-full bottom-0 left-0 h-1 bg-blue-600"></div>
  </div>
@endif

@if (session('success') || session('error') || session('warning') || session('info'))
  <script>
    (function() {
      const duration = 3000;
      const alert = document.getElementById('alert');
      const progress = document.getElementById('alertProgressBar');

      if (progress) {
        progress.style.width = '100%';
        progress.style.transition = `width ${duration}ms linear`;

        requestAnimationFrame(() => {
          progress.style.width = '0%';
        });
      }

      setTimeout(() => {
        alert.style.opacity = '0';
        setTimeout(() => {
          alert.remove();
        }, 500);
      }, duration);
    })();
  </script>
@endif
