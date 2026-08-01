# Metrics integration contract v1

`dcote_metrics` owns and serves the browser tracker from
`https://metrics-api.dcote.net/site-presence-tracker.js`. The site only embeds
the tracker and supplies its runtime configuration. Override the two public
origins with `DCOTE_METRICS_BASE_URL` and `DCOTE_VIDEO_BASE_URL` when needed.

## Site presence WebSocket

- URL: `{metrics_base_url}/metrics/site/ws`
- Message fields: `page`, `userId`, `sessionId`, `tabId`
- `userId`: an HMAC pseudonym for authenticated users, otherwise `null`
- Server response: `{ "ok": true }` or `{ "ok": false, "error": "..." }`

## Player

- URL: `{video_base_url}/videoplayer`
- Query fields: `src`, `poster`, `skip_start`, `ass`, `ass_lang`

Video manifests and subtitles remain under
`{video_base_url}/season-XX/episode-XX/*`. Player telemetry is sent directly to
`https://metrics-api.dcote.net`.

Contract changes must remain backward compatible until both repositories have
been deployed. Browser telemetry is optional and must never block the site.
