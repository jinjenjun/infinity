#!/usr/bin/env node
import fs from "fs";
import os from "os";
import path from "path";
import AdmZip from "adm-zip";
import * as cheerio from "cheerio";

const [, , input, output] = process.argv;
if (!input || !output) {
    console.log("用法: node epub_doctor_cover.js input.epub output.epub");
    process.exit(1);
}
if (!fs.existsSync(input)) {
    console.log("找不到 epub");
    process.exit(1);
}

const tmp = fs.mkdtempSync(path.join(os.tmpdir(), "epubdoc_"));
const zip = new AdmZip(input);
zip.extractAllTo(tmp, true);

const OEBPS = path.join(tmp, "OEBPS");
const META = path.join(tmp, "META-INF");
if (!fs.existsSync(OEBPS)) fs.mkdirSync(OEBPS, { recursive: true });
if (!fs.existsSync(META)) fs.mkdirSync(META, { recursive: true });

const log = [];
let changed = false;

// 掃描檔案（路徑相對於 OEBPS，使用 posix 分隔符）
function scan(dir) {
    let list = [];
    for (const f of fs.readdirSync(dir)) {
        const full = path.join(dir, f);
        if (fs.statSync(full).isDirectory()) list = list.concat(scan(full));
        else list.push(path.relative(OEBPS, full).replace(/\\/g, "/"));
    }
    return list;
}
const files = scan(OEBPS);
log.push(`掃描 OEBPS，找到 ${files.length} 個檔案`);

// 分類
const html = [],
    css = [],
    img = [];
for (const f of files) {
    const ext = f.split(".").pop().toLowerCase();
    if (["xhtml", "html"].includes(ext)) html.push(f);
    else if (ext === "css") css.push(f);
    else if (["jpg", "jpeg", "png", "gif", "svg", "webp"].includes(ext))
        img.push(f);
}
log.push(
    `分類檔案：HTML=${html.length} CSS=${css.length} Images=${img.length}`,
);

// 找 opf 路徑
const opfFile =
    files.find((f) => path.basename(f).toLowerCase() === "content.opf") ||
    files.find((f) => f.toLowerCase().endsWith(".opf"));
const opfPath = path.join(OEBPS, opfFile || "content.opf");

// 優先從 content.opf manifest 找 properties="nav" 的檔案
let navFile = null;
if (fs.existsSync(opfPath)) {
    const $opf = cheerio.load(fs.readFileSync(opfPath, "utf8"), {
        xmlMode: true,
    });
    $opf("manifest item").each((_, el) => {
        const props = $opf(el).attr("properties") || "";
        if (props.includes("nav")) {
            navFile = $opf(el).attr("href")?.replace(/\\/g, "/") || null;
        }
    });
    if (navFile) log.push(`從 content.opf 找到 nav：${navFile}`);
}

// opf 找不到才用檔名猜
if (!navFile) {
    navFile =
        html.find((f) => path.basename(f).toLowerCase() === "toc.xhtml") ||
        html.find((f) => path.basename(f).toLowerCase() === "nav.xhtml") ||
        html.find((f) => f.toLowerCase().includes("nav"));
    if (navFile) log.push(`從檔名猜到 nav：${navFile}`);
}

// 如果同時有 toc.xhtml 和 nav.xhtml，優先用 toc.xhtml，刪掉 nav.xhtml
const hasTocXhtml = html.find(
    (f) => path.basename(f).toLowerCase() === "toc.xhtml",
);
const hasNavXhtml = html.find(
    (f) => path.basename(f).toLowerCase() === "nav.xhtml",
);

if (hasTocXhtml && hasNavXhtml) {
    fs.unlinkSync(path.join(OEBPS, hasNavXhtml));
    html.splice(html.indexOf(hasNavXhtml), 1);
    navFile = hasTocXhtml;
    log.push(`同時有 TOC.xhtml 和 nav.xhtml，刪除 nav.xhtml，改用 TOC.xhtml`);
    changed = true;
}

const ncxFileInOEBPS =
    files.find((f) => path.basename(f).toLowerCase() === "toc.ncx") ||
    files.find((f) => f.toLowerCase().endsWith(".ncx"));

