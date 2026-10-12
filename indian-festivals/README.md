# Indian Festivals WordPress Theme

A devotional parchment-style theme for festivals, panchang, vrat, muhurat, katha, deities, greetings and web stories.

## Install

1. Zip the `indian-festivals` folder, or copy it to `wp-content/themes/`.
2. Appearance > Themes > activate **Indian Festivals**.
3. Install and activate **Elementor** (free). The theme shows a notice with Install and Activate buttons. It works without Elementor too.
4. Settings > Permalinks > choose **Post name**, then save.
5. Test on a staging copy first.

## Create these pages (set the slug as shown)

| Page | Slug | Template |
|---|---|---|
| Home | front page | Settings > Reading > A static page, or leave on latest posts |
| Panchang | `panchang` | Default |
| Wishes | `wishes` | Default (add `[ifs_wish_generator]`) |
| Calendar | `calendar` | Default (add `[ifs_countdown]`) |
| Subscribe | `subscribe` | Default (add `[ifs_countdown]` if needed) |
| Muhurat | `muhurat` | Default |
| About, Contact, Privacy Policy, Terms, Disclaimer, Editorial Policy, Advertise, Sitemap | matching slugs | Page or Full Width |

Tool pages are linked as `/tools/{tool-name}/` (for example `/tools/rahu-kaal/`). Create them in Elementor or with shortcodes.

## Dashboard content types

| Menu | Custom fields | Taxonomies |
|---|---|---|
| Festivals | date, end date, tithi, puja muhurat, deity, year, ICS flag | Deity, Tradition, Region, Festival Type, Month |
| Vrats | vrat type, date, start and end time, parana time | Vrat Type |
| Muhurats | category, date, start and end, notes | Muhurat Type |
| Katha & Aarti | audio URL, language, deity | Deity, Language |
| Deities | epithets, symbols, related festivals | Deity |
| Greetings | festival, language, text, image (featured image) | Language, Festival Tag |
| Web Stories | slides (one per line: `heading | text | image URL`), festival | Festival Tag |
| Holidays | date, state, type | State, Holiday Type, Region |
| Panchang Entries | date, city, vara, all five limbs, sunrise, sunset, moonrise | City |
| Cities | latitude, longitude, timezone | none |
| Posts | standard | Category, Tag |

IF Tools (dashboard) provides CSV import and export for festivals, vrats and panchang entries, an ICS feed (`/?ifs_ics=all`) and a panchang cache clear.

## Elementor widgets

Ten widgets are registered in the **Indian Festivals** category. Each one renders a theme shortcode, so they work in Elementor Free:

- Upcoming Festivals `[ifs_upcoming_festivals count="5"]`
- Panchang Today `[ifs_panchang_today]`
- Vrat Cards `[ifs_vrat_cards]`
- Deity Bands `[ifs_deity_bands]`
- Festival Journey `[ifs_journey]`
- Countdown `[ifs_countdown festival="diwali"]`
- Wish Generator `[ifs_wish_generator]`
- Info Strip `[ifs_info_strip]`
- Ad Slot `[ifs_ad_slot slot="in_content"]`
- Tools Grid `[ifs_tools_grid]`

## Appearance > Customize (Theme options)

Every option is in Appearance > Customize under **Theme:** sections:

- General and branding: logo, dark logo, logo size, default city, language labels
- Colors: presets (Brown Desk, Saffron Temple, Royal Blue) or custom colors
- Typography: heading and body fonts (Devanagari-safe choices), weights, sizes, line height, self-host switch
- Header and menu: layout, top bar, search, sticky header, mobile menu style, active style
- Home sections: show or hide the reference-scene sections. The default scene keeps the screenshot layout; rearrange an Elementor page visually if needed.
- Tools grid: show or hide each tool when the Tools Grid widget is used
- Show and hide: breadcrumbs, related posts, share buttons, author box, next and previous links
- Layout and style: container width, spacing, card style, border style, radius, decorations, archive columns, posts per page
- Footer: copyright, Sanskrit line, disclaimer
- Festival and panchang: timezone, date format, calendar start, data source, API URL and key, regional calendar, disclaimer
- Ads and tracking: AdSense code per slot and reserved height, GA4 ID, Search Console tag, custom head and footer code
- SEO: schema toggles and default social image. Schema is skipped automatically when Rank Math, Yoast or AIOSEO is active.
- Social and subscribe: WhatsApp channel, social profiles, email form embed, push code
- Advanced: custom CSS, 3D motion default, lazy load, font preload, emoji script removal

## Paper cards & 3D motion

