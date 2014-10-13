<?php

class BrowserSort {
  
  function set_array($sort_array, $default_sort, $default_dir, $default_start, $limit) {
    
    // Sets up $sort array for use with $this->set_var() and $this->sql()
    // $sort_array:      (array) Use a semicolon with post-whitespace (; ) if header
    //                  name is diffrent to DB field ('db_field; header_name')
    // $default_sort:    (char) Initial header to order by
    // $default_dir:    (char) Initial direction to order column by
    // $default_start:  (int) Default starting point
    // $limit:          (int) Results per-page
    
    global $s, $d, $start, $sort;
    
    // Make variables safe for DB
    $start = !$start ? $default_start : ($start + 0);
    $s = !$s ? $default_sort : $s;
    $d = !$d ? $default_dir : $d;
    
    $sort = array(
      'sort_array'		=>  $sort_array,
      'default_sort'	=>  $default_sort,
      'default_dir'		=>  $default_dir,
      'default_start'	=>  $default_start,
      'order'					=>  $s,
      'dir'						=>  $d,
      'start'					=>  $start,
      'limit'					=>  $limit,
    );
  }
  
  function set_var($template, $block_name, $block_handle) {
    
    // Creates column headers with directional arrow where needed
    
    global $t, $sort, $REQUEST_URI;
    
    $t->set_block($template, $block_name, $block_handle);
    $t->set_var('arrow_dir_a', ($sort['dir'] == 'asc') ? 'desc' : 'asc');
    $t->set_var('arrow_dir_b', ($sort['dir'] == 'asc') ? 'asc' : 'desc');
		
		// Remove the existing var settings...
		$REQUEST_URI = preg_replace("/&[s|d]=[a-z_]+/", "", $REQUEST_URI);
		
    foreach($sort['sort_array'] as $order) {
      $order = explode('; ', $order); // Seporates DB field names / page column headers by semicolon
      $t->set_var('sort', $order[0]);
      $t->set_var('sort_name', isset($order[1]) ? $order[1] : $order[0]);
     
      if ($sort['order'] != $order[0]) $t->set_var("hide_{$order[0]}", '<!--'); // Hide arrow for every header except $sort[order]
      
      $t->parse($block_handle, $block_name, true);
    }
  }
  
  function sql() {
    // Creates the appropriate order command in SQL
    global $cfg, $sort;
    return(($sort['order'] ? " ORDER BY $sort[order] $sort[dir] " : "") . " limit $sort[start], $sort[limit]");
  }
}