// nav 路徑
const navPath = path.join(OEBPS, navFile || "nav.xhtml");

// toc.ncx 統一放在 OEBPS/，如果原本在根目錄就移進來
const ncxInOEBPS = ncxFileInOEBPS
    ? path.join(OEBPS, ncxFileInOEBPS)
    : path.join(OEBPS, "toc.ncx");
const ncxInRoot = path.join(tmp, "toc.ncx");

if (!ncxFileInOEBPS && fs.existsSync(ncxInRoot)) {
    fs.renameSync(ncxInRoot, ncxInOEBPS);
    log.push("toc.ncx 從根目錄移入 OEBPS/");
    changed = true;
}

const ncxPath = ncxInOEBPS;
const ncxHref = ncxFileInOEBPS || "toc.ncx";
const navHref = navFile || "nav.xhtml";

log.push(`nav 位置：${navHref}`);
log.push(`toc.ncx href：${ncxHref}`);
log.push(`toc.ncx 實際路徑：${ncxPath}`);
log.push(`toc.ncx 存在：${fs.existsSync(ncxPath)}`);
log.push(`content.opf 位置：${opfFile || "content.opf"}`);

// 封面
let cover = html.find((f) => f.toLowerCase().includes("cover"));

// chapters 排除 navFile 和 cover
const chapters = html
    .filter((f) => f !== navFile && f !== cover)
    .sort((a, b) => a.localeCompare(b, undefined, { numeric: true }));

if (chapters.length === 0) {
    console.log("沒有章節");
    process.exit(1);
}
log.push(`封面檔案：${cover || "無"}，章節數：${chapters.length}`);
log.push(`章節列表：${chapters.join(", ")}`);

// 修 HTML（只修有問題的）
function fixHTML(file) {
    const full = path.join(OEBPS, file);
    let content = fs.readFileSync(full, "utf8");
    const $ = cheerio.load(content, { xmlMode: true });
    const missingHtml = $("html").length === 0;
    const missingBody = $("body").length === 0;
    if (!missingHtml && !missingBody) return;
    if (missingHtml) {
        content = `<html><head></head><body>${content}</body></html>`;
    } else if (missingBody) {
        $("html")
            .append(`<body>${$("html").html()}</body>`)
            .children()
            .not("body")
            .remove();
        content = $.xml();
    }
    fs.writeFileSync(full, content);
    log.push(`修正 HTML 結構：${file}`);
    changed = true;
}
if (cover) fixHTML(cover);
chapters.forEach(fixHTML);

// 抓標題
function getTitle(file) {
    const full = path.join(OEBPS, file);
    const content = fs.readFileSync(full, "utf8");
    const $ = cheerio.load(content, { xmlMode: true });
    return (
        $("h1").first().text().trim() ||
        $("title").first().text().trim() ||
        $("h2").first().text().trim() ||
        path.basename(file)
    );
}
const titles = chapters.map(getTitle);

// 檢查 mimetype
const mimetypePath = path.join(tmp, "mimetype");
const correctMimetype = "application/epub+zip";
if (
    !fs.existsSync(mimetypePath) ||
    fs.readFileSync(mimetypePath, "utf8").trim() !== correctMimetype
) {
    fs.writeFileSync(mimetypePath, correctMimetype);
    log.push("修正 mimetype");
    changed = true;
} else {
    log.push("mimetype 正常，跳過");
}

// 檢查 container.xml
const containerPath = path.join(META, "container.xml");
let containerOk = false;
if (fs.existsSync(containerPath)) {
    containerOk = fs
        .readFileSync(containerPath, "utf8")
        .includes("content.opf");
}
if (!containerOk) {
    fs.writeFileSync(
        containerPath,
        `<?xml version="1.0"?>
<container version="1.0" xmlns="urn:oasis:names:tc:opendocument:xmlns:container">
<rootfiles>
<rootfile full-path="OEBPS/content.opf" media-type="application/oebps-package+xml"/>
</rootfiles>
</container>`,
    );
    log.push("修正 container.xml");
    changed = true;
} else {
    log.push("container.xml 正常，跳過");
}

