# WebMCP for WordPress - Design Prompts

Use these prompts with Midjourney, DALL-E, or share with a designer for custom creation.

---

## 1. WordPress.org Plugin Banner (1544×500px)

### Midjourney/DALL-E Prompt:

```
Modern developer-focused WordPress plugin banner. Left side features the bold text
"WebMCP for WordPress" with tagline "AI agents, meet WordPress." in clean sans-serif
typography. Right side shows a macOS-style code editor window displaying JSON tool
definitions with syntax highlighting (green text on dark background), showing
navigator.modelContext API with an array of tools. The window has authentic traffic
lights in the top-left corner (red, yellow, green).

Between the text and code window, subtle visual bridge: WordPress logo (blue W) on
left connects to Chrome/Google logo on right with a minimalist arrow or connection line.

Color palette: WordPress blue (#21759B), Chrome blue (#4285F4), pure white background
with soft gradient to light gray (#f8f9fa). No gradients on text. Professional,
minimal aesthetic — like Stripe or Vercel product pages. High contrast, clean spacing.

Remove any playful elements, clipart, or illustrated icons. Pure typography and
minimalist graphic design. 16:5 aspect ratio (1544×500).
```

### Designer Brief:

**Layout:**
- Left 50%: Text content (plugin name, tagline, description, feature bullets)
- Right 50%: Browser/editor window mockup showing JSON code

