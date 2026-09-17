{{-- resources/views/student/home.blade.php --}}
@extends('layouts.app')

@section('title', 'Home')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/components/carousel.css') }}">
<link rel="stylesheet" href="{{ asset('css/pages/home.css') }}">
@endpush

@section('content')

{{-- Background SVG layer --}}
<div class="home-bg">
  <img src="{{ asset('') }}" alt="" class="home-bg__svg">
</div>

<div class="home-content">

  {{-- Section 1: Book Recommendation --}}
  <section class="section-recommendation">
    <div class="svg-heading-placeholder">
      <img src="{{ asset('svg/Book Recomendation.svg') }}" alt="Book Recommendation">
    </div>

    {{-- Infinite Carousel --}}
    <div class="carousel-wrapper"
         x-data="bookCarousel()"
         x-init="init()">
      <div class="carousel-track"
           x-ref="track"
           @wheel.prevent="scrollWheel($event)">
        {{-- Books duplicated 3x for seamless loop --}}
        @foreach(array_merge($recommendedBooks, $recommendedBooks, $recommendedBooks) as $book)
          <x-book-carousel-card :book="$book" />
        @endforeach
      </div>
    </div>
  </section>

  {{-- Section 2: Book Category --}}
  <section class="section-category">
    <div class="svg-heading-placeholder">
      <img src="{{ asset('svg/Book Category.svg') }}" alt="Book Category">
    </div>

    <div class="category-folder-row">
      @foreach($homeCategories as $cat)
        <x-category-folder-item :category="$cat" />
      @endforeach
    </div>
  </section>

</div>

@endsection

@push('scripts')
<script>
function bookCarousel() {
  return {
    timer: null,
    speed: 0.8,
    isDragging: false,
    startX: 0,
    scrollLeftStart: 0,
    wheelTimer: null,
    init() {
      this.resume();
      this.setupDrag();
    },
    step() {
      if (this.isDragging) return;
      const track = this.$refs.track;
      track.scrollLeft += this.speed;
      if (track.scrollLeft >= track.scrollWidth / 3) {
        track.scrollLeft = 0;
      }
    },
    pause() {
      if (this.timer) {
        clearInterval(this.timer);
        this.timer = null;
      }
    },
    resume() {
      if (!this.timer) {
        this.timer = setInterval(() => this.step(), 30);
      }
    },
    setupDrag() {
      const track = this.$refs.track;
      let hasMoved = false;

      const onMouseDown = (e) => {
        this.isDragging = true;
        hasMoved = false;
        this.startX = e.pageX - track.offsetLeft;
        this.scrollLeftStart = track.scrollLeft;
      };

      const onMouseMove = (e) => {
        if (!this.isDragging) return;
        const x = e.pageX - track.offsetLeft;
        const distance = Math.abs(x - this.startX);

        if (distance > 5) {
          hasMoved = true;
          track.classList.add('is-dragging');
          e.preventDefault();
          const walk = (x - this.startX) * 2;
          track.scrollLeft = this.scrollLeftStart - walk;
        }
      };

      const onMouseUp = (e) => {
        if (this.isDragging) {
          if (hasMoved) {
            e.preventDefault();
          }
          this.isDragging = false;
          hasMoved = false;
          track.classList.remove('is-dragging');
        }
      };

      track.addEventListener('mousedown', onMouseDown);
      document.addEventListener('mousemove', onMouseMove);
      document.addEventListener('mouseup', onMouseUp);
      track.addEventListener('mouseleave', onMouseUp);

      // Prevent click if dragged
      track.addEventListener('click', (e) => {
        if (hasMoved) {
          e.preventDefault();
          e.stopPropagation();
        }
      }, true);
    },
    scrollWheel(event) {
      this.$refs.track.scrollLeft += event.deltaY || event.deltaX;
      clearTimeout(this.wheelTimer);
      this.wheelTimer = setTimeout(() => {
        // Auto-scroll resumes naturally
      }, 1000);    }
  }
}
</script>
@endpush
