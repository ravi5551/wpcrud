<?php
/*
  Plugin Name: WP-CRUD
  Plugin URI: https://www.ravibarale.com/
  Description: Plugin to Insert, Read, Update and Delete Users.
  Version: 1.0.0
  Author: Ravi Barale
  Author URI: https://www.ravibarale.com/
  License: GPL2
 */

/**
 * Add hook to create users table
 */
register_activation_hook(__FILE__, 'create_users_table');

function create_users_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'users_table';

    if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        $sql = "CREATE TABLE `$table_name` (`user_id` int(11) NOT NULL AUTO_INCREMENT,
        `user_name` varchar(500) DEFAULT NULL,`user_email` varchar(200) DEFAULT NULL,
        PRIMARY KEY(user_id));";
        dbDelta($sql);
    }
}

/**
 * Add plugin menu to admin menu bar to show crud page
 */
add_action('admin_menu', 'add_admin_menu');

function add_admin_menu() {
    add_menu_page('WP-CRUD', 'WP-CRUD', 'manage_options', __FILE__, 'wp_crud_page');
}

/**
 * Method to handle crud page operation
 * @global type $wpdb
 */
function wp_crud_page() {

    global $wpdb;
    $table_name = $wpdb->prefix . 'users_table';
    if (isset($_POST['insertuser'])) {
        $user_name = isset($_POST['insert_user_name']) ? $_POST['insert_user_name'] : "";
        $user_email = isset($_POST['insert_user_email']) ? $_POST['insert_user_email'] : "";

        //Insert query to add new user record
        $wpdb->query("INSERT INTO $table_name(user_name,user_email) VALUES('$user_name','$user_email')");
        echo "<script>window.location = 'admin.php?page=wp-crud.php';</script>";
    }
    if (isset($_POST['updateuser'])) {
        $user_id = isset($_POST['uptdid']) ? $_POST['uptdid'] : 0;
        $user_name = isset($_POST['updt_user_name']) ? $_POST['updt_user_name'] : "";
        $user_email = isset($_POST['updt_user_email']) ? $_POST['updt_user_email'] : "";

        if ($user_id > 0) {
            //Update query to existing user record
            $wpdb->query("UPDATE $table_name SET user_name='$user_name',user_email='$user_email' WHERE user_id='$user_id'");
        }
        echo "<script>window.location = 'admin.php?page=wp-crud.php';</script>";
    }
    if (isset($_GET['delt'])) {
        $user_id = $_GET['delt'];
        $wpdb->query("DELETE FROM $table_name WHERE user_id='$user_id'");
        echo "<script>location.replace('admin.php?page=wp-crud.php');</script>";
    }
    ?>
    <div class="wrap">
        <center><h2>Insert, Read, Update and Delete Users</h2></center>
        <table class="wp-list-table widefat striped">
            <thead>
                <tr>
                    <th width="10%">ID</th>
                    <th width="30%">Name</th>
                    <th width="30%">Email</th>
                    <th width="30%">Actions</th>
                </tr>
            </thead>
            <tbody>
            <form action="" method="post" >
                <tr>
                    <td><input type="text" value="" placeholder="ID" disabled></td>
                    <td><input style="width:100%" type="text" id="insert_user_name" name="insert_user_name" placeholder="Enter Full Name" required="required"></td>
                    <td><input style="width:100%" type="email" id="insert_user_email" name="insert_user_email" placeholder="Enter Email" required="required"></td>
                    <td><button id="insertuser" name="insertuser" type="submit">INSERT</button></td>
                </tr>
            </form>
            <?php
            $arr_users = $wpdb->get_results("SELECT * FROM $table_name");
            foreach ($arr_users as $user) {
                echo " <tr>
                <td width='10%'>$user->user_id</td>
                <td width='30%'>$user->user_name</td>
                <td width='30%'>$user->user_email</td>
                <td width='30%'><a href='admin.php?page=wp-crud.php&updt=$user->user_id'><button type='button'>UPDATE</button></a> <a href='admin.php?page=wp-crud.php&delt=$user->user_id'><button type='button'>DELETE</button></a></td>
              </tr>";
            }
            ?>
            </tbody>  
        </table>
        <br>
        <br>
        <?php
        if (isset($_GET['updt'])) {
            $updt_id = $_GET['updt'];
            $result = $wpdb->get_results("SELECT * FROM $table_name WHERE user_id='$updt_id'");
            foreach ($result as $print) {
                echo "<table class='wp-list-table widefat striped'>
                          <thead>
                            <tr>
                              <th width='25%'>ID</th>
                              <th width='25%'>Name</th>
                              <th width='25%'>Email</th>
                              <th width='25%'>Actions</th>
                            </tr>
                          </thead>
                          <tbody>
                            <form action='' method='post'>
                              <tr>
                                <td width='10%'>$print->user_id <input type='hidden' id='uptdid' name='uptdid' value='$print->user_id'></td>
                                <td width='30%'><input width='100%' type='text' id='updt_user_name' name='updt_user_name' value='$print->user_name' required='required'></td>
                                <td width='30%'><input width='100%' type='email' id='updt_user_email' name='updt_user_email' value='$print->user_email' required='required'></td>
                                <td width='30%'><button id='updateuser' name='updateuser' type='submit'>UPDATE</button> <a href='admin.php?page=wp-crud.php'><button type='button'>CANCEL</button></a></td>
                              </tr>
                            </form>
                          </tbody>
                        </table>";
            }
        }
        ?>
    </div>
    <?php
}
