<?php

namespace Thor\Language;

/**
 * Model for the languages table
 * 
 * @property int $id
 * @property string $name
 * @property string $code International language code with 2 letter (ISO 639-1) 
 * or 3 letter (ISO 639-2, ISO 639-3 or ISO 639-5). It is used for URL segments.
 * @property string $locale International locale code in
 * the format [language[_territory][.codeset][@modifier]]
 *  e.g. : en_US, en_AU.UTF-8 .<br>
 * It is used for identify translation locales.
 * @property boolean $is_active
 * @property int $sorting
 */
class Language extends \Illuminate\Database\Eloquent\Model
{

    /**
     *
     * @var array
     */
    protected $guarded = array();
    public static $rules = array(
        'name' => 'required',
        'code' => 'required|unique:languages'
    );

    /**
     * 
     * @return Language[]
     */
    public static function scopeSorted($query)
    {
        return $query->orderBy('sorting', 'asc');
    }

    /**
     * 
     * @return Language[]
     */
    public static function scopeActive($query)
    {
        return $query->where('is_active', '=', 1);
    }

    /**
     * 
     * @return Language[]
     */
    public static function scopeByCode($query, $code)
    {
        return $query->whereRaw('(code=?)', array($code));
    }

    /**
     * 
     * @return Language[]
     */
    public static function scopeByLocale($query, $locale)
    {
        return $query->whereRaw('(locale=?)', array($locale));
    }

    /**
     * 
     * @return Language[]
     */
    public static function scopeToAssoc($query)
    {
        $langs = $query->get();
        $langs_assoc = array();
        foreach($langs as $lang) {
            $langs_assoc[$lang->code] = $lang;
        }
        return $langs_assoc;
    }

}
