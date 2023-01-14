<?php
    namespace native\libs;

    /**
     * Used in SSG (static site generation)
     */
    abstract class Page extends Controller {
        // Algo via middleware sans doute :
        // - Si on en est arrivé là, c'est que le cache n'a pas été touché
        // - Donc on produit le cache là de suite
        // - Si on reçoit un paramètre d'URL GET comme "action=revalidate"
        // -- On refait la page et on la remet en cache
        // - Il faudrait une commande : ezekiel static:build
        // - Cette commande appellerait toutes les pages une par une pour les prebuild
        public function get_static_props(Request $req) : array { return []; }
        abstract public function render(Request $req, Response $res) : void;
    }
