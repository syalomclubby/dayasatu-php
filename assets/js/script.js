window.addEventListener("load", () => {
  const loader = document.getElementById("loader");

  setTimeout(() => {
    loader.style.opacity = "0";

    setTimeout(() => {
      loader.style.display = "none";
      document.body.classList.remove("loading");
      document.body.classList.add("loaded");

      startAnimations(); 

      const priorityElements = document.querySelectorAll('.navbar, .hero-screen .hidden-left, .hero-screen .hidden-right');
      priorityElements.forEach(el => {
        el.classList.add('show');
      });

    }, 500); 
  }, 2000);
});

function startAnimations() {
  const observer = new IntersectionObserver((entries) => {
    entries.forEach((entry) => {
      if (entry.isIntersecting) {
        entry.target.classList.add('show');
        observer.unobserve(entry.target);
      }
    });
  }, {
    rootMargin: '0px 0px 100px 0px',
    threshold: 0.01 
  });

  const hiddenElements = document.querySelectorAll('.hidden-left, .hidden-right');
  hiddenElements.forEach((el) => observer.observe(el));

  window.addEventListener('scroll', () => {
    if ((window.innerHeight + window.scrollY) >= document.body.offsetHeight - 50) {
      hiddenElements.forEach(el => el.classList.add('show'));
    }
  }, { passive: true });
}

// --- KODE NAVIGASI & FORM ---

const menuBtn = document.getElementById("menuBtn");
const mobileNav = document.getElementById("mobileNav");
const mobileShell = document.querySelector(".mobile-nav-shell");
const toTop = document.getElementById("toTop");

function closeMenu() {
  if (!menuBtn || !mobileShell) return;
  mobileShell.classList.remove("open");
  menuBtn.classList.remove("active");
  menuBtn.setAttribute("aria-expanded", "false");
}

if (menuBtn && mobileShell) {
  menuBtn.addEventListener("click", () => {
    const isOpen = mobileShell.classList.toggle("open");
    menuBtn.classList.toggle("active", isOpen);
    menuBtn.setAttribute("aria-expanded", String(isOpen));
  });

  if (mobileNav) {
    mobileNav.querySelectorAll("a").forEach((link) => {
      link.addEventListener("click", closeMenu);
    });
  }

  document.addEventListener("click", (event) => {
    const clickedInside = mobileShell.contains(event.target) || menuBtn.contains(event.target);
    if (!clickedInside && mobileShell.classList.contains("open")) {
      closeMenu();
    }
  });

  window.addEventListener("resize", () => {
    if (window.innerWidth > 768) closeMenu();
  });
}

window.addEventListener("scroll", () => {
  if (!toTop) return;
  if (window.scrollY > 300) toTop.classList.add("show");
  else toTop.classList.remove("show");
});

if (toTop) {
  toTop.addEventListener("click", () => {
    window.scrollTo({ top: 0, behavior: "smooth" });
  });
}

const contactForm = document.getElementById("form-kontak");
if (contactForm) {
  contactForm.addEventListener("submit", (event) => {
    event.preventDefault();
    const nama = document.getElementById("nama").value.trim();
    const status = document.getElementById("status").value.trim();
    const kebutuhan = document.getElementById("kebutuhan").value.trim();
    const lokasi = document.getElementById("lokasi").value.trim();
    const pesan = document.getElementById("pesan").value.trim();
    const nomoradmin = "6282158300335";

    let text = `Halo, saya ${nama}.\nStatus: ${status}\nKebutuhan: ${kebutuhan}`;
    if (lokasi) text += `\nLokasi: ${lokasi}`;
    text += `\nDetail kebutuhan: ${pesan}`;

    const url = `https://wa.me/${nomoradmin}?text=${encodeURIComponent(text)}`;
    window.open(url, "_blank");
  });
}

// Filter produk
const btns = document.querySelectorAll(".filter-btn");
const items = document.querySelectorAll(".product-card");

btns.forEach(btn => btn.onclick = () => {
  const f = btn.dataset.filter;
  btns.forEach(b => b.classList.toggle("active", b === btn));
  items.forEach(el => {
    const ok = f === "all" || el.dataset.category === f;
    el.classList.remove("show");

    if (ok) {
      el.style.display = "";
      void el.offsetWidth;
      el.classList.add("show");
    } else {
      el.style.display = "none";
    }
  });
});

// popup lightbox (gambar)
const images = document.querySelectorAll(".product-card img");
const lightbox = document.getElementById("lightbox");
const lightboxImg = lightbox.querySelector(".lightbox-img");
const closeBtn = lightbox.querySelector(".lightbox-close");

images.forEach(img => {
  img.style.cursor = "pointer";

  img.addEventListener("click", (e) => {
    e.stopPropagation();
    lightboxImg.src = img.src;
    lightbox.classList.add("show");
  });
});

closeBtn.addEventListener("click", () => {
  lightbox.classList.remove("show");
});

lightbox.addEventListener("click", (e) => {
  if (e.target === lightbox) {
    lightbox.classList.remove("show");
  }
});