- Festival, deity and archive cards are textured, irregular torn-paper cutouts with layered edge shadows and slightly raised illustrations.
- A small **3D ON / 3D OFF** control in the top bar saves each visitor's preference in their browser. Default motion is editable in Appearance > Customize > Theme: Advanced.
- Desktop pointers add a reduced, gentle 3D tilt. Card frames retain the original raised hover depth and portrait zoom. Bells, flowers, the watch, lamp glow, milestones and ribbon glint still animate, with smaller movement and slower cycles. Sections fade up with a shorter entrance. Journey arrows select a milestone.
- Coarse-touch devices skip pointer tilt; `prefers-reduced-motion` turns animation off automatically. Content and links work without JavaScript.
- `assets/css/paper-motion.css` and `assets/js/scene-motion.js` are the shared motion files. The decorative SVGs and paper-grain texture are in `assets/images/`.

## Torn paper

Cards and parchment sections use generated, irregular torn edges (`assets/css/torn-paper.css`), with a darker burnt rim and a shadow that follows the torn outline.

## Colours (Drik Panchang style)

`assets/css/drik-theme.css` recolours the design: cream page, deep maroon header, panels and footer, saffron ribbons and highlights, and white content cards. It only changes colours, never layout. Remove that one file from `functions.php` to return to the dark wood look.

## Live date and location

