const notificationListDOM = document.getElementById("notification-list");

function showNotification(message, duration = 3000, color = "#f56565") {
    const container = document.getElementById("notificationsContainer");
    const notification = document.createElement("div");
    notification.className = "notification";
    notification.innerText = message;
    notification.style.backgroundColor = color;

    container.appendChild(notification);

    // show animation
    requestAnimationFrame(() => notification.classList.add("show"));

    let hideTimeout = setTimeout(
        () => removeNotification(notification),
        duration,
    );

    notification.addEventListener("click", () => removeNotification(notif));

    notification.addEventListener("mouseenter", () =>
        clearTimeout(hideTimeout),
    );
    notification.addEventListener("mouseleave", () => {
        hideTimeout = setTimeout(
            () => removeNotification(notification),
            duration,
        );
    });
}

function removeNotification(notification) {
    notification.classList.remove("show");
    notification.addEventListener("transitionend", () => notification.remove());
}

// Show notification if notif exist
if (notif) {
    if (Array.isArray(notif_color)) {
        if (notif_color.length != notif.length)
            notif_color.fill("green", notif_color.length, notif_color.length);
    }

    if (Array.isArray(notif)) {
        notif.forEach((msg, index) => {
            showNotification(msg, 2500, notif_color[index] || "green");
        });
    } else {
        showNotification(notif, 2500, notif_color || "green");
    }
}
