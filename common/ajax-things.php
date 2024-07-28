<?php
namespace List_Things;

add_action('wp_ajax_new_get_things', __NAMESPACE__ .  '\new_get_things'); // Logged-in users.
add_action('wp_ajax_nopriv_new_get_things', __NAMESPACE__ .  '\new_get_things'); // Not-logged-in users.
function new_get_things() {
  if (!isset($_POST['_ajax_nonce']) || !wp_verify_nonce($_POST['_ajax_nonce'], $_POST['_ajax_nonce_action'])) exit('Invalid nonce.');
  $args = isset($_POST['args']) ? sanitize_array($_POST['args']) : null;
  // echo '<pre>';
  //   var_dump($args);
  // echo '</pre>';
  $options = isset($_POST['options']) ? sanitize_array($_POST['options']) : null;
  ?>
    <div class="list-of-things">
      <?php echo get_things($args, $options); ?>
    </div>
  <?php
  die();
}
