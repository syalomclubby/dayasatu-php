window.addEventListener("load", () => {
  const loader = document.getElementById("loader");

  setTimeout(() => {
    loader.style.opacity = "0";

    setTimeout(() => {
      loader.style.display = "none";
      document.body.classList.remove("loading");
      document.body.classList.add("loaded");

      startAnimations();

      const priorityElements = document.querySelectorAll(
        ".navbar, .hero-screen .hidden-left, .hero-screen .hidden-right",
      );
      priorityElements.forEach((el) => {
        el.classList.add("show");
      });
    }, 500);
  }, 2000);
});

function startAnimations() {
  const observer = new IntersectionObserver(
    (entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          entry.target.classList.add("show");
          observer.unobserve(entry.target);
        }
      });
    },
    {
      rootMargin: "0px 0px 100px 0px",
      threshold: 0.01,
    },
  );

  const hiddenElements = document.querySelectorAll(
    ".hidden-left, .hidden-right",
  );
  hiddenElements.forEach((el) => observer.observe(el));

  window.addEventListener(
    "scroll",
    () => {
      if (
        window.innerHeight + window.scrollY >=
        document.body.offsetHeight - 50
      ) {
        hiddenElements.forEach((el) => el.classList.add("show"));
      }
    },
    { passive: true },
  );
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
    const clickedInside =
      mobileShell.contains(event.target) || menuBtn.contains(event.target);
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

const productCards = document.querySelectorAll(".product-card");
const brandButtons = document.querySelectorAll("#brand-filter .filter-btn");
const categoryButtons = document.querySelectorAll(
  "#category-filter .filter-btn",
);
const searchInput = null;
const emptyState = document.getElementById("empty-product");

let currentBrand = "all";
let currentCategory = "all";
const filterTitle = document.getElementById("filter-title");

const productsPerPage = 8;
let currentPage = 1;
let filteredProducts = [];

function renderProducts(cards) {
  // Sembunyikan semua produk
  productCards.forEach((card) => {
    card.style.display = "none";
  });

  const start = (currentPage - 1) * productsPerPage;
  const end = start + productsPerPage;
  cards.forEach((card, index) => {
    if (index >= start && index < end) {
      card.style.display = "";
    }
  });
}

function updatePagination(cards) {
  const paginationNumbers = document.getElementById("pagination-numbers");
  paginationNumbers.innerHTML = "";
  const totalPages = Math.ceil(cards.length / productsPerPage);

  if (currentPage > totalPages) {
    currentPage = totalPages;
  }

  renderProducts(cards);

  for (let i = 1; i <= totalPages; i++) {
    const btn = document.createElement("button");
    btn.className = "page-number";
    btn.textContent = i;

    if (i === currentPage) {
      btn.classList.add("active");
    }

    btn.addEventListener("click", () => {
      currentPage = i;
      animatePagination(() => {
        updatePagination(cards);
      });
    });
    paginationNumbers.appendChild(btn);
  }
}

function filterProducts() {
  const productGrid = document.querySelector(".product-grid");
  productGrid.classList.add("filtering");

  setTimeout(() => {
    const visibleCards = [];

    productCards.forEach((card) => {
      const brand = card.dataset.brand.toLowerCase();
      const category = card.dataset.category.toLowerCase();

      const matchBrand = currentBrand === "all" || brand === currentBrand;

      const matchCategory =
        currentCategory === "all" || category === currentCategory;

      if (matchBrand && matchCategory) {
        visibleCards.push(card);
      } else {
        card.style.display = "none";
      }
    });

    filteredProducts = visibleCards;
    updatePagination(filteredProducts);

    if (emptyState) {
      emptyState.style.display =
        filteredProducts.length === 0 ? "block" : "none";
    }

    productGrid.classList.remove("filtering");
  }, 250);
}

function updateFilterTitle() {
  activeFilters.innerHTML = "";

  if (currentBrand === "all" && currentCategory === "all") {
    activeFilters.style.display = "none";
    filterTitle.textContent = "Filter Produk";
    return;
  }
  activeFilters.style.display = "flex";
  filterTitle.textContent = "Filter Produk";

  if (currentBrand !== "all") {
    const badge = document.createElement("span");
    badge.className = "filter-badge";
    badge.textContent = document.querySelector(
      "#brand-filter .filter-btn.active",
    ).textContent;
    activeFilters.appendChild(badge);
  }

  if (currentCategory !== "all") {
    const badge = document.createElement("span");

    badge.className = "filter-badge";
    badge.textContent = document.querySelector(
      "#category-filter .filter-btn.active",
    ).textContent;
    activeFilters.appendChild(badge);
  }
}

const activeFilters = document.getElementById("active-filters");

brandButtons.forEach((button) => {
  button.addEventListener("click", () => {
    brandButtons.forEach((btn) => btn.classList.remove("active"));
    button.classList.add("active");
    currentBrand = button.dataset.brand;
    updateFilterTitle();
    currentPage = 1;
    filterProducts();
  });
});

categoryButtons.forEach((button) => {
  button.addEventListener("click", () => {
    categoryButtons.forEach((btn) => btn.classList.remove("active"));
    button.classList.add("active");
    currentCategory = button.dataset.category;
    updateFilterTitle();
    currentPage = 1;
    filterProducts();
  });
});

filterProducts();

// popup lightbox (gambar)
const images = document.querySelectorAll(".product-card img");
const lightbox = document.getElementById("lightbox");
const lightboxImg = lightbox.querySelector(".lightbox-img");
const closeBtn = lightbox.querySelector(".lightbox-close");

images.forEach((img) => {
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

// event previous & next

document.getElementById("prev-page").addEventListener("click", () => {
  if (currentPage > 1) {
    currentPage--;
    animatePagination(() => {
      updatePagination(filteredProducts);
    });
  }
});

document.getElementById("next-page").addEventListener("click", () => {
  const totalPages = Math.ceil(filteredProducts.length / productsPerPage);
  if (currentPage < totalPages) {
    currentPage++;
    animatePagination(() => {
      updatePagination(filteredProducts);
    });
  }
});

// animasi Pagination

function animatePagination(callback) {
  const productGrid = document.querySelector(".product-grid");

  productGrid.classList.add("filtering");

  setTimeout(() => {
    callback();

    productGrid.classList.remove("filtering");
  }, 250);
}

// Tampilan Filter
const filterToggle = document.getElementById("filter-toggle");

const filterContent = document.getElementById("filter-content");

const filterArrow = document.getElementById("filter-arrow");

filterToggle.addEventListener("click", () => {
  filterContent.classList.toggle("collapsed");

  filterArrow.classList.toggle("rotate");
});

// See More Button
document.querySelectorAll(".product-description").forEach((desc) => {
  const text = desc.querySelector("p");
  const button = desc.querySelector(".see-more-btn");
  const label = button.querySelector("span");

  if (text.scrollHeight <= text.clientHeight + 2) {
    button.style.display = "none";
    return;
  }

  button.addEventListener("click", () => {
    desc.classList.toggle("expanded");

    label.textContent = desc.classList.contains("expanded")
      ? "See Less"
      : "See More";
  });
});
