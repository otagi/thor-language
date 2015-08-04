<?php

namespace Thor\Language;

use Closure,
    Lang;
use Illuminate\Routing\Router as IlluminateRouter;

/**
 * A Router with multilingual features
 */
class Router extends IlluminateRouter
{

    /**
     * Create a route group with shared attributes, 
     * using the current language code as the prefix.
     * 
     * If you specify a prefix in the attributes variable
     * it will be appended to the language prefix like: en/myprefix
     *
     * @param  array|Closure    $attributes Attributes or callback
     * @param  Closure|null     $callback
     * @return void
     */
    public function langGroup($attributes, Closure $callback = null)
    {
        if($attributes instanceof Closure) {
            return $this->group(array('prefix' => Lang::code()), $attributes);
        } elseif(is_array($attributes)) {
            $attributes['prefix'] = trim(isset($attributes['prefix']) ? (Lang::code() . '/' . $attributes['prefix']) : Lang::code(), '/');
            $this->group($attributes, $callback);
        } else {
            throw new \InvalidArgumentException('First argument must be of type Closure or array');
        }
    }

}
