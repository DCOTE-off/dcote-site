import { defineConfig } from 'vite'
import laravel from 'laravel-vite-plugin'
import vue from '@vitejs/plugin-vue'

export default defineConfig({
  plugins: [
    laravel({
      input: [
        'resources/js/app.js',
        'resources/js/components/dropdown-menu.js',
        'resources/js/components/dropdown-select.js',
        'resources/js/components/grid-images-carousel.js',
        'resources/js/components/image-carousel-modal.js',
        'resources/js/components/rating.js',
        'resources/js/pages/about-project.js',
        'resources/js/pages/anime/episode.js',
        'resources/js/pages/anime/season.js',
        'resources/js/pages/dcote-main.js',
        'resources/js/pages/reading-settings.js',
        'resources/js/pages/reg-login.js',
        'resources/js/pages/selection-cards.js',
        'resources/css/pages/about-project.css',
        'resources/css/pages/about-school.css',
        'resources/css/pages/anime/episode.css',
        'resources/css/pages/anime/index.css',
        'resources/css/pages/anime/season.css',
        'resources/css/pages/dcote-main.css',
        'resources/css/pages/login-reg.css',
        'resources/css/pages/page-404.css',
        'resources/css/pages/ranobe/chapter.css',
        'resources/css/pages/ranobe/index.css',
        'resources/css/pages/ranobe/volume.css',
        'resources/css/pages/reading-settings.css',
        'resources/css/pages/rules.css',
        'resources/css/components/dropdown-menu.css',
        'resources/css/components/dropdown-select.css',
        'resources/css/components/dropdown.css',
        'resources/css/components/image-carousel-modal.css',
        'resources/css/components/list-filter.css',
        'resources/css/components/player-status.css',
        'resources/css/components/rating.css',
      ],
      refresh: true,
    }),
    vue(),
  ],

  server: {
    host: '0.0.0.0',
    port: 5173,
    strictPort: true,
    hmr: {
      host: 'localhost',
      clientPort: 5173,
    },
  },
})
