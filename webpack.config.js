const path = require('path');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const CssMinimizerPlugin = require('css-minimizer-webpack-plugin');
const TerserPlugin = require('terser-webpack-plugin');
const { CleanWebpackPlugin } = require('clean-webpack-plugin');
const CopyWebpackPlugin = require('copy-webpack-plugin');

module.exports = (env, argv) => {
  const isProduction = argv.mode === 'production';
  const isDevelopment = !isProduction;

  return {
    // Точки входу
    entry: {
      main: './src/themes/slavutska-investment/assets/js/main.js',
      admin: './src/themes/slavutska-investment/assets/js/admin.js',
      'contact-form': './src/themes/slavutska-investment/assets/js/contact-form.js',
      landing: './src/themes/slavutska-investment/assets/js/modules/landing.js'
    },

    // Вихідна директорія
    output: {
      path: path.resolve(__dirname, 'src/themes/slavutska-investment/assets/dist'),
      filename: isProduction ? 'js/[name].[contenthash:8].min.js' : 'js/[name].js',
      chunkFilename: isProduction ? 'js/chunks/[name].[contenthash:8].chunk.js' : 'js/chunks/[name].chunk.js',
      clean: true,
      publicPath: '/wp-content/themes/slavutska-investment/assets/dist/'
    },

    // Режим розробки
    mode: isProduction ? 'production' : 'development',
    devtool: isDevelopment ? 'eval-source-map' : 'source-map',

    // Оптимізація
    optimization: {
      minimize: isProduction,
      minimizer: [
        new TerserPlugin({
          terserOptions: {
            compress: {
              drop_console: isProduction,
              drop_debugger: isProduction,
              pure_funcs: isProduction ? ['console.log', 'console.info'] : []
            },
            format: {
              comments: false
            },
            mangle: {
              safari10: true
            }
          },
          extractComments: false
        }),
        new CssMinimizerPlugin({
          minimizerOptions: {
            preset: [
              'default',
              {
                discardComments: { removeAll: true }
              }
            ]
          }
        })
      ],
      splitChunks: {
        chunks: 'all',
        cacheGroups: {
          vendor: {
            test: /[\\/]node_modules[\\/]/,
            name: 'vendors',
            chunks: 'all',
            enforce: true
          },
          common: {
            name: 'common',
            minChunks: 2,
            chunks: 'all',
            enforce: true
          }
        }
      },
      runtimeChunk: {
        name: 'runtime'
      }
    },

    // Налаштування модулів
    module: {
      rules: [
        // JavaScript
        {
          test: /\.js$/,
          exclude: /node_modules/,
          use: {
            loader: 'babel-loader',
            options: {
              presets: [
                [
                  '@babel/preset-env',
                  {
                    useBuiltIns: 'usage',
                    corejs: 3,
                    targets: {
                      browsers: ['> 1%', 'last 2 versions', 'not dead']
                    }
                  }
                ]
              ],
              cacheDirectory: true
            }
          }
        },

        // CSS та SCSS
        {
          test: /\.(css|scss|sass)$/,
          use: [
            isProduction ? MiniCssExtractPlugin.loader : 'style-loader',
            {
              loader: 'css-loader',
              options: {
                sourceMap: isDevelopment,
                importLoaders: 2
              }
            },
            {
              loader: 'postcss-loader',
              options: {
                sourceMap: isDevelopment,
                postcssOptions: {
                  plugins: [
                    ['autoprefixer'],
                    ...(isProduction ? [['cssnano', { preset: 'default' }]] : [])
                  ]
                }
              }
            },
            {
              loader: 'sass-loader',
              options: {
                sourceMap: isDevelopment,
                sassOptions: {
                  outputStyle: isProduction ? 'compressed' : 'expanded',
                  precision: 6
                }
              }
            }
          ]
        },

        // Зображення
        {
          test: /\.(png|jpe?g|gif|svg|webp)$/i,
          type: 'asset',
          parser: {
            dataUrlCondition: {
              maxSize: 8 * 1024 // 8kb
            }
          },
          generator: {
            filename: 'images/[name].[hash:8][ext]'
          },
          use: [
            {
              loader: 'image-webpack-loader',
              options: {
                mozjpeg: {
                  progressive: true,
                  quality: 85
                },
                optipng: {
                  enabled: isProduction,
                  optimizationLevel: 7
                },
                pngquant: {
                  quality: [0.6, 0.8]
                },
                gifsicle: {
                  interlaced: false
                },
                webp: {
                  quality: 85,
                  enabled: isProduction
                },
                svgo: {
                  plugins: [
                    {
                      name: 'removeViewBox',
                      active: false
                    }
                  ]
                }
              }
            }
          ]
        },

        // Шрифти
        {
          test: /\.(woff|woff2|eot|ttf|otf)$/i,
          type: 'asset/resource',
          generator: {
            filename: 'fonts/[name].[hash:8][ext]'
          }
        },

        // Інші файли
        {
          test: /\.(pdf|doc|docx|xls|xlsx)$/i,
          type: 'asset/resource',
          generator: {
            filename: 'documents/[name].[hash:8][ext]'
          }
        }
      ]
    },

    // Плагіни
    plugins: [
      // Очищення директорії збірки
      new CleanWebpackPlugin(),

      // Витягування CSS
      new MiniCssExtractPlugin({
        filename: isProduction ? 'css/[name].[contenthash:8].min.css' : 'css/[name].css',
        chunkFilename: isProduction ? 'css/chunks/[name].[contenthash:8].chunk.css' : 'css/chunks/[name].chunk.css'
      }),

      // Копіювання статичних файлів
      new CopyWebpackPlugin({
        patterns: [
          {
            from: 'src/themes/slavutska-investment/assets/images/static',
            to: 'images/static',
            noErrorOnMissing: true
          },
          {
            from: 'src/themes/slavutska-investment/assets/fonts/static',
            to: 'fonts/static',
            noErrorOnMissing: true
          }
        ]
      })
    ],

    // Налаштування resolve
    resolve: {
      extensions: ['.js', '.json'],
      alias: {
        '@': path.resolve(__dirname, 'src/themes/slavutska-investment/assets'),
        '@js': path.resolve(__dirname, 'src/themes/slavutska-investment/assets/js'),
        '@css': path.resolve(__dirname, 'src/themes/slavutska-investment/assets/css'),
        '@images': path.resolve(__dirname, 'src/themes/slavutska-investment/assets/images'),
        '@fonts': path.resolve(__dirname, 'src/themes/slavutska-investment/assets/fonts')
      }
    },

    // Externals для WordPress
    externals: {
      jquery: 'jQuery',
      wp: 'wp'
    },

    // Налаштування Dev Server
    devServer: {
      static: {
        directory: path.join(__dirname, 'src/themes/slavutska-investment/assets/dist')
      },
      compress: true,
      port: 3000,
      hot: true,
      open: false,
      headers: {
        'Access-Control-Allow-Origin': '*'
      },
      proxy: {
        '/': {
          target: 'http://localhost:80',
          changeOrigin: true,
          secure: false
        }
      }
    },

    // Налаштування продуктивності
    performance: {
      maxEntrypointSize: 250000,
      maxAssetSize: 250000,
      hints: isProduction ? 'warning' : false
    },

    // Налаштування stats
    stats: {
      colors: true,
      hash: false,
      version: false,
      timings: true,
      assets: true,
      chunks: false,
      modules: false,
      reasons: false,
      children: false,
      source: false,
      errors: true,
      errorDetails: true,
      warnings: true,
      publicPath: false
    }
  };
};