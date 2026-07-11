(function () {
	if (window.__dcoteSitePresenceStarted) return;
	window.__dcoteSitePresenceStarted = true;

	const config = window.DCOTE_SITE_METRICS || {};
	const heartbeatMs = Math.min(60000, Math.max(5000, Number(config.heartbeatMs) || 15000));
	const reconnectBaseMs = Math.min(30000, Math.max(1000, Number(config.reconnectMs) || 3000));
	const reconnectMaxMs = Math.min(120000, Math.max(reconnectBaseMs, Number(config.maxReconnectMs) || 60000));
	const storageKey = config.storageKey || "dcote_metrics_session_id";
	const tabId = createId("tab");
	let sessionId = null;
	let socket = null;
	let heartbeatTimer = null;
	let reconnectTimer = null;
	let reconnectAttempt = 0;
	let active = true;

	function createId(prefix) {
		if (window.crypto?.randomUUID) return `${prefix}-${window.crypto.randomUUID()}`;
		return `${prefix}-${Date.now().toString(36)}-${Math.random().toString(36).slice(2)}`;
	}

	function getSessionId() {
		if (sessionId) return sessionId;
		try {
			sessionId = window.sessionStorage.getItem(storageKey) || createId("session");
			window.sessionStorage.setItem(storageKey, sessionId);
		} catch {
			sessionId = createId("session");
		}
		return sessionId;
	}

	function getWebsocketUrl() {
		if (config.websocketUrl) return config.websocketUrl;
		const base = new URL(
			config.metricsBaseUrl || "https://video.dcote.net/metrics-api",
			window.location.href,
		);
		base.protocol = base.protocol === "https:" ? "wss:" : "ws:";
		base.pathname = `${base.pathname.replace(/\/+$/, "")}/metrics/site/ws`;
		base.search = "";
		base.hash = "";
		return base.toString();
	}

	function getPayload() {
		const configuredUser = typeof config.getUserId === "function"
			? config.getUserId()
			: (config.isRegistered ?? config.userId ?? false);
		const page = typeof config.getPage === "function"
			? config.getPage()
			: (config.page || window.location.pathname);
		return {
			page,
			userId: configuredUser ? "registered" : "anonymous",
			sessionId: getSessionId(),
			tabId,
		};
	}

	function sendPresence() {
		if (socket?.readyState !== WebSocket.OPEN) return;
		try {
			socket.send(JSON.stringify(getPayload()));
		} catch {
			// Telemetry must never affect the page itself.
		}
	}

	function clearTimers() {
		if (heartbeatTimer) window.clearInterval(heartbeatTimer);
		if (reconnectTimer) window.clearTimeout(reconnectTimer);
		heartbeatTimer = null;
		reconnectTimer = null;
	}

	function scheduleReconnect() {
		if (!active) return;
		clearTimers();
		const delay = Math.min(reconnectMaxMs, reconnectBaseMs * 2 ** Math.min(reconnectAttempt, 8));
		reconnectAttempt += 1;
		reconnectTimer = window.setTimeout(connect, delay + Math.floor(Math.random() * 1000));
	}

	function connect() {
		if (!active || socket?.readyState === WebSocket.CONNECTING || socket?.readyState === WebSocket.OPEN) return;
		clearTimers();

		let nextSocket;
		try {
			nextSocket = new WebSocket(getWebsocketUrl());
		} catch {
			scheduleReconnect();
			return;
		}
		socket = nextSocket;
		nextSocket.addEventListener("open", () => {
			if (socket !== nextSocket) return;
			reconnectAttempt = 0;
			sendPresence();
			heartbeatTimer = window.setInterval(sendPresence, heartbeatMs);
		});
		nextSocket.addEventListener("close", () => {
			if (socket !== nextSocket) return;
			socket = null;
			scheduleReconnect();
		});
		nextSocket.addEventListener("error", () => nextSocket.close());
	}

	function notifyRouteChange() {
		window.setTimeout(sendPresence, 0);
	}

	function patchHistory(method) {
		const original = window.history[method];
		if (typeof original !== "function") return;
		window.history[method] = function () {
			const result = original.apply(this, arguments);
			notifyRouteChange();
			return result;
		};
	}

	patchHistory("pushState");
	patchHistory("replaceState");
	window.addEventListener("popstate", notifyRouteChange);
	window.addEventListener("visibilitychange", () => {
		if (document.visibilityState === "visible") notifyRouteChange();
	});
	window.addEventListener("online", connect);
	window.addEventListener("pagehide", () => {
		active = false;
		clearTimers();
		socket?.close();
		socket = null;
	});
	window.addEventListener("pageshow", () => {
		active = true;
		connect();
	});

	connect();
})();
