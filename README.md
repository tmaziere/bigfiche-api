# bigfiche-api

_REST API part of Bigfiche_

## Ce qui est versionné
- **composer.json** et **composer.lock** : au moment du déploiement il faut utiliser _composer install_ (PAS update) pour conserver les versions du lock
- **dev_tools/** : fonctionnement à confirmer
- config/**prod.php** : doit contenir une configuration pleinement fonctionnelle (hors données sensibles)

## Ce qui n'est PAS versionné
- www/**index_dev.php** : doit rester la copie de index.php, sauf appel de config/dev.php et tests éventuels
- www/**.htaccess** (penser à rediriger sur index_dev.php en dev)
- config/dev.php (strictement réservé à la config dev, puis appelle prod.php)
- config/security.php : mots de passe et données sensibles, stratégie à revoir (appelé par prod.php)
