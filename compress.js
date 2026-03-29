const fs = require("fs");
const crypto = require("crypto");
const archiver = require("archiver");
const path = require("path");
const { minify: terserMinify } = require("terser");
const postcss = require("postcss");
const cssnano = require("cssnano");

const outputPath = path.join(__dirname, "/dist/ps_popup_dialog_box.ocmod.zip");
const output = fs.createWriteStream(outputPath);
const archive = archiver("zip", { zlib: { level: 4 } });

const jsSrc = path.join(__dirname, "src/catalog/view/javascript/ps_popup_dialog_box.js");
const cssSrc = path.join(__dirname, "src/catalog/view/stylesheet/ps_popup_dialog_box.css");
const jsDest = "catalog/view/javascript/ps_popup_dialog_box.min.js";
const cssDest = "catalog/view/stylesheet/ps_popup_dialog_box.min.css";

output.on("close", () => {
  console.log(`${archive.pointer()} total bytes`);
  console.log("ps_popup_dialog_box.ocmod.zip has been created");
  calculateChecksums(outputPath);
});

archive.on("warning", (err) => (err.code === "ENOENT" ? console.warn("Warning:", err) : Promise.reject(err)));
archive.on("error", (err) => { throw err; });

archive.pipe(output);

async function addMinifiedFiles() {
  const tasks = [];

  if (fs.existsSync(jsSrc)) {
    tasks.push(
      terserMinify(fs.readFileSync(jsSrc, "utf8"), { compress: true, mangle: true })
        .then((result) => archive.append(result.code, { name: jsDest }))
        .then(() => console.log("Minified JavaScript added"))
    );
  } else {
    console.warn(`JS source not found: ${jsSrc}`);
  }

  if (fs.existsSync(cssSrc)) {
    tasks.push(
      postcss([cssnano])
        .process(fs.readFileSync(cssSrc, "utf8"), { from: cssSrc, to: cssDest })
        .then((result) => archive.append(result.css, { name: cssDest }))
        .then(() => console.log("Minified CSS added"))
    );
  } else {
    console.warn(`CSS source not found: ${cssSrc}`);
  }

  await Promise.all(tasks);

  archive.directory("src/admin/", "admin");
  archive.directory("src/catalog/", "catalog");
  archive.file("src/install.json", { name: "install.json" });
  archive.file("src/installation.txt", { name: "installation.txt" });

  archive.finalize();
}

addMinifiedFiles().catch(console.error);

function calculateChecksums(filePath) {
  const fileBuffer = fs.readFileSync(filePath);
  console.log(`MD5 Checksum: ${crypto.createHash("md5").update(fileBuffer).digest("hex")}`);
  console.log(`SHA256 Checksum: ${crypto.createHash("sha256").update(fileBuffer).digest("hex")}`);
}
