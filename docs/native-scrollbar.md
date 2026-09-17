# Native Scrollbar

Use a native browser scrollbar when a scrollable element only needs custom colors,
width, and rounded corners. Do not add JavaScript, refs, pointer handlers, or a
separate track and thumb unless the UI requires behavior the browser cannot provide.

The scrollable element needs a constrained height and vertical overflow:

```css
.scrollable-content {
    --sb-size: clamp(1px, 0.39vw, 6px);
    --sb-thumb-color: #442c61;
    --sb-track-color: #241832;

    overflow-y: auto;
    overflow-x: hidden;
}
```

For Chromium and Safari:

```css
.scrollable-content::-webkit-scrollbar {
    width: var(--sb-size);
    z-index: 100;
}

.scrollable-content::-webkit-scrollbar-track {
    background: var(--sb-track-color);
    border-radius: 999px;
}

.scrollable-content::-webkit-scrollbar-thumb {
    background: var(--sb-thumb-color);
    border-radius: 999px;
}

.scrollable-content::-webkit-scrollbar-thumb:hover {
    cursor: grab;
}

.scrollable-content::-webkit-scrollbar-thumb:active {
    cursor: grabbing;
}

.scrollable-content::-webkit-scrollbar-button,
.scrollable-content::-webkit-scrollbar-button:single-button:vertical:decrement,
.scrollable-content::-webkit-scrollbar-button:single-button:vertical:increment {
    display: none !important;
    width: 0 !important;
    height: 0 !important;
}
```

For Firefox (no WebKit scrollbar selectors):

```css
@-moz-document url-prefix() {
    .scrollable-content {
        scrollbar-color: var(--sb-thumb-color) var(--sb-track-color);
        scrollbar-width: thin;
    }
}
```

On mobile, scale the size with the viewport:

```css
@media (max-width: 768px) {
    .scrollable-content {
        --sb-size: clamp(1px, 1.602vw, 20px);
    }
}
```

Notes:

- Apply the selectors to the element with `overflow-y: auto`, not automatically to `body`.
- `--sb-size` is viewport-based (`clamp`), not a fixed `6px`, so the scrollbar scales with the layout.
- Set the width only on `::-webkit-scrollbar`. The browser uses that width for both the track and thumb.
- Keep `scrollbar-color` inside the Firefox fallback (`@-moz-document url-prefix()`). In Chromium, setting it alongside `::-webkit-scrollbar-*` can override the detailed styles.
- The browser calculates thumb height, position, and drag behavior automatically.
- Native scrollbar arrows and exact geometry are controlled partly by the browser and operating system. CSS cannot guarantee pixel-identical output in every environment.

Optional bottom fade for scroll containers (hidden once scrolled to the end):

```css
.scrollable-content::after {
    background: linear-gradient(180deg, rgba(23, 17, 31, 0), rgba(23, 17, 31, 1));
    content: '';
    height: 34%;
    inset: auto 0 0 0;
    pointer-events: none;
    position: absolute;
    transition: opacity 200ms ease;
    z-index: 1;
}

.scrollable-wrapper.is-at-end .scrollable-content::after {
    opacity: 0;
}
```

Current usage:

- `resources/css/pages/dcote-main.css` — `.updates__news`
- `resources/css/components/comments.css` — `.comment-input__field`
