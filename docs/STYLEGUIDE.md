# TackPad Style Guide

The **single source of truth** for TackPad's visual identity. Every screen —
existing or new — must look like it was always part of the product. Colours and
typography are a **client requirement**: preserve them exactly. Do not introduce
new brand colours, do not switch to a light theme, do not restyle on the side.
When a new feature needs a token that isn't here, add it here first, then use it.

TackPad's identity is a **terminal aesthetic**: a pure-black canvas, white
monospace type, crisp white cards, one deep-navy primary, and a single
tri-colour gradient signature. It reads like a focused command-line tool, not a
generic SaaS dashboard. Keep it that way.

---

## 1. Colour

### 1.1 Core palette

| Token | Hex | Role |
| --- | --- | --- |
| `--tp-black` | `#000000` | App background (the canvas). |
| `--tp-white` | `#FFFFFF` | Primary text on black; card surfaces. |
| `--tp-surface` | `#FEFEFE` | Modal / card surface (near-white). |
| `--tp-navy` | `#06145B` | **Primary** — buttons, modal accents, calendar events, shared indicator. |
| `--tp-navy-ink` | `#06145B` | Primary text/icon colour on white when navy is needed. |

The core interaction pattern is **invert on hover**: a navy (or white) filled
button swaps its background and text colours on hover. Preserve this everywhere.

### 1.2 Signature gradient

The one memorable element. Originally the underline beneath the auth title;
reused for any single "hero" accent (auth title underline, password-strength
meter fill, active-state accents). Never use it as a fill behind text.

```
linear-gradient(90deg, #0FA2D6 0%, #090979 50%, #F18522 100%)
```

| Stop | Hex | Note |
| --- | --- | --- |
| Cyan | `#0FA2D6` | `rgb(15,162,214)` |
| Deep blue | `#090979` | `rgb(9,9,121)` |
| Orange | `#F18522` | `rgb(241,133,34)` |

### 1.3 Semantic task-status colours

Single source of truth for task-row / calendar-event state. The mapping from
state → colour lives only in CSS tokens (mirrored by `taskStatusClass()` in
`core/helpers.php` and `public/js/taskStatus.js`, which decide the *state*).

| Token | Hex | Named | State |
| --- | --- | --- | --- |
| `--task-on-time` | `#90EE90` | lightgreen | Open, not past due. |
| `--task-overdue` | `#F08080` | lightcoral | Open, past due. |
| `--task-completed` | `#D3D3D3` | lightgrey | Completed. |

### 1.4 Functional colours

| Token | Hex | Role |
| --- | --- | --- |
| `--tp-edit` | `#4CAF50` | Edit / positive confirm. |
| `--tp-danger` | `#FF0000` | Delete / destructive. |
| `--tp-danger-ink` | `#B00020` | Error text (on light surfaces). |
| `--tp-sidenav` | `#333333` | Sidebar background. |
| `--tp-sidenav-hover` | `#555555` | Sidebar link hover. |
| `--tp-tab` | `#1C1C1C` | Inactive tab background. |
| `--tp-tab-border` | `#444444` | Inactive tab border. |
| `--tp-muted` | `#CCCCCC` | Muted text on black (empty states, hints). |
| `--tp-hint` | `#CFD6FF` | Calendar hint text (pale navy tint). |
| `--tp-auth-border` | `#005475` | Auth card border (`hsl(197,100%,23%)`). |

### 1.5 Toasts (new, derived from the palette)

Modern toast/inline-alert system replaces raw `alert()` and red inline markers.
Toasts sit on white cards to match modals; the left accent bar carries meaning.

| Variant | Accent | Usage |
| --- | --- | --- |
| success | `--tp-edit` `#4CAF50` | Saved, shared, completed. |
| error | `--tp-danger-ink` `#B00020` | Validation / server failures. |
| info | `--tp-navy` `#06145B` | Neutral confirmations. |

---

## 2. Typography

| Aspect | Value |
| --- | --- |
| Display + body family | **Inconsolata**, monospace fallback (`"Inconsolata", ui-monospace, monospace`). |
| Loaded weight | 700 (the app ships a single bold cut; treat 700 as the default weight). |
| Auth title | `4em`, bold, black on white card; `2.5em` ≤829px; `2em` ≤480px. |
| Sidebar wordmark (`h1`) | `50px`, black on white chip with a white glow. |
| Sidebar greeting (`h2`) | `18px`, white. |
| Section heading (`h1`) | browser default size, white, centred. |
| Buttons / controls | `16px`. |
| Base body (mobile) | `14px`. |
| Tabs | `16px`. |

Font is loaded from Google Fonts:
`https://fonts.googleapis.com/css2?family=Inconsolata:wght@700&display=swap`.
Everything is monospace — this is intentional and part of the brand.

---

## 3. Spacing, radii, borders, shadows

### 3.1 Radii

