# Embla Carousel (v8) — "Select" Carousel Pattern

Use EmblaCarousel when a list of cards must be a carousel on mobile while staying a
plain grid on desktop, and the intended UX is *selection* rather than a swipe gallery:

- the **active** (centered) slide is a normal link — clicking it navigates;
- **neighbors** are not links in practice — clicking one scrolls exactly one step
  toward it, then the clicked slide becomes the active link;
- touch **swipe** still works (kept via `watchDrag`).

Reference implementation: `resources/js/Pages/Home.vue` → `.grid-images` +
`resources/css/pages/dcote-main.css`.

## DOM contract

Embla v8 needs this exact shape. The viewport is whatever node you pass to
`EmblaCarousel(node)`, and the **container is always `node.children[0]`** (hardcoded
in `embla-carousel.esm.js`: `container = root.children[0]`).

```html
<div class="grid-images" ref="gridImagesRef">   <!-- viewport: overflow hidden -->
    <div class="grid-images__inner">             <!-- container: display flex -->
        <a class="grid-image-card">              <!-- slide: direct child -->
            <div class="grid-image-scale">       <!-- visual scale lives here -->
                <div class="wrapper">…</div>
            </div>
        </a>
    </div>
</div>
```

Slides must be **direct children** of the container — embla measures them via
`offsetWidth` and applies loop transforms to them.

## Options used (Home)

```js
gridCarousel = EmblaCarousel(root, {
    align: 'center',
    containScroll: false,
    loop: slidesCount > 1,
    slidesToScroll: 1,
    skipSnaps: false,
    watchDrag: (emblaApi, event) => !gridCarouselSettling,
});
```

## Vue wiring

Init only on mobile, destroy on desktop / unmount. `isMobile` comes from
`useMediaQuery` in `resources/js/Composables/useMediaQuery.js`.

```js
const gridImagesRef = ref(null);
const activeGridIndex = ref(0);
let gridCarousel = null;
let gridCarouselSettling = false;

watch(isMobile, (mobile) => {
    if (mobile) initGridCarousel();
    else destroyGridCarousel();
});

onMounted(() => { if (isMobile.value) initGridCarousel(); });
onBeforeUnmount(destroyGridCarousel);
```

## Click handling

Direction is decided by comparing the clicked slide's center to the viewport center
(works correctly in a loop, unlike comparing indices). Neighbors always
`preventDefault()` so they can never navigate. While the carousel is settling,
**every** click is swallowed (`preventDefault`) — including the active slide's link.

```js
function onSlideClick(index, event) {
    if (gridCarouselSettling) { event.preventDefault(); return; }
    if (index === activeGridIndex.value) return;            // active → navigate

    event.preventDefault();
    if (!gridCarousel) return;

    const rootCenter = root.getBoundingClientRect().left + root.offsetWidth / 2;
    const targetCenter = event.currentTarget.getBoundingClientRect().left + event.currentTarget.offsetWidth / 2;
    const goingNext = targetCenter > rootCenter;

    if (goingNext ? !gridCarousel.canScrollNext() : !gridCarousel.canScrollPrev()) return;
    goingNext ? gridCarousel.scrollNext() : gridCarousel.scrollPrev();
    gridCarouselSettling = true;                             // released on 'settle'
}
```

## Kill the invisible tail

Embla's scroll is an exponential approach, not a finite easing (see "Animation
internals" below). It only considers itself settled when `|target − location| < 0.001px`,
so the last ~5% of travel takes ~70% of the time (measured: ~1200 ms of a ~1700 ms
animation) — imperceptible sub-pixel crawling. Cut it: when the remaining distance
drops below a threshold, jump to the target.

```js
const TAIL_THRESHOLD_PX = 0.5;

gridCarousel.on('scroll', () => {
    const engine = gridCarousel.internalEngine();
    if (engine.dragHandler.pointerDown()) return;          // never kill an active drag
    const remaining = Math.abs(engine.target.get() - engine.location.get());
    if (remaining < TAIL_THRESHOLD_PX) {
        gridCarousel.scrollTo(engine.index.get(), true);   // jump = duration 0
    }
});
```

The threshold is in px, which makes the cut **fps-adaptive** (the point where
movement becomes invisible is reached earlier on a 60 Hz screen than on 30 Hz).

## Gotchas (regressions we actually hit — do not reintroduce)

1. **Never put `transition: all` or `transition: transform` on a slide element.**
   Embla's SlideLooper teleports slides through inline `style.transform` when they
   cross the loop "seam". A CSS transition animates those teleports, so at page load
   and at the wrap point slides visibly fly across the screen. Restrict transitions
   to `scale` (e.g. `transition: scale 0.3s`).

2. **`scale` composes with embla's `transform` — and multiplies the translate.**
   `scale` and `transform` are independent CSS properties that compose
   (`scale × translate`). If the visual scale (`scale: 0.85` for neighbors) sits on
   the slide element, embla's loop shift `translate3d(-contentSize,0,0)` is
   **scaled to `−contentSize × 0.85`**, so the parked "previous" slide lands on top
   of the active one (measured overlap: 144 px). Fix: move the visual scale to an
   **inner wrapper** (`.grid-image-scale`) so it never multiplies embla's translate.

3. **Keep drag, but disable it while a programmatic scroll is settling.**
   On any `pointerdown`, embla's drag handler stops the in-flight tween
   (`scrollBody.useFriction(0).useDuration(0); target.set(location)`) and re-snaps on
   release. So a tap mid-animation cancels the switch and returns to the nearest
   snap — `watchDrag: false` would fix that but kill swipe. Instead return `false`
   from the `watchDrag` callback while `gridCarouselSettling` is true; swipe still
   works when idle.

4. **The viewport must not remain `display: grid`.**
   If the desktop base rule is `display: grid` and the mobile override only changes
   `overflow`, the container becomes a single grid item in one of the 5 columns
   (~20% wide) and slides are `60%` of that — tiny cards. On mobile explicitly set
   `display: block` on the viewport.

5. **A slide parked at `translate3d(-contentSize,0,0)` is correct, not a bug.**
   At the start of a loop the previous slide sits flush against the left edge of the
   active one, ready to wrap in seamlessly. Do not "fix" it by removing the transform.

## Animation internals (for tuning)

Embla v8 uses no named easing. `ScrollBody.seek()` runs a discrete recurrence per
frame (`embla-carousel.esm.js`):

```js
velocity += (target - location) / duration;
velocity *= friction;
location += velocity;
```

Defaults: `duration: 25`, `friction: 0.68`. These are **not milliseconds and not
frames-at-60-fps** — they are recurrence parameters, and convergence is therefore
frame-rate dependent. The curve is exponential (no overshoot); measured time
constant ≈ 130 ms at 30 fps. Programmatic `scrollNext()` always moves exactly one
snap; a swipe release adjusts `duration`/`friction` from the throw force.

## Reuse checklist

- [ ] Viewport `overflow: hidden`; container is the viewport's first child; slides are direct children.
- [ ] Mobile viewport `display: block` (overrides any desktop grid/flex).
- [ ] Visual scale on an inner wrapper, not on the slide.
- [ ] Slide transitions limited to `scale` — never `transform`/`all`.
- [ ] `watchDrag: () => !settling`.
- [ ] Tail cut on `scroll`, guarded by `dragHandler.pointerDown()`, jump via `scrollTo(index, true)`.
- [ ] Click: neighbors `preventDefault()` always; swallow all clicks while settling; direction by center comparison.
- [ ] Init only on mobile; destroy on desktop/unmount.