// 檢查 content.opf（保留原有 metadata）
let existingMetadata = null;
let opfNeedsUpdate = false;

if (fs.existsSync(opfPath)) {
    const opfContent = fs.readFileSync(opfPath, "utf8");
    const $opf = cheerio.load(opfContent, { xmlMode: true });
    existingMetadata = $opf("metadata").toString();
    const manifestHrefs = [];
    $opf("manifest item").each((_, el) => {
        const href = $opf(el).attr("href");
        if (href) manifestHrefs.push(href.replace(/\\/g, "/"));
    });
    const allExpected = [
        ...(cover ? [cover] : []),
        ...chapters,
        ...css,
        ...img,
        ncxHref,
        navHref,
    ];
    const missingInManifest = allExpected.filter(
        (f) => !manifestHrefs.includes(f),
    );
    const spineCount = $opf("spine itemref").length;
    if (missingInManifest.length > 0) {
        log.push(`manifest 缺少 ${missingInManifest.length} 個項目，需要更新`);
        log.push(`缺少的項目：${missingInManifest.join(", ")}`);
        opfNeedsUpdate = true;
    } else if (spineCount === 0) {
        log.push("spine 為空，需要更新");
        opfNeedsUpdate = true;
    } else {
        log.push("content.opf 正常，跳過");
    }
} else {
    log.push("content.opf 不存在，需要建立");
    opfNeedsUpdate = true;
}

if (opfNeedsUpdate) {
    let manifest = [];
    let coverId = null;
    if (cover) {
        coverId = "cover1";
        manifest.push({
            id: coverId,
            href: cover,
            type: "application/xhtml+xml",
        });
    }
    chapters.forEach((f, i) =>
        manifest.push({
            id: `chap${i + 1}`,
            href: f,
            type: "application/xhtml+xml",
        }),
    );
    css.forEach((f, i) =>
        manifest.push({ id: `css${i + 1}`, href: f, type: "text/css" }),
    );
    img.forEach((f, i) => {
        const ext = f.split(".").pop().toLowerCase();
        let type = "image/jpeg";
        if (ext === "png") type = "image/png";
        else if (ext === "gif") type = "image/gif";
        else if (ext === "svg") type = "image/svg+xml";
        else if (ext === "webp") type = "image/webp";
        manifest.push({ id: `img${i + 1}`, href: f, type });
    });
    manifest.push({
        id: "ncx",
        href: ncxHref,
        type: "application/x-dtbncx+xml",
    });
    manifest.push({
        id: "nav",
        href: navHref,
        type: "application/xhtml+xml",
        properties: "nav",
    });

    const manifestXML = manifest
        .map((m) =>
            m.properties
                ? `<item id="${m.id}" href="${m.href}" media-type="${m.type}" properties="${m.properties}"/>`
                : `<item id="${m.id}" href="${m.href}" media-type="${m.type}"/>`,
        )
        .join("\n");

    let spineItems = cover ? [`<itemref idref="${coverId}"/>`] : [];
    spineItems = spineItems.concat(
        chapters.map((_, i) => `<itemref idref="chap${i + 1}"/>`),
    );

    const metadata =
        existingMetadata ||
        `<metadata xmlns:dc="http://purl.org/dc/elements/1.1/">
<dc:identifier id="BookId">uid</dc:identifier>
<dc:title>Book</dc:title>
<dc:language>zh-TW</dc:language>
</metadata>`;

    fs.writeFileSync(
        opfPath,
        `<?xml version="1.0" encoding="utf-8"?>
<package xmlns="http://www.idpf.org/2007/opf" version="2.0" unique-identifier="BookId">
${metadata}
<manifest>
${manifestXML}
</manifest>
<spine toc="ncx">
${spineItems.join("\n")}
</spine>
</package>`,
    );
    log.push("更新 content.opf");
    changed = true;
}

