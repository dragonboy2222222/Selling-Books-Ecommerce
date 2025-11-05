// BookNest small JS
document.addEventListener('DOMContentLoaded', () => {
  // Hero carousel if present
  const el = document.querySelector('.swiper');
  if (el) {
    new Swiper(el, {
      loop: true,
      autoplay: { delay: 3000 },
      slidesPerView: 1,
      spaceBetween: 12,
      pagination: { el: '.swiper-pagination', clickable: true },
    });
  }
});
