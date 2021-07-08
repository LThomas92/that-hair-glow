### Initial Setup
- Make a WordPress installation
- Create a theme at http://underscores.me/ and add it
- cd into the theme cd wp-content/themes/theme-name
- git clone git@bitbucket.org:team_cmyk/node-setup.git .
- In package.json change "name" to theme name
- In webpack.config.js change "proxy" to localhost link to site
- run 'npm install'

### Verify WP Setup

#### Use style.sass link additional stylesheets ie \_header.sass prefix:
```
@import "header";
```

#### Add script.js to src directory
#### Add the following to import the new script file to index.js:
```
require('../js/script')
```
(You can use this to include additional script files)

#### In functions.php replace
```
wp_enqueue_style( 'theme-name-style', get_stylesheet_uri() );
```
### WITH
```
$manifest = json_decode(file_get_contents('dist/assets.json', true));
$main = $manifest->main;

wp_enqueue_style( 'theme-name-style', get_template_directory_uri() . $main->css, false, null );

wp_enqueue_script('theme-name-js', get_template_directory_uri() . $main->js, ['jquery'], null, true);
```

#### Watch for file changes for dev
in the theme directory, type npm run watch

#### When you're ready to push to git for production:
in the theme directory, type npm run build and then push to the git as you would normally.
#### * For now you'll need to remove the extra css files created in dist so they don't add up. There needs to be a system in place to remove the old ones on production.

## Issues

#### If you have issues with the modules you can always remove and reinstall  them by running:
rm -rf node_modules && npm install

#### ADD IMAGES IN STYLES WITH ```url('../images/image.jpg')```
