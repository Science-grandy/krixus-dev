<?php
	function sanitize_input($input) {
	    if (is_array($input)) {
	        // If $input is an array, sanitize each element recursively
	        foreach ($input as $key => &$value) {
	            $value = $this->sanitize_input($value); 
	        }
	    } else {
	        // If $input is not an array, sanitize it as a string
	        $input = trim($input);
	        $input = stripslashes($input);
	        $input = htmlspecialchars($input);
	    }
	    return $input;
	}
?>