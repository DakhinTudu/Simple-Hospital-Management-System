<?php
// App Sidebar Component
function render_app_sidebar($activePage, $role) {
    $menuItems = [];
    
    if ($role === 'patient') {
        $menuItems = [
            ['id' => 'dash', 'label' => 'Dashboard', 'icon' => 'fa-th-large', 'target' => '#list-dash'],
            ['id' => 'book', 'label' => 'Book Appointment', 'icon' => 'fa-calendar-plus-o', 'target' => '#list-home'],
            ['id' => 'history', 'label' => 'Appointment History', 'icon' => 'fa-history', 'target' => '#app-hist'],
            ['id' => 'pres', 'label' => 'Prescriptions', 'icon' => 'fa-file-text-o', 'target' => '#list-pres'],
            ['id' => 'lab', 'label' => 'Lab Reports', 'icon' => 'fa-flask', 'target' => '#list-lab'],
        ];
    } elseif ($role === 'doctor') {
        $menuItems = [
            ['id' => 'dash', 'label' => 'Dashboard', 'icon' => 'fa-th-large', 'target' => '#list-dash'],
            ['id' => 'app', 'label' => 'Appointments', 'icon' => 'fa-calendar-check-o', 'target' => '#list-app'],
            ['id' => 'pres', 'label' => 'Prescription List', 'icon' => 'fa-medkit', 'target' => '#list-pres'],
            ['id' => 'lab', 'label' => 'Lab Test Orders', 'icon' => 'fa-flask', 'target' => '#list-lab'],
        ];
    } elseif ($role === 'admin') {
        $menuItems = [
            ['id' => 'dash', 'label' => 'Dashboard', 'icon' => 'fa-dashboard', 'target' => '#list-dash'],
            ['id' => 'doc', 'label' => 'Doctor List', 'icon' => 'fa-user-md', 'target' => '#list-doc'],
            ['id' => 'pat', 'label' => 'Patient List', 'icon' => 'fa-users', 'target' => '#list-pat'],
            ['id' => 'app', 'label' => 'Appointment Details', 'icon' => 'fa-calendar', 'target' => '#list-app'],
            ['id' => 'pres', 'label' => 'Prescription List', 'icon' => 'fa-file-text', 'target' => '#list-pres'],
            ['id' => 'add-doc', 'label' => 'Manage Doctors', 'icon' => 'fa-plus-circle', 'target' => '#list-settings'],
            ['id' => 'reports', 'label' => 'Reports & Analytics', 'icon' => 'fa-line-chart', 'target' => '#list-reports'],
            ['id' => 'mes', 'label' => 'User Queries', 'icon' => 'fa-envelope-open', 'target' => '#list-mes'],
            ['id' => 'reset', 'label' => 'Admin Tasks', 'icon' => 'fa-shield', 'target' => '#list-reset'],
            ['id' => 'staff', 'label' => 'Staff Management', 'icon' => 'fa-id-badge', 'target' => '#list-staff'],
            ['id' => 'lab', 'label' => 'Lab Reports', 'icon' => 'fa-flask', 'target' => '#list-lab'],
        ];
    }

    echo '<div class="app-sidebar shadow-sm">
            <div class="sidebar-header p-4 text-center">
                <i class="fa fa-heartbeat text-white fa-2x mb-2"></i>
                <h5 class="text-white mb-0 font-weight-bold">GLOBAL HOSPITALS</h5>
                <small class="text-white-50">'.strtoupper($role).' PORTAL</small>
            </div>
            <div class="list-group list-group-flush mt-4" id="list-tab" role="tablist">';
    
    foreach ($menuItems as $item) {
        $activeClass = ($item['id'] === $activePage) ? 'active' : '';
        echo '<a class="list-group-item list-group-item-action '.$activeClass.'" 
                 id="list-'.$item['id'].'-list" 
                 data-toggle="list" 
                 href="'.$item['target'].'" 
                 role="tab">
                <i class="fa '.$item['icon'].' mr-3"></i> '.$item['label'].'
              </a>';
    }

    echo '  </div>
            <div class="sidebar-footer mt-auto p-4">
                <a href="logout.php" class="btn btn-outline-light btn-block btn-sm">
                    <i class="fa fa-sign-out mr-2"></i> Logout
                </a>
            </div>
          </div>';
    // Note: sidebarBackdrop is rendered by app-header.php — do NOT duplicate it here.
}
?>