The header date, Vikram Samvat, hero pocket-watch date and the calendar week update automatically in each visitor's own timezone (`assets/js/live-context.js`). The city is filled in automatically from the visitor's approximate network location (BigDataCloud's free client endpoint, no key or cookies). Visitors can click the city to use precise GPS; the browser only asks for that permission when they click. The default city from the Customizer shows if the lookup fails.

Note for your privacy policy: the approximate-location lookup sends the visitor's IP address to BigDataCloud.

## Tools rail

The journey banner now shows **every tool** in a grid with large, high-contrast icons, readable names and a short description, plus category filters (All, Panchang & timings, Festivals & vrat, Muhurat, Share & plan). Edit the tools and strapline in `template-parts/tools-rail.php`, or change the strapline under Theme: Ads and Tracking.

## Advertising slots

Vertical rails sit on the left and right of **every page** on screens 1100px and wider (120px wide up to 1500px, 160px above). The content column narrows to make room, so rails never cover content or cause sideways scrolling. They are hidden on tablets and phones. Content ad slots also appear inside every page. Horizontal slots also appear above the hero, mid content and above the footer. Every slot reserves its height, so the page does not jump when an ad loads.

Paste AdSense code into Appearance > Customize > Theme: Ads and Tracking. Until you do, each slot shows a clearly labelled placeholder.

## Festival pages (Drik Panchang style)

Each festival gets four views from one template (`single-festival.php`), switched with `?view=`:

- **Timings** (default): location + year bar, olive date card beside the deity image, green timings ribbon, 12/24-hour toggle, red muhurat lines with durations, tithi begins/ends, panchang links, Vrat Katha box and notes.
- **About**: full story, significance, rituals and the Vrat Katha.
- **Muhurat**: the timings card plus an explanation of the muhurat and avoid-periods.
- **Summary**: quick facts table and a one-paragraph summary.

Year navigation: publish one festival post per year with slugs like `diwali-2025`, `diwali-2026`, `diwali-2027`. The template finds sibling year posts automatically for Prev Year / Next Year; missing years show as disabled. New festival fields (muhurat start/end, avoid times, tithi begins/ends, summary, significance, rituals, Katha title/text) are in the Details box.

Note: preview timings are illustrative samples. Connect a licensed panchang source for production dates.

## SEO and ranking

The theme ships its own SEO output and switches it off automatically when Rank Math, Yoast, SEOPress or AIOSEO is active, so nothing is duplicated.

- **Per-post SEO box** (right in the editor): SEO title, meta description, focus keyword, canonical URL and a "hide from search engines" toggle. Also usable for the fallback title.
- **Canonical URLs** on every page. Year and city query variants (`?year=2026`, `?city=Mumbai`) point back to the base address, so Google indexes one version instead of many copies.
- **XML sitemap** at `/sitemap.xml`, with a child sitemap per post type. Publishing triggers a ping to Google.
- **Structured data**: WebSite + SearchAction on the home page, Event for festivals (with start and end dates), Article for blog posts, BreadcrumbList throughout.
- **Open Graph + Twitter cards**, so shared links show a picture and title in WhatsApp, Facebook and X.
- **robots meta**: `index, follow, max-image-preview:large` on content; `noindex` on search results, 404 and anything you mark.
- **Visible breadcrumbs** for visitors and search engines.

### What actually moves rankings

1. **One page, one search phrase.** Give each festival a unique SEO title. "Diwali 2026 Date and Muhurat" is better than repeating "Indian Festivals" on every page.
2. **Write enough text.** Thin pages (a couple of lines) rank poorly and can fail AdSense review. Aim for 400+ words on About pages, with your own writing.
3. **Fresh dates every year.** Update the date and timings each year and let the last-modified date change. Google rewards refreshed pages for date queries.
4. **Internal links.** Link every festival to its related deity page, vrat and month. The Related block and the deity pages do this automatically.
5. **Speed.** Keep images under 200 KB, enable lazy loading (Theme: Advanced), and use a cache plugin (WP Rocket or LiteSpeed).
6. **HTTPS and mobile.** Test at 360px. Google uses mobile-first indexing.
7. **Author and last-updated.** Show author names and update dates on articles.
8. **Search Console.** Submit `/sitemap.xml` there and watch coverage weekly.

## Ad placement manager

Appearance > Customize > Theme: Ads and Tracking controls **where** ads appear, not just what they say. Every slot has three controls:

- **Code** — your AdSense `<ins>` block
- **Show on** — Every page, Home only, Single posts and pages, Festival pages only, Archives and search, or Nowhere
- **Reserved height** — the space kept for the ad so the page does not jump

Slots: header banner, top leaderboard, in content, mid content, left skyscraper, right skyscraper, sidebar, below the title, above the footer, and between archive cards. Enter your AdSense publisher ID (`ca-pub-...`) once and the loader script is injected automatically. Empty slots show no space at all to visitors.

Tip: two skyscrapers plus one in-content block is the most AdSense allows on a typical screen without hurting user experience. Do not stack five ads above the fold.

## Panchang accuracy

The theme does not calculate astronomy. Values come from:

1. **Panchang Entries** (manual or CSV), copied from an authentic panchang you are allowed to use, or
2. A **licensed API** (for example a service built on Swiss Ephemeris), set under Festival and Panchang.

Rahu Kaal, Yamaganda, Gulika, Abhijit and Brahma Muhurat are calculated from the day's sunrise and sunset. Always show the disclaimer.

## Artwork

The theme ships with its Varanasi hero illustration, five festival illustrations, four additional deity portraits and scalable ornament SVGs. Festival and deity featured images replace the bundled fallback artwork automatically.

On a fresh WordPress installation, festival card artwork and *sample dates* show the intended layout. Once a matching Festival post is published, the card uses that post's date and image. The Panchang panel deliberately shows `—` until a verified entry or licensed API is connected.

## Not included yet

- Elementor native widget panels with visual controls (widgets use shortcodes with an attribute field)
- Choghadiya tables, eclipse and moonrise calculators, Hindu date converter, printable PDF calendar
- Drag-and-drop home section order (use the comma-separated order field)
- Dedicated Theme Settings page (all options are in Customize)
- Hindi and English page translation (use WPML or Polylang)
- Web push and WhatsApp channel delivery (add the provider code in Social and Subscribe)

## Legal and trust

- Use original text and licensed images only.
- Show "Last updated" dates, author names and sources on articles.
- Publish the Editorial and Correction Policy, Disclaimer, Privacy Policy and Terms as pages.

## Theme Options (Indian Festivals > Theme Options)

A dedicated admin page with eight tabs. Settings are stored in the same place as the Customizer, so every template reads them automatically.

- **Post Views:** count single-page visits, choose an eye or fire icon, set the starting number for posts never counted, and colour the number by popularity (warm, hot, very hot thresholds). Visits by logged-in administrators are not counted. Full-page caching can reduce counts.
- **Archives:** excerpt on or off, excerpt length, read more link and text, and view counts on cards.
- **Share Buttons:** switch each network on or off (X, Facebook, LinkedIn, Pinterest, Reddit, Telegram, WhatsApp, Pocket, Email), set the X username, choose left, centre or right, and show or hide the heading. Shown on single posts and pages.
- **Advertisement:** show or hide the left and right skyscraper ads, the screen width they appear from, and their vertical offset. Ad code stays in Customize > Theme: Ads and Tracking.
- **Ad Blocker Detector:** optional message box for visitors whose ad blocker hides a test ad. Set the title, message, delay and box colour. Detection is a heuristic and misses some blockers. Keep "visitors can close the message" on, because AdSense policy discourages blocking visitors until they disable a blocker.
- **Background:** background colour, a second colour for a gradient, a background image, and an optional dimmer.
- **Styling:** basic dark skin and extra body classes.
- **Integrations:** Google Maps, YouTube, OpenWeather and Custom Search keys, and an Instagram access token. The keys are stored for future features. Connecting Instagram needs a Meta app and is not automated in this version.

Not included in this version: a full-page takeover ad, a seven-day "trending" sort for post views, and a full colour skin system. The Drik-style colours are fixed in the stylesheet, so the Primary and Accent colour controls in the Customizer do not restyle that layout.
