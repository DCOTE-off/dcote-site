# Native Scrollbar

Use a native browser scrollbar when a scrollable element only needs custom colors,
width, and rounded corners. Do not add JavaScript, refs, pointer handlers, or a
separate track and thumb unless the UI requires behavior the browser cannot provide.

The scrollable element needs a constrained height and vertical overflow:

```css
.scrollable-content {
    --sb-track-color: #241832;
    --sb-thumb-color: #442c61;
    --sb-size: 6px;

    overflow-y: auto;
    overflow-x: hidden;
}
```

For Chromium and Safari:

```css
.scrollable-content::-webkit-scrollbar {
    width: var(--sb-size);
}

.scrollable-content::-webkit-scrollbar-track {
    background: var(--sb-track-color);
    border-radius: 22px;
}

.scrollable-content::-webkit-scrollbar-thumb {
    background: var(--sb-thumb-color);
    border-radius: 22px;
}

.scrollable-content::-webkit-scrollbar-button,
.scrollable-content::-webkit-scrollbar-button:single-button:vertical:decrement,
.scrollable-content::-webkit-scrollbar-button:single-button:vertical:increment {
    display: none !important;
    width: 0 !important;
    height: 0 !important;
}
```

For browsers without WebKit scrollbar selectors, primarily Firefox:

```css
@supports not selector(::-webkit-scrollbar) {
    .scrollable-content {
        scrollbar-color: var(--sb-thumb-color) var(--sb-track-color);
        scrollbar-width: thin;
    }
}
```

Notes:

- Apply the selector to the element with `overflow-y: auto`, not automatically to `body`.
- Set the width only on `::-webkit-scrollbar`. The browser uses that width for both the track and thumb.
- Keep `scrollbar-color` inside the fallback. In Chromium, setting it alongside `::-webkit-scrollbar-*` can override the detailed styles.
- The browser calculates thumb height, position, and drag behavior automatically.
- Native scrollbar arrows and exact geometry are controlled partly by the browser and operating system. CSS cannot guarantee pixel-identical output in every environment.

Current usage: `resources/css/pages/dcote-main.css` applies this pattern to `.updates .updates-news`.
