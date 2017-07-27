function send_event(category, action, label, value) {
    try {
        if (!label || label.length <= 0) {
            ga('send', 'event', category, action);
        }
        else if (!value || value.length <= 0) {
            ga('send', 'event', category, action, label);
        }
        else {
            ga('send', 'event', category, action, label, value);
        }
    } catch (err) {
        console.log(err.message);
    }
}
window.send_event = send_event;
