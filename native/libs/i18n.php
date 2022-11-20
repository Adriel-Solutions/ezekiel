<?php
    namespace native\libs;
    
    /**
     * This library is responsible for internationalizing strings
     * all across the project. It both loads the translations, and provides
     * a quick and handy way to internationalize any given string
     */
    class I18n {

        // The current locale, meant to be served to the end user
        private static $active_locale;

        // The list of all the supported locales across the project
        private static $supported_locales = [];

        // The list of per-language dictionary, identifed by their locale name
        // ( en-US -> list of string-string )
        private static $translations = [];

        /**
         * Defines the default locale for the project
         * and loads all the translations using a "lookup table" require
         */
        public static function load() {
            self::$active_locale = Options::get('I18N_DEFAULT_LOCALE');
            require __DIR__ . '/../../app/translations/index.php';
            setLocale(LC_ALL, self::$active_locale . '.UTF8');
        }

        /**
         * Defines the project-wide locale for the end user
         * If the locale isn't supported, nothing's changed
         *
         * @param {string} $locale The short code for the locale (en-US)
         */
        public static function set_locale($locale) {
            if(!in_array($locale, self::$supported_locales)) return;

            self::$active_locale = $locale;
        }

        /**
         * Stores a new (locale -> dictionary of string-string) inside the library
         *
         * @param {string} $locale The short code for the locale (en-US)
         * @param {array} $dictionary The list of string-string defining the locale
         */
        public static function append_translation($locale, $dictionary) {
            self::$translations[$locale] = $dictionary;
            self::$supported_locales[] = $locale;
        }

        /**
         * Translates a given string using the project-wide defined locale
         *
         * @param {string} $key The string to translate
         * @return {string} The translation of $key if ever found, else the $key unchanged
         */
        public static function translate($key) {
            if(!isset(self::$translations[self::$active_locale])) return $key;

            $current_dictionary = self::$translations[self::$active_locale];

            if(!isset($current_dictionary[$key])) return $key;

            return $current_dictionary[$key];
        }

        /**
         * Returns the list of supported locales in the internal library
         * 
         * @return {array<string>} The list of short locales (en, it)
         */
        public static function get_supported_locales() {
            return self::$supported_locales;
        }

        public static function get_active_locale() {
            return self::$active_locale;
        }
    }
