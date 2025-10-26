# 🚀 Guide d'Installation - Plugin Log Viewer pour CakePHP

## 📋 Vue d'ensemble

Ce guide vous explique comment installer et utiliser le plugin **Log Viewer** dans votre projet CakePHP 4 ou 5. Ce plugin vous permet de visualiser, filtrer et exporter vos fichiers de logs via une interface web moderne.

## 🎯 Prérequis

- CakePHP 4.0+ ou 5.0+
- PHP 7.4+
- Composer installé
- Accès à un dépôt Git (GitHub/GitLab/Bitbucket)

## 📦 Installation

### Méthode 1 : Via Composer (Recommandé)

#### Étape 1 : Ajouter le repository

Ouvrez votre fichier `composer.json` à la racine de votre projet et ajoutez le repository VCS :

```json
{
    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/badrtairlbahrepro/cakephp-log-viewer.git"
        }
    ],
    "require": {
        "badrtairlbahrepro/cakephp-log-viewer": "dev-main"
    },
    "minimum-stability": "dev",
    "prefer-stable": true
}
```

#### Étape 2 : Installer le plugin

Dans votre terminal, exécutez la commande :

```bash
composer require badrtairlbahrepro/cakephp-log-viewer:dev-main
```

Composer va automatiquement :
- Télécharger le plugin depuis GitHub
- L'installer dans le dossier `vendor/badrtairlbahrepro/cakephp-log-viewer/`
- Mettre à jour votre fichier `composer.lock`

### Méthode 2 : Clonage direct

Si vous préférez garder le contrôle total :

```bash
cd plugins/
git clone https://github.com/badrtairlbahrepro/cakephp-log-viewer.git log-viewer
```

Ensuite, exécutez `composer install` à la racine de votre projet.

## ⚙️ Configuration

### Pour CakePHP 5

#### 1. Charger le plugin dans `src/Application.php`

Ouvrez le fichier `src/Application.php` et ajoutez le plugin dans la méthode `bootstrap()` :

```php
<?php

namespace App;

use Cake\Http\BaseApplication;

class Application extends BaseApplication
{
    public function bootstrap(): void
    {
        parent::bootstrap();
        
        // Charger les plugins
        $this->addPlugin('Migrations');
        $this->addPlugin('LogViewer'); // 👈 Ajoutez cette ligne
        
        // ... reste de votre code
    }
}
```

#### 2. Configurer les routes dans `config/routes.php`

Ouvrez le fichier `config/routes.php` et ajoutez les routes pour le plugin :

```php
$routes->scope('/', function (RouteBuilder $builder): void {
    
    // ... vos routes existantes ...
    
    // Routes du plugin Log Viewer
    $builder->scope('/logs', function (RouteBuilder $routes) {
        $routes->connect('/', ['plugin' => 'LogViewer', 'controller' => 'Logs', 'action' => 'index']);
        $routes->connect('/export/*', ['plugin' => 'LogViewer', 'controller' => 'Logs', 'action' => 'export']);
        $routes->connect('/view/*', ['plugin' => 'LogViewer', 'controller' => 'Logs', 'action' => 'view']);
        $routes->connect('/clear/*', ['plugin' => 'LogViewer', 'controller' => 'Logs', 'action' => 'clear']);
        $routes->connect('/download/*', ['plugin' => 'LogViewer', 'controller' => 'Logs', 'action' => 'download']);
    });
    
    // ... reste de vos routes ...
});
```

### Pour CakePHP 4

#### 1. Charger le plugin dans `src/Application.php`

```php
<?php

namespace App;

use Cake\Http\BaseApplication;

class Application extends BaseApplication
{
    public function bootstrap(): void
    {
        parent::bootstrap();
        
        // Charger les plugins
        $this->addPlugin('Migrations');
        $this->addPlugin('LogViewer'); // 👈 Ajoutez cette ligne
        
        // ... reste de votre code
    }
}
```

#### 2. Configurer les routes dans `config/routes.php`

```php
$routes->scope('/', function (RouteBuilder $builder): void {
    
    // ... vos routes existantes ...
    
    // Routes du plugin Log Viewer
    $builder->scope('/logs', function (RouteBuilder $routes) {
        $routes->connect('/', ['plugin' => 'LogViewer', 'controller' => 'Logs', 'action' => 'index']);
        $routes->connect('/export/*', ['plugin' => 'LogViewer', 'controller' => 'Logs', 'action' => 'export']);
        $routes->connect('/view/*', ['plugin' => 'LogViewer', 'controller' => 'Logs', 'action' => 'view']);
        $routes->connect('/clear/*', ['plugin' => 'LogViewer', 'controller' => 'Logs', 'action' => 'clear']);
        $routes->connect('/download/*', ['plugin' => 'LogViewer', 'controller' => 'Logs', 'action' => 'download']);
    });
    
    // ... reste de vos routes ...
});
```

## 🎨 Utilisation

### Accéder au visualiseur

Une fois l'installation terminée, accédez à l'interface du visualiseur de logs via votre navigateur :

```
http://localhost/logs
```

ou

```
http://votre-domaine.com/logs
```

### Fonctionnalités disponibles

#### 📋 Liste des fichiers de logs
- Visualisez tous les fichiers `.log` présents dans votre répertoire `logs/`
- Taille et date de modification affichées
- Tri automatique par date (plus récents en premier)

#### 📊 Statistiques en temps réel
- Compteur par niveau de log (Error, Warning, Info, Debug, Critical, Notice)
- Graphiques colorés et visuels
- Vue d'ensemble rapide de l'état de votre application

