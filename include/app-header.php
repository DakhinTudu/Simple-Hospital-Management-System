<?php
// App Header Component
function render_app_header($username) {
    echo '<div id="sidebarBackdrop" class="sidebar-backdrop"></div>
          <nav class="navbar navbar-expand-lg navbar-dark fixed-top app-top-nav">
            <div class="container-fluid">
                <button class="btn btn-outline-light btn-sm d-lg-none mr-2" id="sidebarToggle">
                    <i class="fa fa-bars"></i>
                </button>
                <span class="navbar-brand d-lg-none text-white">GLOBAL HOSPITALS</span>
                
                <div class="ml-auto d-flex align-items-center">
                    <div class="dropdown">
                        <a class="nav-link dropdown-toggle text-white font-weight-600" href="#" id="userDropdown" data-toggle="dropdown">
                            <i class="fa fa-user-circle-o mr-2 text-info"></i> '.$username.'
                        </a>
                        <div class="dropdown-menu dropdown-menu-right shadow border-0 bg-dark text-white">
                            <a class="dropdown-item py-2 text-white-50" href="#"><i class="fa fa-cog mr-2"></i> Settings</a>
                            <div class="dropdown-divider border-secondary"></div>
                            <a class="dropdown-item py-2 text-danger" href="logout.php"><i class="fa fa-sign-out mr-2"></i> Logout</a>
                        </div>
                    </div>
                </div>
            </div>
          </nav>
          <script>
            document.addEventListener("DOMContentLoaded", function () {
              var toggleBtn = document.getElementById("sidebarToggle");
              var sidebar = document.querySelector(".app-sidebar");
              var backdrop = document.getElementById("sidebarBackdrop");
              if (!toggleBtn || !sidebar) { return; }

              function openSidebar() {
                sidebar.classList.add("show");
                if (backdrop) { backdrop.classList.add("show"); }
              }
              function closeSidebar() {
                sidebar.classList.remove("show");
                if (backdrop) { backdrop.classList.remove("show"); }
              }
              function isMobile() {
                return window.matchMedia("(max-width: 991.98px)").matches;
              }

              toggleBtn.addEventListener("click", function () {
                if (sidebar.classList.contains("show")) {
                  closeSidebar();
                } else {
                  openSidebar();
                }
              });

              if (backdrop) {
                backdrop.addEventListener("click", closeSidebar);
              }

              var menuLinks = document.querySelectorAll(".app-sidebar .list-group-item");
              menuLinks.forEach(function (link) {
                link.addEventListener("click", function () {
                  if (isMobile()) {
                    closeSidebar();
                  }
                });
              });

              // Auto-map table headers -> mobile data-labels for responsive card rows
              var tables = document.querySelectorAll(".table-modern");
              tables.forEach(function (table) {
                var headers = [];
                table.querySelectorAll("thead th").forEach(function (th) {
                  headers.push((th.textContent || "").trim());
                });
                if (!headers.length) { return; }
                table.querySelectorAll("tbody tr").forEach(function (tr) {
                  tr.querySelectorAll("td").forEach(function (td, idx) {
                    if (!td.getAttribute("data-label")) {
                      td.setAttribute("data-label", headers[idx] || "Field");
                    }
                  });
                });
              });

              window.addEventListener("resize", function () {
                if (!isMobile()) {
                  closeSidebar();
                }
              });
            });
          </script>';
}
?>
