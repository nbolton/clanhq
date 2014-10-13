var left_panels_array = new Array({left_panels_array});
var right_panels_array = new Array({right_panels_array});

function append_dropdown(side, panel, count) {
  temp.innerHTML = document.getElementById(panel).innerHTML;
  temp.innerHTML = temp.innerHTML.replace(/%panel_side%/g, side);
  temp.innerHTML = temp.innerHTML.replace(/%panel_order%/g, count);
  temp.innerHTML = temp.innerHTML.replace(/%panel_opposite_side%/g, (side == "left" ? "right" : "left"));
  temp.innerHTML = temp.innerHTML.replace(/%swap_side_icon%/g, (side == "left" ? ">" : "<"));
  document.getElementById('panels_output_' + side).innerHTML += temp.innerHTML;
}

function build_dropdowns() {
  panels_output_left.innerHTML = "";
  panels_output_right.innerHTML = "";
  
  for (i = 0; i < left_panels_array.length; i++) {
    if (left_panels_array[i] != "") {
      append_dropdown('left', left_panels_array[i] + '_panel', i);
	}
  }
  
  for (i = 0; i < right_panels_array.length; i++) {
    if (right_panels_array[i] != "") {
      append_dropdown('right', right_panels_array[i] + '_panel', i);
	}
  }
}

function modify_order(old_key, side, direction) {
  switch (side) {
    case "left": side_array = left_panels_array; break;
    case "right": side_array = right_panels_array; break;
  }
  
  switch (direction) {
    case "up": new_key = old_key - 1; break;
	case "down": new_key = (old_key - 1) + 2; break;
  }
  
  // Cannot shift off end of array!
  if ((new_key < 0) && (direction == "up")) { return false; }
  if ((new_key >= side_array.length) && (direction == "down")) { return false; }
  
  old_value = side_array[old_key];
  new_value = side_array[new_key];
  side_array[new_key] = old_value;
  side_array[old_key] = new_value;
  
  build_dropdowns();
}

function swap_sides(panel_key, old_side, new_side) {
  switch (old_side) {
    case "left":
	  old_side_array = left_panels_array;
	  new_side_array = right_panels_array;
	break;
    case "right":
	  old_side_array = right_panels_array;
	  new_side_array = left_panels_array;
	break;
  }
  
  new_side_array[new_side_array.length] = old_side_array[panel_key];
  old_side_array[panel_key] = "";
  
  build_dropdowns();
}