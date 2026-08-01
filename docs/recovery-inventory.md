# Recovery Inventory

This branch was reconstructed from OpenCode session diffs after the local Git
history was rewritten. The original pre-rewrite commit hashes are no longer
available locally.

## Restored Files

- `resources/views/pages/dcote-main.blade.php`: restored pill buttons and the
  Ayano Sakayanagi desktop/mobile hero image references.
- `public/css/main.css`: restored `btn-pill`, `link-pill`, outline pill styles,
  and VAG Rounded Next font paths under `public/fonts/VAG/`.
- `public/css/pages/dcote-main.css`: restored the current main-page layout and
  its Nunito usage.
- `public/js/components/grid-images-carousel.js`: restored the final saved
  carousel implementation from the same main-page OpenCode session.
- `resources/views/partials/comments.html`: restored the latest saved comments
  template and its corrected VAG font preload paths.
- `public/css/components/comments.css`: restored the matching comments styles
  and VAG Rounded Next font-family declarations.
- `public/css/header.css`: restored the current header styling, including the
  centered pale-lilac `navbar::after` separator.
- `public/css/footer.css`: restored the current footer styling and updated
  VAG Rounded Next font-family declarations. No saved footer `::before` or
  `::after` selector was found in the OpenCode traces; only the header pale
  separator was recoverable from saved diffs.
- `docker/8.4/Dockerfile`: restored the cleaned PHP 8.4 development image.
- `docker/8.4/php.ini`: restored the cleaned PHP configuration without the
  obsolete `pcov.directory` setting.
- `docker-compose.dev.yml`: restored the development compose configuration
  without the obsolete `XDEBUG_MODE` setting.
- `README.md`: restored the documentation for Laravel 10, Blade plus vanilla
  JavaScript, static CSS/JS assets, the cleaned dev image, and its install
  commands.

## Verification

- Blade templates compile successfully with `php artisan view:cache` inside
  the development Docker environment.
- The recovered main page contains `btn-pill`, `link-pill`,
  `btn-pill-outline`, and the `ayano-sakayanagi` image references.
- The recovered global CSS contains the pill selectors and VAG `.woff2` paths.
- The recovered carousel script passes `node --check`.
- The recovered header contains the pale-lilac `navbar::after` pseudo-element.
- Docker image build was not repeated during this recovery pass.

## Known Binary Gap

The OpenCode database preserved the names of the Nunito `.woff2` files and the
Ayano hero images, but their binary contents were not found in the remaining
Git objects, snapshots, local Docker image, or deployed site. The restored
Blade/CSS references therefore need those assets to be supplied separately.
