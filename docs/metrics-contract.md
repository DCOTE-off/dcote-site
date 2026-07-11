# Metrics integration contract v1

`dcote-site` owns and serves the browser tracker from
`/js/site-presence-tracker.js`. The tracker sends telemetry to the optional
`services.metrics.base_url` endpoint. Its default remains
`https://video.dcote.net/metrics-api`, so deployment needs no new environment
variable.

## Site presence WebSocket

- URL: `{base_url}/metrics/site/ws`
- Message fields: `page`, `userId`, `sessionId`, `tabId`
- `userId`: `registered` or `anonymous`
- Server response: `{ "ok": true }` or `{ "ok": false, "error": "..." }`

## Player

- URL: `{base_url}/videoplayer`
- Query fields: `src`, `poster`, `skip_start`, `ass`, `ass_lang`

Contract changes must remain backward compatible until both repositories have
been deployed. Browser telemetry is optional and must never block the site.
