const puppeteer = require('puppeteer');
const zlib = require('zlib');

const main = async () => {
    const content = process.argv[2];
    const desiredFilename = process.argv[3];

    if (!content || !desiredFilename) {
        process.exit(1);
    }

    const base64buffer = new Buffer.from(content, 'base64');
    
    const html = zlib.inflateRawSync(base64buffer).toString();

    const browser = await puppeteer.launch();
    const page = await browser.newPage();
    await page.setContent(html);
    await page.pdf({
        path: `${__dirname}/../var/temp/pdf/${desiredFilename}`,
        printBackground: true,
        format: 'A4'
    });

    await browser.close();

    console.log(desiredFilename);
}

main();