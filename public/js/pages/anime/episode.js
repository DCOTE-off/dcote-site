document.addEventListener("DOMContentLoaded", () => {
	const player = document.getElementById("episode-iframe-player");
	const status = document.getElementById("episode-player-status");
	const retry = document.getElementById("episode-player-retry");
	const buttons = document.querySelectorAll(".subs-and-dubs button");
	let loadTimer = null;

	function hidePlayerError() {
		if (loadTimer) window.clearTimeout(loadTimer);
		loadTimer = null;
		if (status) status.hidden = true;
		if (player) player.hidden = false;
	}

	function watchPlayerLoad() {
		if (!player || !status) return;
		hidePlayerError();
		loadTimer = window.setTimeout(() => {
			player.hidden = true;
			status.hidden = false;
		}, 12000);
	}

	if (player) {
		player.addEventListener("load", hidePlayerError);
		watchPlayerLoad();
	}

	retry?.addEventListener("click", () => {
		if (!player) return;
		watchPlayerLoad();
		const currentSource = player.src;
		player.src = "about:blank";
		window.setTimeout(() => {
			player.src = currentSource;
		}, 0);
	});

	buttons.forEach((button) => {
		button.addEventListener("click", () => {
			if (
				!player
				|| button.hasAttribute("disabled")
				|| button.classList.contains("active")
			) {
				return;
			}
			buttons.forEach((item) => item.classList.remove("active"));
			button.classList.add("active");
			const newSource = button.getAttribute("data-src");
			if (newSource) {
				watchPlayerLoad();
				player.src = newSource;
			}
		});
	});
});
