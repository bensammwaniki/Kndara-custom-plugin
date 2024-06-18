<?php
/**
 * Plugin Name: Kandara Marathon & Volunteer Registration
 * Description: Plugin for handling marathon and volunteer registrations.
 * Version: 3.0
 * Author: Bensam Mwaniki
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

function kandara_enqueue_styles() {
    wp_enqueue_style('bootstrap-css', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css');
    wp_enqueue_script('bootstrap-js', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js', array('jquery'), null, true);
    wp_enqueue_style('kandara-registration-style', plugin_dir_url(__FILE__) . 'style.css');
}
add_action('wp_enqueue_scripts', 'kandara_enqueue_styles');

// Define global variables for table names
global $wpdb;
$table_name = $wpdb->prefix . 'kandara_registrations';
$table_name_volunteers = $wpdb->prefix . 'kandara_volunteers';

// Create the registration table on plugin activation
register_activation_hook(__FILE__, 'kandara_create_registration_table');

function kandara_create_registration_table() {
    global $wpdb;

    $table_name = $wpdb->prefix . 'kandara_registrations';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        first_name varchar(100) NOT NULL,
        last_name varchar(100) NOT NULL,
        id_no varchar(20) NOT NULL,
        email varchar(100) NOT NULL,
        phone varchar(20) NOT NULL,
        mpesa_phone varchar(20) DEFAULT '',
        runner_category varchar(50) NOT NULL,
        race varchar(50) NOT NULL,
        tshirt_size varchar(5) NOT NULL,
        pickup_point varchar(255) NOT NULL,
        gender varchar(10) NOT NULL,
        email_updates tinyint(1) DEFAULT 0,
        whatsapp_updates tinyint(1) DEFAULT 0,
        terms_conditions tinyint(1) NOT NULL,
        PRIMARY KEY  (id)
    ) $charset_collate;";

    $table_name_volunteers = $wpdb->prefix . 'kandara_volunteers';

    $sql .= "CREATE TABLE $table_name_volunteers (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        first_name varchar(100) NOT NULL,
        last_name varchar(100) NOT NULL,
        id_no varchar(20) NOT NULL,
        email varchar(100) NOT NULL,
        phone varchar(20) NOT NULL,
        area_of_interest varchar(255) NOT NULL,
        experience text NOT NULL,
        availability text NOT NULL,
        terms_conditions tinyint(1) NOT NULL,
        PRIMARY KEY  (id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}

// Create the registration form shortcode
add_shortcode('kandara_registration_form', 'kandara_registration_form');

function kandara_registration_form() {
    ob_start();
    ?>
    <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" class="kandara-form">
        <input type="hidden" name="action" value="kandara_process_form">
        <?php wp_nonce_field('kandara-registration-form', 'kandara-registration-nonce'); ?>
        
        <div class="container text-start">
            <h2>Marathon Registration</h2>
            <div class="row align-items-start">
                <div class="form-group col-6">
                    <label for="first_name">First Name</label>
                    <input type="text" name="first_name" id="first_name" class="form-control" required>
                </div>
                <div class="form-group col-6">
                    <label for="last_name">Last Name</label>
                    <input type="text" name="last_name" id="last_name" class="form-control" required>
                </div>

                <div class="form-group col-6">
                    <label for="id_no">ID No</label>
                    <input type="text" name="id_no" id="id_no" class="form-control" required>
                </div>

                <div class="form-group col-6">
                    <label for="email">Email</label>
                    <input type="email" name="email" id="email" class="form-control" required>
                </div>

                <div class="form-group col-6">
                    <label for="phone">Phone Number</label>
                    <input type="text" name="phone" id="phone" class="form-control" required>
                </div>

                <div class="form-group col-6">
                    <label for="mpesa_phone">Mpesa Phone Number</label>
                    <input type="text" name="mpesa_phone" id="mpesa_phone" class="form-control">
                </div>
            </div>

            <hr style="width:100%;text-align:left;margin:20px 0px 20px 0px;">

            <div class="row align-items-start">
                <div class="form-group col-4">
                    <label for="runner_category">Runner Category</label>
                    <select name="runner_category" id="runner_category" class="form-select" required>
                        <option value="Adult">Adult</option>
                        <option value="Children">Children</option>
                    </select>
                </div>

                <div class="form-group col-4">
                    <label for="race">Preferred Race</label>
                    <select name="race" id="race" class="form-select" required>
                        <option value="21km">21km</option>
                        <option value="10km">10km</option>
                        <option value="5km">5km</option>
                    </select>
                </div>

                <div class="form-group col-4">
                    <label for="tshirt_size">Preferred T-Shirt Size</label>
                    <select name="tshirt_size" id="tshirt_size" class="form-select" required>
                        <option value="S">S</option>
                        <option value="M">M</option>
                        <option value="L">L</option>
                        <option value="XL">XL</option>
                    </select>
                </div>
            </div>

            <hr style="width:100%;text-align:left;margin:20px 0px 20px 0px;">

            <div class="form-group">
                <label for="pickup_point">Pick up Point for T-Shirt</label>
                <select name="pickup_point" id="pickup_point" class="form-select" required>
                    <option value="Nairobi (Pension Towers, Loita street 2nd Floor)">Nairobi (Pension Towers, Loita street 2nd Floor)</option>
                    <option value="Kandara (Kamurugu NTK Sacco Office)">Kandara (Kamurugu NTK Sacco Office)</option>
                    <option value="Thika (Arrow Dental Centre. Thika Gateway Plaza, Gatitu Next to Total Petrol Station)">Thika (Arrow Dental Centre. Thika Gateway Plaza, Gatitu Next to Total Petrol Station)</option>
                </select>
            </div>

            <div class="form-group">
                <label>Gender</label>
                <div class="form-check">
                    <input type="radio" name="gender" id="male" value="Male" class="form-check-input" required>
                    <label for="male" class="form-check-label">Male</label>
                </div>
                <div class="form-check">
                    <input type="radio" name="gender" id="female" value="Female" class="form-check-input" required>
                    <label for="female" class="form-check-label">Female</label>
                </div>
            </div>

            <div class="form-check form-switch">
                <input type="checkbox" name="email_updates" id="email_updates" class="form-check-input" role="switch">
                <label for="email_updates" class="form-check-label">I would like to receive email updates regarding the Marathon</label>
            </div>
            <div class="form-check form-switch">
                <input type="checkbox" name="whatsapp_updates" id="whatsapp_updates" class="form-check-input" role="switch">
                <label for="whatsapp_updates" class="form-check-label">I would like to join the race WhatsApp Community for updates and training tips</label>
            </div>
            <div class="form-check form-switch">
                <input type="checkbox" name="terms_conditions" id="terms_conditions" class="form-check-input" role="switch" required>
                <label for="terms_conditions" class="form-check-label">I acknowledge that I have completely read and fully understood, and do hereby accept the terms and conditions of The Kandara Education Run.</label>
            </div>

            <input type="submit" name="kandara_register" value="Submit and Proceed to Make Payment" class="btn btn-primary">
        </div>
    </form>

    <hr style="width:100%;text-align:left;margin:40px 0px 20px 0px;">
    <?php
    return ob_get_clean();
}

// Shortcode for volunteering registration form
function kandara_volunteer_registration_form() {
    ob_start(); 
    ?>
    <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" class="kandara-form">
        <input type="hidden" name="action" value="kandara_process_volunteer_form">
        <?php wp_nonce_field('kandara-volunteer-form', 'kandara-volunteer-nonce'); ?>

        <div class="container text-start">
            <h2>Volunteer Registration</h2>
            <div class="row align-items-start">
                <div class="form-group col-6">
                    <label for="first_name_volunteer">First & Second Name</label>
                    <input type="text" name="first_name_volunteer" id="first_name" class="form-control" required>
                </div>
                <div class="form-group col-6">
                    <label for="last_name_volunteer">Last Name</label>
                    <input type="text" name="last_name_volunteer" id="last_name" class="form-control" required>
                </div>

                <div class="form-group col-6">
                    <label for="email_volunteer">Email</label>
                    <input type="email" name="email_volunteer" id="email" class="form-control" required>
                </div>
                <div class="form-group col-6">
                    <label for="phone_volunteer">Phone Number</label>
                    <input type="text" name="phone_volunteer" id="phone" class="form-control" required>
                </div>
                <hr style="width:100%;text-align:left;margin:20px 0px 20px 0px;">

                <div class="form-group col-6">
                    <label for="area_of_interest">Please select the role you would like to volunteer</label>
                    <select name="area_of_interest" id="area_of_interest" class="form-select" required>
                        <option value="Nairobi (Pension Towers, Loita street 2nd Floor)">Nairobi (Pension Towers, Loita street 2nd Floor)</option>
                        <option value="Kandara (Kamurugu NTK Sacco Office)">Kandara (Kamurugu NTK Sacco Office)</option>
                        <option value="Thika (Arrow Dental Centre. Thika Gateway Plaza, Gatitu Next to Total Petrol Station)">Thika (Arrow Dental Centre. Thika Gateway Plaza, Gatitu Next to Total Petrol Station)</option>
                    </select>
                </div>

                <div class="form-group col-6"> 
                   <label for="kandara_registration_submit">* Please note all areas must be filled to volunteer</label>
                    <input type="submit" name="kandara_registration_submit" value="Volunteer" class="btn btn-primary">
                </div>
            </div> 
        </div> 
    </form>

    <?php
    return ob_get_clean();
}

add_shortcode('kandara_volunteer_registration_form', 'kandara_volunteer_registration_form');

add_action('admin_post_nopriv_kandara_process_form', 'kandara_handle_form_submission');
add_action('admin_post_kandara_process_form', 'kandara_handle_form_submission');
function kandara_handle_form_submission() {
    if (isset($_POST['kandara_register'])) {
        if (!isset($_POST['kandara-registration-nonce']) || !wp_verify_nonce($_POST['kandara-registration-nonce'], 'kandara-registration-form')) {
            wp_die('Nonce verification failed');
        }

        global $wpdb;

        $table_name = $wpdb->prefix . 'kandara_registrations';

        $first_name = sanitize_text_field($_POST['first_name']);
        $last_name = sanitize_text_field($_POST['last_name']);
        $id_no = sanitize_text_field($_POST['id_no']);
        $email = sanitize_email($_POST['email']);
        $phone = sanitize_text_field($_POST['phone']);
        $mpesa_phone = sanitize_text_field($_POST['mpesa_phone']);
        $runner_category = sanitize_text_field($_POST['runner_category']);
        $race = sanitize_text_field($_POST['race']);
        $tshirt_size = sanitize_text_field($_POST['tshirt_size']);
        $pickup_point = sanitize_text_field($_POST['pickup_point']);
        $gender = sanitize_text_field($_POST['gender']);
        $email_updates = isset($_POST['email_updates']) ? 1 : 0;
        $whatsapp_updates = isset($_POST['whatsapp_updates']) ? 1 : 0;
        $terms_conditions = isset($_POST['terms_conditions']) ? 1 : 0;

        $wpdb->insert(
            $table_name,
            array(
                'first_name' => $first_name,
                'last_name' => $last_name,
                'id_no' => $id_no,
                'email' => $email,
                'phone' => $phone,
                'mpesa_phone' => $mpesa_phone,
                'runner_category' => $runner_category,
                'race' => $race,
                'tshirt_size' => $tshirt_size,
                'pickup_point' => $pickup_point,
                'gender' => $gender,
                'email_updates' => $email_updates,
                'whatsapp_updates' => $whatsapp_updates,
                'terms_conditions' => $terms_conditions,
            )
        );

        // Redirect to thank you page
        echo '<script>
                alert("Thank you for volunteering!");
              </script>';
    }
}

function kandara_handle_volunteer_form_submission() {
    if (isset($_POST['kandara_registration_submit'])) { 
        if (!isset($_POST['kandara-volunteer-nonce']) || !wp_verify_nonce($_POST['kandara-volunteer-nonce'], 'kandara-volunteer-form')) {
            wp_die('Nonce verification failed');
        }

        global $wpdb;

        $table_name = $wpdb->prefix . 'kandara_volunteers';

        $first_name = sanitize_text_field($_POST['first_name_volunteer']);
        $last_name = sanitize_text_field($_POST['last_name_volunteer']);
        $email = sanitize_email($_POST['email_volunteer']);
        $phone = sanitize_text_field($_POST['phone_volunteer']);
        $area_of_interest = sanitize_text_field($_POST['area_of_interest']);

        $insert_result = $wpdb->insert(
            $table_name,
            array(
                'first_name' => $first_name,
                'last_name' => $last_name,
                'email' => $email,
                'phone' => $phone,
                'area_of_interest' => $area_of_interest,
            )
        );

        echo '<script>
                alert("Thank you for volunteering!");
              </script>';
    }
}
add_action('admin_post_nopriv_kandara_process_volunteer_form', 'kandara_handle_volunteer_form_submission');
add_action('admin_post_kandara_process_volunteer_form', 'kandara_handle_volunteer_form_submission');

// Display registrations in admin panel
add_action('admin_menu', 'kandara_plugin_menu');

function kandara_plugin_menu() {
    add_menu_page(
        'Kandara Registrations',
        'Kandara Registrations',
        'manage_options',
        'kandara-registrations',
        'kandara_registrations_page'
    );
}
function kandara_registrations_page() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'kandara_registrations';
    $results = $wpdb->get_results("SELECT * FROM $table_name");

    echo '<div class="wrap"><h1 style="text-align: center; margin-bottom: 20px;">Kandara Registrations</h1>';
    echo '<form method="post" action="' . esc_url(admin_url('admin-post.php')) . '">';
    echo '<input type="hidden" name="action" value="kandara_delete_selected_rows">';
    wp_nonce_field('kandara_delete_selected', 'kandara_delete_nonce');
    echo '<input type="hidden" name="table_name" value="kandara_registrations">';
    echo '<table class="table table-hover table-bordered" style="width: 100%; border-collapse: collapse; border-spacing: 0;">';
    echo '<thead class="thead-dark"><tr>
            <th style="padding: 8px; background-color: #262261; color: #fff; border: 1px solid #dee2e6; text-align: center;"><input type="checkbox" id="select_all"></th>
            <th style="padding: 8px; background-color: #262261; color: #fff; border: 1px solid #dee2e6; text-align: center;">ID</th>
            <th style="padding: 8px; background-color: #262261; color: #fff; border: 1px solid #dee2e6; text-align: center;">First Name</th>
            <th style="padding: 8px; background-color: #262261; color: #fff; border: 1px solid #dee2e6; text-align: center;">Last Name</th>
            <th style="padding: 8px; background-color: #262261; color: #fff; border: 1px solid #dee2e6; text-align: center;">ID No</th>
            <th style="padding: 8px; background-color: #262261; color: #fff; border: 1px solid #dee2e6; text-align: center;">Email</th>
            <th style="padding: 8px; background-color: #262261; color: #fff; border: 1px solid #dee2e6; text-align: center;">Phone</th>
            <th style="padding: 8px; background-color: #262261; color: #fff; border: 1px solid #dee2e6; text-align: center;">Mpesa Phone</th>
            <th style="padding: 8px; background-color: #262261; color: #fff; border: 1px solid #dee2e6; text-align: center;">Runner Category</th>
            <th style="padding: 8px; background-color: #262261; color: #fff; border: 1px solid #dee2e6; text-align: center;">Race</th>
            <th style="padding: 8px; background-color: #262261; color: #fff; border: 1px solid #dee2e6; text-align: center;">T-Shirt Size</th>
            <th style="padding: 8px; background-color: #262261; color: #fff; border: 1px solid #dee2e6; text-align: center;">Pickup Point</th>
            <th style="padding: 8px; background-color: #262261; color: #fff; border: 1px solid #dee2e6; text-align: center;">Gender</th>
            <th style="padding: 8px; background-color: #262261; color: #fff; border: 1px solid #dee2e6; text-align: center;">Email Updates</th>
            <th style="padding: 8px; background-color: #262261; color: #fff; border: 1px solid #dee2e6; text-align: center;">WhatsApp Updates</th>
            <th style="padding: 8px; background-color: #262261; color: #fff; border: 1px solid #dee2e6; text-align: center;">Terms Conditions</th>
          </tr></thead><tbody>';

    foreach ($results as $row) {
        echo '<tr>';
        echo '<td style="padding: 8px; border: 1px solid #dee2e6; text-align: center;"><input type="checkbox" class="row_checkbox" name="selected_ids[]" value="' . $row->id . '"></td>';
        echo '<td style="padding: 8px; border: 1px solid #dee2e6; text-align: center;">' . $row->id . '</td>';
        echo '<td style="padding: 8px; border: 1px solid #dee2e6; text-align: center;">' . $row->first_name . '</td>';
        echo '<td style="padding: 8px; border: 1px solid #dee2e6; text-align: center;">' . $row->last_name . '</td>';
        echo '<td style="padding: 8px; border: 1px solid #dee2e6; text-align: center;">'

 . $row->id_no . '</td>';
        echo '<td style="padding: 8px; border: 1px solid #dee2e6; text-align: center;">' . $row->email . '</td>';
        echo '<td style="padding: 8px; border: 1px solid #dee2e6; text-align: center;">' . $row->phone . '</td>';
        echo '<td style="padding: 8px; border: 1px solid #dee2e6; text-align: center;">' . $row->mpesa_phone . '</td>';
        echo '<td style="padding: 8px; border: 1px solid #dee2e6; text-align: center;">' . $row->runner_category . '</td>';
        echo '<td style="padding: 8px; border: 1px solid #dee2e6; text-align: center;">' . $row->race . '</td>';
        echo '<td style="padding: 8px; border: 1px solid #dee2e6; text-align: center;">' . $row->tshirt_size . '</td>';
        echo '<td style="padding: 8px; border: 1px solid #dee2e6; text-align: center;">' . $row->pickup_point . '</td>';
        echo '<td style="padding: 8px; border: 1px solid #dee2e6; text-align: center;">' . $row->gender . '</td>';
        echo '<td style="padding: 8px; border: 1px solid #dee2e6; text-align: center;">' . ($row->email_updates ? 'Yes' : 'No') . '</td>';
        echo '<td style="padding: 8px; border: 1px solid #dee2e6; text-align: center;">' . ($row->whatsapp_updates ? 'Yes' : 'No') . '</td>';
        echo '<td style="padding: 8px; border: 1px solid #dee2e6; text-align: center;">' . ($row->terms_conditions ? 'Yes' : 'No') . '</td>';
        echo '</tr>';
    }


    echo '</tbody></table>';
    echo '<button type="submit" name="delete_selected" class="btn btn-danger" style="margin:20px;">Delete Selected</button>';
    echo '</form>';
    echo '<form id="form-export" method="post" action="' . esc_url(admin_url('admin-post.php')) . '">';
    echo '<input type="hidden" name="action" value="kandara_export_csv">';
    echo '<input type="hidden" name="table_name" value="kandara_registrations">';
    wp_nonce_field('kandara_export_csv', 'kandara_export_nonce');
    echo '<button type="submit" name="export_csv" class="btn btn-primary" style="margin:20px;">Export CSV</button>';
    echo '</form>';
    echo '</div>';
    echo '<script type="text/javascript">
        document.getElementById("select_all").onclick = function() {
            var checkboxes = document.getElementsByClassName("row_checkbox");
            for (var checkbox of checkboxes) {
                checkbox.checked = this.checked;
            }
        }
    </script>';
}
// Display volunteer registrations in admin panel
add_action('admin_menu', 'kandara_volunteer_menu');

function kandara_volunteer_menu() {
    add_submenu_page(
        'kandara-registrations',
        'Kandara Volunteer Registrations',
        'Volunteer Registrations',
        'manage_options',
        'kandara-volunteer-registrations',
        'kandara_volunteer_registrations_page'
    );
}

function kandara_volunteer_registrations_page() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'kandara_volunteers';
    $results = $wpdb->get_results("SELECT * FROM $table_name");

    echo '<div class="wrap"><h1 style="text-align: center; margin-bottom: 20px;">Volunteer Registrations</h1>';
    echo '<form method="post" action="' . esc_url(admin_url('admin-post.php')) . '">';
    echo '<input type="hidden" name="action" value="kandara_delete_selected_rows">';
    wp_nonce_field('kandara_delete_selected', 'kandara_delete_nonce');
    echo '<input type="hidden" name="table_name" value="kandara_volunteers">';
    echo '<table class="table table-hover table-bordered" style="width: 100%; border-collapse: collapse; border-spacing: 0;">';
    echo '<thead class="thead-dark"><tr>
            <th style="padding: 8px; background-color: #262261; color: #fff; border: 1px solid #dee2e6; text-align: center;"><input type="checkbox" id="select_all_volunteers"></th>
            <th style="padding: 8px; background-color: #262261; color: #fff; border: 1px solid #dee2e6; text-align: center;">ID</th>
            <th style="padding: 8px; background-color: #262261; color: #fff; border: 1px solid #dee2e6; text-align: center;">First & Sec Name</th>
            <th style="padding: 8px; background-color: #262261; color: #fff; border: 1px solid #dee2e6; text-align: center;">Last Name</th>
            <th style="padding: 8px; background-color: #262261; color: #fff; border: 1px solid #dee2e6; text-align: center;">Phone</th>
            <th style="padding: 8px; background-color: #262261; color: #fff; border: 1px solid #dee2e6; text-align: center;">Email</th>
            <th style="padding: 8px; background-color: #262261; color: #fff; border: 1px solid #dee2e6; text-align: center;">Volunteer Category</th>
          </tr></thead><tbody>';

    foreach ($results as $row) {
        echo '<tr>';
        echo '<td style="padding: 8px; border: 1px solid #dee2e6; text-align: center;"><input type="checkbox" class="row_checkbox_volunteers" name="selected_ids[]" value="' . $row->id . '"></td>';
        echo '<td style="padding: 8px; border: 1px solid #dee2e6; text-align: center;">' . $row->id . '</td>';
        echo '<td style="padding: 8px; border: 1px solid #dee2e6; text-align: center;">' . $row->first_name . '</td>';
        echo '<td style="padding: 8px; border: 1px solid #dee2e6; text-align: center;">' . $row->last_name . '</td>';
        echo '<td style="padding: 8px; border: 1px solid #dee2e6; text-align: center;">' . $row->phone . '</td>';
        echo '<td style="padding: 8px; border: 1px solid #dee2e6; text-align: center;">' . $row->email . '</td>';
        echo '<td style="padding: 8px; border: 1px solid #dee2e6; text-align: center;">' . $row->area_of_interest . '</td>';
        echo '</tr>';
    }

    echo '</tbody></table>';
    echo '<button type="submit" name="delete_selected" class="btn btn-danger" style="margin:20px;">Delete Selected</button>';
    echo '</form>';
    echo '<form id="form-export" method="post" action="' . esc_url(admin_url('admin-post.php')) . '">';
    echo '<input type="hidden" name="action" value="kandara_export_csv">';
    echo '<input type="hidden" name="table_name" value="kandara_volunteers">';
    wp_nonce_field('kandara_export_csv', 'kandara_export_nonce');
    echo '<button type="submit" name="export_csv" class="btn btn-primary" style="margin:20px;">Export CSV</button>';
    echo '</form>';
    echo '</div>';
    echo '<script type="text/javascript">
        document.getElementById("select_all_volunteers").onclick = function() {
            var checkboxes = document.getElementsByClassName("row_checkbox_volunteers");
            for (var checkbox of checkboxes) {
                checkbox.checked = this.checked;
            }
        }
    </script>';
}

// delete function

function kandara_delete_selected_rows() {
    if (isset($_POST['delete_selected']) && isset($_POST['selected_ids']) && isset($_POST['table_name'])) {
        if (!isset($_POST['kandara_delete_nonce']) || !wp_verify_nonce($_POST['kandara_delete_nonce'], 'kandara_delete_selected')) {
            wp_die('Nonce verification failed');
        }

        global $wpdb;
        $table_name = $wpdb->prefix . sanitize_text_field($_POST['table_name']);
        $selected_ids = implode(",", array_map('intval', $_POST['selected_ids']));
        $wpdb->query("DELETE FROM $table_name WHERE id IN ($selected_ids)");

        wp_redirect($_SERVER['HTTP_REFERER']);
        exit;
    }
}
add_action('admin_post_kandara_delete_selected_rows', 'kandara_delete_selected_rows');
// export

function kandara_export_csv() {
    if (!isset($_POST['kandara_export_nonce']) || !wp_verify_nonce($_POST['kandara_export_nonce'], 'kandara_export_csv')) {
        wp_die('Nonce verification failed.');
    }

    if (isset($_POST['export_csv']) && isset($_POST['table_name'])) {
        global $wpdb;
        $table_name = $wpdb->prefix . sanitize_text_field($_POST['table_name']);
        $results = $wpdb->get_results("SELECT * FROM $table_name", ARRAY_A);

        if (empty($results)) {
            wp_die('No data found to export.');
        }

        $filename = $table_name . '_' . date('Y-m-d_H-i-s') . '.csv';

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=' . $filename);

        $output = fopen('php://output', 'w');

        fputcsv($output, array_keys($results[0]));

        // Output the rows
        foreach ($results as $row) {
            fputcsv($output, $row);
        }

        fclose($output);
        exit;
    }
}
add_action('admin_post_kandara_export_csv', 'kandara_export_csv');

?>
