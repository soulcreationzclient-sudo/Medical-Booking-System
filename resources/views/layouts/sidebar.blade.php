<!-- GOOGLE FONT -->
<link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600;700&display=swap" rel="stylesheet">

<style>
/* =========================
   DESIGN TOKENS (LOGIN MATCH)
   ========================= */
:root {
    --brand-primary: #1363C6;
    --brand-card: #ffffff;
    --brand-border: rgba(255,255,255,0.25);
}

/* =========================
   SIDEBAR CONTAINER
   ========================= */
#sidebar {
    background-color: var(--brand-primary) !important;
    font-family: 'Open Sans', sans-serif;
    width: 250px;
    color: #ffffff;
    box-shadow: 4px 0 20px rgba(0,0,0,0.15);
}

/* =========================
   NAV LIST
   ========================= */
#sidebar .nav {
    margin-top: 10px;
}

/* =========================
   NAV ITEM
   ========================= */
#sidebar .nav-item {
    margin-bottom: 8px;
}

/* =========================
   NAV LINK – BASE
   ========================= */
#sidebar .nav-link {
    display: flex;
    align-items: center;
    gap: 12px;

    font-size: 14px;
    font-weight: 600;
    color: #ffffff !important;

    padding: 14px 18px;
    border-radius: 12px;

    transition: background 0.2s ease, color 0.2s ease;
    position: relative;
}

/* Remove Bootstrap ghost effects */
#sidebar .nav-link::before,
#sidebar .nav-link::after {
    display: none !important;
}

/* =========================
   HOVER
   ========================= */
#sidebar .nav-link:hover {
    background-color: rgba(255,255,255,0.15);
    text-decoration: none;
}

/* =========================
   ACTIVE (WHITE CARD)
   ========================= */
#sidebar .nav-link.active {
    background-color: var(--brand-card) !important;
    color: var(--brand-primary) !important;
    box-shadow: 0 8px 18px rgba(0,0,0,0.18);
}

/* =========================
   MOBILE
   ========================= */
@media (max-width: 768px) {
    #sidebar {
        width: 260px !important;
    }

    #sidebar .nav-link {
        font-size: 15px;
        padding: 16px 18px;
    }
}

/* ENSURE MOBILE OFFCANVAS WORKS */
@media (max-width: 991.98px) {
  #sidebar {
    position: fixed !important;
    visibility: hidden;
  }

  #sidebar.show {
    visibility: visible;
  }
}

</style>

<!-- =========================
     SIDEBAR HTML
     ========================= -->
<div class="offcanvas offcanvas-start"
     tabindex="-1"
     id="sidebar">

    <div class="offcanvas-body p-0">
        <ul class="nav flex-column p-3">

            {{-- DOCTOR --}}
            @can('doctor')
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('doctor.dashboard') ? 'active' : '' }}"
                       href="{{ route('doctor.dashboard') }}">
                        Dashboard
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('doctor.*bookings*') ? 'active' : '' }}"
                       href="{{ route('doctor.overall_bookings') }}">
                        Appointments
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('doctor.schedule') ? 'active' : '' }}"
                       href="{{ route('doctor.schedule') }}">
                        Schedule
                    </a>
                </li>
            @endcan

            {{-- SUPER ADMIN --}}
            @can('super_admin')
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('super_admin.dashboard') ? 'active' : '' }}"
                       href="{{ route('super_admin.dashboard') }}">
                        Dashboard
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('super_admin.hospitals*') ? 'active' : '' }}"
                       href="{{ route('super_admin.hospitals_add_view') }}">
                        Add Hospitals
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('super_admin.enquiries*') ? 'active' : '' }}"
                       href="#">
                        View Enquiry
                    </a>
                </li>
            @endcan

            {{-- HOSPITAL ADMIN --}}
            @can('hospital_admin')
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('hospital_admin.dashboard') ? 'active' : '' }}"
                       href="#">
                        Dashboard
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('hospital_admin.doctors*') ? 'active' : '' }}"
                       href="{{ route('hospital_admin.doctors_add_view') }}">
                        Doctors
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('hospital_admin.specialization*') ? 'active' : '' }}"
                       href="{{ route('hospital_admin.specialization') }}">
                        Specialization
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('hospital_admin.bookings*') ? 'active' : '' }}"
                       href="#">
                        Overall Bookings
                    </a>
                </li>
            @endcan

        </ul>
    </div>
</div>


