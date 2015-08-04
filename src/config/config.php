<?php

return array(
    'autoresolve' => true, // If this is disabled you'll need to call Lang::resolve() manually somewhere in your code
    'segment_index' => 1, // Language code position in the route segments (starting from 1)
    'use_database' => false, // Whether to rely on the database languages or the config ones only (false by default)
    'use_header' => false, // Whether to resolve from HTTP_ACCEPT_LANGUAGE as a route fallback or not (false by default)
    'available_locales' => array('en' => 'en_US', 'es' => 'es_ES', 'fr' => 'fr_FR', 'de' => 'de_DE', 'it' => 'it_IT')
);
