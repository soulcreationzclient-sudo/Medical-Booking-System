<!-- GOOGLE FONT -->
<link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600;700&display=swap" rel="stylesheet">

<style>
.app-navbar {
    background: #1363C6;
    font-family: 'Open Sans', sans-serif;
    box-shadow: 0 4px 12px rgba(0,0,0,0.12);
}

.app-navbar .navbar-brand {
    font-weight: 700;
    font-size: 20px;
    color: #ffffff !important;
}

.app-navbar .nav-link {
    color: #ffffff !important;
    font-weight: 600;
    padding: 8px 14px;
    border-radius: 8px;
}

.app-navbar .nav-link:hover,
.app-navbar .nav-link.active {
    background: rgba(255,255,255,0.15);
}

.logout-btn {
    background: #ef4444;
    border: none;
    font-size: 13px;
    font-weight: 600;
    padding: 6px 14px;
    border-radius: 6px;
    color: #ffffff;
}

.logout-btn:hover {
    background: #dc2626;
}
</style>

<nav class="navbar navbar-expand-lg app-navbar sticky-top">
    <div class="container-fluid">

        <!-- BRAND / ROLE -->
        <a class="navbar-brand d-flex align-items-center gap-3" href="#">
            <span class="fw-bold">Speedbots.io</span>
            <span class="fw-semibold">
                {{ str_replace('_',' ', ucwords(auth()->user()->role)) }}
            </span>
        </a>

        <!-- MOBILE TOGGLE -->
        <button class="navbar-toggler text-white"
                type="button"
                data-bs-toggle="collapse"
                data-bs-target="#mainNavbar">
            ☰
        </button>

        <!-- MENU -->
        <div class="collapse navbar-collapse" id="mainNavbar">
            <ul class="navbar-nav mx-auto mb-2 mb-lg-0">

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
                           href="{{ route('hospital_admin.dashboard') }}">
                            Dashboard
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('hospital_admin.medicines*') ? 'active' : '' }}"
                           href="{{ route('hospital_admin.medicines.index') }}">
                            Medicines
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
                        <a class="nav-link {{ request()->routeIs('hospital_admin.inpersonform') ? 'active' : '' }}"
                           href="{{ route('hospital_admin.inpersonform') }}">
                            In person
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('hospital_admin.*bookings*') ? 'active' : '' }}"
                           href="{{ route('hospital_admin.overall_bookings') }}">
                            Overall Bookings
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('hospital_admin.patients*') ? 'active' : '' }}"
                           href="{{ route('hospital_admin.patients.search') }}">
                            Patients
                        </a>
                    </li>

                    {{-- ✅ NEW: FINANCIALS --}}
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('hospital_admin.financials*') ? 'active' : '' }}"
                           href="{{ route('hospital_admin.financials.index') }}">
                            Financials
                        </a>
                    </li>

                @endcan

            </ul>

            <!-- USER / LOGOUT -->
            <div class="d-flex align-items-center">
                <span class="text-white me-3 fw-semibold">
                    {{ auth()->user()->name }}
                </span>

                <a class="logout-btn"
                   href="{{ route('logout') }}"
                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    Logout
                </a>
            </div>
        </div>
    </div>
</nav>

<form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
    @csrf
</form>