| Token | Value | Applies to |
| --- | --- | --- |
| `--tp-radius-btn` | `10px` | Buttons, tabs, auth card, calendar card. |
| `--tp-radius-modal` | `25px` | Modal container (`25px 25px 0 0` header / `0 0 25px 25px` footer). |
| `--tp-radius-input` | `4px` | Modal inputs / selects. |
| `--tp-radius-sm` | `6px` | Calendar action buttons. |

### 3.2 Borders

- Buttons: `2px solid` in the button's own colour (navy, white, green, red).
- Table cells: `1px solid #000` on white.
- Modal header/footer dividers: `2px solid #000`.
- Auth card: `2px solid #005475`.
- Shared-task row: `4px solid #06145B` left border on the first cell.

### 3.3 Shadows

| Token | Value | Applies to |
| --- | --- | --- |
| `--tp-shadow-soft` | `rgba(0,0,0,0.35) 0 5px 15px` | Options bar, form inputs. |
| `--tp-shadow-white-glow` | `0 2px 18px 0 rgba(255,255,255,0.5)` | Table on black (glow), sidebar wordmark. |
| `--tp-shadow-modal` | `0 4px 8px rgba(0,0,0,0.2), 0 6px 20px rgba(0,0,0,0.19)` | Modal container. |
| `--tp-shadow-navy` | `0 2px 18px 0 rgba(0,0,0,0.5)` | Primary "add" button. |
| `--tp-shadow-card` | `0 10px 30px rgba(0,0,0,0.35)` | Calendar white card. |

### 3.4 Layout

- Content sits on black; primary data (tables, calendar) on white cards centred
  with generous outer margin. Task table width `50%` desktop, `100%` ≤829px.
- Sidebar is an off-canvas drawer (`width: 0` → `250px`), fixed, dark `#333`.
- Options bar is sticky at the top, centred, `50%` width (max `800px`).
- Mobile breakpoints: **829px** (primary), **600px** (calendar), **480px** (auth).

---

## 4. Component patterns

### 4.1 Buttons

- **Primary (navy):** `#06145B` fill, white text, `2px` navy border, radius `10px`,
  `transition 0.4s`. Hover → white fill, navy text.
- **White (toolbar):** white fill, black text; hover → black fill, white text.
- **Edit (green):** `#4CAF50` fill, white text; hover → white fill, green text.
- **Delete (red):** red fill, white text; hover → white fill, red text.
- Padding roughly `15–16px 32px`.

### 4.2 Modals

Centred dialog, white surface, radius `25px`, entrance animation `animatetop`
(slide from `top:-300px` + fade, `0.4s`). Header and footer are white with a
`2px solid #000` divider; close affordance is a bold `×` top-right. Backdrop is
`rgba(0,0,0,0.4)`. All create/edit/confirm happens in modals — never a page
redirect.

### 4.3 Tabs (task segments)

Always visible at the top (`Open` / `Overdue` / `Completed`) so a user never
scrolls to find them. Inactive: `#1C1C1C` bg, `#444` border, white text.
Active: white bg, black text, white border.

### 4.4 Task rows

Row background is its semantic status colour (§1.3). Completed rows are struck
through via `.erledigt td { text-decoration: line-through }`. Shared rows get a
navy left accent + a share badge next to the title. Exact completion date/time
is shown in the completed list ("Completed on" column).

### 4.5 Calendar

FullCalendar on a white card (radius `10px`, `--tp-shadow-card`). Events use the
status semantics: open = navy, completed = grey + strikethrough. FC primary
buttons are navy; active/hover use edit-green `#4CAF50`. Drag-to-reschedule
persists immediately with no reload.

### 4.6 Avatars (new)

Round, `2px` navy ring, sit in the sidebar (current user) and beside a shared
task's collaborator. Settable by URL (with a confirm-before-apply preview) or by
image upload. Fallback: user's monogram on a navy disc, white monospace initial.

### 4.7 Auth (new, unified)

One screen toggling **Login / Register** via buttons (no separate routes to
click through). Black canvas with the app icon faint behind, a white card
(`2px solid #005475`, radius `10px`), and the tri-colour gradient underline
under the title. Register adds a live **password-strength meter** (gradient
fill, driven by `zxcvbn`) and a **generate strong password** button.

---

## 5. Interactive states & motion

- **Hover:** the invert pattern (§4.1); links in sidebar darken to `#555`.
- **Focus:** every interactive element must show a visible keyboard focus ring
  (navy on white surfaces, white on black). Never remove focus outlines without
  replacing them.
- **Transitions:** `0.3s–0.5s ease` for colour/inversion; `0.4s` drawer slide;
  `0.4s` modal entrance.
- **Reduced motion:** honour `prefers-reduced-motion: reduce` — disable the
  modal slide and non-essential transitions.
- **Selection state:** the checked checkboxes are the single source of truth for
  bulk actions; toolbar buttons derive their visibility from that state.

---

## 6. Theme handling

TackPad is **single-theme (dark canvas)** by deliberate brand choice. There is
no light/dark toggle and none should be added. "Dark" here means the black
canvas with white type and white data cards — not an inverted colour scheme.
All tokens above are absolute; they do not vary by `prefers-color-scheme`.
