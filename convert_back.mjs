import sharp from 'sharp';
import fs from 'fs';
import path from 'path';
import { fileURLToPath } from 'url';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

const inputDir = path.join(__dirname, 'public', 'images', 'cars');

async function convertToPNG(inputPath, outputPath) {
    try {
        // Convert WebP to PNG with white background
        await sharp(inputPath)
            .flatten({ background: { r: 255, g: 255, b: 255 } }) // White background
            .png()
            .toFile(outputPath);

        console.log(`Converted: ${path.basename(inputPath)} to PNG`);
    } catch (error) {
        console.error(`Error converting ${inputPath}:`, error);
    }
}

async function main() {
    const files = fs.readdirSync(inputDir).filter(file => file.endsWith('.webp'));

    for (const file of files) {
        const inputPath = path.join(inputDir, file);
        const outputPath = path.join(inputDir, file.replace('.webp', '.png'));
        await convertToPNG(inputPath, outputPath);
    }

    console.log('All WebP converted back to PNG with white background.');
}

main().catch(console.error);