#### 🔍 Recherche avancée
- Recherche textuelle en temps réel dans les logs
- Filtrage par niveau de log (Error, Warning, Info, Debug, etc.)
- Filtrage par plage de dates avec sélecteur de date
- Combinaison de plusieurs filtres

#### 💾 Export des données
- Export en CSV pour analyse dans Excel
- Export en JSON pour traitement programmatique
- Les filtres appliqués sont conservés dans l'export

#### 📥 Téléchargement
- Téléchargez n'importe quel fichier de log
- Format brut conservé

#### 🗑️ Nettoyage
- Vide un fichier de log (utile pour libérer de l'espace)
- Requiert une confirmation via POST

## 🔒 Sécurité

### ⚠️ IMPORTANT : Sécurisez l'accès en production

Le plugin donne accès direct à vos fichiers de logs. **Ne laissez JAMAIS cet accès ouvert en production sans protection** !

### Option 1 : Ajouter une authentification

Modifiez le contrôleur du plugin pour exiger une authentification :

```php
// Dans vendor/badrtairlbahrepro/cakephp-log-viewer/src/Controller/LogsController.php
// Ou dans un fichier de override

public function initialize(): void
{
    parent::initialize();
    
    // Vérifier l'authentification
    $this->Authentication->allowUnauthenticated([]); // Tout nécessite une authentification
    
    // Ou utiliser un middleware d'authentification
    if (!$this->getRequest()->getAttribute('authentication')->getIdentity()) {
        throw new \Cake\Http\Exception\ForbiddenException('Accès non autorisé');
    }
}
```

### Option 2 : Restreindre par IP

Ajoutez un middleware pour restreindre l'accès par adresse IP :

```php
// Dans config/routes.php ou dans un middleware custom

use Cake\Http\MiddlewareQueue;

$middlewareQueue->add(function ($request, $handler) {
    // Votre adresse IP autorisée
    $allowedIPs = ['127.0.0.1', '192.168.1.100'];
    
    if (str_starts_with($request->getPath(), '/logs')) {
        $clientIP = $request->clientIp();
        
        if (!in_array($clientIP, $allowedIPs)) {
            throw new \Cake\Http\Exception\ForbiddenException('Accès refusé');
        }
    }
    
    return $handler->handle($request);
});
```

### Option 3 : Désactiver en production

Dans `src/Application.php` :

```php
public function bootstrap(): void
{
    parent::bootstrap();
    
    // Ne charger le plugin qu'en développement
    if (Configure::read('debug')) {
        $this->addPlugin('LogViewer');
    }
}
```

## 🔄 Mise à jour

Pour mettre à jour le plugin vers la dernière version :

```bash
composer update badrtairlbahrepro/cakephp-log-viewer
```

## 🐛 Dépannage

### Le plugin ne s'affiche pas

1. **Vérifiez que le plugin est bien chargé** :
   ```bash
   composer show badrtairlbahrepro/cakephp-log-viewer
   ```

2. **Vérifiez les routes** :
   Assurez-vous que les routes sont bien configurées dans `config/routes.php`

3. **Videz le cache** :
   ```bash
   bin/cake cache clear_all
   ```

### Erreur 404 sur `/logs`

- Vérifiez que le plugin est chargé dans `Application.php`
- Vérifiez que les routes sont correctement configurées
- Vérifiez les permissions d'écriture sur le dossier `logs/`

### Les fichiers de logs n'apparaissent pas

- Assurez-vous que vos fichiers sont bien au format `.log`
- Vérifiez les permissions de lecture du dossier `logs/`
- Vérifiez que le chemin est correct dans la configuration de CakePHP

## 📖 Exemple d'utilisation

### Créer des logs dans votre application

```php
<?php

namespace App\Controller;

use Cake\Log\Log;
use Cake\Controller\Controller;

class MyController extends Controller
{
    public function index()
    {
        // Log de niveau debug
        Log::debug('Chargement de la page d\'accueil');
        
        // Log d'information
        Log::info('Utilisateur connecté : ' . $this->getIdentity()->email);
        
        // Log d'erreur
        Log::error('Échec de la connexion à la base de données');
        
        // Log d'avertissement
        Log::warning('Espace disque faible');
        
        // Log critique
        Log::critical('Système critique en panne');
    }
}
```

### Accéder aux logs via le visualiseur

1. Lancez votre application : `bin/cake server` ou via votre serveur web
2. Accédez à `http://localhost/logs`
3. Cliquez sur un fichier de log pour voir son contenu
4. Utilisez les filtres pour trouver rapidement les erreurs
5. Exportez les données si nécessaire

## 🎓 Ressources complémentaires

- [Documentation CakePHP Plugins](https://book.cakephp.org/5/en/plugins.html)
- [Documentation CakePHP Routing](https://book.cakephp.org/5/en/routing.html)
- [GitHub du Plugin](https://github.com/badrtairlbahrepro/cakephp-log-viewer)

## 📝 Changelog

### Version 1.0.0
- Version initiale
- Visualisation des fichiers de logs
- Recherche et filtrage
- Statistiques par niveau
- Export CSV/JSON
- Téléchargement des logs
- Interface moderne avec AdminLTE

## 💬 Support

Pour toute question ou problème :
- Ouvrez une issue sur [GitHub](https://github.com/badrtairlbahrepro/cakephp-log-viewer/issues)
- Consultez la documentation en ligne
- Contactez le mainteneur du plugin

---

**Bon développement ! 🚀**
