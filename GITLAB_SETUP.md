# 📦 Guide : Ajouter le plugin sur GitLab interne

## 🎯 Principe
Avoir le plugin disponible sur GitHub (public) ET sur GitLab interne (privé)

---

## 📋 ÉTAPE 1 : Créer le projet sur GitLab

1. Allez sur votre GitLab interne
2. Créez un nouveau projet : `cakephp-log-viewer`
3. Copiez l'URL (ex: `https://gitlab.company.com/devteam/cakephp-log-viewer.git`)

---

## 🔧 ÉTAPE 2 : Ajouter GitLab comme remote

```bash
cd /Users/badrtairlbahre/Desktop/Projects/cakephp-log-viewer-standalone

# Remplacer par votre URL GitLab
git remote add gitlab https://gitlab.company.com/devteam/cakephp-log-viewer.git

# Vérifier
git remote -v
```

---

## 📤 ÉTAPE 3 : Pousser les branches sur GitLab

```bash
git push gitlab adminlte
git push gitlab bootstrap
```

---

## 💻 ÉTAPE 4 : Configurer le token GitLab (REQUIS)

### Générer un token GitLab

1. Allez sur GitLab → **Settings** → **Access Tokens**
2. Créez un token avec les scopes suivants :
   - `read_api` ou `read_repository`
3. **Copiez le token généré** (vous ne le reverrez pas !)

### Configurer Composer

**Option 1 : Via `auth.json` (recommandé)**

Créez un fichier `auth.json` à la racine de chaque projet qui utilisera le plugin :

```json
{
    "gitlab-token": {
        "gitlab.company.com": "VOTRE_TOKEN_GITLAB_ICI"
    }
}
```

⚠️ **Important :** Ajoutez `auth.json` dans `.gitignore` pour ne pas commiter le token !

**Option 2 : Dans `composer.json`**

Ajoutez la configuration dans le fichier `composer.json` de chaque projet :

```json
{
    "config": {
        "gitlab-token": {
            "gitlab.company.com": "VOTRE_TOKEN_GITLAB_ICI"
        }
    },
    "repositories": [
        {
            "type": "vcs",
            "url": "https://gitlab.company.com/devteam/cakephp-log-viewer.git"
        }
    ],
    "require": {
        "badrtairlbahrepro/cakephp-log-viewer": "dev-bootstrap"
    },
    "minimum-stability": "dev"
}
```

---

## 🚀 ÉTAPE 5 : Installer le plugin dans vos projets

```bash
cd /chemin/vers/votre/projet
composer install
```

---

## 🔄 ÉTAPE 6 : Synchronisation future

À chaque modification du plugin, pousser sur les deux remotes :

```bash
git add .
git commit -m "Description des changements"
git push origin bootstrap
git push gitlab bootstrap
```

Ou en une seule commande :
```bash
git push origin bootstrap && git push gitlab bootstrap
```

---

## 📝 Note sur `auth.json`

Le fichier `auth.json` contient des informations sensibles (token GitLab). Il faut :

✅ **Ajouter dans `.gitignore` :**
```
auth.json
```

✅ **Commiter l'exemple :**
```bash
cp auth.json auth.json.example
git add auth.json.example
```

Les développeurs copieront `auth.json.example` en `auth.json` et ajouteront leur propre token.

