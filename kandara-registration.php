<?php
/**
 * Plugin Name: Kandara Registration
 * Description: A custom plugin for Kandara Marathon registration form.
 * Version: 1.0
 * Author: Bensam Mwaniki
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Enqueue Bootstrap
function kandara_enqueue_styles() {
    wp_enqueue_style('bootstrap-css', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css');
    wp_enqueue_script('bootstrap-js', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js', array('jquery'), null, true);
    wp_enqueue_style('kandara-registration-style', plugin_dir_url(__FILE__) . 'style.css');
}
add_action('wp_enqueue_scripts', 'kandara_enqueue_styles');

// Create the registration form
function kandara_registration_form() {
    ob_start();
    ?>
    <form method="post" action="" class="kandara-form">
        <div class="container text-start">
            <div class="row align-items-start">
                <div class="form-group col-6">
                    <label for="last_name">First Name</label>
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

<hr style="width:100%;text-align:left;margin:20px 0px 20px 0px;">

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

                    </div>
                    <input type="submit" name="kandara_registration_submit" value="Submit and Proceed to Make Payment" class="btn btn-primary">
                </div>
        </div>
    </form>
    <?php
    return ob_get_clean();
}
add_shortcode('kandara_registration_form', 'kandara_registration_form');

// Handle form submission
function kandara_handle_form_submission() {
    if (isset($_POST['kandara_registration_submit'])) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'kandara_registrations';
        $wpdb->insert($table_name, array(
            'first_name' => sanitize_text_field($_POST['first_name']),
            'last_name' => sanitize_text_field($_POST['last_name']),
            'id_no' => sanitize_text_field($_POST['id_no']),
            'email' => sanitize_email($_POST['email']),
            'phone' => sanitize_text_field($_POST['phone']),
            'mpesa_phone' => sanitize_text_field($_POST['mpesa_phone']),
            'runner_category' => sanitize_text_field($_POST['runner_category']),
            'gender' => sanitize_text_field($_POST['gender']),
            'race' => sanitize_text_field($_POST['race']),
            'tshirt_size' => sanitize_text_field($_POST['tshirt_size']),
            'pickup_point' => sanitize_text_field($_POST['pickup_point']),
            'email_updates' => isset($_POST['email_updates']) ? 1 : 0,
            'whatsapp_updates' => isset($_POST['whatsapp_updates']) ? 1 : 0,
            'terms_conditions' => isset($_POST['terms_conditions']) ? 1 : 0,
    ));}
}
add_action('init', 'kandara_handle_form_submission');

// Create the database table on plugin activation
function kandara_create_db_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'kandara_registrations';
    $charset_collate = $wpdb->get_charset_collate();
    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        first_name varchar(255) NOT NULL,
        last_name varchar(255) NOT NULL,
        id_no varchar(255) NOT NULL,
        email varchar(255) NOT NULL,
        phone varchar(255) NOT NULL,
        mpesa_phone varchar(255),
        runner_category varchar(255) NOT NULL,
        gender varchar(255) NOT NULL,
        race varchar(255) NOT NULL,
        tshirt_size varchar(255) NOT NULL,
        pickup_point varchar(255) NOT NULL,
        email_updates tinyint(1),
        whatsapp_updates tinyint(1),
        terms_conditions tinyint(1),
        PRIMARY KEY  (id)
    ) $charset_collate;";
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}
register_activation_hook(__FILE__, 'kandara_create_db_table');

function kandara_enqueue_admin_scripts($hook) {
    if ($hook != 'toplevel_page_kandara-registrations') {
        return;
    }
    wp_enqueue_script('kandara-admin-js', plugin_dir_url(__FILE__) . 'admin.js', array('jquery'), null, true);
}
add_action('admin_enqueue_scripts', 'kandara_enqueue_admin_scripts');


// Display the data in admin area
function kandara_admin_menu() {
    add_menu_page('Kandara Registrations', 'Kandara Registrations', 'manage_options', 'kandara-registrations', 'kandara_registrations_page');
}
add_action('admin_menu', 'kandara_admin_menu');

function kandara_registrations_page() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'kandara_registrations';
    $results = $wpdb->get_results("SELECT * FROM $table_name");

    echo '<div class="wrap"><h1 style="text-align: center; margin-bottom: 20px;">Kandara Registrations</h1>';
    echo '<form method="post" action="">';
    echo '<table class="table table-hover table-bordered" style="width: 100%; border-collapse: collapse; border-spacing: 0;">';
    echo '<thead class="thead-dark"><tr>
            <th style="padding: 8px; background-color: #343a40; color: #fff; border: 1px solid #dee2e6; text-align: center;"><input type="checkbox" id="select_all"></th>
            <th style="padding: 8px; background-color: #343a40; color: #fff; border: 1px solid #dee2e6; text-align: center;">ID</th>
            <th style="padding: 8px; background-color: #343a40; color: #fff; border: 1px solid #dee2e6; text-align: center;">First Name</th>
            <th style="padding: 8px; background-color: #343a40; color: #fff; border: 1px solid #dee2e6; text-align: center;">Last Name</th>
            <th style="padding: 8px; background-color: #343a40; color: #fff; border: 1px solid #dee2e6; text-align: center;">ID No</th>
            <th style="padding: 8px; background-color: #343a40; color: #fff; border: 1px solid #dee2e6; text-align: center;">Email</th>
            <th style="padding: 8px; background-color: #343a40; color: #fff; border: 1px solid #dee2e6; text-align: center;">Phone</th>
            <th style="padding: 8px; background-color: #343a40; color: #fff; border: 1px solid #dee2e6; text-align: center;">Mpesa Phone</th>
            <th style="padding: 8px; background-color: #343a40; color: #fff; border: 1px solid #dee2e6; text-align: center;">Runner Category</th>
            <th style="padding: 8px; background-color: #343a40; color: #fff; border: 1px solid #dee2e6; text-align: center;">Gender</th>
            <th style="padding: 8px; background-color: #343a40; color: #fff; border: 1px solid #dee2e6; text-align: center;">Race</th>
            <th style="padding: 8px; background-color: #343a40; color: #fff; border: 1px solid #dee2e6; text-align: center;">T-Shirt Size</th>
            <th style="padding: 8px; background-color: #343a40; color: #fff; border: 1px solid #dee2e6; text-align: center;">Pickup Point</th>
            <th style="padding: 8px; background-color: #343a40; color: #fff; border: 1px solid #dee2e6; text-align: center;">Email Updates</th>
            <th style="padding: 8px; background-color: #343a40; color: #fff; border: 1px solid #dee2e6; text-align: center;">WhatsApp Updates</th>
            <th style="padding: 8px; background-color: #343a40; color: #fff; border: 1px solid #dee2e6; text-align: center;">Terms & Conditions</th>
          </tr></thead><tbody>';

    foreach ($results as $row) {
        echo '<tr>';
        echo '<td style="padding: 8px; border: 1px solid #dee2e6; text-align: center;"><input type="checkbox" class="row_checkbox" name="selected_ids[]" value="' . $row->id . '"></td>';
        echo '<td style="padding: 8px; border: 1px solid #dee2e6; text-align: center;">' . $row->id . '</td>';
        echo '<td style="padding: 8px; border: 1px solid #dee2e6; text-align: center;">' . $row->first_name . '</td>';
        echo '<td style="padding: 8px; border: 1px solid #dee2e6; text-align: center;">' . $row->last_name . '</td>';
        echo '<td style="padding: 8px; border: 1px solid #dee2e6; text-align: center;">' . $row->id_no . '</td>';
        echo '<td style="padding: 8px; border: 1px solid #dee2e6; text-align: center;">' . $row->email . '</td>';
        echo '<td style="padding: 8px; border: 1px solid #dee2e6; text-align: center;">' . $row->phone . '</td>';
        echo '<td style="padding: 8px; border: 1px solid #dee2e6; text-align: center;">' . $row->mpesa_phone . '</td>';
        echo '<td style="padding: 8px; border: 1px solid #dee2e6; text-align: center;">' . $row->runner_category . '</td>';
        echo '<td style="padding: 8px; border: 1px solid #dee2e6; text-align: center;">' . $row->gender . '</td>';
        echo '<td style="padding: 8px; border: 1px solid #dee2e6; text-align: center;">' . $row->race . '</td>';
        echo '<td style="padding: 8px; border: 1px solid #dee2e6; text-align: center;">' . $row->tshirt_size . '</td>';
        echo '<td style="padding: 8px; border: 1px solid #dee2e6; text-align: center;">' . $row->pickup_point . '</td>';
        echo '<td style="padding: 8px; border: 1px solid #dee2e6; text-align: center;">' . ($row->email_updates ? 'Yes' : 'No') . '</td>';
        echo '<td style="padding: 8px; border: 1px solid #dee2e6; text-align: center;">' . ($row->whatsapp_updates ? 'Yes' : 'No') . '</td>';
        echo '<td style="padding: 8px; border: 1px solid #dee2e6; text-align: center;">' . ($row->terms_conditions ? 'Yes' : 'No') . '</td>';
        echo '</tr>';
    }

    echo '</tbody></table>';
    echo '<button type="submit" name="delete_selected" class="btn btn-danger" style="margin:20px;">Delete Selected</button>';
    echo '<button type="submit" name="export_csv" class="btn btn-primary" style="margin:20px;">Export CSV</button>';
    echo '</form>';
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
function kandara_handle_actions() {
    global $wpdb;

    if (isset($_POST['delete_selected']) && isset($_POST['selected_ids'])) {
        $selected_ids = $_POST['selected_ids'];

        foreach ($selected_ids as $id) {
            $wpdb->delete($wpdb->prefix . 'kandara_registrations', array('id' => $id));
        }
    }

    if (isset($_POST['export_csv'])) {
        kandara_export_csv();
    }
}
add_action('admin_init', 'kandara_handle_actions');

function kandara_handle_delete() {
    global $wpdb;

    if (isset($_POST['delete_selected']) && isset($_POST['selected_ids'])) {
        $selected_ids = $_POST['selected_ids'];

        foreach ($selected_ids as $id) {
            $wpdb->delete($wpdb->prefix . 'kandara_registrations', array('id' => $id));
        }
    }
}
add_action('admin_init', 'kandara_handle_delete');

function kandara_export_csv() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'kandara_registrations';
    $results = $wpdb->get_results("SELECT * FROM $table_name", ARRAY_A);

    if (empty($results)) {
        return;
    }

    $filename = 'kandara_registrations_' . date('Y-m-d') . '.csv';
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment;filename=' . $filename);

    $output = fopen('php://output', 'w');
    $header = array_keys($results[0]);
    fputcsv($output, $header);

    foreach ($results as $row) {
        fputcsv($output, $row);
    }

    fclose($output);
    exit;
}
