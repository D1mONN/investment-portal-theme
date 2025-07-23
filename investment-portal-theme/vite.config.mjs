// FILE: vite.config.js

/**
 * Vite Configuration for Slavuta Invest Theme
 * 
 * This configuration handles the build process for all theme assets,
 * including JavaScript, SCSS, and image optimization.
 */
import path from 'path';
import { defineConfig } from 'vite';
import { viteStaticCopy } from 'vite-plugin-static-copy';
import imagemin from 'vite-plugin-imagemin';

// Theme directory configuration
const themePath = './wp-content/themes/slavuta-invest';

export default defineConfig(({ command, mode }) => {
  return {
    base: command === 'build' ? './' : '/',
    
    build: {
      outDir: path.resolve(__dirname, 'dist'),
      assetsDir: '',
      manifest: true,
      rollupOptions: {
        input: {
          main: path.resolve(__dirname, 'assets/js/main.js'),
        },
        output: {
          entryFileNames: 'js/[name].[hash].js',
          chunkFileNames: 'js/[name].[hash].js',
          assetFileNames: (assetInfo) => {
            const extType = assetInfo.name.split('.').at(1);
            if (/css/i.test(extType)) {
              return 'css/[name].[hash][extname]';
            }
            if (/png|jpe?g|svg|gif|tiff|bmp|ico/i.test(extType)) {
              return 'images/[name].[hash][extname]';
            }
            if (/woff2?|ttf|otf|eot/i.test(extType)) {
              return 'fonts/[name].[hash][extname]';
            }
            return '[name].[hash][extname]';
          },
        },
      },
      minify: mode === 'production',
      sourcemap: mode === 'development',
    },
    
    plugins: [
      // Copy static assets
      viteStaticCopy({
        targets: [
          {
            src: 'assets/images/static/*',
            dest: 'images/static',
          },
          {
            src: 'assets/fonts/*',
            dest: 'fonts',
          },
        ],
      }),
      
      // Image optimization for production
      mode === 'production' && imagemin({
        gifsicle: {
          optimizationLevel: 7,
          interlaced: false,
        },
        optipng: {
          optimizationLevel: 7,
        },
        mozjpeg: {
          quality: 85,
        },
        pngquant: {
          quality: [0.8, 0.9],
          speed: 4,
        },
        svgo: {
          plugins: [
            {
              name: 'removeViewBox',
              active: false,
            },
            {
              name: 'removeEmptyAttrs',
              active: false,
            },
          ],
        },
      }),
    ].filter(Boolean),

    resolve: {
      alias: {
        '@scss': path.resolve(__dirname, 'assets/scss'),
        '@': path.resolve(__dirname, 'assets'),
      },
    },
    
    server: {
      host: '0.0.0.0',
      // Вмикає CORS, щоб браузер не блокував запити
      cors: true,
      // Явно вказуємо порт
      port: 5173,
      strictPort: true,
      hmr: {
        host: 'localhost',
      },
      // Proxy WordPress site for development
      proxy: {
        '/': {
          target: 'http://localhost:8000', // Change to your local WordPress URL
          changeOrigin: true,
          bypass: (req) => {
            // Bypass proxy for Vite assets
            if (req.url.startsWith('/@') || req.url.startsWith('/node_modules')) {
              return req.url;
            }
          },
        },
      },
    },

    css: {
      preprocessorOptions: {
        scss: {
          // Цей рядок є ключовим. Він вказує Sass,
          // що папка 'assets/scss' є кореневою для пошуку файлів.
          includePaths: [path.resolve(__dirname, 'assets/scss')],
        }
      }
    },
    
    optimizeDeps: {
      include: ['swiper', 'swiper/modules'],
    },
  };
});