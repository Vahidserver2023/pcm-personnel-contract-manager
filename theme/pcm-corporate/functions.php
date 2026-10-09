/*
Theme Name: pcm-corporate
Theme URI: https://example.com/pcm-corporate
Author: PCM Team
Description: Corporate RTL WordPress theme for the PCM Human Resources platform.
Version: 1.0.0
Text Domain: pcm-corporate
Requires at least: 6.4
Tested up to: 6.6
Requires PHP: 8.2
*/

:root {
    --pcm-bg: #f4f7fb;
    --pcm-surface: #ffffff;
    --pcm-text: #162033;
    --pcm-muted: #667085;
    --pcm-primary: #1943a8;
    --pcm-accent: #0ea5e9;
    --pcm-border: #dfe7f4;
    --pcm-success: #0b8457;
    --pcm-warning: #efb63d;
    --pcm-danger: #d14343;
}

body {
    margin: 0;
    font-family: 'Vazirmatn', Tahoma, sans-serif;
    background: var(--pcm-bg);
    color: var(--pcm-text);
    direction: rtl;
    line-height: 1.7;
}

a {
    color: var(--pcm-primary);
    text-decoration: none;
}

img {
    max-width: 100%;
    height: auto;
}

.container {
    width: min(1200px, calc(100% - 32px));
    margin: 0 auto;
}

.site-header {
    background: #0d172a;
    color: #fff;
    padding: 16px 0;
}

.site-header .inner {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 16px;
}

.site-nav {
    display: flex;
    gap: 18px;
    flex-wrap: wrap;
}

.site-nav a {
    color: #fff;
    opacity: 0.9;
}

.hero {
    background: linear-gradient(135deg, #0d172a, #1943a8);
    color: #fff;
    padding: 70px 0;
}

.hero h1 {
    font-size: clamp(2rem, 4vw, 4rem);
    margin-bottom: 16px;
}

.card-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 20px;
    margin: 32px 0;
}

.card {
    background: var(--pcm-surface);
    border: 1px solid var(--pcm-border);
    border-radius: 16px;
    padding: 24px;
    box-shadow: 0 8px 22px rgba(0, 0, 0, 0.04);
}

.site-footer {
    background: #09111c;
    color: #dfe7f4;
    padding: 40px 0;
    margin-top: 48px;
}

@media (max-width: 768px) {
    .site-header .inner {
        flex-direction: column;
        align-items: flex-start;
    }
}
