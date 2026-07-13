document.addEventListener("DOMContentLoaded", () => {
  // Dashboard Only
  const slot = document.getElementById("mobile-banner-slot");
  const banner = document.querySelector(".welcome-banner");

  if (slot && banner) {
    const breakpoint = 768;

    const originalParent = banner.parentNode;
    const originalNext = banner.nextSibling;

    function relocate() {
      if (window.innerWidth <= breakpoint) {
        if (banner.parentNode !== slot) {
          slot.appendChild(banner);
        }
      } else {
        if (banner.parentNode !== originalParent) {
          if (originalNext) {
            originalParent.insertBefore(banner, originalNext);
          } else {
            originalParent.appendChild(banner);
          }
        }
      }
    }

    relocate();

    window.addEventListener("resize", relocate);
  }

  // Sidebar
  const menuToggle = document.getElementById("menuToggle");
  const sidebar = document.getElementById("sidebar");
  const overlay = document.getElementById("sidebarOverlay");
  const sidebarClose = document.getElementById("sidebarClose");

  if (menuToggle && sidebar && overlay) {
    function openSidebar() {
      sidebar.classList.add("show");
      overlay.classList.add("show");
      document.body.classList.add("sidebar-open");
    }

    function closeSidebar() {
      sidebar.classList.remove("show");
      overlay.classList.remove("show");
      document.body.classList.remove("sidebar-open");
    }

    menuToggle.addEventListener("click", () => {
      sidebar.classList.contains("show") ? closeSidebar() : openSidebar();
    });

    overlay.addEventListener("click", closeSidebar);

    if (sidebarClose) {
      sidebarClose.addEventListener("click", closeSidebar);
    }

    document.addEventListener("keydown", (e) => {
      if (e.key === "Escape") {
        closeSidebar();
      }
    });

    window.addEventListener("resize", () => {
      if (window.innerWidth > 768) {
        closeSidebar();
      }
    });
  }

  // Notification Expand
  const notifPanel = document.getElementById("notifPanel");
  const toggleNotifBtn = document.getElementById("toggleNotifBtn");

  if (notifPanel && toggleNotifBtn) {
    toggleNotifBtn.addEventListener("click", () => {
      notifPanel.classList.toggle("expanded");

      toggleNotifBtn.innerHTML = notifPanel.classList.contains("expanded")
        ? `Show Less <i class="fa-solid fa-chevron-up"></i>`
        : `See More <i class="fa-solid fa-chevron-down"></i>`;
    });
  }

  // Search
  const searchInput = document.getElementById("searchInput");
  const tableContainer = document.getElementById("tableContainer");
  const paginationContainer = document.getElementById("paginationContainer");
  const clearBtn = document.getElementById("searchClear");

  if (searchInput && tableContainer) {
    const currentPage = window.location.pathname.split("/").pop();

    let searchTimer = null;

    async function loadTable(page = 1) {
      const keyword = searchInput.value;

      let url = `${currentPage}?ajax=1&search=${encodeURIComponent(keyword)}`;

      // hanya products yang punya pagination
      if (paginationContainer) {
        url += `&page=${page}`;
      }

      const response = await fetch(url);
      const data = await response.json();

      tableContainer.innerHTML = data.table;

      if (paginationContainer && data.pagination !== undefined) {
        paginationContainer.innerHTML = data.pagination;
        bindPagination();
      }

      bindDeleteButton();
    }

    searchInput.addEventListener("input", function () {
      clearTimeout(searchTimer);

      clearBtn.style.display = searchInput.value ? "flex" : "none";

      searchTimer = setTimeout(() => {
        loadTable(1);
      }, 300);
    });

    clearBtn.addEventListener("click", function () {
      searchInput.value = "";
      clearBtn.style.display = "none";

      loadTable(1);
    });

    function bindPagination() {
      if (!paginationContainer) return;

      document.querySelectorAll(".page-link").forEach((link) => {
        link.onclick = function (e) {
          e.preventDefault();

          loadTable(this.dataset.page);
        };
      });
    }

    if (paginationContainer) {
      bindPagination();
    }
  }

  function bindDeleteButton() {
    const deleteConfigs = [
      {
        selector: ".btn-delete-product",
        title: "Delete Product?",
        text: "Deleted products cannot be restored.",
        confirm: "Delete Product",
      },
      {
        selector: ".btn-delete-category",
        title: "Delete Category?",
        text: "Deleted categories cannot be restored.",
        confirm: "Delete Category",
      },
      {
        selector: ".btn-delete-brand",
        title: "Delete Brand?",
        text: "Deleted brands cannot be restored.",
        confirm: "Delete Brand",
      },
      {
        selector: ".btn-delete-user",
        title: "Delete User?",
        text: "Deleted users cannot be restored.",
        confirm: "Delete User",
      },
    ];

    deleteConfigs.forEach((config) => {
      document.querySelectorAll(config.selector).forEach((button) => {
        button.onclick = function (e) {
          e.preventDefault();

          const url = this.href;

          Swal.fire({
            title: config.title,
            text: config.text,
            icon: "warning",

            showCancelButton: true,

            confirmButtonText: config.confirm,
            cancelButtonText: "Cancel",

            reverseButtons: true,

            customClass: {
              popup: "swal-popup",
              title: "swal-title",
              htmlContainer: "swal-text",
              confirmButton: "swal-btn-delete",
              cancelButton: "swal-btn-cancel",
            },

            buttonsStyling: false,
          }).then((result) => {
            if (result.isConfirmed) {
              window.location.href = url;
            }
          });
        };
      });
    });
  }

  bindDeleteButton();
});
