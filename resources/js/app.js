import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.store('confirmModal', {
    open: false,
    formId: null,
    message: 'Apakah Anda yakin ingin menghapus data ini? Tindakan ini tidak dapat dibatalkan.',
    open(formId, message = null) {
        this.formId = formId;

        if (message) {
            this.message = message;
        }

        this.open = true;
    },
    cancel() {
        this.open = false;
        this.formId = null;
    },
    submit() {
        if (this.formId) {
            document.getElementById(this.formId)?.submit();
        }
    },
});

window.confirmDelete = (formId, message = null) => {
    Alpine.store('confirmModal').open(formId, message);
};

Alpine.data('teachersCarousel', () => ({
    px: 0,
    paused: false,
    pxPerSecond: 50,
    setW: 0,
    track: null,
    rafId: null,
    frameTime: null,

    init() {
        this.$nextTick(() => this.setup());

        window.addEventListener('resize', () => this.setup(), { passive: true });

        if ('IntersectionObserver' in window) {
            const io = new IntersectionObserver((entries) => {
                entries.forEach((entry) => {
                    if (entry.isIntersecting) {
                        this.play();
                    } else {
                        this.pause();
                    }
                });
            }, { rootMargin: '100px' });
            io.observe(this.$refs.wrap);
        }
    },

    setup() {
        const track = this.$refs.track;
        const container = this.$refs.wrap;
        if (!track || !container || container.clientWidth === 0) {
            return;
        }

        const firstSet = track.firstElementChild;
        if (!firstSet) {
            return;
        }

        while ((track.scrollWidth / 2) < container.clientWidth * 1.1 && track.children.length < 40) {
            const clone = firstSet.cloneNode(true);
            clone.setAttribute('aria-hidden', 'true');
            track.appendChild(clone);
        }

        this.track = track;
        this.setW = Math.round(firstSet.getBoundingClientRect().width + 24);

        this.play();
    },

    play() {
        if (this.rafId != null || this.setW <= 0 || window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
            return;
        }

        this.frameTime = null;

        const frame = (now) => {
            if (this.rafId == null) {
                return;
            }

            if (!this.paused) {
                if (this.frameTime != null) {
                    const delta = Math.min(now - this.frameTime, 100);
                    this.px += (this.pxPerSecond / 1000) * delta;
                    if (this.px >= this.setW) {
                        this.px -= this.setW;
                    }
                    this.track.style.transform = `translateX(${-this.px}px)`;
                }
                this.frameTime = now;
            } else {
                this.frameTime = null;
            }

            this.rafId = requestAnimationFrame(frame);
        };

        this.rafId = requestAnimationFrame(frame);
    },

    pause() {
        if (this.rafId != null) {
            cancelAnimationFrame(this.rafId);
            this.rafId = null;
            this.frameTime = null;
        }
    },
}));

Alpine.start();

const revealElements = () => document.querySelectorAll('.reveal:not(.reveal-shown)');

if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
    revealElements().forEach((el) => el.classList.add('reveal-shown'));
} else {
    const io = new IntersectionObserver(
        (entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('reveal-shown');
                    io.unobserve(entry.target);
                }
            });
        },
        { threshold: 0.15, rootMargin: '0px 0px -40px 0px' }
    );

    revealElements().forEach((el) => io.observe(el));
}