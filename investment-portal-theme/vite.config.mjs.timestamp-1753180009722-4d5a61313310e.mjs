// vite.config.mjs
import { defineConfig } from "file:///mnt/c/Users/d1ma/Documents/GitHub/investment-portal-theme/investment-portal-theme/node_modules/vite/dist/node/index.js";
import path from "path";
import { viteStaticCopy } from "file:///mnt/c/Users/d1ma/Documents/GitHub/investment-portal-theme/investment-portal-theme/node_modules/vite-plugin-static-copy/dist/index.js";
import imagemin from "file:///mnt/c/Users/d1ma/Documents/GitHub/investment-portal-theme/investment-portal-theme/node_modules/vite-plugin-imagemin/dist/index.mjs";
var __vite_injected_original_dirname = "/mnt/c/Users/d1ma/Documents/GitHub/investment-portal-theme/investment-portal-theme";
var vite_config_default = defineConfig(({ command, mode }) => {
  return {
    base: command === "build" ? "./" : "/",
    build: {
      outDir: path.resolve(__vite_injected_original_dirname, "dist"),
      assetsDir: "",
      manifest: true,
      rollupOptions: {
        input: {
          main: path.resolve(__vite_injected_original_dirname, "assets/js/main.js")
        },
        output: {
          entryFileNames: "js/[name].[hash].js",
          chunkFileNames: "js/[name].[hash].js",
          assetFileNames: (assetInfo) => {
            const extType = assetInfo.name.split(".").at(1);
            if (/css/i.test(extType)) {
              return "css/[name].[hash][extname]";
            }
            if (/png|jpe?g|svg|gif|tiff|bmp|ico/i.test(extType)) {
              return "images/[name].[hash][extname]";
            }
            if (/woff2?|ttf|otf|eot/i.test(extType)) {
              return "fonts/[name].[hash][extname]";
            }
            return "[name].[hash][extname]";
          }
        }
      },
      minify: mode === "production",
      sourcemap: mode === "development"
    },
    plugins: [
      // Copy static assets
      viteStaticCopy({
        targets: [
          {
            src: "assets/images/static/*",
            dest: "images/static"
          },
          {
            src: "assets/fonts/*",
            dest: "fonts"
          }
        ]
      }),
      // Image optimization for production
      mode === "production" && imagemin({
        gifsicle: {
          optimizationLevel: 7,
          interlaced: false
        },
        optipng: {
          optimizationLevel: 7
        },
        mozjpeg: {
          quality: 85
        },
        pngquant: {
          quality: [0.8, 0.9],
          speed: 4
        },
        svgo: {
          plugins: [
            {
              name: "removeViewBox",
              active: false
            },
            {
              name: "removeEmptyAttrs",
              active: false
            }
          ]
        }
      })
    ].filter(Boolean),
    resolve: {
      alias: {
        "@": path.resolve(__vite_injected_original_dirname, "assets"),
        "@js": path.resolve(__vite_injected_original_dirname, "assets/js"),
        "@scss": path.resolve(__vite_injected_original_dirname, "assets/scss"),
        "@images": path.resolve(__vite_injected_original_dirname, "assets/images")
      }
    },
    server: {
      host: true,
      // Вмикає CORS, щоб браузер не блокував запити
      cors: true,
      // Явно вказуємо порт
      port: 5173,
      strictPort: true,
      hmr: {
        host: "localhost"
      },
      // Proxy WordPress site for development
      proxy: {
        "/": {
          target: "http://localhost:8000",
          // Change to your local WordPress URL
          changeOrigin: true,
          bypass: (req) => {
            if (req.url.startsWith("/@") || req.url.startsWith("/node_modules")) {
              return req.url;
            }
          }
        }
      }
    },
    css: {
      preprocessorOptions: {
        scss: {
          additionalData: `
            @import "@scss/abstracts/variables";
            @import "@scss/abstracts/functions";
            @import "@scss/abstracts/mixins";
          `
        }
      }
    },
    optimizeDeps: {
      include: ["swiper", "swiper/modules"]
    }
  };
});
export {
  vite_config_default as default
};
//# sourceMappingURL=data:application/json;base64,ewogICJ2ZXJzaW9uIjogMywKICAic291cmNlcyI6IFsidml0ZS5jb25maWcubWpzIl0sCiAgInNvdXJjZXNDb250ZW50IjogWyJjb25zdCBfX3ZpdGVfaW5qZWN0ZWRfb3JpZ2luYWxfZGlybmFtZSA9IFwiL21udC9jL1VzZXJzL2QxbWEvRG9jdW1lbnRzL0dpdEh1Yi9pbnZlc3RtZW50LXBvcnRhbC10aGVtZS9pbnZlc3RtZW50LXBvcnRhbC10aGVtZVwiO2NvbnN0IF9fdml0ZV9pbmplY3RlZF9vcmlnaW5hbF9maWxlbmFtZSA9IFwiL21udC9jL1VzZXJzL2QxbWEvRG9jdW1lbnRzL0dpdEh1Yi9pbnZlc3RtZW50LXBvcnRhbC10aGVtZS9pbnZlc3RtZW50LXBvcnRhbC10aGVtZS92aXRlLmNvbmZpZy5tanNcIjtjb25zdCBfX3ZpdGVfaW5qZWN0ZWRfb3JpZ2luYWxfaW1wb3J0X21ldGFfdXJsID0gXCJmaWxlOi8vL21udC9jL1VzZXJzL2QxbWEvRG9jdW1lbnRzL0dpdEh1Yi9pbnZlc3RtZW50LXBvcnRhbC10aGVtZS9pbnZlc3RtZW50LXBvcnRhbC10aGVtZS92aXRlLmNvbmZpZy5tanNcIjsvLyBGSUxFOiB2aXRlLmNvbmZpZy5qc1xuXG4vKipcbiAqIFZpdGUgQ29uZmlndXJhdGlvbiBmb3IgU2xhdnV0YSBJbnZlc3QgVGhlbWVcbiAqIFxuICogVGhpcyBjb25maWd1cmF0aW9uIGhhbmRsZXMgdGhlIGJ1aWxkIHByb2Nlc3MgZm9yIGFsbCB0aGVtZSBhc3NldHMsXG4gKiBpbmNsdWRpbmcgSmF2YVNjcmlwdCwgU0NTUywgYW5kIGltYWdlIG9wdGltaXphdGlvbi5cbiAqL1xuXG5pbXBvcnQgeyBkZWZpbmVDb25maWcgfSBmcm9tICd2aXRlJztcbmltcG9ydCBwYXRoIGZyb20gJ3BhdGgnO1xuaW1wb3J0IHsgdml0ZVN0YXRpY0NvcHkgfSBmcm9tICd2aXRlLXBsdWdpbi1zdGF0aWMtY29weSc7XG5pbXBvcnQgaW1hZ2VtaW4gZnJvbSAndml0ZS1wbHVnaW4taW1hZ2VtaW4nO1xuXG4vLyBUaGVtZSBkaXJlY3RvcnkgY29uZmlndXJhdGlvblxuY29uc3QgdGhlbWVQYXRoID0gJy4vd3AtY29udGVudC90aGVtZXMvc2xhdnV0YS1pbnZlc3QnO1xuXG5leHBvcnQgZGVmYXVsdCBkZWZpbmVDb25maWcoKHsgY29tbWFuZCwgbW9kZSB9KSA9PiB7XG4gIHJldHVybiB7XG4gICAgYmFzZTogY29tbWFuZCA9PT0gJ2J1aWxkJyA/ICcuLycgOiAnLycsXG4gICAgXG4gICAgYnVpbGQ6IHtcbiAgICAgIG91dERpcjogcGF0aC5yZXNvbHZlKF9fZGlybmFtZSwgJ2Rpc3QnKSxcbiAgICAgIGFzc2V0c0RpcjogJycsXG4gICAgICBtYW5pZmVzdDogdHJ1ZSxcbiAgICAgIHJvbGx1cE9wdGlvbnM6IHtcbiAgICAgICAgaW5wdXQ6IHtcbiAgICAgICAgICBtYWluOiBwYXRoLnJlc29sdmUoX19kaXJuYW1lLCAnYXNzZXRzL2pzL21haW4uanMnKSxcbiAgICAgICAgfSxcbiAgICAgICAgb3V0cHV0OiB7XG4gICAgICAgICAgZW50cnlGaWxlTmFtZXM6ICdqcy9bbmFtZV0uW2hhc2hdLmpzJyxcbiAgICAgICAgICBjaHVua0ZpbGVOYW1lczogJ2pzL1tuYW1lXS5baGFzaF0uanMnLFxuICAgICAgICAgIGFzc2V0RmlsZU5hbWVzOiAoYXNzZXRJbmZvKSA9PiB7XG4gICAgICAgICAgICBjb25zdCBleHRUeXBlID0gYXNzZXRJbmZvLm5hbWUuc3BsaXQoJy4nKS5hdCgxKTtcbiAgICAgICAgICAgIGlmICgvY3NzL2kudGVzdChleHRUeXBlKSkge1xuICAgICAgICAgICAgICByZXR1cm4gJ2Nzcy9bbmFtZV0uW2hhc2hdW2V4dG5hbWVdJztcbiAgICAgICAgICAgIH1cbiAgICAgICAgICAgIGlmICgvcG5nfGpwZT9nfHN2Z3xnaWZ8dGlmZnxibXB8aWNvL2kudGVzdChleHRUeXBlKSkge1xuICAgICAgICAgICAgICByZXR1cm4gJ2ltYWdlcy9bbmFtZV0uW2hhc2hdW2V4dG5hbWVdJztcbiAgICAgICAgICAgIH1cbiAgICAgICAgICAgIGlmICgvd29mZjI/fHR0ZnxvdGZ8ZW90L2kudGVzdChleHRUeXBlKSkge1xuICAgICAgICAgICAgICByZXR1cm4gJ2ZvbnRzL1tuYW1lXS5baGFzaF1bZXh0bmFtZV0nO1xuICAgICAgICAgICAgfVxuICAgICAgICAgICAgcmV0dXJuICdbbmFtZV0uW2hhc2hdW2V4dG5hbWVdJztcbiAgICAgICAgICB9LFxuICAgICAgICB9LFxuICAgICAgfSxcbiAgICAgIG1pbmlmeTogbW9kZSA9PT0gJ3Byb2R1Y3Rpb24nLFxuICAgICAgc291cmNlbWFwOiBtb2RlID09PSAnZGV2ZWxvcG1lbnQnLFxuICAgIH0sXG4gICAgXG4gICAgcGx1Z2luczogW1xuICAgICAgLy8gQ29weSBzdGF0aWMgYXNzZXRzXG4gICAgICB2aXRlU3RhdGljQ29weSh7XG4gICAgICAgIHRhcmdldHM6IFtcbiAgICAgICAgICB7XG4gICAgICAgICAgICBzcmM6ICdhc3NldHMvaW1hZ2VzL3N0YXRpYy8qJyxcbiAgICAgICAgICAgIGRlc3Q6ICdpbWFnZXMvc3RhdGljJyxcbiAgICAgICAgICB9LFxuICAgICAgICAgIHtcbiAgICAgICAgICAgIHNyYzogJ2Fzc2V0cy9mb250cy8qJyxcbiAgICAgICAgICAgIGRlc3Q6ICdmb250cycsXG4gICAgICAgICAgfSxcbiAgICAgICAgXSxcbiAgICAgIH0pLFxuICAgICAgXG4gICAgICAvLyBJbWFnZSBvcHRpbWl6YXRpb24gZm9yIHByb2R1Y3Rpb25cbiAgICAgIG1vZGUgPT09ICdwcm9kdWN0aW9uJyAmJiBpbWFnZW1pbih7XG4gICAgICAgIGdpZnNpY2xlOiB7XG4gICAgICAgICAgb3B0aW1pemF0aW9uTGV2ZWw6IDcsXG4gICAgICAgICAgaW50ZXJsYWNlZDogZmFsc2UsXG4gICAgICAgIH0sXG4gICAgICAgIG9wdGlwbmc6IHtcbiAgICAgICAgICBvcHRpbWl6YXRpb25MZXZlbDogNyxcbiAgICAgICAgfSxcbiAgICAgICAgbW96anBlZzoge1xuICAgICAgICAgIHF1YWxpdHk6IDg1LFxuICAgICAgICB9LFxuICAgICAgICBwbmdxdWFudDoge1xuICAgICAgICAgIHF1YWxpdHk6IFswLjgsIDAuOV0sXG4gICAgICAgICAgc3BlZWQ6IDQsXG4gICAgICAgIH0sXG4gICAgICAgIHN2Z286IHtcbiAgICAgICAgICBwbHVnaW5zOiBbXG4gICAgICAgICAgICB7XG4gICAgICAgICAgICAgIG5hbWU6ICdyZW1vdmVWaWV3Qm94JyxcbiAgICAgICAgICAgICAgYWN0aXZlOiBmYWxzZSxcbiAgICAgICAgICAgIH0sXG4gICAgICAgICAgICB7XG4gICAgICAgICAgICAgIG5hbWU6ICdyZW1vdmVFbXB0eUF0dHJzJyxcbiAgICAgICAgICAgICAgYWN0aXZlOiBmYWxzZSxcbiAgICAgICAgICAgIH0sXG4gICAgICAgICAgXSxcbiAgICAgICAgfSxcbiAgICAgIH0pLFxuICAgIF0uZmlsdGVyKEJvb2xlYW4pLFxuICAgIFxuICAgIHJlc29sdmU6IHtcbiAgICAgIGFsaWFzOiB7XG4gICAgICAgICdAJzogcGF0aC5yZXNvbHZlKF9fZGlybmFtZSwgJ2Fzc2V0cycpLFxuICAgICAgICAnQGpzJzogcGF0aC5yZXNvbHZlKF9fZGlybmFtZSwgJ2Fzc2V0cy9qcycpLFxuICAgICAgICAnQHNjc3MnOiBwYXRoLnJlc29sdmUoX19kaXJuYW1lLCAnYXNzZXRzL3Njc3MnKSxcbiAgICAgICAgJ0BpbWFnZXMnOiBwYXRoLnJlc29sdmUoX19kaXJuYW1lLCAnYXNzZXRzL2ltYWdlcycpLFxuICAgICAgfSxcbiAgICB9LFxuICAgIFxuICAgIHNlcnZlcjoge1xuICAgICAgaG9zdDogdHJ1ZSxcbiAgICAgIC8vIFx1MDQxMlx1MDQzQ1x1MDQzOFx1MDQzQVx1MDQzMFx1MDQ1NCBDT1JTLCBcdTA0NDlcdTA0M0VcdTA0MzEgXHUwNDMxXHUwNDQwXHUwNDMwXHUwNDQzXHUwNDM3XHUwNDM1XHUwNDQwIFx1MDQzRFx1MDQzNSBcdTA0MzFcdTA0M0JcdTA0M0VcdTA0M0FcdTA0NDNcdTA0MzJcdTA0MzBcdTA0MzIgXHUwNDM3XHUwNDMwXHUwNDNGXHUwNDM4XHUwNDQyXHUwNDM4XG4gICAgICBjb3JzOiB0cnVlLFxuICAgICAgLy8gXHUwNDJGXHUwNDMyXHUwNDNEXHUwNDNFIFx1MDQzMlx1MDQzQVx1MDQzMFx1MDQzN1x1MDQ0M1x1MDQ1NFx1MDQzQ1x1MDQzRSBcdTA0M0ZcdTA0M0VcdTA0NDBcdTA0NDJcbiAgICAgIHBvcnQ6IDUxNzMsXG4gICAgICBzdHJpY3RQb3J0OiB0cnVlLFxuICAgICAgaG1yOiB7XG4gICAgICAgIGhvc3Q6ICdsb2NhbGhvc3QnLFxuICAgICAgfSxcbiAgICAgIC8vIFByb3h5IFdvcmRQcmVzcyBzaXRlIGZvciBkZXZlbG9wbWVudFxuICAgICAgcHJveHk6IHtcbiAgICAgICAgJy8nOiB7XG4gICAgICAgICAgdGFyZ2V0OiAnaHR0cDovL2xvY2FsaG9zdDo4MDAwJywgLy8gQ2hhbmdlIHRvIHlvdXIgbG9jYWwgV29yZFByZXNzIFVSTFxuICAgICAgICAgIGNoYW5nZU9yaWdpbjogdHJ1ZSxcbiAgICAgICAgICBieXBhc3M6IChyZXEpID0+IHtcbiAgICAgICAgICAgIC8vIEJ5cGFzcyBwcm94eSBmb3IgVml0ZSBhc3NldHNcbiAgICAgICAgICAgIGlmIChyZXEudXJsLnN0YXJ0c1dpdGgoJy9AJykgfHwgcmVxLnVybC5zdGFydHNXaXRoKCcvbm9kZV9tb2R1bGVzJykpIHtcbiAgICAgICAgICAgICAgcmV0dXJuIHJlcS51cmw7XG4gICAgICAgICAgICB9XG4gICAgICAgICAgfSxcbiAgICAgICAgfSxcbiAgICAgIH0sXG4gICAgfSxcbiAgICBcbiAgICBjc3M6IHtcbiAgICAgIHByZXByb2Nlc3Nvck9wdGlvbnM6IHtcbiAgICAgICAgc2Nzczoge1xuICAgICAgICAgIGFkZGl0aW9uYWxEYXRhOiBgXG4gICAgICAgICAgICBAaW1wb3J0IFwiQHNjc3MvYWJzdHJhY3RzL3ZhcmlhYmxlc1wiO1xuICAgICAgICAgICAgQGltcG9ydCBcIkBzY3NzL2Fic3RyYWN0cy9mdW5jdGlvbnNcIjtcbiAgICAgICAgICAgIEBpbXBvcnQgXCJAc2Nzcy9hYnN0cmFjdHMvbWl4aW5zXCI7XG4gICAgICAgICAgYCxcbiAgICAgICAgfSxcbiAgICAgIH0sXG4gICAgfSxcbiAgICBcbiAgICBvcHRpbWl6ZURlcHM6IHtcbiAgICAgIGluY2x1ZGU6IFsnc3dpcGVyJywgJ3N3aXBlci9tb2R1bGVzJ10sXG4gICAgfSxcbiAgfTtcbn0pOyJdLAogICJtYXBwaW5ncyI6ICI7QUFTQSxTQUFTLG9CQUFvQjtBQUM3QixPQUFPLFVBQVU7QUFDakIsU0FBUyxzQkFBc0I7QUFDL0IsT0FBTyxjQUFjO0FBWnJCLElBQU0sbUNBQW1DO0FBaUJ6QyxJQUFPLHNCQUFRLGFBQWEsQ0FBQyxFQUFFLFNBQVMsS0FBSyxNQUFNO0FBQ2pELFNBQU87QUFBQSxJQUNMLE1BQU0sWUFBWSxVQUFVLE9BQU87QUFBQSxJQUVuQyxPQUFPO0FBQUEsTUFDTCxRQUFRLEtBQUssUUFBUSxrQ0FBVyxNQUFNO0FBQUEsTUFDdEMsV0FBVztBQUFBLE1BQ1gsVUFBVTtBQUFBLE1BQ1YsZUFBZTtBQUFBLFFBQ2IsT0FBTztBQUFBLFVBQ0wsTUFBTSxLQUFLLFFBQVEsa0NBQVcsbUJBQW1CO0FBQUEsUUFDbkQ7QUFBQSxRQUNBLFFBQVE7QUFBQSxVQUNOLGdCQUFnQjtBQUFBLFVBQ2hCLGdCQUFnQjtBQUFBLFVBQ2hCLGdCQUFnQixDQUFDLGNBQWM7QUFDN0Isa0JBQU0sVUFBVSxVQUFVLEtBQUssTUFBTSxHQUFHLEVBQUUsR0FBRyxDQUFDO0FBQzlDLGdCQUFJLE9BQU8sS0FBSyxPQUFPLEdBQUc7QUFDeEIscUJBQU87QUFBQSxZQUNUO0FBQ0EsZ0JBQUksa0NBQWtDLEtBQUssT0FBTyxHQUFHO0FBQ25ELHFCQUFPO0FBQUEsWUFDVDtBQUNBLGdCQUFJLHNCQUFzQixLQUFLLE9BQU8sR0FBRztBQUN2QyxxQkFBTztBQUFBLFlBQ1Q7QUFDQSxtQkFBTztBQUFBLFVBQ1Q7QUFBQSxRQUNGO0FBQUEsTUFDRjtBQUFBLE1BQ0EsUUFBUSxTQUFTO0FBQUEsTUFDakIsV0FBVyxTQUFTO0FBQUEsSUFDdEI7QUFBQSxJQUVBLFNBQVM7QUFBQTtBQUFBLE1BRVAsZUFBZTtBQUFBLFFBQ2IsU0FBUztBQUFBLFVBQ1A7QUFBQSxZQUNFLEtBQUs7QUFBQSxZQUNMLE1BQU07QUFBQSxVQUNSO0FBQUEsVUFDQTtBQUFBLFlBQ0UsS0FBSztBQUFBLFlBQ0wsTUFBTTtBQUFBLFVBQ1I7QUFBQSxRQUNGO0FBQUEsTUFDRixDQUFDO0FBQUE7QUFBQSxNQUdELFNBQVMsZ0JBQWdCLFNBQVM7QUFBQSxRQUNoQyxVQUFVO0FBQUEsVUFDUixtQkFBbUI7QUFBQSxVQUNuQixZQUFZO0FBQUEsUUFDZDtBQUFBLFFBQ0EsU0FBUztBQUFBLFVBQ1AsbUJBQW1CO0FBQUEsUUFDckI7QUFBQSxRQUNBLFNBQVM7QUFBQSxVQUNQLFNBQVM7QUFBQSxRQUNYO0FBQUEsUUFDQSxVQUFVO0FBQUEsVUFDUixTQUFTLENBQUMsS0FBSyxHQUFHO0FBQUEsVUFDbEIsT0FBTztBQUFBLFFBQ1Q7QUFBQSxRQUNBLE1BQU07QUFBQSxVQUNKLFNBQVM7QUFBQSxZQUNQO0FBQUEsY0FDRSxNQUFNO0FBQUEsY0FDTixRQUFRO0FBQUEsWUFDVjtBQUFBLFlBQ0E7QUFBQSxjQUNFLE1BQU07QUFBQSxjQUNOLFFBQVE7QUFBQSxZQUNWO0FBQUEsVUFDRjtBQUFBLFFBQ0Y7QUFBQSxNQUNGLENBQUM7QUFBQSxJQUNILEVBQUUsT0FBTyxPQUFPO0FBQUEsSUFFaEIsU0FBUztBQUFBLE1BQ1AsT0FBTztBQUFBLFFBQ0wsS0FBSyxLQUFLLFFBQVEsa0NBQVcsUUFBUTtBQUFBLFFBQ3JDLE9BQU8sS0FBSyxRQUFRLGtDQUFXLFdBQVc7QUFBQSxRQUMxQyxTQUFTLEtBQUssUUFBUSxrQ0FBVyxhQUFhO0FBQUEsUUFDOUMsV0FBVyxLQUFLLFFBQVEsa0NBQVcsZUFBZTtBQUFBLE1BQ3BEO0FBQUEsSUFDRjtBQUFBLElBRUEsUUFBUTtBQUFBLE1BQ04sTUFBTTtBQUFBO0FBQUEsTUFFTixNQUFNO0FBQUE7QUFBQSxNQUVOLE1BQU07QUFBQSxNQUNOLFlBQVk7QUFBQSxNQUNaLEtBQUs7QUFBQSxRQUNILE1BQU07QUFBQSxNQUNSO0FBQUE7QUFBQSxNQUVBLE9BQU87QUFBQSxRQUNMLEtBQUs7QUFBQSxVQUNILFFBQVE7QUFBQTtBQUFBLFVBQ1IsY0FBYztBQUFBLFVBQ2QsUUFBUSxDQUFDLFFBQVE7QUFFZixnQkFBSSxJQUFJLElBQUksV0FBVyxJQUFJLEtBQUssSUFBSSxJQUFJLFdBQVcsZUFBZSxHQUFHO0FBQ25FLHFCQUFPLElBQUk7QUFBQSxZQUNiO0FBQUEsVUFDRjtBQUFBLFFBQ0Y7QUFBQSxNQUNGO0FBQUEsSUFDRjtBQUFBLElBRUEsS0FBSztBQUFBLE1BQ0gscUJBQXFCO0FBQUEsUUFDbkIsTUFBTTtBQUFBLFVBQ0osZ0JBQWdCO0FBQUE7QUFBQTtBQUFBO0FBQUE7QUFBQSxRQUtsQjtBQUFBLE1BQ0Y7QUFBQSxJQUNGO0FBQUEsSUFFQSxjQUFjO0FBQUEsTUFDWixTQUFTLENBQUMsVUFBVSxnQkFBZ0I7QUFBQSxJQUN0QztBQUFBLEVBQ0Y7QUFDRixDQUFDOyIsCiAgIm5hbWVzIjogW10KfQo=
