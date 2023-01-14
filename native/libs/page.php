<?php
    namespace native\libs;

    /**
     * Used in SSG (static site generation)
     */
    abstract class Page extends Controller {
        abstract public function get_static_props() : array;
        abstract public function render() : string;
    }
