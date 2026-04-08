<?php

/*Les valeurs getenv() permettent de ne pas expo clé */

define('DB_HOST', getenv('DB_HOST') ?: 'db.3wa.io');
define('DB_NAME', getenv('DB_NAME') ?: 'lenimette_portfolio_sprint');
define('DB_USER', getenv('DB_USER') ?: 'lenimette');
define('DB_PASS', getenv('DB_PASS') ?: 'f6239dd1394ac41e40e1bf0c31c7d039');