// 檢查 toc.ncx
let ncxNeedsUpdate = false;
if (fs.existsSync(ncxPath)) {
    const $ncx = cheerio.load(fs.readFileSync(ncxPath, "utf8"), {
        xmlMode: true,
    });
    const count = $ncx("navPoint").length;
    if (count !== chapters.length) {
        log.push(
            `toc.ncx navPoint 數量 (${count}) 與章節數 (${chapters.length}) 不符，需要更新`,
        );
        ncxNeedsUpdate = true;
    } else {
        log.push("toc.ncx 正常，跳過");
    }
} else {
    log.push("toc.ncx 不存在，需要建立");
    ncxNeedsUpdate = true;
}
if (ncxNeedsUpdate) {
    const navpoints = chapters
        .map(
            (f, i) => `
<navPoint id="nav${i + 1}" playOrder="${i + 1}">
  <navLabel><text>${titles[i]}</text></navLabel>
  <content src="${f}"/>
</navPoint>`,
        )
        .join("\n");
    fs.writeFileSync(
        ncxPath,
        `<?xml version="1.0" encoding="utf-8"?>
<ncx xmlns="http://www.daisy.org/z3986/2005/ncx/" version="2005-1">
<head>
<meta name="dtb:uid" content="uid"/>
<meta name="dtb:depth" content="1"/>
</head>
<docTitle><text>Book</text></docTitle>
<navMap>
${navpoints}
</navMap>
</ncx>`,
    );
    log.push("更新 toc.ncx");
    changed = true;
}

// nav：存在就不動，找不到才新建
if (fs.existsSync(navPath)) {
    log.push("nav 存在，跳過");
} else {
    log.push("nav 不存在，需要建立");
    const navlinks = chapters
        .map((f, i) => `<li><a href="${f}">${titles[i]}</a></li>`)
        .join("\n");
    fs.writeFileSync(
        navPath,
        `<?xml version="1.0" encoding="utf-8"?>
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:epub="http://www.idpf.org/2007/ops">
<head><title>TOC</title><meta charset="utf-8"/></head>
<body>
<h1>目錄</h1>
<nav epub:type="toc"><ol>${navlinks}</ol></nav>
</body>
</html>`,
    );
    log.push("建立 nav");
    changed = true;
}

// 打包
const newZip = new AdmZip();
newZip.addFile("mimetype", Buffer.from("application/epub+zip"), "", 0);
function add(dir, base = "") {
    for (const f of fs.readdirSync(dir)) {
        if (f === "mimetype") continue;
        const full = path.join(dir, f);
        const rel = path.join(base, f);
        if (fs.statSync(full).isDirectory()) add(full, rel);
        else newZip.addLocalFile(full, path.dirname(rel));
    }
}
add(tmp);
newZip.writeZip(output);

// 輸出 log
if (changed) {
    console.log("EPUB 已修復，以下項目有修改：");

    console.log("\n執行紀錄：");
    log.forEach((l) => console.log(" -", l));
} else {
    console.log("EPUB 結構正常，無需修改。");
}

// 修改項目彙整
const updatedItems = [];
if (
    log.some(
        (l) => l.includes("更新 content.opf") || l.includes("建立 content.opf"),
    )
)
    updatedItems.push("content.opf");
if (log.some((l) => l.includes("更新 toc.ncx") || l.includes("建立 toc.ncx")))
    updatedItems.push("toc.ncx");
if (log.some((l) => l.includes("建立 nav") || l.includes("更新 nav")))
    updatedItems.push("nav");
if (log.some((l) => l.includes("修正 HTML"))) updatedItems.push("HTML 結構");
if (log.some((l) => l.includes("修正 mimetype"))) updatedItems.push("mimetype");
if (log.some((l) => l.includes("修正 container.xml")))
    updatedItems.push("container.xml");
if (log.some((l) => l.includes("移入"))) updatedItems.push("toc.ncx 位置");
if (log.some((l) => l.includes("刪除 nav"))) updatedItems.push("nav 清理");

if (updatedItems.length > 0) {
    console.log("\n修改項目彙整：");
    updatedItems.forEach((l) => console.log(" *", l));
    process.stderr.write(updatedItems.join(", "));
}
