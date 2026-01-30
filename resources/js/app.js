import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

document.addEventListener('DOMContentLoaded', () => {
    if (!window.Echo || !window.liveReloadChannels) {
        return;
    }

    window.liveReloadChannels.forEach(({ name, event }) => {
        if (!name || !event) {
            return;
        }

        const events = Array.isArray(event) ? event : [event];
        const channel = window.Echo.channel(name);

        events.forEach((eventName) => {
            channel.listen(`.${eventName}`, () => {
                window.location.reload();
            });
        });
    });
});
