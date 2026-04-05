import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static values = {
        delay: { type: Number, default: 10000 },
    };

    connect() {
        requestAnimationFrame(() => {
            this.element.classList.add('is-visible');
        });

        this.timeout = window.setTimeout(() => {
            this.close();
        }, this.delayValue);
    }

    close() {
        this.element.classList.remove('is-visible');
        this.element.classList.add('is-hiding');

        window.setTimeout(() => {
            this.element.remove();
        }, 350);
    }

    disconnect() {
        if (this.timeout) {
            window.clearTimeout(this.timeout);
        }
    }
}