**Typography:**
- Plugin name: 64px, bold (700), dark charcoal (#1a1a1a)
- "WebMCP for WordPress" — apply blue gradient only to "WordPress" word
- Tagline: 28px, regular weight, medium gray (#555)
- Description: 16px, lighter gray (#777), max-width 400px
- Feature bullets: 14px, small dot indicators

**Visual Elements:**
- macOS-style code editor window (dark theme, #1e1e1e background)
- Authentic traffic lights (red, yellow, green) in top-left corner
- Browser URL bar showing "navigator.modelContext"
- JSON code with syntax highlighting:
  - Keywords: blue (#569cd6)
  - Strings: orange (#ce9178)
  - Properties: light blue (#9cdcfe)
  - Brackets: light gray (#d4d4d4)
- Subtle connection badge showing WordPress "W" → Chrome logo

**Background:**
- Soft radial gradients in corners (very subtle, low opacity)
- White to light gray linear gradient

---

## 2. WordPress.org Plugin Icon (256×256px & 128×128px)

### Midjourney/DALL-E Prompt:

```
Geometric minimalist icon for WordPress plugin. Central hexagon shape with WordPress
"W" mark inside. Surrounding the hexagon: circuit-board style connection nodes and
trace lines, suggesting API connectivity and integration.

Color: WordPress blue (#21759B) as primary, Chrome blue (#4285F4) as accent. Connection
nodes are placed at top-left, top-right, and bottom-center positions. Subtle gradient
from WordPress blue to Chrome blue across the icon.

No text, no labels. Pure geometric shapes — hexagons, circles, lines. Clean, technical,
modern. Must remain clear and recognizable at 128×128px (small sizes). SVG-style vector art.

Flat design, no shadows or 3D effects. Minimal line weight. High contrast against both
white and dark backgrounds.
```

### Designer Brief:

**Specifications:**
- Format: Square (256×256, scale down to 128×128 for Apple icon sizes)
- Style: Geometric, flat, no shadows or depth
- Primary shapes: Hexagon (center), circles (connection nodes), lines (traces)

**Color Palette:**
- Primary: WordPress blue (#21759B)
- Accent: Chrome blue (#4285F4)
- Light background: white or light gray

**Icon Hierarchy:**
- Foreground: WordPress "W" mark (bold, inside hexagon)
- Mid-layer: Hexagon border
- Background: Connection nodes and circuit traces (subtle, low opacity or lighter shade)

**Scalability Notes:**
- Line weight: 2-3px at 256×256 (scales proportionally)
- Node circles: 6px diameter at 256×256
- Must remain recognizable at 32×32px favicon size

**Exportables:**
- SVG (scalable, preferred)
- PNG transparent background (256×256, 128×128, 64×64, 32×32)

---

## 3. README Social Preview / OG Image (1280×640px)

### Midjourney/DALL-E Prompt:

```
Dark-themed social preview image for GitHub repository. Minimal, bold typography.

Main headline in white, large: "WebMCP for WordPress"
Subheadline: "Structured tools for AI agents. No scraping. No guesswork." (smaller, light gray)

Lower half shows code snippet on dark background (like a terminal):

const tools = navigator.modelContext.tools;
const post = await tools.publish_post({
  title: "Hello World",
  content: "...",
  status: "published"
});

Color scheme: Dark background (nearly black, #0d1117), code text in bright green/cyan,
white headlines. WordPress blue and Chrome blue accent colors used sparingly for borders
or highlights.

Minimalist aesthetic, no images, no illustrations. Professional, developer-focused.
High contrast text. 2:1 aspect ratio (1280×640).
```

### Designer Brief:

**Layout:**
- Dark background: #0d1117 (GitHub dark mode standard)
- Top 40%: Headlines
- Bottom 60%: Code block

**Typography:**
- Main headline: "WebMCP for WordPress" — 72px, bold (700), white, centered
- Subheadline: 32px, regular, light gray (#adbac7), centered, line-height 1.4
- Code: 14px monospace, syntax highlighting

**Code Styling:**
- Background: #010409 (slightly darker than main background)
- Text: #7ee787 (GitHub code green)
- Keywords/operators: #79c0ff (cyan)
- Strings: #a371f7 (purple)
- Comments: #8b949e (gray, if used)
- Border: thin 1px line in blue accent (#4285F4) or white with low opacity

**Visual Accents:**
- Subtle left border in WordPress blue (#21759B) — 4px wide
- Right border in Chrome blue (#4285F4) — 4px wide
- Or corner accent: small hexagon icon from plugin icon (top-right or bottom-left)

**Export:**
- PNG (1280×640, high DPI for retina displays)

---

## 4. Reddit/Twitter Post Header (1200×630px)

### Midjourney/DALL-E Prompt:

```
Eye-catching social media header image for Twitter/Reddit post announcing WordPress
WebMCP support. Split-screen comparison layout:

LEFT SIDE: "Before" visualization — AI agent robot (stylized, simple geometric shapes)
with arrows pointing at a WordPress site, suggesting scraping/confusion. Text: "Scraping. Guessing. Messy."

RIGHT SIDE: "After" visualization — Same AI agent, now connected to WordPress via
clean API interface (JSON boxes, tool icons). Text: "Structured. Reliable. Clean."

Center: Bold text "WordPress just got WebMCP support" in white, arching over both scenes.

Colors: Dark background (#1a1a1a), using WordPress blue (#21759B) and Chrome blue (#4285F4)
for accent highlights. High contrast, energetic but not cartoonish. Modern, technical feel.

Bottom banner: small text "WebMCP for WordPress — AI agents, meet WordPress."

Aspect ratio: 1200×630 (16:8.3).
```

### Designer Brief:

**Layout:**
- Dark background: #1a1a1a (dark charcoal)
- Center headline arching across top: "WordPress just got WebMCP support"
- Left 50%: "Before" concept (current state)
- Right 50%: "After" concept (with WebMCP)

**Typography:**
- Main headline: 56px, bold (700), white, centered, uppercase
- Side labels: 24px, medium (600), accent colors (WordPress blue for "Before", Chrome blue for "After")
- Bottom tagline: 16px, light gray, centered

**Visual Concepts (Minimalist):**

**Before (Left):**
- Stylized AI agent icon (simple geometric shapes: circle head, lines for confusion)
- Tangled arrows or lines pointing at WordPress site representation
- Keywords overlay: "Scraping", "Guessing", "Messy" in small text, light gray
- Color accent: muted red or orange (#c85a54) for confusion/chaos

**After (Right):**
- Same AI agent, but composed/happy
- Clean, organized arrows pointing to structured JSON blocks
- JSON tool visualization (stacked cards showing tool names and schemas)
- Keywords overlay: "Structured", "Reliable", "Clean" in small text, light gray
- Color accent: Chrome blue (#4285F4) for clean/connection

**Bridge Element:**
- Center: subtle arrow or "→" pointing from left to right
- Or: small hexagon icon from plugin branding

**Export:**
- PNG (1200×630, high DPI)
- GIF version optional (subtle animation: confusion → clarity)

---

## Design System Notes for Future Assets

**Typography Stack:**
- Headings: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto' (system fonts)
- Body: Same stack
- Code: 'Monaco', 'Courier New', or 'Consolas' monospace

**Color Tokens:**
- Primary: WordPress blue #21759B
- Secondary: Chrome/Google blue #4285F4
- Neutral dark: #1a1a1a, #1e1e1e (for code/dark themes)
- Neutral light: #f8f9fa, #ffffff
- Text primary: #1a1a1a (dark mode: #e0e0e0)
- Text secondary: #666666 (dark mode: #adbac7)

**Spacing:**
- Grid: 8px base unit (8, 16, 24, 32, 48, 64...)
- Padding: 60px/80px for large containers
- Gap: 12px/24px for element spacing

**Imagery Style:**
- Minimalist, geometric, technical
- No illustrations, clipart, or mascots
- Code samples and JSON formatting showcased as visual elements
- Browser/IDE window mockups with authentic UI chrome (traffic lights, etc.)

**Tone:**
- Professional, developer-centric (like Stripe, Vercel, GitHub)
- Trustworthy and modern
- Technical but approachable
- No corporate speak, no playfulness
