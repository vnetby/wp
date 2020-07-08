const fs = require("fs");
const SITE_CONF = JSON.parse(fs.readFileSync('.siteconf.json'));

const ABSPATH = `./${SITE_CONF.SRC}/assets`;

const assets = [
  // "jquery3",
  "hamburgers",
  "animate-css",
  "fancybox",
  "slick",
  "niceSelect",
  "aos",
  "parallax"
  // "overlay-scrollbar",
  // "chart-js",
];

const readAssetsPath = () => {
  return new Promise((resolve, reject) => {
    fs.readdir(ABSPATH, (err, data) => {
      if (err) {
        reject(err);
        return;
      }
      resolve(data);
    });
  });
};

const getFiles = (paths, ext) => {
  return new Promise((resolve, reject) => {
    let res = "";
    paths.forEach(path => {
      let files = fs.readdirSync(`${ABSPATH}/${path}`);
      files.forEach(file => {
        if (file.endsWith(`.${ext}`)) {
          res += fs.readFileSync(`${ABSPATH}/${path}/${file}`);
        }
      });
    });
    resolve(res);
  });
};

getFiles(assets, "css").then(file => {
  fs.writeFile(`${ABSPATH}/assets.min.css`, file, err => {
    if (err) throw err;
    console.log("css has been written");
  });
});

getFiles(assets, "js").then(file => {
  fs.writeFile(`${ABSPATH}/assets.min.js`, file, err => {
    if (err) throw err;
    console.log("js has been written");
  });
});
