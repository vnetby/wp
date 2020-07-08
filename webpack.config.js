const path = require('path');
const ExtractTextPlugin = require('extract-text-webpack-plugin');
const autoprefixer = require('autoprefixer');
const OptimizeCssAssetsPlugin = require('optimize-css-assets-webpack-plugin');

const fs = require('fs');

const SITE_CONF = JSON.parse(fs.readFileSync('.siteconf.json'));

const theme = `./${SITE_CONF.SRC}`;


const isDev = process.argv[2] !== '--env=build';

const HEAD_SCRIPTS = env => createConfig({ entry: '/js/dev/index.head.js', outputFile: 'head.min.js', cssFileName: 'head.min.css' });

const SCRIPTS = env => createConfig({ entry: '/js/dev/index.js', outputFile: 'main.min.js', cssFileName: 'main.min.css' });



const createConfig = ({ entry, outputFile, cssFileName }) => {
  return {
    mode: !isDev ? 'production' : 'development',
    entry: `${theme}${entry}`,
    output: {
      path: path.resolve(theme, 'js'),
      filename: outputFile
    },
    module: {
      rules: [
        {
          test: /\.js$/,
          // exclude: /node_modules/,
          use: {
            loader: "babel-loader",
            options: {
              cacheDirectory: true,
              presets: [
                '@babel/preset-env',
                '@babel/preset-react'
              ],
              plugins: [
                '@babel/plugin-transform-shorthand-properties',
                "@babel/plugin-transform-arrow-functions"
              ]
            }
          }
        },
        {
          test: /\.css$/,
          use: ExtractTextPlugin.extract({
            fallback: 'style-loader',
            use: [
              {
                loader: 'css-loader',
                options: {
                  url: false,
                  sourceMap: isDev
                }
              }
            ]
          })
        },
        {
          test: /\.less$/,
          use: ExtractTextPlugin.extract({
            fallback: 'style-loader',
            use: [
              {
                loader: 'css-loader',
                options: {
                  url: false,
                  sourceMap: isDev
                }
              },
              {
                loader: 'postcss-loader',
                options: {
                  plugins: [autoprefixer({ grid: true })],
                  sourceMap: isDev
                }
              },
              {
                loader: 'less-loader',
                options: {
                  sourceMap: isDev
                }
              }
            ]
          })
        },
        {
          test: /\.scss$/,
          use: ExtractTextPlugin.extract({
            fallback: 'style-loader',
            use: [
              {
                loader: 'css-loader',
                options: {
                  url: false,
                  sourceMap: isDev
                }

              },
              {
                loader: 'sass-loader',
                options: {
                  sourceMap: true
                }
              }
            ]
          })
        }
      ]
    },
    plugins: [
      new ExtractTextPlugin({
        filename: `../css/${cssFileName}`
      }),
      !isDev ?
        new OptimizeCssAssetsPlugin({
          cssProcessorPluginOptions: {
            preset: ['default', { discardComments: { removeAll: true } }],
          },
          canPrint: true
        }) : { apply: () => { } }
    ],
    devtool: !isDev ? false : 'source-map',
    watch: isDev,
    watchOptions: {
      ignored: /node_modules/,
      poll: 100
    }
  }
}





module.exports = [HEAD_SCRIPTS, SCRIPTS];







