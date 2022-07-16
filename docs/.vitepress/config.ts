import fs from 'fs';
import { defineConfig } from 'vitepress';

const removeExtension = (filename: string) => filename.split('.').shift() ?? '';
const kebabToSpaced = (kebab: string) => kebab.split('-').join(' ');
const capitalizeFirstLetter = (name: string) => name.charAt(0).toUpperCase() + name.slice(1);

const blockTitle = (filename: string) => {
    return removeExtension(filename);
}

const howToTitle = (filename: string) => {
    const noExtension = removeExtension(filename);
    const spaced = kebabToSpaced(noExtension);

    return capitalizeFirstLetter(spaced);
}

const blockItems = fs.readdirSync('./blocks')
    .filter(file => file !== 'index.md')
    .sort()
    .map(file => ({
        text: blockTitle(file),
        link: `/blocks/${file}`,
    }));

const howToItems = fs.readdirSync('./how-to')
    .filter(file => file !== 'index.md')
    .sort()
    .map(file => ({
        text: howToTitle(file),
        link: `/how-to/${file}`,
    }));

export default defineConfig({
    base: '/notion-sdk-php/',
    title: 'Notion SDK PHP',
    description: 'A complete Notion SDK for PHP developers.',
    lang: 'en-US',
    lastUpdated: true,
    head: [
        ['link', { rel: "apple-touch-icon", sizes: "180x180", href: "/favicons/apple-touch-icon.png"}],
        ['link', { rel: "icon", type: "image/png", sizes: "32x32", href: "/favicons/favicon-32x32.png"}],
        ['link', { rel: "icon", type: "image/png", sizes: "16x16", href: "/favicons/favicon-16x16.png"}],
        ['link', { rel: "manifest", href: "/favicons/site.webmanifest"}],
        ['link', { rel: "shortcut icon", href: "/favicons/favicon.ico"}],
        ['meta', { name: "theme-color", content: "#787CB5"}],
    ],
    themeConfig: {
        logo: '/logo.png',
        nav: [
            { text: 'Documentation', link: '/getting-started' },
            { text: 'Changelog', link: 'https://github.com/mariosimao/notion-sdk-php/blob/main/CHANGELOG.md'},
        ],
        socialLinks: [
            { icon: 'github', link: 'https://github.com/mariosimao/notion-sdk-php' },
            { icon: 'twitter', link: 'https://twitter.com/mariogsimao' },
        ],
        sidebar: [
            {
                text: 'Introduction',
                items: [
                    { text: 'Getting started', link: '/getting-started' },
                ],
            },
            {
                text: 'Blocks',
                collapsible: true,
                items: [
                    { text: 'Introduction', link: '/blocks/' },
                    ...blockItems,
                ],
            },
            {
                text: 'How to',
                collapsible: true,
                items: [
                    ...howToItems,
                ],
            }
        ],
        footer: {
            message: 'Released under the MIT License.',
            copyright: 'Copyright © 2021-present Mario Simão',
        },
    },
});
