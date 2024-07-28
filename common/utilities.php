<?php
namespace List_Things;

function format_list_of_things($things, $and_or = 'and') {
  if (!$things) return;
  if (!is_array($things)) return $things;
  $things = sanitize_array($things);
  $formatted_list = $things[0];
  if (count($things) == 2) {
    $formatted_list .= ' ' . $and_or . ' ' . $things[1];
  } elseif (count($things) > 2) {
    $i = 1;
    while ($i < count($things) - 1) {
      $formatted_list .= ', ' . $things[$i];
      $i++;
    }
    $formatted_list .= ' ' . $and_or . ' ' . $things[$i];
  }
  return $formatted_list;
}

function sanitize_array($array) {
  if (!$array) return;
  $truthy_vals = [1, '1', 'true'];
  $falsey_vals = [0, '0', 'false'];
  foreach($array as $key => $val) {
    if (is_bool($val)) continue;
    if (is_array($val)) {
      $array[$key] = sanitize_array($val);
    } elseif (is_string($val)) {
      $val = sanitize_text_field($val);
      if (is_numeric($val)) {
        $array[$key] = intval($val);
      } elseif (in_array(strtolower($val), $truthy_vals, true)) {
        $val = true;
      } elseif (in_array(strtolower($val), $falsey_vals, true)) {
        $val = false;
      }
      $array[$key] = $val;
    }
  }
  return $array;
}

function get_post_type_names($post_types) {
  if (is_string($post_types)) {
    $post_type_obj = get_post_type_object($post_types);
    if ($post_type_obj) {
      $post_type_names = get_post_type_labels($post_type_obj)->name;
    } else {
      $post_type_names = $post_types;
    }
  } elseif (is_array($post_types)) {
    foreach ($post_types as $post_type) {
      $post_type_obj = get_post_type_object($post_type);
      if ($post_type_obj) {
        $post_type_names[] = get_post_type_labels($post_type_obj)->name;
      } else {
        $post_type_names[] = $post_type;
      }
    }
  } else {
    $post_type_names = 'things';
  }
  return $post_type_names;
}