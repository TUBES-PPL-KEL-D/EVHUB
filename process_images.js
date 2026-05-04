const sharp = require('sharp');
const fs = require('fs');
const path = require('path');

const inputDir = path.join(__dirname, 'public', 'images', 'cars');
const outputDir = inputDir; // Overwrite original

async function processImage(inputPath, outputPath) {
    try {
        // Read image
        const image = sharp(inputPath);

        // Get image info
        const metadata = await image.metadata();

        // Create a mask for white background (assuming white is #FFFFFF)
        // We'll make pixels that are close to white transparent
        await image
            .ensureAlpha() // Add alpha channel if not present
            .raw() // Get raw pixel data
            .toBuffer({ resolveWithObject: true })
            .then(async ({ data, info }) => {
                const { width, height, channels } = info;
                const newData = Buffer.alloc(width * height * 4); // RGBA

                for (let i = 0; i < data.length; i += channels) {
                    const r = data[i];
                    const g = data[i + 1];
                    const b = data[i + 2];
                    const a = channels === 4 ? data[i + 3] : 255;

                    // Check if pixel is close to white (within tolerance)
                    const tolerance = 30; // Adjust as needed
                    const isWhite = r > 255 - tolerance && g > 255 - tolerance && b > 255 - tolerance;

                    if (isWhite) {
                        // Make transparent
                        newData[i] = r;
                        newData[i + 1] = g;
                        newData[i + 2] = b;
                        newData[i + 3] = 0; // Alpha = 0
                    } else {
                        newData[i] = r;
                        newData[i + 1] = g;
                        newData[i + 2] = b;
                        newData[i + 3] = a;
                    }
                }

                // Save as WebP
                await sharp(newData, {
                    raw: {
                        width,
                        height,
                        channels: 4
                    }
                })
                .webp({ quality: 80 }) // Good quality, smaller size
                .toFile(outputPath.replace('.png', '.webp'));
            });

        console.log(`Processed: ${path.basename(inputPath)}`);
    } catch (error) {
        console.error(`Error processing ${inputPath}:`, error);
    }
}

async function main() {
    const files = fs.readdirSync(inputDir).filter(file => file.endsWith('.png'));

    for (const file of files) {
        const inputPath = path.join(inputDir, file);
        const outputPath = path.join(outputDir, file.replace('.png', '.webp'));
        await processImage(inputPath, outputPath);
    }

    console.log('All images processed.');
}

main().catch(console.error);