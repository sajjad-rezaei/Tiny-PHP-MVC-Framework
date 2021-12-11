<?php

function get_hook($hook, ...$args ) {
    global $hook_events;

    if (isset($hook_events[$hook])) {

        foreach($hook_events[$hook] as $function) {
            
            if(is_callable($function) OR function_exists($function))
                call_user_func_array($function, $args);
        
        }
    }

}

function add_hook($hook, $func) {
    global $hook_events;
    $hook_events[$hook][] = $func;

}



